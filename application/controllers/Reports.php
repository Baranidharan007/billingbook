<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reports extends MY_Controller {
	public function __construct(){
		parent::__construct();
		$this->load_global();
		$this->load->model('reports_model','reports');
	}
	
	
	//Supplier Items Report 
	public function supplier_items(){
		$this->permission_check('supplier_items_report');
		$data=$this->data;
		$data['page_title']=$this->lang->line('supplier_items_report');
		$this->load->view('report-supplier_items', $data);
	}
	public function show_supplier_items_report(){
		echo $this->reports->show_supplier_items_report();
	}
	
	//Sales Report 
	public function sales(){
		$this->permission_check('sales_report');
		$data=$this->data;
		$data['page_title']=$this->lang->line('sales_report');
		$this->load->view('report-sales', $data);
	}
	public function show_sales_report(){
		echo $this->reports->show_sales_report();
	}

	//Sales Return Report 
	public function sales_return(){
		$this->permission_check('sales_return_report');
		$data=$this->data;
		$data['page_title']=$this->lang->line('sales_return_report');
		$this->load->view('report-sales-return', $data);
	}
	public function show_sales_return_report(){
		echo $this->reports->show_sales_return_report();
	}

	//Purchase report
	public function purchase(){
		$this->permission_check('purchase_report');
		$data=$this->data;
		$data['page_title']=$this->lang->line('purchase_report');
		$this->load->view('report-purchase', $data);
	}
	public function show_purchase_report(){
		echo $this->reports->show_purchase_report();
	}

	//Purchase Return report
	public function purchase_return(){
		$this->permission_check('purchase_return_report');
		$data=$this->data;
		$data['page_title']=$this->lang->line('purchase_return_report');
		$this->load->view('report-purchase-return', $data);
	}
	public function show_purchase_return_report(){
		echo $this->reports->show_purchase_return_report();
	}

	//Expense report
	public function expense(){
		$this->permission_check('expense_report');
		$data=$this->data;
		$data['page_title']=$this->lang->line('expense_report');
		$this->load->view('report-expense', $data);
	}
	public function show_expense_report(){
		echo $this->reports->show_expense_report();
	}
	//Profit report
	public function profit_loss(){
		$this->permission_check('profit_report');
		$data=$this->data;
		$data['page_title']=$this->lang->line('profit_and_loss_report');
		$this->load->view('report-profit-loss', $data);
	}
	public function get_profit_by_item(){
		echo $this->reports->get_profit_by_item();
	}
	public function get_profit_by_invoice(){
		echo $this->reports->get_profit_by_invoice();
	}

	//Summary report
	public function stock(){
		$this->permission_check('stock_report');
		$data=$this->data;
		$data['page_title']=$this->lang->line('stock_report');
		$this->load->view('report-stock', $data);
	}
	/*Stock Report*/
	public function show_stock_report(){
		echo $this->reports->show_stock_report();
	}
	public function brand_wise_stock(){
		echo $this->reports->brand_wise_stock();
	}
	//Item Sales Report 
	public function item_sales(){
		$this->permission_check('item_sales_report');
		$data=$this->data;
		$data['page_title']=$this->lang->line('item_sales_report');
		$this->load->view('report-sales-item', $data);
	}
	public function show_item_sales_report(){
		echo $this->reports->show_item_sales_report();
	}
	
	//Purchase Payments report
	public function purchase_payments(){
		$this->permission_check('purchase_payments_report');
		$data=$this->data;
		$data['page_title']=$this->lang->line('purchase_payments_report');
		$this->load->view('report-purchase-payments', $data);
	}
	public function show_purchase_payments_report(){
		echo $this->reports->show_purchase_payments_report();
	}

	//Sales Payments report
	public function sales_payments(){
		$this->permission_check('sales_payments_report');
		$data=$this->data;
		$data['page_title']=$this->lang->line('sales_payments_report');
		$this->load->view('report-sales-payments', $data);
	}
	public function show_sales_payments_report(){
		echo $this->reports->show_sales_payments_report();
	}
	//Expired Items Report 
	public function expired_items(){
		$this->permission_check('expired_items_report');
		$data=$this->data;
		$data['page_title']=$this->lang->line('expired_items_report');
		$this->load->view('report-expired-items', $data);
	}
	public function show_expired_items_report(){
		echo $this->reports->show_expired_items_report();
	}
	public function get_profit_loss_report(){
		echo json_encode($this->reports->get_profit_loss_report());
	}


	//Item Sales Report 
	public function seller_points(){
		$this->permission_check('seller_points_report');
		$data=$this->data;
		$data['page_title']=$this->lang->line('seller_points_report');
		$this->load->view('report-seller-points', $data);
	}
	public function show_seller_points_report(){
		echo $this->reports->show_seller_points_report();
	}
	
	//Sales Tax Report 
	public function sales_tax(){
		$this->permission_check('sales_tax_report');
		$data=$this->data;
		$data['page_title']=$this->lang->line('sales_tax_report');
		$this->load->view('report-sales-tax', $data);
	}
	public function show_sales_tax_report(){
		echo $this->reports->show_sales_tax_report();
	}

	//purchase Tax Report 
	public function purchase_tax(){
		$this->permission_check('purchase_tax_report');
		$data=$this->data;
		$data['page_title']=$this->lang->line('purchase_tax_report');
		$this->load->view('report-purchase-tax', $data);
	}
	public function show_purchase_tax_report(){
		echo $this->reports->show_purchase_tax_report();
	}

	//GSTR-1 Report 
	public function gstr_1(){
		$this->permission_check('gstr_1_report');
		$data=$this->data;
		$data['page_title']=$this->lang->line('gstr_1_report');
		$this->load->view('gst/report-gstr-1', $data);
	}
	public function show_gstr_1_report(){
		echo $this->reports->show_gstr_1_report();
	}
	//GSTR-2 Report 
	public function gstr_2(){
		$this->permission_check('gstr_2_report');
		$data=$this->data;
		$data['page_title']=$this->lang->line('gstr_2_report');
		$this->load->view('gst/report-gstr-2', $data);
	}
	public function show_gstr_2_report(){
		echo $this->reports->show_gstr_2_report();
	}

	//Sales Report 
	public function customer_orders(){
		$this->permission_check('customer_orders_report');
		$data=$this->data;
		$data['page_title']=$this->lang->line('customer_orders');
		$this->load->view('report-customer-orders', $data);
	}
	public function show_customer_orders(){
		echo $this->reports->show_customer_orders();
	}
}

