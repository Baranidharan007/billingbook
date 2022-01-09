<?php
 function get_accounts_select_list($select_id='',$parent_id=0){
 	  $CI =& get_instance();

	 
	   $CI->db->where("store_id",get_current_store_id());

	  $CI->db->select("*")->where("status=1")->from("ac_accounts");
	  $CI->db->order_by("sort_code");
	  $q1=$CI->db->get();

	  $str='';
	   if($q1->num_rows($q1)>0)
	    {  
	    	//$str.='<option value="">-Select-</option>'; 
	        foreach($q1->result() as $res1)
	      { 
	        $selected = ($select_id==$res1->id)? 'selected' : '';
	        
        		$str.= ($res1->parent_id==0) ? '<optgroup class="bg-yellow" label="'.$res1->account_name.'">' : '';
        	$str.="<option $selected data-account-name='".$res1->account_name."' value='".$res1->id."'>";
        			$str.=add_dash($res1->account_name,$res1->parent_id,$res1->sort_code);
        		
        	$str.="</option>";	
        		$str.= ($res1->parent_id==0) ? '</optgroup>' : '';
	        
	      //  echo get_accounts_select_list_sub(null,$res1->id);
	      }
	    }
	    else
	    {
	    	//$str.='<option value="">No Records Found</option>'; 
	    }
	    return $str;
 }

 function add_dash($value,$parent_id,$sort_code){
 	if($parent_id==0){
 		return $value;
 	}
 	else{
 		$dash='';
 		$count = count(explode(".", $sort_code));
 		for ($i=0; $i < $count-2; $i++) { 
 			$dash .= "&nbsp;&nbsp;&nbsp;";
 		}
 		return $dash."--".$value;
 	}
 	
 }


function insert_account_transaction($data=array()){
	
		$transaction_type 			= (empty($data['transaction_type'])) ? '' : $data['transaction_type'];
		$reference_table_id			= $data['reference_table_id'];
		$debit_account_id 			= (empty($data['debit_account_id'])) ? NULL : $data['debit_account_id'];
		$credit_account_id 			= (empty($data['credit_account_id'])) ? NULL : $data['credit_account_id'];
		$debit_amt 					= (empty($data['debit_amt'])) ? 0 : $data['debit_amt'];
		$credit_amt 				= (empty($data['credit_amt'])) ? 0 : $data['credit_amt'];
		$process 					= (empty($data['process'])) ? 'SAVE' : $data['process'];
		$note 						= (empty($data['note'])) ? '' : $data['note'];
		$transaction_date			= $data['transaction_date'];
		$payment_code 				= (empty($data['payment_code'])) ? '' : $data['payment_code'];
		$customer_id 				= (empty($data['customer_id'])) ? NULL : $data['customer_id'];
		$supplier_id 				= (empty($data['supplier_id'])) ? NULL : $data['supplier_id'];
	

	$CI =& get_instance();

	$transaction = array();
	if($transaction_type=='EXPENSE PAYMENT'){
		if($process=='UPDATE'){
			//delete previouse data of the transactions
			$CI->db->where("ref_expense_id",$reference_table_id)->delete("ac_transactions");
		}
		$transaction = array( 
								"transaction_type" 		=> $transaction_type,
								"ref_expense_id" 	=> $reference_table_id,
								"debit_account_id" 		=> $debit_account_id,
								"debit_amt"		 		=> $debit_amt,
							);
	}
	else if($transaction_type=='PURCHASE PAYMENT RETURN'){
		$transaction = array( 
								"transaction_type" 		=> $transaction_type,
								"ref_purchasepaymentsreturn_id" 	=> $reference_table_id,
								"credit_account_id" 	=> $credit_account_id,
								"credit_amt"		 	=> $credit_amt,
							);
	}
	else if($transaction_type=='PURCHASE PAYMENT'){
		$transaction = array( 
								"transaction_type" 		=> $transaction_type,
								"ref_purchasepayments_id" 	=> $reference_table_id,
								"debit_account_id" 		=> $debit_account_id,
								"debit_amt"		 		=> $debit_amt,
							);
	}
	else if($transaction_type=='SALES PAYMENT RETURN'){
		$transaction = array( 
								"transaction_type" 		=> $transaction_type,
								"ref_salespaymentsreturn_id" 	=> $reference_table_id,
								"debit_account_id" 		=> $debit_account_id,
								"debit_amt"		 		=> $debit_amt,
							);
	}
	else if($transaction_type=='SALES PAYMENT' || $transaction_type=='SALES PAYMENT & OB'){
		//CUSTOMER BULK PAYMENT INCLUDES OB PAYMENT
		$transaction = array( 
								"transaction_type" 		=> $transaction_type,
								"ref_salespayments_id" 	=> $reference_table_id,
								"credit_account_id" 	=> $credit_account_id,
								"credit_amt"		 	=> $credit_amt,
							);
		
		
	}
	else if($transaction_type=='OPENING BALANCE PAID' && !empty($supplier_id)){
		$transaction = array( 
								"transaction_type" 		=> $transaction_type,
								"ref_purchasepayments_id" 		=> $reference_table_id,
								"debit_account_id" 		=> $debit_account_id,
								"debit_amt"		 		=> $debit_amt,
							);
	}
	
	else if($transaction_type=='OPENING BALANCE PAID' && !empty($customer_id)){
		//SALES PAYMENTS
		$transaction = array( 
								"transaction_type" 		=> $transaction_type,
								"ref_salespayments_id" 		=> $reference_table_id,
								"credit_account_id" 	=> $credit_account_id,
								"credit_amt"		 	=> $credit_amt,
							);
	}
	
	else if($transaction_type=='OPENING BALANCE' && empty($customer_id) && empty($supplier_id)){
		//WHILE CREATING ACCOUNT
		$transaction = array( 
								"transaction_type" 		=> $transaction_type,
								"ref_accounts_id" 		=> $reference_table_id,
								"credit_account_id" 	=> $credit_account_id,
								"credit_amt"		 	=> $credit_amt,
							);
	}
	
	else if($transaction_type=='DEPOSIT'){
		if($process=='UPDATE'){
			//delete previouse data of the transactions
			$CI->db->where("ref_moneydeposits_id",$reference_table_id)->delete("ac_transactions");
		}
		$transaction = array( 
								"transaction_type" 		=> $transaction_type,
								"ref_moneydeposits_id" 	=> $reference_table_id,
								"debit_account_id" 		=> $debit_account_id,
								"credit_account_id" 	=> $credit_account_id,
								"debit_amt"		 		=> $debit_amt,
								"credit_amt"		 	=> $credit_amt,
							);
	}
	else if($transaction_type=='TRANSFER'){
		if($process=='UPDATE'){
			//delete previouse data of the transactions
			$CI->db->where("ref_moneytransfer_id",$reference_table_id)->delete("ac_transactions");
		}
		$transaction = array( 
								"transaction_type" 		=> $transaction_type,
								"ref_moneytransfer_id" 	=> $reference_table_id,
								"debit_account_id" 		=> $debit_account_id,
								"credit_account_id" 	=> $credit_account_id,
								"debit_amt"		 		=> $debit_amt,
								"credit_amt"		 	=> $credit_amt,
							);
	}
	else{
		//"Invalid Transaction Type";
		return false;
	}

	$transaction['store_id'] = get_current_store_id();
	$transaction['created_by'] = $CI->session->userdata('inv_username');
	$transaction['created_date'] = date("Y-m-d");
	$transaction['transaction_date'] = $transaction_date;
	$transaction['note'] = $note;
	$transaction['payment_code'] = $payment_code;
	$transaction['customer_id'] = $customer_id;
	$transaction['supplier_id'] = $supplier_id;

	if($CI->db->insert("ac_transactions",$transaction)){

		if(!empty($debit_account_id)){
			if(!update_account_balance($debit_account_id)){
				return false;
			}
		}

		if(!empty($credit_account_id)){
			if(!update_account_balance($credit_account_id)){
				return false;
			}
		}


		return true;
	}
	return false;
}



function get_account_balance($account_id){
	$CI =& get_instance();
	$debit = $CI->db->select("coalesce(sum(debit_amt),0) as debit")->where('debit_account_id',$account_id)->get("ac_transactions")->row()->debit;
	$credit = $CI->db->select("coalesce(sum(credit_amt),0) as credit")->where('credit_account_id',$account_id)->get("ac_transactions")->row()->credit;
	$balance = $credit-$debit;
	return $balance;
}
function update_account_balance($account_id){
	$CI =& get_instance();
	$balance = get_account_balance($account_id);
	$q1=$CI->db->set('balance',$balance)->where("id",$account_id)->update("ac_accounts");
	if(!$q1){
		return false;
	}
	return true;
}
