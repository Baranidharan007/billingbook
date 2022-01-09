<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Smtp extends MY_Controller {
	public function __construct(){
		parent::__construct();
		$this->load_global();
	}
	
	//Open SMS Form 
	public function index(){
		$this->permission_check('smtp_settings');
		$data=$this->data;
		$data['page_title']=$this->lang->line('smtp_settings');
		$smtp_rec = get_store_details();
		$data['smtp_status']=$smtp_rec->smtp_status;
		$data['smtp_host']=$smtp_rec->smtp_host;
		$data['smtp_port']=$smtp_rec->smtp_port;
		$data['smtp_user']=$smtp_rec->smtp_user;
		$data['smtp_pass']=$smtp_rec->smtp_pass;
		$this->load->view('smtp', $data);
	}


	//Create Message
	public function send_message(){
		$this->permission_check('send_sms');
		$data=$this->data;
		$this->load->model('sms_model');
		extract($this->security->xss_clean(html_escape($_POST)));
		$result= $this->sms_model->send_sms($mobile,$message);
		echo $result;
	}

	
	//Open SMS API Form 
	public function api(){
		if(!is_admin()){
			show_error("Access Denied", 403, $heading = "Unauthorized Access!!");exit();	
		}
		$this->permission_check('sms_api_view');
		$data=$this->data;
		$data['page_title']=$this->lang->line('sms_api');
		$this->load->view('sms-api', $data);
	}

	//UPDATE SMS API
	public function update_smtp(){
		$this->permission_check_with_msg('smtp_settings');
		//Extract Inputs
		extract(xss_html_filter(array_merge($this->data,$_POST,$_GET)));

		//Update SMTP Settings
		$info['smtp_status'] = $smtp_status;
		$info['smtp_host'] = $smtp_host;
		$info['smtp_port'] = $smtp_port;
		$info['smtp_user'] = $smtp_user;
		$info['smtp_pass'] = $smtp_pass;

		$q1 = $this->db->where("id",get_current_store_id())->update("db_store",$info);
		if(!$q1){
			echo "failed";
		}
		echo "success";

	}
	public function send_SMS_by_Twilio(){
		$this->load->model('twilio_model');
		$this->twilio_model->index();
	}
}

