<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Account_transactions extends MY_Controller {
	public function __construct(){
		parent::__construct();
		$this->load_global();
		$this->load->model('account_transactions_model','accounts');
	}
	
	public function get_sales_code($salespayment_id){
		if(empty($salespayment_id)) { return ''; }
		return $this->db->select("a.sales_code,a.id")
						->from("db_sales a")
						->from("db_salespayments b")
						->where("a.id=b.sales_id")
						->where("b.id=",$salespayment_id)->get()->row();
	}
	public function get_sales_return_code($returnpayment_id){
		if(empty($returnpayment_id)) { return ''; }
		 return $this->db->select("a.return_code,a.id")
						->from("db_salesreturn a")
						->from("db_salespaymentsreturn b")
						->where("a.id=b.return_id")
						->where("b.id=",$returnpayment_id)->get()->row();
	}
	public function get_purchase_code($purchasepayment_id){
		if(empty($purchasepayment_id)) { return ''; }
		return $this->db->select("a.purchase_code,a.id")
						->from("db_purchase a")
						->from("db_purchasepayments b")
						->where("a.id=b.purchase_id")
						->where("b.id=",$purchasepayment_id)->get()->row();
	}
	public function get_purchase_return_code($returnpayment_id){
		if(empty($returnpayment_id)) { return ''; }
		 return $this->db->select("a.return_code,a.id")
						->from("db_purchasereturn a")
						->from("db_purchasepaymentsreturn b")
						->where("a.id=b.return_id")
						->where("b.id=",$returnpayment_id)->get()->row();
	}
	public function get_expense_code($expense_id){
		if(empty($expense_id)) { return ''; }
		return $this->db->select("a.expense_code,a.id")
						->from("db_expense a")
						->where("a.id=",$expense_id)->get()->row();
	}
	public function ajax_list()
	{
		
		$list = $this->accounts->get_datatables();
		
		$data = array();
		$no = $_POST['start'];
		//Find previouse balance -> which follows transactions
		$prev_balance=0;
		$account_id = $this->input->post('account_id');
		$from_date = $this->input->post('from_date');
     	$from_date = system_fromatted_date($from_date);
     	if($from_date!='1970-01-01'){
     		$this->db->where("a.transaction_date>=",$from_date);
			$credit_amt=$this->db->select("coalesce(sum(credit_amt),0) as credit_amt")
									->where("credit_account_id",$account_id)
									->where("transaction_date<",$from_date)
									->get("ac_transactions a")->row()->credit_amt;
			$debit_amt=$this->db->select("coalesce(sum(debit_amt),0) as debit_amt")
									->where("debit_account_id",$account_id)
									->where("transaction_date<",$from_date)
									->get("ac_transactions a")->row()->debit_amt;
			$prev_balance = $credit_amt - $debit_amt;
     	}

		foreach ($list as $accounts) {
			$no++;
			$row = array();
			$row[] = show_date($accounts->transaction_date);

				$account_cr_dr = ($_POST['account_id']==$accounts->debit_account_id) ? 'Debit_entry' : 'Credit_entry';

				$description = ($account_cr_dr=='Debit_entry') ? ucwords(strtolower($accounts->transaction_type)) : ucwords(strtolower($accounts->transaction_type));
				$description = "<b>".$description."</b>";

				//CUSTOMER OR ACCOUNT
				$from_='';
				$to_='';
				if($accounts->transaction_type!='OPENING BALANCE PAID'){
					
					if(!empty($accounts->supplier_id)){
						if(!empty($accounts->ref_purchasepayments_id)){
							$query = $this->get_purchase_code($accounts->ref_purchasepayments_id);
							$purchase_code = $query->purchase_code;
							$purchase_id = $query->id;
							$from_ = "<a data-toggle='tooltip' title='View Purchase Payments' href='".base_url('purchase/invoice/').$purchase_id."'>".$purchase_code."</a>";
						}
						else if(!empty($accounts->ref_purchasepaymentsreturn_id)){
							$query = $this->get_purchase_return_code($accounts->ref_purchasepaymentsreturn_id);
							$return_code = $query->return_code;
							$return_id = $query->id;
							$from_ = "<a data-toggle='tooltip' title='View Purchase Return Payments' href='".base_url('purchase_return/invoice/').$return_id."'>".$return_code."</a>";
						}
					}
					else if(!empty($accounts->customer_id)){
						if(!empty($accounts->ref_salespayments_id)){
							$query = $this->get_sales_code($accounts->ref_salespayments_id);
							$sales_code = $query->sales_code;
							$sales_id = $query->id;
							$from_ = "<a data-toggle='tooltip' title='View Sales Payments' href='".base_url('sales/invoice/').$sales_id."'>".$sales_code."</a>";
						}
						else if(!empty($accounts->ref_salespaymentsreturn_id)){
							$query = $this->get_sales_return_code($accounts->ref_salespaymentsreturn_id);
							$return_code = $query->return_code;
							$return_id = $query->id;
							$from_ = "<a data-toggle='tooltip' title='View Sales Return Payments' href='".base_url('sales_return/invoice/').$return_id."'>".$return_code."</a>";
						}
					}
					else{
						$from_ = get_account_name($accounts->debit_account_id);
					}

					if(!empty($accounts->supplier_id)){
						if(!empty($accounts->ref_purchasepayments_id)){
							$query = $this->get_purchase_code($accounts->ref_purchasepayments_id);
							$purchase_code = $query->purchase_code;
							$purchase_id = $query->id;
							$to_ = "<a data-toggle='tooltip' title='View Purchase Payments' href='".base_url('purchase/invoice/').$purchase_id."'>".$purchase_code."</a>";
						}
						else if(!empty($accounts->ref_purchasepaymentsreturn_id)){
							$query = $this->get_purchase_return_code($accounts->ref_purchasepaymentsreturn_id);
							$return_code = $query->return_code;
							$return_id = $query->id;
							$to_ = "<a data-toggle='tooltip' title='View Purchase Return Payments' href='".base_url('purchase_return/invoice/').$return_id."'>".$return_code."</a>";
						}
					}
					else if(!empty($accounts->customer_id)){
						//It mean it has sales code
						if(!empty($accounts->ref_salespayments_id)){
							$query = $this->get_sales_code($accounts->ref_salespayments_id);
							$sales_code = $query->sales_code;
							$sales_id = $query->id;
							$to_ = "<a data-toggle='tooltip' title='View Sales Payments' href='".base_url('sales/invoice/').$sales_id."'>".$sales_code."</a>";
						}
						else if(!empty($accounts->ref_salespaymentsreturn_id)){
							$query = $this->get_sales_return_code($accounts->ref_salespaymentsreturn_id);
							$return_code = $query->return_code;
							$return_id = $query->id;
							$to_ = "<a data-toggle='tooltip' title='View Sales Return Payments' href='".base_url('sales_return/invoice/').$return_id."'>".$return_code."</a>";
						}
					}
					else if(!empty($accounts->ref_expense_id)){
							
								$query = $this->get_expense_code($accounts->ref_expense_id);
								$expense_code = $query->expense_code;
								$expense_id = $accounts->ref_expense_id;
								$to_ = "<a data-toggle='tooltip' title='View Expense Details' href='".base_url('expense/update/').$expense_id."'>".$expense_code."</a>";
							
					}
					else{
						$to_ = get_account_name($accounts->credit_account_id);
					}
					$description_ext = 
							($account_cr_dr=='Debit_entry') ? 
							'[To: '.$to_."]" 
							:
							 ((!empty($from_)) ? '[From: '.$from_.']' : '');

					$description = ($accounts->transaction_type!='OPENING BALANCE') ? $description."<br>".$description_ext : $description;
				}
			$row[] = $description;

			$row[] = ($account_cr_dr=='Debit_entry') ? store_number_format($accounts->debit_amt) : '';
			$row[] = ($account_cr_dr=='Credit_entry') ? store_number_format($accounts->credit_amt) : '';

			$balance = ($account_cr_dr=='Debit_entry') ? (0-$accounts->debit_amt) : ($accounts->credit_amt-0);
			$prev_balance += $balance;

			$row[] = store_number_format($prev_balance);

			$row[] = $accounts->note;			
			$row[] = ucfirst($accounts->created_by);			
					$link='';
					$title='';
					$entry_of='';
					$record_id='';

					if($accounts->ref_moneytransfer_id){
						$link = "money_transfer/update/".$accounts->ref_moneytransfer_id;
						$title = 'Edit Transfer Entry';
						$entry_of=1;
						$record_id=$accounts->ref_moneytransfer_id;
					}
					else if($accounts->ref_moneydeposits_id){
						$link = "money_deposit/update/".$accounts->ref_moneydeposits_id;
						$title = 'Edit Deposit Entry';
						$entry_of=2;
						$record_id=$accounts->ref_moneydeposits_id;
					}
					else if($accounts->ref_salespayments_id){
						$link = "";
						$title = '';
						$entry_of=3;
						$record_id=$accounts->ref_salespayments_id;
					}
					else if($accounts->ref_salespaymentsreturn_id){
						$link = "";
						$title = '';
						$entry_of=4;
						$record_id=$accounts->ref_salespaymentsreturn_id;
					}
					else if($accounts->ref_purchasepayments_id){
						$link = "";
						$title = '';
						$entry_of=5;
						$record_id=$accounts->ref_purchasepayments_id;
					}
					else if($accounts->ref_purchasepaymentsreturn_id){
						$link = "";
						$title = '';
						$entry_of=6;
						$record_id=$accounts->ref_purchasepaymentsreturn_id;
					}
					else if($accounts->ref_expense_id){
						$link = "";
						$title = '';
						$entry_of=7;
						$record_id=$accounts->ref_expense_id;
					}


				     $str2 = '<div class="btn-group" title="View Account">
										<a class="btn btn-primary btn-o dropdown-toggle" data-toggle="dropdown" href="#">
											Action <span class="caret"></span>
										</a>
										<ul role="menu" class="dropdown-menu dropdown-light pull-right">';


											/*if($this->permissions('accounts_edit'))
											$str2.='<li>
												<a data-toggle="tooltip" title="Edit Record ?" href="'.base_url().$link.'">
													<i class="fa fa-fw fa-edit text-blue"></i>'.$title.'
												</a>
											</li>';*/

											if($this->permissions('accounts_delete'))
											$str2.='<li>
												<a style="cursor:pointer" title="Delete Record ?" onclick="delete_transaction('.$record_id.','.$entry_of.')">
													<i class="fa fa-fw fa-trash text-red"></i>Delete
												</a>
											</li>
											
										</ul>
									</div>';			
			$row[] = ($accounts->transaction_type!='OPENING BALANCE') ? $str2 : '';

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
	
	public function delete_transaction(){
		$entry_of =$this->input->post('entry_of');
		
		$this->permission_check_with_msg('accounts_delete');
		$id=$this->input->post('q_id');

		if($entry_of==1){//transfer
			$this->load->model('money_transfer_model');
			echo $this->money_transfer_model->delete_money_transfer_from_table($id);
		}
		else if($entry_of==2){//deposit
			$this->load->model('money_deposit_model');
			echo $this->money_deposit_model->delete_money_deposit_from_table($id);
		}
		else if($entry_of==3){//sales payments
			$this->load->model('sales_model');
			echo $this->sales_model->delete_payment($id);
		}
		else if($entry_of==4){//sales return payments
			$this->load->model('sales_return_model');
			echo $this->sales_return_model->delete_payment($id);
		}
		else if($entry_of==5){//purchase payments
			$this->load->model('purchase_model');
			echo $this->purchase_model->delete_payment($id);
		}
		else if($entry_of==6){//purchase return payments
			$this->load->model('purchase_returns_model');
			echo $this->purchase_returns_model->delete_payment($id);
		}
		else if($entry_of==7){//purchase return payments
			$this->load->model('expense_model');
			echo $this->expense_model->delete_expenses_from_table($id);
		}
		
	}
	
	


}
