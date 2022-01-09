<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Expense_model extends CI_Model {

	//Datatable start
	var $table = 'db_expense as a';
	var $column_order = array('a.id','a.expense_date','b.category_name','a.reference_no','a.expense_for','a.expense_amt','a.account_id','a.note','a.created_by','a.store_id'); //set column field database for datatable orderable
	var $column_search = array('a.id','a.expense_date','b.category_name','a.reference_no','a.expense_for','a.expense_amt','a.account_id','a.note','a.created_by','a.store_id'); //set column field database for datatable searchable 
	var $order = array('a.id' => 'desc'); // default order 

	public function __construct()
	{
		parent::__construct();
	}

	private function _get_datatables_query()
	{
		
		$this->db->from($this->table);
		$this->db->from('db_expense_category as b');
		$this->db->select($this->column_search)->where('b.id=a.category_id');
		//echo $this->db->get_compiled_select();exit();
		//if not admin
		//if(!is_admin()){
			$this->db->where("a.store_id",get_current_store_id());
		//}
	     if(!is_admin()){
	      	if($this->session->userdata('role_id')!='2'){
	      		if(!permissions('show_all_users_expenses')){
	      			$this->db->where("upper(a.created_by)",strtoupper($this->session->userdata('inv_username')));
	      		}
	      	}
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
		$expense_code = get_init_code('expense');
		$info = array(  
						'count_id' 					=> get_count_id('db_expense'), 
	    				'expense_code' 				=> $expense_code,
	    				'category_id' 				=> $category_id,
	    				'expense_for' 				=> $expense_for,
	    				'expense_amt' 				=> $expense_amt,
	    				'reference_no' 				=> $reference_no,
	    				'note' 				=> $note,
	    				'expense_date' 				=> system_fromatted_date($expense_date),
	    				/*System Info*/
	    				'created_date' 				=> $CUR_DATE,
	    				'created_time' 				=> $CUR_TIME,
	    				'created_by' 				=> $CUR_USERNAME,
	    				'system_ip' 				=> $SYSTEM_IP,
	    				'system_name' 				=> $SYSTEM_NAME,
	    				'status' 					=> 1,
	    				'payment_type' 				=> $payment_type,
	    				'account_id' 		=> (empty($account_id)) ? null : $account_id,
	    			);
		
		$info['store_id']=(store_module() && is_admin()) ? $store_id : get_current_store_id();	

		$q1 = $this->db->insert('db_expense', $info);
		$reference_table_id = $this->db->insert_id();
		//Set the payment to specified account
		if(!empty($account_id)){
			//ACCOUNT INSERT
			$payment_code=get_init_code('expense_payment');
			$insert_bit = insert_account_transaction(array(
														'transaction_type'  	=> 'EXPENSE PAYMENT',
														'reference_table_id'  	=> $reference_table_id,
														'debit_account_id'  	=> $account_id,
														'credit_account_id'  	=> null,
														'debit_amt'  			=> $expense_amt,
														'credit_amt'  			=> 0,
														'process'  				=> 'SAVE',
														'note'  				=> $note,
														'transaction_date'  	=> $CUR_DATE,
														'payment_code'  		=> $payment_code,
														'customer_id'  			=> null,
														'supplier_id'  			=> null,
												));
			if(!$insert_bit){
				return "failed";
			}
		}
		//end

		if ($q1){
			    $this->session->set_flashdata('success', 'Success!! Record Added Successfully!');
		        return "success";
		}
		else{
		        return "failed";
		}
		
	}

	//Get expenses_details
	public function get_details($id,$data){
		//Validate This expenses already exist or not
		$query=$this->db->query("select * from db_expense where upper(id)=upper('$id')");
		if($query->num_rows()==0){
			show_404();exit;
		}
		else{
			$query=$query->row();
			$data['q_id']=$query->id;
			$data['expense_code']=$query->expense_code;			
			$data['expense_date']=show_date($query->expense_date);
			$data['category_id']=$query->category_id;
			$data['reference_no']=$query->reference_no;
			$data['expense_for']=$query->expense_for;
			$data['expense_amt']=store_number_format($query->expense_amt,0);
			$data['note']=$query->note;
			$data['store_id']=$query->store_id;
			$data['payment_type']=$query->payment_type;
			$data['account_id']=$query->account_id;
			return $data;
		}
	}
	public function update_expense(){
		//Filtering XSS and html escape from user inputs 
		extract($this->security->xss_clean(html_escape(array_merge($this->data,$_POST))));
		$info = array(
	    				'category_id' 			=> $category_id,
	    				'expense_for' 				=> $expense_for,
	    				'expense_amt' 				=> $expense_amt,
	    				'reference_no' 				=> $reference_no,
	    				'note' 				=> $note,
	    				'expense_date' 				=> system_fromatted_date($expense_date),
	    				'payment_type' 				=> $payment_type,
	    				'account_id' 		=> (empty($account_id)) ? null : $account_id,
	    			);
		
		$info['store_id']=(store_module() && is_admin()) ? $store_id : get_current_store_id();	

		$q1 = $this->db->where('id',$q_id)->update('db_expense', $info);

		//Set the payment to specified account
		if(!empty($account_id)){
			//ACCOUNT INSERT
			$insert_bit = insert_account_transaction(array(
														'transaction_type'  	=> 'EXPENSE PAYMENT',
														'reference_table_id'  	=> $q_id,
														'debit_account_id'  	=> $account_id,
														'credit_account_id'  	=> null,
														'debit_amt'  			=> $expense_amt,
														'credit_amt'  			=> 0,
														'process'  				=> 'UPDATE',
														'note'  				=> $note,
														'transaction_date'  	=> $CUR_DATE,
														'payment_code'  		=> '',
														'customer_id'  			=> null,
														'supplier_id'  			=> null,
												));
			if(!$insert_bit){
				return "failed";
			}
		}
		//end

		if ($q1){
				$this->session->set_flashdata('success', 'Success!! Record Updated Successfully!');
		        return "success";
		}
		else{
		        return "failed";
		}
		
	}
	public function update_status($id,$status){
		if (set_status_of_table($id,$status,'db_expense')){
            echo "success";
        }
        else{
            echo "failed";
        }
	}
	
	public function check_table_data($table_name,$field,$value){
		return $this->db->query("select count(*) as tot_count from db_expense where $field='$value'")->row()->tot_count;
	}
	
	public function delete_expenses_from_table($ids){
		$this->db->trans_begin();
      	//ACCOUNT RESET
		$reset_accounts = $this->db->select("debit_account_id,credit_account_id")
									->where("ref_expense_id in ($ids)")
									->group_by("debit_account_id,credit_account_id")
									->get("ac_transactions");
		//ACCOUNT RESET END

		$this->db->where("id in ($ids)");
		//if not admin
		if(!is_admin()){
			$this->db->where("store_id",get_current_store_id());
		}

		$query1=$this->db->delete("db_expense");
		 if(!$query1){
            echo "failed";
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
