<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Customers_advance_model extends CI_Model {
	//Datatable start
	var $table = 'db_custadvance as a';
	var $column_order = array(
								'a.id',
								'a.payment_code',
								'a.payment_date',
								'b.customer_name',
								'a.amount',
								'a.payment_type',
								'a.created_by',
								); //set column field database for datatable orderable
	var $column_search = array(
								'a.id',
								'a.payment_code',
								'a.payment_date',
								'b.customer_name',
								'a.amount',
								'a.payment_type',
								'a.created_by',
								); //set column field database for datatable searchable 
	var $order = array('a.id' => 'desc'); // default order 

	public function __construct()
	{
		parent::__construct();
	}

	private function _get_datatables_query()
	{
		$this->db->select($this->column_order);
		$this->db->from($this->table);
		$this->db->from('db_customers as b');
		$this->db->where('b.id=a.customer_id');
		//if(!is_admin()){
	      $this->db->where("a.store_id",get_current_store_id());
	    //}
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


	//Save Cutomers
	public function store_record($command='save'){
		//Filtering XSS and html escape from user inputs 
		extract($this->security->xss_clean(html_escape(array_merge($this->data,$_POST))));

		$payment_date=system_fromatted_date($payment_date);
		//Validate This customers already exist or not
		$store_id=(store_module() && is_admin()) ? $store_id : get_current_store_id();  	
		
		$info = array(
	                'customer_id'         	=> $customer_id,
	                'payment_date'         	=> $payment_date,
	                'amount'         		=> $amount,
	                'payment_type'         	=> $payment_type,
	                'note'		         	=> $note,
	              );
		if($command=='save'){
			$this->db->query("ALTER TABLE db_custadvance AUTO_INCREMENT = 1");
			$save_operation = array(
				                'store_id'         		=> $store_id, 
				                'count_id' 				=> get_count_id('db_custadvance'), 
				                'payment_code'         => get_init_code('custadvance'), 
				                /*System Info*/
				                'created_date'        	=> $CUR_DATE,
				                'created_time'        	=> $CUR_TIME,
				                'created_by'        	=> $CUR_USERNAME,
				                'system_ip'         	=> $SYSTEM_IP,
				                'system_name'         	=> $SYSTEM_NAME,
				                'status'          		=> 1,
				              );
		    $info = array_merge($info,$save_operation);
		    $query1 = $this->db->insert('db_custadvance', $info);
		    if(!$query1){
		    	return "failed";
		    }

		    if(!set_customer_tot_advance($customer_id)){
		    	return "failed";
		    }
		    $this->session->set_flashdata('success', 'Success!! New Advance Payment Record Added!');
		}
		else{
			//UPDATE OPERATION
			$query1 = $this->db->where('id',$q_id)->update('db_custadvance', $info);
			if(!$query1){
		    	return "failed";
		    }
		    if(!set_customer_tot_advance($customer_id)){
		    	return "failed";
		    }
		    $this->session->set_flashdata('success', 'Success!! Advance Payment Record Updated!');
		}
		return "success";
		
	}

	//Get customers_details
	public function get_details($id,$data){
		//Validate This customers already exist or not
		$query=$this->db->query("select * from db_custadvance where id='$id'");
		if($query->num_rows()==0){
			show_404();exit;
		}
		else{
			$query=$query->row();
			$data['q_id']=$query->id;
			$data['store_id']=$query->store_id;
			$data['customer_id']=$query->customer_id;
			$data['payment_date']=show_date($query->payment_date);
			$data['amount']=$query->amount;
			$data['payment_type']=$query->payment_type;
			$data['note']=$query->note;

			return $data;
		}
	}

	public function delete_advance_from_table($ids){
			$this->db->trans_begin();
			
			//RESET CUSTOMER IDS
			$customer_ids = $this->db->select("customer_id")
									->where("store_id",get_current_store_id())
									->where("id in ($ids)")
									->group_by("customer_id")
									->get("db_custadvance");
			//END
			
			$this->db->where("id in ($ids)");
			//if not admin
			if(!is_admin()){
				$this->db->where("store_id",get_current_store_id());
			}

			$query1=$this->db->delete("db_custadvance");
			if (!$query1){
				echo "failed";
			}
			#---------------------------------
			//RESET CUSTOMER IDS
	        if($customer_ids->num_rows()>0){
	        	foreach ($customer_ids->result() as $res1) {
	        		if(!set_customer_tot_advance($res1->customer_id)){
						return 'failed';
					}
	        	}
	        }
	        //RESET CUSTOMER IDS end

	        $this->db->trans_commit();
	        echo "success";	
		
	}
}
