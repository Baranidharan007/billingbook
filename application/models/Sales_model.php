<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sales_model extends CI_Model {

	//Datatable start
	var $table = 'db_sales as a';
	var $column_order = array( 
							'a.id',
							'a.sales_date',
							'a.sales_code',
							'a.reference_no',
							'b.customer_name',
							'a.grand_total',
							'a.paid_amount',
							'a.payment_status',
							'a.created_by',
							'a.return_bit',
							'a.pos',
							'a.store_id',
							'a.quotation_id',
							'a.due_date',
							); //set column field database for datatable orderable
	var $column_search = array( 
							'a.id',
							'a.sales_date',
							'a.sales_code',
							'a.reference_no',
							'b.customer_name',
							'a.grand_total',
							'a.paid_amount',
							'a.payment_status',
							'a.created_by',
							'a.return_bit',
							'a.pos',
							'a.store_id',
							'a.due_date',
							);//set column field database for datatable searchable 
	var $order = array('a.id' => 'desc'); // default order  

	public function __construct()
	{
		parent::__construct();
		$CI =& get_instance();
	}

	private function _get_datatables_query()
	{
		
		$this->db->select($this->column_order);
		$this->db->from($this->table);
		$this->db->from('db_customers as b');
		//$this->db->from('db_warehouse as c');
		$this->db->where('b.id=a.customer_id');
		//$this->db->where('c.id=a.warehouse_id');
		/*If warehouse selected*/
		$warehouse_id = $this->input->post('warehouse_id');
		if(!empty($warehouse_id)){
			$this->db->from('db_warehouse as w');
			$this->db->where('a.warehouse_id=w.id');
			$this->db->where('w.id',$warehouse_id);
		}
		//if(!is_admin()){
	      $this->db->where("a.store_id",get_current_store_id());
	    //}
	      if(!is_admin()){
	      	if($this->session->userdata('role_id')!='2'){
	      		if(!permissions('show_all_users_sales_invoices')){
	      			$this->db->where("upper(a.created_by)",strtoupper($this->session->userdata('inv_username')));
	      		}
	      	}
	      }
	     $sales_from_date = $this->input->post('sales_from_date');
	     $sales_from_date = system_fromatted_date($sales_from_date);
	     $sales_to_date = $this->input->post('sales_to_date');
	     $sales_to_date = system_fromatted_date($sales_to_date);
	     $users = $this->input->post('users');
	     if($users && !empty($users)){
	     	$this->db->where("upper(a.created_by)",strtoupper($users));
	     }
	     if($sales_from_date!='1970-01-01'){
	     	$this->db->where("a.sales_date>=",$sales_from_date);
	     }
	     if($sales_to_date!='1970-01-01'){
	     	$this->db->where("a.sales_date<=",$sales_to_date);
	     }
	    //echo $this->db->get_compiled_select();exit();
		$i = 0;
	
		foreach ($this->column_search as $item) // loop column 
		{
			if($_POST['search']['value']) // if datatable send POST for search
			{
				
				

				if($i===0) // first loop
				{
					$this->db->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.

					$this->db->like($item, $_POST['search']['value']);

				}
				else
				{
					$this->db->or_like($item, $_POST['search']['value']);
				}

				


				if(count($this->column_search) - 1 == $i) //last loop
					$this->db->group_end(); //close bracket
			}
			$i++;
		}
		
		if(isset($_POST['order'])) // here order processing
		{
			$this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		} 
		else if(isset($this->order))
		{
			$order = $this->order;
			$this->db->order_by(key($order), $order[key($order)]);
		}
	}

	function get_datatables()
	{
		$this->_get_datatables_query();
		if($_POST['length'] != -1)
		$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	function count_filtered()
	{
		$this->_get_datatables_query();
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all()
	{
		$this->db->where("store_id",get_current_store_id());
		$this->db->from($this->table);
		return $this->db->count_all_results();
	}
	//Datatable end

	public function xss_html_filter($input){
		return $this->security->xss_clean(html_escape($input));
	}

	//Save Sales
	public function verify_save_and_update(){
		//Filtering XSS and html escape from user inputs 
		extract($this->xss_html_filter(array_merge($this->data,$_POST,$_GET)));
		//echo "<pre>";print_r($this->xss_html_filter(array_merge($this->data,$_POST,$_GET)));exit();
		
		//varify max sales usage of the package subscription
		validate_package_offers('max_invoices','db_sales');
		//END

		$this->db->trans_begin();
		$sales_date=system_fromatted_date($sales_date);

		$due_date=(!empty($due_date)) ? system_fromatted_date($due_date) : NULL;
		if($due_date=='1970-01-01'){
			$due_date = NULL;
		}
		//echo $due_date;exit;
		if($other_charges_input=='' || $other_charges_input==0){$other_charges_input=null;}
	    if($other_charges_tax_id=='' || $other_charges_tax_id==0){$other_charges_tax_id=null;}
	    if($other_charges_amt=='' || $other_charges_amt==0){$other_charges_amt=null;}
	    if($discount_to_all_input=='' || $discount_to_all_input==0){$discount_to_all_input=null;}
	    if($tot_discount_to_all_amt=='' || $tot_discount_to_all_amt==0){$tot_discount_to_all_amt=null;}
	    if($tot_round_off_amt=='' || $tot_round_off_amt==0){$tot_round_off_amt=null;}

	    $prev_item_ids = array();
	    
	    if($command=='save'){//Create sales code unique if first time entry

			$this->db->query("ALTER TABLE db_sales AUTO_INCREMENT = 1");
			
		    $sales_entry = array(
		    				'sales_code' 				=> get_init_code('sales'),
		    				'count_id' 					=> get_count_id('db_sales'),  
		    				'reference_no' 				=> $reference_no, 
		    				'sales_date' 				=> $sales_date,
		    				'due_date' 				=> $due_date,
		    				'sales_status' 				=> $sales_status,
		    				'customer_id' 				=> $customer_id,
		    				/*'warehouse_id' 				=> $warehouse_id,*/
		    				/*Other Charges*/
		    				'other_charges_input' 		=> $other_charges_input,
		    				'other_charges_tax_id' 		=> $other_charges_tax_id,
		    				'other_charges_amt' 		=> $other_charges_amt,
		    				/*Discount*/
		    				'discount_to_all_input' 	=> $discount_to_all_input,
		    				'discount_to_all_type' 		=> $discount_to_all_type,
		    				'tot_discount_to_all_amt' 	=> $tot_discount_to_all_amt,
		    				/*Subtotal & Total */
		    				'subtotal' 					=> $tot_subtotal_amt,
		    				'round_off' 				=> $tot_round_off_amt,
		    				'grand_total' 				=> $tot_total_amt,
		    				'sales_note' 				=> $sales_note,
		    				/*System Info*/
		    				'created_date' 				=> $CUR_DATE,
		    				'created_time' 				=> $CUR_TIME,
		    				'created_by' 				=> $CUR_USERNAME,
		    				'system_ip' 				=> $SYSTEM_IP,
		    				'system_name' 				=> $SYSTEM_NAME,
		    				'status' 					=> 1,
		    			);
		    if(isset($quotation_id)){
				$sales_entry['quotation_id'] = $quotation_id;
			}
		    $sales_entry['store_id']=(store_module() && is_admin()) ? $store_id : get_current_store_id();  	
		    $sales_entry['warehouse_id']=(warehouse_module() && warehouse_count()>1) ? $warehouse_id : get_store_warehouse_id();
			$q1 = $this->db->insert('db_sales', $sales_entry);
			$sales_id = $this->db->insert_id();
			//SET QUOTATION STATUS
			if(isset($quotation_id)){
				$q11 = $this->db->set("sales_status",'Converted')->where("id",$quotation_id)->update("db_quotation");
			    	if(!$q11){
			    		return false;
			    	}
			}

		}
		else if($command=='update'){	
			$sales_entry = array(
		    				'reference_no' 				=> $reference_no, 
		    				'sales_date' 			=> $sales_date,
		    				'due_date' 				=> $due_date,
		    				'sales_status' 			=> $sales_status,
		    				'customer_id' 				=> $customer_id,
		    				/*'warehouse_id' 				=> $warehouse_id,*/
		    				/*Other Charges*/
		    				'other_charges_input' 		=> $other_charges_input,
		    				'other_charges_tax_id' 		=> $other_charges_tax_id,
		    				'other_charges_amt' 		=> $other_charges_amt,
		    				/*Discount*/
		    				'discount_to_all_input' 	=> $discount_to_all_input,
		    				'discount_to_all_type' 		=> $discount_to_all_type,
		    				'tot_discount_to_all_amt' 	=> $tot_discount_to_all_amt,
		    				/*Subtotal & Total */
		    				'subtotal' 					=> $tot_subtotal_amt,
		    				'round_off' 				=> $tot_round_off_amt,
		    				'grand_total' 				=> $tot_total_amt,
		    				'sales_note' 			=> $sales_note,
		    			);
			//print_r($sales_entry);exit;
			$sales_entry['store_id']=(store_module() && is_admin()) ? $store_id : get_current_store_id();  	
			$sales_entry['warehouse_id']=(warehouse_module() && warehouse_count()>1) ? $warehouse_id : get_store_warehouse_id();
			$q1 = $this->db->where('id',$sales_id)->update('db_sales', $sales_entry);

			##############################################START
			//FIND THE PREVIOUSE ITEM LIST ID'S
			$prev_item_ids = $this->db->select("item_id")->from("db_salesitems")->where("sales_id",$sales_id)->get()->result_array();
			##############################################END

			$q11=$this->db->query("delete from db_salesitems where sales_id='$sales_id'");
			if(!$q11){
				return "failed";
			}
		}
		//end

		
		//Import post data from form
		for($i=1;$i<=$rowcount;$i++){
		
			if(isset($_REQUEST['tr_item_id_'.$i]) && !empty($_REQUEST['tr_item_id_'.$i])){

				$item_id 			=$this->xss_html_filter(trim($_REQUEST['tr_item_id_'.$i]));
				$sales_qty			=$this->xss_html_filter(trim($_REQUEST['td_data_'.$i.'_3']));
				$price_per_unit 	=$this->xss_html_filter(trim($_REQUEST['td_data_'.$i.'_4']));
				$tax_id 			=$this->xss_html_filter(trim($_REQUEST['tr_tax_id_'.$i]));
				$tax_amt 			=$this->xss_html_filter(trim($_REQUEST['td_data_'.$i.'_11']));
				$unit_total_cost	=$this->xss_html_filter(trim($_REQUEST['td_data_'.$i.'_10']));
				//$discount_input	=$this->xss_html_filter(trim($_REQUEST['td_data_'.$i.'_8']));
				$total_cost			=$this->xss_html_filter(trim($_REQUEST['td_data_'.$i.'_9']));
				$tax_type			=$this->xss_html_filter(trim($_REQUEST['tr_tax_type_'.$i]));
				$unit_tax			=$this->xss_html_filter(trim($_REQUEST['tr_tax_value_'.$i]));
				$description		=$this->xss_html_filter(trim($_REQUEST['description_'.$i]));

                //$discount_input  =(empty($discount_input)) ? 0 : $discount_input;
				//$discount_amt 		=($sales_qty * $unit_total_cost)*$discount_input/100;


				$discount_type 		=$this->xss_html_filter(trim($_REQUEST['item_discount_type_'.$i]));
				$discount_input 	=$this->xss_html_filter(trim($_REQUEST['item_discount_input_'.$i]));
				$discount_amt	    =$this->xss_html_filter(trim($_REQUEST['td_data_'.$i.'_8']));//Amount

				$discount_amt_per_unit = $discount_amt/$sales_qty;
				if($tax_type=='Exclusive'){
					$single_unit_total_cost = $price_per_unit + ($unit_tax * $price_per_unit / 100);
				}
				else{//Inclusive
					$single_unit_total_cost =$price_per_unit;
				}
				$single_unit_total_cost -=$discount_amt_per_unit;


				if($tax_id=='' || $tax_id==0){$tax_id=null;}
				if($tax_amt=='' || $tax_amt==0){$tax_amt=null;}
				if($discount_input=='' || $discount_input==0){$discount_input=null;}
				//if($unit_total_cost=='' || $unit_total_cost==0){$unit_total_cost=null;}
				if($total_cost=='' || $total_cost==0){$total_cost=null;}
				
				
				$item_details = get_item_details($item_id);
				$item_name = $item_details->item_name;
				$service_bit = $item_details->service_bit;
				$current_stock_of_item = total_available_qty_items_of_warehouse($warehouse_id,null,$item_id);
				if($current_stock_of_item<$sales_qty && $service_bit==0){
					return $item_name." has only ".$current_stock_of_item." in Stock!!";exit;
				}
				
				$salesitems_entry = array(
		    				'sales_id' 			=> $sales_id, 
		    				'sales_status'		=> $sales_status, 
		    				'item_id' 			=> $item_id, 
		    				'description' 		=> $description, 
		    				'sales_qty' 		=> $sales_qty,
		    				'price_per_unit' 	=> $price_per_unit,
		    				'tax_type' 			=> $tax_type,
		    				'tax_id' 			=> $tax_id,
		    				'tax_amt' 			=> $tax_amt,
		    				'discount_input' 	=> $discount_input,
		    				'discount_amt' 		=> $discount_amt,
		    				'discount_type' 	=> $discount_type,
		    				'unit_total_cost' 	=> $single_unit_total_cost,
		    				'total_cost' 		=> $total_cost,
		    				'status'	 		=> 1,
		    				'seller_points'		=> get_seller_points($item_id) * $sales_qty,

		    			);
				
				$salesitems_entry['store_id']=(store_module() && is_admin()) ? $store_id : get_current_store_id();  	
				$q2 = $this->db->insert('db_salesitems', $salesitems_entry);
				
				//UPDATE itemS QUANTITY IN itemS TABLE
				$this->load->model('pos_model');				
				$q6=$this->pos_model->update_items_quantity($item_id);
				if(!$q6){
					return "failed";
				}
				
			}
		
		}//for end

		if($amount=='' || $amount==0){$amount=null;}
		if($amount>0 && !empty($payment_type)){

			//is total advance payment enabled ?
			$advance_adjusted=0;
			if(isset($allow_tot_advance)){
				$tot_advance = get_customer_details($customer_id)->tot_advance;
				if($tot_advance>0){
					if($amount==$tot_advance){
						$advance_adjusted = $amount;
					}
					else if($amount>$tot_advance){
						$advance_adjusted = $tot_advance;	
					}
					else{
						$advance_adjusted =  $amount;
					}
				}
			}
			//end 

			$payment_code=get_init_code('sales_payment');
			$salespayments_entry = array(
					'payment_code' 		=> $payment_code,
		    		'count_id'	  		=> get_count_id('db_salespayments'),
					'sales_id' 			=> $sales_id, 
					'payment_date'		=> $sales_date,//Current Payment with sales entry
					'payment_type' 		=> $payment_type,
					'payment' 			=> $amount,
					'payment_note' 		=> $payment_note,
					'created_date' 		=> $CUR_DATE,
    				'created_time' 		=> $CUR_TIME,
    				'created_by' 		=> $CUR_USERNAME,
    				'system_ip' 		=> $SYSTEM_IP,
    				'system_name' 		=> $SYSTEM_NAME,
    				'status' 			=> 1,
    				'account_id' 		=> (empty($account_id)) ? null : $account_id,
    				'customer_id' 		=> $customer_id,
    				'advance_adjusted' 	=> $advance_adjusted,
    				'cheque_number' 	=> $cheque_number,
    				'cheque_period' 	=> $cheque_period,
    				'cheque_status' 	=> "Pending",
				);
			$salespayments_entry['store_id']=(store_module() && is_admin()) ? $store_id : get_current_store_id();  	
			$q3 = $this->db->insert('db_salespayments', $salespayments_entry);


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
			
		}
		
		
		

		$q10=$this->update_sales_payment_status($sales_id);
		if($q10!=1){
			return "failed";
		}
		
		
		if(!set_customer_tot_advance($customer_id)){
			return "failed";
		}
		/*$q10=$this->set_quotation_sales_status($sales_id);
		if(!$q10){
			return "failed";
		}*/
		
		//Dont save if invoice credit limit exceeds
		if(!check_credit_limit_with_invoice($customer_id,$sales_id)){
			return 'failed';
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
		
		//Calculate Opening balance before and after invoice
		$q7=calculate_ob_of_customer($sales_id,$customer_id);
		if(!$q7){
			return "failed";
		}

		/*$this->db->set("due_date",null)->where("due_date",'1970-01-01')->or_where("due_date","0000-00-00")->update("db_sales");*/

		$this->db->trans_commit();
		$this->session->set_flashdata('success', 'Success!! Record Saved Successfully! '.$sms_info);
		return "success<<<###>>>$sales_id";
		
	}//verify_save_and_update() function end

	function update_sales_payment_status_by_sales_id($sales_id){
		$q8=$this->db->query("select COALESCE(SUM(payment),0) as payment from db_salespayments where sales_id='$sales_id'");
		$sum_of_payments=$q8->row()->payment;
		

		$q9=$this->db->query("select coalesce(grand_total,0) as total from db_sales where id='$sales_id'");
		$payble_total=$q9->row()->total;
		
		//$pending_amt=$payble_total-$sum_of_payments;

		$payment_status='';
		if($payble_total==$sum_of_payments){
			$payment_status="Paid";
		}
		else if($sum_of_payments!=0 && ($sum_of_payments<$payble_total)){
			$payment_status="Partial";
		}
		else if($sum_of_payments==0){
			$payment_status="Unpaid";
		}


		$q7=$this->db->query("update db_sales set 
							payment_status='$payment_status',
							paid_amount=$sum_of_payments 
							where id='$sales_id'");
		$customer_id =$this->db->query("select customer_id from db_sales where id=$sales_id")->row()->customer_id;
		$q12 = $this->db->query("update db_customers set sales_due=(select COALESCE(SUM(grand_total),0)-COALESCE(SUM(paid_amount),0) from db_sales where customer_id='$customer_id' and sales_status='Final') where id=$customer_id");
		if(!$q7)
		{
			return false;
		}
		else{
			return true;
		}
	}


	function update_sales_payment_status($sales_id=null){
	//UPDATE PRODUCTS QUANTITY IN PRODUCTS TABLE
		if(empty($sales_id)){ //If sales ID not exist you need setup all the customers sales due
			$q11=$this->db->query("select id from db_customers");
			if($q11->num_rows()>0){
				foreach ($q11->result() as $res) {
					$q12=$this->db->query("select id from db_sales where customer_id=".$res->id);
					if($q12->num_rows()>0){
						foreach ($q12->result() as $res12) {
							if(!$this->update_sales_payment_status_by_sales_id($res12->id)){
								return false;
							}
						}
					}
					else{
						$q13=$this->db->query("update db_customers set sales_due=0 where id=".$res->id);
						if(!$q13){
							return false;
						}
					}
				}
			}
			return true;
		}
		else{
					if(!$this->update_sales_payment_status_by_sales_id($sales_id)){
						return false;
					}
					return true;
		}
	}


	//Get sales_details
	public function get_details($id,$data){
		//Validate This sales already exist or not
		$query=$this->db->query("select * from db_sales where upper(id)=upper('$id')");
		if($query->num_rows()==0){
			show_404();exit;
		}
		else{
			$query=$query->row();
			$data['q_id']=$query->id;
			$data['item_code']=$query->item_code;
			$data['item_name']=$query->item_name;
			$data['category_name']=$query->category_name;
			$data['hsn']=$query->hsn;
			$data['unit_name']=$query->unit_name;
			$data['available_qty']=$query->available_qty;
			$data['alert_qty']=$query->alert_qty;
			$data['sales_price']=$query->sales_price;
			$data['sales_price']=$query->sales_price;
			$data['gst_percentage']=$query->gst_percentage;
			
			return $data;
		}
	}
	public function update_status($id,$status){
		
        $query1="update db_sales set status='$status' where id=$id";
        if ($this->db->simple_query($query1)){
            echo "success";
        }
        else{
            echo "failed";
        }
	}
	public function delete_sales($ids){
      	$this->db->trans_begin();
      	//ACCOUNT RESET
		$reset_accounts = $this->db->select("debit_account_id,credit_account_id")
									->where("ref_salespayments_id in ($ids)")
									->group_by("debit_account_id,credit_account_id")
									->get("ac_transactions");
		//ACCOUNT RESET END

      	##############################################START
		//FIND THE PREVIOUSE ITEM LIST ID'S
		$prev_item_ids = $this->db->select("item_id")->from("db_salesitems")->where("sales_id in ($ids)")->get()->result_array();
		##############################################END
		
		//RESET QUOTATION RESET
		if(!$this->reset_quotation_sales_status_to_null($ids)){
			return "failed";
		}

		//find customer list group by
		$this->db->select("customer_id");
		$this->db->where("id in ($ids)");
		$this->db->where("store_id",get_current_store_id());
		$this->db->group_by("customer_id");
		$customer_ids=$this->db->get("db_sales");
		//end

		#----------------------------------
		$this->db->where("id in ($ids)");
		//if not admin
		if(!is_admin()){
			$this->db->where("store_id",get_current_store_id());
		}

		$q3=$this->db->delete("db_sales");
		#----------------------------------
		

		$q6=$this->db->query("select id from db_items");
		if($q6->num_rows()>0){
			$this->load->model('pos_model');				
			foreach ($q6->result() as $res6) {
				$q6=$this->pos_model->update_items_quantity($res6->id);
				if(!$q6){
					return "failed";
				}
			}
		}
		
		$q2=$this->update_sales_payment_status();
		if(!$q2){ return "failed";}
		
		$this->load->model('sales_return_model');
		$q2=$this->update_sales_payment_status();
		if(!$q2){ return "failed";}
		
		##############################################START
		/*Update items in all warehouses of the item*/
		$q7=update_warehouse_items($prev_item_ids);
		if(!$q7){
			return "failed";
		}
		##############################################END
		
		//ACCOUNT RESET
        if($reset_accounts->num_rows()>0){
        	foreach ($reset_accounts->result() as $res1) {
        		if(!update_account_balance($res1->debit_account_id)){
					return 'failed';
				}

				if(!update_account_balance($res1->credit_account_id)){
					return 'failed';
				}

        	}
        }
        //ACCOUNT RESET END

        if($customer_ids->num_rows()>0){
        	foreach ($customer_ids->result() as $customer_id) {
        		if(!set_customer_tot_advance($customer_id->customer_id)){
		        	return 'failed';
		        }
        	}
        		
        }


		$this->db->trans_commit();
		return "success";
	}
	public function search_item($q){
		$json_array=array();
        $query1="select id,item_name from db_items where (upper(item_name) like upper('%$q%') or upper(item_code) like upper('%$q%'))";

        $q1=$this->db->query($query1);
        if($q1->num_rows()>0){
            foreach ($q1->result() as $value) {
            	$json_array[]=['id'=>(int)$value->id, 'text'=>$value->item_name];
            }
        }
        return json_encode($json_array);
	}
	
	public function find_item_details($id){
		$json_array=array();
        $query1="select id,hsn,alert_qty,unit_name,sales_price,sales_price,gst_percentage,available_qty from db_items where id=$id";

        $q1=$this->db->query($query1);
        if($q1->num_rows()>0){
            foreach ($q1->result() as $value) {
            	$json_array=['id'=>$value->id, 
        			 'hsn'=>$value->hsn,
        			 'alert_qty'=>$value->alert_qty,
        			 'unit_name'=>$value->unit_name,
        			 'sales_price'=>$value->sales_price,
        			 'sales_price'=>$value->sales_price,
        			 'gst_percentage'=>$value->gst_percentage,
        			 'available_qty'=>$value->available_qty,
        			];
            }
        }
        return json_encode($json_array);
	}

	
	public function reset_quotation_sales_status_to_null($sales_ids){
			$this->db->where("id in($sales_ids)");
			$this->db->where("quotation_id!=''");
			$this->db->select("quotation_id");
			$quotation_ids = $this->db->get("db_sales");

			//if records exist
			if($quotation_ids->num_rows()>0){
				$tmpArr = array();
			    foreach ($quotation_ids->result() as $sub) {
			      $tmpArr[] = $sub->quotation_id;
			    }
			    $quotation_ids = implode(',', $tmpArr);
			    if(!empty($quotation_ids)){
			    	$q11 = $this->db->set("sales_status",null)->where("id in($quotation_ids)")->update("db_quotation");
			    	if(!$q11){
			    		return false;
			    	}
			    }
			}
			return true;
  	}


	
	/*v1.1*/
	public function inclusive($price='',$tax_per){
		return ($tax_per!=0) ? $price/(($tax_per/100)+1)/10 : $tax_per;
	}
	public function get_items_info($rowcount,$item_id){
		extract($_POST);

		$res1=$this->db->select('*')->from('db_items')->where("id=$item_id")->get()->row();
		$q3=$this->db->query("select * from db_tax where id=".$res1->tax_id)->row();

		//Get Customer Price 
		$sales_price = get_price_level_price($customer_id,$res1->sales_price);
		$sales_price = number_format($sales_price,2,'.','');

		
		$item_available_qty = total_available_qty_items_of_warehouse($warehouse_id,null,$res1->id);// $res1->stock;
		
		$item_tax_amt = ($res1->tax_type=='Inclusive') ? calculate_inclusive($sales_price,$q3->tax) :calculate_exclusive($sales_price,$q3->tax);

		$info = array(
							'item_id' 					=> $res1->id, 
							'description' 				=> '', 
							'item_name' 				=> $res1->item_name,
							'item_available_qty' 		=> $item_available_qty,
							'item_price' 				=> $res1->price, 
							'item_sales_price' 			=> $sales_price, 
							'item_tax_name' 			=> $q3->tax_name, 
							'item_sales_qty' 			=> ($item_available_qty<1 && $res1->service_bit!=1) ? $item_available_qty : number_format(1,2), 
							'item_tax_id' 				=> $q3->id, 
							'item_tax' 					=> $q3->tax, 
							'item_tax_type' 			=> $res1->tax_type, 
							'item_tax_amt' 				=> $item_tax_amt, 
							'item_discount' 			=> 0, 
							'item_discount_type' 		=> 'Percentage', 
							'item_discount_input' 		=> 0, 
							'service_bit' 				=> $res1->service_bit, 
						);

		$this->return_row_with_data($rowcount,$info);
	}
	/* For Quotation Items List Retrieve*/
	public function return_quotation_list($quotation_id){
		$q1=$this->db->select('*')->from('db_quotationitems')->where("quotation_id=$quotation_id")->get();
		$rowcount =1;
		foreach ($q1->result() as $res1) {
			$res2=$this->db->query("select * from db_items where id=".$res1->item_id)->row();
			$q3=$this->db->query("select * from db_tax where id=".$res1->tax_id)->row();
			
			$info = array(
							'item_id' 					=> $res1->item_id, 
							'description' 				=> $res1->description, 
							'item_name' 				=> $res2->item_name,
							'item_available_qty' 		=> $res2->stock,
							'item_price' 				=> $res2->price, 
							'item_sales_price' 			=> $res1->price_per_unit, 
							'item_tax_name' 			=> $q3->tax_name, 
							'item_sales_qty' 			=> $res1->quotation_qty, 
							'item_tax_id' 				=> $res1->tax_id, 
							'item_tax' 					=> $q3->tax, 
							'item_tax_type' 			=> $res1->tax_type, 
							'item_tax_amt' 				=> $res1->tax_amt, 
							'item_discount' 			=> $res1->discount_input, 
							'item_discount_type' 		=> $res1->discount_type, 
							'item_discount_input' 		=> $res1->discount_input, 
							'service_bit' 				=> 1, 
						);

			$result = $this->return_row_with_data($rowcount++,$info);
		}
		return $result;
	}
	/* For Purchase Items List Retrieve*/
	public function return_sales_list($sales_id){
		$q1=$this->db->select('*')->from('db_salesitems')->where("sales_id=$sales_id")->get();
		$rowcount =1;
		foreach ($q1->result() as $res1) {
			$res2=$this->db->query("select * from db_items where id=".$res1->item_id)->row();
			$q3=$this->db->query("select * from db_tax where id=".$res1->tax_id)->row();
			
			$info = array(
							'item_id' 					=> $res1->item_id, 
							'description' 				=> $res1->description, 
							'item_name' 				=> $res2->item_name,
							'item_available_qty' 		=> $res2->stock,
							'item_price' 				=> $res2->price, 
							'item_sales_price' 			=> $res1->price_per_unit, 
							'item_tax_name' 			=> $q3->tax_name, 
							'item_sales_qty' 			=> $res1->sales_qty, 
							'item_tax_id' 				=> $res1->tax_id, 
							'item_tax' 					=> $q3->tax, 
							'item_tax_type' 			=> $res1->tax_type, 
							'item_tax_amt' 				=> $res1->tax_amt, 
							'item_discount' 			=> $res1->discount_input, 
							'item_discount_type' 		=> $res1->discount_type, 
							'item_discount_input' 		=> $res1->discount_input, 
							'service_bit' 				=> $res2->service_bit, 
						);

			$result = $this->return_row_with_data($rowcount++,$info);
		}
		return $result;
	}

	public function return_row_with_data($rowcount,$info){
		extract($info);
		$item_amount = ($item_sales_price * $item_sales_qty) + $item_tax_amt;
		?>
            <tr id="row_<?=$rowcount;?>" data-row='<?=$rowcount;?>'>
               <td id="td_<?=$rowcount;?>_1">
                  <label class='form-control' style='height:auto;' data-toggle="tooltip" title='Edit ?' >
                  <a id="td_data_<?=$rowcount;?>_1" href="javascript:void()" onclick="show_sales_item_modal(<?=$rowcount;?>)" title=""><?=$item_name;?></a> 
                  		<i onclick="show_sales_item_modal(<?=$rowcount;?>)" class="fa fa-edit pointer"></i>
                  	</label>
               </td>

               <!-- description  -->
               <!-- <td id="td_<?=$rowcount;?>_17">
                  
                  <textarea rows="1" type="text" style="font-weight: bold; height=34px;" id="td_data_<?=$rowcount;?>_17" name="td_data_<?=$rowcount;?>_17" class="form-control no-padding"><?=$description;?></textarea>
               </td> -->

               <!-- Qty -->
               <td id="td_<?=$rowcount;?>_3">
                  <div class="input-group ">
                     <span class="input-group-btn">
                     <button onclick="decrement_qty(<?=$rowcount;?>)" type="button" class="btn btn-default btn-flat"><i class="fa fa-minus text-danger"></i></button></span>
                     <input typ="text" value="<?=$item_sales_qty;?>" class="form-control no-padding text-center" onkeyup="calculate_tax(<?=$rowcount;?>)" id="td_data_<?=$rowcount;?>_3" name="td_data_<?=$rowcount;?>_3">
                     <span class="input-group-btn">
                     <button onclick="increment_qty(<?=$rowcount;?>)" type="button" class="btn btn-default btn-flat"><i class="fa fa-plus text-success"></i></button></span>
                  </div>
               </td>
               
               <!-- Unit Cost Without Tax-->
               <td id="td_<?=$rowcount;?>_10"><input type="text" name="td_data_<?=$rowcount;?>_10" id="td_data_<?=$rowcount;?>_10" class="form-control text-right no-padding only_currency text-center" onkeyup="calculate_tax(<?=$rowcount;?>)" value="<?=store_number_format($item_sales_price,0);?>"></td>

               <!-- Discount -->
               <td id="td_<?=$rowcount;?>_8">
                  <input type="text" data-toggle="tooltip" title="Click to Change" name="td_data_<?=$rowcount;?>_8" id="td_data_<?=$rowcount;?>_8" class="pointer form-control text-right no-padding only_currency text-center item_discount" value="<?=store_number_format($item_discount,0);?>" onclick="show_sales_item_modal(<?=$rowcount;?>)" readonly>
               </td>

               <!-- Tax Amount -->
               <td id="td_<?=$rowcount;?>_11">
                  <input type="text" name="td_data_<?=$rowcount;?>_11" id="td_data_<?=$rowcount;?>_11" class="form-control text-right no-padding only_currency text-center" value="<?=store_number_format($item_tax_amt,0);?>" readonly>
               </td>

               <!-- Tax Details -->
               <td id="td_<?=$rowcount;?>_12">
                  <label class='form-control ' style='width:100%;padding-left:0px;padding-right:0px;'>
                  <a id="td_data_<?=$rowcount;?>_12" href="javascript:void()" data-toggle="tooltip" title='Click to Change' onclick="show_sales_item_modal(<?=$rowcount;?>)" title=""><?=$item_tax_name ;?></a>
                  	</label>
               </td>

               <!-- Amount -->
               <td id="td_<?=$rowcount;?>_9"><input type="text" name="td_data_<?=$rowcount;?>_9" id="td_data_<?=$rowcount;?>_9" class="form-control text-right no-padding only_currency text-center" style="border-color: #f39c12;" readonly value="<?=store_number_format($item_amount,0);?>"></td>
               
               <!-- ADD button -->
               <td id="td_<?=$rowcount;?>_16" style="text-align: center;">
                  <a class=" fa fa-fw fa-minus-square text-red" style="cursor: pointer;font-size: 34px;" onclick="removerow(<?=$rowcount;?>)" title="Delete ?" name="td_data_<?=$rowcount;?>_16" id="td_data_<?=$rowcount;?>_16"></a>
               </td>
               <input type="hidden" id="td_data_<?=$rowcount;?>_4" name="td_data_<?=$rowcount;?>_4" value="<?=$item_sales_price;?>">
               <input type="hidden" id="td_data_<?=$rowcount;?>_15" name="td_data_<?=$rowcount;?>_15" value="<?=$item_tax_id;?>">
               <input type="hidden" id="td_data_<?=$rowcount;?>_5" name="td_data_<?=$rowcount;?>_5" value="<?=$item_tax_amt;?>">
               <input type="hidden" id="tr_available_qty_<?=$rowcount;?>_13" value="<?=$item_available_qty;?>">
               <input type="hidden" id="tr_item_id_<?=$rowcount;?>" name="tr_item_id_<?=$rowcount;?>" value="<?=$item_id;?>">
               
               <input type="hidden" id="tr_tax_type_<?=$rowcount;?>" name="tr_tax_type_<?=$rowcount;?>" value="<?=$item_tax_type;?>">
               <input type="hidden" id="tr_tax_id_<?=$rowcount;?>" name="tr_tax_id_<?=$rowcount;?>" value="<?=$item_tax_id;?>">
               <input type="hidden" id="tr_tax_value_<?=$rowcount;?>" name="tr_tax_value_<?=$rowcount;?>" value="<?=$item_tax;?>">
               <input type="hidden" id="description_<?=$rowcount;?>" name="description_<?=$rowcount;?>" value="<?=$description;?>">
               <input type="hidden" id="service_bit_<?=$rowcount;?>" name="service_bit_<?=$rowcount;?>" value="<?=$service_bit;?>">

               <input type="hidden" id="item_discount_type_<?=$rowcount;?>" name="item_discount_type_<?=$rowcount;?>" value="<?=$item_discount_type;?>">
               <input type="hidden" id="item_discount_input_<?=$rowcount;?>" name="item_discount_input_<?=$rowcount;?>" value="<?=store_number_format($item_discount_input,0);?>">
            </tr>
		<?php

	}
	public function delete_payment($payment_id){
        $this->db->trans_begin();

        //ACCOUNT RESET
		$reset_accounts = $this->db->select("debit_account_id,credit_account_id")
									->where("ref_salespayments_id in ($payment_id)")
									->group_by("debit_account_id,credit_account_id")
									->get("ac_transactions");
		//ACCOUNT RESET END

		$salespayments = $this->db->query("select sales_id,customer_id from db_salespayments where id=$payment_id")->row();
		$sales_id = $salespayments->sales_id;
		$customer_id = $salespayments->customer_id;

		$q1=$this->db->query("delete from db_salespayments where id='$payment_id'");
		if(!$q1){
			return "failed";
		}
		$q2=$this->update_sales_payment_status($sales_id);
		if(!$q2){
			return "failed";
		}

		//ACCOUNT RESET
        if($reset_accounts->num_rows()>0){
        	foreach ($reset_accounts->result() as $res1) {
        		if(!update_account_balance($res1->debit_account_id)){
					return 'failed';
				}

				if(!update_account_balance($res1->credit_account_id)){
					return 'failed';
				}

        	}
        }
        //ACCOUNT RESET END

        if(!set_customer_tot_advance($customer_id)){
        	return 'failed';
        }
		$this->db->trans_commit();
		return "success";
		
	}

	public function show_pay_now_modal($sales_id){
		$q1=$this->db->query("select * from db_sales where id=$sales_id");
		$res1=$q1->row();
		$customer_id = $res1->customer_id;
		$q2=$this->db->query("select * from db_customers where id=$customer_id");
		$res2=$q2->row();

		$customer_name=$res2->customer_name;
	    $customer_mobile=$res2->mobile;
	    $customer_phone=$res2->phone;
	    $customer_email=$res2->email;
	    $customer_country=$res2->country_id;
	    $customer_state=$res2->state_id;
	    $customer_address=$res2->address;
	    $customer_postcode=$res2->postcode;
	    $customer_gst_no=$res2->gstin;
	    $customer_tax_number=$res2->tax_number;
	    $customer_opening_balance=$res2->opening_balance;
	    $customer_tot_advance=$res2->tot_advance;

	    $sales_date=$res1->sales_date;
	    $reference_no=$res1->reference_no;
	    $sales_code=$res1->sales_code;
	    $sales_note=$res1->sales_note;
	    $grand_total=$res1->grand_total;
	    $paid_amount=$res1->paid_amount;
	    $due_amount =$grand_total - $paid_amount;

	    if(!empty($customer_country)){
	      $customer_country = $this->db->query("select country from db_country where id='$customer_country'")->row()->country;  
	    }
	    if(!empty($customer_state)){
	      $customer_state = $this->db->query("select state from db_states where id='$customer_state'")->row()->state;  
	    }

		?>
		<div class="modal fade" id="pay_now">
		  <div class="modal-dialog ">
		    <div class="modal-content">
		      <div class="modal-header header-custom">
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		          <span aria-hidden="true">&times;</span></button>
		        <h4 class="modal-title text-center"><?= $this->lang->line('payments'); ?></h4>
		      </div>
		      <div class="modal-body">
		        
		    <div class="row">
		      <div class="col-md-12">
		      	<div class="row invoice-info">
			        <div class="col-sm-4 invoice-col">
			          <?= $this->lang->line('customer_details'); ?>
			          <address>
			            <strong><?php echo  $customer_name; ?></strong><br>
			            <?php echo (!empty(trim($customer_mobile))) ? $this->lang->line('mobile').": ".$customer_mobile."<br>" : '';?>
			            <?php echo (!empty(trim($customer_phone))) ? $this->lang->line('phone').": ".$customer_phone."<br>" : '';?>
			            <?php echo (!empty(trim($customer_email))) ? $this->lang->line('email').": ".$customer_email."<br>" : '';?>
			            <?php echo (!empty(trim($customer_gst_no))) ? $this->lang->line('gst_number').": ".$customer_gst_no."<br>" : '';?>
			            <?php echo (!empty(trim($customer_tax_number))) ? $this->lang->line('tax_number').": ".$customer_tax_number."<br>" : '';?>
			            
			          </address>
			        </div>
			        <!-- /.col -->
			        <div class="col-sm-4 invoice-col">
			          <?= $this->lang->line('sales_details'); ?>
			          <address>
			            <b><?= $this->lang->line('invoice'); ?> #<?php echo  $sales_code; ?></b><br>
			            <b><?= $this->lang->line('date'); ?> :<?php echo show_date($sales_date); ?></b><br>
			            <b><?= $this->lang->line('grand_total'); ?> :<?php echo $grand_total; ?></b><br>
			          </address>
			        </div>
			        <!-- /.col -->
			       
			        <div class="col-sm-4 invoice-col">
			          <b><?= $this->lang->line('paid_amount'); ?> :<span><?php echo number_format($paid_amount,2,'.',''); ?></span></b><br>
			          <b><?= $this->lang->line('due_amount'); ?> :<span id='due_amount_temp'><?php echo number_format($due_amount,2,'.',''); ?></span></b><br>
			         
			        </div>
			        <!-- /.col -->
			      </div>
			      <!-- /.row -->
		      </div>
		      <div class="col-md-12">
		        <div>
		        <input type="hidden" name="payment_row_count" id='payment_row_count' value="1">
		        <div class="col-md-12  payments_div">
		          <div class="box box-solid bg-gray">
		            <div class="box-body">
			            <div class="row">
	                         <div class="col-md-12">
	                          <span for="">
	                            <label>
	                            <?= $this->lang->line('advance'); ?> : <label><?=store_number_format($customer_tot_advance)?></label>
	                          </label>
	                          </span>
	                          <div class="checkbox">
	                            <label>
	                              <input type="checkbox" id="allow_tot_advance" name="allow_tot_advance"> <?= $this->lang->line('adjust_advance_payment'); ?>
	                            </label>
	                          </div>
	                         </div>
	                  	</div>

		              <div class="row">
		         		<div class="col-md-6">
		                  <div class="">
		                  <label for="payment_date"><?= $this->lang->line('date'); ?></label>
		                    <div class="input-group date">
			                      <div class="input-group-addon">
			                      <i class="fa fa-calendar"></i>
			                      </div>
			                      <input type="text" class="form-control pull-right datepicker" value="<?= show_date(date("d-m-Y")); ?>" id="payment_date" name="payment_date" readonly>
			                    </div>
		                      <span id="payment_date_msg" style="display:none" class="text-danger"></span>
		                </div>
		               </div>
		                <div class="col-md-6">
		                  <div class="">
		                  <label for="amount"><?= $this->lang->line('amount'); ?></label>
		                    <input type="text" class="form-control text-right paid_amt" id="amount" name="amount" placeholder="" value="<?=$due_amount;?>" >
		                      <span id="amount_msg" style="display:none" class="text-danger"></span>
		                </div>
		               </div>

		               

		                <div class="col-md-6">
		                  <div class="">
		                    <label for="payment_type"><?= $this->lang->line('payment_type'); ?></label>
		                    <select class="form-control" id='payment_type' name="payment_type" onchange="show_cheque_details()">
		                      <?php
		                        $q1=$this->db->query("select * from db_paymenttypes where status=1 and store_id=".get_current_store_id());
		                         if($q1->num_rows()>0){
		                             foreach($q1->result() as $res1){
		                             echo "<option value='".$res1->payment_type."'>".$res1->payment_type ."</option>";
		                           }
		                         }
		                         else{
		                            echo "No Records Found";
		                         }
		                        ?>
		                    </select>
		                    <span id="payment_type_msg" style="display:none" class="text-danger"></span>
		                  </div>
		                </div>
		                <div class="col-md-6">
		                  <div class="">
		                    <label for="account_id"><?= $this->lang->line('account'); ?></label>
		                    <select class="form-control" id='account_id' name="account_id">
		                      <?php
                                echo '<option value="">-None-</option>'; 
                                echo get_accounts_select_list();
                                ?>
		                    </select>
		                    <span id="account_id_msg" style="display:none" class="text-danger"></span>
		                  </div>
		                </div>

		                <div class="cheque_div" style="display: none;">
		               	<div class="col-md-6">
                        <label for="cheque_number"><?= $this->lang->line('cheque_number'); ?></label>
                          <input type="text" class="form-control" id="cheque_number" name="cheque_number">
                            <span id="cheque_number_msg" style="display:none" class="text-danger"></span>
                     	</div>
                     	<div class="col-md-6">
                     	<label for="cheque_period"><?= $this->lang->line('cheque_period'); ?></label>
                          <input type="text" class="form-control" id="cheque_period" name="cheque_period">
                            <span id="cheque_period_msg" style="display:none" class="text-danger"></span>
                     	</div>
                     	</div><!-- cheque_div -->



		            <div class="clearfix"></div>
		        </div>  
		        <div class="row">
		               <div class="col-md-12">
		                  <div class="">
		                    <label for="payment_note"><?= $this->lang->line('payment_note'); ?></label>
		                    <textarea type="text" class="form-control" id="payment_note" name="payment_note" placeholder="" ></textarea>
		                    <span id="payment_note_msg" style="display:none" class="text-danger"></span>
		                  </div>
		               </div>
		                
		            <div class="clearfix"></div>
		        </div>   
		        </div>
		        </div>
		      </div><!-- col-md-12 -->
		    </div>
		      </div><!-- col-md-9 -->
		      <!-- RIGHT HAND -->
		    </div>
		      </div>
		      <div class="modal-footer">
		      	<input type="hidden" id="customer_id" value="<?=$customer_id?>">
		        <button type="button" class="btn btn-default btn-lg" data-dismiss="modal">Close</button>
		        <button type="button" onclick="save_payment(<?=$sales_id;?>)" class="btn bg-green btn-lg place_order btn-lg payment_save">Save<i class="fa  fa-check "></i></button>
		      </div>
		    </div>
		    <!-- /.modal-content -->
		  </div>
		  <!-- /.modal-dialog -->
		</div>
		<?php
	}

	public function save_payment(){
		extract($this->xss_html_filter(array_merge($this->data,$_POST,$_GET)));
		//print_r($this->xss_html_filter(array_merge($this->data,$_POST,$_GET)));exit();
    	if($amount=='' || $amount==0){$amount=null;}
		if($amount>0 && !empty($payment_type)){
			$this->db->query("ALTER TABLE db_salespayments AUTO_INCREMENT = 1");

			$this->db->trans_begin();

			$payment_code=get_init_code('sales_payment');
			$salespayments_entry = array(
					'payment_code' 		=> $payment_code,
		    		'count_id'	  		=> get_count_id('db_salespayments'),
					'sales_id' 			=> $sales_id, 
					'payment_date'		=> system_fromatted_date($payment_date),//Current Payment with sales entry
					'payment_type' 		=> $payment_type,
					'payment' 			=> $amount,
					'payment_note' 		=> $payment_note,
					'created_date' 		=> $CUR_DATE,
    				'created_time' 		=> $CUR_TIME,
    				'created_by' 		=> $CUR_USERNAME,
    				'system_ip' 		=> $SYSTEM_IP,
    				'system_name' 		=> $SYSTEM_NAME,
    				'status' 			=> 1,
    				'account_id' 		=> (empty($account_id)) ? null : $account_id,
    				'customer_id' 		=> $customer_id,
    				'cheque_number' 	=> $cheque_number,
    				'cheque_period' 	=> $cheque_period,
    				'cheque_status' 	=> "Pending",
				);
			$salespayments_entry['store_id']=$this->db->select("store_id")->where('id',$sales_id)->get('db_sales')->row()->store_id;

			//is total advance payment enabled ?
			$advance_adjusted=0;
			if($allow_tot_advance=='checked'){
				$tot_advance = get_customer_details($customer_id)->tot_advance;
				if($tot_advance>0){
					if($amount==$tot_advance){
						$advance_adjusted = $amount;
					}
					else if($amount>$tot_advance){
						$advance_adjusted = $tot_advance;	
					}
					else{
						$advance_adjusted =  $amount;
					}
				}
			}

			//end 
			$salespayments_entry['advance_adjusted'] = $advance_adjusted;
			$q3 = $this->db->insert('db_salespayments', $salespayments_entry);

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

			if(!set_customer_tot_advance($customer_id)){
	        	return 'failed';
	        }
			
		}
		else{
			return "Please Enter Valid Amount!";
		}
		
		$q10=$this->update_sales_payment_status($sales_id);
		if($q10!=1){
			return "failed";
		}

		$this->db->trans_commit();
		return "success";

	}
	
	public function view_payments_modal($sales_id){
		$q1=$this->db->query("select * from db_sales where id=$sales_id");
		$res1=$q1->row();
		$customer_id = $res1->customer_id;
		$q2=$this->db->query("select * from db_customers where id=$customer_id");
		$res2=$q2->row();

		$customer_name=$res2->customer_name;
	    $customer_mobile=$res2->mobile;
	    $customer_phone=$res2->phone;
	    $customer_email=$res2->email;
	    $customer_country=$res2->country_id;
	    $customer_state=$res2->state_id;
	    $customer_address=$res2->address;
	    $customer_postcode=$res2->postcode;
	    $customer_gst_no=$res2->gstin;
	    $customer_tax_number=$res2->tax_number;
	    $customer_opening_balance=$res2->opening_balance;

	    $sales_date=$res1->sales_date;
	    $reference_no=$res1->reference_no;
	    $sales_code=$res1->sales_code;
	    $sales_note=$res1->sales_note;
	    $grand_total=$res1->grand_total;
	    $paid_amount=$res1->paid_amount;
	    $due_amount =$grand_total - $paid_amount;

	    if(!empty($customer_country)){
	      $customer_country = $this->db->query("select country from db_country where id='$customer_country'")->row()->country;  
	    }
	    if(!empty($customer_state)){
	      $customer_state = $this->db->query("select state from db_states where id='$customer_state'")->row()->state;  
	    }

		?>
		<div class="modal fade" id="view_payments_modal">
		  <div class="modal-dialog modal-lg">
		    <div class="modal-content">
		      <div class="modal-header header-custom">
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		          <span aria-hidden="true">&times;</span></button>
		        <h4 class="modal-title text-center"><?= $this->lang->line('payments'); ?></h4>
		      </div>
		      <div class="modal-body">
		        
		    <div class="row">
		      <div class="col-md-12">
		      	<div class="row invoice-info">
			        <div class="col-sm-4 invoice-col">
			          <?= $this->lang->line('customer_details'); ?>
			          <address>
			            <strong><?php echo  $customer_name; ?></strong><br>
			            <?php echo (!empty(trim($customer_mobile))) ? $this->lang->line('mobile').": ".$customer_mobile."<br>" : '';?>
			            <?php echo (!empty(trim($customer_phone))) ? $this->lang->line('phone').": ".$customer_phone."<br>" : '';?>
			            <?php echo (!empty(trim($customer_email))) ? $this->lang->line('email').": ".$customer_email."<br>" : '';?>
			            <?php echo (!empty(trim($customer_gst_no))) ? $this->lang->line('gst_number').": ".$customer_gst_no."<br>" : '';?>
			            <?php echo (!empty(trim($customer_tax_number))) ? $this->lang->line('tax_number').": ".$customer_tax_number."<br>" : '';?>
			          </address>
			        </div>
			        <!-- /.col -->
			        <div class="col-sm-4 invoice-col">
			          <?= $this->lang->line('sales_details'); ?>
			          <address>
			            <b><?= $this->lang->line('invoice'); ?> #<?php echo  $sales_code; ?></b><br>
			            <b><?= $this->lang->line('date'); ?> :<?php echo show_date($sales_date); ?></b><br>
			            <b><?= $this->lang->line('grand_total'); ?> :<?php echo $grand_total; ?></b><br>
			          </address>
			        </div>
			        <!-- /.col -->
			        <div class="col-sm-4 invoice-col">
			          <b><?= $this->lang->line('paid_amount'); ?> :<span><?php echo number_format($paid_amount,2,'.',''); ?></span></b><br>
			          <b><?= $this->lang->line('due_amount'); ?> :<span id='due_amount_temp'><?php echo number_format($due_amount,2,'.',''); ?></span></b><br>
			         
			        </div>
			        <!-- /.col -->
			      </div>
			      <!-- /.row -->
		      </div>
		      <div class="col-md-12">
		       
		     
		              <div class="row">
		         		<div class="col-md-12">
		                  
		                      <table class="table table-bordered">
                                  <thead>
                                  <tr class="bg-primary">
                                    <th>#</th>
                                    <th><?= $this->lang->line('payment_date'); ?></th>
                                    <th><?= $this->lang->line('payment'); ?></th>
                                    <th><?= $this->lang->line('payment_type'); ?></th>
                                    <th><?= $this->lang->line('account'); ?></th>
                                    <th><?= $this->lang->line('payment_note'); ?></th>
                                    <th><?= $this->lang->line('created_by'); ?></th>
                                    <th><?= $this->lang->line('action'); ?></th>
                                  </tr>
                                </thead>
                                <tbody>
                                	<?php
                                	$q1=$this->db->query("select * from db_salespayments where sales_id=$sales_id");
									$i=1;
									$str = '';
									if($q1->num_rows()>0){
										foreach ($q1->result() as $res1) {
											echo "<tr>";
											echo "<td>".$i++."</td>";
											echo "<td>".show_date($res1->payment_date)."</td>";
											echo "<td>".store_number_format($res1->payment)."</td>";
											echo "<td class='text-left'>";
			                                    echo $res1->payment_type;
			                                    if(!empty($res1->cheque_number)){
				                                    echo "<br>Cheque no.:".$res1->cheque_number;
				                                    echo "<br>Period:".$res1->cheque_period;
				                                }
			                                  echo "</td>";
											echo "<td>".get_account_name($res1->account_id)."</td>";
											echo "<td>".$res1->payment_note."</td>";
											echo "<td>".ucfirst($res1->created_by)."</td>";
										
											echo "<td>
											<a onclick='show_receipt(".$res1->id.")' title='Print Receipt' class='pointer btn  btn-default' ><i class='fa fa-print'></i>
											<a onclick='delete_sales_payment(".$res1->id.")' title='Delete Payment ?' class='pointer btn  btn-danger' ><i class='fa fa-trash'></i>
											</</td>";	
											echo "</tr>";
										}
									}
									else{
										echo "<tr><td colspan='7' class='text-danger text-center'>No Records Found</td></tr>";
									}
									?>
                                </tbody>
                            </table>
		               
		               </div>
		            <div class="clearfix"></div>
		        </div>    
		       
		     
		   
		      </div><!-- col-md-9 -->
		      <!-- RIGHT HAND -->
		    </div>
		      </div>
		      <div class="modal-footer">
		        <button type="button" class="btn btn-default btn-lg" data-dismiss="modal">Close</button>
		        
		      </div>
		    </div>
		    <!-- /.modal-content -->
		  </div>
		  <!-- /.modal-dialog -->
		</div>
		<?php
	}
}
