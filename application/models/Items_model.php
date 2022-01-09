<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Items_model extends CI_Model {

	//Datatable start
	var $table = 'db_items as a';
	var $column_order = array( 
								'a.id',
								'a.item_image',
								'a.item_code',
								'a.item_name',
								'e.brand_name',
								'b.category_name',
								'c.unit_name',
								'a.stock',
								'a.alert_qty',
								'a.sales_price',
								'd.tax_name',
								'a.service_bit',
								'd.tax',
								'a.status',
								'a.store_id',
								'a.sku',
								'a.hsn',
								'a.item_group',
								); //set column field database for datatable orderable
	var $column_search = array( 
								'a.id',
								'a.item_image',
								'a.item_code',
								'a.item_name',
								'e.brand_name',
								'b.category_name',
								'c.unit_name',
								'a.stock',
								'a.alert_qty',
								'a.sales_price',
								'd.tax_name',
								'a.service_bit',
								'd.tax',
								'a.status',
								'a.store_id',
								'a.sku',
								'a.hsn',
								'a.item_group',
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
		$this->db->join('db_category as b',"b.id=a.category_id","left");
		$this->db->join('db_units as c',"c.id=a.unit_id","left");
		$this->db->join('db_tax as d',"d.id=a.tax_id","left");
		$this->db->select("CASE WHEN e.brand_name IS NULL THEN '' ELSE e.brand_name END AS brand_name");
		$this->db->join('db_brands as e','e.id=a.brand_id','left');
		

		/*If warehouse selected*/
		$warehouse_id = $this->input->post('warehouse_id');
		$item_type = $this->input->post('item_type');

		if(!empty($warehouse_id)){
			/*$this->db->from('db_warehouseitems as w');
			$this->db->where('a.id=w.item_id');
			$this->db->where('w.warehouse_id',$warehouse_id);*/
		}
		if($item_type=='Items'){
			$this->db->where('a.service_bit=0');
		}
		if($item_type=='Services'){
			$this->db->where('a.service_bit=1');
		}

		//if not admin
		//if(!is_admin()){
			$this->db->where("a.store_id",get_current_store_id());
		//}
		
		$this->db->where("a.child_bit=0");

		//	echo $this->db->get_compiled_select();exit();
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
	public function verify_and_save(){
		//Filtering XSS and html escape from user inputs 
		extract($this->security->xss_clean(html_escape(array_merge($this->data,$_POST,$_GET))));

		//varify max sales usage of the package subscription
		validate_package_offers('max_items','db_items');
		//END

		$this->db->trans_begin();
		$this->db->trans_strict(TRUE);

		$file_name='';
		if(!empty($_FILES['item_image']['name'])){

			$new_name = time();
			$config['file_name'] = $new_name;
			$config['upload_path']          = './uploads/items/';
	        $config['allowed_types']        = 'jpg|png|jpeg';
	        $config['max_size']             = 1024;
	        $config['max_width']            = 1500;
	        $config['max_height']           = 1500;
	       
	        $this->load->library('upload', $config);

	        if ( ! $this->upload->do_upload('item_image'))
	        {	
	                $error = array('error' => $this->upload->display_errors());
	                print($error['error']);
	                exit();
	        }
	        else
	        {		
	        	$file_name=$this->upload->data('file_name');
	        	/*Create Thumbnail*/
	        	$config['image_library'] = 'gd2';
				$config['source_image'] = 'uploads/items/'.$file_name;
				$config['create_thumb'] = TRUE;
				$config['maintain_ratio'] = TRUE;
				$config['width']         = 75;
				$config['height']       = 50;
				$this->load->library('image_lib', $config);
				$this->image_lib->resize();
				//end

	        	
	        }
		}
		
		//Validate This items already exist or not
		$store_id=(store_module() && is_admin()) ? $store_id : get_current_store_id();
		/*$query=$this->db->query("select * from db_items where upper(item_name)=upper('$item_name') and store_id=$store_id");
		if($query->num_rows()>0){
			return "Sorry! This Items Name already Exist.";
		}*/
		
		//Create items unique Number
		$this->db->query("ALTER TABLE db_items AUTO_INCREMENT = 1");
		//end

		
		//$stock = $current_opening_stock + $new_opening_stock;

		$alert_qty = empty(trim($alert_qty)) ? '0' : $alert_qty;
		$profit_margin = (empty(trim($profit_margin))) ? 'null' : $profit_margin;

		
		#------------------------------------
		$info = array(
							'count_id' 					=> get_count_id('db_items'), 
		    				'item_code' 				=> get_init_code('item'), 
		    				'item_name' 				=> $item_name,
		    				'brand_id' 					=> $brand_id,
		    				'category_id' 				=> $category_id,
		    				'sku' 						=> $sku,
		    				'hsn' 						=> $hsn,
		    				'unit_id' 					=> $unit_id,
		    				'alert_qty' 				=> $alert_qty,
		    				
		    				'price' 					=> $price,
		    				'tax_id' 					=> $tax_id,
		    				'purchase_price' 			=> $purchase_price,
		    				'tax_type' 					=> $tax_type,
		    				'profit_margin' 			=> $profit_margin,
		    				'sales_price' 				=> $sales_price,
		    				/*System Info*/
		    				'created_date' 				=> $CUR_DATE,
		    				'created_time' 				=> $CUR_TIME,
		    				'created_by' 				=> $CUR_USERNAME,
		    				'system_ip' 				=> $SYSTEM_IP,
		    				'system_name' 				=> $SYSTEM_NAME,
		    				'status' 					=> 1,
		    				'seller_points'				=> $seller_points,
		    				'custom_barcode'			=> $custom_barcode,
		    				'description'				=> $description,
		    				'item_group'				=> $item_group,
		    			);
			
		$info['store_id']=(store_module() && is_admin()) ? $store_id : get_current_store_id();	

		$query1 = $this->db->insert('db_items', $info);
		#------------------------------------
		if(!$query1){
			return "failed";
		}
		
		$item_id = $this->db->insert_id();
		/*$q1=$this->stock_entry($CUR_DATE,$item_id,$info['store_id']);
			if(!$q1){
				return "failed";
			}*/

		//Insert Variants in db_items table
		if($existing_row_count>0){
			for($i=1;$i<=$existing_row_count;$i++){
				if(isset($_REQUEST['tr_variant_id_'.$i]) && !empty($_REQUEST['tr_variant_id_'.$i])){

					$variant_id 		=$this->xss_html_filter(trim($_REQUEST['tr_variant_id_'.$i]));
					$sku				=$this->xss_html_filter(trim($_REQUEST['td_data_'.$i.'_2']));
					$hsn				=$this->xss_html_filter(trim($_REQUEST['td_data_'.$i.'_9']));
					$custom_barcode 	=$this->xss_html_filter(trim($_REQUEST['td_data_'.$i.'_8']));
					$price 			 	=$this->xss_html_filter(trim($_REQUEST['td_data_'.$i.'_3']));
					$purchase_price	 	=$this->xss_html_filter(trim($_REQUEST['td_data_'.$i.'_4']));
					$profit_margin	 	=$this->xss_html_filter(trim($_REQUEST['td_data_'.$i.'_5']));
					$sales_price	 	=$this->xss_html_filter(trim($_REQUEST['td_data_'.$i.'_6']));
					
					$variant_details = $this->db->select("*")->where("id",$variant_id)->get("db_variants")->row();
					$variant_name = $variant_details->variant_name;

					$info = array(
							'count_id' 					=> get_count_id('db_items'), 
		    				'item_code' 				=> get_init_code('item'), 
		    				'item_name' 				=> $item_name."-".$variant_name,
		    				'brand_id' 					=> $brand_id,
		    				'category_id' 				=> $category_id,
		    				'sku' 						=> $sku,
		    				'hsn' 						=> $hsn,
		    				'unit_id' 					=> $unit_id,
		    				'alert_qty' 				=> $alert_qty,
		    				
		    				'price' 					=> $price,
		    				'tax_id' 					=> $tax_id,
		    				'purchase_price' 			=> $purchase_price,
		    				'tax_type' 					=> $tax_type,
		    				'profit_margin' 			=> $profit_margin,
		    				'sales_price' 				=> $sales_price,
		    				/*System Info*/
		    				'created_date' 				=> $CUR_DATE,
		    				'created_time' 				=> $CUR_TIME,
		    				'created_by' 				=> $CUR_USERNAME,
		    				'system_ip' 				=> $SYSTEM_IP,
		    				'system_name' 				=> $SYSTEM_NAME,
		    				'status' 					=> 1,
		    				'seller_points'				=> $seller_points,
		    				'custom_barcode'			=> $custom_barcode,
		    				'description'				=> $description,
		    				//'item_group'				=> 'Subvariant',
		    				'parent_id'					=> $item_id,
		    				'child_bit'					=> 1,
		    				'variant_id'				=> $variant_id,
		    			);
							
						$info['store_id']=(store_module() && is_admin()) ? $store_id : get_current_store_id();	

						$query1 = $this->db->insert('db_items', $info);
						#------------------------------------
						if(!$query1){
							return "failed";
						}
				}
			
			}//for end
		}

		
		if ($query1){
				
				if(!empty($file_name)){
					//echo "update db_items set item_image ='$file_name' where id=".$item_id;exit();
					$q1=$this->db->query("update db_items set item_image ='uploads/items/$file_name' where id=".$item_id);
				}
				//$this->db->query("update db_items set expire_date=null where expire_date='0000-00-00'");
				$this->db->trans_commit();
				$this->session->set_flashdata('success', 'Success!! New Item Added Successfully!');
		        return "success";
		}
		else{
				$this->db->trans_rollback();
				//unlink('uploads/items/'.$file_name);
		        return "failed";
		}
		
	}

	//Get items_details
	public function get_details($id,$data){
		//Validate This items already exist or not
		$query=$this->db->query("select * from db_items where upper(id)=upper('$id')");
		if($query->num_rows()==0){
			show_404();exit;
		}
		else{
			$query=$query->row();
			$data['q_id']=$query->id;
			$data['store_id']=$query->store_id;
			$data['item_code']=$query->item_code;
			$data['item_name']=$query->item_name;
			$data['brand_id']=$query->brand_id;
			$data['category_id']=$query->category_id;
			$data['sku']=$query->sku;
			$data['hsn']=$query->hsn;
			$data['unit_id']=$query->unit_id;
			$data['alert_qty']=$query->alert_qty;
			$data['price']=store_number_format($query->price,0);
			$data['tax_id']=$query->tax_id;
			$data['purchase_price']=store_number_format($query->purchase_price,0);
			$data['tax_type']=$query->tax_type;
			$data['profit_margin']=$query->profit_margin;
			$data['sales_price']=store_number_format($query->sales_price,0);
			$data['stock']=$query->stock;
			
			$data['seller_points']=$query->seller_points;
			$data['custom_barcode']=$query->custom_barcode;
			$data['description']=$query->description;
			$data['item_group']=$query->item_group;
			
			return $data;
		}
	}
	public function update_items(){
		//Filtering XSS and html escape from user inputs 
		extract($this->security->xss_clean(html_escape(array_merge($this->data,$_POST,$_GET))));
		
		//Validate This items already exist or not
		$store_id=(store_module() && is_admin()) ? $store_id : get_current_store_id();
		$this->db->trans_begin();
		/*$query=$this->db->query("select * from db_items where upper(item_name)=upper('$item_name') and id<>$q_id and store_id=$store_id");
		if($query->num_rows()>0){
			return "This Items Name already Exist.";
		}
		else{*/

			$file_name=$item_image='';
			if(!empty($_FILES['item_image']['name'])){

				$new_name = time();
				$config['file_name'] = $new_name;
				$config['upload_path']          = './uploads/items/';
		        $config['allowed_types']        = 'jpg|png';
		        $config['max_size']             = 1024;
		        $config['max_width']            = 1500;
		        $config['max_height']           = 1500;
		       
		        $this->load->library('upload', $config);

		        if ( ! $this->upload->do_upload('item_image'))
		        {
		                $error = array('error' => $this->upload->display_errors());
		                print($error['error']);
		                exit();
		        }
		        else
		        {		
		        	$file_name=$this->upload->data('file_name');
		        	
		        	/*Create Thumbnail*/
		        	$config['image_library'] = 'gd2';
					$config['source_image'] = 'uploads/items/'.$file_name;
					$config['create_thumb'] = TRUE;
					$config['maintain_ratio'] = TRUE;
					$config['width']         = 75;
					$config['height']       = 50;
					$this->load->library('image_lib', $config);
					$this->image_lib->resize();
					//end

					//$item_image=" ,item_image='".$config['source_image']."' ";
					$item_image=$config['source_image'];

		        }
			}

			//$stock = $current_opening_stock + $new_opening_stock;
			$alert_qty = (empty(trim($alert_qty))) ? '0' : $alert_qty;
			$profit_margin = (empty(trim($profit_margin))) ? 'null' : $profit_margin;
			//$expire_date= (!empty(trim($expire_date))) ? system_fromatted_date($expire_date) : 'null';

			$info = array(
		    				'item_name' 				=> $item_name,
		    				'brand_id' 					=> $brand_id,
		    				'category_id' 				=> $category_id,
		    				'sku' 						=> $sku,
		    				'hsn' 						=> $hsn,
		    				'unit_id' 					=> $unit_id,
		    				'alert_qty' 				=> $alert_qty,
		    				
		    				'price' 					=> $price,
		    				'tax_id' 					=> $tax_id,
		    				'purchase_price' 			=> $purchase_price,
		    				'tax_type' 					=> $tax_type,
		    				'profit_margin' 			=> $profit_margin,
		    				'sales_price' 				=> $sales_price,
		    				'seller_points' 			=> $seller_points,
		    				'custom_barcode'			=> $custom_barcode,
		    				'description'				=> $description,
		    				'item_group'				=> $item_group,
		    				
		    			);

			//Image Path	
			if(!empty($item_image)){
				$info['item_image']=$item_image;	
			}

			//Store ID
			$info['store_id']=(store_module() && is_admin()) ? $store_id : get_current_store_id();	

			$query1 = $this->db->where('id',$q_id)->update('db_items', $info);


			if(!$query1){
				return "failed";
			}

			/*$q1=$this->stock_entry($CUR_DATE,$q_id,$info['store_id']);
			if(!$q1){
				return "failed";
			}*/

			//Insert Variants in db_items table
			if($existing_row_count>0){
				for($i=1;$i<=$existing_row_count;$i++){
					if(isset($_REQUEST['tr_variant_id_'.$i]) && !empty($_REQUEST['tr_variant_id_'.$i])){

						$variant_id 		=$this->xss_html_filter(trim($_REQUEST['tr_variant_id_'.$i]));
						$sku				=$this->xss_html_filter(trim($_REQUEST['td_data_'.$i.'_2']));
						$hsn				=$this->xss_html_filter(trim($_REQUEST['td_data_'.$i.'_9']));
						$custom_barcode 	=$this->xss_html_filter(trim($_REQUEST['td_data_'.$i.'_8']));
						$price 			 	=$this->xss_html_filter(trim($_REQUEST['td_data_'.$i.'_3']));
						$purchase_price	 	=$this->xss_html_filter(trim($_REQUEST['td_data_'.$i.'_4']));
						$profit_margin	 	=$this->xss_html_filter(trim($_REQUEST['td_data_'.$i.'_5']));
						$sales_price	 	=$this->xss_html_filter(trim($_REQUEST['td_data_'.$i.'_6']));
						$count_id 			=$this->xss_html_filter(trim($_REQUEST['count_id_'.$i]));
						$item_code 			=$this->xss_html_filter(trim($_REQUEST['item_code_'.$i]));
						
						$variant_details = $this->db->select("*")->where("id",$variant_id)->get("db_variants")->row();
						$variant_name = $variant_details->variant_name;

						$info = array(
								
			    				'item_name' 				=> $item_name."-".$variant_name,
			    				'brand_id' 					=> $brand_id,
			    				'category_id' 				=> $category_id,
			    				'sku' 						=> $sku,
			    				'hsn' 						=> $hsn,
			    				'unit_id' 					=> $unit_id,
			    				'alert_qty' 				=> $alert_qty,
			    				
			    				'price' 					=> $price,
			    				'tax_id' 					=> $tax_id,
			    				'purchase_price' 			=> $purchase_price,
			    				'tax_type' 					=> $tax_type,
			    				'profit_margin' 			=> $profit_margin,
			    				'sales_price' 				=> $sales_price,
			    				/*System Info*/
			    				'created_date' 				=> $CUR_DATE,
			    				'created_time' 				=> $CUR_TIME,
			    				'created_by' 				=> $CUR_USERNAME,
			    				'system_ip' 				=> $SYSTEM_IP,
			    				'system_name' 				=> $SYSTEM_NAME,
			    				'status' 					=> 1,
			    				'seller_points'				=> $seller_points,
			    				'custom_barcode'			=> $custom_barcode,
			    				'description'				=> $description,
			    				//'item_group'				=> 'Subvariant',
			    				'parent_id'					=> $q_id,
			    				'child_bit'					=> 1,
			    				'variant_id'				=> $variant_id,
			    			);

							$info['count_id']=(!empty($count_id)) ? $count_id : get_count_id('db_items');	
							$info['item_code']=(!empty($item_code)) ? $item_code : get_init_code('item');
							$info['store_id']=(store_module() && is_admin()) ? $store_id : get_current_store_id();	


							//FIND THE THIS VARIANT SAVED IN DB_ITEMS OT NOT
							$q3 = $this->db->select("id")->where('variant_id',$variant_id)->where('parent_id',$q_id)->get("db_items");
							if($q3->num_rows()>0){
								//YES ITEM ALREADY EXIST
								$query1 = $this->db->where("id",$q3->row()->id)->update('db_items', $info);	
							}
							else{
								$query1 = $this->db->insert('db_items', $info);
							}
							#------------------------------------
							if(!$query1){
								return "failed";
							}
					}
				
				}//for end
			}

			
		
			
			if ($query1){
				  // $this->db->query("update db_items set expire_date=null where expire_date='0000-00-00'");
				   $this->db->trans_commit();
				   $this->session->set_flashdata('success', 'Success!! Item Updated Successfully!');
			        return "success";
			}
			else{
					$this->db->trans_rollback();
			        return "failed";
			}
		/*}*/
	}
	public function update_status($id,$status){
       if (set_status_of_table($id,$status,'db_items')){
            echo "success";
        }
        else{
            echo "failed";
        }
	}

	public function delete_items_from_table($ids){
		$this->db->trans_begin();
		//find the this item has the Purchase Return 
		$purchase_ret_rec = $this->db->select("*")->where("store_id",get_current_store_id())->where("item_id in($ids)")->group_by('item_id')->get("db_purchaseitemsreturn");
		if($purchase_ret_rec->num_rows()>0){
			$i=1;
			echo "Can't Delete!<br>These Items List Have the Purchase Returns Records!";
			foreach($purchase_ret_rec->result() as $res1){
				echo "<br>".$i++.". ".get_item_name($res1->item_id);
			}
			exit;
		}

		//find the this item has the Purchase
		$purchase_rec = $this->db->select("*")->where("store_id",get_current_store_id())->where("item_id in($ids)")->group_by('item_id')->get("db_purchaseitems");
		if($purchase_rec->num_rows()>0){
			$i=1;
			echo "Can't Delete!<br>These Items List Have the Purchase Records!";
			foreach($purchase_rec->result() as $res1){
				echo "<br>".$i++.". ".get_item_name($res1->item_id);
			}
			exit;
		}

		//find the this item has the Sales Return 
		$sales_ret_rec = $this->db->select("*")->where("store_id",get_current_store_id())->where("item_id in($ids)")->group_by('item_id')->get("db_salesitemsreturn");
		if($sales_ret_rec->num_rows()>0){
			$i=1;
			echo "Can't Delete!<br>These Items List Have the Sales Returns Records!";
			foreach($sales_ret_rec->result() as $res1){
				echo "<br>".$i++.". ".get_item_name($res1->item_id);
			}
			exit;
		}

		//find the this item has the sales
		$sales_rec = $this->db->select("*")->where("store_id",get_current_store_id())->where("item_id in($ids)")->group_by('item_id')->get("db_salesitems");
		if($sales_rec->num_rows()>0){
			$i=1;
			echo "Can't Delete!<br>These Items List Have the Sales Records!";
			foreach($sales_rec->result() as $res1){
				echo "<br>".$i++.". ".get_item_name($res1->item_id);
			}
			exit;
		}

		//find the this item has the quotation
		$quotation_rec = $this->db->select("*")->where("store_id",get_current_store_id())->where("item_id in($ids)")->group_by('item_id')->get("db_quotationitems");
		if($quotation_rec->num_rows()>0){
			$i=1;
			echo "Can't Delete!<br>These Items List Have the Quotation Records!";
			foreach($quotation_rec->result() as $res1){
				echo "<br>".$i++.". ".get_item_name($res1->item_id);
			}
			exit;
		}
	
		//find the this item has the stock adjustment entries
		$quotation_rec = $this->db->select("*")->where("store_id",get_current_store_id())->where("item_id in($ids)")->group_by('item_id')->get("db_stockadjustmentitems");
		if($quotation_rec->num_rows()>0){
			$i=1;
			echo "Can't Delete!<br>These Items List Have the Stock Adjustment Records!";
			foreach($quotation_rec->result() as $res1){
				echo "<br>".$i++.". ".get_item_name($res1->item_id);
			}
			exit;
		}		


		
		$q1=$this->db->query("delete from db_items where id in($ids)");
		$q1=$this->db->query("delete from db_items where parent_id in($ids)");
		
		/*Update items in all warehouses of the item*/
		$q7=update_warehousewise_items_qty_by_store(null,$ids);
		if(!$q7){
			return "failed";
		}

        if($q1 && $q7){
        	$this->db->trans_commit();
            echo "success";
        }
        else{
            echo "failed";
        }	
	}


	public function inclusive($price='',$tax_per){
		return $price/(($tax_per/100)+1)/10;
	}

	//GET Labels from Purchase Invoice
	public function get_purchase_items_info($rowcount,$item_id,$purchase_qty){
		$q1=$this->db->select('*')->from('db_items')->where("id=$item_id")->get();
		$tax=$this->db->query("select tax from db_tax where id=".$q1->row()->tax_id)->row()->tax;

		$info['item_id'] = $q1->row()->id;
		$info['item_name'] = $q1->row()->item_name;
		$info['item_available_qty'] = $q1->row()->stock;
		$info['item_sales_qty'] = $purchase_qty;

	    return $this->return_row_with_data($rowcount,$info);
	}

	public function get_items_info($rowcount,$item_id){
		$q1=$this->db->select('*')->from('db_items')->where("id=$item_id")->get();
		$tax=$this->db->query("select tax from db_tax where id=".$q1->row()->tax_id)->row()->tax;

		$info['item_id'] = $q1->row()->id;
		$info['item_name'] = $q1->row()->item_name;
		$info['item_available_qty'] = $q1->row()->stock;
		$info['item_sales_qty'] = 1;

		$this->return_row_with_data($rowcount,$info);
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
                     <input typ="text" value="<?=$item_sales_qty;?>" class="form-control no-padding text-center" onkeyup="calculate_tax(<?=$rowcount;?>)" id="td_data_<?=$rowcount;?>_3" name="td_data_<?=$rowcount;?>_3">
                     <span class="input-group-btn">
                     <button onclick="increment_qty(<?=$rowcount;?>)" type="button" class="btn btn-default btn-flat"><i class="fa fa-plus text-success"></i></button></span>
                  </div>
               </td>
               
               <!-- Remove button -->
               <td id="td_<?=$rowcount;?>_16" style="text-align: center;">
                  <a class=" fa fa-fw fa-minus-square text-red" style="cursor: pointer;font-size: 34px;" onclick="removerow(<?=$rowcount;?>)" title="Delete ?" name="td_data_<?=$rowcount;?>_16" id="td_data_<?=$rowcount;?>_16"></a>
               </td>
              <input type="hidden" id="tr_available_qty_<?=$rowcount;?>_13" value="<?=$item_available_qty;?>">
               <input type="hidden" id="tr_item_id_<?=$rowcount;?>" name="tr_item_id_<?=$rowcount;?>" value="<?=$item_id;?>">
            </tr>
		<?php

	}
	public function xss_html_filter($input){
		return $this->security->xss_clean(html_escape($input));
	}

	public function preview_labels(){
		//print_r($_POST);exit();
		$CI =& get_instance();
		//Filtering XSS and html escape from user inputs 
		$store_name=$this->db->query("select store_name from db_store where id=".get_current_store_id())->row()->store_name;
		$rowcount = $this->input->post('hidden_rowcount');

		$is_roll_paper=true;
		$page_break = (isset($is_roll_paper) && !empty($is_roll_paper)) ? 'page-break-after: always;' : '';

		?>
		<div style=" height:5in !important;  width:8.5in !important; line-height: 16px !important; ">
			<div class="inner-div-2" style=" height:11in !important;  width:8.5in !important; line-height: 16px !important;">
				<div style="">

					<?php
					//Import post data from form
					for($i=1;$i<=$rowcount;$i++){
					
						if(isset($_POST['tr_item_id_'.$i]) && !empty($_POST['tr_item_id_'.$i])){
							

							$item_id 			=$this->xss_html_filter(trim($_POST['tr_item_id_'.$i]));
							$item_count 			=$this->xss_html_filter(trim($_POST['td_data_'.$i."_3"]));
							$res1=$this->db->query("select * from db_items where id=$item_id")->row();

							$item_name =$res1->item_name;
							$item_code = (!empty($res1->custom_barcode)) ? $res1->custom_barcode : $res1->item_code;
							$item_price =$res1->sales_price;

							for($j=1;$j<=$item_count;$j++){
							?>
							<div style="height:1in !important; line-height: 1in; width:2.5in !important; display: inline-block; <?=$page_break;?>  " class="label_border text-center">
							<div style="display:inline-block;vertical-align:middle;line-height:16px !important;">
								<b style="display: block !important" class="text-uppercase"><?=$store_name;?></b>
									<span style="display: block !important">
									<?= $item_name;?>
									</span>
								<b>Price:</b>
								<span><?= $CI->currency($item_price);?></span>
								<img class="center-block" style="max-height: 0.35in !important; width: 100%; opacity: 1.0" src="<?php echo base_url();?>barcode/get_barcode/?code=<?php echo urldecode($item_code);?>">

							</div>
							</div>
							<br>
							<?php
							}
						}
					
					}//for end
					?>
					
					
				</div>
			</div>
		</div>
		<?php
		
	}



	public function return_variant_data_in_row($rowcount,$variant_id){
		extract($_POST);

		$res1=$this->db->select('*')->from('db_variants')->where("id=$variant_id")->get()->row();
		
		$info = array(
							'item_id'					=> '', 
							'variant_id' 				=> $res1->id, 
							'variant_name' 				=> $res1->variant_name,
							'item_price' 				=> '',
							'item_sales_price' 			=> '',
							'variant_item_sku' 			=> '',
							'variant_item_hsn' 			=> '',
							'variant_profit_margin' 	=> '',
							'variant_purchase_price' 	=> '',
							'barcode'				 	=> '',
							'count_id'				 	=> '',
							'item_code'				 	=> '',
						);

		$this->return_variant_data_in_html_row($rowcount,$info);
	}
	public function get_variants_list_in_row($parent_id){
		$q1=$this->db->select('*')->from('db_items')->where("parent_id=$parent_id")->get();
		$rowcount =1;
		foreach ($q1->result() as $res1) {

			$res2=$this->db->select('*')->from('db_variants')->where("id",$res1->variant_id)->get()->row();
			
			$info = array(
							'item_id'					=> $res1->id, 
							'variant_id' 				=> $res2->id, 
							'variant_name' 				=> $res2->variant_name,
							'item_price' 				=> '',
							'item_sales_price' 			=> store_number_format($res1->sales_price,0),
							'variant_item_sku' 			=> $res1->sku,
							'variant_item_hsn' 			=> $res1->hsn,
							'variant_profit_margin' 	=> $res1->profit_margin,
							'variant_purchase_price' 	=> store_number_format($res1->price,0),
							'barcode'				 	=> $res1->custom_barcode,
							'count_id'				 	=> $res1->count_id,
							'item_code'				 	=> $res1->item_code,
						);
			
			$result = $this->return_variant_data_in_html_row($rowcount++,$info);
		}
		return $result;
	}

	public function return_variant_data_in_html_row($rowcount,$info){
		extract($info);
		?>
            <tr id="row_<?=$rowcount;?>" data-row='<?=$rowcount;?>'>
               <td id="td_<?=$rowcount;?>_1">
                  <label class='form-control' style='height:auto;' >
                  <a id="td_data_<?=$rowcount;?>_1" href="javascript:" title=""><?=$variant_name;?></a>
                  	</label>
               </td>
  
               <!-- SKU-->
               <td id="td_<?=$rowcount;?>_2"><input type="text" name="td_data_<?=$rowcount;?>_2" id="td_data_<?=$rowcount;?>_2" class="form-control text-center no-padding" value="<?=$variant_item_sku;?>" placeholder='Optional'></td>

               <!-- HSN-->
               <td id="td_<?=$rowcount;?>_9"><input type="text" name="td_data_<?=$rowcount;?>_9" id="td_data_<?=$rowcount;?>_9" class="form-control text-center no-padding" value="<?=$variant_item_hsn;?>" placeholder='Optional'></td>

               <!-- Barcode-->
               <td id="td_<?=$rowcount;?>_8"><input type="text" name="td_data_<?=$rowcount;?>_8" id="td_data_<?=$rowcount;?>_8" class="form-control text-center no-padding" value="<?=$barcode;?>" placeholder='Optional'></td>

               <!-- Price-->
               <td id="td_<?=$rowcount;?>_3"><input type="text" name="td_data_<?=$rowcount;?>_3" id="td_data_<?=$rowcount;?>_3" class="form-control text-right no-padding only_currency text-center" onkeyup='calculate_purchase_price_of_all_row()' placeholder='Required' style="border-color: #f39c12;" value="<?=$variant_purchase_price;?>"></td>

               <!-- Purchase Price-->
               <td id="td_<?=$rowcount;?>_4"><input type="text" name="td_data_<?=$rowcount;?>_4" id="td_data_<?=$rowcount;?>_4" class="form-control text-right no-padding only_currency text-center" placeholder='Required' style="border-color: #f39c12;" value="" readonly></td>

               <!-- Profit Margin-->
               <td id="td_<?=$rowcount;?>_5"><input type="text" name="td_data_<?=$rowcount;?>_5" id="td_data_<?=$rowcount;?>_5" class="form-control text-right no-padding only_currency text-center" placeholder='Required' onchange='calculate_sales_price_of_all_row()' style="border-color: #f39c12;" value="<?=$variant_profit_margin;?>"></td>

               <!-- Sales Price -->
               <td id="td_<?=$rowcount;?>_6"><input type="text" name="td_data_<?=$rowcount;?>_6" id="td_data_<?=$rowcount;?>_6" class="form-control text-right no-padding only_currency text-center" placeholder='Required' onchange='calculate_profit_margin_of_all_row()' style="border-color: #f39c12;" value="<?=$item_sales_price;?>"></td>
               
               <!-- Delete button -->
               <td id="td_<?=$rowcount;?>_7" style="text-align: center;">
                  <a class=" fa fa-fw fa-minus-square text-red" style="cursor: pointer;font-size: 34px;" onclick="removerow_also_delete_from_database('<?=$item_id;?>',<?=$rowcount;?>)" title="Delete ?" name="td_data_<?=$rowcount;?>_7" id="td_data_<?=$rowcount;?>_7"></a>
               </td>
               <input type="hidden" id="tr_variant_id_<?=$rowcount;?>" name="tr_variant_id_<?=$rowcount;?>" value="<?=$variant_id;?>">
               <input type="hidden" id="count_id_<?=$rowcount;?>" name="count_id_<?=$rowcount;?>" value="<?=$count_id;?>">
               <input type="hidden" id="item_code_<?=$rowcount;?>" name="item_code_<?=$rowcount;?>" value="<?=$item_code;?>">
            </tr>
		<?php

	}

}
