<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Store_model extends CI_Model {

	var $table = 'db_store';
	var $column_order = array('store_code','store_name','mobile','address','status','id'); //set column field database for datatable orderable
	var $column_search = array('store_code','store_name','mobile','address','status','id'); //set column field database for datatable searchable 
	var $order = array('id' => 'desc'); // default order 

	private function _get_datatables_query()
	{
		
		$this->db->from($this->table);

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
		$this->db->from($this->table);
		return $this->db->count_all_results();
	}

	public function store_making_codes(){
		 /*Create Store Code*/
		$this->db->query("ALTER TABLE db_store AUTO_INCREMENT = 1");
        $store_id=$this->db->query('select max(id)+1 as store_id from db_store')->row()->store_id;
		$data = array();
        $data['store_code'] = 'ST'.str_pad($store_id, 4, '0', STR_PAD_LEFT);
        $data['category_init'] ="CT"."/".str_pad($store_id, 2, '0', STR_PAD_LEFT)."/";
        $data['item_init'] ="IT".str_pad($store_id, 2, '0', STR_PAD_LEFT);
        $data['supplier_init'] ="SU"."/".str_pad($store_id, 2, '0', STR_PAD_LEFT)."/";
        $data['purchase_init'] ="PU"."/".date("Y")."/".str_pad($store_id, 2, '0', STR_PAD_LEFT)."/";
        $data['purchase_return_init'] ="PR"."/".date("Y")."/".str_pad($store_id, 2, '0', STR_PAD_LEFT)."/";
        $data['customer_init'] ="CU"."/".str_pad($store_id, 2, '0', STR_PAD_LEFT)."/";
        $data['sales_init'] ="SL"."/".date("Y")."/".str_pad($store_id, 2, '0', STR_PAD_LEFT)."/";
        $data['sales_return_init'] ="SR"."/".date("Y")."/".str_pad($store_id, 2, '0', STR_PAD_LEFT)."/";
        $data['expense_init'] ="EX"."/".date("Y")."/".str_pad($store_id, 2, '0', STR_PAD_LEFT)."/";
        $data['accounts_init'] ="AC"."/".str_pad($store_id, 2, '0', STR_PAD_LEFT)."/";
        $data['quotation_init'] ="QT"."/".date("Y")."/".str_pad($store_id, 2, '0', STR_PAD_LEFT)."/";
        $data['money_transfer_init'] ="MT"."/".str_pad($store_id, 2, '0', STR_PAD_LEFT)."/";
        $data['sales_payment_init'] ="SP"."/".date("Y")."/".str_pad($store_id, 2, '0', STR_PAD_LEFT)."/";
        $data['sales_return_payment_init'] ="SRP"."/".date("Y")."/".str_pad($store_id, 2, '0', STR_PAD_LEFT)."/";
        $data['purchase_payment_init'] ="PP"."/".date("Y")."/".str_pad($store_id, 2, '0', STR_PAD_LEFT)."/";
        $data['purchase_return_payment_init'] ="PRP"."/".date("Y")."/".str_pad($store_id, 2, '0', STR_PAD_LEFT)."/";
        $data['expense_payment_init'] ="XP"."/".date("Y")."/".str_pad($store_id, 2, '0', STR_PAD_LEFT)."/";
        $data['cust_advance_init'] ="ADV"."/".date("Y")."/".str_pad($store_id, 2, '0', STR_PAD_LEFT)."/";
        $data['language_id'] =1;
        $data['sales_discount'] =0;
        $data['change_return'] =1;
        $data['round_off'] =1;
        $data['sales_invoice_format_id'] =3;
        $data['pos_invoice_format_id'] =1;
        $data['sales_invoice_footer_text'] ='This is footer text. It is in Store Management.';
        return $data;
	}

	public function create_url_sms_api($store_id){
		$q1=$this->db->select("*")->where("store_id",$store_id)->get("db_smsapi");
		if($q1->num_rows()==0){
			$insertArray = [
			   [
			      'store_id' => $store_id,
			      'info' => 'url',
			      'key' => 'weblink',
			      'key_value' => 'http://example.com/sendmessage',
			   ],
			   [
			      'store_id' => $store_id,
			      'info' => 'mobile',
			      'key' => 'mobiles',
			      'key_value' => '',
			   ],
			   [
			      'store_id' => $store_id,
			      'info' => 'message',
			      'key' => 'message',
			      'key_value' => '',
			   ],
			   
			];
			if(!$this->db->insert_batch('db_smsapi', $insertArray)){
				return false;
			}
		}
		return true;
	}

	public function create_url_sms_templates($store_id){
		$q1=$this->db->select("*")->where("store_id",$store_id)->get("db_smstemplates");
		if($q1->num_rows()==0){
			$insertArray = [
			   [
			      'store_id' => $store_id,
			      'template_name' => 'GREETING TO CUSTOMER ON SALES',
			      'content' => "Hi {{customer_name}},
Your sales Id is {{sales_id}},
Sales Date {{sales_date}},
Total amount  {{sales_amount}},
You have paid  {{paid_amt}},
and due amount is  {{due_amt}}
Thank you Visit Again",
			      'variables' => "{{customer_name}}                          
{{sales_id}}
{{sales_date}}
{{sales_amount}}
{{paid_amt}}
{{due_amt}}
{{store_name}}
{{store_mobile}}
{{store_address}}
{{store_website}}
{{store_email}}
",
				'status'	=> 1,
				'undelete_bit'	=> 1,
			   ],
			   [
			      'store_id' => $store_id,
			      'template_name' => 'GREETING TO CUSTOMER ON SALES RETURN',
			      'content' => "Hi {{customer_name}},
Your sales return Id is {{return_id}},
Return Date {{return_date}},
Total amount  {{return_amount}},
We paid  {{paid_amt}},
and due amount is  {{due_amt}}
Thank you Visit Again",
			      'variables' => "{{customer_name}}                          
{{return_id}}
{{return_date}}
{{return_amount}}
{{paid_amt}}
{{due_amt}}
{{company_name}}
{{company_mobile}}
{{company_address}}
{{company_website}}
{{company_email}}
",
				'status'	=> 1,
				'undelete_bit'	=> 1,
			   ],
			   
			];
			if(!$this->db->insert_batch('db_smstemplates', $insertArray)){
				return false;
			}
		}
		return true;
	}

	public function save_registration(){
		extract($this->security->xss_clean(html_escape(array_merge($this->data,$this->store_making_codes(),$_POST,$_GET))));
		$country = $this->db->select("country")->where("id",$country)->get("db_country")->row()->country;
		$state = $this->db->select("state")->where("id",$state)->get("db_states")->row()->state;

		$this->db->query("ALTER TABLE db_store AUTO_INCREMENT = 1");
		$this->db->trans_begin();
		$data = array(
		    				'store_code'				=> $store_code,
		    				'store_name'				=> $store_name,
		    				'store_website'				=> '',
		    				'mobile'					=> $mobile,
		    				'phone'						=> '',
		    				'email'						=> $email,
		    				'country'					=> $country,
		    				'state'						=> $state,
		    				'city'						=> $city,
		    				'address'					=> ' ',
		    				'postcode'					=> '',
		    				'bank_details'				=> '',
		    				'category_init'				=> $category_init,
		    				'item_init'					=> $item_init,
		    				'supplier_init'				=> $supplier_init,
		    				'purchase_init'				=> $purchase_init,
		    				'purchase_return_init'		=> $purchase_return_init,
		    				'customer_init'				=> $customer_init,
		    				'sales_init'				=> $sales_init,
		    				'sales_return_init'			=> $sales_return_init,
		    				'expense_init'				=> $expense_init,
		    				'quotation_init'			=> $quotation_init,
		    				'money_transfer_init'		=> $money_transfer_init,
		    				'accounts_init'				=> $accounts_init,
		    				'currency_id'				=> $currency,
		    				'currency_placement'		=> $currency_placement,
		    				'timezone'					=> $timezone,
		    				'date_format'				=> $date_format,
		    				'date_format'				=> $date_format,
		    				'time_format'				=> $time_format,
		    				'sales_discount'			=> $sales_discount,
		    				'change_return'				=> $change_return,
		    				'sales_invoice_format_id'	=> $sales_invoice_format_id,
		    				'pos_invoice_format_id'		=> $pos_invoice_format_id,
		    				'sales_invoice_footer_text'	=> $sales_invoice_footer_text,
		    				'round_off'					=> $round_off,
		    				'decimals'					=> $decimals,
		    				'sales_payment_init'		=> $sales_payment_init,
		    				'sales_return_payment_init'	=> $sales_return_payment_init,
		    				'purchase_payment_init'		=> $purchase_payment_init,
		    				'purchase_return_payment_init'	=> $purchase_return_payment_init,
		    				'expense_payment_init'	=> $expense_payment_init,
		    				'cust_advance_init'	=> $cust_advance_init,
		    			);


		
			$store_code_count=$this->db->query("select count(*) as store_code_count from db_store where upper(store_code)=upper('$store_code')")->row()->store_code_count;
			if($store_code_count>0){
				echo "Sorry! Store Code Already Exist!\nPlease Change Store Code";exit();
			}
			$extra_info = array(
							'invoice_view'				=> 1,
		    				'sms_status'				=> 0,
		    				'language_id'				=> $language_id,
		    				/*System Info*/
		    				'created_date' 				=> $CUR_DATE,
		    				'created_time' 				=> $CUR_TIME,
		    				'created_by' 				=> $CUR_USERNAME,
		    				'system_ip' 				=> $SYSTEM_IP,
		    				'system_name' 				=> $SYSTEM_NAME,
		    				'status' 					=> 1,
		    			);
			$data=array_merge($data,$extra_info);
			$q1 = $this->db->insert('db_store', $data);
			if(!$q1){
				echo "failed";exit();
			}

			$store_id = $this->db->insert_id();
			$this->load->model('customers_model');

			$q2=$this->customers_model->create_walk_in_customer($store_id);

			$q3 = $this->create_default_warehouse($store_id,null,null);
			if(!$q3){
				echo "failed";exit();
			}

			//Create User
			if(!empty($email)){
			$query=$this->db->query("select * from db_users where email='$email'")->num_rows();
			if($query>0){ return "This Email ID already exist.";}
			}
			$info = array(
			    				'username' 				=> $first_name, 
			    				'last_name' 			=> $last_name, 
			    				'password' 				=> md5($password), 
			    				'mobile' 				=> $mobile,
			    				'email' 				=> $email,
			    				/*System Info*/
			    				'created_date' 			=> $CUR_DATE,
			    				'created_time' 			=> $CUR_TIME,
			    				'created_by' 			=> $CUR_USERNAME,
			    				'system_ip' 			=> $SYSTEM_IP,
			    				'system_name' 			=> $SYSTEM_NAME,
			    				'status' 				=> 1,
			    			);
			if(!empty($profile_picture)){
				$info['profile_picture'] = $profile_picture;
			}
			
			$info['role_id'] = store_admin_id();
			
			$info['store_id']=(store_module()) ? $store_id : $this->session->userdata('store_id');	
			$q1 = $this->db->insert('db_users', $info);
			if (!$q1){
				return "failed";
			}
			$user_id = $this->db->insert_id();

			//UPDATE THE USER ID INTO STORE
			$this->db->set("user_id",$user_id)
						->where("id",$store_id)
						->update("db_store");
						
			if(warehouse_module() && isset($_POST['warehouses']) && $role_id!=1 && $role_id!=store_admin_id()){
				$warehouses_list = sizeof($_POST['warehouses']);
				foreach ($_POST['warehouses'] as $res => $val) {
					$warehouse_info = array ( 'user_id'=> $user_id, 'warehouse_id'=>$val );
					$q2 = $this->db->insert("db_userswarehouses",$warehouse_info);
					if (!$q2){
						return "failed";
					}
				}
			}

			if(!$this->create_url_sms_api($store_id)){
				return "failed";
			}
			if(!$this->create_url_sms_templates($store_id)){
				return "failed";
			}
			$this->db->trans_commit();
			$this->session->set_flashdata('success', 'Account created Succssfully!! Please Login!');
			return "success";
			

		
	}
	public function verify_and_save(){

		//Filtering XSS and html escape from user inputs 
		extract($this->security->xss_clean(html_escape(array_merge($this->data,$_POST,$_GET))));
		
		$this->db->trans_begin();

		
		$store_logo='';
		if(!empty($_FILES['store_logo']['name'])){
			$config['upload_path']          = './uploads/store/';
	        $config['allowed_types']        = 'gif|jpg|jpeg|png';
	        $config['max_size']             = 1000;
	        $config['max_width']            = 1000;
	        $config['max_height']           = 1000;

	        $this->load->library('upload', $config);

	        if ( ! $this->upload->do_upload('store_logo'))
	        {
	                $error = array('error' => $this->upload->display_errors());
	                return $error['error'];
	                exit();
	        }
	        else
	        {
	        	   $store_logo='uploads/store/'.$this->upload->data('file_name');
	        }
		}

		$change_return = (isset($change_return)) ? 1 : 0;
		$round_off = (isset($round_off)) ? 1 : 0;


		$data = array(
		    				'store_code'				=> $store_code,
		    				'store_name'				=> $store_name,
		    				'store_website'				=> $store_website,
		    				'mobile'					=> $mobile,
		    				'phone'						=> $phone,
		    				'email'						=> $email,
		    				'country'					=> $country,
		    				'state'						=> $state,
		    				'city'						=> $city,
		    				'address'					=> $address,
		    				'postcode'					=> $postcode,
		    				'bank_details'				=> $bank_details,
		    				'category_init'				=> $category_init,
		    				'item_init'					=> $item_init,
		    				'supplier_init'				=> $supplier_init,
		    				'purchase_init'				=> $purchase_init,
		    				'purchase_return_init'		=> $purchase_return_init,
		    				'customer_init'				=> $customer_init,
		    				'sales_init'				=> $sales_init,
		    				'sales_return_init'			=> $sales_return_init,
		    				'expense_init'				=> $expense_init,
		    				'quotation_init'			=> $quotation_init,
		    				'money_transfer_init'		=> $money_transfer_init,
		    				'accounts_init'				=> $accounts_init,
		    				'currency_id'				=> $currency,
		    				'currency_placement'		=> $currency_placement,
		    				'timezone'					=> $timezone,
		    				'date_format'				=> $date_format,
		    				'date_format'				=> $date_format,
		    				'time_format'				=> $time_format,
		    				'sales_discount'			=> $sales_discount,
		    				'sales_discount'			=> $sales_discount,
		    				'change_return'				=> $change_return,
		    				'sales_invoice_format_id'	=> $sales_invoice_format_id,
		    				'pos_invoice_format_id'		=> $pos_invoice_format_id,
		    				'sales_invoice_footer_text'	=> $sales_invoice_footer_text,
		    				'round_off'					=> $round_off,
		    				'decimals'					=> $decimals,
		    				'sales_payment_init'		=> $sales_payment_init,
		    				'sales_return_payment_init'	=> $sales_return_payment_init,
		    				'purchase_payment_init'		=> $purchase_payment_init,
		    				'purchase_return_payment_init'	=> $purchase_return_payment_init,
		    				'expense_payment_init'	=> $expense_payment_init,
		    				'cust_advance_init'	=> $cust_advance_init,
		    			);

		if(!empty($store_logo)){
			$data['store_logo']=$store_logo;
		}

		/*custom helper*/
		if(gst_number()){
			$data['gst_no']=$gst_no;
		}
		if(vat_number()){
			$data['vat_no']=$vat_no;
		}
		if(pan_number()){
			$data['pan_no']=$pan_no;
		}
		/*end*/


		if($command=='save'){
			$store_code_count=$this->db->query("select count(*) as store_code_count from db_store where upper(store_code)=upper('$store_code')")->row()->store_code_count;
			if($store_code_count>0){
				echo "Sorry! Store Code Already Exist!\nPlease Change Store Code";exit();
			}
			$extra_info = array(
							'invoice_view'				=> 1,
		    				'sms_status'				=> 0,
		    				'language_id'				=> $language_id,
		    				/*System Info*/
		    				'created_date' 				=> $CUR_DATE,
		    				'created_time' 				=> $CUR_TIME,
		    				'created_by' 				=> $CUR_USERNAME,
		    				'system_ip' 				=> $SYSTEM_IP,
		    				'system_name' 				=> $SYSTEM_NAME,
		    				'status' 					=> 1,
		    			);
			$data=array_merge($data,$extra_info);
			$q1 = $this->db->insert('db_store', $data);
			$store_id = $this->db->insert_id();
			$this->load->model('customers_model');
			$q2=$this->customers_model->create_walk_in_customer($store_id);

			if(!$this->create_url_sms_api($store_id)){
				return "failed";
			}
			if(!$this->create_url_sms_templates($store_id)){
				return "failed";
			}
			$q3 = $this->create_default_warehouse($store_id,null,null);
			if(!$q3){
				echo "failed";exit();
			}
			if($q1){
				$this->db->trans_commit();
				$this->session->set_flashdata('success', 'Success!! Record Saved Successfully! ');
				echo "success";
			}

		}
		
		exit();
	}

	//Get store_details
	public function get_details($id){
		$data=$this->data;

		$query1=$this->db->query("select * from db_store where upper(id)=upper('$id')");
		if($query1->num_rows()==0){
			show_404();exit;
		}
		else{
			/* QUERY 1*/
			$query=$this->db->query("select * from db_sitesettings order by id asc limit 1");
			$query=$query->row();
			$data['q_id']=$query1->row()->id;
			return array_merge($data,$query1->row_array());
			return $data;
		}
	}


	public function update_status($id,$status){
       if (set_status_of_table($id,$status,'db_store')){
            echo "success";
        }
        else{
            echo "failed";
        }
	}
	public function delete_store_from_table($ids){
			$this->db->trans_begin();
			$query1=$this->db->where_in('id',$ids)->where('id!=1')->delete('db_store');
	        if ($query1){
	        	$this->db->trans_commit();
	            echo "success";
	        }
	        else{
	            echo "failed";
	        }	
	}
	public function create_default_warehouse($store_id,$mobile='',$email=''){
		$query1="insert into db_warehouse(store_id,warehouse_type,warehouse_name,mobile,email,status) 
									values($store_id,'System','System Warehouse','$mobile','$email',1)";
		
		if ($this->db->simple_query($query1)){
				$this->session->set_flashdata('success', 'Success!! New Warehouse Created Succssfully!!');
		        return "success";
		}
		else{
		        return "failed";
		}
	}

}
