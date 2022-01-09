<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Money_deposit extends MY_Controller {
	public function __construct(){
		parent::__construct();
		$this->load_global();
		$this->load->model('money_deposit_model','money_deposit');
	}
	public function index()
	{
		$this->permission_check('money_deposit_view');
		$data=$this->data;
		$data['page_title']=$this->lang->line('deposit_list');
		$this->load->view('accounts/money_deposit_list',$data);
	}
	public function add()
	{
		$this->permission_check('money_deposit_add');
		$data=$this->data;
		$data['page_title']=$this->lang->line('deposit');
		$this->load->view('accounts/money_deposit',$data);
	}
	
	
	public function new_money_deposit(){
		$this->permission_check('money_deposit_add');
		$this->form_validation->set_rules('deposit_date', 'Deposit date', 'trim|required');
		$this->form_validation->set_rules('credit_account_id', 'To Account', 'trim|required');
		$this->form_validation->set_rules('amount', 'Amount', 'trim|required');
		
		if ($this->form_validation->run() == TRUE) {
			$result=$this->money_deposit->verify_and_save();
			echo $result;
		} else {
			echo "Please Fill Compulsory(* marked) Fields.";
		}
	}
	public function update($id){
		$this->belong_to('ac_moneydeposits',$id);
		$this->permission_check('money_deposit_edit');
		$data=$this->data;
		$result=$this->money_deposit->get_details($id,$data);
		$data=array_merge($data,$result);
		$data['page_title']=$this->lang->line('money_deposit');
		$this->load->view('accounts/money_deposit', $data);
	}
	public function update_money_deposit(){
		$this->form_validation->set_rules('deposit_date', 'Deposit date', 'trim|required');
		$this->form_validation->set_rules('credit_account_id', 'To Account', 'trim|required');
		$this->form_validation->set_rules('amount', 'Amount', 'trim|required');
		
		if ($this->form_validation->run() == TRUE) {
			$result=$this->money_deposit->update_money_deposit();
			echo $result;
		} else {
			echo "Please Fill Compulsory(* marked) Fields.";
		}
	}

	public function ajax_list()
	{
		$list = $this->money_deposit->get_datatables();
		
		$data = array();
		$no = $_POST['start'];
		foreach ($list as $money_deposit) {
			$no++;
			$row = array();
			$row[] = '<input type="checkbox" name="checkbox[]" value='.$money_deposit->id.' class="checkbox column_checkbox" >';
			$row[] = show_date($money_deposit->deposit_date);
			$row[] = $money_deposit->reference_no;

			$row[] = (!empty($money_deposit->debit_account_id)) ? get_account_name($money_deposit->debit_account_id) : '';
			$row[] = get_account_name($money_deposit->credit_account_id);

			$row[] = store_number_format($money_deposit->amount);

			$row[] = ucfirst($money_deposit->created_by);			
				     $str2 = '<div class="btn-group" title="View Money Deposit">
										<a class="btn btn-primary btn-o dropdown-toggle" data-toggle="dropdown" href="#">
											Action <span class="caret"></span>
										</a>
										<ul role="menu" class="dropdown-menu dropdown-light pull-right">';

											if($this->permissions('money_deposit_edit'))
											$str2.='<li>
												<a title="Edit Record ?" href="'.base_url().'money_deposit/update/'.$money_deposit->id.'">
													<i class="fa fa-fw fa-edit text-blue"></i>Edit
												</a>
											</li>';

											if($this->permissions('money_deposit_delete'))
											$str2.='<li>
												<a style="cursor:pointer" title="Delete Record ?" onclick="delete_money_deposit('.$money_deposit->id.')">
													<i class="fa fa-fw fa-trash text-red"></i>Delete
												</a>
											</li>
											
										</ul>
									</div>';			
			$row[] = $str2;

			$data[] = $row;
		}

		$output = array(
						"draw" => $_POST['draw'],
						"recordsTotal" => $this->money_deposit->count_all(),
						"recordsFiltered" => $this->money_deposit->count_filtered(),
						"data" => $data,
				);
		//output to json format
		echo json_encode($output);
	}
	
	public function delete_money_deposit(){
		$this->permission_check_with_msg('money_deposit_delete');
		$id=$this->input->post('q_id');
		echo $this->money_deposit->delete_money_deposit_from_table($id);
	}
	public function multi_delete_money_deposit(){
		$this->permission_check_with_msg('money_deposit_delete');
		$ids=implode (",",$_POST['checkbox']);
		echo $this->money_deposit->delete_money_deposit_from_table($ids);
	}
	


}
