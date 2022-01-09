<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tax_model extends CI_Model {

	var $table = 'db_tax';
	var $column_order = array('id','tax_name','tax','status','store_id'); //set column field database for datatable orderable
	var $column_search = array('id','tax_name','tax','status','store_id'); //set column field database for datatable searchable 
	var $order = array('id' => 'desc'); // default order 

	private function _get_datatables_query()
	{
		
		$this->db->from($this->table);
		$this->db->where('group_bit is null');
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

		//Validate This tax already exist or not
		$store_id = (store_module() && is_admin()) ? $store_id : get_current_store_id();
		$query=$this->db->query("select * from db_tax where upper(tax_name)=upper('$tax_name') and store_id=$store_id");
		if($query->num_rows()>0){
			return "Tax Name Already Exist.";
			
		}
		else{
			$info = array(
		    				'tax_name' 				=> $tax_name, 
		    				'tax' 				=> $tax,
		    				'status' 				=> 1,
		    			);
			
			$info['store_id']=(store_module() && is_admin()) ? $store_id : get_current_store_id();	

			$q1 = $this->db->insert('db_tax', $info);
			if ($q1){
					$this->session->set_flashdata('success', 'Success!! New tax Percentage Added Successfully!');
			        return "success";
			}
			else{
			        return "failed";
			}
		}
	}

	//Get tax_details
	public function get_details($id){
		$data=$this->data;
		extract($data);
		extract($_POST);

		//Validate This tax already exist or not
		$query=$this->db->query("select * from db_tax where upper(id)=upper('$id')");
		if($query->num_rows()==0){
			show_404();exit;
		}
		else{
			$query=$query->row();
			$data['q_id']=$query->id;
			$data['tax_name']=$query->tax_name;
			$data['tax']=store_number_format($query->tax,0);
			$data['store_id']=$query->store_id;
			
			return $data;
		}
	}
	public function update_tax(){
		//Filtering XSS and html escape from user inputs 
		extract($this->security->xss_clean(html_escape(array_merge($this->data,$_POST))));
		
		//Validate This tax already exist or not
		$store_id = (store_module() && is_admin()) ? $store_id : get_current_store_id();
		$query=$this->db->query("select * from db_tax where upper(tax_name)=upper('$tax_name') and id<>$q_id and store_id=$store_id");
		if($query->num_rows()>0){
			return "Tax Name Already Exist.";
			
		}
		else{
			$info = array(
		    				'tax_name' 				=> $tax_name, 
		    				'tax' 				=> $tax,
		    			);
			
			$info['store_id']=(store_module() && is_admin()) ? $store_id : get_current_store_id();

			$q1 = $this->db->where('id',$q_id)->where('store_id',$store_id)->update('db_tax', $info);
		
			if ($q1){
					$this->session->set_flashdata('success', 'Success!! tax Percentage Updated Successfully!');
			        return "success";
			}
			else{
			        return "failed";
			}
		}
	}

	public function update_status($id,$status){
       if (set_status_of_table($id,$status,'db_tax')){
            echo "success";
        }
        else{
            echo "failed";
        }
	}
	
	public function delete_tax_from_table($ids){	
		$tot=$this->db->query('SELECT COUNT(*) AS tot,b.tax_name FROM db_items a,`db_tax` b WHERE b.id=a.`tax_id` AND a.tax_id IN ('.$ids.') GROUP BY a.tax_id');

		if($tot->num_rows() > 0){
			foreach($tot->result() as $res){
				$tax_name[] =$res->tax_name;
			}
			$list=implode (",",$tax_name);
			echo "Sorry! Can't Delete,<br>Tax Name {".$list."} already used to add Items!";
			exit();
		}

		$this->db->where("id in ($ids)");
		//if not admin
		if(!is_admin()){
			$this->db->where("store_id",get_current_store_id());
		}

		$query1=$this->db->delete("db_tax");
        if ($query1){
            echo "success";
        }
        else{
            echo "failed";
        }
	}
	
}
