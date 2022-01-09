<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Accounts extends MY_Controller {
	public function __construct(){
		parent::__construct();
		$this->load_global();
		$this->load->model('accounts_model','accounts');
	}
	public function index()
	{
		$this->permission_check('accounts_view');
		$data=$this->data;
		$data['page_title']=$this->lang->line('accounts_list');
		$this->load->view('accounts/accounts_list',$data);
	}

	public function book($account_id)
	{
		$this->belong_to('ac_accounts',$account_id);
		$this->permission_check('accounts_view');
		$data=$this->data;
		$data['page_title']=$this->lang->line('account_book');
		$data['account_id']=$account_id;
		$this->load->view('accounts/account_book',$data);
	}
	public function cash_transactions()
	{
		$this->permission_check('accounts_view');
		$data=$this->data;
		$data['page_title']=$this->lang->line('cash_transactions');
		$this->load->view('accounts/cash_transactions',$data);
	}

	public function add()
	{
		$this->permission_check('accounts_add');
		$data=$this->data;
		$data['page_title']=$this->lang->line('add_account');
		$this->load->view('accounts/accounts',$data);
	}
	
	
	public function newaccounts(){
		$this->permission_check('accounts_add');
		$this->form_validation->set_rules('account_code', 'Account Code', 'trim|required');
		$this->form_validation->set_rules('account_name', 'Account Name', 'trim|required');
		$this->form_validation->set_rules('opening_balance', 'Oprning Balance', 'trim|required');
		
		if ($this->form_validation->run() == TRUE) {
			$result=$this->accounts->verify_and_save();
			echo $result;
		} else {
			echo "Please Fill Compulsory(* marked) Fields.";
		}
	}
	public function update($id){
		$this->permission_check('accounts_edit');
		$this->belong_to('ac_accounts',$id);
		$data=$this->data;
		$result=$this->accounts->get_details($id,$data);
		$data=array_merge($data,$result);
		$data['page_title']=$this->lang->line('accounts');
		$this->load->view('accounts/accounts', $data);
	}
	public function update_accounts(){
		$this->form_validation->set_rules('account_code', 'Account Code', 'trim|required');
		$this->form_validation->set_rules('account_name', 'Account Name', 'trim|required');
		//$this->form_validation->set_rules('opening_balance', 'Oprning Balance', 'trim|required');

		if ($this->form_validation->run() == TRUE) {
			$result=$this->accounts->update_accounts();
			echo $result;
		} else {
			echo "Please Fill Compulsory(* marked) Fields.";
		}
	}

	public function ajax_list()
	{
		$list = $this->accounts->get_datatables();
		
		$data = array();
		$no = $_POST['start'];
		foreach ($list as $accounts) {
			$no++;
			$row = array();
			$row[] = ($accounts->delete_bit) ? '<span data-toggle="tooltip" title="Resticted" class="text-danger fa fa-fw fa-ban"></span>' : '<input type="checkbox" name="checkbox[]" value='.$accounts->id.' class="checkbox column_checkbox" >';
			//$row[] = get_store_name($accounts->store_id);
			$row[] = $accounts->account_code;
			$row[] = $accounts->account_name;
			$row[] = get_account_name($accounts->parent_id);
			$row[] = store_number_format($accounts->balance);
			
			$row[] = ucfirst($accounts->created_by);			
				     $str2 = '<div class="btn-group" title="View Account">
										<a class="btn btn-primary btn-o dropdown-toggle" data-toggle="dropdown" href="#">
											Action <span class="caret"></span>
										</a>
										<ul role="menu" class="dropdown-menu dropdown-light pull-right">';

											if($this->permissions('accounts_edit'))
											$str2.='<li>
												<a data-toggle="tooltip" title="Edit Record ?" href="'.base_url().'accounts/update/'.$accounts->id.'">
													<i class="fa fa-fw fa-edit text-blue"></i>Edit
												</a>
											</li>';

											if($this->permissions('accounts_view'))
											$str2.='<li>
												<a data-toggle="tooltip" title="Click to check Account!" href="'.base_url().'accounts/book/'.$accounts->id.'">
													<i class="fa fa-fw fa-book text-blue"></i>Account Book
												</a>
											</li>';

											if($this->permissions('accounts_delete') && !$accounts->delete_bit)
											$str2.='<li>
												<a style="cursor:pointer" data-toggle="tooltip" title="Delete Record ?" onclick="delete_accounts('.$accounts->id.')">
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
						"recordsTotal" => $this->accounts->count_all(),
						"recordsFiltered" => $this->accounts->count_filtered(),
						"data" => $data,
				);
		//output to json format
		echo json_encode($output);
	}
	
	public function delete_accounts(){
		$this->permission_check_with_msg('accounts_delete');
		$id=$this->input->post('q_id');
		echo $this->accounts->delete_accounts_from_table($id);
	}
	public function multi_delete_accounts(){
		$this->permission_check_with_msg('accounts_delete');
		$ids=implode (",",$_POST['checkbox']);
		echo $this->accounts->delete_accounts_from_table($ids);
	}
	


}
