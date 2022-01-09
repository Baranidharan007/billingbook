<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Quotation_model extends CI_Model {

	//Datatable start
	var $table = 'db_quotation as a';
	var $column_order = array( 
								'a.id',
								'a.quotation_date',
								'a.expire_date',
								'a.quotation_code',
								'a.reference_no',
								'b.customer_name',
								'a.grand_total',
								'a.created_by',
								'a.store_id',
								'a.sales_status',
								); //set column field database for datatable orderable
	var $column_search = array( 
								'a.id',
								'a.quotation_date',
								'a.expire_date',
								'a.quotation_code',
								'a.reference_no',
								'b.customer_name',
								'a.grand_total',
								'a.created_by',
								'a.store_id',
								'a.sales_status',
								); //set column field database for datatable searchable 
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
	      		if(!permissions('show_all_users_quotations')){
	      			$this->db->where("upper(a.created_by)",strtoupper($this->session->userdata('inv_username')));
	      		}
	      	}
	      }
	     $quotation_from_date = $this->input->post('quotation_from_date');
	     $quotation_from_date = system_fromatted_date($quotation_from_date);
	     $quotation_to_date = $this->input->post('quotation_to_date');
	     $quotation_to_date = system_fromatted_date($quotation_to_date);
	     $users = $this->input->post('users');
	     if($users && !empty($users)){
	     	$this->db->where("upper(a.created_by)",strtoupper($users));
	     }
	     if($quotation_from_date!='1970-01-01'){
	     	$this->db->where("a.quotation_date>=",$quotation_from_date);
	     }
	     if($quotation_to_date!='1970-01-01'){
	     	$this->db->where("a.quotation_date<=",$quotation_to_date);
	     }
	   // echo $this->db->get_compiled_select();exit();
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

	//Save Quotation
	public function verify_save_and_update(){
		//Filtering XSS and html escape from user inputs 
		extract($this->xss_html_filter(array_merge($this->data,$_POST,$_GET)));
		//echo "<pre>";print_r($this->xss_html_filter(array_merge($this->data,$_POST,$_GET)));exit();
		
		$this->db->trans_begin();
		$quotation_date=system_fromatted_date($quotation_date);
		$expire_date=date('Y-m-d',strtotime($expire_date));
		$expire_date = ($expire_date=='1970-01-01') ? null : $expire_date;

		if($other_charges_input=='' || $other_charges_input==0){$other_charges_input=null;}
	    if($other_charges_tax_id=='' || $other_charges_tax_id==0){$other_charges_tax_id=null;}
	    if($other_charges_amt=='' || $other_charges_amt==0){$other_charges_amt=null;}
	    if($discount_to_all_input=='' || $discount_to_all_input==0){$discount_to_all_input=null;}
	    if($tot_discount_to_all_amt=='' || $tot_discount_to_all_amt==0){$tot_discount_to_all_amt=null;}
	    if($tot_round_off_amt=='' || $tot_round_off_amt==0){$tot_round_off_amt=null;}

	    $prev_item_ids = array();
	    
	    if($command=='save'){//Create quotation code unique if first time entry

			$this->db->query("ALTER TABLE db_quotation AUTO_INCREMENT = 1");
			
		    $quotation_entry = array(
		    				'quotation_code' 				=> get_init_code('quotation'),
		    				'count_id' 					=> get_count_id('db_quotation'),  
		    				'reference_no' 				=> $reference_no, 
		    				'quotation_date' 				=> $quotation_date,
		    				'expire_date' 				=> $expire_date,
		    				'quotation_status' 				=> $quotation_status,
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
		    				'quotation_note' 				=> $quotation_note,
		    				/*System Info*/
		    				'created_date' 				=> $CUR_DATE,
		    				'created_time' 				=> $CUR_TIME,
		    				'created_by' 				=> $CUR_USERNAME,
		    				'system_ip' 				=> $SYSTEM_IP,
		    				'system_name' 				=> $SYSTEM_NAME,
		    				'status' 					=> 1,
		    			);
		    $quotation_entry['store_id']=(store_module() && is_admin()) ? $store_id : get_current_store_id();  	
		    $quotation_entry['warehouse_id']=(warehouse_module() && warehouse_count()>1) ? $warehouse_id : get_store_warehouse_id();
			$q1 = $this->db->insert('db_quotation', $quotation_entry);
			$quotation_id = $this->db->insert_id();
		}
		else if($command=='update'){	
			$quotation_entry = array(
		    				'reference_no' 				=> $reference_no, 
		    				'quotation_date' 			=> $quotation_date,
		    				'expire_date' 				=> $expire_date,
		    				'quotation_status' 			=> $quotation_status,
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
		    				'quotation_note' 			=> $quotation_note,
		    			);
			$quotation_entry['store_id']=(store_module() && is_admin()) ? $store_id : get_current_store_id();  	
			$quotation_entry['warehouse_id']=(warehouse_module() && warehouse_count()>1) ? $warehouse_id : get_store_warehouse_id();
			$q1 = $this->db->where('id',$quotation_id)->update('db_quotation', $quotation_entry);

			##############################################START
			//FIND THE PREVIOUSE ITEM LIST ID'S
			$prev_item_ids = $this->db->select("item_id")->from("db_quotationitems")->where("quotation_id",$quotation_id)->get()->result_array();
			##############################################END

			$q11=$this->db->query("delete from db_quotationitems where quotation_id='$quotation_id'");
			if(!$q11){
				return "failed";
			}
		}
		//end

		
		//Import post data from form
		for($i=1;$i<=$rowcount;$i++){
		
			if(isset($_REQUEST['tr_item_id_'.$i]) && !empty($_REQUEST['tr_item_id_'.$i])){

				$item_id 			=$this->xss_html_filter(trim($_REQUEST['tr_item_id_'.$i]));
				$quotation_qty			=$this->xss_html_filter(trim($_REQUEST['td_data_'.$i.'_3']));
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
				//$discount_amt 		=($quotation_qty * $unit_total_cost)*$discount_input/100;


				$discount_type 		=$this->xss_html_filter(trim($_REQUEST['item_discount_type_'.$i]));
				$discount_input 	=$this->xss_html_filter(trim($_REQUEST['item_discount_input_'.$i]));
				$discount_amt	    =$this->xss_html_filter(trim($_REQUEST['td_data_'.$i.'_8']));//Amount

				$discount_amt_per_unit = $discount_amt/$quotation_qty;
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
				
			
				
				$quotationitems_entry = array(
		    				'quotation_id' 			=> $quotation_id, 
		    				'quotation_status'		=> $quotation_status, 
		    				'item_id' 			=> $item_id, 
		    				'description' 		=> $description, 
		    				'quotation_qty' 		=> $quotation_qty,
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
		    				'seller_points'		=> get_seller_points($item_id) * $quotation_qty,

		    			);
				
				$quotationitems_entry['store_id']=(store_module() && is_admin()) ? $store_id : get_current_store_id();  	
				$q2 = $this->db->insert('db_quotationitems', $quotationitems_entry);
				
				//UPDATE itemS QUANTITY IN itemS TABLE
				$this->load->model('pos_model');				
				$q6=$this->pos_model->update_items_quantity($item_id);
				if(!$q6){
					return "failed";
				}
				
			}
		
		}//for end

		$sms_info='';
		/*
		if(isset($send_sms) && $customer_id!=1){
			if(send_sms_using_template($quotation_id,1)==true){
				$sms_info = 'SMS Has been Sent!';
			}else{
				$sms_info = 'Failed to Send SMS';
			}
		}*/
		
		$this->db->trans_commit();
		$this->session->set_flashdata('success', 'Success!! Record Saved Successfully! '.$sms_info);
		return "success<<<###>>>$quotation_id";
		
	}//verify_save_and_update() function end

	


	//Get quotation_details
	public function get_details($id,$data){
		//Validate This quotation already exist or not
		$query=$this->db->query("select * from db_quotation where upper(id)=upper('$id')");
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
			$data['quotation_price']=$query->quotation_price;
			$data['quotation_price']=$query->quotation_price;
			$data['gst_percentage']=$query->gst_percentage;
			
			return $data;
		}
	}

	public function delete_quotation($ids){
      	$this->db->trans_begin();
      	
		//find the this item has the Purchase Return 
		$converted_rec = $this->db->select("*")->where("quotation_id in($ids)")->get("db_sales");
		if($converted_rec->num_rows()>0){
			$i=1;
			echo "Can't Delete!<br>These Quotations List Have the Sales Records!";
			foreach($converted_rec->result() as $res1){
				echo "<br>".$i++.". Sales ID:".$res1->sales_code;
			}
			exit;
		}

		#----------------------------------
		$this->db->where("id in ($ids)");
		//if not admin
		if(!is_admin()){
			$this->db->where("store_id",get_current_store_id());
		}

		$q3=$this->db->delete("db_quotation");
		#----------------------------------
		
		$this->db->trans_commit();
		return "success";
	}
	
	public function get_items_info($rowcount,$item_id){
		extract($_POST);

		$res1=$this->db->select('*')->from('db_items')->where("id=$item_id")->get()->row();
		$q3=$this->db->query("select * from db_tax where id=".$res1->tax_id)->row();

		//Get Customer Price 
		$quotation_price = get_price_level_price($customer_id,$res1->sales_price);
		$quotation_price = number_format($quotation_price,2,'.','');

		
		$item_available_qty = total_available_qty_items_of_warehouse($warehouse_id,null,$res1->id);// $res1->stock;
		
		$item_tax_amt = ($res1->tax_type=='Inclusive') ? calculate_inclusive($quotation_price,$q3->tax) :calculate_exclusive($quotation_price,$q3->tax);

		$info = array(
							'item_id' 					=> $res1->id, 
							'description' 				=> '', 
							'item_name' 				=> $res1->item_name,
							'item_available_qty' 		=> $item_available_qty,
							'item_price' 				=> $res1->price, 
							'item_quotation_price' 			=> $quotation_price, 
							'item_tax_name' 			=> $q3->tax_name, 
							'item_quotation_qty' 			=> 1, 
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
	/* For Purchase Items List Retrieve*/
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
							'item_quotation_price' 			=> $res1->price_per_unit, 
							'item_tax_name' 			=> $q3->tax_name, 
							'item_quotation_qty' 			=> $res1->quotation_qty, 
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
		$item_amount = ($item_quotation_price * $item_quotation_qty) + $item_tax_amt;
		?>
            <tr id="row_<?=$rowcount;?>" data-row='<?=$rowcount;?>'>
               <td id="td_<?=$rowcount;?>_1">
                  <label class='form-control' style='height:auto;' data-toggle="tooltip" title='Edit ?' >
                  <a id="td_data_<?=$rowcount;?>_1" href="javascript:void()" onclick="show_quotation_item_modal(<?=$rowcount;?>)" title=""><?=$item_name;?></a> 
                  		<i onclick="show_quotation_item_modal(<?=$rowcount;?>)" class="fa fa-edit pointer"></i>
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
                     <input typ="text" value="<?=$item_quotation_qty;?>" class="form-control no-padding text-center" onkeyup="calculate_tax(<?=$rowcount;?>)" id="td_data_<?=$rowcount;?>_3" name="td_data_<?=$rowcount;?>_3">
                     <span class="input-group-btn">
                     <button onclick="increment_qty(<?=$rowcount;?>)" type="button" class="btn btn-default btn-flat"><i class="fa fa-plus text-success"></i></button></span>
                  </div>
               </td>
               
               <!-- Unit Cost Without Tax-->
               <td id="td_<?=$rowcount;?>_10"><input type="text" name="td_data_<?=$rowcount;?>_10" id="td_data_<?=$rowcount;?>_10" class="form-control text-right no-padding only_currency text-center" onkeyup="calculate_tax(<?=$rowcount;?>)" value="<?=store_number_format($item_quotation_price,0);?>"></td>

               <!-- Discount -->
               <td id="td_<?=$rowcount;?>_8">
                  <input type="text" data-toggle="tooltip" title="Click to Change" name="td_data_<?=$rowcount;?>_8" id="td_data_<?=$rowcount;?>_8" class="pointer form-control text-right no-padding only_currency text-center item_discount" value="<?=store_number_format($item_discount,0);?>" onclick="show_quotation_item_modal(<?=$rowcount;?>)" readonly>
               </td>

               <!-- Tax Amount -->
               <td id="td_<?=$rowcount;?>_11">
                  <input type="text" name="td_data_<?=$rowcount;?>_11" id="td_data_<?=$rowcount;?>_11" class="form-control text-right no-padding only_currency text-center" value="<?=store_number_format($item_tax_amt,2);?>" readonly>
               </td>

               <!-- Tax Details -->
               <td id="td_<?=$rowcount;?>_12">
                  <label class='form-control ' style='width:100%;padding-left:0px;padding-right:0px;'>
                  <a id="td_data_<?=$rowcount;?>_12" href="javascript:void()" data-toggle="tooltip" title='Click to Change' onclick="show_quotation_item_modal(<?=$rowcount;?>)" title=""><?=$item_tax_name ;?></a>
                  	</label>
               </td>

               <!-- Amount -->
               <td id="td_<?=$rowcount;?>_9"><input type="text" name="td_data_<?=$rowcount;?>_9" id="td_data_<?=$rowcount;?>_9" class="form-control text-right no-padding only_currency text-center" style="border-color: #f39c12;" readonly value="<?=store_number_format($item_amount,2);?>"></td>
               
               <!-- ADD button -->
               <td id="td_<?=$rowcount;?>_16" style="text-align: center;">
                  <a class=" fa fa-fw fa-minus-square text-red" style="cursor: pointer;font-size: 34px;" onclick="removerow(<?=$rowcount;?>)" title="Delete ?" name="td_data_<?=$rowcount;?>_16" id="td_data_<?=$rowcount;?>_16"></a>
               </td>
               <input type="hidden" id="td_data_<?=$rowcount;?>_4" name="td_data_<?=$rowcount;?>_4" value="<?=$item_quotation_price;?>">
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
               <input type="hidden" id="item_discount_input_<?=$rowcount;?>" name="item_discount_input_<?=$rowcount;?>" value="<?=$item_discount_input;?>">
            </tr>
		<?php

	}

}
