<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use Twilio\Rest\Client;
class Twilio_model extends CI_Model {

    public function index($mobile,$message)
    {

        $data = ['phone' => $mobile, 'text' => $message];
        //echo "<pre>";
        //print_r($this->sendSMS($data));
        return $this->sendSMS($data);
    }

    protected function sendSMS($data) {
        $q1=$this->db->select("*")->where('store_id',get_current_store_id())->get("db_twilio");
        if($q1->num_rows()>0){
            $account_sid = $q1->row()->account_sid;
            $auth_token = $q1->row()->auth_token;
            $twilio_phone = $q1->row()->twilio_phone;

            if(empty($account_sid) || empty($auth_token) || empty($twilio_phone)){
                return "Invalid Twilio API Details!";
            }

            $client = new Client($account_sid, $auth_token);
            try{
            // Use the client to do fun stuff like send text messages!
             $response = $client->messages->create(
                // the number you'd like to send the message to
                $data['phone'],
                array(
                    // A Twilio phone number you purchased at twilio.com/console
                    "from" => $twilio_phone,
                    // the body of the text message you'd like to send
                    'body' => $data['text']
                )
            );

             //print($response->status);
             if($response->status=='queued' || $response->status=='sent'){
                return "success";
             }
             else{
                return "failed";
             }

            }
            catch(Exception $e){
               // print_r($e);
                return "failed";
            }


        }
          
    }

}

/* End of file twilio_sms.php */
/* Location: ./application/models/twilio_sms.php */