<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users_model extends CI_Model {
	
	public function __construct()
	{
		parent::__construct();
	}
	public function verify_and_save($data){

		//varify max sales usage of the package subscription
		validate_package_offers('max_users','db_users');
		//END

		extract($_POST);
		extract($data);
		$this->db->trans_begin();

		$profile_picture='';
		if(!empty($_FILES['profile_picture']['name'])){
			$config['upload_path']          = './uploads/users/';
	        $config['allowed_types']        = 'gif|jpg|png';
	        $config['max_size']             = 500;
	        $config['max_width']            = 500;
	        $config['max_height']           = 500;

	        $this->load->library('upload', $config);

	        if ( ! $this->upload->do_upload('profile_picture'))
	        {
	                $error = array('error' => $this->upload->display_errors());
	                print($error['error']);
	                exit();
	        }
	        else
	        {
	        	   $profile_picture='uploads/users/'.$this->upload->data('file_name');
	        }
		}


		/*$query=$this->db->query("select * from db_users where username='$new_user'")->num_rows();
		if($query>0){ return "This username already exist.";}*/
		if(!empty($mobile)){
			$query=$this->db->query("select * from db_users where mobile='$mobile'")->num_rows();
			if($query>0){ return "This Moble Number already exist.";}
		}
		if(!empty($email)){
			$query=$this->db->query("select * from db_users where email='$email'")->num_rows();
			if($query>0){ return "This Email ID already exist.";}
		}
		$info = array(
		    				'username' 				=> $new_user, 
		    				'last_name' 			=> $last_name, 
		    				'password' 				=> md5($pass), 
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
		if(isset($role_id)){
			$info['role_id'] = $role_id;
		}
		$info['store_id']=(store_module()) ? $store_id : $this->session->userdata('store_id');	
		$q1 = $this->db->insert('db_users', $info);
		if (!$q1){
			return "failed";
		}
		if(warehouse_module() && isset($_POST['warehouses']) && $role_id!=1 && $role_id!=store_admin_id()){
			$user_id = $this->db->insert_id();

			$warehouses_list = sizeof($_POST['warehouses']);
			foreach ($_POST['warehouses'] as $res => $val) {
				$warehouse_info = array ( 'user_id'=> $user_id, 'warehouse_id'=>$val );
				$q2 = $this->db->insert("db_userswarehouses",$warehouse_info);
				if (!$q2){
					return "failed";
				}
			}
		}

		$this->db->trans_commit();
		$this->session->set_flashdata('success', 'Success!! New User created Succssfully!!');
		return "success";

		

	}
	public function verify_and_update($data){
		
		extract($_POST);
		extract($data);
		$this->db->trans_begin();

		$profile_picture='';
		if(!empty($_FILES['profile_picture']['name'])){
			
			$config['upload_path']          = './uploads/users/';
	        $config['allowed_types']        = 'gif|jpg|png';
	        $config['max_size']             = 500;
	        $config['max_width']            = 500;
	        $config['max_height']           = 500;

	        $this->load->library('upload', $config);

	        if ( ! $this->upload->do_upload('profile_picture'))
	        {
	                $error = array('error' => $this->upload->display_errors());
	                print($error['error']);
	                exit();
	        }
	        else
	        {
	        	   $profile_picture='uploads/users/'.$this->upload->data('file_name');
	        }
		}


		if(!is_admin()){
			$user_store_id = $this->db->select('store_id')->where("id",$q_id)->get('db_users')->row()->store_id;
			if(empty($user_store_id)){
				echo "Something went Wrong!!";exit();
			}
			if($user_store_id!=get_current_store_id()){
				echo "Something went Wrong!!";exit();
			}
		}

		/*$query=$this->db->query("select * from db_users where username='$new_user' and id<>$q_id")->num_rows();
		if($query>0){ return "This username already exist.";}*/
		if(!empty($mobile)){
			$query=$this->db->query("select * from db_users where mobile='$mobile' and id<>$q_id")->num_rows();
			if($query>0){ return "This Moble Number already exist.";}
		}
		if(!empty($email)){
			$query=$this->db->query("select * from db_users where email='$email' and id<>$q_id")->num_rows();
			if($query>0){ return "This Email ID already exist.";}
		}
		
		$user_data = array(
		    				'username' 				=> $new_user, 
		    				'last_name' 			=> $last_name, 
		    				'mobile' 				=> $mobile,
		    				'email' 				=> $email,
		    			);
		if(isset($role_id)){
			$user_data['role_id'] = $role_id;
		}
		$user_data['store_id']=(store_module()) ? $store_id : $this->session->userdata('store_id');	

		if(!empty($profile_picture)){
			$user_data['profile_picture'] = $profile_picture;
		}

		$q1 = $this->db->where('id',$q_id)->update('db_users', $user_data);
		if (!$q1){
			return "failed";
		}
		if(warehouse_module() && isset($_POST['warehouses']) && $role_id!=1 && $role_id!=store_admin_id()){
			$this->db->where('user_id',$q_id)->delete("db_userswarehouses");
			$warehouses_list = sizeof($_POST['warehouses']);
			foreach ($_POST['warehouses'] as $res => $val) {
				$warehouse_info = array ( 'user_id'=> $q_id, 'warehouse_id'=>$val );
				$q2 = $this->db->insert("db_userswarehouses",$warehouse_info);
				if (!$q2){
					return "failed";
				}
			}
		}

		$this->db->trans_commit();
		$this->session->set_flashdata('success', 'Success!! User Updated Succssfully!!');
		return "success";
	}

	public function status_update($id,$status){
		if($id==$this->session->userdata('inv_userid')){
			echo "You Can't Diactivate Yourself!";exit();
		}
       if (set_status_of_table($id,$status,'db_users')){
            echo "success";
        }
        else{
            echo "failed";
        }
	}
	public function password_update($currentpass,$newpass,$data){
		
        $query=$this->db->query("select * from db_users where password='$currentpass' and id=".$data['CUR_USERID']);
		if($query->num_rows()==1){

			$query1="update db_users set password='$newpass' where id=".$data['CUR_USERID'];
			if ($this->db->simple_query($query1)){
			        return "success";
			}
			else{
			        return "failed";
			}
		}
		else{
			return "Invalid Current Password!";
			}
	}
	//Get users deatils
	public function get_details($id){
		$data=$this->data;

		//Validate This suppliers already exist or not
		$query=$this->db->query("select * from db_users where id=$id");
		if($query->num_rows()==0){
			show_404();exit;
		}
		else{
			$query=$query->row();
			$data['q_id']=$query->id;
			$data['store_id']=$query->store_id;
			$data['username']=$query->username;
			$data['mobile']=$query->mobile;
			$data['email']=$query->email;
			$data['role_id']=$query->role_id;
			$data['profile_picture']=$query->profile_picture;
			$data['last_name']=$query->last_name;
			return $data;
		}
	}

	public function delete_user($id){

		if($id==1){
			echo "Restricted! Can't Delete User Admin!!";
			exit();
		}
		if(demo_app()==true){
			echo "Restricted! Can't Delete Users in Demo mode!!";
			exit();	
		}
		if($id==$this->session->userdata('inv_userid')){
			echo "You Can't Delete Yourself!";exit();
		}
		$this->db->where("id=$id");
		$this->db->where("id!=".$this->session->userdata('inv_userid'));
		//if not admin
		if(!is_admin()){
			$this->db->where("store_id",get_current_store_id());
		}

		$query1=$this->db->delete("db_users");
        if ($query1){
        	$this->session->set_flashdata('success', 'Success!! User Deleted Succssfully!');
            echo "success";
        }
        else{
            echo "failed";
        }	
	}

}
