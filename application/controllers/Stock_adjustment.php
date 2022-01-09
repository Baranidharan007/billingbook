<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Stock_adjustment extends MY_Controller {
	public function __construct(){
		parent::__construct();
		$this->load_global();
		$this->load->model('stock_adjustment_model','stock_adjustment');
	}

	public function index()
	{
		$this->permission_check('stock_adjustment_view');
		$data=$this->data;
		$data['page_title']=$this->lang->line('stock_adjustment_list');
		$this->load->view('stock_adjustment/stock_adjustment_list',$data);
	}
	
	public function add()
	{
		$this->permission_check('stock_adjustment_add');
		$data=$this->data;
		$data['page_title']=$this->lang->line('stock_adjustment');
		$this->load->view('stock_adjustment/stock_adjustment',$data);
	}

	public function stock_adjustment_save_and_update(){
		$this->form_validation->set_rules('adjustment_date', 'Stock Adjustment Date', 'trim|required');
		
		if ($this->form_validation->run() == TRUE) {
	    	$result = $this->stock_adjustment->verify_save_and_update();
	    	echo $result;
		} else {
			echo "Please Fill Compulsory(* marked) Fields.";
		}
	}
	
	public function update($id){
		$this->belong_to('db_stockadjustment',$id);
		$this->permission_check('stock_adjustment_edit');
		$data=$this->data;
		$data=array_merge($data,array('adjustment_id'=>$id));
		$data['page_title']=$this->lang->line('stock_adjustment');
		$this->load->view('stock_adjustment/stock_adjustment', $data);
	}
	
	

	public function ajax_list()
	{
		$list = $this->stock_adjustment->get_datatables();
		
		$data = array();
		$no = $_POST['start'];
		foreach ($list as $stock_adjustment) {
			
			$no++;
			$row = array();
			$row[] = '<input type="checkbox" name="checkbox[]" value='.$stock_adjustment->id.' class="checkbox column_checkbox" >';
			$row[] = show_date($stock_adjustment->adjustment_date);
			$row[] = $stock_adjustment->reference_no;
			$row[] = ucfirst($stock_adjustment->created_by);
					$str2 = '<div class="btn-group" title="View Account">
										<a class="btn btn-primary btn-o dropdown-toggle" data-toggle="dropdown" href="#">
											Action <span class="caret"></span>
										</a>
										<ul role="menu" class="dropdown-menu dropdown-light pull-right">';
											if($this->permissions('stock_adjustment_view'))
											$str2.='<li>
												<a title="View Invoice" href="'.base_url().'stock_adjustment/details/'.$stock_adjustment->id.'" ><i class="fa fa-fw fa-eye text-blue"></i>View Stock Adjustment
												</a>
											</li>';

											if($this->permissions('stock_adjustment_edit'))
											$str2.='<li>
												<a title="Update Record ?" href="'.base_url().'stock_adjustment/update/'.$stock_adjustment->id.'">
													<i class="fa fa-fw fa-edit text-blue"></i>Edit
												</a>
											</li>';

											if($this->permissions('stock_adjustment_delete'))
											$str2.='<li>
												<a style="cursor:pointer" title="Delete Record ?" onclick="delete_stock_adjustment(\''.$stock_adjustment->id.'\')">
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
						"recordsTotal" => $this->stock_adjustment->count_all(),
						"recordsFiltered" => $this->stock_adjustment->count_filtered(),
						"data" => $data,
				);
		//output to json format
		echo json_encode($output);
	}
	
	public function delete_stock_adjustment(){
		$this->permission_check_with_msg('stock_adjustment_delete');
		$id=$this->input->post('q_id');
		echo $this->stock_adjustment->delete_stock_adjustment($id);
	}
	public function multi_delete(){
		$this->permission_check_with_msg('stock_adjustment_delete');
		$ids=implode (",",$_POST['checkbox']);
		echo $this->stock_adjustment->delete_stock_adjustment($ids);
	}


	

	//Stock Adjustment invoice form
	public function details($id)
	{
		$this->belong_to('db_stockadjustment',$id);
		if(!$this->permissions('stock_adjustment_add') && !$this->permissions('stock_adjustment_edit')){
			$this->show_access_denied_page();
		}
		$data=$this->data;
		$data=array_merge($data,array('adjustment_id'=>$id));
		$data['page_title']=$this->lang->line('stock_adjustment_details');
		$this->load->view('stock_adjustment/stock-adjustment-invoice',$data);
	}
	
	


	public function return_row_with_data($rowcount,$item_id){
		echo $this->stock_adjustment->get_items_info($rowcount,$item_id);
	}
	public function return_stock_adjustment_list($adjustment_id){
		echo $this->stock_adjustment->return_stock_adjustment_list($adjustment_id);
	}


	
}
