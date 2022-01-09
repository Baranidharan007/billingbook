<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sales_payments extends MY_Controller {
	public function __construct(){
		parent::__construct();
		$this->load_global();
		$this->load->model('sales_payments_model','sales');
	}

	public function index()
	{
		$this->permission_check('sales_payment_view');
		$data=$this->data;
		$data['page_title']=$this->lang->line('sales_payments');
		$this->load->view('sales_payments/list',$data);
	}


	public function ajax_list()
	{
		$list = $this->sales->get_datatables();
		
		$data = array();
		$no = $_POST['start'];
		foreach ($list as $sales) {
			
			$no++;
			$row = array();
			
			$row[] = $sales->payment_code;
			$row[] = show_date($sales->payment_date);
			$row[] = get_sales_code($sales->sales_id);
			$row[] = get_customer_details($sales->customer_id)->customer_name;
			$row[] = store_number_format($sales->payment);


			//$cheque_status = ($sales->cheque_status) ? 'Cleared' : 'Not Cleared';
			$str = (!empty($sales->cheque_number)) ? "<br>Cheque no.:".$sales->cheque_number."<br>Period:".$sales->cheque_period."<br>Status:".
			"<span class='label label-info' style='cursor:pointer'>".$sales->cheque_status."</span>" : '';
			$row[] = ($sales->payment_type).$str;


			$row[] = $sales->payment_note;
			$row[] = ucfirst($sales->created_by);

					$str2 = '<div class="btn-group" title="View Account">
										<a class="btn btn-primary btn-o dropdown-toggle" data-toggle="dropdown" href="#">
											Action <span class="caret"></span>
										</a>
										<ul role="menu" class="dropdown-menu dropdown-light pull-right">';
											
											if($this->permissions('sales_payment_add') && strtoupper($sales->payment_type)==strtoupper(cheque_name()))
											$str2.='<li>
												<a title="Update Cheque Status" class="pointer" onclick="update_cheque_status('.$sales->id.')" >
													<i class="fa fa-fw fa-hourglass-half text-blue"></i>Update Cheque Status
												</a>
											</li>';



											if($this->permissions('sales_payment_delete'))
											$str2.='<li>
												<a style="cursor:pointer" title="Delete Record ?" onclick="delete_payment(\''.$sales->id.'\')">
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
						"recordsTotal" => $this->sales->count_all(),
						"recordsFiltered" => $this->sales->count_filtered(),
						"data" => $data,
				);
		//output to json format
		echo json_encode($output);
	}
	
	
	/*public function multi_delete(){
		$this->permission_check_with_msg('sales_delete');
		$ids=implode (",",$_POST['checkbox']);
		echo $this->sales->delete_payment($ids);
	}*/


	//Table ajax code
	/*public function search_item(){
		$q=$this->input->get('q');
		$result=$this->sales->search_item($q);
		echo $result;
	}
	public function find_item_details(){
		$id=$this->input->post('id');
		
		$result=$this->sales->find_item_details($id);
		echo $result;
	}*/


	public function delete_payment(){
		$this->permission_check_with_msg('sales_payment_delete');
		$payment_id = $this->input->post('payment_id');
		echo $this->sales->delete_payment($payment_id);
	}
	public function show_cheque_payments_modal(){
		$this->permission_check_with_msg('sales_payment_view');
		$payment_id=$this->input->post('payment_id');
		echo $this->sales->show_cheque_payments_modal($payment_id);
	}
	public function update_cheque_payment(){
		$this->permission_check_with_msg('sales_add');
		echo $this->sales->update_cheque_payment();
	}

	//Print sales Payment Receipt
	/*public function print_show_receipt($payment_id){
		if(!$this->permissions('sales_add') && !$this->permissions('sales_edit')){
			$this->show_access_denied_page();
		}
		$data=$this->data;
		$data['page_title']=$this->lang->line('payment_receipt');
		$data=array_merge($data,array('payment_id'=>$payment_id));
		$this->load->view('print-cust-payment-receipt',$data);
	}*/
	
	
	
}
