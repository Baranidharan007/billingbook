<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sales_payments_model extends CI_Model {

	//Datatable start
	var $table = 'db_salespayments as a';
	var $column_order = array( 
							'a.id',
							'a.payment_date',
							'a.payment_code',
							'a.customer_id',
							'a.sales_id',
							'a.payment',
							'a.payment_type',
							'a.created_by',
							'a.store_id',
							'a.cheque_number',
							'a.cheque_period',
							'a.cheque_status',
							'a.payment_note',
							); //set column field database for datatable orderable
	var $column_search = array( 
							'a.id',
							'a.payment_date',
							'a.payment_code',
							'a.customer_id',
							'a.sales_id',
							'a.payment',
							'a.payment_type',
							'a.created_by',
							'a.store_id',
							'a.cheque_number',
							'a.cheque_period',
							'a.cheque_status',
							'a.payment_note',
							);//set column field database for datatable searchable 
	var $order = array('a.id' => 'desc'); // default order  

	public function __construct()
	{
		parent::__construct();
		$CI =& get_instance();
	}

	private function _get_datatables_query()
	{
		
		$this->db->select($this->column_order);
		$this->db->from($this->table);
		
		//if(!is_admin()){
	      $this->db->where("a.store_id",get_current_store_id());
	    //}
	      if(!is_admin()){
	      	if($this->session->userdata('role_id')!='2'){
	      		if(!permissions('show_all_users_sales_invoices')){
	      			$this->db->where("upper(a.created_by)",strtoupper($this->session->userdata('inv_username')));
	      		}
	      	}
	      }

	     $payment_type_search = $this->input->post('payment_type_search');
	     $cheque_status_search = $this->input->post('cheque_status_search');
	     
	     if(!empty($payment_type_search)){
	     	$this->db->where("upper(payment_type)",strtoupper($payment_type_search));
	     }
	     if(!empty($cheque_status_search)){
	     	$this->db->where("upper(cheque_status)",strtoupper($cheque_status_search));
	     }
	     
	   // echo $this->db->get_compiled_select();exit();
		$i = 0;
	
		foreach ($this->column_search as $item) // loop column 
		{
			if($_POST['search']['value']) // if datatable send POST for search
			{
				
				

				if($i===0) // first loop
				{
					$this->db->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.

					$this->db->like($item, $_POST['search']['value']);

				}
				else
				{
					$this->db->or_like($item, $_POST['search']['value']);
				}

				


				if(count($this->column_search) - 1 == $i) //last loop
					$this->db->group_end(); //close bracket
			}
			$i++;
		}
		
		if(isset($_POST['order'])) // here order processing
		{
			$this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		} 
		else if(isset($this->order))
		{
			$order = $this->order;
			$this->db->order_by(key($order), $order[key($order)]);
		}
	}

	function get_datatables()
	{
		$this->_get_datatables_query();
		if($_POST['length'] != -1)
		$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	function count_filtered()
	{
		$this->_get_datatables_query();
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all()
	{
		$this->db->where("store_id",get_current_store_id());
		$this->db->from($this->table);
		return $this->db->count_all_results();
	}
	//Datatable end

	public function xss_html_filter($input){
		return $this->security->xss_clean(html_escape($input));
	}



	
	public function delete_payment($payment_id){
        $this->db->trans_begin();

        //ACCOUNT RESET
		$reset_accounts = $this->db->select("debit_account_id,credit_account_id")
									->where("ref_salespayments_id in ($payment_id)")
									->group_by("debit_account_id,credit_account_id")
									->get("ac_transactions");
		//ACCOUNT RESET END

		$salespayments = $this->db->query("select sales_id,customer_id from db_salespayments where id=$payment_id")->row();
		$sales_id = $salespayments->sales_id;
		$customer_id = $salespayments->customer_id;

		$q1=$this->db->query("delete from db_salespayments where id='$payment_id'");
		if(!$q1){
			return "failed";
		}
		$q2=$this->update_sales_payment_status($sales_id);
		if(!$q2){
			return "failed";
		}

		//ACCOUNT RESET
        if($reset_accounts->num_rows()>0){
        	foreach ($reset_accounts->result() as $res1) {
        		if(!update_account_balance($res1->debit_account_id)){
					return 'failed';
				}

				if(!update_account_balance($res1->credit_account_id)){
					return 'failed';
				}

        	}
        }
        //ACCOUNT RESET END

        if(!set_customer_tot_advance($customer_id)){
        	return 'failed';
        }
		$this->db->trans_commit();
		return "success";
		
	}

	public function show_cheque_payments_modal($payment_id){
		 $q3 = $this->db->select("sales_id,cheque_status")->where("id",$payment_id)->get("db_salespayments")->row();

		 $sales_id = $q3->sales_id;
		 $cheque_status = $q3->cheque_status;
		$q1=$this->db->query("select * from db_sales where id=$sales_id");
		$res1=$q1->row();
		$customer_id = $res1->customer_id;
		$q2=$this->db->query("select * from db_customers where id=$customer_id");
		$res2=$q2->row();

		$customer_name=$res2->customer_name;
	    $customer_mobile=$res2->mobile;
	    $customer_phone=$res2->phone;
	    $customer_email=$res2->email;
	    $customer_country=$res2->country_id;
	    $customer_state=$res2->state_id;
	    $customer_address=$res2->address;
	    $customer_postcode=$res2->postcode;
	    $customer_gst_no=$res2->gstin;
	    $customer_tax_number=$res2->tax_number;
	    $customer_opening_balance=$res2->opening_balance;
	    $customer_tot_advance=$res2->tot_advance;

	    $sales_date=$res1->sales_date;
	    $reference_no=$res1->reference_no;
	    $sales_code=$res1->sales_code;
	    $sales_note=$res1->sales_note;
	    $grand_total=$res1->grand_total;
	    $paid_amount=$res1->paid_amount;
	    $due_amount =$grand_total - $paid_amount;

	    if(!empty($customer_country)){
	      $customer_country = $this->db->query("select country from db_country where id='$customer_country'")->row()->country;  
	    }
	    if(!empty($customer_state)){
	      $customer_state = $this->db->query("select state from db_states where id='$customer_state'")->row()->state;  
	    }

		?>
		<div class="modal fade" id="pay_now">
		  <div class="modal-dialog ">
		    <div class="modal-content">
		      <div class="modal-header header-custom">
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		          <span aria-hidden="true">&times;</span></button>
		        <h4 class="modal-title text-center"><?= $this->lang->line('payments'); ?></h4>
		      </div>
		      <div class="modal-body">
		        
		    <div class="row">
		      <div class="col-md-12">
		      	<div class="row invoice-info">
			        <div class="col-sm-4 invoice-col">
			          <?= $this->lang->line('customer_details'); ?>
			          <address>
			            <strong><?php echo  $customer_name; ?></strong><br>
			            <?php echo (!empty(trim($customer_mobile))) ? $this->lang->line('mobile').": ".$customer_mobile."<br>" : '';?>
			            <?php echo (!empty(trim($customer_phone))) ? $this->lang->line('phone').": ".$customer_phone."<br>" : '';?>
			            <?php echo (!empty(trim($customer_email))) ? $this->lang->line('email').": ".$customer_email."<br>" : '';?>
			            <?php echo (!empty(trim($customer_gst_no))) ? $this->lang->line('gst_number').": ".$customer_gst_no."<br>" : '';?>
			            <?php echo (!empty(trim($customer_tax_number))) ? $this->lang->line('tax_number').": ".$customer_tax_number."<br>" : '';?>
			            
			          </address>
			        </div>
			        <!-- /.col -->
			        <div class="col-sm-4 invoice-col">
			          <?= $this->lang->line('sales_details'); ?>
			          <address>
			            <b><?= $this->lang->line('invoice'); ?> #<?php echo  $sales_code; ?></b><br>
			            <b><?= $this->lang->line('date'); ?> :<?php echo show_date($sales_date); ?></b><br>
			            <b><?= $this->lang->line('grand_total'); ?> :<?php echo $grand_total; ?></b><br>
			          </address>
			        </div>
			        <!-- /.col -->
			       
			        <div class="col-sm-4 invoice-col">
			          <b><?= $this->lang->line('paid_amount'); ?> :<span><?php echo number_format($paid_amount,2,'.',''); ?></span></b><br>
			          <b><?= $this->lang->line('due_amount'); ?> :<span id='due_amount_temp'><?php echo number_format($due_amount,2,'.',''); ?></span></b><br>
			         
			        </div>
			        <!-- /.col -->
			      </div>
			      <!-- /.row -->
		      </div>
		      <div class="col-md-12">
		        <div>
		        <input type="hidden" name="payment_row_count" id='payment_row_count' value="1">
		        <div class="col-md-12  payments_div">
		          <div class="box box-solid bg-gray">
		            <div class="box-body">
			           

		              <div class="row">
		         		
		             
		                <div class="col-md-6">
		                  <div class="">
		                    <label for="cheque_status"><?= $this->lang->line('cheque_status'); ?></label>
		                    <select class="form-control" id='cheque_status' name="cheque_status">
		                     <?=get_cheque_status_select_list($cheque_status)?>
		                    </select>
		                    <span id="cheque_status_msg" style="display:none" class="text-danger"></span>
		                  </div>
		                </div>
		               

		                



		            <div class="clearfix"></div>
		        </div>  

		        </div>
		        </div>
		      </div><!-- col-md-12 -->
		    </div>
		      </div><!-- col-md-9 -->
		      <!-- RIGHT HAND -->
		    </div>
		      </div>
		      <div class="modal-footer">
		      	<input type="hidden" id="customer_id" value="<?=$customer_id?>">
		        <button type="button" class="btn btn-default btn-lg" data-dismiss="modal">Close</button>
		        <button type="button" onclick="update_cheque_payment(<?=$payment_id;?>)" class="btn bg-green btn-lg place_order btn-lg payment_save">Update<i class="fa  fa-check "></i></button>
		      </div>
		    </div>
		    <!-- /.modal-content -->
		  </div>
		  <!-- /.modal-dialog -->
		</div>
		<?php
	}

	public function update_cheque_payment(){
		extract($this->xss_html_filter(array_merge($this->data,$_POST,$_GET)));
		//print_r($this->xss_html_filter(array_merge($this->data,$_POST,$_GET)));exit();
    	$q1 = $this->db->query("update db_salespayments set cheque_status='".$cheque_status."' where id=$payment_id");
    	if(!$q1){
    		return "failed";
    	}
    	return "success";
	}
	
	
}
