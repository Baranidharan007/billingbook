<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Stock_transfer extends MY_Controller {
	public function __construct(){
		parent::__construct();
		$this->load_global();
		$this->load->model('stock_transfer_model','stock_transfer');
	}

	public function view()
	{
		$this->permission_check('stock_transfer_view');
		$data=$this->data;
		$data['page_title']=$this->lang->line('stock_transfer_list');
		$this->load->view('warehouse/stock_transfer_list',$data);
	}
	public function add()
	{	
		$this->permission_check('stock_transfer_add');
		$data=$this->data;
		$data['page_title']=$this->lang->line('stock_transfer');
		$this->load->view('warehouse/stock_transfer',$data);
	}
	

	public function stock_save_and_update(){
		$this->form_validation->set_rules('transfer_date', 'Stock Date', 'trim|required');
		$this->form_validation->set_rules('warehouse_from', 'Warehouse From', 'trim|required');
		$this->form_validation->set_rules('warehouse_to', 'Warehouse To', 'trim|required');
		
		if ($this->form_validation->run() == TRUE) {
	    	$result = $this->stock_transfer->verify_save_and_update();
	    	echo $result;
		} else {
			echo "Please Fill Compulsory(* marked) Fields.";
		}
	}
	
	
	public function update($id){
		$this->belong_to('db_stocktransfer',$id);
		$this->permission_check('stock_transfer_edit');
		$data=$this->data;
		$data=array_merge($data,array('stocktransfer_id'=>$id));
		$data['page_title']=$this->lang->line('stock_transfer');
		$this->load->view('warehouse/stock_transfer', $data);
	}
	

	public function ajax_list()
	{
		$list = $this->stock_transfer->get_datatables();
		
		$data = array();
		$no = $_POST['start'];
		foreach ($list as $stock_transfer) {
			
			$no++;
			$row = array();
			$row[] = '<input type="checkbox" name="checkbox[]" value='.$stock_transfer->id.' class="checkbox column_checkbox" >';
			
			$row[] = show_date($stock_transfer->transfer_date);
			$row[] = get_warehouse_name($stock_transfer->warehouse_from);
			$row[] = get_warehouse_name($stock_transfer->warehouse_to);
					
					$str='<i>';
					$str.="Items: ".get_total_stocktranfer_items($stock_transfer->id);
					$str.="<br>Quantity: ".get_total_stocktranfer_items_qty($stock_transfer->id);
					$str.='</i>';

			$row[] = $str;
			$row[] = $stock_transfer->note;
			
			$row[] = ucfirst($stock_transfer->created_by);

					
					
					$str2 = '<div class="btn-group" title="View Account">
										<a class="btn btn-primary btn-o dropdown-toggle" data-toggle="dropdown" href="#">
											Action <span class="caret"></span>
										</a>
										<ul role="menu" class="dropdown-menu dropdown-light pull-right">';
											if($this->permissions('stock_transfer_view'))
											$str2.='<li>
												<a title="View Invoice" href="'.base_url('stock_transfer/info/'.$stock_transfer->id).'" >
													<i class="fa fa-fw fa-eye text-blue"></i>View Transfer
												</a>
											</li>';

											if($this->permissions('stock_transfer_edit'))
											$str2.='<li>
												<a title="Update Record ?" href="'.base_url('stock_transfer/update/').$stock_transfer->id.'">
													<i class="fa fa-fw fa-edit text-blue"></i>Edit
												</a>
											</li>';

										
										

											if($this->permissions('stock_transfer_delete'))
											$str2.='<li>
												<a style="cursor:pointer" title="Delete Record ?" onclick="delete_stock(\''.$stock_transfer->id.'\')">
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
						"recordsTotal" => $this->stock_transfer->count_all(),
						"recordsFiltered" => $this->stock_transfer->count_filtered(),
						"data" => $data,
				);
		//output to json format
		echo json_encode($output);
	}

	public function info($id)
	{	
		$this->belong_to('db_stocktransfer',$id);
		if(!$this->permissions('stock_transfer_add') && !$this->permissions('stock_transfer_edit')){
			$this->show_access_denied_page();
		}
		$data=$this->data;
		$data=array_merge($data,array('stocktransfer_id'=>$id));
		$data['page_title']=$this->lang->line('stock_transfer_details');
		$this->load->view('warehouse/stock_transfer_info',$data);
	}

	public function delete_stock(){
		$this->permission_check_with_msg('stock_transfer_delete');
		$id=$this->input->post('q_id');
		echo $this->stock_transfer->delete_stock($id);
	}
	public function multi_delete(){
		$this->permission_check_with_msg('stock_transfer_delete');
		$ids=implode (",",$_POST['checkbox']);
		echo $this->stock_transfer->delete_stock($ids);
	}


	//Table ajax code
	public function search_item(){
		$q=$this->input->get('q');
		$result=$this->stock_transfer->search_item($q);
		echo $result;
	}
	public function find_item_details(){
		$id=$this->input->post('id');
		
		$result=$this->stock_transfer->find_item_details($id);
		echo $result;
	}
	
	public function return_row_with_data($rowcount,$item_id){
		echo $this->stock_transfer->get_items_info($rowcount,$item_id);
	}
	public function return_stock_list($stock_id){
		echo $this->stock_transfer->return_stock_list($stock_id);
	}
	
	public function print_invoice($transfer_id)
	{
		$this->belong_to('db_stocktransfer',$transfer_id);
		if(!$this->permissions('stock_transfer_add') && !$this->permissions('stock_transfer_edit')){
			$this->show_access_denied_page();
		}
		$data=$this->data;
		$data=array_merge($data,array('stocktransfer_id'=>$transfer_id));
		$data['page_title']=$this->lang->line('stock_transfer');
		
		$this->load->view('warehouse/print-stock-transfer-invoice',$data);
	
	}

	public function pdf($stocktransfer_id){
		$this->belong_to('db_stocktransfer',$stocktransfer_id);
		if(!$this->permissions('stock_transfer_add') && !$this->permissions('stock_transfer_edit')){
			$this->show_access_denied_page();
		}
		
		$data=$this->data;
		$data['page_title']=$this->lang->line('stock_transfer');
        $data=array_merge($data,array('stocktransfer_id'=>$stocktransfer_id));
        $this->load->view('warehouse/print-stock-transfer-invoice',$data);
       

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
        $this->dompdf->stream("stock_transfer_$stocktransfer_id", array("Attachment"=>0));
	}
}
