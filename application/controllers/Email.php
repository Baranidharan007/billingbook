<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Email extends MY_Controller {
	public function __construct(){
		parent::__construct();
		$this->load_global();
	}
	
	//Open SMS Form 
	public function index(){
		$this->permission_check('send_email');
		$data=$this->data;
		$data['page_title']=$this->lang->line('send_email');
		$this->load->view('email', $data);
	}


	//Create Message
	public function send_message(){
		$this->permission_check('send_email');
		$data=$this->data;
		$this->load->model('email_model');
		$email_info = array(
							'email_to' 			=> $this->input->post('email_to'), 
							'subject' 			=> $this->input->post('email_subject'), 
							'message' 			=> $this->input->post('email_content'), 
						);
		$result= $this->email_model->send_email($email_info);
		echo $result;
	}

	
	//Open SMS API Form 
	/*public function api(){
		
		$this->permission_check('email_settings');
		$data=$this->data;
		$data['page_title']=$this->lang->line('email_api');
		$this->load->view('email-api', $data);
	}*/

	//UPDATE SMS API
	/*public function api_update(){
		$this->permission_check_with_msg('email_settings');
		$this->load->model('email_model');
    	echo $this->email_model->api_update();
	}
	public function send_SMS_by_Twilio(){
		$this->load->model('twilio_model');
		$this->twilio_model->index();
	}*/
}

