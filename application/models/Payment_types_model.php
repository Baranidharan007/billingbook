<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Payment_types_model extends CI_Model {

	var $table = 'db_paymenttypes';
	var $column_order = array('payment_type','status','store_id'); //set column field database for datatable orderable
	var $column_search = array('payment_type','status','store_id'); //set column field database for datatable searchable 
	var $order = array('id' => 'desc'); // default order 

	private function _get_datatables_query()
	{
		
		$this->db->from($this->table);
		//if not admin
		//if(!is_admin()){
			$this->db->where("store_id",get_current_store_id());
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


	public function verify_and_save(){
		//Filtering XSS and html escape from user inputs 
		extract($this->security->xss_clean(html_escape(array_merge($this->data,$_POST))));
		$store_id=(store_module() && is_admin()) ? $store_id : get_current_store_id();  	
		//Validate This units already exist or not
		$query=$this->db->query("select * from db_paymenttypes where upper(payment_type)=upper('$payment_type_name') and store_id=$store_id");
		if($query->num_rows()>0){
			return "This Payment Type Name Already Exist.";
			
		}
		else{
			$info = array(
		    				'store_id' 				=> $store_id,
		    				'payment_type' 				=> $payment_type_name,
		    				'status' 				=> 1,
		    			);

			$q1 = $this->db->insert('db_paymenttypes', $info);
			
			if ($q1){
					$this->session->set_flashdata('success', 'Success!! Record Added Successfully!');
			        return "success";
			}
			else{
			        return "failed";
			}
		}
	}

	//Get units_details
	public function get_details($id,$data){
		//Validate This units already exist or not
		$query=$this->db->query("select * from db_paymenttypes where upper(id)=upper('$id')");
		if($query->num_rows()==0){
			show_404();exit;
		}
		else{
			$query=$query->row();
			$data['q_id']=$query->id;
			$data['payment_type_name']=$query->payment_type;
			$data['store_id']=$query->store_id;
			return $data;
		}
	}
	public function update_payment_type(){
		//Filtering XSS and html escape from user inputs 
		extract($this->security->xss_clean(html_escape(array_merge($this->data,$_POST))));
		$store_id=(store_module() && is_admin()) ? $store_id : get_current_store_id();  	
		//Validate This units already exist or not
		$query=$this->db->query("select * from db_paymenttypes where upper(payment_type)=upper('$payment_type_name') and id<>$q_id and store_id=$store_id");
		if($query->num_rows()>0){
			return "This Payment Type Name Already Exist.";
			
		}
		else{
			$info = array(
		    				'payment_type' 				=> $payment_type_name,
		    			);
			
			$q1 = $this->db->where('id',$q_id)->where('store_id',$store_id)->update('db_paymenttypes', $info);
			
			if ($q1){
					$this->session->set_flashdata('success', 'Success!! Record Updated Successfully!');
			        return "success";
			}
			else{
			        return "failed";
			}
		}
	}
	public function update_status($id,$status){
       if (set_status_of_table($id,$status,'db_paymenttypes')){
            echo "success";
        }
        else{
            echo "failed";
        }
	}
	
	public function delete_payment_type($id){
		/*$this->db->select("count(*) as tot");
		$this->db->select("b.payment_type");
		$this->db->from("db_salespayments a");
		$this->db->from("db_paymenttypes b");
		$this->db->where("upper(b.payment_type)=upper(a.`payment_type`)");
		$this->db->where("b.store_id",get_current_store_id());
		$this->db->where("b.id",$id);
		//echo $this->db->get_compiled_select();exit;
		$tot=$this->db->get()->row()->tot;

		if($tot> 0){
			echo "Sorry! Can't Delete,<br>Payment Type already used in Sales Payment Receive!";
			exit();
		}*/

		$this->db->where("id",$id);
		//if not admin
		if(!is_admin()){
			$this->db->where("store_id",get_current_store_id());
		}

		$query1=$this->db->delete("db_paymenttypes");
        if ($query1){
            echo "success";
        }
        else{
            echo "failed";
        }
	}


}
