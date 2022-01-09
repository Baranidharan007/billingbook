<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Units_model extends CI_Model {

	var $table = 'db_units';
	var $column_order = array('unit_name','description','status','store_id'); //set column field database for datatable orderable
	var $column_search = array('unit_name','description','status','store_id'); //set column field database for datatable searchable 
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
		$query=$this->db->query("select * from db_units where upper(unit_name)=upper('$unit_name') and store_id=$store_id");
		if($query->num_rows()>0){
			return "This Units Name Already Exist.";
			
		}
		else{
			$info = array(
		    				'store_id' 				=> $store_id, 
		    				'unit_name' 				=> $unit_name, 
		    				'description' 				=> $description,
		    				'status' 				=> 1,
		    			);
			
			$q1 = $this->db->insert('db_units', $info);
			if ($q1){
					$this->session->set_flashdata('success', 'Success!! Unit Name Added Successfully!');
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
		$query=$this->db->query("select * from db_units where upper(id)=upper('$id')");
		if($query->num_rows()==0){
			show_404();exit;
		}
		else{
			$query=$query->row();
			$data['q_id']=$query->id;
			$data['unit_name']=$query->unit_name;
			$data['description']=$query->description;
			$data['store_id']=$query->store_id;
			return $data;
		}
	}
	public function update_unit(){
		//Filtering XSS and html escape from user inputs 
		extract($this->security->xss_clean(html_escape(array_merge($this->data,$_POST))));
		$store_id=(store_module() && is_admin()) ? $store_id : get_current_store_id();  	
		//Validate This units already exist or not
		$query=$this->db->query("select * from db_units where upper(unit_name)=upper('$unit_name') and id<>$q_id and store_id=$store_id");
		if($query->num_rows()>0){
			return "This units Name already Exist.";
			
		}
		else{
			$info = array(
		    				'unit_name' 				=> $unit_name, 
		    				'description' 				=> $description,
		    			);
			
			$info['store_id']=(store_module() && is_admin()) ? $store_id : get_current_store_id();

			$q1 = $this->db->where('id',$q_id)->where('store_id',$store_id)->update('db_units', $info);
		
			if ($q1){
					$this->session->set_flashdata('success', 'Success!! units Updated Successfully!');
			        return "success";
			}
			else{
			        return "failed";
			}
		}
	}
	public function update_status($id,$status){
       if (set_status_of_table($id,$status,'db_units')){
            echo "success";
        }
        else{
            echo "failed";
        }
	}
	public function delete_unit($id){
		$tot_rec = $this->db->select("count(*) as tot")->where("store_id",get_current_store_id())->where("unit_id in($id)")->get("db_items")->row()->tot;
		if($tot_rec>0){
			echo "Can't Delete! Already used to add Items.";exit;
		}
		$this->db->where("id",$id);
		//if not admin
		if(!is_admin()){
			$this->db->where("store_id",get_current_store_id());
		}

		$query1=$this->db->delete("db_units");
        if ($query1){
            echo "success";
        }
        else{
            echo "failed";
        }
	}


}
