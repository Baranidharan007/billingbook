<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Variants extends MY_Controller {
	public function __construct(){
		parent::__construct();
		$this->load_global();
		$this->load->model('variants_model','variant');
	}

	public function add(){
		$this->permission_check('variant_add');
		$data=$this->data;
		$data['page_title']=$this->lang->line('variant');
		$this->load->view('variants/variants', $data);
	}
	public function newvariant(){
		$this->form_validation->set_rules('variant', 'Variant', 'trim|required');
	

		if ($this->form_validation->run() == TRUE) {
			
			$this->load->model('variants_model');
			$result=$this->variants_model->verify_and_save();
			echo $result;
		} else {
			echo "Please Enter Variant name.";
		}
	}
	public function update($id){
		$this->belong_to('db_variants',$id);
		$this->permission_check('variant_edit');
		$data=$this->data;

		$this->load->model('variants_model');
		$result=$this->variants_model->get_details($id,$data);
		$data=array_merge($data,$result);
		$data['page_title']=$this->lang->line('variant');
		$this->load->view('variants/variants', $data);
	}
	public function update_variant(){
		$this->form_validation->set_rules('variant', 'Variant', 'trim|required');
		$this->form_validation->set_rules('q_id', '', 'trim|required');

		if ($this->form_validation->run() == TRUE) {
			$this->load->model('variants_model');
			$result=$this->variants_model->update_variant();
			echo $result;
		} else {
			echo "Please Enter Variant name.";
		}
	}
	public function view(){
		$this->permission_check('variant_view');
		$data=$this->data;
		$data['page_title']=$this->lang->line('variants_list');
		$this->load->view('variants/variants_list', $data);
	}

	public function ajax_list()
	{
		$list = $this->variant->get_datatables();
		
		$data = array();
		$no = $_POST['start'];
		foreach ($list as $variant) {
			$no++;
			$row = array();
			$row[] = '<input type="checkbox" name="checkbox[]" value='.$variant->id.' class="checkbox column_checkbox" >';
			
			$row[] = $variant->variant_name;
			$row[] = $variant->description;

			 		if($variant->status==1){ 
			 			$str= "<span onclick='update_status(".$variant->id.",0)' id='span_".$variant->id."'  class='label label-success' style='cursor:pointer'>Active </span>";}
					else{ 
						$str = "<span onclick='update_status(".$variant->id.",1)' id='span_".$variant->id."'  class='label label-danger' style='cursor:pointer'> Inactive </span>";
					}
			$row[] = $str;			
					$str2 = '<div class="btn-group" title="View Account">
										<a class="btn btn-primary btn-o dropdown-toggle" data-toggle="dropdown" href="#">
											Action <span class="caret"></span>
										</a>
										<ul role="menu" class="dropdown-menu dropdown-light pull-right">';

											if($this->permissions('variant_edit'))
											$str2.='<li>
												<a title="Edit Record ?" href="'.base_url().'variants/update/'.$variant->id.'">
													<i class="fa fa-fw fa-edit text-blue"></i>Edit
												</a>
											</li>';

											if($this->permissions('variant_delete'))
											$str2.='<li>
												<a style="cursor:pointer" title="Delete Record ?" onclick="delete_variant('.$variant->id.')">
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
						"recordsTotal" => $this->variant->count_all(),
						"recordsFiltered" => $this->variant->count_filtered(),
						"data" => $data,
				);
		//output to json format
		echo json_encode($output);
	}

	public function update_status(){
		$this->permission_check_with_msg('variant_edit');
		$id=$this->input->post('id');
		$status=$this->input->post('status');

		$this->load->model('variants_model');
		$result=$this->variants_model->update_status($id,$status);
		return $result;
	}
	
	public function delete_variant(){
		$this->permission_check_with_msg('variant_delete');
		$id=$this->input->post('q_id');
		return $this->variant->delete_variants_from_table($id);
	}
	public function multi_delete(){
		$this->permission_check_with_msg('variant_delete');
		$ids=implode (",",$_POST['checkbox']);
		return $this->variant->delete_variants_from_table($ids);
	}

}

