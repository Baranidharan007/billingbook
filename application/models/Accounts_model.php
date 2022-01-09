<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Accounts_model extends CI_Model {

	//Datatable start
	var $table = 'ac_accounts as a';
	var $column_order = array('a.id','a.account_name','a.parent_id','a.note','a.account_code','a.balance','a.created_by','a.store_id','a.delete_bit'); //set column field database for datatable orderable
	var $column_search = array('a.id','a.account_name','a.parent_id','a.note','a.account_code','a.balance','a.created_by','a.store_id'); //set column field database for datatable searchable 
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
		$this->db->trans_begin();
		extract($this->security->xss_clean(html_escape(array_merge($this->data,$_POST))));
		$store_id=(store_module() && is_admin()) ? $store_id : get_current_store_id();	

		$query=$this->db->query("select * from ac_accounts where upper(account_code)=upper('$account_code') and store_id=$store_id");
		if($query->num_rows()>0){
			return "Sorry! This Account Code Name already Exist.";
		}
		$this->db->where("upper(account_name)",strtoupper($account_name));
		$this->db->where("store_id",$store_id);
		if(!empty($parent_id)){
			$this->db->where("parent_id",$parent_id);
		}
		$query=$this->db->count_all_results("ac_accounts");
		if($query>0){
			return "Sorry! This Account Name already Exist.";
		}


		$this->db->query("ALTER TABLE ac_accounts AUTO_INCREMENT = 1");
		if(empty($parent_id)) { 
			$parent_id=0;
			$maxid=$this->db->select("coalesce(max(id),0)+1 as maxid")->get("ac_accounts")->row()->maxid;
			$subtree_count='';
			$sort_code = $maxid;
		}
		else{
			//Find the sub tree count
			$this->db->select("sort_code")->where("id",$parent_id)->from("ac_accounts");
			$sort_code=$this->db->get()->row()->sort_code;
			$maxid=$this->db->select("count(*)+1 as maxid")->where("parent_id",$parent_id)->get("ac_accounts")->row()->maxid;
			$sort_code = $sort_code.".".$maxid;
		}
		
		$info = array(  
						'count_id' 					=> get_count_id('ac_accounts'), 
	    				'store_id' 					=> $store_id,
	    				'sort_code' 				=> $sort_code,
	    				'account_code' 				=> $account_code,
	    				'parent_id' 				=> $parent_id,
	    				'account_name' 				=> $account_name,
	    				'note' 						=> $note,
	    				/*System Info*/
	    				'created_date' 				=> $CUR_DATE,
	    				'created_time' 				=> $CUR_TIME,
	    				'created_by' 				=> $CUR_USERNAME,
	    				'system_ip' 				=> $SYSTEM_IP,
	    				'system_name' 				=> $SYSTEM_NAME,
	    				'status' 					=> 1,
	    			);
		$q1 = $this->db->insert('ac_accounts', $info);
		if(!$q1){
			return "failed";
		}


		//ACCOUNT INSERT
		$insert_bit = insert_account_transaction(array(
													'transaction_type'  	=> 'OPENING BALANCE',
													'reference_table_id'  	=> $this->db->insert_id(),
													'debit_account_id'  	=> null,
													'credit_account_id'  	=> $this->db->insert_id(),
													'debit_amt'  			=> 0,
													'credit_amt'  			=> $opening_balance,
													'process'  				=> 'SAVE',
													'note'  				=> '',
													'transaction_date'  	=> $CUR_DATE,
													'payment_code'  		=> '',
													'customer_id'  			=> '',
													'supplier_id'  			=> '',
											));
		if(!$insert_bit){
			return "failed";
		}
		//END

		$this->session->set_flashdata('success', 'Success!! Record Added Successfully!');
		$this->db->trans_commit();
		return "success";
		
		
	}

	//Get expenses_details
	public function get_details($id,$data){
		//Validate This expenses already exist or not
		$query=$this->db->query("select * from ac_accounts where upper(id)=upper('$id')");
		if($query->num_rows()==0){
			show_404();exit;
		}
		else{
			$query=$query->row();
			$data['q_id']=$query->id;
			$data['account_code']=$query->account_code;			
			$data['parent_id']=$query->parent_id;
			$data['account_name']=$query->account_name;
			$data['note']=$query->note;
			$data['opening_balance']=$query->balance;
			$data['store_id']=$query->store_id;
			return $data;
		}
	}
	public function update_accounts(){
		//Filtering XSS and html escape from user inputs 
		extract($this->security->xss_clean(html_escape(array_merge($this->data,$_POST))));
		$store_id=(store_module() && is_admin()) ? $store_id : get_current_store_id();	

		$query=$this->db->query("select * from ac_accounts where upper(account_code)=upper('$account_code') and id!=$q_id and store_id=$store_id");
		if($query->num_rows()>0){
			return "Sorry! This Account Code Name already Exist.";
		}
		$this->db->where("upper(account_name)",strtoupper($account_name));
		$this->db->where("store_id",$store_id);
		if(!empty($parent_id)){
			$this->db->where("parent_id",$parent_id);
		}

		$this->db->where("id!=",$q_id);
		
		$query=$this->db->count_all_results("ac_accounts");
		if($query>0){
			return "Sorry! This Account Name already Exist.";
		}


		$this->db->query("ALTER TABLE ac_accounts AUTO_INCREMENT = 1");
		if(empty($parent_id)) { 
			$parent_id=0;
			$maxid=$this->db->select("coalesce(max(id),0)+1 as maxid")->get("ac_accounts")->row()->maxid;
			$subtree_count='';
			$sort_code = $maxid;
		}
		else{
			//Find the sub tree count
			$this->db->select("sort_code")->where("id",$parent_id)->from("ac_accounts");
			$sort_code=$this->db->get()->row()->sort_code;
			$maxid=$this->db->select("count(*)+1 as maxid")->where("parent_id",$parent_id)->get("ac_accounts")->row()->maxid;
			$sort_code = $sort_code.".".$maxid;
		}


		$info = array(
	    				'sort_code' 		=> $sort_code,
	    				'store_id' 			=> $store_id,
	    				'parent_id' 			=> $parent_id,
	    				'account_name' 				=> $account_name,
	    				'account_code' 				=> $account_code,
	    				'note' 				=> $note,
	    			);
		

		$q1 = $this->db->where('id',$q_id)->update('ac_accounts', $info);
		if ($q1){
				$this->session->set_flashdata('success', 'Success!! Record Updated Successfully!');
		        return "success";
		}
		else{
		        return "failed";
		}
		
	}
	
	
	public function delete_accounts_from_table($ids){
		$this->db->trans_begin();
		

		$reset_accounts = $this->db->select("debit_account_id,credit_account_id")
							 ->where("ref_accounts_id in ($ids)")
							 ->where("ref_moneytransfer_id in ($ids)")
							 ->where("ref_moneydeposits_id in ($ids)")
							 ->group_by("debit_account_id,credit_account_id")->get("ac_transactions");

		$this->db->where("id in ($ids)");
		//if not admin
		if(!is_admin()){
			$this->db->where("store_id",get_current_store_id());
		}

		$this->db->where("delete_bit=0");
		$query1=$this->db->delete("ac_accounts");
        if (!$query1){
            return "failed";
        }

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

        $this->db->trans_commit();
        return "success";
        
	}
}
