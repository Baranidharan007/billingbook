<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Roles_model extends CI_Model {

	var $table = 'db_roles';
	var $column_order = array('role_name','description','status','store_id'); //set column field database for datatable orderable
	var $column_search = array('role_name','description','status','store_id'); //set column field database for datatable searchable 
	var $order = array('id' => 'desc'); // default order 

	private function _get_datatables_query()
	{
		
		$this->db->from($this->table);
		//if not admin
		/*if(!is_admin()){
			$this->db->where("store_id",get_current_store_id());
		}*/
		$this->db->where("store_id",$this->input->post('store_id'));

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
		
		//Validate This role_name already exist or not
		$store_id=(store_module() && is_admin()) ? $store_id : get_current_store_id();	
		if(strtoupper(trim($role_name))==strtoupper('Admin')){
			echo "You Couldn't Create Admin Role!!";exit();
		}
		$query=$this->db->query("select * from db_roles where upper(role_name)=upper('$role_name') and store_id=$store_id");
		if($query->num_rows()>0){
			return "This Role Name Name already Exist.";
			
		}
		else{
			$info = array(
		    				'role_name' 				=> $role_name, 
		    				'description' 				=> $description,
		    				'status' 				=> 1,
		    			);
			
			$info['store_id']=(store_module() && is_admin()) ? $store_id : get_current_store_id();	

			$q1 = $this->db->insert('db_roles', $info);
			if ($q1 && $this->set_persmissions($this->db->insert_id(),$info['store_id'])){
					$this->session->set_flashdata('success', 'Success!! New Role Name Added Successfully!');
			        return "success";
			}
			else{
			        return "failed";
			}
		}
	}

	//Get role_name_details
	public function get_details($id,$data){
		//Validate This role_name already exist or not
		$query=$this->db->query("select * from db_roles where upper(id)=upper('$id')");
		if($query->num_rows()==0){
			show_404();exit;
		}
		else{
			$query=$query->row();
			$data['q_id']=$query->id;
			$data['role_name']=$query->role_name;
			$data['description']=$query->description;
			$data['store_id']=$query->store_id;
			return $data;
		}
	}
	public function update_role(){
		//Filtering XSS and html escape from user inputs 
		extract($this->security->xss_clean(html_escape(array_merge($this->data,$_POST))));

		//Validate This role_name already exist or not
		$store_id=(store_module() && is_admin()) ? $store_id : get_current_store_id();
		if(strtoupper(trim($role_name))==strtoupper('Admin')){
			echo "You Couldn't Create Admin Role!!";exit();
		}
		$query=$this->db->query("select * from db_roles where upper(role_name)=upper('$role_name') and id<>$q_id and store_id=$store_id");
		if($query->num_rows()>0){
			return "This Role Name Name already Exist.";
			
		}
		else{
			$info = array(
		    				'role_name' 				=> $role_name, 
		    				'description' 				=> $description,
		    			);
			
			$info['store_id']=(store_module() && is_admin()) ? $store_id : get_current_store_id();

			$q1 = $this->db->where('id',$q_id)->update('db_roles', $info);
		
			if ($q1 && $this->set_persmissions($q_id,$info['store_id'])){
					$this->session->set_flashdata('success', 'Success!! Role Updated Successfully!');
			        return "success";
			}
			else{
			        return "failed";
			}
		}
	}

	public function update_status($id,$status){
		if($id==1){
			echo "Restricted! Can't Update this User Status!";exit();
		}
       if (set_status_of_table($id,$status,'db_roles')){
            echo "success";
        }
        else{
            echo "failed";
        }
	}
	public function delete_roles_from_table($ids){
			if($ids==1){
				echo "Restricted! Can't Delete this User!";exit();
			}

			$this->db->trans_begin();
			#----------------------------------Delete Role
			$this->db->where("id in ($ids)");
			//if not admin
			if(!is_admin()){
				$this->db->where("store_id",get_current_store_id());
			}

			$query1=$this->db->delete("db_roles");
			#----------------------------------
			#----------------------------------Delete permissions
			$this->db->where("role_id in ($ids)");
			//if not admin
			if(!is_admin()){
				$this->db->where("store_id",get_current_store_id());
			}

			$query2=$this->db->delete("db_permissions");
			#----------------------------------

	        if ($query1 && $query2){
	        	$this->db->trans_commit();
	            echo "success";
	        }
	        else{
	            echo "failed";
	        }
	        exit();
	}

	function get_selected($role_id= 0,$store_id,$permissions_array){
		$info=array();
		foreach ($permissions_array as $key => $value) {
			if(isset($_POST['permission'][$value])) {
				 array_push ($info,array('permissions'  =>  $value,'role_id'   =>  $role_id, 'store_id'=>$store_id));
			}
		}
		return $info;
	}

	public function set_persmissions($role_id= 0,$store_id){
		//echo "<pre>"; print_r($this->security->xss_clean(html_escape(array_merge($_POST))));exit;
		$result =array();
		//PERMISSIONS KEY FROM FRONT END
		$result= ($this->get_selected($role_id,$store_id,array(
														'users_add',
														'users_edit',
														'users_delete',
														'users_view',
														'tax_add',
														'tax_edit',
														'tax_delete',
														'tax_view',
														/*'currency_add',
														'currency_edit',
														'currency_delete',
														'currency_view',*/
														'store_edit',
														/*'site_edit',*/
														'units_add',
														'units_edit',
														'units_delete',
														'units_view',
														'roles_add',
							                            'roles_edit',
							                            'roles_delete',
							                            'roles_view',
							                            'places_add',
							                            'places_edit',
							                            'places_delete',
							                            'places_view',
							                            'expense_add',
							                            'expense_edit',
							                            'expense_delete',
							                            'expense_view',
							                            'items_add',
							                            'items_edit',
							                            'items_delete',
							                            'items_view',
							                            'import_items',
							                            'brand_add',
														'brand_edit',
														'brand_delete',
														'brand_view',
							                            'suppliers_add',
							                            'suppliers_edit',
							                            'suppliers_delete',
							                            'suppliers_view',
							                            'customers_add',
							                            'customers_edit',
							                            'customers_delete',
							                            'customers_view',
							                            'purchase_add',
							                            'purchase_edit',
							                            'purchase_delete',
							                            'purchase_view',
							                            'sales_add',
							                            'sales_edit',
							                            'sales_delete',
							                            'sales_view',
							                            'sales_payment_view',
							                            'sales_payment_add',
							                            'sales_payment_delete',
							                            'sales_report',
							                            'purchase_report',
							                            'expense_report',
							                            'profit_report',
							                            'stock_report',
							                            'item_sales_report',
							                            'purchase_payments_report',
							                            'sales_payments_report',
							                            'expired_items_report',
							                            'items_category_add',
							                            'items_category_edit',
							                            'items_category_delete',
							                            'items_category_view',
							                            'print_labels',
							                            'expense_category_add',
				                                        'expense_category_edit',
				                                        'expense_category_delete',
				                                        'expense_category_view',
				                                        'dashboard_view',
				                                        'dashboard_info_box_1',
				                                        'dashboard_info_box_2',
				                                        'dashboard_pur_sal_chart',
				                                        'dashboard_recent_items',
				                                        'dashboard_expired_items',
				                                        'dashboard_stock_alert',
				                                        'dashboard_trending_items_chart',
				                                        'send_sms',
				                                        'sms_template_edit',
				                                        'sms_template_view',
				                                        'sms_api_view',
				                                        'sms_api_edit',
				                                        'purchase_return_add',
				                                        'purchase_return_edit',
				                                        'purchase_return_delete',
				                                        'purchase_return_view',
				                                        'purchase_return_report',
				                                        'sales_return_add',
				                                        'sales_return_edit',
				                                        'sales_return_delete',
				                                        'sales_return_view',
				                                        'sales_return_report',
				                                        'sales_return_payment_view',
							                            'sales_return_payment_add',
							                            'sales_return_payment_delete',
							                            'purchase_return_payment_view',
							                            'purchase_return_payment_add',
							                            'purchase_return_payment_delete',
							                            'purchase_payment_view',
							                            'purchase_payment_add',
							                            'purchase_payment_delete',
							                            'payment_types_add',
							                            'payment_types_edit',
							                            'payment_types_delete',
							                            'payment_types_view',
							                            'import_customers',
							                            'import_suppliers',
							                            'stock_transfer_add',
			                                         	'stock_transfer_edit',
			                                          	'stock_transfer_delete',
			                                          	'stock_transfer_view',
			                                          	'warehouse_add',
			                                         	'warehouse_edit',
			                                          	'warehouse_delete',
			                                          	'warehouse_view',
			                                          	'supplier_items_report',
			                                          	'seller_points_report',
			                                          	'services_add',
				                                        'services_edit',
				                                        'services_delete',
				                                        'services_view',
			                                          	'quotation_add',
							                            'quotation_edit',
							                            'quotation_delete',
							                            'quotation_view',
							                            'import_services',
							                            'stock_adjustment_add',
		                                                'stock_adjustment_edit',
		                                                'stock_adjustment_delete',
		                                                'stock_adjustment_view',
		                                                'variant_add',
														'variant_edit',
														'variant_delete',
														'variant_view',
														'accounts_add',
				                                          'accounts_edit',
				                                          'accounts_delete',
				                                          'accounts_view',
				                                          'money_transfer_add',
				                                          'money_transfer_edit',
				                                          'money_transfer_delete',
				                                          'money_transfer_view',
				                                          'money_deposit_add',
				                                          'money_deposit_edit',
				                                          'money_deposit_delete',
				                                          'money_deposit_view',
				                                          'sales_tax_report',
				                                          'purchase_tax_report',
				                                          'cash_transactions',
				                                        'show_all_users_sales_invoices',
														'show_all_users_sales_return_invoices',
														'show_all_users_purchase_invoices',
														'show_all_users_purchase_return_invoices',
														'show_all_users_expenses',
														'show_all_users_quotations',
														'subscription',
														'smtp_settings',
														'send_email',
														'sms_settings',
														'email_template_edit',
														'email_template_view',
														'cust_adv_payments_add',
														'cust_adv_payments_edit',
														'cust_adv_payments_delete',
														'cust_adv_payments_view',
														'gstr_1_report',
														'gstr_2_report',
														'customer_orders_report',
													)));


		$this->db->trans_begin();		

		//BEFORE SAVING DELETE ALL PERSMISSIONS OF THE SPESIFIED ROLE
		$this->db->where("role_id",$role_id);
		$this->db->where("store_id",$store_id);
		$this->db->delete('db_permissions');


		//SAVE PERSMISSIONS
		$q1= $this->db->insert_batch('db_permissions',$result);
		if(!$q1){
			return false;
		}
		//SAVE PERMANENTALY
		$this->db->trans_commit();
		return true;
	}

}
