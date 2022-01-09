<?php 
	/**
	 * Author: Askarali
	 * Date: 06-11-2018
	 */
	class Users extends MY_Controller{
		public function __construct(){
			parent::__construct();
			$this->load_global();

			$this->load->model('state_model','state');
		}
		
		public function index(){
			$this->permission_check('users_add');
			$data=$this->data;//My_Controller constructor data accessed here
			$data['page_title']=$this->lang->line('create_users');
			$this->load->view('users',$data);
		}
		public function save_or_update(){
			$data=$this->data;//My_Controller constructor data accessed here
			$this->form_validation->set_rules('new_user', 'Usenname', 'required|trim|min_length[5]|max_length[50]');
			
			if($_GET['command']!='update'){
				$this->form_validation->set_rules('pass', 'Password', 'required|trim|min_length[5]|max_length[50]');
			}

			if ($this->form_validation->run() == TRUE) {
				$this->load->model('users_model');
				
				if($_GET['command']!='update'){
					$result=$this->users_model->verify_and_save($data);
				}
				else{
					
					$result=$this->users_model->verify_and_update($data);
				}
				
				echo $result;
			} 
			else {
				echo validation_errors();
				//echo  "Username & Password must have 5 to 15 Characters!";
			}
		
		}
		public function view(){
			$this->permission_check('users_view');
			$data=$this->data;//My_Controller constructor data accessed here
			$data['page_title']=$this->lang->line('users_list');
			$this->load->view('users-view',$data);
		}
		public function status_update(){
			$this->permission_check_with_msg('users_edit');
			$userid=$this->input->post('id');
			$status=$this->input->post('status');

			$this->load->model('users_model');
			$result=$this->users_model->status_update($userid,$status);
			return $result;

		}
		public function password_reset(){
			$data=$this->data;//My_Controller constructor data accessed here
			$data['page_title']=$this->lang->line('change_password');
			$this->load->view('change-pass',$data);
		}
		public function password_update(){
			if($this->session->userdata('inv_username')=='admin' && demo_app()){
	        	echo "Restricted Admin Password Change";exit();
	        }
	        if(demo_app()){
				echo "Restricted in Demo";exit();
			}
			$data=$this->data;//My_Controller constructor data accessed here
			$currentpass=$this->input->post('currentpass');
			$newpass=$this->input->post('newpass');

			$this->load->model('users_model');
			$result=$this->users_model->password_update(md5($currentpass),md5($newpass),$data);
			echo $result;

		}
		public function dbbackup(){
			if(demo_app()){
				echo "Restricted in Demo";exit();
			}
			if(!special_access()){
				$this->permission_check_with_msg('database_backup');
			}

			if(!special_access()){
				show_error("Access Denied", 403, $heading = "Unauthorized Access!!");exit();	
			}
	            
			// Load the DB utility class
			$this->load->dbutil();
			$prefs = array( 'newline' => "\n",
				'format' => 'zip',
				'filename' => 'database_backup.sql',
				'foreign_key_checks' => FALSE,
				);


			// Backup your entire database and assign it to a variable
			$backup = $this->dbutil->backup($prefs);

			// Load the file helper and write the file to your server
			$this->load->helper('file');
			write_file('dbbackup/dbbackup'.date('d-M-Y-h-m-s').'.gz', $backup);

			// Load the download helper and send the file to your desktop
			$this->load->helper('download');
			force_download('dbbackup/dbbackup'.date('d-M-Y-h-m-s').'.gz', $backup);

		}

		public function edit($id){
			if(!is_admin()){
				$user_store_id = $this->db->select('store_id')->where("id",$id)->get('db_users')->row()->store_id;
				if(empty($user_store_id)){
					show_error("Invalid Data", 403, $heading = "You have entered Invalid Data!!");exit();
				}
				if($user_store_id!=get_current_store_id()){
					show_error("Access Denied", 403, $heading = "Unauthorized Access!!");exit();
				}
			}
			$this->permission_check('users_edit');
			$this->load->model('users_model');
			$data=$this->users_model->get_details($id);
			$data['page_title']=$this->lang->line('edit_user');
			$this->load->view('users', $data);
		}
		public function delete_user(){
			$this->permission_check_with_msg('users_delete');
			$this->load->model('users_model');
			$id=$this->input->post('q_id');
			$result=$this->users_model->delete_user($id);
			return $result;
		}
	}

	

?>
