<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Account_transactions_model extends CI_Model {

	//Datatable start
	var $table = 'ac_transactions as a';
	var $column_order = array( 
							'a.id',
							'a.transaction_date',
							'a.transaction_type',
							'a.debit_account_id',
							'a.credit_account_id',
							'a.debit_amt',
							'a.credit_amt',
							'a.note',
							'a.created_by',
							'a.created_date',
							'a.payment_code',
							'a.customer_id',
							'a.suppliers_id',
							'a.ref_salespayments_id',
							'a.ref_salespaymentsreturn_id',
							'a.ref_purchasepayments_id',
							'a.ref_purchasepaymentsreturn_id',
							'a.ref_expense_id',
							); //set column field database for datatable orderable
	var $column_search = array( 
							'a.id',
							'a.transaction_date',
							'a.transaction_type',
							'a.debit_account_id',
							'a.credit_account_id',
							'a.debit_amt',
							'a.credit_amt',
							'a.note',
							'a.created_by',
							'a.created_date',
							'a.payment_code',
							'a.customer_id',
							'a.suppliers_id',
							'a.ref_salespayments_id',
							'a.ref_salespaymentsreturn_id',
							'a.ref_purchasepayments_id',
							'a.ref_purchasepaymentsreturn_id',
							'a.ref_expense_id',
							);//set column field database for datatable searchable 
	var $order = array('a.id' => 'asc'); // default order 

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
		if(isset($_POST['account_id'])){
			$account_id = $_POST['account_id'];
			$this->db->where("(a.credit_account_id=$account_id or a.debit_account_id=$account_id)");
			//$this->db->or_where("a.debit_account_id",$_POST['account_id']);
		}

		$from_date = $this->input->post('from_date');
     	$from_date = system_fromatted_date($from_date);
     	if($from_date!='1970-01-01'){
     		$this->db->where("a.transaction_date>=",$from_date);
     	}

     	$to_date = $this->input->post('to_date');
     	$to_date = system_fromatted_date($to_date);
     	if($to_date!='1970-01-01'){
     		$this->db->where("a.transaction_date<=",$to_date);
     	}

     	$users = $this->input->post('users');
     	if($users && !empty($users)){
 	    	$this->db->where("upper(a.created_by)",strtoupper($users));
    	}

    	//echo $this->db->get_compiled_select();exit;

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

	
}
