<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Money_deposit_model extends CI_Model {

	//Datatable start
	var $table = 'ac_moneydeposits as a';
	var $column_order = array( 
							'a.id',
							'a.deposit_date',
							'a.reference_no',
							'a.debit_account_id',
							'a.credit_account_id',
							'a.amount',
							'a.created_by',
							); //set column field database for datatable orderable
	var $column_search = array( 
							'a.id',
							'a.deposit_date',
							'a.reference_no',
							'a.debit_account_id',
							'a.credit_account_id',
							'a.amount',
							'a.created_by',
							);//set column field database for datatable searchable 

	var $order = array('a.id' => 'desc'); // default order 

	public function __construct()
	{
		parent::__construct();
	}

	private function _get_datatables_query()
	{
		
		$this->db->from($this->table);
		//if(!is_admin()){
			$this->db->where("a.store_id",get_current_store_id());
		//}

		$deposit_date = $this->input->post('deposit_date');
     	$deposit_date = system_fromatted_date($deposit_date);
     	if($deposit_date!='1970-01-01'){
     		$this->db->where("a.deposit_date=",$deposit_date);
     	}

     	$users = $this->input->post('users');
     	if($users && !empty($users)){
 	    	$this->db->where("upper(a.created_by)",strtoupper($users));
    	}
    	$debit_account_id = $this->input->post('debit_account_id');
     	if($debit_account_id && !empty($debit_account_id)){
 	    	$this->db->where("a.debit_account_id",$debit_account_id);
    	}
    	$credit_account_id = $this->input->post('credit_account_id');
     	if($credit_account_id && !empty($credit_account_id)){
 	    	$this->db->where("a.credit_account_id",$credit_account_id);
    	}
    	
     	

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

	//Save Cutomers
	public function verify_and_save(){
		//Filtering XSS and html escape from user inputs 
		extract($this->security->xss_clean(html_escape(array_merge($this->data,$_POST))));
		$store_id=(store_module() && is_admin()) ? $store_id : get_current_store_id();	

		$this->db->query("ALTER TABLE ac_moneydeposits AUTO_INCREMENT = 1");

		$info = array(  
	    				'store_id' 					=> $store_id,
	    				'deposit_date' 				=> system_fromatted_date($deposit_date),
	    				'debit_account_id' 			=> (!empty($debit_account_id)) ? $debit_account_id : null,
	    				'credit_account_id' 			=> $credit_account_id,
	    				'amount' 					=> $amount,
	    				'note' 						=> $note,
	    				'reference_no' 				=> $reference_no,
	    				/*System Info*/
	    				'created_date' 				=> $CUR_DATE,
	    				'created_time' 				=> $CUR_TIME,
	    				'created_by' 				=> $CUR_USERNAME,
	    				'system_ip' 				=> $SYSTEM_IP,
	    				'system_name' 				=> $SYSTEM_NAME,
	    				'status' 					=> 1,
	    			);
		$q1 = $this->db->insert('ac_moneydeposits', $info);
		if(!$q1){
			return "failed";
		}

		//Set the payment to specified account
		//ACCOUNT INSERT
		$insert_bit = insert_account_transaction(array(
													'transaction_type'  	=> 'DEPOSIT',
													'reference_table_id'  	=> $this->db->insert_id(),
													'debit_account_id'  	=> $debit_account_id,
													'credit_account_id'  	=> $credit_account_id,
													'debit_amt'  			=> $amount,
													'credit_amt'  			=> $amount,
													'process'  				=> 'SAVE',
													'note'  				=> $note,
													'transaction_date'  	=> $CUR_DATE,
													'payment_code'  		=> '',
													'customer_id'  			=> '',
													'supplier_id'  			=> '',
											));
		if(!$insert_bit){
			return "failed";
		}
		//end


		$this->session->set_flashdata('success', 'Success!! Record Added Successfully!');
		return "success";
		
	}

	//Get expenses_details
	public function get_details($id,$data){
		//Validate This expenses already exist or not
		$query=$this->db->query("select * from ac_moneydeposits where upper(id)=upper('$id')");
		if($query->num_rows()==0){
			show_404();exit;
		}
		else{
			$query=$query->row();
			$data['q_id']=$query->id;
			$data['deposit_date']=$query->deposit_date;			
			$data['reference_no']=$query->reference_no;			
			$data['debit_account_id']=$query->debit_account_id;
			$data['credit_account_id']=$query->credit_account_id;
			$data['amount']=$query->amount;
			$data['note']=$query->note;
			$data['store_id']=$query->store_id;
			return $data;
		}
	}
	public function update_money_deposit(){
		//Filtering XSS and html escape from user inputs 
		extract($this->security->xss_clean(html_escape(array_merge($this->data,$_POST))));
		
		$info = array(
	    				'deposit_date' 			=> system_fromatted_date($deposit_date),
	    				'debit_account_id' 			=> (!empty($debit_account_id)) ? $debit_account_id : null,
	    				'credit_account_id' 			=> $credit_account_id,
	    				'amount' 					=> $amount,
	    				'note' 						=> $note,
	    				'reference_no' 				=> $reference_no,
	    			);
		

		$q1 = $this->db->where('id',$q_id)->update('ac_moneydeposits', $info);
		if(!$q1){
			return "failed";
		}

		//Set the payment to specified account
		
		//ACCOUNT INSERT
		$insert_bit = insert_account_transaction(array(
													'transaction_type'  	=> 'DEPOSIT',
													'reference_table_id'  	=> $q_id,
													'debit_account_id'  	=> $debit_account_id,
													'credit_account_id'  	=> $credit_account_id,
													'debit_amt'  			=> $amount,
													'credit_amt'  			=> $amount,
													'process'  				=> 'UPDATE',
													'note'  				=> $note,
													'transaction_date'  	=> system_fromatted_date($deposit_date),
													'payment_code'  		=> '',
													'customer_id'  			=> '',
													'supplier_id'  			=> '',
											));
		if(!$insert_bit){
			return "failed";
		}
		
		//end


		$this->session->set_flashdata('success', 'Success!! Record Updated Successfully!');
		return "success";
		
	}
	
	
	public function delete_money_deposit_from_table($ids){
		$this->db->trans_begin();
		//ACCOUNT RESET
		$reset_accounts = $this->db->select("debit_account_id,credit_account_id")->where("ref_moneydeposits_id in ($ids)")->group_by("debit_account_id,credit_account_id")->get("ac_transactions");
		//ACCOUNT RESET END

		$this->db->where("id in ($ids)");
		//if not admin
		if(!is_admin()){
			$this->db->where("store_id",get_current_store_id());
		}

		$query1=$this->db->delete("ac_moneydeposits");
        if (!$query1){
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
        $this->db->trans_commit();
		return "success";
        
	}
}
