<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Suppliers extends MY_Controller {
	public function __construct(){
		parent::__construct();
		$this->load_global();
		$this->load->model('suppliers_model','suppliers');
	}
	
	public function index()
	{
		$this->permission_check('suppliers_view');
		$data=$this->data;
		$data['page_title']=$this->lang->line('suppliers_list');
		$this->load->view('suppliers-list',$data);
	}
	public function add()
	{
		$this->permission_check('suppliers_add');
		$data=$this->data;
		$data['page_title']=$this->lang->line('suppliers');
		$this->load->view('suppliers',$data);
	}

	public function newsuppliers(){
		$this->form_validation->set_rules('supplier_name', 'Supplier Name', 'trim|required');
		
		if ($this->form_validation->run() == TRUE) {
			$result=$this->suppliers->verify_and_save();
			echo $result;
		} else {
			echo "Please Fill Compulsory(* marked) Fields.";
		}
	}
	public function update($id){
		$this->belong_to('db_suppliers',$id);
		$this->permission_check('suppliers_edit');
		$data=$this->data;
		$result=$this->suppliers->get_details($id,$data);
		$data=array_merge($data,$result);
		$data['page_title']=$this->lang->line('suppliers');
		$this->load->view('suppliers', $data);
	}
	public function update_suppliers(){
		$this->form_validation->set_rules('supplier_name', 'Customer Name', 'trim|required');
		
		if ($this->form_validation->run() == TRUE) {
			$result=$this->suppliers->update_suppliers();
			echo $result;
		} else {
			echo "Please Enter suppliers name.";
		}
	}

	public function ajax_list()
	{
		$list = $this->suppliers->get_datatables();
		
		$data = array();
		$no = $_POST['start'];
		foreach ($list as $suppliers) {
			$opening_balance =(!empty($suppliers->opening_balance)) ? $suppliers->opening_balance : 0;
			$opening_balance -=get_paid_sob($suppliers->id);
			$purchase_due =(!empty($suppliers->purchase_due)) ? $suppliers->purchase_due : 0;
			$purchase_return_due =(!empty($suppliers->purchase_return_due)) ? $suppliers->purchase_return_due : 0;
			$total = ($opening_balance)+$purchase_due-$purchase_return_due;

			$no++;
			$row = array();
			$row[] = '<input type="checkbox" name="checkbox[]" value='.$suppliers->id.' class="checkbox column_checkbox" >';
			
			$row[] = $suppliers->supplier_code;
			$row[] = $suppliers->supplier_name;
			$row[] = $suppliers->mobile;
			$row[] = $suppliers->email;
			$row[] = store_number_format($opening_balance);
			$row[] = store_number_format($purchase_due);
			$row[] = store_number_format($purchase_return_due);
			$row[] = store_number_format($total);

			 		if($suppliers->status==1){ 
			 			$str= "<span onclick='update_status(".$suppliers->id.",0)' id='span_".$suppliers->id."'  class='label label-success' style='cursor:pointer'>Active </span>";}
					else{ 
						$str = "<span onclick='update_status(".$suppliers->id.",1)' id='span_".$suppliers->id."'  class='label label-danger' style='cursor:pointer'> Inactive </span>";
					}
			$row[] = $str;			
					$str2 = '<div class="btn-group" title="View Account">
										<a class="btn btn-primary btn-o dropdown-toggle" data-toggle="dropdown" href="#">
											Action <span class="caret"></span>
										</a>
										<ul role="menu" class="dropdown-menu dropdown-light pull-right">';

											if($this->permissions('suppliers_edit'))
											$str2.='<li>
												<a title="Edit Record ?" href="'.base_url().'suppliers/update/'.$suppliers->id.'">
													<i class="fa fa-fw fa-edit text-blue"></i>Edit
												</a>
											</li>';
											if($this->permissions('purchase_payment_add'))
						                      $str2.='<li>
						                        <a title="Pay Opening Balance & Purchase Due Payments" class="pointer" onclick="pay_now('.$suppliers->id.')" >
						                          <i class="fa fa-fw fa-money text-blue"></i>Pay Due Payments
						                        </a>
						                      </li>';
						                      if($this->permissions('purchase_return_payment_add'))
						                      $str2.='<li>
						                        <a title="Receive Return Due" class="pointer" onclick="pay_return_due('.$suppliers->id.')" >
						                          <i class="fa fa-fw fa-money text-blue"></i>Receive Return Due
						                        </a>
						                      </li>';
											if($this->permissions('suppliers_edit'))
											$str2.='<li>
												<a style="cursor:pointer" title="Delete Record ?" onclick="delete_suppliers('.$suppliers->id.')">
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
						"recordsTotal" => $this->suppliers->count_all(),
						"recordsFiltered" => $this->suppliers->count_filtered(),
						"data" => $data,
				);
		//output to json format
		echo json_encode($output);
	}
	public function update_status(){
		$this->permission_check_with_msg('suppliers_edit');
		$id=$this->input->post('id');
		$status=$this->input->post('status');

		$result=$this->suppliers->update_status($id,$status);
		return $result;
	}
	
	public function delete_suppliers(){
		$this->permission_check_with_msg('suppliers_delete');
		$id=$this->input->post('q_id');
		return $this->suppliers->delete_suppliers_from_table($id);
	}
	public function multi_delete(){
		$this->permission_check_with_msg('suppliers_delete');
		$ids=implode (",",$_POST['checkbox']);
		return $this->suppliers->delete_suppliers_from_table($ids);
	}
	
	public function show_pay_now_modal(){
	    $this->permission_check_with_msg('purchase_payment_add');
	    $supplier_id=$this->input->post('supplier_id');
	    echo $this->suppliers->show_pay_now_modal($supplier_id);
	}
	public function save_payment(){
	    $this->permission_check_with_msg('purchase_payment_add');
	    echo $this->suppliers->save_payment();
	}
	public function show_pay_return_due_modal(){
	    $this->permission_check_with_msg('purchase_return_payment_add');
	    $supplier_id=$this->input->post('supplier_id');
	    echo $this->suppliers->show_pay_return_due_modal($supplier_id);
	}
	public function save_return_due_payment(){
	    $this->permission_check_with_msg('purchase_payment_add');
	    echo $this->suppliers->save_return_due_payment();
	}
	public function delete_opening_balance_entry(){
		$this->permission_check_with_msg('sales_payment_delete');
		$entry_id = $this->input->post('entry_id');
		echo $this->suppliers->delete_opening_balance_entry($entry_id);
	}
}
