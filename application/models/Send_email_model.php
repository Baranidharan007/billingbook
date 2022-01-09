<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Send_email_model extends CI_Model {
	
	public function send_message($to_email='',$subject='',$message)
	{
		/*
		Steps: 
		1.
		Open Gmail -> Account -> Security -> Disable 2 Step Authorization -> Search for -> Less secured app access -> Enable

		2.
		Localhost:
			// Remove  ";" from this line to enable
				extension=php_openssl.dll
	
		3.
			Help link: 
				https://www.formget.com/codeigniter-gmail-smtp/
				http://getsourcecodes.com/codeigniter-tutorials/sending-email-via-gmail-smtp-server-in-codeigniter/
		*/


		//Store details
		$store_rec = get_store_details();
		if(!$store_rec->smtp_status){
			return "Email sending is disabled!!";
		}
		//Load email library
		$this->load->library('email');
		//SMTP & mail configuration
		$config = array(
		            'protocol' => 'smtp', 
		            'smtp_host' => $store_rec->smtp_host,//'ssl://smtp.gmail.com', 
		            'smtp_port' => $store_rec->smtp_port,//465, 
		            'smtp_user' => $store_rec->smtp_user,//'example@gmail.com', 
		            'smtp_pass' => $store_rec->smtp_pass, 
		            'mailtype' => 'html', 
		            'charset' => 'iso-8859-1'
		);
		$this->email->initialize($config);
		$this->email->set_mailtype("html");
		$this->email->set_newline("\r\n");
		 
		//Email content
		$htmlContent = $message;
		 
		$this->email->to($to_email);
		$this->email->from($store_rec->smtp_user,$store_rec->store_name);
		$this->email->subject($subject);
		//$this->email->attach(base_url('uploads/upi/webmagics-qr.png'));
		$this->email->message($htmlContent);
		 
		//Send email
		if($this->email->send()){
			echo "Email Sent Successfully!!";
		}
		else{
			echo "Failed to send Email!!";
		}
		echo $this->email->print_debugger();	
	}

}

/* End of file Send_email_model.php */
/* Location: ./application/models/Send_email_model.php */