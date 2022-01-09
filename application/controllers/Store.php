<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Store extends MY_Controller {
	public function __construct(){
		parent::__construct();
		$this->load_global();
		$this->load->model('store_model','store');
	}

	public function add(){
		$this->permission_check('store_add');
		$data=array_merge($this->data,$this->store->store_making_codes());
		$data['page_title']=$this->lang->line('store');
		$this->load->view('store', $data);
	}
	public function newstore(){
		$result=$this->store->verify_and_save();
		echo $result;	
	}
	
	
	
	public function view(){
		$this->permission_check('store_view');
		$data=array_merge($this->data);
		$data['page_title']=$this->lang->line('store_list');
		$this->load->view('store/store_list', $data);
	}

	public function ajax_list()
	{
		$list = $this->store->get_datatables();
		
		$data = array();
		$no = $_POST['start'];
		foreach ($list as $store) {
			$no++;
			$row = array();
			$disable = ($store->id==1) ? 'disabled' : '';

			$row[] = ($store->id==1) ? '<span data-toggle="tooltip" title="Resticted" class="text-danger fa fa-fw fa-ban"></span>' : '<input type="checkbox" name="checkbox[]" '.$disable.' value='.$store->id.' class="checkbox column_checkbox" >';

			$row[] = $store->store_code;
			$row[] = $store->store_name;
			$row[] = $store->mobile;
			$row[] = $store->address;

					if($store->id==1){ 
						$str= "  <span  class='label label-default' disabled='disabled' style='cursor:disabled'>Restricted</span>"; }
			 		else if($store->status==1){ 
			 			$str= "<span onclick='update_status(".$store->id.",0)' id='span_".$store->id."'  class='label label-success' style='cursor:pointer'>Active </span>";}
					else{ 
						$str = "<span onclick='update_status(".$store->id.",1)' id='span_".$store->id."'  class='label label-danger' style='cursor:pointer'> Inactive </span>";
					}
			$row[] = $str;			
					$str2 = '<div class="btn-group" title="View Account">
										<a class="btn btn-primary btn-o dropdown-toggle" data-toggle="dropdown" href="#">
											Action <span class="caret"></span>
										</a>
										<ul role="menu" class="dropdown-menu dropdown-light pull-right">';

											if($this->permissions('store_edit'))
											$str2.='<li>
												<a title="Edit Record ?" href="'.base_url('store_profile/update/'.$store->id).'">
													<i class="fa fa-fw fa-edit text-blue"></i>Edit
												</a>
											</li>';

											if($this->permissions('store_delete') && $store->id!=1)
											$str2.='<li>
												<a style="cursor:pointer" title="Delete Record ?" onclick="delete_store('.$store->id.')">
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
						"recordsTotal" => $this->store->count_all(),
						"recordsFiltered" => $this->store->count_filtered(),
						"data" => $data,
				);
		//output to json format
		echo json_encode($output);
	}

	public function update_status(){
		$this->permission_check_with_msg('store_edit');
		$id=$this->input->post('id');
		$status=$this->input->post('status');

		
		$result=$this->store->update_status($id,$status);
		return $result;
	}
	
	public function delete_store(){
		$this->permission_check_with_msg('store_delete');
		$id=$this->input->post('q_id');
		return $this->store->delete_store_from_table($id);
	}
	public function multi_delete(){
		$this->permission_check_with_msg('store_delete');
		$ids=implode (",",$_POST['checkbox']);
		return $this->store->delete_store_from_table($ids);
	}

}

