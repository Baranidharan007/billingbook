<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Customers_advance extends MY_Controller {
	public function __construct() {
		parent::__construct();
		$this->load_global();
		$this->load->model('customers_advance_model', 'advance');
	}

	public function index() {
		$this->permission_check('brand_view');
		$data = $this->data;
		$data['page_title'] = $this->lang->line('advance_payments_list');
		$this->load->view('customers_advance/list', $data);
	}

	public function add() {
		$this->permission_check('cust_adv_payments_add');
		$data = $this->data;
		$data['page_title'] = $this->lang->line('new_advance');
		$this->load->view('customers_advance/create', $data);
	}
	public function new_advance() {
		$this->form_validation->set_rules('payment_date', 'Advance Date', 'trim|required');
		$this->form_validation->set_rules('customer_id', 'Customer Name', 'trim|required');
		$this->form_validation->set_rules('amount', 'Amount', 'trim|required');
		$this->form_validation->set_rules('payment_type', 'Payment Type', 'trim|required');

		if ($this->form_validation->run() == TRUE) {

			$result = $this->advance->store_record();
			echo $result;
		} else {
			echo "Please fill compulsary(*) fields!!";
		}
	}
	public function update($id) {
		$this->belong_to('db_custadvance', $id);
		$this->permission_check('cust_adv_payments_edit');
		$data = $this->data;

		$result = $this->advance->get_details($id, $data);
		$data = array_merge($data, $result);
		$data['page_title'] = $this->lang->line('edit_advance');
		$this->load->view('customers_advance/create', $data);
	}
	public function update_advance() {
		$this->form_validation->set_rules('payment_date', 'Advance Date', 'trim|required');
		$this->form_validation->set_rules('customer_id', 'Customer Name', 'trim|required');
		$this->form_validation->set_rules('amount', 'Amount', 'trim|required');
		$this->form_validation->set_rules('payment_type', 'Payment Type', 'trim|required');
		$this->form_validation->set_rules('q_id', '', 'trim|required');

		if ($this->form_validation->run() == TRUE) {
			$result = $this->advance->store_record('update');
			echo $result;
		} else {
			echo "Please fill compulsary(*) fields!!";
		}
	}
	

	public function ajax_list() {
		$list = $this->advance->get_datatables();

		$data = array();
		$no = $_POST['start'];
		foreach ($list as $rec) {
			$no++;
			$row = array();
			$row[] = '<input type="checkbox" name="checkbox[]" value=' . $rec->id . ' class="checkbox column_checkbox" >';
			$row[] = $rec->payment_code;
			$row[] = show_date($rec->payment_date);
			$row[] = $rec->customer_name;
			$row[] = $rec->amount;
			$row[] = $rec->payment_type;
			$row[] = ucfirst($rec->created_by);
			$str2 = '<div class="btn-group" title="View Account">
										<a class="btn btn-primary btn-o dropdown-toggle" data-toggle="dropdown" href="#">
											Action <span class="caret"></span>
										</a>
										<ul role="menu" class="dropdown-menu dropdown-light pull-right">';

										if ($this->permissions('brand_edit')) {
											$str2 .= '<li>
												<a title="Edit Record ?" href="' . base_url() . 'customers_advance/update/' . $rec->id . '">
													<i class="fa fa-fw fa-edit text-blue"></i>Edit
												</a>
											</li>';
										}

										if ($this->permissions('brand_delete')) {
											$str2 .= '<li>
												<a style="cursor:pointer" title="Delete Record ?" onclick="delete_advance(' . $rec->id . ')">
													<i class="fa fa-fw fa-trash text-red"></i>Delete
												</a>
											</li>
											</ul>
										</div>';
										}

			$row[] = $str2;
			$data[] = $row;
		}

		$output = array(
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->advance->count_all(),
			"recordsFiltered" => $this->advance->count_filtered(),
			"data" => $data,
		);
		//output to json format
		echo json_encode($output);
	}

	public function update_status() {
		$this->permission_check_with_msg('cust_adv_payments_edit');
		$id = $this->input->post('id');
		$status = $this->input->post('status');

		$result = $this->advance->update_status($id, $status);
		return $result;
	}

	public function delete_advance() {
		$this->permission_check_with_msg('cust_adv_payments_delete');
		$id = $this->input->post('q_id');
		return $this->advance->delete_advance_from_table($id);
	}
	public function multi_delete() {
		$this->permission_check_with_msg('cust_adv_payments_delete');
		$ids = implode(",", $_POST['checkbox']);
		return $this->advance->delete_advance_from_table($ids);
	}

}
