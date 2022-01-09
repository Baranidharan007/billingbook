<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Quotation extends MY_Controller {
	public function __construct(){
		parent::__construct();
		$this->load_global();
		$this->load->model('quotation_model','quotation');
		$this->load->helper('sms_template_helper');
	}

	public function is_sms_enabled(){
		return is_sms_enabled();
	}

	public function index()
	{
		$this->permission_check('quotation_view');
		$data=$this->data;
		$data['page_title']=$this->lang->line('quotation_list');
		$this->load->view('quotation/quotation_list',$data);
	}
	public function add()
	{	
		$this->permission_check('quotation_add');
		$data=$this->data;
		$data['page_title']=$this->lang->line('quotation');
		$this->load->view('quotation/quotation',$data);
	}
	

	public function quotation_save_and_update(){
		$this->form_validation->set_rules('quotation_date', 'Quotation Date', 'trim|required');
		$this->form_validation->set_rules('customer_id', 'Customer Name', 'trim|required');
		
		if ($this->form_validation->run() == TRUE) {
	    	$result = $this->quotation->verify_save_and_update();
	    	echo $result;
		} else {
			echo "Please Fill Compulsory(* marked) Fields.";
		}
	}
	
	
	public function update($id){
		$this->belong_to('db_quotation',$id);
		$this->permission_check('quotation_edit');
		$data=$this->data;
		$data=array_merge($data,array('quotation_id'=>$id));
		$data['page_title']=$this->lang->line('quotation');
		$this->load->view('quotation/quotation', $data);
	}
	

	public function ajax_list()
	{
		$list = $this->quotation->get_datatables();
		
		$data = array();
		$no = $_POST['start'];
		foreach ($list as $quotation) {
			
			$no++;
			$row = array();
			$row[] = '<input type="checkbox" name="checkbox[]" value='.$quotation->id.' class="checkbox column_checkbox" >';
			
			$str='';
			        if($quotation->sales_status!='')
			          $str="<span title='Converted to Sales Invoice' class='label label-success' style='cursor:pointer'> Converted </span>";
			$row[] = show_date($quotation->quotation_date)."<br>".$str;
			$row[] = (!empty($quotation->expire_date)) ? show_date($quotation->expire_date) : '';

			$row[] = $quotation->quotation_code;
			
			$row[] = $quotation->reference_no;
			$row[] = $quotation->customer_name;
			
			$row[] = store_number_format($quotation->grand_total);
			$row[] = ucfirst($quotation->created_by);

					 $str1=base_url().'quotation/update/';

					$str2 = '<div class="btn-group" title="View Account">
										<a class="btn btn-primary btn-o dropdown-toggle" data-toggle="dropdown" href="#">
											Action <span class="caret"></span>
										</a>
										<ul role="menu" class="dropdown-menu dropdown-light pull-right">';

											if($this->permissions('sales_add') && $quotation->sales_status=='')
											$str2.='<li>
												<a title="Convert to Invoice" href="'.base_url().'sales/quotation/'.$quotation->id.'" >
													<i class="fa fa-fw fa-exchange text-blue"></i>Convert to Invoice
												</a>
											</li>';

											if($quotation->sales_status=='Converted')
											$str2.='<li>
												<a title="View to Invoice" href="'.base_url().'sales/invoice/'.get_sales_id_of_quotation($quotation->id).'" >
													<i class="fa fa-fw fa-eye text-blue"></i>View Sales Invoice
												</a>
											</li>';

											if($this->permissions('quotation_view'))
											$str2.='<li>
												<a title="View Invoice" href="'.base_url().'quotation/invoice/'.$quotation->id.'" >
													<i class="fa fa-fw fa-eye text-blue"></i>View Quotation
												</a>
											</li>';

											if($this->permissions('quotation_edit'))
											$str2.='<li>
												<a title="Update Record ?" href="'.$str1.$quotation->id.'">
													<i class="fa fa-fw fa-edit text-blue"></i>Edit
												</a>
											</li>';

											if($this->permissions('quotation_add') || $this->permissions('quotation_edit'))
											$str2.='<li>
												<a title="Take Print" target="_blank" href="'.base_url().'quotation/print_invoice/'.$quotation->id.'">
													<i class="fa fa-fw fa-print text-blue"></i>Print
												</a>
											</li>

											<li>
												<a title="Download PDF" target="_blank" href="'.base_url().'quotation/pdf/'.$quotation->id.'">
													<i class="fa fa-fw fa-file-pdf-o text-blue"></i>PDF
												</a>
											</li>';

											if($this->permissions('quotation_delete'))
											$str2.='<li>
												<a style="cursor:pointer" title="Delete Record ?" onclick="delete_quotation(\''.$quotation->id.'\')">
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
						"recordsTotal" => $this->quotation->count_all(),
						"recordsFiltered" => $this->quotation->count_filtered(),
						"data" => $data,
				);
		//output to json format
		echo json_encode($output);
	}
	public function update_status(){
		$this->permission_check('quotation_edit');
		$id=$this->input->post('id');
		$status=$this->input->post('status');

		
		$result=$this->quotation->update_status($id,$status);
		return $result;
	}
	public function delete_quotation(){
		$this->permission_check_with_msg('quotation_delete');
		$id=$this->input->post('q_id');
		echo $this->quotation->delete_quotation($id);
	}
	public function multi_delete(){
		$this->permission_check_with_msg('quotation_delete');
		$ids=implode (",",$_POST['checkbox']);
		echo $this->quotation->delete_quotation($ids);
	}


	//Table ajax code
	public function search_item(){
		$q=$this->input->get('q');
		$result=$this->quotation->search_item($q);
		echo $result;
	}
	public function find_item_details(){
		$id=$this->input->post('id');
		
		$result=$this->quotation->find_item_details($id);
		echo $result;
	}

	//quotation invoice form
	public function invoice($id)
	{	
		$this->belong_to('db_quotation',$id);
		if(!$this->permissions('quotation_add') && !$this->permissions('quotation_edit')){
			$this->show_access_denied_page();
		}
		$data=$this->data;
		$data=array_merge($data,array('quotation_id'=>$id));
		$data['page_title']=$this->lang->line('quotation_invoice');
		$this->load->view('quotation/quotation-invoice',$data);
	}
	
	//Print quotation invoice 
	public function print_invoice($quotation_id)
	{
		$this->belong_to('db_quotation',$quotation_id);
		if(!$this->permissions('quotation_add') && !$this->permissions('quotation_edit')){
			$this->show_access_denied_page();
		}
		$data=$this->data;
		$data=array_merge($data,array('quotation_id'=>$quotation_id));
		$data['page_title']=$this->lang->line('quotation_invoice');
		
			$this->load->view('quotation/print-quotation-invoice-2',$data);
		
	}


	public function pdf($quotation_id){
		if(!$this->permissions('quotation_add') && !$this->permissions('quotation_edit')){
			$this->show_access_denied_page();
		}
		
		$data=$this->data;
		$data['page_title']=$this->lang->line('quotation_invoice');
        $data=array_merge($data,array('quotation_id'=>$quotation_id));
      
			$this->load->view('quotation/print-quotation-invoice-2',$data);
		
       

        // Get output html
        $html = $this->output->get_output();
        // Load pdf library
        $this->load->library('pdf');
        
        // Load HTML content
        $this->dompdf->loadHtml($html);
        
        // (Optional) Setup the paper size and orientation
        $this->dompdf->setPaper('A4', 'portrait');/*landscape or portrait*/
        
        // Render the HTML as PDF
        $this->dompdf->render();
        
        // Output the generated PDF (1 = download and 0 = preview)
        $this->dompdf->stream("Quotation_$quotation_id-".date('M')."_".date('d')."_".date('Y'), array("Attachment"=>0));
	}
	
	

	
	/*v1.1*/
	public function return_row_with_data($rowcount,$item_id){
		echo $this->quotation->get_items_info($rowcount,$item_id);
	}
	public function return_quotation_list($quotation_id){
		echo $this->quotation->return_quotation_list($quotation_id);
	}
	
	public function show_pay_now_modal(){
		$this->permission_check_with_msg('quotation_view');
		$quotation_id=$this->input->post('quotation_id');
		echo $this->quotation->show_pay_now_modal($quotation_id);
	}
	public function save_payment(){
		$this->permission_check_with_msg('quotation_add');
		echo $this->quotation->save_payment();
	}
	public function view_payments_modal(){
		$this->permission_check_with_msg('quotation_view');
		$quotation_id=$this->input->post('quotation_id');
		echo $this->quotation->view_payments_modal($quotation_id);
	}
	public function get_customers_select_list(){
		echo get_customers_select_list(null,$_POST['store_id']);
	}
	public function get_items_select_list(){
		echo get_items_select_list(null,$_POST['store_id']);
	}
	public function get_tax_select_list(){
		echo get_tax_select_list(null,$_POST['store_id']);
	}
	/*Get warehouse select list*/
	public function get_warehouse_select_list(){
		echo get_warehouse_select_list(null,$_POST['store_id']);
	}
	//Print quotation Payment Receipt
	public function print_show_receipt($payment_id){
		if(!$this->permissions('quotation_add') && !$this->permissions('quotation_edit')){
			$this->show_access_denied_page();
		}
		$data=$this->data;
		$data['page_title']=$this->lang->line('payment_receipt');
		$data=array_merge($data,array('payment_id'=>$payment_id));
		$this->load->view('print-cust-payment-receipt',$data);
	}
	
	public function get_users_select_list(){
		echo get_users_select_list($this->session->userdata("role_id"),$_POST['store_id']);
	}
}
