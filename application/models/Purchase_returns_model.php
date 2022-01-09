<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Purchase_returns_model extends CI_Model {
	//Datatable start
	var $table = 'db_purchasereturn as a';
	var $column_order = array( 
								'a.id',
								'a.return_date',
								'c.purchase_code',
								'a.return_code',
								'a.return_status',
								'a.reference_no',
								'b.supplier_name',
								'a.grand_total',
								'a.paid_amount',
								'a.payment_status',
								'a.created_by',
								'a.store_id'
								); //set column field database for datatable orderable
	var $column_search = array( 
								'a.id',
								'a.return_date',
								'c.purchase_code',
								'a.return_code',
								'a.return_status',
								'a.reference_no',
								'b.supplier_name',
								'a.grand_total',
								'a.paid_amount',
								'a.payment_status',
								'a.created_by',
								'a.store_id'
								); //set column field database for datatable searchable 
	var $order = array('a.id' => 'desc'); // default order 

	public function __construct(){
		parent::__construct();
	}

	private function _get_datatables_query()
	{
		$this->db->select($this->column_order);
		$this->db->from($this->table);
		$this->db->from('db_suppliers as b');
		//$this->db->select("CASE WHEN c.purchase_code IS NULL THEN '' ELSE c.purchase_code END AS purchase_code");
		$this->db->join('db_purchase as c','c.id=a.purchase_id','left');
		$this->db->where('b.id=a.supplier_id');

		/*If warehouse selected*/
		$warehouse_id = $this->input->post('warehouse_id');
		if(!empty($warehouse_id)){
			$this->db->from('db_warehouse as w');
			$this->db->where('a.warehouse_id=w.id');
			$this->db->where('w.id',$warehouse_id);
		}

		if(!is_admin()){
	      	if($this->session->userdata('role_id')!='2'){
	      		if(!permissions('show_all_users_purchase_return_invoices')){
	      			$this->db->where("upper(a.created_by)",strtoupper($this->session->userdata('inv_username')));
	      		}
	      	}
	      }
	      
		/*if(!is_admin()){*/
	      $this->db->where("a.store_id",get_current_store_id());
	    /*}*/
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

	//Save Cutomers
	public function verify_save_and_update(){
		//Filtering XSS and html escape from user inputs 
		extract($this->xss_html_filter(array_merge($this->data,$_POST,$_GET)));
		//echo "<pre>";print_r($this->xss_html_filter(array_merge($this->data,$_POST,$_GET)));exit();
		
		//varify max sales usage of the package subscription
		validate_package_offers('max_invoices','db_purchasereturn');
		//END

		$this->db->trans_begin();
		$return_date=system_fromatted_date($return_date);

		if($other_charges_input=='' || $other_charges_input==0){$other_charges_input=null;}
	    if($other_charges_tax_id=='' || $other_charges_tax_id==0){$other_charges_tax_id=null;}
	    if($other_charges_amt=='' || $other_charges_amt==0){$other_charges_amt=null;}
	    if($discount_to_all_input=='' || $discount_to_all_input==0){$discount_to_all_input=null;}
	    if($tot_discount_to_all_amt=='' || $tot_discount_to_all_amt==0){$tot_discount_to_all_amt=null;}
	    if($tot_round_off_amt=='' || $tot_round_off_amt==0){$tot_round_off_amt=null;}
	    $purchase_id = (isset($purchase_id)&&!empty($purchase_id)) ? $purchase_id : null;
	    //If you are editing the return products.
	    if(isset($return_id) && !empty($return_id)){
			//$previous_return=$this->db->query("select item_id,return_qty from db_purchaseitemsreturn where return_id=".$return_id);
		}

		$prev_item_ids = array();
	    if($command=='save' || $command=='create'){//Create purchase code unique if first time entry
		    
			$this->db->query("ALTER TABLE db_purchasereturn AUTO_INCREMENT = 1");
			
		    $purchase_entry = array(
		    				'purchase_id' 		=> $purchase_id, 
		    				'count_id' 					=> get_count_id('db_purchasereturn'), 
		    				'return_code' 			=> get_init_code('purchase_return'), 
		    				'reference_no' 				=> $reference_no, 
		    				'return_date' 			=> $return_date,
		    				'return_status' 			=> $return_status,
		    				'supplier_id' 				=> $supplier_id,
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
		    				'return_note' 			=> $return_note,
		    				/*System Info*/
		    				'created_date' 				=> $CUR_DATE,
		    				'created_time' 				=> $CUR_TIME,
		    				'created_by' 				=> $CUR_USERNAME,
		    				'system_ip' 				=> $SYSTEM_IP,
		    				'system_name' 				=> $SYSTEM_NAME,
		    				'status' 					=> 1,
		    			);
		    $purchase_entry['store_id']=(store_module() && is_admin()) ? $store_id : get_current_store_id();  	
		    $purchase_entry['warehouse_id']=(warehouse_module() && warehouse_count()>1) ? $warehouse_id : get_store_warehouse_id();
			$q1 = $this->db->insert('db_purchasereturn', $purchase_entry);
			$return_id = $this->db->insert_id();
		}
		else if($command=='update'){
			$purchase_entry = array(
							'purchase_id' 		=> $purchase_id,
		    				'reference_no' 				=> $reference_no, 
		    				'return_date' 			=> $return_date,
		    				'return_status' 			=> $return_status,
		    				'supplier_id' 				=> $supplier_id,
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
		    				'return_note' 			=> $return_note,
		    			);
			$purchase_entry['store_id']=(store_module() && is_admin()) ? $store_id : get_current_store_id();  	
			$purchase_entry['warehouse_id']=(warehouse_module() && warehouse_count()>1) ? $warehouse_id : get_store_warehouse_id();
			$q1 = $this->db->where('id',$return_id)->update('db_purchasereturn', $purchase_entry);

			##############################################START
			//FIND THE PREVIOUSE ITEM LIST ID'S
			$prev_item_ids = $this->db->select("item_id")->from("db_purchaseitemsreturn")->where("return_id",$return_id)->get()->result_array();
			##############################################END

			$q11=$this->db->query("delete from db_purchaseitemsreturn where return_id='$return_id'");
			if(!$q11){
				return "failed";
			}
		}
		//end
		
		/*if(isset($return_id) && isset($previous_return)){
			foreach ($previous_return->result() as $row){
					if(!empty($purchase_id)){
			         $this->adjust_purchase_item($purchase_id,$row->item_id,$row->return_qty,'add_qty_in_update_mode');
					}
			}
		}*/
		
		//Import post data from form
		for($i=1;$i<=$rowcount;$i++){
		
			if(isset($_REQUEST['tr_item_id_'.$i]) && !empty($_REQUEST['tr_item_id_'.$i])){

				$item_id 			=$this->xss_html_filter(trim($_REQUEST['tr_item_id_'.$i]));
				$return_qty		    =$this->xss_html_filter(trim($_REQUEST['td_data_'.$i.'_3']));
				$price_per_unit 	=$this->xss_html_filter(trim($_REQUEST['td_data_'.$i.'_4']));
				$tax_id 			=$this->xss_html_filter(trim($_REQUEST['tr_tax_id_'.$i]));
				$tax_amt 			=$this->xss_html_filter(trim($_REQUEST['td_data_'.$i.'_5']));
				
				$unit_total_cost	=$this->xss_html_filter(trim($_REQUEST['td_data_'.$i.'_10']));
				$total_cost			=$this->xss_html_filter(trim($_REQUEST['td_data_'.$i.'_9']));
				$tax_type			=$this->xss_html_filter(trim($_REQUEST['tr_tax_type_'.$i]));
				$discount_type 		=$this->xss_html_filter(trim($_REQUEST['item_discount_type_'.$i]));
				$discount_input 	=$this->xss_html_filter(trim($_REQUEST['item_discount_input_'.$i]));
				$discount_amt	    =$this->xss_html_filter(trim($_REQUEST['td_data_'.$i.'_8']));//Amount
				$description		=$this->xss_html_filter(trim($_REQUEST['description_'.$i]));

				
				$purchaseitems_entry = array(
		    				'purchase_id' 		=> $purchase_id,
		    				'return_id' 		=> $return_id,
		    				'return_status'		=> $return_status,
		    				'item_id' 			=> $item_id,
		    				'return_qty' 		=> $return_qty,
		    				'price_per_unit' 	=> $price_per_unit,
		    				'tax_id' 			=> $tax_id,
		    				'tax_amt' 			=> $tax_amt,
		    				'discount_input' 	=> $discount_input,
		    				'discount_type' 	=> $discount_type,
		    				'discount_amt' 		=> $discount_amt,
		    				'unit_total_cost' 	=> $unit_total_cost,
		    				'total_cost' 		=> $total_cost,
		    				'status'			=> 1,
		    				'description'		=> $description,
		    				'tax_type'		=> $tax_type,
		    			);
				$purchaseitems_entry['store_id']=(store_module() && is_admin()) ? $store_id : get_current_store_id();  	
				$q2 = $this->db->insert('db_purchaseitemsreturn', $purchaseitems_entry);

				/*Find the Item Exist in Purchase entry or not*/
				//$this->adjust_purchase_item($purchase_id,$item_id,$return_qty,$command);
				
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
			$payment_code=get_init_code('purchase_return_payment');
			$purchasepayments_entry = array(
					'payment_code' 		=> $payment_code,
		    		'count_id'	  		=> get_count_id('db_purchasepaymentsreturn'),
					'purchase_id' 		=> $purchase_id,
					'return_id' 		=> $return_id, 
					'payment_date'		=> $return_date,//Current Payment with Purchase entry
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
    				'supplier_id' 		=> $supplier_id,
				);
			$purchasepayments_entry['store_id']=(store_module() && is_admin()) ? $store_id : get_current_store_id();  	
			$q3 = $this->db->insert('db_purchasepaymentsreturn', $purchasepayments_entry);
			
			//Set the payment to specified account
			if(!empty($account_id)){
				//ACCOUNT INSERT
				$insert_bit = insert_account_transaction(array(
															'transaction_type'  	=> 'PURCHASE PAYMENT RETURN',
															'reference_table_id'  	=> $this->db->insert_id(),
															'debit_account_id'  	=> null,
															'credit_account_id'  	=> $account_id,
															'debit_amt'  			=> 0,
															'credit_amt'  			=> $amount,
															'process'  				=> 'SAVE',
															'note'  				=> $payment_note,
															'transaction_date'  	=> $CUR_DATE,
															'payment_code'  		=> $payment_code,
															'customer_id'  			=> null,
															'supplier_id'  			=> $supplier_id,
													));
				if(!$insert_bit){
					return "failed";
				}
			}
			//end
		}
		
		if(isset($purchase_id) && !empty($purchase_id)){
			$this->db->set('return_bit',1)->where('id',$purchase_id)->update('db_purchase');
		}

		$q10=$this->update_purchase_payment_status($return_id);
		if($q10!=1){
			return "failed";
		}
		
		##############################################START
		//FIND THE PREVIOUSE ITEM LIST ID'S
		$curr_item_ids = $this->db->select("item_id")->from("db_purchaseitemsreturn")->where("return_id",$return_id)->get()->result_array();
		$two_array = array_merge($prev_item_ids,$curr_item_ids);

		/*Update items in all warehouses of the item*/
		$q7=update_warehouse_items($two_array);
		if(!$q7){
			return "failed";
		}
		##############################################END
		
		//exit();
		$this->db->trans_commit();
		$this->session->set_flashdata('success', 'Success!! Record Saved Successfully!');
		return "success<<<###>>>$return_id";
		
	}//verify_save_and_update() function end



	public function delete_payment($payment_id){
        $this->db->trans_begin();
         //ACCOUNT RESET
		$reset_accounts = $this->db->select("debit_account_id,credit_account_id")
									->where("ref_purchasepaymentsreturn_id in ($payment_id)")
									->group_by("debit_account_id,credit_account_id")
									->get("ac_transactions");
		//ACCOUNT RESET END
		$return_id = $this->db->query("select return_id from db_purchasepaymentsreturn where id=$payment_id")->row()->return_id;

		$q1=$this->db->query("delete from db_purchasepaymentsreturn where id='$payment_id'");
		$q2=$this->update_purchase_payment_status($return_id);

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

		if($q1!=1 || $q2!=1)
		{
			$this->db->trans_rollback();
		    return "failed";
		}
		else{
			$this->db->trans_commit();
		        return "success";
		}
	}


	function update_purchase_payment_status_by_purchase_id($return_id){
	//UPDATE PRODUCTS QUANTITY IN PRODUCTS TABLE

		$q8=$this->db->query("select COALESCE(SUM(payment),0) as payment from db_purchasepaymentsreturn where return_id='$return_id'");
		$sum_of_payments=$q8->row()->payment;
		

		$q9=$this->db->query("select coalesce(grand_total,0) as total from db_purchasereturn where id='$return_id'");
		$payble_total=$q9->row()->total;
		
		$pending_amt=$payble_total-$sum_of_payments;

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


		$q7=$this->db->query("update db_purchasereturn set 
							payment_status='$payment_status',
							paid_amount=$sum_of_payments 
							where id='$return_id'");
		$supplier_id =$this->db->query("select supplier_id from db_purchasereturn where id=$return_id")->row()->supplier_id;
		$q12 = $this->db->query("update db_suppliers set purchase_return_due=(select COALESCE(SUM(grand_total),0)-COALESCE(SUM(paid_amount),0) from db_purchasereturn where supplier_id='$supplier_id') where id=$supplier_id");
		if(!$q7)
		{
			return false;
		}
		else{
			return true;
		}

	}

	function update_purchase_payment_status($return_id=null){
	//UPDATE PRODUCTS QUANTITY IN PRODUCTS TABLE
		if(empty($return_id)){ //If purchase ID not exist you need setup all the suppliers purchase due
			$q11=$this->db->query("select id from db_suppliers");
			if($q11->num_rows()>0){
				foreach ($q11->result() as $res) {
					$q12=$this->db->query("select id from db_purchasereturn where supplier_id=".$res->id);
					if($q12->num_rows()>0){
						foreach ($q12->result() as $res12) {
							if(!$this->update_purchase_payment_status_by_purchase_id($res12->id)){
								return false;
							}
						}
					}
					else{
						$q13=$this->db->query("update db_suppliers set purchase_return_due=0 where id=".$res->id);
						if(!$q13){
							return false;
						}
					}
				}
			}
			return true;
		}
		else{
					if(!$this->update_purchase_payment_status_by_purchase_id($return_id)){
						return false;
					}
					return true;
		}
	}

	public function update_return_bit(){
		$q1=$this->db->query("SELECT COUNT(*) AS tot_purchase_ids,purchase_id FROM db_purchasereturn  GROUP BY purchase_id");
		/*Reset to null*/
		$this->db->set('return_bit',null)->update('db_purchase');

		foreach ($q1->result() as $res) {
			if(!empty($res->purchase_id)){
				$this->db->set('return_bit',1)->where('id',$res->purchase_id)->update('db_purchase');
			}
		}
	}
	public function delete_return($ids){
      	$this->db->trans_begin();
      	//ACCOUNT RESET
		$reset_accounts = $this->db->select("debit_account_id,credit_account_id")
									->where("ref_purchasepaymentsreturn_id in ($ids)")
									->group_by("debit_account_id,credit_account_id")
									->get("ac_transactions");
		//ACCOUNT RESET END

      	##############################################START
		//FIND THE PREVIOUSE ITEM LIST ID'S
		$prev_item_ids = $this->db->select("item_id")->from("db_purchaseitemsreturn")->where("return_id in ($ids)")->get()->result_array();
		##############################################END
      	//If you are editing the return products.
		/*$q1=$this->db->query("select item_id,return_qty,purchase_id from db_purchaseitemsreturn where return_id in($ids)");
		if($q1->num_rows()>0){
			foreach ($q1->result() as $row){
					if(!empty($row->purchase_id)){
			         $this->adjust_purchase_item($row->purchase_id,$row->item_id,$row->return_qty,'add_qty_in_update_mode');
					}
			}
		}*/
			#----------------------------------
			$this->db->where("id in ($ids)");
			//if not admin
			if(!is_admin()){
				$this->db->where("store_id",get_current_store_id());
			}

			$q3=$this->db->delete("db_purchasereturn");
			#----------------------------------

			if(!$q3){
					return "failed";
			}

		$q6=$this->db->query("select id from db_items where store_id=".get_current_store_id());
		if($q6->num_rows()>0){
			$this->load->model('pos_model');				
			foreach ($q6->result() as $res6) {
				$q6=$this->pos_model->update_items_quantity($res6->id);
				if(!$q6){
					return "failed";
				}
			}
		}

		$this->update_return_bit();

		if(!$this->update_purchase_payment_status()){
			return "failed";
		}
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

		$this->db->trans_commit();
	    return "success";
		
	}
	public function search_item($q){
		$json_array=array();
        $query1="select id,item_name from db_items where (upper(item_name) like upper('%$q%') or upper(item_code) like upper('%$q%')) and store_id=$store_id";

        $q1=$this->db->query($query1);
        if($q1->num_rows()>0){
            foreach ($q1->result() as $value) {
            	$json_array[]=['id'=>$value->id, 'text'=>$value->item_name];
            }
        }
        return json_encode($json_array);
	}
	
	public function find_item_details($id){
		$json_array=array();
        $query1="select id,item_name,tax_id,sales_price,price,stock,tax_type,profit_margin from db_items where id=$id";

        $q1=$this->db->query($query1);
        if($q1->num_rows()>0){
            foreach ($q1->result() as $value) {
            	$json_array=['id'=>$value->id, 
        			 'item_name'=>$value->item_name,
        			 'purchase_price'=>$value->price,
        			 'sales_price'=>$value->sales_price,
        			 'tax_id'=>$value->tax_id,
        			 'stock'=>$value->stock,
        			 'profit_margin'=>$value->profit_margin,
        			 'tax_type'=>$value->tax_type,
        			];
            }
        }
        return json_encode($json_array);
	}
	

	public function inclusive($price='',$tax_per){
		return $price/(($tax_per/100)+1)/10;
	}

	public function get_items_info($rowcount,$item_id){
	    $purchase_id = $_POST['purchase_id'];//empty or value

	    /*Find the selected item exist or not in purchase entry*/
	    if(!empty($purchase_id)){
	    $valid_qty=$this->db->query("select count(*) as valid_qty from db_purchaseitems where item_id=$item_id and purchase_id=$purchase_id")->row()->valid_qty;
	      if($valid_qty==0){
	        return 'item_not_exist';
	        //return "Sorry! This Item Not exist in this Purchase Entry!!";
	      }
	    }
	    
	    $res1=$this->db->select('*')->from('db_items')->where("id=$item_id")->get()->row();
	    
	    $q3=$this->db->query("select * from db_tax where id=".$res1->tax_id)->row();

	    $item_tax_amt = ($res1->tax_type=='Inclusive') ? calculate_inclusive($res1->purchase_price,$q3->tax) :calculate_exclusive($res1->purchase_price,$q3->tax);

	      
	      $info = array(
	              'item_tax'          		=> $q3->tax, 
	              'item_tax_name'       	=> $q3->tax_name, 
	              'item_name'         		=> $res1->item_name,
	              'item_price'        		=> $res1->price, 
	              'item_available_qty'    	=> $res1->stock,
	              'item_id'           		=> $res1->id, 
	              'item_purchase_price'     => $res1->purchase_price, 
	              'item_tax_id'        	 	=> $res1->tax_id, 
	              'item_purchase_qty'      	=> 1, 
	              'description'         	=> '', 
	              'item_tax_type'       	=> $res1->tax_type, 
	              'item_tax_amt'        	=> $item_tax_amt, 
	              'item_discount'       	=> 0, 
	              'item_discount_type'    	=> 'Percentage', 
	              'item_discount_input'     => 0, 
	            );
	      

	    $this->return_row_with_data($rowcount,$info);
	  }
	/* For Purchase Items List Retrieve*/
	public function purchase_list($purchase_id){
	    $q1=$this->db->select('*')->from('db_purchaseitems')->where("purchase_id=$purchase_id")->get();
	    $rowcount =1;
	    foreach ($q1->result() as $res1) {
	      $res2=$this->db->query("select * from db_items where id=".$res1->item_id)->row();
	      $q3=$this->db->query("select * from db_tax where id=".$res1->tax_id)->row();
	      
	      $info = array(
	              'item_tax'          	=> $q3->tax, 
	              'item_tax_name'       => $q3->tax_name, 
	              'item_name'         	=> $res2->item_name,
	              'item_price'        	=> $res2->price, 
	              'item_available_qty'  => $res1->purchase_qty,
	              'item_id'           	=> $res1->item_id, 
	              'item_purchase_price' => $res1->price_per_unit, 
	              'item_tax_id'         => $res1->tax_id, 
	              'item_purchase_qty'   => $res1->purchase_qty,
	              'description'         => $res1->description, 
	              'item_tax_type'       => $res1->tax_type, 
	              'item_tax_amt'        => $res1->tax_amt, 
	              'item_discount'       => $res1->discount_input, 
	              'item_discount_type'  => $res1->discount_type, 
	              'item_discount_input' => $res1->discount_input, 
	            );
	      
	      


	      $result = $this->return_row_with_data($rowcount++,$info);
	    }
	    return $result;
	  }

	/* For Return Items List Retrieve*/
	public function return_purchase_list($return_id){
	    $q1=$this->db->select('*')->from('db_purchaseitemsreturn')->where("return_id=$return_id")->get();
	    $rowcount =1;
	    foreach ($q1->result() as $res1) {
	      $q2=$this->db->query("select * from db_items where id=".$res1->item_id);
	      $q3=$this->db->query("select * from db_tax where id=".$res1->tax_id)->row();
	      
	      
	      /*Find the purchase qty and Item Stock Qty*/
	      $item_purchase_qty=0;
	       if(!empty($res1->purchase_id)) { 
	      $item_purchase_qty = $this->db->query("select coalesce(purchase_qty,0) as purchase_qty from db_purchaseitems where purchase_id=".$res1->purchase_id.' and item_id='.$res1->item_id)->row()->purchase_qty ;
	      } 
	      $item_stock_qty = $this->db->query("select coalesce(stock,0) as stock from db_items where id=".$res1->item_id)->row()->stock;
	      /*end*/
	      
	      //$info['item_available_qty'] = ($item_stock_qty<$item_purchase_qty) ? $item_stock_qty+$res1->return_qty : $item_purchase_qty+$res1->return_qty;
	      //NEW
	      $item_available_qty = (!empty($res1->purchase_id)) ? $item_purchase_qty : $item_stock_qty+$res1->return_qty;


	      $info = array(
	              'item_tax'          		=> $q3->tax, 
	              'item_tax_name'       	=> $q3->tax_name, 
	              'item_name'         		=> $q2->row()->item_name,
	              'item_price'        		=> $q2->row()->price, 
	              'item_available_qty'    	=> $item_available_qty,
	              'item_id'           		=> $res1->item_id, 
	              'item_purchase_price'     => $res1->price_per_unit, 
	              'item_tax_id'         	=> $res1->tax_id, 
	              'item_purchase_qty'      	=> $res1->return_qty, 
	              'description'         	=> $res1->description, 
	              'item_tax_type'       	=> $res1->tax_type, 
	              'item_tax_amt'        	=> $res1->tax_amt, 
	              'item_discount'       	=> $res1->discount_input, 
	              'item_discount_type'    	=> $res1->discount_type, 
	              'item_discount_input'     => $res1->discount_input, 
	            );

	      
	      $result = $this->return_row_with_data($rowcount++,$info);
	    }
	    return $result;
	  }

	public function return_row_with_data($rowcount,$info){
		extract($info);

		$item_unit_cost = $item_purchase_price+$item_tax_amt;
		$item_amount = $item_unit_cost * $item_purchase_qty;

		?>
            <tr id="row_<?=$rowcount;?>" data-row='<?=$rowcount;?>'>
               <td id="td_<?=$rowcount;?>_1">
                  <label class='form-control' style='height:auto;' data-toggle="tooltip" title='Edit ?' >
                  <a id="td_data_<?=$rowcount;?>_1" href="javascript:void()" onclick="show_purchase_item_modal(<?=$rowcount;?>)" title=""><?=$item_name;?></a> 
                  		<i onclick="show_purchase_item_modal(<?=$rowcount;?>)" class="fa fa-edit pointer"></i>
                  	</label>
               </td>
               <!-- Qty -->
               <td id="td_<?=$rowcount;?>_3">
                  <div class="input-group ">
                     <span class="input-group-btn">
                     <button onclick="decrement_qty(<?=$rowcount;?>)" type="button" class="btn btn-default btn-flat"><i class="fa fa-minus text-danger"></i></button></span>
                     <input typ="text" value="<?=$item_purchase_qty;?>" class="form-control no-padding text-center" onkeyup="calculate_tax(<?=$rowcount;?>)" id="td_data_<?=$rowcount;?>_3" name="td_data_<?=$rowcount;?>_3">
                     <span class="input-group-btn">
                     <button onclick="increment_qty(<?=$rowcount;?>)" type="button" class="btn btn-default btn-flat"><i class="fa fa-plus text-success"></i></button></span>
                  </div>
               </td>
               <!-- Purchase Price -->
               <td id="td_<?=$rowcount;?>_4"><input type="text" name="td_data_<?=$rowcount;?>_4" id="td_data_<?=$rowcount;?>_4" class="form-control text-right no-padding only_currency text-center"  onkeyup="calculate_tax(<?=$rowcount;?>)" value="<?=store_number_format($item_purchase_price,0);?>" ></td>

               <!-- Discount -->
               <td id="td_<?=$rowcount;?>_8">
                  <input type="text" data-toggle="tooltip" title="Click to Change" name="td_data_<?=$rowcount;?>_8" id="td_data_<?=$rowcount;?>_8" class="pointer form-control text-right no-padding only_currency text-center item_discount" value="<?=store_number_format($item_discount,0);?>" onclick="show_purchase_item_modal(<?=$rowcount;?>)" readonly>
               </td>
               <!-- Tax Amount -->
               <td id="td_<?=$rowcount;?>_5"><input type="text" name="td_data_<?=$rowcount;?>_5" id="td_data_<?=$rowcount;?>_5" class="form-control text-right no-padding only_currency text-center" readonly  value="<?=store_number_format($item_tax_amt,0);?>"></td>

               <!-- Unit Cost -->
               <td id="td_<?=$rowcount;?>_10"><input type="text" name="td_data_<?=$rowcount;?>_10" id="td_data_<?=$rowcount;?>_10" class="form-control text-right no-padding only_currency text-center" readonly value="<?=store_number_format($item_unit_cost,0);?>"></td>

               <!-- Amount -->
               <td id="td_<?=$rowcount;?>_9"><input type="text" name="td_data_<?=$rowcount;?>_9" id="td_data_<?=$rowcount;?>_9" class="form-control text-right no-padding only_currency text-center" style="border-color: #f39c12;" readonly value="<?=store_number_format($item_amount,0);?>"></td>

              
               <!-- ADD button -->
               <td id="td_<?=$rowcount;?>_16" style="text-align: center;">
                  <a class=" fa fa-fw fa-minus-square text-red" style="cursor: pointer;font-size: 34px;" onclick="removerow(<?=$rowcount;?>)" title="Delete ?" name="td_data_<?=$rowcount;?>_16" id="td_data_<?=$rowcount;?>_16"></a>
               </td>
               <input type="hidden" id="tr_available_qty_<?=$rowcount;?>_13" value="<?=$item_available_qty;?>">
               <input type="hidden" id="tr_item_id_<?=$rowcount;?>" name="tr_item_id_<?=$rowcount;?>" value="<?=$item_id;?>">

               <input type="hidden" id="tr_tax_type_<?=$rowcount;?>" name="tr_tax_type_<?=$rowcount;?>" value="<?=$item_tax_type;?>">
               <input type="hidden" id="tr_tax_id_<?=$rowcount;?>" name="tr_tax_id_<?=$rowcount;?>" value="<?=$item_tax_id;?>">
               <input type="hidden" id="tr_tax_value_<?=$rowcount;?>" name="tr_tax_value_<?=$rowcount;?>" value="<?=$item_tax;?>">

               <input type="hidden" id="description_<?=$rowcount;?>" name="description_<?=$rowcount;?>" value="<?=$description;?>">
               

               <input type="hidden" id="item_discount_type_<?=$rowcount;?>" name="item_discount_type_<?=$rowcount;?>" value="<?=$item_discount_type;?>">
               <input type="hidden" id="item_discount_input_<?=$rowcount;?>" name="item_discount_input_<?=$rowcount;?>" value="<?=store_number_format($item_discount_input,0);?>">

            </tr>
		<?php

	}

	public function show_pay_now_modal($return_id){
		$q1=$this->db->query("select * from db_purchasereturn where id=$return_id");
		$res1=$q1->row();
		$supplier_id = $res1->supplier_id;
		$q2=$this->db->query("select * from db_suppliers where id=$supplier_id");
		$res2=$q2->row();

		$supplier_name=$res2->supplier_name;
	    $supplier_mobile=$res2->mobile;
	    $supplier_phone=$res2->phone;
	    $supplier_email=$res2->email;
	    $supplier_state=$res2->state_id;
	    $supplier_address=$res2->address;
	    $supplier_postcode=$res2->postcode;
	    $supplier_gst_no=$res2->gstin;
	    $supplier_tax_number=$res2->tax_number;
	    $supplier_opening_balance=$res2->opening_balance;

	    $return_date=$res1->return_date;
	    $reference_no=$res1->reference_no;
	    $return_code=$res1->return_code;
	    $return_note=$res1->return_note;
	    $grand_total=$res1->grand_total;
	    $paid_amount=$res1->paid_amount;
	    $due_amount =$grand_total - $paid_amount;

	    $supplier_country = $this->db->query("select country from db_country where id=".$res2->country_id)->row()->country;
    	$supplier_state = (!empty($supplier_state)) ? $this->db->query("select state from db_states where id=".$res2->state_id)->row()->state : '';


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
			          <?= $this->lang->line('supplier_details'); ?>
			          <address>
			            <strong><?php echo  $supplier_name; ?></strong><br>
			            <?php echo (!empty(trim($supplier_mobile))) ? $this->lang->line('mobile').": ".$supplier_mobile."<br>" : '';?>
			            <?php echo (!empty(trim($supplier_phone))) ? $this->lang->line('phone').": ".$supplier_phone."<br>" : '';?>
			            <?php echo (!empty(trim($supplier_email))) ? $this->lang->line('email').": ".$supplier_email."<br>" : '';?>
			            <?php echo (!empty(trim($supplier_gst_no))) ? $this->lang->line('gst_number').": ".$supplier_gst_no."<br>" : '';?>
			            <?php echo (!empty(trim($supplier_tax_number))) ? $this->lang->line('tax_number').": ".$supplier_tax_number."<br>" : '';?>
			          </address>
			        </div>
			        <!-- /.col -->
			        <div class="col-sm-4 invoice-col">
			          <?= $this->lang->line('purchase_details'); ?>:
			          <address>
			            <b><?= $this->lang->line('invoice'); ?> #<?php echo  $return_code; ?></b><br>
			            <b><?= $this->lang->line('date'); ?> :<?php echo  show_date($return_date); ?></b><br>
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
		                    <input type="text" class="form-control text-right paid_amt" id="amount" name="amount" placeholder="" value="<?=$due_amount;?>" onkeyup="calculate_payments()">
		                      <span id="amount_msg" style="display:none" class="text-danger"></span>
		                </div>
		               </div>
		                <div class="col-md-6">
		                  <div class="">
		                    <label for="payment_type"><?= $this->lang->line('payment_type'); ?></label>
		                    <select class="form-control" id='payment_type' name="payment_type">
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
		      	<input type="hidden" id="supplier_id" value="<?=$supplier_id?>">
		        <button type="button" class="btn btn-default btn-lg" data-dismiss="modal">Close</button>
		        <button type="button" onclick="save_payment(<?=$return_id;?>)" class="btn bg-green btn-lg place_order btn-lg payment_save">Save<i class="fa  fa-check "></i></button>
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
			$payment_code=get_init_code('purchase_return_payment');

			$purchasepayments_entry = array(
					'payment_code' 		=> $payment_code,
		    		'count_id'	  		=> get_count_id('db_purchasepaymentsreturn'),
					'return_id' 		=> $return_id, 
					'payment_date'		=> system_fromatted_date($payment_date),//Current Payment with Purchase entry
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
    				'supplier_id' 		=> $supplier_id,
				);
			$purchasepayments_entry['store_id']=$this->db->select("store_id")->where('id',$return_id)->get('db_purchasereturn')->row()->store_id;
			$q3 = $this->db->insert('db_purchasepaymentsreturn', $purchasepayments_entry);
			
			//Set the payment to specified account
			if(!empty($account_id)){
				//ACCOUNT INSERT
				$insert_bit = insert_account_transaction(array(
															'transaction_type'  	=> 'PURCHASE PAYMENT RETURN',
															'reference_table_id'  	=> $this->db->insert_id(),
															'debit_account_id'  	=> null,
															'credit_account_id'  	=> $account_id,
															'debit_amt'  			=> 0,
															'credit_amt'  			=> $amount,
															'process'  				=> 'SAVE',
															'note'  				=> $payment_note,
															'transaction_date'  	=> $CUR_DATE,
															'payment_code'  		=> $payment_code,
															'customer_id'  			=> null,
															'supplier_id'  			=> $supplier_id,
													));
				if(!$insert_bit){
					return "failed";
				}
			}
			//end

		}
		else{
			return "Please Enter Valid Amount!";
		}
		
		$q10=$this->update_purchase_payment_status($return_id);
		if($q10!=1){
			return "failed";
		}
		return "success";

	}
	
	public function view_payments_modal($return_id){
		$q1=$this->db->query("select * from db_purchasereturn where id=$return_id");
		$res1=$q1->row();
		$supplier_id = $res1->supplier_id;
		$q2=$this->db->query("select * from db_suppliers where id=$supplier_id");
		$res2=$q2->row();

		$supplier_name=$res2->supplier_name;
	    $supplier_mobile=$res2->mobile;
	    $supplier_phone=$res2->phone;
	    $supplier_email=$res2->email;
	    $supplier_state=$res2->state_id;
	    $supplier_address=$res2->address;
	    $supplier_postcode=$res2->postcode;
	    $supplier_gst_no=$res2->gstin;
	    $supplier_tax_number=$res2->tax_number;
	    $supplier_opening_balance=$res2->opening_balance;

	    $return_date=$res1->return_date;
	    $reference_no=$res1->reference_no;
	    $return_code=$res1->return_code;
	    $return_note=$res1->return_note;
	    $grand_total=$res1->grand_total;
	    $paid_amount=$res1->paid_amount;
	    $due_amount =$grand_total - $paid_amount;

	    $supplier_country = $this->db->query("select country from db_country where id=".$res2->country_id)->row()->country;
    	$supplier_state=(!empty($supplier_state)) ? $this->db->query("select state from db_states where id=".$res2->state_id)->row()->state : '';


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
			          <?= $this->lang->line('supplier_details'); ?>
			          <address>
			            <strong><?php echo  $supplier_name; ?></strong><br>
			            <?php echo (!empty(trim($supplier_mobile))) ? $this->lang->line('mobile').": ".$supplier_mobile."<br>" : '';?>
			            <?php echo (!empty(trim($supplier_phone))) ? $this->lang->line('phone').": ".$supplier_phone."<br>" : '';?>
			            <?php echo (!empty(trim($supplier_email))) ? $this->lang->line('email').": ".$supplier_email."<br>" : '';?>
			            <?php echo (!empty(trim($supplier_gst_no))) ? $this->lang->line('gst_number').": ".$supplier_gst_no."<br>" : '';?>
			            <?php echo (!empty(trim($supplier_tax_number))) ? $this->lang->line('tax_number').": ".$supplier_tax_number."<br>" : '';?>
			          </address>
			        </div>
			        <!-- /.col -->
			        <div class="col-sm-4 invoice-col">
			          <?= $this->lang->line('purchase_details'); ?>
			          <address>
			            <b>Invoice #<?php echo  $return_code; ?></b><br>
			            <b>Date :<?=  show_date($return_date); ?></b><br>
			            <b>Grand Total :<?php echo $grand_total; ?></b><br>
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
                                	$q1=$this->db->query("select * from db_purchasepaymentsreturn where return_id=$return_id");
									$i=1;
									$str = '';
									if($q1->num_rows()>0){
										foreach ($q1->result() as $res1) {
											echo "<tr>";
											echo "<td>".$i++."</td>";
											echo "<td>".show_date($res1->payment_date)."</td>";
											echo "<td>".$res1->payment."</td>";
											echo "<td>".$res1->payment_type."</td>";
											echo "<td>".get_account_name($res1->account_id)."</td>";
											echo "<td>".$res1->payment_note."</td>";
											echo "<td>".ucfirst($res1->created_by)."</td>";
										
											echo "<td><a onclick='delete_return_payment(".$res1->id.")' class='pointer btn  btn-danger' ><i class='fa fa-trash'></i></</td>";	
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
