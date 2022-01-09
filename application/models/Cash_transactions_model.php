<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cash_transactions_model extends CI_Model {

	//Datatable start
	var $table = '';
	var $column_order = array( 
							
							); //set column field database for datatable orderable
	var $column_search = array( 
							
							);//set column field database for datatable searchable 
	//var $order = array('a.id' => 'asc'); // default order 

	public function __construct()
	{
		parent::__construct();
	}

	private function _get_datatables_query()
	{
		
		

		
		$from_date_query = $from_date_query_expense = $to_date_query = $to_date_query_expense = $users_query  ='';

		$from_date = $this->input->post('from_date');
     	$from_date = system_fromatted_date($from_date);
     	if($from_date!='1970-01-01'){
     		$from_date_query = " and payment_date >= '$from_date'";
     		$from_date_query_expense = " and expense_date >= '$from_date'";
     		
     		
     	}

     	$to_date = $this->input->post('to_date');
     	$to_date = system_fromatted_date($to_date);
     	if($to_date!='1970-01-01'){
     		$to_date_query = " and payment_date <= '$from_date'";
     		$to_date_query_expense = " and expense_date <= '$from_date'";
     	}

     	$users = $this->input->post('users');

     	if(!empty($users)){
 	    	
 	    	$users_query = " and upper(created_by) = '".strtoupper($users)."' ";
    	}


		$i = 0;
	
		foreach ($this->column_search as $item) // loop column 
		{
			if($_POST['search']['value']) // if datatable send POST for search
			{
				
				if($i===0) // first loop
				{
					//$this->db->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
					//$this->db->like($item, $_POST['search']['value']);
				}
				else
				{
					//$this->db->or_like($item, $_POST['search']['value']);
				}

				//if(count($this->column_search) - 1 == $i) //last loop
				//	$this->db->group_end(); //close bracket
			}
			$i++;
		}
		
		if(isset($_POST['order'])) // here order processing
		{
			//$this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);


		} 
		else if(isset($this->order))
		{
			//$order = $this->order;
			//$this->db->order_by(key($order), $order[key($order)]);
		}


		$query =  "SELECT 	
							'SALES_PAYMENT',
							id,
							payment_date,
							payment_code,
							payment_type,
							payment,
							payment_note,
							created_by,
							created_date,
							created_time,
							account_id,
							store_id
							FROM db_salespayments 
							WHERE store_id = ".get_current_store_id()." 
							$from_date_query
							$to_date_query 
							$users_query 

							UNION ALL

							SELECT 
							'PURCHASE_PAYMENT',
							id,
							payment_date,
							payment_code,
							payment_type,
							payment,
							payment_note,
							created_by,
							created_date,
							created_time,
							account_id,
							store_id
							FROM db_purchasepayments
							WHERE store_id = ".get_current_store_id()." 
							$from_date_query
							$to_date_query 
							$users_query 

							UNION ALL

							SELECT 
							'SALES_RETURN_PAYMENT',
							id,
							payment_date,
							payment_code,
							payment_type,
							payment,
							payment_note,
							created_by,
							created_date,
							created_time,
							account_id,
							store_id 
							FROM
							db_salespaymentsreturn 
							WHERE store_id = ".get_current_store_id()." 
							$from_date_query
							$to_date_query 
							$users_query 

							UNION ALL

							SELECT 
							'PURCHASE_RETURN_PAYMENT',
							id,
							payment_date,
							payment_code,
							payment_type,
							payment,
							payment_note,
							created_by,
							created_date,
							created_time,
							account_id,
							store_id 
							FROM
							db_purchasepaymentsreturn 
							WHERE store_id = ".get_current_store_id()." 
							$from_date_query
							$to_date_query 
							$users_query 

							UNION ALL

							SELECT 
							'EXPENSE',
							id,
							expense_date,
							expense_code,
							payment_type,
							expense_amt,
							note,
							created_by,
							created_date,
							created_time,
							account_id,
							store_id 
							FROM
							db_expense
							WHERE store_id = ".get_current_store_id()." 
							$from_date_query_expense
							$to_date_query_expense 
							$users_query 					

							ORDER BY payment_date,created_time ASC
							

							";
		//echo $query;exit;
		return $query;

	}

	
	function get_datatables()
	{

		
		$query = $this->_get_datatables_query();

		if($_POST['length'] != -1){
			$start_end_query = " limit  ".$_POST['start'].", ".$_POST['length']." ";
			$query .= $start_end_query;
		}
		//echo $query;exit;
		$query = $this->db->query($query);

		return $query->result();
	}

	function count_filtered()
	{
		$query = $this->_get_datatables_query();
		$query = $this->db->query($query);
		return $query->num_rows();
	}

	public function count_all()
	{
		//$this->db->where("store_id",get_current_store_id());
		//$this->db->from($this->table);
		//return $this->db->count_all_results();
		$query = $this->_get_datatables_query();
		$query = $this->db->query($query);
		return $query->num_rows();
	}

	public function link_account(){
		extract((array_merge($this->data,$_POST,$_GET)));
		//print_r($this->xss_html_filter(array_merge($this->data,$_POST,$_GET)));exit();
    	
		$this->db->trans_begin();

		if($account_of==1){
			$get_details = $this->db->select("*")->where("id",$rec_id)->get("db_salespayments")->row();

			//Sales Payments
			$transaction_type = 'SALES PAYMENT';
			$debit_account_id = null;
			$credit_account_id = $account_id;
			$debit_amt = 0;
			$credit_amt = $get_details->payment;
			$customer_id = $get_details->customer_id;
			$supplier_id = null;
			$payment_code=get_init_code('sales_payment');
			$payment_note = $get_details->payment_note;
			$transaction_date = $get_details->payment_date;

			//ACCOUNT RESET
			$this->db->where("ref_salespayments_id in ($rec_id)");
			$this->db->select("debit_account_id,credit_account_id");
			$this->db->group_by("debit_account_id,credit_account_id");
			$reset_accounts = $this->db->get("ac_transactions");
			//ACCOUNT RESET END

			//Delete that account trasaction only
			$this->db->where("ref_salespayments_id",$rec_id)->delete("ac_transactions");

			//Updates Sales Payment Account ID
			$this->db->set("account_id",$account_id)->where("id",$rec_id)->update("db_salespayments");
		}
		if($account_of==2){
			$get_details = $this->db->select("*")->where("id",$rec_id)->get("db_purchasepayments")->row();

			//Purchase Payments
			$transaction_type = 'PURCHASE PAYMENT';
			$debit_account_id = $account_id;
			$credit_account_id = null;
			$debit_amt = $get_details->payment;
			$credit_amt = 0;
			$customer_id = null;
			$supplier_id = $get_details->supplier_id;
			$payment_code=get_init_code('purchase_payment');
			$payment_note = $get_details->payment_note;
			$transaction_date = $get_details->payment_date;

			//ACCOUNT RESET
			$this->db->where("ref_purchasepayments_id in ($rec_id)");
			$this->db->select("debit_account_id,credit_account_id");
			$this->db->group_by("debit_account_id,credit_account_id");
			$reset_accounts = $this->db->get("ac_transactions");
			//ACCOUNT RESET END

			//Delete that account trasaction only
			$this->db->where("ref_purchasepayments_id",$rec_id)->delete("ac_transactions");

			//Updates Purchase Payment Account ID
			$this->db->set("account_id",$account_id)->where("id",$rec_id)->update("db_purchasepayments");

		}
		if($account_of==3){
			$get_details = $this->db->select("*")->where("id",$rec_id)->get("db_salespaymentsreturn")->row();

			//Sales Return Payments
			$transaction_type = 'SALES PAYMENT RETURN';
			$debit_account_id = $account_id;
			$credit_account_id = null;
			$debit_amt = $get_details->payment;
			$credit_amt = 0;
			$customer_id = $get_details->customer_id;
			$supplier_id = null;
			$payment_code=get_init_code('sales_return_payment');
			$payment_note = $get_details->payment_note;
			$transaction_date = $get_details->payment_date;

			//ACCOUNT RESET
			$this->db->where("ref_salespaymentsreturn_id in ($rec_id)");
			$this->db->select("debit_account_id,credit_account_id");
			$this->db->group_by("debit_account_id,credit_account_id");
			$reset_accounts = $this->db->get("ac_transactions");
			//ACCOUNT RESET END

			//Delete that account trasaction only
			$this->db->where("ref_salespaymentsreturn_id",$rec_id)->delete("ac_transactions");

			//Updates SAles Payment return Account ID
			$this->db->set("account_id",$account_id)->where("id",$rec_id)->update("db_salespaymentsreturn");

		}
		if($account_of==4){
			$get_details = $this->db->select("*")->where("id",$rec_id)->get("db_purchasepaymentsreturn")->row();

			//Purchase Return Payments
			$transaction_type = 'PURCHASE PAYMENT RETURN';
			$debit_account_id = null;
			$credit_account_id = $account_id;
			$debit_amt = 0;
			$credit_amt = $get_details->payment;
			$customer_id = null;
			$supplier_id = $get_details->supplier_id;
			$payment_code=get_init_code('purchase_return_payment');
			$payment_note = $get_details->payment_note;
			$transaction_date = $get_details->payment_date;

			//ACCOUNT RESET
			$this->db->where("ref_purchasepaymentsreturn_id in ($rec_id)");
			$this->db->select("debit_account_id,credit_account_id");
			$this->db->group_by("debit_account_id,credit_account_id");
			$reset_accounts = $this->db->get("ac_transactions");
			//ACCOUNT RESET END

			//Delete that account trasaction only
			$this->db->where("ref_purchasepaymentsreturn_id",$rec_id)->delete("ac_transactions");

			//Updates purchase Payment return Account ID
			$this->db->set("account_id",$account_id)->where("id",$rec_id)->update("db_purchasepaymentsreturn");

		}
		if($account_of==5){
			$get_details = $this->db->select("*")->where("id",$rec_id)->get("db_expense")->row();

			//Expenses
			$transaction_type = 'EXPENSE PAYMENT';
			$debit_account_id = $account_id;
			$credit_account_id = null;
			$debit_amt = $get_details->expense_amt;
			$credit_amt = 0;
			$customer_id = null;
			$supplier_id = null;
			$payment_code=get_init_code('expense_payment');
			$payment_note = $get_details->note;
			$transaction_date = $get_details->expense_date;

			//ACCOUNT RESET
			$this->db->where("ref_expense_id in ($rec_id)");
			$this->db->select("debit_account_id,credit_account_id");
			$this->db->group_by("debit_account_id,credit_account_id");
			$reset_accounts = $this->db->get("ac_transactions");
			//ACCOUNT RESET END

			//Delete that account trasaction only
			$this->db->where("ref_expense_id",$rec_id)->delete("ac_transactions");

			//Updates expense Payment Account ID
			$this->db->set("account_id",$account_id)->where("id",$rec_id)->update("db_expense");
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


		//Set the payment to specified account
		if(!empty($account_id)){
			//ACCOUNT INSERT
			$insert_bit = insert_account_transaction(array(
														'transaction_type'  	=> $transaction_type,
														'reference_table_id'  	=> $rec_id,
														'debit_account_id'  	=> $debit_account_id,
														'credit_account_id'  	=> $credit_account_id,
														'debit_amt'  			=> $debit_amt,
														'credit_amt'  			=> $credit_amt,
														'process'  				=> 'SAVE',
														'note'  				=> $payment_note,
														'transaction_date'  	=> $transaction_date,
														'payment_code'  		=> $payment_code,
														'customer_id'  			=> $customer_id,
														'supplier_id'  			=> $supplier_id,
												));
			if(!$insert_bit){
				return "failed";
			}
		}
		//end
		

		
		
		

		$this->db->trans_commit();
		return "success";

	}

	
}
