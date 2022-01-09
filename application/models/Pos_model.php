<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pos_model extends CI_Model {

	public function inclusive($price='',$tax_per){
		return ($tax_per!=0) ? $price/(($tax_per/100)+1)/10 : $tax_per;
	}

	public function get_details(){
		$data=$this->data;
		extract($data);
		extract($_POST);
		$CI =& get_instance();
		
		
		  $i=0;
		 
		  if(!empty($id)){
		  	$this->db->where(" a.category_id=$id ");
		  }
		  $this->db->select("a.*,b.tax,b.tax_name");

		  $this->db->join(" db_tax b ","b.id=a.tax_id",'left');
		  
		  $this->db->from("db_items as a");
		  $this->db->where("a.store_id",$store_id);
		  $this->db->where("a.status",1);

		  
	      $q2=$this->db->get();
	      $table='';
	      if($q2->num_rows()>0){
	        foreach($q2->result() as $res2){
	        	if($res2->item_group=='Variants'){continue;}
	        	$w_stock = total_available_qty_items_of_warehouse($warehouse_id,$store_id,$res2->id);
	        	$item_code = $res2->item_code;
	        	$item_tax_type = $res2->tax_type;
	        	$item_tax_id = $res2->tax_id;

	        	$item_sales_price = get_price_level_price($customer_id,$res2->sales_price);
				$item_sales_price = number_format($item_sales_price,2,'.','');

	        	$item_cost = $res2->purchase_price;
	        	$item_tax = $res2->tax;
	        	$item_tax_name = $res2->tax_name;
	        	$service_bit = $res2->service_bit;
	        	$item_sales_qty = 1;

				$item_tax_amt = ($item_tax_type=='Inclusive') ? calculate_inclusive($item_sales_price,$item_tax) :calculate_exclusive($item_sales_price,$item_tax);

				
	        	if($w_stock <1 && !$service_bit){
	        		$str="zero_stock()";
	        		$disabled='';
	        		$bg_color="background-color:#9d9999";
	        	}
	        	else{
	        		$str="addrow($res2->id)";
	        		$disabled="disabled=disabled";
	        		$bg_color="background-color:#dbf4cd";
	        	}

	        	$label_title = (!$service_bit) ? $w_stock.' Quantity in Stock' : 'Service Item';
	        	$label = (!$service_bit) ? "Qty: ".$w_stock : 'Service';

	        	$item_image = $res2->item_image;
	        	if(!empty($res2->parent_id)){
	        		$item_image = get_item_details($res2->parent_id)->item_image;
	        	}

	        	$img_src = (!empty($item_image) && file_exists($item_image)) ? base_url(return_item_image_thumb($item_image)) : base_url('theme/images/no_image.png');

	        	$table .= '<div class="col-md-3 col-xs-6 " id="item_parent_'.$i.'" '.$disabled.' data-toggle="tooltip" style="padding-left:5px;padding-right:5px;" title="'.$res2->item_name.'">
	          <div class="box box-default item_box" id="div_'.$res2->id.'" onclick="'.$str.'"
	          				data-item-id="'.$res2->id.'"
	          				data-item-name="'.$res2->item_name.'"
	          				data-item-available-qty="'.$w_stock.'"
	          				data-item-sales-price="'.$item_sales_price.'"
	          				data-item-cost="'.$item_cost.'"
	          				data-item-tax-id="'.$item_tax_id.'"
	          				data-item-tax-type="'.$item_tax_type.'"
	          				data-item-tax-value="'.$item_tax.'"
	          				data-item-tax-name="'.$item_tax_name.'"
	          				data-item-tax-amt="'.$item_tax_amt.'"
	          				data-service_bit="'.$service_bit.'"
	           				style="max-height: 150px;min-height: 150px;cursor: pointer;'.$bg_color.'">
	           	<span class="label label-danger push-right" style="font-weight: bold;font-family: sans-serif;" data-toggle="tooltip" title="'.$label_title.'">'.$label.'</span>
	          

	            <div class="box-body box-profile">
	            	<center>
	            	<img class=" img-responsive item_image" style="border: 1px solid gray;"  src="'.$img_src.'" alt="Item picture">
	              </center>
	              <lable class="text-center search_item" style="font-weight: bold;font-family: sans-serif;" id="item_'.$i.'">'.substr($res2->item_name,0,25).'</label><br>
	              <span class="" style="font-family: sans-serif;font-size:150%; " >'.$CI->currency($item_sales_price).'
	              </span>
	            </div>


	          </div>
	        </div>';
	          $i++;
	          }//for end
	          return $table;
	      }//if num_rows() end
	     
	}
	//CROSS SITE FILTER
	public function xss_html_filter($input){
		return $this->security->xss_clean(html_escape($input));
	}
	
	//Save Sales
	public function pos_save_update(){//Save or update sales
		$this->db->trans_begin();
		extract($this->xss_html_filter(array_merge($this->data,$_POST,$_GET)));
		//print_r($this->xss_html_filter(array_merge($this->data,$_POST,$_GET)));exit();

		//varify max sales usage of the package subscription
		validate_package_offers('max_invoices','db_sales');
		//END

		//check payment method
		if(isset($by_cash) && $by_cash==true){ //by cash payment
			$by_cash=true;
			$payment_row_count=1;
		}else{ //by multiple payments
			$by_cash=false;
		}
		//end 

		$store_id=(store_module() && is_admin()) ? $store_id : get_current_store_id();
		$rowcount 			=$hidden_rowcount;
		$sales_date 		=date("Y-m-d",strtotime($CUR_DATE));
		//$points 			= (empty($points_use)) ? 'NULL' : $points_use;
		$discount_input 	= (empty($discount_input)) ? 'NULL' : $discount_input;
		$tot_disc 		= (empty($tot_disc) || $tot_disc==0) ? 'NULL' : $tot_disc;
		$tot_grand 		= (empty($tot_grand)) ? 'NULL' : $tot_grand;
		//$tot_grand		=round($tot_amt);
		$round_off = number_format($tot_grand-$tot_amt,2,'.','');
		

		//FIND CUSTOMER INFORMATION BY ITS ID
		$q1=$this->db->query("select customer_name,mobile from db_customers where id=$customer_id");
		$customer_name 	= $q1->row()->customer_name;
		$mobile 		= $q1->row()->mobile;

		$prev_item_ids = array();

		if($command=='update'){
				$sales_entry = array(
		    				'store_id' 				=> $store_id,
		    				'sales_date' 				=> $sales_date,
		    				'sales_status' 				=> 'Final',
		    				'customer_id' 				=> $customer_id,
		    				/*'warehouse_id' 				=> $warehouse_id,*/
		    				/*Discount*/
		    				'discount_to_all_input' 	=> $discount_input,
		    				'discount_to_all_type' 		=> $discount_type,
		    				'tot_discount_to_all_amt' 	=> $tot_disc,
		    				/*Subtotal & Total */
		    				'subtotal' 					=> $tot_amt,
		    				'round_off' 				=> $round_off,
		    				'grand_total' 				=> $tot_grand,
		    				'sales_note' 				=> $sales_note,
		    			);
				$sales_entry['warehouse_id']=(warehouse_module() && warehouse_count()>1) ? $warehouse_id : get_store_warehouse_id();
				$q3 = $this->db->where('id',$sales_id)->update('db_sales', $sales_entry);

				##############################################START
				//FIND THE PREVIOUSE ITEM LIST ID'S
				$prev_item_ids = $this->db->select("item_id")->from("db_salesitems")->where("sales_id",$sales_id)->get()->result_array();
				##############################################END
				
				$q11=$this->db->query("delete from db_salesitems where sales_id='$sales_id'");
				$q12=$this->db->query("delete from db_salespayments where sales_id='$sales_id'");
				if(!$q11 || !$q12){
					return "failed";
				}
		}
		else{
			$this->db->query("ALTER TABLE db_sales AUTO_INCREMENT = 1");

			$sales_entry = array(
							'count_id' 					=> get_count_id('db_sales'),  
		    				'sales_code' 				=> get_init_code('sales'), 
		    				'store_id' 				=> $store_id,
		    				'sales_date' 				=> $sales_date,
		    				'sales_status' 				=> 'Final',
		    				'customer_id' 				=> $customer_id,
		    				/*'warehouse_id' 				=> $warehouse_id,*/
		    				/*Discount*/
		    				'discount_to_all_input' 	=> $discount_input,
		    				'discount_to_all_type' 		=> $discount_type,
		    				'tot_discount_to_all_amt' 	=> $tot_disc,
		    				/*Subtotal & Total */
		    				'subtotal' 					=> $tot_amt,
		    				'round_off' 				=> $round_off,
		    				'grand_total' 				=> $tot_grand,
		    				/*System Info*/
		    				'created_date' 				=> $CUR_DATE,
		    				'created_time' 				=> $CUR_TIME,
		    				'created_by' 				=> $CUR_USERNAME,
		    				'system_ip' 				=> $SYSTEM_IP,
		    				'system_name' 				=> $SYSTEM_NAME,
		    				'pos' 						=> 1,
		    				'status' 					=> 1,
		    				'sales_note' 				=> $sales_note,
		    			);
			$sales_entry['warehouse_id']=(warehouse_module() && warehouse_count()>1) ? $warehouse_id : get_store_warehouse_id();
			$q3 = $this->db->insert('db_sales', $sales_entry);
			$sales_id = $this->db->insert_id();
		}
		//Import post data from form
		for($i=0;$i<$rowcount;$i++){
		
			if(isset($_REQUEST['tr_item_id_'.$i]) && trim($_REQUEST['tr_item_id_'.$i])!=''){
			
				//RECEIVE VALUES FROM FORM
				$item_id 	=$this->xss_html_filter(trim($_REQUEST['tr_item_id_'.$i]));
				$sales_qty 	=$this->xss_html_filter(trim($_REQUEST['item_qty_'.$item_id]));
				$price_per_unit =$this->xss_html_filter(trim($_REQUEST['sales_price_'.$i]));
				$tax_amt =$this->xss_html_filter(trim($_REQUEST['td_data_'.$i.'_11']));
				$tax_type =$this->xss_html_filter(trim($_REQUEST['tr_tax_type_'.$i]));
				$tax_id =$this->xss_html_filter(trim($_REQUEST['tr_tax_id_'.$i]));
				$tax_value =$this->xss_html_filter(trim($_REQUEST['tr_tax_value_'.$i]));//%
				$total_cost =$this->xss_html_filter(trim($_REQUEST['td_data_'.$i.'_4']));
				$description =$this->xss_html_filter(trim($_REQUEST['description_'.$i]));
				
				$discount_type =$this->xss_html_filter(trim($_REQUEST['item_discount_type_'.$i]));
				$discount_input =$this->xss_html_filter(trim($_REQUEST['item_discount_input_'.$i]));
				$discount_amt =$this->xss_html_filter(trim($_REQUEST['item_discount_'.$i]));

				if($tax_type=='Exclusive'){
					$single_unit_total_cost = $price_per_unit + ($tax_value * $price_per_unit / 100);
				}
				else{//Inclusive
					$single_unit_total_cost =$price_per_unit;
				}

				
				if($tax_id=='' || $tax_id==0){$tax_id=null;}
				if($tax_amt=='' || $tax_amt==0){$tax_amt=null;}
				if($total_cost=='' || $total_cost==0){$total_cost=null;}
				
				
				/* ******************************** */
				//Find the qty available or not
				$item_details = get_item_details($item_id);
				$item_name = $item_details->item_name;
				$service_bit = $item_details->service_bit;

				
				/*$current_stock_of_item = total_available_qty_items_of_warehouse($warehouse_id,null,$item_id);
				if($current_stock_of_item<$sales_qty && $service_bit==0){
					return $item_name." has only ".$current_stock_of_item." in Stock!!";exit;
				}*/
				
				$salesitems_entry = array(
		    				'store_id' 			=> $store_id, 
		    				'sales_id' 			=> $sales_id, 
		    				'sales_status'		=> 'Final', 
		    				'item_id' 			=> $item_id, 
		    				'description' 		=> $description, 
		    				'sales_qty' 		=> $sales_qty,
		    				'price_per_unit' 	=> $price_per_unit,
		    				'tax_id' 			=> $tax_id,
		    				'tax_amt' 			=> $tax_amt,
		    				'tax_type' 			=> $tax_type,
		    				'discount_type' 	=> $discount_type,
		    				'discount_input' 	=> $discount_input,
		    				'discount_amt' 		=> $discount_amt,
		    				'unit_total_cost' 	=> $single_unit_total_cost,
		    				'total_cost' 		=> $total_cost,
		    				'status'	 		=> 1,
		    				'seller_points'		=> get_seller_points($item_id) * $sales_qty,
		    			);
				$q4 = $this->db->insert('db_salesitems', $salesitems_entry);

				$q11=$this->update_items_quantity($item_id);
				if(!$q11){
					return "failed";
				}

			}
		
		}//for end
		
		if($pay_all=='true'){
			$by_cash=true;
			$payment_row_count=1;
		}
		else{
			$by_cash=false;
		}

		//UPDATE CUSTMER MULTPLE PAYMENTS
		for($i=1;$i<=$payment_row_count;$i++){
		
			if((isset($_REQUEST['amount_'.$i]) && trim($_REQUEST['amount_'.$i])!='') || ($by_cash==true)){

				if($by_cash==true){
					//RECEIVE VALUES FROM FORM
					$amount 		=$tot_grand;
					$payment_type 	='Cash';
					$payment_note 	='Paid By Cash';
				}
				else{
					//RECEIVE VALUES FROM FORM
					$amount 		=$this->xss_html_filter(trim($_REQUEST['amount_'.$i]));
					$payment_type 	=$this->xss_html_filter(trim($_REQUEST['payment_type_'.$i]));
					$payment_note 	=$this->xss_html_filter(trim($_REQUEST['payment_note_'.$i]));
				}

				$account_id 	=$this->xss_html_filter(trim($_REQUEST['account_id_'.$i]));
				//If amount is greater than paid amount
				$change_return=0;
				if($amount>$tot_grand){
					$change_return =$amount-$tot_grand;
					$amount =$tot_grand;
				}
				//end
				$payment_code=get_init_code('sales_payment');
				$salespayments_entry = array(
					'payment_code' 		=> $payment_code,
		    		'count_id'	  		=> get_count_id('db_salespayments'),
					'store_id' 		=> $store_id, 
					'sales_id' 		=> $sales_id, 
					'payment_date'		=> $sales_date,//Current Payment with sales entry
					'payment_type' 		=> $payment_type,
					'payment' 			=> $amount,
					'payment_note' 		=> $payment_note,
					'created_date' 		=> $CUR_DATE,
    				'created_time' 		=> $CUR_TIME,
    				'created_by' 		=> $CUR_USERNAME,
    				'system_ip' 		=> $SYSTEM_IP,
    				'system_name' 		=> $SYSTEM_NAME,
    				'change_return' 	=> $change_return,
    				'status' 			=> 1,
    				'customer_id' 		=> $customer_id,
    				'account_id' 		=> (empty($account_id)) ? null : $account_id,
				);
			  
			  $q7 = $this->db->insert('db_salespayments', $salespayments_entry);

			    if(!$q7)
				{
					return "failed";
				}
				//Set the payment to specified account
				if(!empty($account_id)){
					//ACCOUNT INSERT
					$insert_bit = insert_account_transaction(array(
																'transaction_type'  	=> 'SALES PAYMENT',
																'reference_table_id'  	=> $this->db->insert_id(),
																'debit_account_id'  	=> null,
																'credit_account_id'  	=> $account_id,
																'debit_amt'  			=> 0,
																'credit_amt'  			=> $amount,
																'process'  				=> 'SAVE',
																'note'  				=> $payment_note,
																'transaction_date'  	=> $CUR_DATE,
																'payment_code'  		=> $payment_code,
																'customer_id'  			=> $customer_id,
																'supplier_id'  			=> null,
														));
					if(!$insert_bit){
						return "failed";
					}
				}
				//end
				
			}//if()
		
		}//for end

	
		//UPDATE itemS QUANTITY IN itemS TABLE
		$this->load->model('sales_model');				
		$q6=$this->sales_model->update_sales_payment_status($sales_id);
		if(!$q6){
			return "failed";
		}

		//Calculate Opening balance before and after invoice
		$q7=calculate_ob_of_customer($sales_id,$customer_id);
		if(!$q7){
			return "failed";
		}

		if(isset($hidden_invoice_id) && !empty($hidden_invoice_id)){
			$q13=$this->hold_invoice_delete($hidden_invoice_id);
			if(!$q13){
				return "failed";
			}
		}
		
		
		$sms_info='';
		if(isset($send_sms) && $customer_id!=1){
			if(send_sms_using_template($sales_id,1)==true){
				$sms_info = 'SMS Has been Sent!';
			}else{
				$sms_info = 'Failed to Send SMS';
			}
		}

		##############################################START
		//FIND THE PREVIOUSE ITEM LIST ID'S
		$curr_item_ids = $this->db->select("item_id")->from("db_salesitems")->where("sales_id",$sales_id)->get()->result_array();
		$two_array = array_merge($prev_item_ids,$curr_item_ids);

		/*Update items in all warehouses of the item*/
		$q7=update_warehouse_items($two_array);
		if(!$q7){
			return "failed";
		}
		##############################################END

		

		//Dont save if invoice credit limit exceeds
		if(!check_credit_limit_with_invoice($customer_id,$sales_id)){
			return 'failed';
		}

		
		//COMMIT RECORD
		$this->db->trans_commit();
		
		$this->session->set_flashdata('success', 'Success!! Sales Created Successfully!'.$sms_info);
        return "success<<<###>>>$sales_id";


	}

	public function update_items_quantity($item_id){
		//FIND IS IS SERVICE OR NOT
		$service_bit=$this->db->query("select service_bit from db_items where id='$item_id'")->row()->service_bit;
		if($service_bit==1){
			return true;
		}
		
		//UPDATE itemS QUANTITY IN itemS TABLE
		$q7=$this->db->query("select COALESCE(SUM(adjustment_qty),0) as stock_qty from db_stockadjustmentitems where item_id='$item_id'");
		$stock_qty=$q7->row()->stock_qty;

		$q8=$this->db->query("select COALESCE(SUM(purchase_qty),0) as pu_tot_qty from db_purchaseitems where item_id='$item_id' and purchase_status='Received'");
		$pu_tot_qty=$q8->row()->pu_tot_qty;
		
		$q9=$this->db->query("select coalesce(SUM(sales_qty),0) as sl_tot_qty from db_salesitems where item_id='$item_id' and sales_status='Final'");
		$sl_tot_qty=$q9->row()->sl_tot_qty;

		/*Fid Return Items Count*/
		$q6=$this->db->query("select COALESCE(SUM(return_qty),0) as pu_return_tot_qty from db_purchaseitemsreturn where item_id='$item_id' ");/*and purchase_id is null */
		$pu_return_tot_qty=$q6->row()->pu_return_tot_qty;

		/*Fid Return Items Count*/
		$q6=$this->db->query("select COALESCE(SUM(return_qty),0) as sl_return_tot_qty from db_salesitemsreturn where item_id='$item_id' ");/*and sales_id is null */
		$sl_return_tot_qty=$q6->row()->sl_return_tot_qty;

		$stock=((($stock_qty+$pu_tot_qty)-$sl_tot_qty)+$sl_return_tot_qty)-$pu_return_tot_qty;
		$q7=$this->db->query("update db_items set stock=$stock where id='$item_id'");
		if($q7){
			return true;
		}
		else{
			return false;
		}
	}	
	

	public function edit_pos($sales_id){
		$data=$this->data;
		extract($data);
	     $q2=$this->db->query("select * from db_sales where id='$sales_id'");
	    if($q2->num_rows()>0){
	      $res2=$q2->row();
	      $sales_date=show_date($res2->sales_date);
	      $customer_id=$res2->customer_id;
	      $discount_input=$res2->discount_to_all_input;
	      $discount_type=$res2->discount_to_all_type;
	      $grand_total=$res2->grand_total;
	      $store_id=$res2->store_id;
	      $warehouse_id=$res2->warehouse_id;
	      $sales_note=$res2->sales_note;

	      $q3=$this->db->query("SELECT * FROM db_salesitems WHERE sales_id='$sales_id'");
		  $rows=$q3->num_rows();
		  if($rows>0){
		  	$i=0;
		  	
		  	foreach ($q3->result() as $res3) { 
		  		$q5=$this->db->query("select * from db_items where id=".$res3->item_id);
		  		$price_per_unit = $res3->price_per_unit;
		  		$description = $res3->description;
		  		$service_bit = $q5->row()->service_bit;
		  		$stock=$q5->row()->stock + $res3->sales_qty;

		  		$item_discount = $res3->discount_amt;
		  		$item_discount_type = $res3->discount_type;
		  		$item_discount_input = $res3->discount_input;


		  		$q6=$this->db->query("select * from db_tax where id=".$res3->tax_id)->row();

		  		//$item_tax_type = $q5->row()->tax_type;
	        	/*if($item_tax_type=='Exclusive'){
	        		$per_item_price_inc_tax=$price_per_unit+(($price_per_unit*$q5->row()->tax)/100);
				}
				else{//Inclusive	
					$per_item_price_inc_tax=$price_per_unit;
				}*/
				$per_item_price_inc_tax=$price_per_unit;
				$per_item_price_inc_tax=number_format($per_item_price_inc_tax,2,'.','');	

				$tax_amt = $res3->tax_amt;
				$tax_type = $res3->tax_type;
				$tax_id = $res3->tax_id;
				$tax_value = $q6->tax;

		  		$quantity        ='<div class="input-group input-group-sm"><span class="input-group-btn"><button onclick="decrement_qty('.$res3->item_id.','.$i.')" type="button" class="btn btn-default btn-flat"><i class="fa fa-minus text-danger"></i></button></span>';
			    $quantity       .='<input typ="text" value="'.$res3->sales_qty.'" class="form-control min_width" onkeyup="item_qty_input('.$res3->item_id.','.$i.')" id="item_qty_'.$res3->item_id.'" name="item_qty_'.$res3->item_id.'">';
			    $quantity       .='<span class="input-group-btn"><button onclick="increment_qty('.$res3->item_id.','.$i.')" type="button" class="btn btn-default btn-flat"><i class="fa fa-plus text-success"></i></button></span></div>';
			    $sub_total       =$res3->total_cost;
			    $remove_btn      ='<a class="fa fa-fw fa-trash-o text-red" style="cursor: pointer;font-size: 20px;" onclick="removerow('.$i.')" title="Delete Item?"></a>';
			    
		  		echo '<tr id="row_'.$i.'" data-row="0" data-item-id="'.$res3->item_id.'" >'; /*item id */
		  		echo '<td id="td_'.$i.'_0">
		  		<a data-toggle="tooltip" title="Click to Change Tax" class="pointer" id="td_data_'.$i.'_0" onclick="show_sales_item_modal('.$i.')">'.$q5->row()->item_name.'</a>
		  		</td>';  /*td_0_0 item name*/
		  		echo '<td id="td_'.$i.'_1">'.$stock.'</td>';  /*td_0_1 item available qty*/
		  		echo '<td id="td_'.$i.'_2">'.$quantity.'</td>';    /*td_0_2 item available qty */

		  		$info = '<input id="sales_price_'.$i.'" onblur="set_to_original('.$i.','.$q5->row()->purchase_price.')" onkeyup="update_price('.$i.','.$q5->row()->purchase_price.')" name="sales_price_'.$i.'" type="text" class="form-control min_width" value="'.$per_item_price_inc_tax.'">';

		  		echo '<td id="td_'.$i.'_3" class="text-right" >'.$info.'</td>';    /*td_0_3 item sales price */

		  		$info = '<input data-toggle="tooltip" title="Click to Change" onclick="show_sales_item_modal('.$i.')" id="item_discount_'.$i.'" readonly name="item_discount_'.$i.'" type="text" class="form-control text-left no-padding" value="'.$item_discount.'">';

		  		echo '<td id="td_'.$i.'_6" class="text-right" >'.$info.'</td>';

		  		echo '<td id="td_'.$i.'_11"><input data-toggle="tooltip" title="Click to Change" id="td_data_'.$i.'_11" onclick="show_sales_item_modal('.$i.')" name="td_data_'.$i.'_11" type="text" class="form-control no-padding pointer min_width" readonly value="'.$tax_amt.'"></td>';
		  		echo '<td id="td_'.$i.'_4" class="text-right" >
		  		<input data-toggle="tooltip" title="Total" id="td_data_'.$i.'_4" name="td_data_'.$i.'_4" type="text" class="form-control no-padding pointer min_width" readonly value="'.store_number_format($sub_total,false).'"></td>';    /*td_0_4 item sub_total */
		  		echo '<td id="td_'.$i.'_5">'.$remove_btn.'</td>';    /* td_0_5 item gst_amt  */

		  		echo '<input type="hidden" name="tr_item_id_'.$i.'" id="tr_item_id_'.$i.'" value="'.$res3->item_id.'">'; 
		  		echo '<input type="hidden" id="tr_item_per_'.$i.'" name="tr_item_per_'.$i.'" value="'.$q6->tax.'">';
		  		echo '<input type="hidden" id="tr_sales_price_temp_'.$i.'" name="tr_sales_price_temp_'.$i.'" value="'.$per_item_price_inc_tax.'">';
		  		echo '</tr>';
		  		echo '<input type="hidden" id="tr_tax_type_'.$i.'" name="tr_tax_type_'.$i.'" value="'.$tax_type.'">';
        		echo '<input type="hidden" id="tr_tax_id_'.$i.'" name="tr_tax_id_'.$i.'" value="'.$tax_id.'">';
        		echo '<input type="hidden" id="tr_tax_value_'.$i.'" name="tr_tax_value_'.$i.'" value="'.$tax_value.'">';
        		echo '<input type="hidden" id="description_'.$i.'" name="description_'.$i.'" value="'.$description.'">';
        		echo '<input type="hidden" id="service_bit_'.$i.'" name="service_bit_'.$i.'" value="'.$service_bit.'">';
		  		echo '<input type="hidden" id="item_discount_type_'.$i.'" name="item_discount_type_'.$i.'" value="'.$item_discount_type.'">';
        		echo '<input type="hidden" id="item_discount_input_'.$i.'" name="item_discount_input_'.$i.'" value="'.$item_discount_input.'">';
		  		$i++;
		  	}//foreach() end

		  	echo "<<<###>>>".$discount_input."<<<###>>>".$discount_type."<<<###>>>".$customer_id."<<<###>>>".$store_id."<<<###>>>".$warehouse_id."<<<###>>>".$sales_note;

		  }//if ()
		 
	    }
	    else{
	      print "Record Not Available";
	    }
	     
	}//edit_pos()

	
	/* ######################################## HOLD INVOICE ############################# */
	
	public function hold_invoice_list(){
		$data=$this->data;
		extract($data);
		extract($_POST);
		  $i=0;
		  $str ='';
	      $q2=$this->db->query("select * from db_hold where store_id=".get_current_store_id()." order by id desc");
	      if($q2->num_rows()>0){
	        foreach($q2->result() as $res2){
	     
                  $str =$str."<tr>";
                  $str =$str."<td>".$res2->id."</td>";
                  $str =$str."<td>".show_date($res2->sales_date)."</td>";
                  $str =$str."<td>".$res2->reference_id."</td>";
                  $str =$str."<td>";
                  	$str =$str.'<a class="fa fa-fw fa-trash-o text-red" style="cursor: pointer;font-size: 20px;" onclick="hold_invoice_delete('.$res2->id.')" title="Delete Invoive?"></a>';
                  	$str =$str.'<a class="fa fa-fw fa-edit text-success" style="cursor: pointer;font-size: 20px;" onclick="hold_invoice_edit('.$res2->id.')" title="Edit Invoive?"></a>';
                  $str =$str."</td>";
                $str =$str."</tr>";
	     
	          $i++;
	          }//for end
	      }//if num_rows() end
	      else{
	      	
	      	$str =$str."<tr>";
	      		$str =$str.'<td colspan="4" class="text-danger text-center">No Records Found</td>';
	      	$str =$str.'</tr>';
	      	
	      }
		return $str;
	}
	public function hold_invoice_delete($id){
		$this->db->trans_begin();
		$q1=$this->db->query("DELETE from db_hold where id='$id' and store_id=".get_current_store_id());
		if(!$q1){
			return "failed";
		}
		//COMMIT RECORD
		$this->db->trans_commit();
        return "success";

	}


	public function hold_invoice_edit(){
		$data=$this->data;
		extract($this->xss_html_filter(array_merge($this->data,$_POST,$_GET)));
	     $q2=$this->db->query("select * from db_hold where id='$hold_id'");
	    if($q2->num_rows()>0){
	      $res2=$q2->row();
	      $sales_date=show_date($res2->sales_date);
	      $customer_id=$res2->customer_id;
	      $discount_input=$res2->discount_to_all_input;
	      $discount_type=$res2->discount_to_all_type;
	      $grand_total=$res2->grand_total;
	      $store_id=$res2->store_id;
	      $warehouse_id=$res2->warehouse_id;
	      $sales_note=$res2->sales_note;

	      $q3=$this->db->query("SELECT * FROM db_holditems WHERE hold_id='$hold_id'");
		  $rows=$q3->num_rows();
		  if($rows>0){
		  	$i=0;
		  	
		  	foreach ($q3->result() as $res3) { 
		  		$q5=$this->db->query("select * from db_items where id=".$res3->item_id);
		  		$price_per_unit = $res3->price_per_unit;
		  		$description = $res3->description;
		  		$service_bit = $q5->row()->service_bit;
		  		$stock=$q5->row()->stock + $res3->sales_qty;

		  		$item_discount = $res3->discount_amt;
		  		$item_discount_type = $res3->discount_type;
		  		$item_discount_input = $res3->discount_input;


		  		$q6=$this->db->query("select * from db_tax where id=".$res3->tax_id)->row();

		  		
				$per_item_price_inc_tax=$price_per_unit;
				$per_item_price_inc_tax=number_format($per_item_price_inc_tax,2,'.','');	

				$tax_amt = $res3->tax_amt;
				$tax_type = $res3->tax_type;
				$tax_id = $res3->tax_id;
				$tax_value = $q6->tax;

		  		$quantity        ='<div class="input-group input-group-sm"><span class="input-group-btn"><button onclick="decrement_qty('.$res3->item_id.','.$i.')" type="button" class="btn btn-default btn-flat"><i class="fa fa-minus text-danger"></i></button></span>';
			    $quantity       .='<input typ="text" value="'.$res3->sales_qty.'" class="form-control min_width" onkeyup="item_qty_input('.$res3->item_id.','.$i.')" id="item_qty_'.$res3->item_id.'" name="item_qty_'.$res3->item_id.'">';
			    $quantity       .='<span class="input-group-btn"><button onclick="increment_qty('.$res3->item_id.','.$i.')" type="button" class="btn btn-default btn-flat"><i class="fa fa-plus text-success"></i></button></span></div>';
			    $sub_total       =$res3->total_cost;
			    $remove_btn      ='<a class="fa fa-fw fa-trash-o text-red" style="cursor: pointer;font-size: 20px;" onclick="removerow('.$i.')" title="Delete Item?"></a>';
			    
		  		echo '<tr id="row_'.$i.'" data-row="0" data-item-id="'.$res3->item_id.'" >'; /*item id */
		  		echo '<td id="td_'.$i.'_0">
		  		<a data-toggle="tooltip" title="Click to Change Tax" class="pointer" id="td_data_'.$i.'_0" onclick="show_sales_item_modal('.$i.')">'.$q5->row()->item_name.'</a>
		  		</td>';  /*td_0_0 item name*/
		  		echo '<td id="td_'.$i.'_1">'.$stock.'</td>';  /*td_0_1 item available qty*/
		  		echo '<td id="td_'.$i.'_2">'.$quantity.'</td>';    /*td_0_2 item available qty */

		  		$info = '<input id="sales_price_'.$i.'" onblur="set_to_original('.$i.','.$q5->row()->purchase_price.')" onkeyup="update_price('.$i.','.$q5->row()->purchase_price.')" name="sales_price_'.$i.'" type="text" class="form-control min_width" value="'.$per_item_price_inc_tax.'">';

		  		echo '<td id="td_'.$i.'_3" class="text-right" >'.$info.'</td>';    /*td_0_3 item sales price */

		  		$info = '<input data-toggle="tooltip" title="Click to Change" onclick="show_sales_item_modal('.$i.')" id="item_discount_'.$i.'" readonly name="item_discount_'.$i.'" type="text" class="form-control text-left no-padding" value="'.$item_discount.'">';

		  		echo '<td id="td_'.$i.'_6" class="text-right" >'.$info.'</td>';

		  		echo '<td id="td_'.$i.'_11"><input data-toggle="tooltip" title="Click to Change" id="td_data_'.$i.'_11" onclick="show_sales_item_modal('.$i.')" name="td_data_'.$i.'_11" type="text" class="form-control no-padding pointer min_width" readonly value="'.$tax_amt.'"></td>';
		  		echo '<td id="td_'.$i.'_4" class="text-right" >
		  		<input data-toggle="tooltip" title="Total" id="td_data_'.$i.'_4" name="td_data_'.$i.'_4" type="text" class="form-control no-padding pointer min_width" readonly value="'.store_number_format($sub_total,false).'"></td>';    /*td_0_4 item sub_total */
		  		echo '<td id="td_'.$i.'_5">'.$remove_btn.'</td>';    /* td_0_5 item gst_amt  */

		  		echo '<input type="hidden" name="tr_item_id_'.$i.'" id="tr_item_id_'.$i.'" value="'.$res3->item_id.'">'; 
		  		echo '<input type="hidden" id="tr_item_per_'.$i.'" name="tr_item_per_'.$i.'" value="'.$q6->tax.'">';
		  		echo '<input type="hidden" id="tr_sales_price_temp_'.$i.'" name="tr_sales_price_temp_'.$i.'" value="'.$per_item_price_inc_tax.'">';
		  		echo '</tr>';
		  		echo '<input type="hidden" id="tr_tax_type_'.$i.'" name="tr_tax_type_'.$i.'" value="'.$tax_type.'">';
        		echo '<input type="hidden" id="tr_tax_id_'.$i.'" name="tr_tax_id_'.$i.'" value="'.$tax_id.'">';
        		echo '<input type="hidden" id="tr_tax_value_'.$i.'" name="tr_tax_value_'.$i.'" value="'.$tax_value.'">';
        		echo '<input type="hidden" id="description_'.$i.'" name="description_'.$i.'" value="'.$description.'">';
        		echo '<input type="hidden" id="service_bit_'.$i.'" name="service_bit_'.$i.'" value="'.$service_bit.'">';
		  		echo '<input type="hidden" id="item_discount_type_'.$i.'" name="item_discount_type_'.$i.'" value="'.$item_discount_type.'">';
        		echo '<input type="hidden" id="item_discount_input_'.$i.'" name="item_discount_input_'.$i.'" value="'.$item_discount_input.'">';
		  		$i++;
		  	}//foreach() end

		  	echo "<<<###>>>".$discount_input."<<<###>>>".$discount_type."<<<###>>>".$customer_id."<<<###>>>".$store_id."<<<###>>>".$warehouse_id."<<<###>>>".$sales_note."<<<###>>>".$hold_id;

		  }//if ()
		 
	    }
	    else{
	      print "Record Not Available";
	    }
	     
	}//edit_pos()


	public function hold_list_save_update(){//Save or update sales
		$this->db->trans_begin();
		extract($this->xss_html_filter(array_merge($this->data,$_POST,$_GET)));
		//print_r($this->xss_html_filter(array_merge($this->data,$_POST,$_GET)));exit();

		$store_id= get_current_store_id();
		$rowcount 			=$hidden_rowcount;
		$sales_date 		=date("Y-m-d",strtotime($CUR_DATE));
		//$points 			= (empty($points_use)) ? 'NULL' : $points_use;
		$discount_input 	= (empty($discount_input)) ? 'NULL' : $discount_input;
		$tot_disc 		= (empty($tot_disc) || $tot_disc==0) ? 'NULL' : $tot_disc;
		$tot_grand 		= (empty($tot_grand)) ? 'NULL' : $tot_grand;
		//$tot_grand		=round($tot_amt);
		$round_off = number_format($tot_grand-$tot_amt,2,'.','');
		

		$prev_item_ids = array();


		$tot = $this->db->select("count(*) as tot")->where("store_id",$store_id)->where("reference_id",$reference_id)->get("db_hold")->row()->tot;
		if($tot>0){
			$q11=$this->db->query("delete from db_hold where reference_id='$reference_id' and store_id=".$store_id);
			if(!$q11){
				return "failed";
			}
		}
		

		$sales_entry = array(
						'reference_id' 				=> $reference_id,
	    				'store_id' 					=> $store_id,
	    				'sales_date' 				=> $sales_date,
	    				'sales_status' 				=> 'Final',
	    				'customer_id' 				=> $customer_id,
	    				/*Discount*/
	    				'discount_to_all_input' 	=> $discount_input,
	    				'discount_to_all_type' 		=> $discount_type,
	    				'tot_discount_to_all_amt' 	=> $tot_disc,
	    				/*Subtotal & Total */
	    				'subtotal' 					=> $tot_amt,
	    				'round_off' 				=> $round_off,
	    				'grand_total' 				=> $tot_grand,
	    				'pos' 						=> 1,
	    				'sales_note' 				=> $sales_note,
	    			);
		$sales_entry['warehouse_id']=(warehouse_module() && warehouse_count()>1) ? $warehouse_id : get_store_warehouse_id();
		$q3 = $this->db->insert('db_hold', $sales_entry);
		$hold_id = $this->db->insert_id();
		
		//Import post data from form
		for($i=0;$i<$rowcount;$i++){
		
			if(isset($_REQUEST['tr_item_id_'.$i]) && trim($_REQUEST['tr_item_id_'.$i])!=''){
			
				//RECEIVE VALUES FROM FORM
				$item_id 	=$this->xss_html_filter(trim($_REQUEST['tr_item_id_'.$i]));
				$sales_qty 	=$this->xss_html_filter(trim($_REQUEST['item_qty_'.$item_id]));
				$price_per_unit =$this->xss_html_filter(trim($_REQUEST['sales_price_'.$i]));
				$tax_amt =$this->xss_html_filter(trim($_REQUEST['td_data_'.$i.'_11']));
				$tax_type =$this->xss_html_filter(trim($_REQUEST['tr_tax_type_'.$i]));
				$tax_id =$this->xss_html_filter(trim($_REQUEST['tr_tax_id_'.$i]));
				$tax_value =$this->xss_html_filter(trim($_REQUEST['tr_tax_value_'.$i]));//%
				$total_cost =$this->xss_html_filter(trim($_REQUEST['td_data_'.$i.'_4']));
				$description =$this->xss_html_filter(trim($_REQUEST['description_'.$i]));
				
				$discount_type =$this->xss_html_filter(trim($_REQUEST['item_discount_type_'.$i]));
				$discount_input =$this->xss_html_filter(trim($_REQUEST['item_discount_input_'.$i]));
				$discount_amt =$this->xss_html_filter(trim($_REQUEST['item_discount_'.$i]));

				if($tax_type=='Exclusive'){
					$single_unit_total_cost = $price_per_unit + ($tax_value * $price_per_unit / 100);
				}
				else{//Inclusive
					$single_unit_total_cost =$price_per_unit;
				}

				
				if($tax_id=='' || $tax_id==0){$tax_id=null;}
				if($tax_amt=='' || $tax_amt==0){$tax_amt=null;}
				if($total_cost=='' || $total_cost==0){$total_cost=null;}
				
				
				/* ******************************** */
				//Find the qty available or not
				$item_details = get_item_details($item_id);
				$item_name = $item_details->item_name;
				$service_bit = $item_details->service_bit;
				$current_stock_of_item = total_available_qty_items_of_warehouse($warehouse_id,null,$item_id);
				if($current_stock_of_item<$sales_qty && $service_bit==0){
					return $item_name." has only ".$current_stock_of_item." in Stock!!";exit;
				}
				
				$salesitems_entry = array(
							'store_id' 			=> $store_id,
		    				'hold_id' 			=> $hold_id, 
		    				'item_id' 			=> $item_id, 
		    				'description' 		=> $description, 
		    				'sales_qty' 		=> $sales_qty,
		    				'price_per_unit' 	=> $price_per_unit,
		    				'tax_id' 			=> $tax_id,
		    				'tax_amt' 			=> $tax_amt,
		    				'tax_type' 			=> $tax_type,
		    				'discount_type' 	=> $discount_type,
		    				'discount_input' 	=> $discount_input,
		    				'discount_amt' 		=> $discount_amt,
		    				'unit_total_cost' 	=> $single_unit_total_cost,
		    				'total_cost' 		=> $total_cost,
		    			);
				$q4 = $this->db->insert('db_holditems', $salesitems_entry);

				$q11=$this->update_items_quantity($item_id);
				if(!$q11){
					return "failed";
				}

			}
		
		}//for end
		

		//COMMIT RECORD
		$this->db->trans_commit();
		
		$this->session->set_flashdata('success', 'Success!! Sales Created Successfully!');
        return "success";


	}
}
