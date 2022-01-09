<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Stock_transfer_model extends CI_Model {

	//Datatable start
	var $table = 'db_stocktransfer as a';
	var $column_order = array( 'a.id','a.transfer_date','a.note','a.warehouse_from','a.warehouse_to','a.created_by','a.store_id'); //set column field database for datatable orderable
	var $column_search = array('a.id','a.transfer_date','a.note','a.warehouse_from','a.warehouse_to','a.created_by','a.store_id'); //set column field database for datatable searchable 
	var $order = array('a.id' => 'desc'); // default order  

	public function __construct()
	{
		parent::__construct();
		$CI =& get_instance();
	}

	private function _get_datatables_query()
	{
		
		$this->db->select($this->column_order);
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
	//Datatable end

	public function xss_html_filter($input){
		return $this->security->xss_clean(html_escape($input));
	}

	//Save Stock
	public function verify_save_and_update(){
		//Filtering XSS and html escape from user inputs 
		extract($this->xss_html_filter(array_merge($this->data,$_POST,$_GET)));
		//echo "<pre>";print_r($this->xss_html_filter(array_merge($this->data,$_POST,$_GET)));exit();
		
		$this->db->trans_begin();
		$transfer_date=system_fromatted_date($transfer_date);

		$store_id=(store_module() && is_admin()) ? $store_from : get_current_store_id();  	
		//$to_store_id=(store_module() && is_admin()) ? $store_to : get_current_store_id();  	
		
		$prev_item_ids = array();

	    if($command=='save'){//Create stock code unique if first time entry

			$this->db->query("ALTER TABLE db_stocktransfer AUTO_INCREMENT = 1");
			
		    $stock_entry = array(
		    				'note' 						=> $note, 
		    				'store_id' 					=> $store_id, 
		    				//'to_store_id' 				=> $to_store_id, 
		    				'transfer_date' 			=> $transfer_date,
		    				'warehouse_from' 			=> $warehouse_from,
		    				'warehouse_to' 				=> $warehouse_to,
		    				/*System Info*/
		    				'created_date' 				=> $CUR_DATE,
		    				'created_time' 				=> $CUR_TIME,
		    				'created_by' 				=> $CUR_USERNAME,
		    				'system_ip' 				=> $SYSTEM_IP,
		    				'system_name' 				=> $SYSTEM_NAME,
		    				'status' 					=> 1,
		    			);
		   
			$q1 = $this->db->insert('db_stocktransfer', $stock_entry);
			$stocktransfer_id = $this->db->insert_id();
		}
		else if($command=='update'){	
			$stock_entry = array(
							'store_id' 					=> $store_id, 
		    				//'to_store_id' 				=> $to_store_id, 
		    				'note' 						=> $note, 
		    				'transfer_date' 			=> $transfer_date,
		    				'warehouse_from' 			=> $warehouse_from,
		    				'warehouse_to' 				=> $warehouse_to,
		    			);
		
			$q1 = $this->db->where('id',$stocktransfer_id)->update('db_stocktransfer', $stock_entry);

			##############################################START
			//FIND THE PREVIOUSE ITEM LIST ID'S
			$prev_item_ids = $this->db->select("item_id")->from("db_stocktransferitems")->where("stocktransfer_id",$stocktransfer_id)->get()->result_array();
			##############################################END

			$q11=$this->db->query("delete from db_stocktransferitems where stocktransfer_id='$stocktransfer_id'");
			if(!$q11){
				return "failed";
			}
		}
		//end

		//Import post data from form
		for($i=1;$i<=$rowcount;$i++){
		
			if(isset($_REQUEST['tr_item_id_'.$i]) && !empty($_REQUEST['tr_item_id_'.$i])){

				$item_id 			=$this->xss_html_filter(trim($_REQUEST['tr_item_id_'.$i]));
				$transfer_qty		=$this->xss_html_filter(trim($_REQUEST['td_data_'.$i.'_3']));
				

				//find item details
				$res_1 = $this->db->select("*")->where("id",$item_id)->get("db_items")->row();
				$item_name = $res_1->item_name;
				$category_id = $res_1->category_id;
				$brand_id 	 = $res_1->brand_id;
				$unit_id 	 = $res_1->unit_id;
				$tax_id 	 = $res_1->tax_id;

				
				$stockitems_entry = array(
							'store_id' 			=> $store_id, 
		    				//'to_store_id' 		=> $to_store_id, 
		    				'stocktransfer_id' 	=> $stocktransfer_id, 
		    				'item_id' 			=> $item_id, 
		    				'warehouse_from' 	=> $warehouse_from,
		    				'warehouse_to' 		=> $warehouse_to,
		    				'transfer_qty' 		=> $transfer_qty,
		    				'status'	 		=> 1,
		    			);
				
				$q2 = $this->db->insert('db_stocktransferitems', $stockitems_entry);
				
			}
		
		}//for end


		/*Update all store warehouses*/
		/*$q_1 = $this->db->select("id")->get("db_store");
		foreach($q_1->result() as $res1){
			$q_2 = update_warehousewise_items_qty_by_store($res1->id);
			if(!$q_2){
				return "failed";
			}
		}*/
		##############################################START
		//FIND THE PREVIOUSE ITEM LIST ID'S
		$curr_item_ids = $this->db->select("item_id")->from("db_stocktransferitems")->where("stocktransfer_id",$stocktransfer_id)->get()->result_array();
		$two_array = array_merge($prev_item_ids,$curr_item_ids);

		/*Update items in all warehouses of the item*/
		$q7=update_warehouse_items($two_array);
		if(!$q7){
			return "failed";
		}
		##############################################END
		/**/

		$this->db->trans_commit();
		$this->session->set_flashdata('success', 'Success!! Record Saved Successfully! ');
		return "success<<<###>>>$stocktransfer_id";
		
	}//verify_save_and_update() function end


	//Get stock_details
	public function get_details($id,$data){
		//Validate This stock already exist or not
		$query=$this->db->query("select * from db_stocktransfer where upper(id)=upper('$id')");
		if($query->num_rows()==0){
			show_404();exit;
		}
		else{
			$query=$query->row();
			$data['stocktransfer_id']=$query->id;
			$data['note']=$query->note;
			$data['warehouse_from']=$query->warehouse_from;
			$data['warehouse_to']=$query->warehouse_to;
			$data['store_id']=$query->store_id;
			return $data;
		}
	}

	public function delete_stock($ids){
      	$this->db->trans_begin();
      	##############################################START
		//FIND THE PREVIOUSE ITEM LIST ID'S
		$prev_item_ids = $this->db->select("item_id")->from("db_stocktransferitems")->where("stocktransfer_id in ($ids)")->get()->result_array();
		##############################################END

		#----------------------------------
		$this->db->where("id in ($ids)");
		//if not admin
		if(!is_admin()){
			$this->db->where("store_id",get_current_store_id());
		}

		$q3=$this->db->delete("db_stocktransfer");
		if(!$q3){
			return "failed";
		}
		#----------------------------------
		
		##############################################START
		/*Update items in all warehouses of the item*/
		$q7=update_warehouse_items($prev_item_ids);
		if(!$q7){
			return "failed";
		}
		##############################################END
		
		
		$this->db->trans_commit();
		return "success";
	}
	public function search_item($q){
		$json_array=array();
        $query1="select id,item_name from db_items where (upper(item_name) like upper('%$q%') or upper(item_code) like upper('%$q%'))";

        $q1=$this->db->query($query1);
        if($q1->num_rows()>0){
            foreach ($q1->result() as $value) {
            	$json_array[]=['id'=>(int)$value->id, 'text'=>$value->item_name];
            }
        }
        return json_encode($json_array);
	}
	
	public function find_item_details($id){
		$json_array=array();
        $query1="select id,hsn,alert_qty,unit_name,purchase_price,sales_price,gst_percentage,available_qty from db_items where id=$id";

        $q1=$this->db->query($query1);
        if($q1->num_rows()>0){
            foreach ($q1->result() as $value) {
            	$json_array=['id'=>$value->id, 
        			 'hsn'=>$value->hsn,
        			 'alert_qty'=>$value->alert_qty,
        			 'unit_name'=>$value->unit_name,
        			 'purchase_price'=>$value->purchase_price,
        			 'sales_price'=>$value->sales_price,
        			 'gst_percentage'=>$value->gst_percentage,
        			 'available_qty'=>$value->available_qty,
        			];
            }
        }
        return json_encode($json_array);
	}

	
	public function get_items_info($rowcount,$item_id){
		extract($this->xss_html_filter(array_merge($this->data,$_POST,$_GET)));
		$q1=$this->db->select('*')->from('db_items')->where("id=$item_id")->get();
		$tax=$this->db->query("select tax from db_tax where id=".$q1->row()->tax_id)->row()->tax;

		$info['item_id'] = $q1->row()->id;
		$info['item_name'] = $q1->row()->item_name;
		$info['item_available_qty'] = total_available_qty_items_of_warehouse($warehouse_id,null,$q1->row()->id);
		$info['item_stock_qty'] = ($info['item_available_qty']>1) ? 1 : $info['item_available_qty'];
		$this->return_row_with_data($rowcount,$info);
	}
	/* For Purchase Items List Retrieve*/
	public function return_stock_list($stock_id){
		$q1=$this->db->select('*')->from('db_stocktransferitems')->where("stocktransfer_id=$stock_id")->get();
		$rowcount =1;
		foreach ($q1->result() as $res1) {
			$q2=$this->db->query("select id,item_name,stock,price,sales_price,tax_type from db_items where id=".$res1->item_id);
			$warehouse_id = $res1->warehouse_from;
			$info['item_id'] = $res1->item_id;
			$info['item_name'] = $q2->row()->item_name;
			$info['item_available_qty'] = total_available_qty_items_of_warehouse($warehouse_id,null,$q2->row()->id)+$res1->transfer_qty;
			$info['item_stock_qty'] = $res1->transfer_qty;
			$result = $this->return_row_with_data($rowcount++,$info);
		}
		return $result;
	}

	public function return_row_with_data($rowcount,$info){
		extract($info);
		
		?>
            <tr id="row_<?=$rowcount;?>" data-row='<?=$rowcount;?>'>
               <td id="td_<?=$rowcount;?>_1">
                  <!-- item name  -->
                  <input type="text" style="font-weight: bold;" id="td_data_<?=$rowcount;?>_1" class="form-control no-padding" value='<?=$item_name;?>' readonly >
               </td>
               <!-- Qty -->
               <td id="td_<?=$rowcount;?>_3">
                  <div class="input-group ">
                     <span class="input-group-btn">
                     <button onclick="decrement_qty(<?=$rowcount;?>)" type="button" class="btn btn-default btn-flat"><i class="fa fa-minus text-danger"></i></button></span>
                     <input typ="text" value="<?=$item_stock_qty;?>" class="form-control no-padding text-center" onkeyup="calculate_tax(<?=$rowcount;?>)" id="td_data_<?=$rowcount;?>_3" name="td_data_<?=$rowcount;?>_3">
                     <span class="input-group-btn">
                     <button onclick="increment_qty(<?=$rowcount;?>)" type="button" class="btn btn-default btn-flat"><i class="fa fa-plus text-success"></i></button></span>
                  </div>
               </td>
               
               <!-- ADD button -->
               <td id="td_<?=$rowcount;?>_16" style="text-align: center;">
                  <a class=" fa fa-fw fa-minus-square text-red" style="cursor: pointer;font-size: 34px;" onclick="removerow(<?=$rowcount;?>)" title="Delete ?" name="td_data_<?=$rowcount;?>_16" id="td_data_<?=$rowcount;?>_16"></a>
               </td>
            
               <input type="hidden" id="tr_available_qty_<?=$rowcount;?>_13" value="<?=$item_available_qty;?>">
               <input type="hidden" id="tr_item_id_<?=$rowcount;?>" name="tr_item_id_<?=$rowcount;?>" value="<?=$item_id;?>">
            </tr>
		<?php

	}


}
