<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends MY_Controller {
	public function __construct(){
		parent::__construct();
		$this->load_global();
		if($this->get_current_version_of_db()!=app_version()){ redirect(base_url('updates/update_db'),'refresh'); }
	}
	public function dashboard_values(){
		$this->load->model('dashboard_model');//Model
		$data=$this->dashboard_model->breadboard_values();//Model->Method
		echo json_encode($data);
	}

	public function index()
	{	
		$this->load->model('dashboard_model');//Model

		$data=array_merge($this->data,$this->dashboard_model->get_bar_chart(),$this->dashboard_model->get_pie_chart());
		if(is_admin()){
			$data = array_merge($data,$this->dashboard_model->get_subscription_chart());
		}
		$data['page_title']=$this->lang->line('dashboard');
		if(isset($_POST['store_id'])){
			$data['store_id'] =$_POST['store_id'];
		}
		if(!$this->permissions('dashboard_view')){
			$this->load->view('role/dashboard_empty',$data);
		}
		else{
			$this->load->view('dashboard',$data);
		}
		
	}
	public function get_storewise_details($from='All'){

			//$from= $this->input->get_post('from');
			if(is_user()){
				$this->db->where("id!=1");
			}
			$q1=$this->db->select("*")->get("db_store");
		        if($q1->num_rows()>0){
		          $i=1;
		          foreach ($q1->result() as $row){
		          	
		          	/*SALES TOTAL*/
		            if($from=='Today'){
		          		$this->db->where("sales_date > DATE_SUB(NOW(), INTERVAL 1 DAY)");
		          	}
		          	if($from=='Weekly'){
		          		$this->db->where("sales_date > DATE_SUB(NOW(), INTERVAL 1 WEEK)");
		          	}
		          	if($from=='Monthly'){
		          		$this->db->where("sales_date > DATE_SUB(NOW(), INTERVAL 1 MONTH)");
		          	}
		          	if($from=='Yearly'){
		          		$this->db->where("sales_date > DATE_SUB(NOW(), INTERVAL 1 YEAR)");
		          	}
		            $this->db->where("store_id",$row->id); 
		            $this->db->select("COALESCE(sum(grand_total),0) AS tot_sal_grand_total");
		            $this->db->from("db_sales");
		            $this->db->where("sales_status='Final'");
		            $sal_total=$this->db->get()->row()->tot_sal_grand_total;
		      		
		      		/*SALES DUE*/
		            if($from=='Today'){
		          		$this->db->where("sales_date > DATE_SUB(NOW(), INTERVAL 1 DAY)");
		          	}
		          	if($from=='Weekly'){
		          		$this->db->where("sales_date > DATE_SUB(NOW(), INTERVAL 1 WEEK)");
		          	}
		          	if($from=='Monthly'){
		          		$this->db->where("sales_date > DATE_SUB(NOW(), INTERVAL 1 MONTH)");
		          	}
		          	if($from=='Yearly'){
		          		$this->db->where("sales_date > DATE_SUB(NOW(), INTERVAL 1 YEAR)");
		          	}
		            $this->db->where("store_id",$row->id); 
		            $this->db->select("COALESCE(sum(grand_total),0)-COALESCE(sum(paid_amount),0) AS sales_due_total");
		            $this->db->from("db_sales");
		            $this->db->where("sales_status='Final'");
		            $sales_due_total=$this->db->get()->row()->sales_due_total;

		            /*EXPENSE */
		            if($from=='Today'){
		          		$this->db->where("expense_date > DATE_SUB(NOW(), INTERVAL 1 WEEK)");
		          	}
		          	if($from=='Weekly'){
		          		$this->db->where("expense_date > DATE_SUB(NOW(), INTERVAL 1 WEEK)");
		          	}
		          	if($from=='Monthly'){
		          		$this->db->where("expense_date > DATE_SUB(NOW(), INTERVAL 1 MONTH)");
		          	}
		          	if($from=='Yearly'){
		          		$this->db->where("expense_date > DATE_SUB(NOW(), INTERVAL 1 YEAR)");
		          	}
		            $this->db->where("store_id",$row->id); 
		            $this->db->select("COALESCE(SUM(expense_amt),0) AS exp_total");
		            $this->db->from("db_expense");
		            $exp_total=$this->db->get()->row()->exp_total;


		            echo "<tr>";
		            echo "<td>".$i++."</td>";
		            echo "<td>".$row->store_name."</td>";
		            echo "<td>".$this->store_wise_currency($row->id,store_number_format($sal_total))."</td>";
		            echo "<td>".$this->store_wise_currency($row->id,store_number_format($exp_total))."</td>";
		            echo "<td>".$this->store_wise_currency($row->id,store_number_format($sales_due_total))."</td>";
		            echo "</tr>";
		          }//foreach
		        }
		
	}
}
