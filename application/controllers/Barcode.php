<?php
if( ! defined('BASEPATH')) exit('No direct script access allowed');

class Barcode extends CI_Controller {
	public function __construct(){
		parent::__construct();
		//$this->load_global();
	}
	/*function _remap($input) {
        $this->index($input);
    }*/
    function index($input){
    	$this->load->library('zend');
		$this->zend->load('Zend/Barcode');

		$barcodeOptions = array(
			    		'text' => $input, 
			    		'fontSize' => 10, 
			    		'factor'=>1.8,
			    		'barHeight'=> 12, 
						);
		$rendererOptions = array();
		$renderer = Zend_Barcode::factory('Code128', 'image', $barcodeOptions, $rendererOptions)->render();

		/*
		Zend Barcode Library:
		1. Code128 	-> Allowed characters: the complete ASCII-character set
			code128 : working
		
		2. Code25 	-> Allowed characters:‘0123456789’
				code25: not works
		3. Code39	-> Allowed characters:‘0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ -.$/+%’
				code39 : works
		*/
    }

    public function get_barcode(){
    	return $this->index($this->input->get('code'));
    }
}

