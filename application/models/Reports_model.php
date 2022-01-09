<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reports_model extends CI_Model {

	public function show_supplier_items_report(){
		extract($_POST);

		if(!empty($store_id)){
			$this->db->where("store_id",$store_id);
		}
		if($item_id!=''){
			$this->db->where("id=$item_id");
		}
		$q1 = $this->db->select("*")->from("db_items");
		
		$q1=$this->db->get();
		if($q1->num_rows()>0){
			$i=0;
			$tot_grand_total=0;
			$tot_paid_amount=0;
			$tot_due_amount=0;
			foreach ($q1->result() as $res1) {
				

				$this->db->select("a.unit_total_cost,b.store_id,b.warehouse_id, b.id,a.item_id,b.supplier_id,b.purchase_code,b.purchase_date");
				$this->db->from("db_purchaseitems a");
				$this->db->where("a.item_id",$res1->id);
				$this->db->from("db_purchase b");
				$this->db->where("a.purchase_id=b.id");
				

				if($supplier_id!=''){
					$this->db->where("b.supplier_id=$supplier_id");
				}
				if(!empty($store_id)){
					$this->db->where("b.store_id",$store_id);
				}
				$this->db->order_by("a.id","desc");
				//$this->db->limit(1);

				$q2=$this->db->get();
				if($q2->num_rows()>0){
					foreach ($q2->result() as $res2){
						$supplier_name = $this->db->select("supplier_name")->from("db_suppliers")->where("id",$res2->supplier_id)->get()->row()->supplier_name;
						
						$q3 = $this->db->select("*")->from("db_items")->where("id",$res2->item_id)->get()->row();
						$item_code = $q3->item_code;
						$item_name = $q3->item_name;

						echo "<tr>";
						echo "<td>".++$i."</td>";
						
						if(store_module() && is_admin()){
							echo "<td>".get_store_name($res2->store_id)."</td>";	
						}
						if(warehouse_module() && warehouse_count()>0){
							echo "<td>".get_warehouse_name($res2->warehouse_id)."</td>";	
						}
						echo "<td><a title='View Invoice' href='".base_url("purchase/invoice/$res2->id")."'>".$res2->purchase_code."</a></td>";
						echo "<td>".show_date($res2->purchase_date)."</td>";
						echo "<td>".$supplier_name."</td>";
						echo "<td>".$item_code."</td>";
						echo "<td>".$item_name."</td>";
						echo "<td class='text-right'>".store_number_format($res2->unit_total_cost)."</td>";
						echo "</tr>";

					}
				}


			}

		}
		else{
			echo "<tr>";
			echo "<td class='text-center text-danger' colspan=8>No Records Found</td>";
			echo "</tr>";
		}
		
	    exit;
	}

	public function show_sales_report(){
		extract($_POST);

		/*$from_date=date("Y-m-d",strtotime($from_date));
		$to_date=date("Y-m-d",strtotime($to_date));*/
		
		$from_date = system_fromatted_date($from_date);
		$to_date = system_fromatted_date($to_date);


		if(warehouse_module() && warehouse_count()>0 && !empty($warehouse_id)){
				$this->db->where("a.warehouse_id",$warehouse_id);
		}

		$this->db->select("a.id,a.warehouse_id,a.sales_code,a.sales_date,b.customer_name,b.customer_code,a.grand_total,a.paid_amount,a.store_id");
	    
		if($customer_id!=''){
			
			$this->db->where("a.customer_id=$customer_id");
		}
		if($view_all=="no"){
			$this->db->where("(a.sales_date>='$from_date' and a.sales_date<='$to_date')");
		}
		$this->db->where("b.`id`= a.`customer_id`");
		$this->db->from("db_sales as a");
		$this->db->where("a.`sales_status`= 'Final'");

		if(!empty($store_id)){
			$this->db->where("a.store_id",$store_id);
		}
		
		$this->db->from("db_customers as b");
		if($show_account_receivable==1){
			$this->db->where("a.grand_total!=a.paid_amount");
		}
		
		//echo $this->db->get_compiled_select();exit();
		
		$q1=$this->db->get();
		if($q1->num_rows()>0){
			$i=0;
			$tot_grand_total=0;
			$tot_paid_amount=0;
			$tot_due_amount=0;
			foreach ($q1->result() as $res1) {
				echo "<tr>";
				echo "<td>".++$i."</td>";
				if(store_module() && is_admin()){
					echo "<td>".get_store_name($res1->store_id)."</td>";	
				}
				if(warehouse_module() && warehouse_count()>0){
					echo "<td>".get_warehouse_name($res1->warehouse_id)."</td>";	
				}
				if($store_id==get_current_store_id()){
				echo "<td><a title='View Invoice' href='".base_url("sales/invoice/$res1->id")."'>".$res1->sales_code."</a></td>";
				}
				else{
				echo "<td>".$res1->sales_code."</td>";	
				}
				echo "<td>".show_date($res1->sales_date)."</td>";
				echo "<td>".$res1->customer_code."</td>";
				echo "<td>".$res1->customer_name."</td>";
				echo "<td class='text-right'>".store_number_format($res1->grand_total)."</td>";
				echo "<td class='text-right'>".store_number_format($res1->paid_amount)."</td>";
				echo "<td class='text-right'>".store_number_format(($res1->grand_total-$res1->paid_amount))."</td>";
				echo "</tr>";
				$tot_grand_total+=$res1->grand_total;
				$tot_paid_amount+=$res1->paid_amount;
				$tot_due_amount+=($res1->grand_total-$res1->paid_amount);

			}

			$total_columns_count=5;
			if(store_module() && is_admin()){
				$total_columns_count ++;
			}
			if(warehouse_module() && warehouse_count()>0){
				$total_columns_count ++;
			}
			echo "<tr>
					  <td class='text-right text-bold' colspan='$total_columns_count'><b>Total :</b></td>
					  <td class='text-right text-bold'>".store_number_format($tot_grand_total)."</td>
					  <td class='text-right text-bold'>".store_number_format($tot_paid_amount)."</td>
					  <td class='text-right text-bold'>".store_number_format($tot_due_amount)."</td>
				  </tr>";
		}
		else{
			$total_columns_count=8;
			if(store_module() && is_admin()){
				$total_columns_count ++;
			}
			if(warehouse_module() && warehouse_count()>0){
				$total_columns_count ++;
			}
			echo "<tr>";
			echo "<td class='text-center text-danger' colspan='$total_columns_count'>No Records Found</td>";
			echo "</tr>";
		}
		
	    exit;
	}

	public function show_sales_return_report(){
		extract($_POST);

		/*$from_date=date("Y-m-d",strtotime($from_date));
		$to_date=date("Y-m-d",strtotime($to_date));*/
		
		$from_date = system_fromatted_date($from_date);
		$to_date = system_fromatted_date($to_date);

		if(warehouse_module() && warehouse_count()>0 && !empty($warehouse_id)){
			$this->db->where("a.warehouse_id",$warehouse_id);
		}

		$this->db->select("a.id,a.warehouse_id,a.return_code,a.return_date,b.customer_name,b.customer_code,a.grand_total,a.paid_amount,a.store_id");
	    
		if($customer_id!=''){
			
			$this->db->where("a.customer_id=$customer_id");
		}
		if($view_all=="no"){
			$this->db->where("(a.return_date>='$from_date' and a.return_date<='$to_date')");
		}
		$this->db->where("b.`id`= a.`customer_id`");
		$this->db->from("db_salesreturn as a");
		$this->db->from("db_customers as b");
		$this->db->select("CASE WHEN c.sales_code IS NULL THEN '' ELSE c.sales_code END AS sales_code");
		$this->db->join('db_sales as c','c.id=a.sales_id','left');
		
		
		if(!empty($store_id)){
			$this->db->where("a.store_id",$store_id);
		}
		//echo $this->db->get_compiled_select();exit();
		
		$q1=$this->db->get();
		if($q1->num_rows()>0){
			$i=0;
			$tot_grand_total=0;
			$tot_paid_amount=0;
			$tot_due_amount=0;
			foreach ($q1->result() as $res1) {
				echo "<tr>";
				echo "<td>".++$i."</td>";
				if(store_module() && is_admin()){
					echo "<td>".get_store_name($res1->store_id)."</td>";	
				}
				if(warehouse_module() && warehouse_count()>0){
					echo "<td>".get_warehouse_name($res1->warehouse_id)."</td>";	
				}
				if($store_id==get_current_store_id()){
				echo "<td><a title='View Invoice' href='".base_url("sales_return/invoice/$res1->id")."'>".$res1->return_code."</a></td>";
				}
				else{
				echo "<td>".$res1->return_code."</td>";	
				}

				
				echo "<td>".show_date($res1->return_date)."</td>";
				
				echo (!empty($res1->sales_code)) ? "<td><a title='Return Raised Against this Invoice' href='".base_url("sales/invoice/$res1->id")."'>".$res1->sales_code."</a></td>" : '<td>-NA-</td>';
				echo "<td>".$res1->customer_name."</td>";
				echo "<td class='text-right'>".store_number_format($res1->grand_total)."</td>";
				echo "<td class='text-right'>".store_number_format($res1->paid_amount)."</td>";
				echo "<td class='text-right'>".store_number_format(($res1->grand_total-$res1->paid_amount))."</td>";
				echo "</tr>";
				$tot_grand_total+=$res1->grand_total;
				$tot_paid_amount+=$res1->paid_amount;
				$tot_due_amount+=($res1->grand_total-$res1->paid_amount);

			}

			$total_columns_count=5;
			if(store_module() && is_admin()){
				$total_columns_count ++;
			}
			if(warehouse_module() && warehouse_count()>0){
				$total_columns_count ++;
			}
			echo "<tr>
					  <td class='text-right text-bold' colspan='$total_columns_count'><b>Total :</b></td>
					  <td class='text-right text-bold'>".store_number_format($tot_grand_total)."</td>
					  <td class='text-right text-bold'>".store_number_format($tot_paid_amount)."</td>
					  <td class='text-right text-bold'>".store_number_format($tot_due_amount)."</td>
				  </tr>";
		}
		else{
			$total_columns_count=8;
			if(store_module() && is_admin()){
				$total_columns_count ++;
			}
			if(warehouse_module() && warehouse_count()>0){
				$total_columns_count ++;
			}
			echo "<tr>";
			echo "<td class='text-center text-danger' colspan='$total_columns_count'>No Records Found</td>";
			echo "</tr>";
		}
		
	    exit;
	}

	public function show_purchase_report(){
		extract($_POST);
		
		/*$from_date=date("Y-m-d",strtotime($from_date));
		$to_date=date("Y-m-d",strtotime($to_date));*/
		
		$from_date = system_fromatted_date($from_date);
		$to_date = system_fromatted_date($to_date);

		if(warehouse_module() && warehouse_count()>0 && !empty($warehouse_id)){
			$this->db->where("a.warehouse_id",$warehouse_id);
		}

		$this->db->select("a.id,a.warehouse_id,a.purchase_code,a.purchase_date,b.supplier_name,b.supplier_code,a.grand_total,a.paid_amount,a.store_id");
	    
		if($supplier_id!=''){
			$this->db->where("a.supplier_id=$supplier_id");
		}
		if($view_all=="no"){
			$this->db->where("(a.purchase_date>='$from_date' and a.purchase_date<='$to_date')");
		}
		$this->db->where("b.`id`= a.`supplier_id`");
		$this->db->from("db_purchase as a");
		$this->db->where("a.`purchase_status`= 'Received'");
		$this->db->from("db_suppliers as b");
		
		
		if(!empty($store_id)){
			$this->db->where("a.store_id",$store_id);
		}
		if($show_account_payble==1){
			$this->db->where("a.grand_total!=a.paid_amount");
		}
		//echo $this->db->get_compiled_select();
		
		$q1=$this->db->get();
		if($q1->num_rows()>0){
			$i=0;
			$tot_grand_total=0;
			$tot_paid_amount=0;
			$tot_due_amount=0;
			foreach ($q1->result() as $res1) {
				echo "<tr>";
				echo "<td>".++$i."</td>";
				if(store_module() && is_admin()){
					echo "<td>".get_store_name($res1->store_id)."</td>";	
				}
				if(warehouse_module() && warehouse_count()>0){
					echo "<td>".get_warehouse_name($res1->warehouse_id)."</td>";	
				}
				if($store_id==get_current_store_id()){
				echo "<td><a title='View Invoice' href='".base_url("purchase/invoice/$res1->id")."'>".$res1->purchase_code."</a></td>";
				}
				else{
				echo "<td>".$res1->purchase_code."</td>";	
				}

				
				echo "<td>".show_date($res1->purchase_date)."</td>";
				echo "<td>".$res1->supplier_code."</td>";
				echo "<td>".$res1->supplier_name."</td>";
				echo "<td class='text-right'>".store_number_format($res1->grand_total)."</td>";
				echo "<td class='text-right'>".store_number_format($res1->paid_amount)."</td>";
				echo "<td class='text-right'>".store_number_format(($res1->grand_total-$res1->paid_amount))."</td>";
				echo "</tr>";
				$tot_grand_total+=$res1->grand_total;
				$tot_paid_amount+=$res1->paid_amount;
				$tot_due_amount+=($res1->grand_total-$res1->paid_amount);

			}
			$total_columns_count=5;
			if(store_module() && is_admin()){
				$total_columns_count ++;
			}
			if(warehouse_module() && warehouse_count()>0){
				$total_columns_count ++;
			}
			echo "<tr>
					  <td class='text-right text-bold' colspan='$total_columns_count'><b>Total :</b></td>
					  <td class='text-right text-bold'>".store_number_format($tot_grand_total)."</td>
					  <td class='text-right text-bold'>".store_number_format($tot_paid_amount)."</td>
					  <td class='text-right text-bold'>".store_number_format($tot_due_amount)."</td>
				  </tr>";
		}
		else{
			$total_columns_count=8;
			if(store_module() && is_admin()){
				$total_columns_count ++;
			}
			if(warehouse_module() && warehouse_count()>0){
				$total_columns_count ++;
			}
			echo "<tr>";
			echo "<td class='text-center text-danger' colspan='$total_columns_count'>No Records Found</td>";
			echo "</tr>";
		}
		
	    exit;
	}

	public function show_purchase_return_report(){
		extract($_POST);
		
		/*$from_date=date("Y-m-d",strtotime($from_date));
		$to_date=date("Y-m-d",strtotime($to_date));*/
		
		$from_date = system_fromatted_date($from_date);
		$to_date = system_fromatted_date($to_date);

		if(warehouse_module() && warehouse_count()>0 && !empty($warehouse_id)){
			$this->db->where("a.warehouse_id",$warehouse_id);
		}
		
		$this->db->select("a.id,a.warehouse_id,a.return_code,a.return_date,b.supplier_name,a.grand_total,a.paid_amount,a.store_id");
	    
		if($supplier_id!=''){
			$this->db->where("a.supplier_id=$supplier_id");
		}
		if($view_all=="no"){
			$this->db->where("(a.return_date>='$from_date' and a.return_date<='$to_date')");
		}
		$this->db->where("b.`id`= a.`supplier_id`");
		$this->db->from("db_purchasereturn as a");
		$this->db->from("db_suppliers as b");
		$this->db->select("CASE WHEN c.purchase_code IS NULL THEN '' ELSE c.purchase_code END AS purchase_code");
		$this->db->join('db_purchase as c','c.id=a.purchase_id','left');

		
		
		if(!empty($store_id)){
			$this->db->where("a.store_id",$store_id);
		}
		//echo $this->db->get_compiled_select();
		
		$q1=$this->db->get();
		if($q1->num_rows()>0){
			$i=0;
			$tot_grand_total=0;
			$tot_paid_amount=0;
			$tot_due_amount=0;
			foreach ($q1->result() as $res1) {
				echo "<tr>";
				echo "<td>".++$i."</td>";
				if(store_module() && is_admin()){
					echo "<td>".get_store_name($res1->store_id)."</td>";	
				}
				if(warehouse_module() && warehouse_count()>0){
					echo "<td>".get_warehouse_name($res1->warehouse_id)."</td>";	
				}
				if($store_id==get_current_store_id()){
				echo "<td><a title='View Invoice' href='".base_url("purchase_return/invoice/$res1->id")."'>".$res1->return_code."</a></td>";
				}
				else{
				echo "<td>".$res1->return_code."</td>";	
				}

				
				echo "<td>".show_date($res1->return_date)."</td>";
				echo (!empty($res1->purchase_code)) ? "<td><a title='Return Raised Against this Invoice' href='".base_url("purchase/invoice/$res1->id")."'>".$res1->purchase_code."</a></td>" : '<td>-NA-</td>';
				
				echo "<td>".$res1->supplier_name."</td>";
				echo "<td class='text-right'>".store_number_format($res1->grand_total)."</td>";
				echo "<td class='text-right'>".store_number_format($res1->paid_amount)."</td>";
				echo "<td class='text-right'>".store_number_format(($res1->grand_total-$res1->paid_amount))."</td>";
				echo "</tr>";
				$tot_grand_total+=$res1->grand_total;
				$tot_paid_amount+=$res1->paid_amount;
				$tot_due_amount+=($res1->grand_total-$res1->paid_amount);

			}
			$total_columns_count=5;
			if(store_module() && is_admin()){
				$total_columns_count ++;
			}
			if(warehouse_module() && warehouse_count()>0){
				$total_columns_count ++;
			}
			echo "<tr>
					  <td class='text-right text-bold' colspan='$total_columns_count'><b>Total :</b></td>
					  <td class='text-right text-bold'>".store_number_format($tot_grand_total)."</td>
					  <td class='text-right text-bold'>".store_number_format($tot_paid_amount)."</td>
					  <td class='text-right text-bold'>".store_number_format($tot_due_amount)."</td>
				  </tr>";
		}
		else{
			$total_columns_count=8;
			if(store_module() && is_admin()){
				$total_columns_count ++;
			}
			if(warehouse_module() && warehouse_count()>0){
				$total_columns_count ++;
			}
			echo "<tr>";
			echo "<td class='text-center text-danger' colspan='$total_columns_count'>No Records Found</td>";
			echo "</tr>";
		}
		
	    exit;
	}
	
	public function show_expense_report(){
		extract($_POST);
		/*$from_date=date("Y-m-d",strtotime($from_date));
		$to_date=date("Y-m-d",strtotime($to_date));*/

		$from_date = system_fromatted_date($from_date);
		$to_date = system_fromatted_date($to_date);

		/*$q1=$this->db->query("SELECT a.*,b.category_name from db_expense as a,db_expense_category as b where b.id=a.category_id and a.expense_date>='$from_date' and expense_date<='$to_date'");*/
		
		$this->db->select("a.*,b.category_name");
	    
		if($category_id!=''){
			$this->db->where("a.category_id=$category_id");
		}
		if($view_all=="no"){
			$this->db->where("(a.expense_date>='$from_date' and a.expense_date<='$to_date')");
		}
		$this->db->where("b.`id`= a.`category_id`");
		$this->db->from("db_expense as a");
		$this->db->from("db_expense_category as b");
		if(!empty($store_id)){
			$this->db->where("a.store_id",$store_id);
		}
		//echo $this->db->get_compiled_select();
		
		$q1=$this->db->get();
		
		if($q1->num_rows()>0){
			$i=0;
			$tot_expense_amt=0;
			foreach ($q1->result() as $res1) {
				echo "<tr>";
				echo "<td>".++$i."</td>";
				if(store_module() && is_admin()){
					echo "<td>".get_store_name($res1->store_id)."</td>";	
				}
				echo "<td>".$res1->expense_code."</td>";
				echo "<td>".show_date($res1->expense_date)."</td>";
				echo "<td>".$res1->category_name."</td>";
				echo "<td>".$res1->reference_no."</td>";
				echo "<td>".$res1->expense_for."</td>";
				echo "<td class='text-right'>".store_number_format($res1->expense_amt)."</td>";
				echo "<td>".$res1->note."</td>";
				echo "<td>".ucfirst($res1->created_by)."</td>";
				echo "</tr>";
				$tot_expense_amt+=$res1->expense_amt;
			}
			$total_columns_count=5;
			if(store_module() && is_admin()){
				$total_columns_count ++;
			}
			if(warehouse_module() && warehouse_count()>0){
				$total_columns_count ++;
			}
			echo "<tr>
					  <td class='text-right text-bold' colspan='$total_columns_count'><b>Total Expense :</b></td>
					  <td class='text-right text-bold'>".store_number_format($tot_expense_amt)."</td>
					  <td colspan='2'></td>
				  </tr>";
		}
		else{
			$total_columns_count=8;
			if(store_module() && is_admin()){
				$total_columns_count ++;
			}
			if(warehouse_module() && warehouse_count()>0){
				$total_columns_count ++;
			}
			echo "<tr>";
			echo "<td class='text-center text-danger' colspan='$total_columns_count'>No Records Found</td>";
			echo "</tr>";
		}
		
	    exit;
	}
	public function show_stock_report(){
		extract($_POST);
		if(!empty($store_id)){
			$this->db->where("a.store_id",$store_id);
		}
		if(!is_admin()){
			$this->db->where("a.store_id",get_current_store_id());
		}
		$this->db->select("a.sales_price,a.item_code,a.purchase_price,a.item_name,a.tax_type,a.store_id,a.id as item_id,a.item_group");
		$this->db->select("b.tax_name");
		$this->db->from("db_items as a");

		$this->db->from("db_tax as b");
		$this->db->where("b.id=a.tax_id");
		$this->db->where("a.service_bit=0");
		//echo $this->db->get_compiled_select();exit;
		$q1=$this->db->get();
		if($q1->num_rows()>0){
			$i=0;
			$tot_stock = 0;
			foreach ($q1->result() as $res1) {
				if($res1->item_group=='Variants'){continue;}
					$available_qty_wh = total_available_qty_items_of_warehouse($warehouse_id,$res1->store_id,$res1->item_id);

					/*if($available_qty_wh>0){*/
						$tax_type = ($res1->tax_type=='Inclusive') ? 'Inc.' : 'Exc.';
						echo "<tr>";
						echo "<td>".++$i."</td>";
						if(store_module() && is_admin()){
							echo "<td>".get_store_name($res1->store_id)."</td>";	
						}
						echo "<td>".$res1->item_code."</td>";
						echo "<td>".$res1->item_name."</td>";
						echo "<td class='text-right'>".store_number_format($res1->purchase_price)."</td>";
						echo "<td>".$res1->tax_name."[".$tax_type."]</td>";
						echo "<td class='text-right'>".store_number_format($res1->sales_price)."</td>";
						echo "<td>".$available_qty_wh."</td>";
						echo "</tr>";
						$tot_stock +=$available_qty_wh;
					/*}*/

			}
			$total_columns_count=6;
			if(store_module() && is_admin()){
				$total_columns_count ++;
			}
			echo "<tr>
					  <td class='text-right text-bold' colspan='$total_columns_count'><b>Total :</b></td>
					  <td class='text-left text-bold'>".number_format($tot_stock,2)."</td>
				  </tr>";
		}
		else{
			$total_columns_count=7;
			if(store_module() && is_admin()){
				$total_columns_count ++;
			}
			echo "<tr>";
			echo "<td class='text-center text-danger' colspan='$total_columns_count'>No Records Found</td>";
			echo "</tr>";
		}
		
		
	    exit;
	}
	public function brand_wise_stock(){
		extract($_POST);

		if(!empty($store_id)){
			$this->db->where("b.store_id",$store_id);
		}
		if(!is_admin()){
			$this->db->where("b.store_id",get_current_store_id());
		}
		$this->db->from("db_brands as b");
		$this->db->select("b.id,b.brand_name,b.store_id");
		//echo $this->db->get_compiled_select();exit();
		$q1=$this->db->get();
		if($q1->num_rows()>0){
			$i=0;
			$tot_stock=0;
			foreach ($q1->result() as $res1) {
					$available_qty=0;
					$brand_id=$res1->id;
					$store_id=$res1->store_id;

					$str='';
					if(empty($store_id)){
					     $str =" and store_id= $store_id ";
					}
					if(!empty($warehouse_id)){
				         $str =" and warehouse_id= $warehouse_id ";
				    }
					$q3 = "select COALESCE(sum(available_qty),0) as available_qty from 
							db_warehouseitems where 
							item_id in (select id from db_items where brand_id=$brand_id)
							$str
							";

					$q3=$this->db->query($q3);

					$available_qty = $q3->row()->available_qty;
					echo "<tr>";
					echo "<td>".++$i."</td>";
					if(store_module() && is_admin()){
						echo "<td>".get_store_name($res1->store_id)."</td>";	
					}
					echo "<td>".$res1->brand_name."</td>";
					echo "<td>".$available_qty."</td>";
					echo "</tr>";
					$tot_stock+=$available_qty;
			}
			$total_columns_count=2;
			if(store_module() && is_admin()){
				$total_columns_count ++;
			}
			echo "<tr>
					  <td class='text-right text-bold' colspan='$total_columns_count'><b>Total :</b></td>
					  <td class='text-left text-bold'>".number_format($tot_stock,2)."</td>
				  </tr>";

		}
		else{
			$total_columns_count=3;
			if(store_module() && is_admin()){
				$total_columns_count ++;
			}
			echo "<tr>";
			echo "<td class='text-center text-danger' colspan='$total_columns_count'>No Records Found</td>";
			echo "</tr>";
		}
		
	    exit;
	}
	public function show_item_sales_report(){
		extract($_POST);

		/*$from_date=date("Y-m-d",strtotime($from_date));
		$to_date=date("Y-m-d",strtotime($to_date));*/
		
		$from_date = system_fromatted_date($from_date);
		$to_date = system_fromatted_date($to_date);

		if(warehouse_module() && warehouse_count()>0 && !empty($warehouse_id)){
			$this->db->where("a.warehouse_id",$warehouse_id);
		}
		
		$this->db->select("a.id,a.sales_code,a.sales_date,b.customer_name,b.customer_code,a.grand_total,a.paid_amount,a.store_id");
		$this->db->select("c.sales_qty,d.item_name");
	    
	    
		if($view_all=="no"){
			$this->db->where("(a.sales_date>='$from_date' and a.sales_date<='$to_date')");
		}
//		$this->db->group_by("c.`item_id`");
		$this->db->order_by("a.`sales_date`,a.sales_code",'desc');
		$this->db->from("db_sales as a");
		$this->db->where("a.`id`= c.`sales_id`");
		$this->db->where("a.`sales_status`= 'Final'");
		$this->db->from("db_items as d");
		$this->db->where("d.`id`= c.`item_id`");
		$this->db->from("db_customers as b");
		$this->db->where("b.`id`= a.`customer_id`");
		$this->db->from("db_salesitems as c");
		if($item_id!=''){
			$this->db->where("c.item_id=$item_id");
		}
		if(!empty($store_id)){
			$this->db->where("a.store_id",$store_id);
		}
		
		
		//echo $this->db->get_compiled_select();exit();
		
		$q1=$this->db->get();
		if($q1->num_rows()>0){
			$i=0;
			$tot_grand_total=0;
			$tot_paid_amount=0;
			$tot_due_amount=0;
			foreach ($q1->result() as $res1) {
				echo "<tr>";
				echo "<td>".++$i."</td>";
				if(store_module() && is_admin()){
					echo "<td>".get_store_name($res1->store_id)."</td>";	
				}
				if($store_id==get_current_store_id()){
				echo "<td><a title='View Invoice' href='".base_url("sales/invoice/$res1->id")."'>".$res1->sales_code."</a></td>";
				}
				else{
				echo "<td>".$res1->sales_code."</td>";	
				}

				
				echo "<td>".show_date($res1->sales_date)."</td>";
				echo "<td>".$res1->customer_name."</td>";
				echo "<td>".$res1->item_name."</td>";
				echo "<td>".$res1->sales_qty."</td>";
				echo "<td class='text-right'>".store_number_format($res1->grand_total)."</td>";
				echo "<td class='text-right'>".store_number_format($res1->paid_amount)."</td>";
				echo "<td class='text-right'>".store_number_format(($res1->grand_total-$res1->paid_amount))."</td>";
				echo "</tr>";
				$tot_grand_total+=$res1->grand_total;
				$tot_paid_amount+=$res1->paid_amount;
				$tot_due_amount+=($res1->grand_total-$res1->paid_amount);

			}

			$total_columns_count=5;
			if(store_module() && is_admin()){
				$total_columns_count ++;
			}
			if(warehouse_module() && warehouse_count()>0){
				$total_columns_count ++;
			}

			echo "<tr>
					  <td class='text-right text-bold' colspan='$total_columns_count'><b>Total :</b></td>
					  <td class='text-right text-bold'>".store_number_format($tot_grand_total)."</td>
					  <td class='text-right text-bold'>".store_number_format($tot_paid_amount)."</td>
					  <td class='text-right text-bold'>".store_number_format($tot_due_amount)."</td>
				  </tr>";
		}
		else{
			$total_columns_count=8;
			if(store_module() && is_admin()){
				$total_columns_count ++;
			}
			if(warehouse_module() && warehouse_count()>0){
				$total_columns_count ++;
			}
			echo "<tr>";
			echo "<td class='text-center text-danger' colspan='$total_columns_count'>No Records Found</td>";
			echo "</tr>";
		}
		
	    exit;
	}
	public function show_purchase_payments_report(){
		extract($_POST);
		$supplier_id = $this->input->post('supplier_id');
		/*$from_date=date("Y-m-d",strtotime($from_date));
		$to_date=date("Y-m-d",strtotime($to_date));*/

		$from_date = system_fromatted_date($from_date);
		$to_date = system_fromatted_date($to_date);
		
		$this->db->select("c.id,c.purchase_code,a.payment_date,b.supplier_name,b.supplier_code,a.payment_type,a.payment_note,a.payment,a.store_id");
	    
		if($supplier_id!=''){
			$this->db->where("c.supplier_id=$supplier_id");
		}
		$this->db->where("b.id=c.`supplier_id`");
		$this->db->where("(a.payment_date>='$from_date' and a.payment_date<='$to_date')");
		
		$this->db->where("c.id=a.purchase_id");

		$this->db->from("db_purchasepayments as a");
		$this->db->from("db_suppliers as b");
		$this->db->from("db_purchase as c");
		$this->db->where("c.`purchase_status`= 'Received'");
		if(!empty($store_id)){
			$this->db->where("a.store_id",$store_id);
		}
		//$this->db->group_by("c.purchase_code");
		
		//echo $this->db->get_compiled_select();
		
		$q1=$this->db->get();
		if($q1->num_rows()>0){
			$i=0;
			$tot_payment=0;
			foreach ($q1->result() as $res1) {
				echo "<tr>";
				echo "<td>".++$i."</td>";
				if(store_module() && is_admin()){
					echo "<td>".get_store_name($res1->store_id)."</td>";	
				}
				if($res1->store_id==get_current_store_id()){
				echo "<td><a title='View Invoice' href='".base_url("purchase/invoice/$res1->id")."'>".$res1->purchase_code."</a></td>";
				}
				else{
				echo "<td>".$res1->purchase_code."</td>";	
				}

				
				echo "<td>".show_date($res1->payment_date)."</td>";
				echo "<td>".$res1->supplier_code."</td>";
				echo "<td>".$res1->supplier_name."</td>";
				echo "<td>".$res1->payment_type."</td>";
				echo "<td>".$res1->payment_note."</td>";
				echo "<td class='text-right'>".store_number_format(($res1->payment))."</td>";
				echo "</tr>";
				$tot_payment+=$res1->payment;
			}
			$total_columns_count=7;
			if(store_module() && is_admin()){
				$total_columns_count ++;
			}
			echo "<tr>
					  <td class='text-right text-bold' colspan='$total_columns_count'><b>Total :</b></td>
					  <td class='text-right text-bold'>".store_number_format($tot_payment)."</td>
				  </tr>";
		}
		else{
			$total_columns_count=8;
			if(store_module() && is_admin()){
				$total_columns_count ++;
			}
			echo "<tr>";
			echo "<td class='text-center text-danger' colspan='$total_columns_count'>No Records Found</td>";
			echo "</tr>";
		}
		
	    exit;
	}
	public function show_sales_payments_report(){
		extract($_POST);
		
		/*$from_date=date("Y-m-d",strtotime($from_date));
		$to_date=date("Y-m-d",strtotime($to_date));*/

		$from_date = system_fromatted_date($from_date);
		$to_date = system_fromatted_date($to_date);
		
		$this->db->select("c.id,c.sales_code,a.payment_date,b.customer_name,b.customer_code,a.payment_type,a.payment_note,a.payment,c.store_id");
	    
		if($customer_id!=''){
			$this->db->where("c.customer_id=$customer_id");
		}
		$this->db->where("b.id=c.`customer_id`");
		$this->db->where("(a.payment_date>='$from_date' and a.payment_date<='$to_date')");
		
		$this->db->where("c.id=a.sales_id");

		$this->db->from("db_salespayments as a");
		$this->db->from("db_customers as b");
		$this->db->from("db_sales as c");
		$this->db->where("c.`sales_status`= 'Final'");
		if(!empty($store_id)){
			$this->db->where("a.store_id",$store_id);
		}
		if(!empty($payment_type)){
			$this->db->where("a.payment_type",$payment_type);
		}
		//$this->db->group_by("c.sales_code");
		
		//echo $this->db->get_compiled_select();
		
		$q1=$this->db->get();
		if($q1->num_rows()>0){
			$i=0;
			$tot_payment=0;
			foreach ($q1->result() as $res1) {
				echo "<tr>";
				echo "<td>".++$i."</td>";
				if(store_module() && is_admin()){
					echo "<td>".get_store_name($res1->store_id)."</td>";	
				}
				if($res1->store_id==get_current_store_id()){
				echo "<td><a title='View Invoice' href='".base_url("sales/invoice/$res1->id")."'>".$res1->sales_code."</a></td>";
				}
				else{
				echo "<td>".$res1->sales_code."</td>";	
				}
				
				echo "<td>".show_date($res1->payment_date)."</td>";
				echo "<td>".$res1->customer_code."</td>";
				echo "<td>".$res1->customer_name."</td>";
				echo "<td>".$res1->payment_type."</td>";
				echo "<td>".$res1->payment_note."</td>";
				echo "<td class='text-right'>".store_number_format(($res1->payment))."</td>";
				echo "</tr>";
				$tot_payment+=$res1->payment;
			}
			$total_columns_count=7;
			if(store_module() && is_admin()){
				$total_columns_count ++;
			}
			echo "<tr>
					  <td class='text-right text-bold' colspan='$total_columns_count'><b>Total :</b></td>
					  <td class='text-right text-bold'>".store_number_format($tot_payment)."</td>
				  </tr>";
		}
		else{
			$total_columns_count=8;
			if(store_module() && is_admin()){
				$total_columns_count ++;
			}
			echo "<tr>";
			echo "<td class='text-center text-danger' colspan='$total_columns_count'>No Records Found</td>";
			echo "</tr>";
		}
		
	    exit;
	}
	/*Expired Items Report*/
	public function show_expired_items_report(){
		extract($_POST);
		$CI =& get_instance();

		
		//$to_date=date("Y-m-d",strtotime($to_date));
		$to_date = system_fromatted_date($to_date);

		$this->db->select("id,item_code,item_name,expire_date,stock,lot_number,store_id");
	    
		if($item_id!=''){
			
			$this->db->where("id=$item_id");
		}
		if($view_all=="no"){
			$this->db->where("(expire_date<='$to_date')");
		}
		$this->db->from("db_items");
		if(!empty($store_id)){
			$this->db->where("store_id",$store_id);
		}
		//echo $this->db->get_compiled_select();exit();
		
		$q1=$this->db->get();
		if($q1->num_rows()>0){
			$i=0;
			foreach ($q1->result() as $res1) {
				if(get_total_qty_of_warehouse_item($res1->id,$warehouse_id,$res1->store_id) > 0 && !empty($warehouse_id)){
					echo "<tr>";
					echo "<td>".++$i."</td>";
					if(store_module() && is_admin()){
						echo "<td>".get_store_name($res1->store_id)."</td>";	
					}
					echo "<td>".$res1->item_code."</td>";
					echo "<td>".$res1->item_name."</td>";
					echo "<td>".$res1->lot_number."</td>";
					echo "<td>".show_date($res1->expire_date)."</td>";
					echo "<td>".get_total_qty_of_warehouse_item($res1->id,$warehouse_id,$res1->store_id)."</td>";

				}
				else{
					echo "<tr>";
					echo "<td>".++$i."</td>";
					if(store_module() && is_admin()){
						echo "<td>".get_store_name($res1->store_id)."</td>";	
					}
					echo "<td>".$res1->item_code."</td>";
					echo "<td>".$res1->item_name."</td>";
					echo "<td>".$res1->lot_number."</td>";
					echo "<td>".show_date($res1->expire_date)."</td>";
					echo "<td>".$res1->stock."</td>";
				}

			}
		}
		else{
			$total_columns_count=6;
			if(store_module() && is_admin()){
				$total_columns_count ++;
			}
			echo "<tr>";
			echo "<td class='text-center text-danger' colspan='$total_columns_count'>No Records Found</td>";
			echo "</tr>";
		}
		
	    exit;
	}

	public function get_profit_loss_report(){

			$store_id=$this->input->post('store_id');
			$CI =& get_instance();
			$info=array();

			//Get opening Balance
			if(store_module() && is_admin()){if(!empty($store_id)){ 
						$this->db->where("a.store_id",$store_id);}
					}else{ 
						$this->db->where("a.store_id",get_current_store_id());	
				}

		

			$this->db->select("SUM(b.adjustment_qty * a.purchase_price) AS  opening_stock_price");
			$this->db->from("db_items AS a , db_stockadjustmentitems AS b");
			$this->db->where("a.id=b.item_id");
            $opening_stock_price=$this->db->get()->row()->opening_stock_price;
            $info['opening_stock_price']=(store_number_format($opening_stock_price));
            


            //total purchase amt
            if(store_module() && is_admin()){
				if(!empty($store_id)){ $this->db->where("b.store_id",$store_id);}
				}else{ $this->db->where("b.store_id",get_current_store_id());	
			}

			$this->db->select("COALESCE(SUM(a.tax_amt),0) AS tax_amt");
			$this->db->from("db_purchaseitems as a,db_purchase as b");
			$this->db->where("a.purchase_id=b.id and b.purchase_status='Received'");
            $purchase_tax_amt=$this->db->get()->row()->tax_amt;
            $info['purchase_tax_amt']=(store_number_format($purchase_tax_amt));
            




            //total purchase amt
            if(store_module() && is_admin()){
				if(!empty($store_id)){ $this->db->where("store_id",$store_id);}
				}else{ $this->db->where("store_id",get_current_store_id());	
			}
			$this->db->select("COALESCE(SUM(grand_total),0) AS pur_total");
			$this->db->from("db_purchase");
			$this->db->where("purchase_status='Received'");
            $pur_total=$this->db->get()->row()->pur_total;
            $pur_total-=$purchase_tax_amt;
            $info['pur_total']=(store_number_format($pur_total));



            //Other Charge of Purchase entry
            if(store_module() && is_admin()){
				if(!empty($store_id)){ $this->db->where("store_id",$store_id);}
				}else{ $this->db->where("store_id",get_current_store_id());	
			}
			$this->db->select("COALESCE(SUM(other_charges_amt),0) AS other_charges_amt");
			$this->db->from("db_purchase");
			$this->db->where("purchase_status='Received'");
            $pur_other_charges_amt=$this->db->get()->row()->other_charges_amt;
            $info['pur_other_charges_amt']=(store_number_format($pur_other_charges_amt));


            
            //Disount purchase entry
            if(store_module() && is_admin()){
				if(!empty($store_id)){ $this->db->where("b.store_id",$store_id);}
				}else{ $this->db->where("b.store_id",get_current_store_id());	
			}
			$this->db->select("COALESCE(SUM(a.discount_amt),0) AS discount_amt");
			$this->db->from("db_purchaseitems as a,db_purchase as b");
			$this->db->where("a.purchase_id=b.id and b.purchase_status='Received'");
            $purchase_discount_amt=$this->db->get()->row()->discount_amt;
            

            if($purchase_discount_amt==0){
            	if(store_module() && is_admin()){
					if(!empty($store_id)){ $this->db->where("store_id",$store_id);}
					}else{ $this->db->where("store_id",get_current_store_id());	
				}
				$this->db->select("COALESCE(SUM(tot_discount_to_all_amt),0) AS tot_discount_to_all_amt");
				$this->db->from("db_purchase");
				$this->db->where("purchase_status='Received'");
				$purchase_discount_amt=$this->db->get()->row()->tot_discount_to_all_amt;
            }
            $info['purchase_discount_amt']=(store_number_format($purchase_discount_amt));

            //purchase Paid Amount
            if(store_module() && is_admin()){
				if(!empty($store_id)){ $this->db->where("store_id",$store_id);}
				}else{ $this->db->where("store_id",get_current_store_id());	
			}
			$this->db->select("COALESCE(SUM(paid_amount),0) AS paid_amount");
			$this->db->from("db_purchase");
            $purchase_paid_amount=$this->db->get()->row()->paid_amount;
            $info['purchase_paid_amount']=(store_number_format($purchase_paid_amount));
            
            //total purchase return tax amt
            if(store_module() && is_admin()){
				if(!empty($store_id)){ $this->db->where("store_id",$store_id);}
				}else{ $this->db->where("store_id",get_current_store_id());	
			}
			$this->db->select("COALESCE(SUM(tax_amt),0) AS tax_amt");
			$this->db->from("db_purchaseitemsreturn");
            $purchase_return_tax_amt=$this->db->get()->row()->tax_amt;
            $info['purchase_return_tax_amt']=(store_number_format($purchase_return_tax_amt));
            
            //total purchase return amt
            if(store_module() && is_admin()){
				if(!empty($store_id)){ $this->db->where("store_id",$store_id);}
				}else{ $this->db->where("store_id",get_current_store_id());	
			}
			$this->db->select("COALESCE(SUM(grand_total),0) AS pur_total");
			$this->db->from("db_purchasereturn");
            $pur_return_total=$this->db->get()->row()->pur_total;
            $pur_return_total-=$purchase_return_tax_amt;
            $info['pur_return_total']=(store_number_format($pur_return_total));




            
            //Other Charge of Purchase return entry
            if(store_module() && is_admin()){
				if(!empty($store_id)){ $this->db->where("store_id",$store_id);}
				}else{ $this->db->where("store_id",get_current_store_id());	
			}
			$this->db->select("COALESCE(SUM(other_charges_amt),0) AS other_charges_amt");
			$this->db->from("db_purchasereturn");
            $pur_return_other_charges_amt=$this->db->get()->row()->other_charges_amt;
            $info['pur_return_other_charges_amt']=(store_number_format($pur_return_other_charges_amt));
            
            //Disount purchase return entry
            if(store_module() && is_admin()){
				if(!empty($store_id)){ $this->db->where("store_id",$store_id);}
				}else{ $this->db->where("store_id",get_current_store_id());	
			}
			$this->db->select("COALESCE(SUM(discount_amt),0) AS discount_amt");
			$this->db->from("db_purchaseitemsreturn");
            $purchase_return_discount_amt=$this->db->get()->row()->discount_amt;

            if($purchase_return_discount_amt==0){
            	if(store_module() && is_admin()){
					if(!empty($store_id)){ $this->db->where("store_id",$store_id);}
					}else{ $this->db->where("store_id",get_current_store_id());	
				}
				$this->db->select("COALESCE(SUM(tot_discount_to_all_amt),0) AS tot_discount_to_all_amt");
				$this->db->from("db_purchasereturn");
				$purchase_return_discount_amt=$this->db->get()->row()->tot_discount_to_all_amt;
            }
            $info['purchase_return_discount_amt']=(store_number_format($purchase_return_discount_amt));




            //Purchase Return Paid Amount
            if(store_module() && is_admin()){
				if(!empty($store_id)){ $this->db->where("store_id",$store_id);}
				}else{ $this->db->where("store_id",get_current_store_id());	
			}
			$this->db->select("COALESCE(SUM(paid_amount),0) AS paid_amount");
			$this->db->from("db_purchasereturn");
            $purchase_return_paid_amount=$this->db->get()->row()->paid_amount;
            $info['purchase_return_paid_amount']=(store_number_format($purchase_return_paid_amount));
            
            
            //total sales amt
            if(store_module() && is_admin()){
				if(!empty($store_id)){ $this->db->where("b.store_id",$store_id);}
				}else{ $this->db->where("b.store_id",get_current_store_id());	
			}
			$this->db->select("COALESCE(SUM(a.tax_amt),0) AS tax_amt");
			$this->db->from("db_salesitems as a,db_sales as b");
			$this->db->where("a.sales_id=b.id and b.sales_status='Final'");
            $sales_tax_amt=$this->db->get()->row()->tax_amt;
            $info['sales_tax_amt']=(store_number_format($sales_tax_amt));
            



            //Other Charge of Sales entry
            if(store_module() && is_admin()){
				if(!empty($store_id)){ $this->db->where("store_id",$store_id);}
				}else{ $this->db->where("store_id",get_current_store_id());	
			}
			$this->db->select("COALESCE(SUM(other_charges_amt),0) AS other_charges_amt");
			$this->db->from("db_sales");
			$this->db->where("sales_status='Final'");
            $sal_other_charges_amt=$this->db->get()->row()->other_charges_amt;
            $info['sal_other_charges_amt']=(store_number_format($sal_other_charges_amt));
            



            //Disount sales entry
            if(store_module() && is_admin()){
				if(!empty($store_id)){ $this->db->where("b.store_id",$store_id);}
				}else{ $this->db->where("b.store_id",get_current_store_id());	
			}
			$this->db->select("COALESCE(SUM(a.discount_amt),0) AS discount_amt");
			$this->db->from("db_salesitems as a,db_sales as b");
			$this->db->where(" a.sales_id=b.id and b.sales_status='Final'");
            $sales_discount_amt=$this->db->get()->row()->discount_amt;
            
            if($sales_discount_amt==0){
            	if(store_module() && is_admin()){
					if(!empty($store_id)){ $this->db->where("store_id",$store_id);}
					}else{ $this->db->where("store_id",get_current_store_id());	
				}
				$this->db->select("COALESCE(SUM(tot_discount_to_all_amt),0) AS tot_discount_to_all_amt");
				$this->db->from("db_sales");
				$this->db->where("sales_status='Received'");
				$sales_discount_amt=$this->db->get()->row()->tot_discount_to_all_amt;
            }
            $info['sales_discount_amt']=(store_number_format($sales_discount_amt));
            
            


            //Total SAles amount
            if(store_module() && is_admin()){
				if(!empty($store_id)){ $this->db->where("store_id",$store_id);}
				}else{ $this->db->where("store_id",get_current_store_id());	
			}
			$this->db->select("COALESCE(sum(grand_total),0) AS tot_sal_grand_total");
			$this->db->from("db_sales");
			$this->db->where("sales_status='Final'");
            $sal_total=$this->db->get()->row()->tot_sal_grand_total;
            $sal_total-=$sales_tax_amt;
            $info['sal_total']=(store_number_format($sal_total));
            

        

            //sales Paid Amount
            if(store_module() && is_admin()){
				if(!empty($store_id)){ $this->db->where("store_id",$store_id);}
				}else{ $this->db->where("store_id",get_current_store_id());	
			}
			$this->db->select("COALESCE(SUM(paid_amount),0) AS paid_amount");
			$this->db->from("db_sales");
            $sales_paid_amount=$this->db->get()->row()->paid_amount;
            $info['sales_paid_amount']=(store_number_format($sales_paid_amount));
            


            //total sales return amt
            if(store_module() && is_admin()){
				if(!empty($store_id)){ $this->db->where("store_id",$store_id);}
				}else{ $this->db->where("store_id",get_current_store_id());	
			}
			$this->db->select("COALESCE(SUM(tax_amt),0) AS tax_amt");
			$this->db->from("db_salesitemsreturn");
            $sales_return_tax_amt=$this->db->get()->row()->tax_amt;
            $info['sales_return_tax_amt']=(store_number_format($sales_return_tax_amt));
            

         

            //total sales return amt
            if(store_module() && is_admin()){
				if(!empty($store_id)){ $this->db->where("store_id",$store_id);}
				}else{ $this->db->where("store_id",get_current_store_id());	
			}
			$this->db->select("COALESCE(SUM(grand_total),0) AS sal_total");
			$this->db->from("db_salesreturn");
            $sal_return_total=$this->db->get()->row()->sal_total;
            $sal_return_total-=$sales_return_tax_amt;
            $info['sal_return_total']=(store_number_format($sal_return_total));
          


            //Other Charge of Sales return entry
            if(store_module() && is_admin()){
				if(!empty($store_id)){ $this->db->where("store_id",$store_id);}
				}else{ $this->db->where("store_id",get_current_store_id());	
			}
			$this->db->select("COALESCE(SUM(other_charges_amt),0) AS other_charges_amt");
			$this->db->from("db_salesreturn");
            $sal_return_other_charges_amt=$this->db->get()->row()->other_charges_amt;
            $info['sal_return_other_charges_amt']=(store_number_format($sal_return_other_charges_amt));
            
            //Disount sales return entry
            if(store_module() && is_admin()){
				if(!empty($store_id)){ $this->db->where("store_id",$store_id);}
				}else{ $this->db->where("store_id",get_current_store_id());	
			}
			$this->db->select("COALESCE(SUM(discount_amt),0) AS discount_amt");
			$this->db->from("db_salesitemsreturn");
            $sales_return_discount_amt=$this->db->get()->row()->discount_amt;
            
            if($sales_return_discount_amt==0){
            	if(store_module() && is_admin()){
					if(!empty($store_id)){ $this->db->where("store_id",$store_id);}
					}else{ $this->db->where("store_id",get_current_store_id());	
				}
				$this->db->select("COALESCE(SUM(tot_discount_to_all_amt),0) AS tot_discount_to_all_amt");
				$this->db->from("db_salesreturn");
	            $sales_return_discount_amt=$this->db->get()->row()->tot_discount_to_all_amt;
            }
            $info['sales_return_discount_amt']=(store_number_format($sales_return_discount_amt));
            

            //sales Return Paid Amount
            if(store_module() && is_admin()){
				if(!empty($store_id)){ $this->db->where("store_id",$store_id);}
				}else{ $this->db->where("store_id",get_current_store_id());	
			}
			$this->db->select("COALESCE(SUM(paid_amount),0) AS paid_amount");
			$this->db->from("db_salesreturn");
            $sales_return_paid_amount=$this->db->get()->row()->paid_amount;
            $info['sales_return_paid_amount']=(store_number_format($sales_return_paid_amount));
            
            
            //total expense amt
            if(store_module() && is_admin()){
				if(!empty($store_id)){ $this->db->where("store_id",$store_id);}
				}else{ $this->db->where("store_id",get_current_store_id());	
			}
			$this->db->select("COALESCE(SUM(expense_amt),0) AS exp_total");
			$this->db->from("db_expense");
            $exp_total=$this->db->get()->row()->exp_total;
            $info['exp_total']=(store_number_format($exp_total));;
            
            //total purchase due
            if(store_module() && is_admin()){
				if(!empty($store_id)){ $this->db->where("store_id",$store_id);}
				}else{ $this->db->where("store_id",get_current_store_id());	
			}
			$this->db->select("COALESCE(SUM(purchase_due),0) AS purchase_due");
			$this->db->from("db_suppliers");
            $purchase_due_total=$this->db->get()->row()->purchase_due;
            $info['purchase_due_total']=(store_number_format($purchase_due_total));
            
            //total purchase due
            if(store_module() && is_admin()){
				if(!empty($store_id)){ $this->db->where("store_id",$store_id);}
				}else{ $this->db->where("store_id",get_current_store_id());	
			}
			$this->db->select("COALESCE(SUM(purchase_return_due),0) AS purchase_due");
			$this->db->from("db_suppliers");
            $purchase_return_due_total=$this->db->get()->row()->purchase_due;
            $info['purchase_return_due_total']=(store_number_format($purchase_return_due_total));
            
            //total sales due
            if(store_module() && is_admin()){
				if(!empty($store_id)){ $this->db->where("store_id",$store_id);}
				}else{ $this->db->where("store_id",get_current_store_id());	
			}
			$this->db->select("COALESCE(SUM(sales_due),0) AS sales_due");
			$this->db->from("db_customers");
            $sales_due_total=$this->db->get()->row()->sales_due;
            $info['sales_due_total']=(store_number_format($sales_due_total));
            
            //total sales return due
            if(store_module() && is_admin()){
				if(!empty($store_id)){ $this->db->where("store_id",$store_id);}
				}else{ $this->db->where("store_id",get_current_store_id());	
			}
			$this->db->select("COALESCE(SUM(sales_return_due),0) AS return_due");
			$this->db->from("db_customers");
            $sales_return_due_total=$this->db->get()->row()->return_due;
            $info['sales_return_due_total']=(store_number_format($sales_return_due_total));
            
            
            

            if(store_module() && is_admin()){
				if(!empty($store_id)){ $this->db->where("c.store_id",$store_id);}
				}else{ $this->db->where("c.store_id",get_current_store_id());	
			}
			$this->db->select("b.tax_amt,b.item_id,a.item_name,COALESCE(sum(b.sales_qty),0) as sales_qty,a.purchase_price,
                  COALESCE(SUM(total_cost),0) as total_cost");
			$this->db->from("db_items as a, db_salesitems as b, db_sales as c");
			$this->db->where("c.id=b.sales_id and a.id=b.item_id and c.sales_status='Final'");
	
			$this->db->group_by("item_id");
			//$this->db->where("a.service_bit=0");
            $q1=$this->db->get();
            
            /*SELECT SUM(a.total_cost ) - SUM(a.sales_qty*b.purchase_price) AS gross FROM 
			  db_salesitems a,
			  db_items b 
			  WHERE b.id = a.item_id
			*/
            if($q1->num_rows()>0){
            $i=0;
            $tot_purchase_price=0;
            $tot_sales_cost=0;
            $gross_profit=0;
            $tot_purchase_return_price=0;
            $tot_sales_return_price=0;
            $tot_sales_qty=0;
            $tot_purchase_return_qty=0;
            $tot_sales_return_qty=0;
            $grand_profit=0;
            $tot_net_profit=0;
            foreach ($q1->result() as $res1) {
	              /*Purchase Return Quantity*/
	              $purchase_return_qty=$this->db->query("
	                  SELECT COALESCE(sum(return_qty),0) as return_qty
	                  FROM db_purchaseitemsreturn
	                  WHERE 
	                  item_id =".$res1->item_id)->row()->return_qty;
	            
	              /*Sales Return Quantity*/
	              $q3=$this->db->query("
	                  SELECT COALESCE(sum(total_cost),0) as total_cost,COALESCE(sum(return_qty),0) as return_qty
	                  FROM db_salesitemsreturn
	                  WHERE 
	                  item_id =".$res1->item_id);
	              $sales_return_total_cost=$q3->row()->total_cost;
	              $sales_return_qty=$q3->row()->return_qty;
	              
	              $qty = $res1->sales_qty-$sales_return_qty;
	              $purchase_price = $res1->purchase_price * $qty;
	            
	              $total_cost = ($res1->total_cost - $sales_return_total_cost);
	              //$purchase_return_price = $res1->purchase_price*$purchase_return_qty;
	              $profit = $total_cost - $purchase_price;
	            
	              $tax_amt = $res1->tax_amt/$res1->sales_qty;
	            
	                //$net_profit =$profit-($tax_amt*$qty);
	                $net_profit =$profit;//As Per Customer Needs
	            
	              $gross_profit+=$profit;
	              $tot_net_profit+=$net_profit;
            	}  //for    
            }//foreach
            else{
	            $gross_profit=0;
	            $tot_net_profit=0;
            }
            //$gross_profit -=$exp_total;
            $tot_net_profit -=$exp_total;
            $info['gross_profit']=(store_number_format($gross_profit));
            $info['tot_net_profit']=(store_number_format($tot_net_profit));
            return $info;
	}
	public function get_profit_by_item(){
		$CI =& get_instance();
		extract($_POST);
		/*$from_date=date("Y-m-d",strtotime($from_date));
		$to_date=date("Y-m-d",strtotime($to_date));*/

		$from_date = system_fromatted_date($from_date);
		$to_date = system_fromatted_date($to_date);

		
		if(warehouse_module() && warehouse_count()>0 && !empty($warehouse_id)){
			$this->db->where("c.warehouse_id",$warehouse_id);
		}
		if(store_module() && is_admin()){
			if(!empty($store_id)){ $this->db->where("c.store_id",$store_id);}
			}else{ $this->db->where("c.store_id",get_current_store_id());	
		}
		$this->db->select("a.service_bit, b.tax_amt,b.item_id,a.item_name,COALESCE(sum(b.sales_qty),0) as sales_qty,a.purchase_price,
						COALESCE(SUM(total_cost),0) as total_cost");
		$this->db->from("db_items as a, db_salesitems as b, db_sales as c");
		$this->db->where("c.id=b.sales_id and a.id=b.item_id and c.sales_status='Final'");
		$this->db->where("( c.sales_date>='".$from_date."' and  c.sales_date<='".$to_date."')");
		$this->db->group_by("item_id");
		//echo $this->db->get_compiled_select();exit();
        $q1=$this->db->get();
		
		if($q1->num_rows()>0){
			$i=0;
			$tot_purchase_price=0;
			$tot_sales_cost=0;
			$gross_profit=0;
			$tot_purchase_return_price=0;
			$tot_sales_return_price=0;
			$tot_sales_qty=0;
			$tot_purchase_return_qty=0;
			$tot_sales_return_qty=0;
			$grand_profit=0;
			$tot_net_profit=0;
			foreach ($q1->result() as $res1) {
				/*Purchase Return Quantity*/
				$purchase_return_qty=$this->db->query("
						SELECT COALESCE(sum(return_qty),0) as return_qty
						FROM db_purchaseitemsreturn
						WHERE 
						item_id =".$res1->item_id)->row()->return_qty;

				/*Sales Return Quantity*/
				$q3=$this->db->query("
						SELECT COALESCE(sum(total_cost),0) as total_cost,COALESCE(sum(return_qty),0) as return_qty
						FROM db_salesitemsreturn
						WHERE 
						item_id =".$res1->item_id);
				$sales_return_total_cost=$q3->row()->total_cost;
				$sales_return_qty=$q3->row()->return_qty;
				
				$qty = $res1->sales_qty-$sales_return_qty;
				//$purchase_price =  $res1->purchase_price * $qty;
				$purchase_price = ($res1->service_bit==0) ? $res1->purchase_price * $qty : 0;

				$total_cost = ($res1->total_cost - $sales_return_total_cost);
				//$purchase_return_price = $res1->purchase_price*$purchase_return_qty;
				$profit = $total_cost - $purchase_price;

				$tax_amt = $res1->tax_amt/$res1->sales_qty;

			    $net_profit =$profit-($tax_amt*$qty);

				echo "<tr>";
				echo "<td>".++$i."</td>";
				echo "<td>".$res1->item_name."</td>";
				echo "<td>".$qty."</td>";
				echo "<td style='text-align:right;'>".store_number_format($total_cost)."</td>";
				echo "<td style='text-align:right;'>".(store_number_format($purchase_price))."</td>";
				/*echo "<td style=''>".$purchase_return_qty."</td>";
				echo "<td style='text-align:right;'>".(store_number_format($purchase_return_price))."</td>";*/
				/*echo "<td style=''>".$sales_return_qty."</td>";
				echo "<td style='text-align:right;'>".($sales_return_total_cost)."</td>";*/
				echo "<td style='text-align:right;'>".(store_number_format($profit))."</td>";
				//echo "<td style='text-align:right;'>".(store_number_format($net_profit))."</td>";
				echo "</tr>";
				$tot_purchase_price+=$purchase_price;
				//$tot_purchase_return_price+=$purchase_return_price;
				$tot_sales_cost+=$total_cost;
				//$tot_sales_return_cost+=$sales_return_total_cost;
				//$gross_profit+=(($profit + $purchase_return_price)-$sales_return_total_cost);
				$tot_sales_qty+=($res1->sales_qty-$sales_return_qty);
				$tot_purchase_return_qty+=$purchase_return_qty;
				$tot_sales_return_qty+=$sales_return_qty;
				$gross_profit+=$profit;
				$tot_net_profit+=$net_profit;
			}
			echo "<tr>
					  <td class='text-right text-bold' colspan='2'><b>Total :</b></td>
					  <td class='text-bold'>".$tot_sales_qty."</td>
					  <td class='text-right text-bold'>".(store_number_format($tot_sales_cost))."</td>
					  <td class='text-right text-bold'>".(store_number_format($tot_purchase_price))."</td>
					  
					  <td class='text-right text-bold'>".(store_number_format($gross_profit))."</td>
				  </tr>";
				  /*<td class='text-bold'>".$tot_purchase_return_qty."</td>
					  <td class='text-right text-bold'>".(store_number_format($tot_purchase_return_price))."</td>
					  <td class='text-bold'>".$tot_sales_return_qty."</td>
					  <td class='text-right text-bold'>".(store_number_format($tot_sales_return_cost))."</td>
					  */
		}
		else{
			echo "<tr>";
			echo "<td class='text-center text-danger' colspan=7>No Records Found</td>";
			echo "</tr>";
		}
		
	    exit;
	}
	public function get_profit_by_invoice(){
		$CI =& get_instance();
		extract($_POST);
		/*$from_date=date("Y-m-d",strtotime($from_date));
		$to_date=date("Y-m-d",strtotime($to_date));*/

		$from_date = system_fromatted_date($from_date);
		$to_date = system_fromatted_date($to_date);
		

		
		if(warehouse_module() && warehouse_count()>0 && !empty($warehouse_id)){
			$this->db->where("a.warehouse_id",$warehouse_id);
		}
		if(store_module() && is_admin()){
			if(!empty($store_id)){ $this->db->where("a.store_id",$store_id);}
			}else{ $this->db->where("a.store_id",get_current_store_id());	
		}
		$this->db->select("a.id,a.sales_date,a.sales_code,b.customer_name");
		$this->db->from("db_sales as a,db_customers as b");
		$this->db->where("a.sales_status='Final' and b.id=a.customer_id");

		$q1=$this->db->get();

		if($q1->num_rows()>0){
			$i=0;
			$tot_purchase_price=0;
			$tot_sales_cost=0;
			$tot_profit=0;
			$net_profit=0;
			$tot_net_profit=0;

			foreach ($q1->result() as $res1) {
				$q2=$this->db->query("SELECT b.sales_qty,COALESCE(SUM(purchase_price*sales_qty),0) AS purchase_price, COALESCE(SUM(total_cost),0) AS total_cost FROM db_items AS a, db_salesitems AS b, db_sales AS c WHERE c.id=b.sales_id AND a.id=b.item_id and c.sales_status='Final'
					AND b.sales_id=".$res1->id);

				$q3=$this->db->query("SELECT COALESCE(SUM(purchase_price*return_qty),0) AS purchase_price, COALESCE(SUM(total_cost),0) AS total_cost FROM db_items AS a, db_salesitemsreturn AS b, db_salesreturn AS c WHERE c.id=b.return_id AND a.id=b.item_id and c.return_status!='Final'
					AND b.sales_id=".$res1->id);
				$purchase_return_price=$q3->row()->purchase_price;



				//Total price item_purchase_price * qty
				$purchase_price = ($q2->row()->purchase_price-$purchase_return_price);
				//Total price item_sales_price * qty
				$sales_price = ($q2->row()->total_cost-$q3->row()->total_cost);

				$profit = $sales_price - $purchase_price;
				
				/*$sales_tax_amt =$this->db->query("select COALESCE(SUM(tax_amt),0) AS tax_amt from db_salesitems where sales_id=".$res1->id)->row()->tax_amt;
				
				$sales_return_tax_amt =$this->db->query("select COALESCE(SUM(tax_amt),0) AS tax_amt from db_salesitemsreturn where sales_id=".$res1->id)->row()->tax_amt;

				$net_profit = $profit + ($sales_tax_amt-$sales_return_tax_amt);*/
				echo "<tr>";
				echo "<td>".++$i."</td>";
				echo "<td>".$res1->sales_code."</td>";
				echo "<td>".show_date($res1->sales_date)."</td>";
				echo "<td>".$res1->customer_name."</td>";
				echo "<td style='text-align:right;'>".store_number_format($sales_price)."</td>";
				echo "<td style='text-align:right;'>".store_number_format($purchase_price)."</td>";
				echo "<td style='text-align:right;'>".store_number_format($profit)."</td>";
				//echo "<td style='text-align:right;'>".(store_number_format($net_profit))."</td>";
				echo "</tr>";
				$tot_purchase_price+=$purchase_price;
				$tot_sales_cost+=$sales_price;
				$tot_profit+=$profit;
				$tot_net_profit+=$net_profit;
			}
			echo "<tr>
					  <td class='text-right text-bold' colspan='4'><b>Total :</b></td>
					  <td class='text-right text-bold'>".(store_number_format($tot_sales_cost))."</td>
					  <td class='text-right text-bold'>".(store_number_format($tot_purchase_price))."</td>
					  <td class='text-right text-bold'>".(store_number_format($tot_profit))."</td>
					  
				  </tr>";
		}
		else{
			echo "<tr>";
			echo "<td class='text-center text-danger' colspan=7>No Records Found</td>";
			echo "</tr>";
		}
		
	    exit;
	}

	public function show_seller_points_report(){
		extract($_POST);

		/*$from_date=date("Y-m-d",strtotime($from_date));
		$to_date=date("Y-m-d",strtotime($to_date));*/
		
		$from_date = system_fromatted_date($from_date);
		$to_date = system_fromatted_date($to_date);

		if(warehouse_module() && warehouse_count()>0 && !empty($warehouse_id)){
			$this->db->where("a.warehouse_id",$warehouse_id);
		}
		
		$this->db->select("a.created_by,c.seller_points, a.id,a.sales_code,a.sales_date,b.customer_name,b.customer_code,a.grand_total,a.paid_amount,a.store_id");
		$this->db->select("c.sales_qty,d.item_name");
	    
	    
		if($view_all=="no"){
			$this->db->where("(a.sales_date>='$from_date' and a.sales_date<='$to_date')");
		}
//		$this->db->group_by("c.`item_id`");
		$this->db->order_by("a.`sales_date`,a.sales_code",'desc');
		$this->db->from("db_sales as a");
		$this->db->where("a.`id`= c.`sales_id`");
		$this->db->where("a.`sales_status`= 'Final'");
		$this->db->from("db_items as d");
		$this->db->where("d.`id`= c.`item_id`");
		$this->db->from("db_customers as b");
		$this->db->where("b.`id`= a.`customer_id`");
		$this->db->from("db_salesitems as c");
		if(!empty($created_by)){
			$this->db->where("upper(a.created_by)=upper('$created_by')");
		}
		if($item_id!=''){
			$this->db->where("c.item_id=$item_id");
		}
		if(!empty($store_id)){
			$this->db->where("a.store_id",$store_id);
		}
		
		
		//echo $this->db->get_compiled_select();exit();
		
		$q1=$this->db->get();
		if($q1->num_rows()>0){
			$i=0;
			$tot_seller_points=0;
			foreach ($q1->result() as $res1) {
				echo "<tr>";
				echo "<td>".++$i."</td>";
				if(store_module() && is_admin()){
					echo "<td>".get_store_name($res1->store_id)."</td>";	
				}
				if($store_id==get_current_store_id()){
				echo "<td><a title='View Invoice' href='".base_url("sales/invoice/$res1->id")."'>".$res1->sales_code."</a></td>";
				}
				else{
				echo "<td>".$res1->sales_code."</td>";	
				}

				
				echo "<td>".show_date($res1->sales_date)."</td>";
				echo "<td>".$res1->customer_name."</td>";
				echo "<td>".$res1->item_name."</td>";
				echo "<td>".$res1->sales_qty."</td>";
				echo "<td>".ucfirst($res1->created_by)."</td>";
				echo "<td>".$res1->seller_points."</td>";
				echo "</tr>";
				$tot_seller_points+=$res1->seller_points;
			}

			$total_columns_count=6;
			if(store_module() && is_admin()){
				$total_columns_count ++;
			}
			if(warehouse_module() && warehouse_count()>0){
				$total_columns_count ++;
			}

			echo "<tr>
					  <td class='text-right text-bold' colspan='$total_columns_count'><b>Total :</b></td>
					  <td class='text-left text-bold'>".number_format($tot_seller_points,2)."</td>
				  </tr>";
		}
		else{
			$total_columns_count=6;
			if(store_module() && is_admin()){
				$total_columns_count ++;
			}
			if(warehouse_module() && warehouse_count()>0){
				$total_columns_count ++;
			}
			echo "<tr>";
			echo "<td class='text-center text-danger' colspan='$total_columns_count'>No Records Found</td>";
			echo "</tr>";
		}
		
	    exit;
	}//report end

	public function show_sales_tax_report(){
		extract($_POST);
		
		$from_date = system_fromatted_date($from_date);
		$to_date = system_fromatted_date($to_date);


		if(warehouse_module() && warehouse_count()>0 && !empty($warehouse_id)){
				$this->db->where("a.warehouse_id",$warehouse_id);
		}

		$this->db->select("a.warehouse_id,a.store_id,");
		$this->db->select("a.id,a.sales_code,a.sales_date,b.customer_name,a.grand_total,b.tax_number");
		$this->db->select("a.tot_discount_to_all_amt");
		$this->db->select("a.round_off");
		
		/*if($customer_id!=''){	
			$this->db->where("a.customer_id=$customer_id");
		}*/
		
		$this->db->where("(a.sales_date>='$from_date' and a.sales_date<='$to_date')");
		
		$this->db->from("db_sales as a");

		if(!empty($store_id)){
			$this->db->where("a.store_id",$store_id);
		}
		
		$this->db->from("db_customers as b");
		$this->db->where("b.`id`= a.`customer_id`");
		
		//echo $this->db->get_compiled_select();exit();
		
		$q1=$this->db->get();
		if($q1->num_rows()>0){
			$i=0;
			$tot_price_per_unit=0;
			$tot_discount_amt=0;
			$tot_tax_amt=0;
			$tot_round_off=0;
			$tot_grand_total=0;
			foreach ($q1->result() as $res1) {

				/*Find Tax Amount*/
				$q2 = $this->db->select("COALESCE(sum(tax_amt),0) as tax_amt")
								->select("COALESCE(sum(price_per_unit),0) as price_per_unit")
								->select("COALESCE(sum(discount_amt),0) as discount_amt")
								->where("sales_id",$res1->id)->get("db_salesitems")->row();
				$tax_amt = $q2->tax_amt;
				$discount_amt = $q2->discount_amt;
				$price_per_unit = $q2->price_per_unit;

				echo "<tr>";
				echo "<td>".++$i."</td>";
				if(store_module() && is_admin()){
					echo "<td>".get_store_name($res1->store_id)."</td>";	
				}

				if($res1->store_id==get_current_store_id()){
				echo "<td><a data-toggle='tooltip' target='_blank' title='View Invoice' href='".base_url("sales/invoice/$res1->id")."'>".$res1->sales_code."</a></td>";
				}
				else{
				echo "<td>".$res1->sales_code."</td>";	
				}

				echo "<td>".show_date($res1->sales_date)."</td>";
				echo "<td>".$res1->customer_name."</td>";
				echo "<td>".$res1->tax_number."</td>";
				echo "<td class='text-right'>".store_number_format($price_per_unit)."</td>";
				echo "<td class='text-right'>".store_number_format($discount_amt)."</td>";
				echo "<td class='text-right'>".store_number_format($tax_amt)."</td>";
				echo "<td class='text-right'>".store_number_format($res1->round_off)."</td>";
				echo "<td class='text-right'>".store_number_format($res1->grand_total)."</td>";
				echo "</tr>";
				$tot_price_per_unit+=$price_per_unit;
				$tot_discount_amt+=$discount_amt;
				$tot_tax_amt+=$tax_amt;
				$tot_round_off+=$res1->round_off;
				$tot_grand_total+=$res1->grand_total;

			}

			$total_columns_count=5;
			if(store_module() && is_admin()){
				$total_columns_count ++;
			}
			
			echo "<tr>
					  <td class='text-right text-bold' colspan='$total_columns_count'><b>Total :</b></td>
					  <td class='text-right text-bold'>".store_number_format($tot_price_per_unit)."</td>
					  <td class='text-right text-bold'>".store_number_format($tot_discount_amt)."</td>
					  <td class='text-right text-bold'>".store_number_format($tot_tax_amt)."</td>
					  <td class='text-right text-bold'>".store_number_format($tot_round_off)."</td>
					  <td class='text-right text-bold'>".store_number_format($tot_grand_total)."</td>
				  </tr>";
		}
		else{
			$total_columns_count=10;
			if(store_module() && is_admin()){
				$total_columns_count ++;
			}
			echo "<tr>";
			echo "<td class='text-center text-danger' colspan='$total_columns_count'>No Records Found</td>";
			echo "</tr>";
		}
		
	    exit;
	}//end

	public function show_purchase_tax_report(){
		extract($_POST);
		
		$from_date = system_fromatted_date($from_date);
		$to_date = system_fromatted_date($to_date);


		if(warehouse_module() && warehouse_count()>0 && !empty($warehouse_id)){
				$this->db->where("a.warehouse_id",$warehouse_id);
		}

		$this->db->select("a.warehouse_id,a.store_id,");
		$this->db->select("a.id,a.purchase_code,a.purchase_date,b.supplier_name,a.grand_total,b.tax_number");
		$this->db->select("a.tot_discount_to_all_amt");
		$this->db->select("a.round_off");
		
		/*if($supplier_id!=''){	
			$this->db->where("a.supplier_id=$supplier_id");
		}*/
		
		$this->db->where("(a.purchase_date>='$from_date' and a.purchase_date<='$to_date')");
		
		$this->db->from("db_purchase as a");

		if(!empty($store_id)){
			$this->db->where("a.store_id",$store_id);
		}
		
		$this->db->from("db_suppliers as b");
		$this->db->where("b.`id`= a.`supplier_id`");
		
		//echo $this->db->get_compiled_select();exit();
		
		$q1=$this->db->get();
		if($q1->num_rows()>0){
			$i=0;
			$tot_price_per_unit=0;
			$tot_discount_amt=0;
			$tot_tax_amt=0;
			$tot_round_off=0;
			$tot_grand_total=0;
			foreach ($q1->result() as $res1) {

				/*Find Tax Amount*/
				$q2 = $this->db->select("COALESCE(sum(tax_amt),0) as tax_amt")
								->select("COALESCE(sum(price_per_unit),0) as price_per_unit")
								->select("COALESCE(sum(discount_amt),0) as discount_amt")
								->where("purchase_id",$res1->id)->get("db_purchaseitems")->row();
				$tax_amt = $q2->tax_amt;
				$discount_amt = $q2->discount_amt;
				$price_per_unit = $q2->price_per_unit;

				echo "<tr>";
				echo "<td>".++$i."</td>";
				if(store_module() && is_admin()){
					echo "<td>".get_store_name($res1->store_id)."</td>";	
				}

				if($res1->store_id==get_current_store_id()){
				echo "<td><a data-toggle='tooltip' target='_blank' title='View Invoice' href='".base_url("purchase/invoice/$res1->id")."'>".$res1->purchase_code."</a></td>";
				}
				else{
				echo "<td>".$res1->purchase_code."</td>";	
				}

				echo "<td>".show_date($res1->purchase_date)."</td>";
				echo "<td>".$res1->supplier_name."</td>";
				echo "<td>".$res1->tax_number."</td>";
				echo "<td class='text-right'>".store_number_format($price_per_unit)."</td>";
				echo "<td class='text-right'>".store_number_format($discount_amt)."</td>";
				echo "<td class='text-right'>".store_number_format($tax_amt)."</td>";
				echo "<td class='text-right'>".store_number_format($res1->round_off)."</td>";
				echo "<td class='text-right'>".store_number_format($res1->grand_total)."</td>";
				echo "</tr>";
				$tot_price_per_unit+=$price_per_unit;
				$tot_discount_amt+=$discount_amt;
				$tot_tax_amt+=$tax_amt;
				$tot_round_off+=$res1->round_off;
				$tot_grand_total+=$res1->grand_total;

			}

			$total_columns_count=5;
			if(store_module() && is_admin()){
				$total_columns_count ++;
			}
			
			echo "<tr>
					  <td class='text-right text-bold' colspan='$total_columns_count'><b>Total :</b></td>
					  <td class='text-right text-bold'>".store_number_format($tot_price_per_unit)."</td>
					  <td class='text-right text-bold'>".store_number_format($tot_discount_amt)."</td>
					  <td class='text-right text-bold'>".store_number_format($tot_tax_amt)."</td>
					  <td class='text-right text-bold'>".store_number_format($tot_round_off)."</td>
					  <td class='text-right text-bold'>".store_number_format($tot_grand_total)."</td>
				  </tr>";
		}
		else{
			$total_columns_count=10;
			if(store_module() && is_admin()){
				$total_columns_count ++;
			}
			echo "<tr>";
			echo "<td class='text-center text-danger' colspan='$total_columns_count'>No Records Found</td>";
			echo "</tr>";
		}
		
	    exit;
	}//end

	public function show_gstr_1_report(){
		extract($_POST);
		
		$from_date = system_fromatted_date($from_date);
		$to_date = system_fromatted_date($to_date);


		if(warehouse_module() && warehouse_count()>0 && !empty($warehouse_id)){
				$this->db->where("a.warehouse_id",$warehouse_id);
		}

		$this->db->select("a.warehouse_id,a.store_id,");
		$this->db->select("a.id,a.sales_code,a.sales_date,b.customer_name,a.grand_total,b.tax_number,a.customer_id,b.state_id");
		$this->db->select("a.tot_discount_to_all_amt");
		$this->db->select("a.round_off");
		
		
		$this->db->where("(a.sales_date>='$from_date' and a.sales_date<='$to_date')");
		
		$this->db->from("db_sales as a");

		if(!empty($store_id)){
			$this->db->where("a.store_id",$store_id);
		}
		
		$this->db->from("db_customers as b");
		$this->db->where("b.`id`= a.`customer_id`");
		
		//echo $this->db->get_compiled_select();exit();
		
		$q1=$this->db->get();
		if($q1->num_rows()>0){
			$i=0;
			$tot_price_per_unit=0;
			$tot_discount_amt=0;
			$tot_tax_amt=0;
			$tot_round_off=0;
			$tot_grand_total=0;

			$tot_cgst_amt=0;
			$tot_sgst_amt=0;
			$tot_igst_amt=0;

			foreach ($q1->result() as $res1) {

				/*Find Tax Amount*/
				$q2 = $this->db->select("COALESCE(sum(tax_amt),0) as tax_amt,tax_id")
								->select("COALESCE(sum(price_per_unit),0) as price_per_unit")
								->select("COALESCE(sum(discount_amt),0) as discount_amt")
								->where("sales_id",$res1->id)->get("db_salesitems")->row();
				$tax_amt = $q2->tax_amt;
				$discount_amt = $q2->discount_amt;
				$price_per_unit = $q2->price_per_unit;


				/*Find Customer State*/
				$customer_state='';
				if(!empty($res1->state_id)){
					$customer_state=$this->db->query("select state from db_states where id='".$res1->state_id."'")->row()->state;
				}

				/*Set GST type*/
				$sgst_amt =$cgst_amt=$igst_amt = 0;

				$total_before_tax = $res1->grand_total - $discount_amt - $tax_amt;

				$total_after_tax = $total_before_tax + $tax_amt;

				

				if(empty($customer_state) || (strtoupper($customer_state) == strtoupper(get_store_details($res1->store_id)->state))){
				    $sgst_amt = $cgst_amt = $tax_amt / 2;
				}else{
				    $sgst_amt = $cgst_amt = 0;
				    $igst_amt = $tax_amt;
				}


				echo "<tr>";
				echo "<td>".++$i."</td>";
				if(store_module() && is_admin()){
					echo "<td>".get_store_name($res1->store_id)."</td>";	
				}

				if($res1->store_id==get_current_store_id()){
				echo "<td><a data-toggle='tooltip' target='_blank' title='View Invoice' href='".base_url("sales/invoice/$res1->id")."'>".$res1->sales_code."</a></td>";
				}
				else{
				echo "<td>".$res1->sales_code."</td>";	
				}

				echo "<td>".show_date($res1->sales_date)."</td>";
				echo "<td>".$res1->customer_name."</td>";
				echo "<td>".$res1->tax_number."</td>";
				echo "<td class='text-right'>".store_number_format($price_per_unit)."</td>";
				echo "<td class='text-right'>".store_number_format($discount_amt)."</td>";
				echo "<td class='text-right'>".get_tax_details($q2->tax_id)->tax_name."</td>";
				echo "<td class='text-right'>".store_number_format($cgst_amt)."</td>";
				echo "<td class='text-right'>".store_number_format($sgst_amt)."</td>";
				echo "<td class='text-right'>".store_number_format($igst_amt)."</td>";

				echo "<td class='text-right'>".store_number_format($res1->round_off)."</td>";
				echo "<td class='text-right'>".store_number_format($res1->grand_total)."</td>";
				echo "</tr>";
				$tot_price_per_unit+=$price_per_unit;
				$tot_discount_amt+=$discount_amt;
				$tot_tax_amt+=$tax_amt;
				$tot_round_off+=$res1->round_off;
				$tot_grand_total+=$res1->grand_total;

				$tot_cgst_amt+=$cgst_amt;
				$tot_sgst_amt+=$sgst_amt;
				$tot_igst_amt+=$igst_amt;


			}

			$total_columns_count=5;
			if(store_module() && is_admin()){
				$total_columns_count ++;
			}
			
			echo "<tr>
					  <td class='text-right text-bold' colspan='$total_columns_count'><b>Total :</b></td>
					  <td class='text-right text-bold'>".store_number_format($tot_price_per_unit)."</td>
					  <td class='text-right text-bold'>".store_number_format($tot_discount_amt)."</td>
					  <td class='text-right text-bold'></td>
					  <td class='text-right text-bold'>".store_number_format($tot_cgst_amt)."</td>
					  <td class='text-right text-bold'>".store_number_format($tot_sgst_amt)."</td>
					  <td class='text-right text-bold'>".store_number_format($tot_igst_amt)."</td>
					  <td class='text-right text-bold'>".store_number_format($tot_round_off)."</td>
					  <td class='text-right text-bold'>".store_number_format($tot_grand_total)."</td>
				  </tr>";
		}
		else{
			$total_columns_count=12;
			if(store_module() && is_admin()){
				$total_columns_count ++;
			}
			echo "<tr>";
			echo "<td class='text-center text-danger' colspan='$total_columns_count'>No Records Found</td>";
			echo "</tr>";
		}
		
	    exit;
	}//end
	
	public function show_gstr_2_report(){
		extract($_POST);
		
		$from_date = system_fromatted_date($from_date);
		$to_date = system_fromatted_date($to_date);


		if(warehouse_module() && warehouse_count()>0 && !empty($warehouse_id)){
				$this->db->where("a.warehouse_id",$warehouse_id);
		}

		$this->db->select("a.warehouse_id,a.store_id,");
		$this->db->select("a.id,a.purchase_code,a.purchase_date,b.supplier_name,a.grand_total,b.tax_number,a.supplier_id,b.state_id");
		$this->db->select("a.tot_discount_to_all_amt");
		$this->db->select("a.round_off");
		
		
		$this->db->where("(a.purchase_date>='$from_date' and a.purchase_date<='$to_date')");
		
		$this->db->from("db_purchase as a");

		if(!empty($store_id)){
			$this->db->where("a.store_id",$store_id);
		}
		
		$this->db->from("db_suppliers as b");
		$this->db->where("b.`id`= a.`supplier_id`");
		
		//echo $this->db->get_compiled_select();exit();
		
		$q1=$this->db->get();
		if($q1->num_rows()>0){
			$i=0;
			$tot_price_per_unit=0;
			$tot_discount_amt=0;
			$tot_tax_amt=0;
			$tot_round_off=0;
			$tot_grand_total=0;

			$tot_cgst_amt=0;
			$tot_sgst_amt=0;
			$tot_igst_amt=0;

			foreach ($q1->result() as $res1) {

				/*Find Tax Amount*/
				$q2 = $this->db->select("COALESCE(sum(tax_amt),0) as tax_amt,tax_id")
								->select("COALESCE(sum(price_per_unit),0) as price_per_unit")
								->select("COALESCE(sum(discount_amt),0) as discount_amt")
								->where("purchase_id",$res1->id)->get("db_purchaseitems")->row();
				$tax_amt = $q2->tax_amt;
				$discount_amt = $q2->discount_amt;
				$price_per_unit = $q2->price_per_unit;


				/*Find supplier State*/
				$supplier_state='';
				if(!empty($res1->state_id)){
					$supplier_state=$this->db->query("select state from db_states where id='".$res1->state_id."'")->row()->state;
				}

				/*Set GST type*/
				$sgst_amt =$cgst_amt=$igst_amt = 0;

				$total_before_tax = $res1->grand_total - $discount_amt - $tax_amt;

				$total_after_tax = $total_before_tax + $tax_amt;

				
				
				if(empty($supplier_state) || (strtoupper($supplier_state) == strtoupper(get_store_details($res1->store_id)->state))){
				    $sgst_amt = $cgst_amt = $tax_amt / 2;
				}else{
				    $sgst_amt = $cgst_amt = 0;
				    $igst_amt = $tax_amt;
				}


				echo "<tr>";
				echo "<td>".++$i."</td>";
				if(store_module() && is_admin()){
					echo "<td>".get_store_name($res1->store_id)."</td>";	
				}

				if($res1->store_id==get_current_store_id()){
				echo "<td><a data-toggle='tooltip' target='_blank' title='View Invoice' href='".base_url("purchase/invoice/$res1->id")."'>".$res1->purchase_code."</a></td>";
				}
				else{
				echo "<td>".$res1->purchase_code."</td>";	
				}

				echo "<td>".show_date($res1->purchase_date)."</td>";
				echo "<td>".$res1->supplier_name."</td>";
				echo "<td>".$res1->tax_number."</td>";
				echo "<td class='text-right'>".store_number_format($price_per_unit)."</td>";
				echo "<td class='text-right'>".store_number_format($discount_amt)."</td>";
				echo "<td class='text-right'>".get_tax_details($q2->tax_id)->tax_name."</td>";
				echo "<td class='text-right'>".store_number_format($cgst_amt)."</td>";
				echo "<td class='text-right'>".store_number_format($sgst_amt)."</td>";
				echo "<td class='text-right'>".store_number_format($igst_amt)."</td>";

				echo "<td class='text-right'>".store_number_format($res1->round_off)."</td>";
				echo "<td class='text-right'>".store_number_format($res1->grand_total)."</td>";
				echo "</tr>";
				$tot_price_per_unit+=$price_per_unit;
				$tot_discount_amt+=$discount_amt;
				$tot_tax_amt+=$tax_amt;
				$tot_round_off+=$res1->round_off;
				$tot_grand_total+=$res1->grand_total;

				$tot_cgst_amt+=$cgst_amt;
				$tot_sgst_amt+=$sgst_amt;
				$tot_igst_amt+=$igst_amt;


			}

			$total_columns_count=5;
			if(store_module() && is_admin()){
				$total_columns_count ++;
			}
			
			echo "<tr>
					  <td class='text-right text-bold' colspan='$total_columns_count'><b>Total :</b></td>
					  <td class='text-right text-bold'>".store_number_format($tot_price_per_unit)."</td>
					  <td class='text-right text-bold'>".store_number_format($tot_discount_amt)."</td>
					  <td class='text-right text-bold'></td>
					  <td class='text-right text-bold'>".store_number_format($tot_cgst_amt)."</td>
					  <td class='text-right text-bold'>".store_number_format($tot_sgst_amt)."</td>
					  <td class='text-right text-bold'>".store_number_format($tot_igst_amt)."</td>
					  <td class='text-right text-bold'>".store_number_format($tot_round_off)."</td>
					  <td class='text-right text-bold'>".store_number_format($tot_grand_total)."</td>
				  </tr>";
		}
		else{
			$total_columns_count=12;
			if(store_module() && is_admin()){
				$total_columns_count ++;
			}
			echo "<tr>";
			echo "<td class='text-center text-danger' colspan='$total_columns_count'>No Records Found</td>";
			echo "</tr>";
		}
		
	    exit;
	}//end


	public function show_customer_orders(){
		extract($_POST);

		
		$within_date = (!empty($within_date)) ? system_fromatted_date($within_date) : '';
		
		/*
			SELECT sales_code, MAX(sales_date),customer_id FROM db_sales WHERE store_id=2 GROUP BY customer_id

			SELECT c.id, c.customer_name, s.sales_code, MAX(s.sales_date) FROM db_customers c 
LEFT JOIN db_sales s ON s.customer_id = c.id WHERE s.store_id=2 GROUP BY s.customer_id



		*/
		/*$this->db->select("c.id, c.customer_name, s.sales_code, MAX(s.sales_date) as sales_date");
		$this->db->from("db_customers as c");
		$this->db->join("db_sales as s","s.customer_id = c.id and s.`sales_status`= 'Final'","left");
	    if(!empty($store_id)){
			$this->db->where("c.store_id",$store_id);
		}
		$this->db->group_by("s.customer_id");
		$this->db->where("a.`sales_status`= 'Final'");*/

		/*if($customer_id!=''){
			$this->db->where("a.customer_id=$customer_id");
		}*/
		/*if(!empty($within_date)){
			$this->db->where("a.sales_date>='$within_date'");
		}*/
	

		$this->db->select("*");
		$this->db->from("db_customers");
	    if(!empty($store_id)){
			$this->db->where("store_id",$store_id);
		}
		if($customer_id!=''){
			$this->db->where("id=$customer_id");
		}
		//echo $this->db->get_compiled_select();exit();
		
		$q1=$this->db->get();

		//print_r($q1);exit;
		if($q1->num_rows()>0){

			$i=0;
			foreach ($q1->result() as $res1) {

				$this->db->select("max(sales_date) as sales_date,sales_code,id")
						->from("db_sales")
						->where("customer_id",$res1->id)
						->group_by("customer_id");
				if(!empty($within_date)){
					$this->db->where("sales_date>='$within_date'");
				}
						//echo $this->db->get_compiled_select();exit();
						$q2 = $this->db->get();
				if($q2->num_rows()>0){
					
						$res2 = $q2->row();

						
						$date_difference = date_difference($res2->sales_date,date("Y-m-d"));

						echo "<tr>";
						echo "<td>".++$i."</td>";
						if(store_module() && is_admin()){
							echo "<td>".get_store_name($res1->store_id)."</td>";	
						}
						echo "<td>".$res1->customer_name."</td>";
						echo "<td>".show_date($res2->sales_date)."</td>";
						
						if($store_id==get_current_store_id()){
						echo "<td><a title='View Invoice' href='".base_url("sales/invoice/$res2->id")."'>".$res2->sales_code."</a></td>";
						}
						else{
						echo "<td>".$res2->sales_code."</td>";	
						}
						echo "<td>".$date_difference."</td>";
						echo "</tr>";
				}
				

			}

		}
		else{
			$total_columns_count=5;
			if(store_module() && is_admin()){
				$total_columns_count ++;
			}
			if(warehouse_module() && warehouse_count()>0){
				$total_columns_count ++;
			}
			echo "<tr>";
			echo "<td class='text-center text-danger' colspan='$total_columns_count'>No Records Found</td>";
			echo "</tr>";
		}
		
	    exit;
	}

}
