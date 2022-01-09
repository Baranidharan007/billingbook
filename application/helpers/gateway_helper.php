<?php

function paypal(){
	$CI =& get_instance();
	return $CI->db->select("*")->where('store_id',1)->get("db_paypal")->row();
}

function instamojo(){
	$CI =& get_instance();
	return $CI->db->select("*")->where('store_id',1)->get("db_instamojo")->row();
}

function stripe(){
	$CI =& get_instance();
	return $CI->db->select("*")->where('store_id',1)->get("db_stripe")->row();
}