<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Stock_adjustment_model extends CI_Model {
	//Datatable start
	var $table = 'db_stockadjustment as a';
	var $column_order = array( 
								'a.id',
								'a.adjustment_date',
								'a.reference_no',
								'a.created_by',
								'a.store_id'
								); //set column field database for datatable orderable
	var $column_search = array( 
								'a.id',
								'a.adjustment_date',
								'a.reference_no',
								'a.created_by',
								'a.store_id'
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
		
		/*If warehouse selected*/
		$warehouse_id = $this->input->post('warehouse_id');
		if(!empty($warehouse_id)){
			$this->db->from('db_warehouse as w');
			$this->db->where('a.warehouse_id=w.id');
			$this->db->where('w.id',$warehouse_id);
		}

		
		//if not admin
	    /*if(!is_admin()){*/
	      $this->db->where("a.store_id",get_current_store_id());
	    /*}*/
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

	public function xss_html_filter($input){
		return $this->security->xss_clean(html_escape($input));
	}

	//Save Cutomers
	public function verify_save_and_update(){
		//Filtering XSS and html escape from user inputs 
		extract($this->xss_html_filter(array_merge($this->data,$_POST,$_GET)));
		//echo "<pre>";print_r($this->xss_html_filter(array_merge($this->data,$_POST,$_GET)));exit();
		
		$this->db->trans_begin();
		$adjustment_date=system_fromatted_date($adjustment_date);

	    $prev_item_ids = array();
	    
	    $store_id = (store_module() && is_admin()) ? $store_id : get_current_store_id();  
	    $warehouse_id=(warehouse_module() && warehouse_count()>1) ? $warehouse_id : get_store_warehouse_id(); 	
	    if($command=='save'){//Create purchase code unique if first time entry
		    
			$this->db->query("ALTER TABLE db_stockadjustment AUTO_INCREMENT = 1");
			
		    $purchase_entry = array(
		    				'store_id' 				=> $store_id, 
		    				'warehouse_id' 				=> $warehouse_id, 
		    				'reference_no' 				=> $reference_no, 
		    				'adjustment_date' 			=> $adjustment_date,
		    				'adjustment_note' 			=> $adjustment_note,
		    				/*System Info*/
		    				'created_date' 				=> $CUR_DATE,
		    				'created_time' 				=> $CUR_TIME,
		    				'created_by' 				=> $CUR_USERNAME,
		    				'system_ip' 				=> $SYSTEM_IP,
		    				'system_name' 				=> $SYSTEM_NAME,
		    				'status' 					=> 1,
		    			);
		    
		     	
			$q1 = $this->db->insert('db_stockadjustment', $purchase_entry);
			$adjustment_id = $this->db->insert_id();
		}
		else if($command=='update'){	
			$purchase_entry = array(
		    				'store_id' 				=> $store_id, 
		    				'warehouse_id' 				=> $warehouse_id, 
		    				'reference_no' 				=> $reference_no, 
		    				'adjustment_date' 			=> $adjustment_date,
		    				'adjustment_note' 			=> $adjustment_note,
		    			);
			
			
			$q1 = $this->db->where('id',$adjustment_id)->update('db_stockadjustment', $purchase_entry);

			##############################################START
			//FIND THE PREVIOUSE ITEM LIST ID'S
			$prev_item_ids = $this->db->select("item_id")->from("db_stockadjustmentitems")->where("adjustment_id",$adjustment_id)->get()->result_array();
			##############################################END

			$q11=$this->db->query("delete from db_stockadjustmentitems where adjustment_id='$adjustment_id'");
			if(!$q11){
				return "failed";
			}
		}
		//end

		

		//Import post data from form
		for($i=1;$i<=$rowcount;$i++){
		
			if(isset($_REQUEST['tr_item_id_'.$i]) && !empty($_REQUEST['tr_item_id_'.$i])){

				$item_id 			=$this->xss_html_filter(trim($_REQUEST['tr_item_id_'.$i]));
				$adjustment_qty		=$this->xss_html_filter(trim($_REQUEST['td_data_'.$i.'_3']));
				$description		=$this->xss_html_filter(trim($_REQUEST['description_'.$i]));

				$adjustment_entry = array(
							'store_id' 				=> $store_id, 
		    				'warehouse_id' 				=> $warehouse_id, 
		    				'adjustment_id' 		=> $adjustment_id,
		    				'item_id' 			=> $item_id,
		    				'adjustment_qty' 		=> $adjustment_qty,
		    				'description' 		=> $description, 
		    				'status'			=> 1,
		    			);
				
				$q2 = $this->db->insert('db_stockadjustmentitems', $adjustment_entry);
				
				//UPDATE itemS QUANTITY IN itemS TABLE
				$this->load->model('pos_model');				
				$q6=$this->pos_model->update_items_quantity($item_id);
				if(!$q6){
					return "failed";
				}

				
			}
		
		}//for end

	
		##############################################START
		//FIND THE PREVIOUSE ITEM LIST ID'S
		$curr_item_ids = $this->db->select("item_id")->from("db_stockadjustmentitems")->where("adjustment_id",$adjustment_id)->get()->result_array();
		$two_array = array_merge($prev_item_ids,$curr_item_ids);

		/*Update items in all warehouses of the item*/
		$q7=update_warehouse_items($two_array);
		if(!$q7){
			return "failed";
		}
		##############################################END
		
		$this->db->trans_commit();
		$this->session->set_flashdata('success', 'Success!! Record Saved Successfully!');
		return "success<<<###>>>$adjustment_id";
		
	}//verify_save_and_update() function end




	public function delete_stock_adjustment($ids){
      	$this->db->trans_begin();

      	##############################################START
		//FIND THE PREVIOUSE ITEM LIST ID'S
		$prev_item_ids = $this->db->select("item_id")->from("db_stockadjustmentitems")->where("adjustment_id in ($ids)")->get()->result_array();
		##############################################END

		#----------------------------------
		$this->db->where("id in ($ids)");
		//if not admin
		if(!is_admin()){
			$this->db->where("store_id",get_current_store_id());
		}

		$q3=$this->db->delete("db_stockadjustment");
		#----------------------------------
		#----------------------------------
		$this->db->where("adjustment_id in ($ids)");
		//if not admin
		if(!is_admin()){
			$this->db->where("store_id",get_current_store_id());
		}

		$q7=$this->db->delete("db_stockadjustmentitems");
		#----------------------------------

		$q6=$this->db->query("select id from db_items");
		if($q6->num_rows()>0){
			$this->load->model('pos_model');				
			foreach ($q6->result() as $res6) {
				$q6=$this->pos_model->update_items_quantity($res6->id);
				if(!$q6){
					return "failed";
				}
			}
		}

		##############################################START
		/*Update items in all warehouses of the item*/
		$q7=update_warehouse_items($prev_item_ids);
		if(!$q7){
			return "failed";
		}
		##############################################END
		
		
		if($q3!=1)
		{
			$this->db->trans_rollback();
		    return "failed";
		}
		else{
			$this->db->trans_commit();
		        return "success";
		}
	}
	
	
	
	public function get_items_info($rowcount,$item_id){
		$res1=$this->db->select('*')->from('db_items')->where("id=$item_id")->get()->row();
		
		$info = array(
							'item_id' 					=> $res1->id, 
							'description' 				=> $res1->description, 
							'item_name' 				=> $res1->item_name,
							'item_adjustment_qty' 		=> 1, 
							'service_bit' 				=> $res1->service_bit, 
						);

		$this->return_row_with_data($rowcount,$info);
	}

	/* For Stock_adjustment Items List Retrieve*/
	public function return_stock_adjustment_list($adjustment_id){
		$q1=$this->db->select('*')->from('db_stockadjustmentitems')->where("adjustment_id=$adjustment_id")->get();
		$rowcount =1;
		foreach ($q1->result() as $res1) {
			$res2=$this->db->query("select * from db_items where id=".$res1->item_id)->row();
			
			$info = array(
							'item_id' 					=> $res1->item_id, 
							'description' 				=> $res1->description, 
							'item_name' 				=> $res2->item_name,
							'item_adjustment_qty' 		=> $res1->adjustment_qty, 
							'service_bit' 				=> $res2->service_bit, 
						);

			$result = $this->return_row_with_data($rowcount++,$info);
		}
		return $result;
	}

	public function return_row_with_data($rowcount,$info){
		extract($info);
		
		
	
		?>
            <tr id="row_<?=$rowcount;?>" data-row='<?=$rowcount;?>'>
               <td id="td_<?=$rowcount;?>_1">
                  <label class='form-control' style='height:auto;' data-toggle="tooltip" title='Edit ?' >
                  <a id="td_data_<?=$rowcount;?>_1" href="javascript:void()" onclick="show_purchase_item_modal(<?=$rowcount;?>)" title=""><?=$item_name;?></a> 
                  		<i onclick="show_purchase_item_modal(<?=$rowcount;?>)" class="fa fa-edit pointer"></i>
                  	</label>
               </td>
               <!-- Qty -->
               <td id="td_<?=$rowcount;?>_3">
                  <div class="input-group ">
                     <span class="input-group-btn">
                     <button onclick="decrement_qty(<?=$rowcount;?>)" type="button" class="btn btn-default btn-flat"><i class="fa fa-minus text-danger"></i></button></span>
                     <input typ="text" value="<?=$item_adjustment_qty;?>" class="form-control no-padding text-center" onkeyup="calculate_tax(<?=$rowcount;?>)" id="td_data_<?=$rowcount;?>_3" name="td_data_<?=$rowcount;?>_3">
                     <span class="input-group-btn">
                     <button onclick="increment_qty(<?=$rowcount;?>)" type="button" class="btn btn-default btn-flat"><i class="fa fa-plus text-success"></i></button></span>
                  </div>
               </td>
              

               <!-- ADD button -->
               <td id="td_<?=$rowcount;?>_16" style="text-align: center;">
                  <a class=" fa fa-fw fa-minus-square text-red" style="cursor: pointer;font-size: 34px;" onclick="removerow(<?=$rowcount;?>)" title="Delete ?" name="td_data_<?=$rowcount;?>_16" id="td_data_<?=$rowcount;?>_16"></a>
               </td>
               
               <input type="hidden" id="tr_item_id_<?=$rowcount;?>" name="tr_item_id_<?=$rowcount;?>" value="<?=$item_id;?>">

               <input type="hidden" id="description_<?=$rowcount;?>" name="description_<?=$rowcount;?>" value="<?=$description;?>">
               <input type="hidden" id="service_bit_<?=$rowcount;?>" name="service_bit_<?=$rowcount;?>" value="<?=$service_bit;?>">
               
            </tr>
		<?php

	}

}
