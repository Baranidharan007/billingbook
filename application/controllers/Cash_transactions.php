<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cash_transactions extends MY_Controller {
	public function __construct() {
		parent::__construct();
		$this->load_global();
		$this->load->model('cash_transactions_model', 'cash');
	}

	public function ajax_list() {
		$list = $this->cash->get_datatables();

		$data = array();
		$no = $_POST['start'];
		$prev_balance = 0;
		foreach ($list as $cash) {
			$no++;
			$row = array();
			$row[] = show_date($cash->payment_date);
			$row[] = $cash->payment_code;
			$row[] = $cash->payment_type;
			$row[] = $cash->payment;
			$row[] = $cash->payment_note;
			$row[] = ucfirst($cash->created_by);
			$row[] = get_account_name($cash->account_id);
					 $account_of=0;
					 if($cash->SALES_PAYMENT == "SALES_PAYMENT"){
					 	$account_of = 1;
					 }
					 if($cash->SALES_PAYMENT == "PURCHASE_PAYMENT"){
					 	$account_of = 2;
					 }
					 if($cash->SALES_PAYMENT == "SALES_RETURN_PAYMENT"){
					 	$account_of = 3;
					 }
					 if($cash->SALES_PAYMENT == "PURCHASE_RETURN_PAYMENT"){
					 	$account_of = 4;
					 }
					 if($cash->SALES_PAYMENT == "EXPENSE"){
					 	$account_of = 5;
					 }


			$row[] = '<button type="button" class="btn btn-primary btn-flat btn-sm" onclick="link_account('.$account_of.','.$cash->id.','.$cash->account_id.')">Link Account</button>';
			$data[] = $row;
		}

		$output = array(
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->cash->count_all(),
			"recordsFiltered" => $this->cash->count_filtered(),
			"data" => $data,
		);
		//output to json format
		echo json_encode($output);
	}

	public function delete_transaction() {
		$entry_of = $this->input->post('entry_of');

		$this->permission_check_with_msg('cash_delete');
		$id = $this->input->post('q_id');

		if ($entry_of == 'transfer') {
			$this->load->model('money_transfer_model');
			echo $this->money_transfer_model->delete_money_transfer_from_table($id);
		} else if ($entry_of == 'deposit') {
			$this->load->model('money_deposit_model');
			echo $this->money_deposit_model->delete_money_deposit_from_table($id);
		}

	}

	public function link_account(){
		echo $this->cash->link_account();
	}

}
