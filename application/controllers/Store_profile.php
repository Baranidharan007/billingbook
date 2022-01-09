<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Store_profile extends MY_Controller {
	public function __construct(){
		parent::__construct();
		$this->load_global();
		$this->load->model('store_profile_model','store');
	}

	public function update($id){
		//if not admin
		if(!is_admin()){
			if($id!=get_current_store_id()){
				show_error("Access Denied", 403, $heading = "You Don't Have Enough Permission!!");exit();
			}
		}

		$this->permission_check('store_edit');
		$data=$this->store->get_details($id);
		$data['page_title']=$this->lang->line('store');
		$this->load->view('store', $data);
	}
	public function update_store(){
		$result=$this->store->update_store();
		echo $result;	
	}

}