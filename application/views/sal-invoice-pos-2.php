<!DOCTYPE html>
<html>
<head>
<!-- TABLES CSS CODE -->
<?php include"comman/code_css.php"; ?>
<style type="text/css">
	body{
		font-family: arial;
		font-size: 13px;
		font-weight: bold;
		padding-top:15px;
	}

	@media print {
        .no-print { display: none; }
    }
    .text-bold{
    	font-weight: bold;
    }
</style>
</head>
<body onload="window.print();">
	<?php
	$CI =& get_instance();
	
    

  	$q3=$this->db->query("SELECT b.store_id, b.created_by, a.customer_name,a.mobile,a.phone,a.gstin,a.tax_number,a.email,
                           a.opening_balance,a.country_id,a.state_id,
                           a.postcode,a.address,b.sales_date,b.created_time,b.reference_no,
                           b.sales_code,b.sales_note,
                           coalesce(b.grand_total,0) as grand_total,
                           coalesce(b.subtotal,0) as subtotal,
                           coalesce(b.paid_amount,0) as paid_amount,
                           coalesce(b.other_charges_input,0) as other_charges_input,
                           other_charges_tax_id,
                           coalesce(b.other_charges_amt,0) as other_charges_amt,
                           discount_to_all_input,
                           b.discount_to_all_type,
                           coalesce(b.tot_discount_to_all_amt,0) as tot_discount_to_all_amt,
                           coalesce(b.round_off,0) as round_off,
                           b.payment_status

                           FROM db_customers a,
                           db_sales b 
                           WHERE 
                           a.`id`=b.`customer_id` AND 
                           b.`id`='$sales_id' 
                           ");
                          
    
    $res3=$q3->row();
    $customer_name=$res3->customer_name;
    $customer_mobile=$res3->mobile;
    $customer_phone=$res3->phone;
    $customer_email=$res3->email;
    $customer_country=$res3->country_id;
    $customer_state=$res3->state_id;
    $customer_address=$res3->address;
    $customer_postcode=$res3->postcode;
    $customer_gst_no=$res3->gstin;
    $customer_tax_number=$res3->tax_number;
    $customer_opening_balance=$res3->opening_balance;
    $sales_date=show_date($res3->sales_date);
    $reference_no=$res3->reference_no;
    $created_time=show_time($res3->created_time);
    $sales_code=$res3->sales_code;
    $sales_note=$res3->sales_note;

    
    $subtotal=$res3->subtotal;
    $grand_total=$res3->grand_total;
    $other_charges_input=$res3->other_charges_input;
    $other_charges_tax_id=$res3->other_charges_tax_id;
    $other_charges_amt=$res3->other_charges_amt;
    $paid_amount=$res3->paid_amount;
    $discount_to_all_input=$res3->discount_to_all_input;
    $discount_to_all_type=$res3->discount_to_all_type;
    //$discount_to_all_type = ($discount_to_all_type=='in_percentage') ? '%' : 'Fixed';
    $tot_discount_to_all_amt=$res3->tot_discount_to_all_amt;
    $round_off=$res3->round_off;
    $payment_status=$res3->payment_status;
    
    if($discount_to_all_input>0){
    	$str="($discount_to_all_input%)";
    }else{
    	$str="(Fixed)";
    }

    if(!empty($customer_country)){
      $customer_country = $this->db->query("select country from db_country where id='$customer_country'")->row()->country;  
    }
    if(!empty($customer_state)){
      $customer_state = $this->db->query("select state from db_states where id='$customer_state'")->row()->state;  
    }

    $q1=$this->db->query("select * from db_store where id=".$res3->store_id." ");
    $res1=$q1->row();
    $store_name		=$res1->store_name;
    $company_mobile		=$res1->mobile;
    $company_phone		=$res1->phone;
    $company_email		=$res1->email;
    $company_country	=$res1->country;
    $company_state		=$res1->state;
    $company_city		=$res1->city;
    $company_address	=$res1->address;
    $company_postcode	=$res1->postcode;
    $company_gst_no		=$res1->gst_no;//Goods and Service Tax Number (issued by govt.)
    $company_vat_number	=$res1->vat_no;//Goods and Service Tax Number (issued by govt.)
    $store_logo=(!empty($res1->store_logo)) ? $res1->store_logo : store_demo_logo();
    
    ?>
	<table width="95%" align="center">
		<tr>
			<td>
				<table width="100%">
					<tr>
						<td width="60%">
							<img src="<?= base_url($store_logo);?>" width="60%" height="auto">
						</td>
						<td>
							<b>
								<?= $this->lang->line('date').":".$sales_date; ?> <br>
								<?= $this->lang->line('time').":".$created_time; ?>
							</b>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr><td align="center"><strong><?= $this->lang->line('invoice_number'); ?> : <?= $sales_code; ?></strong></td></tr>
		<tr><td><h4><?= $this->lang->line('ship_from'); ?></h4></td></tr>
		<tr>
			<td>
				<table width="100%" border="1" cellpadding="0">
					<tr>
						<td width="55%" style="padding:5px;">
							<span>													 
				                <strong><?= $store_name; ?></strong><br>
				                	<?php echo (!empty(trim($company_address))) ? $this->lang->line('company_address')."".$company_address."<br>" : '';?> 
						            <?= $company_city; ?>
						            <?php echo (!empty(trim($company_postcode))) ? "-".$company_postcode : '';?>
						            <br>
						            <?php echo ( gst_number() && !empty(trim($company_gst_no))) ? $this->lang->line('gst_number').": ".$company_gst_no."<br>" : '';?>
						            <?php echo (!empty(trim($company_vat_number))) ? $this->lang->line('vat_number').": ".$company_vat_number."<br>" : '';?>
						            <?php if(!empty(trim($company_mobile))) 
						            		{ 
						            			echo $this->lang->line('phone').": ".$company_mobile;
						            			if(!empty($company_phone)){
						            				echo ",".$company_phone;
						            			}
						            			echo "<br>";
						            		}

						            ?> 
						            <?php echo $this->lang->line('seller').": ".ucfirst($res3->created_by);?>
							</span>
						</td>
						<td align="center" style="padding:5px;">
							<div style="display:inline-block;vertical-align:middle;line-height:16px !important;">	
								<img class="center-block" style=" width: 100%; opacity: 1.0" src="<?php echo base_url();?>barcode/<?php echo $sales_code;?>">
							</div>
						</td>
					</tr>
					
				</table>
			</td>
		</tr>
		<tr>
			<td>
				<table width="100%" cellpadding="0">
					<tr style="border-left: 1px solid;border-bottom: 1px solid;border-right: 1px solid;">
						<td width="25%" style="border-right: 1px solid;padding:5px;">
							<h1><?= $this->lang->line('to'); ?>:</h1>
						</td>
						<td style="padding:5px;" >
							<span>													 
				                <strong><?php echo  $customer_name; ?></strong><br>
					            <?php 
					              if(!empty($customer_address)){
					                echo $customer_address;
					              }
					              if(!empty($customer_country)){
					                echo $customer_country;
					              }
					              if(!empty($customer_state)){
					                echo ",".$customer_state;
					              }
					              if(!empty($customer_city)){
					                echo ",".$customer_city;
					              }
					              if(!empty($customer_postcode)){
					                echo "-".$customer_postcode;
					              }
					            ?>
					            <br>
					            <?php echo (!empty(trim($customer_mobile))) ? $this->lang->line('mobile').": ".$customer_mobile."<br>" : '';?>
					            <?php echo (!empty(trim($customer_phone))) ? $this->lang->line('phone').": ".$customer_phone."<br>" : '';?>
					            <?php echo (!empty(trim($customer_email))) ? $this->lang->line('email').": ".$customer_email."<br>" : '';?>
					            <?php echo (!empty(trim($customer_gst_no))) ? $this->lang->line('gst_number').": ".$customer_gst_no."<br>" : '';?>
					            <?php echo (!empty(trim($customer_tax_number))) ? $this->lang->line('tax_number').": ".$customer_tax_number."<br>" : '';?>
							</span>
						</td>
						
					</tr>
					
				</table>
			</td>
		</tr>

		<?php 
		$q4=$this->db->query("select payment_type from db_salespayments where sales_id=".$sales_id);
	    if($q4->num_rows()>0){
	    	echo "<tr><td>";
	    	echo $this->lang->line('payment_type').":";
	    	$info = array();
	    	foreach ($q4->result() as $res) {
	    		$info[]=$res->payment_type." ";
	    		
	    	}
	    	echo implode('/', $info);
	    	echo "</td></tr>";
	    }
		?>
		
		
		<tr>
			<td>

				<table width="100%" cellpadding="0" border="1" cellspacing="0"  >
					<thead>
					<tr>
						<th class="text-uppercase text-center" style="padding-left: 2px; padding-right: 2px;"><?= $this->lang->line('description'); ?></th>
						<th class="text-uppercase" style="text-align: center;padding-left: 2px; padding-right: 2px;"><?= $this->lang->line('quantity'); ?></th>
						<th class="text-uppercase" style="text-align: center;padding-left: 2px; padding-right: 2px;"><?= $this->lang->line('price'); ?></th>
					</tr>
					</thead>
					<tbody>
						<?php
			              $i=0;
			              $tot_qty=0;
			              $subtotal=0;
			              $tax_amt=0;
			              $q2=$this->db->query("select b.item_name,a.sales_qty,a.unit_total_cost,a.price_per_unit,a.tax_amt,c.tax,a.total_cost from db_salesitems a,db_items b,db_tax c where c.id=a.tax_id and b.id=a.item_id and a.sales_id='$sales_id'");
			              foreach ($q2->result() as $res2) {
			                  echo "<tr>";  
			                  echo "<td style='padding-left: 2px; padding-right: 2px;'>".$res2->item_name."</td>";
			                  echo "<td style='text-align: center;padding-left: 2px; padding-right: 2px;'>".$res2->sales_qty."</td>";
			                  echo "<td style='text-align: right;padding-left: 2px; padding-right: 2px;' >".number_format(($res2->total_cost),2,'.','')."</td>";
			                  echo "</tr>";  
			                  //$tot_qty+=$res2->sales_qty;
			                  $subtotal+=($res2->total_cost);
			                  $tax_amt+=$res2->tax_amt;
			              }
			              $before_tax = $subtotal-$tax_amt;
			              ?>
					
				   </tbody>
					<tfoot>
					 
					
					<tr>
						<td style=" padding-left: 2px; padding-right: 2px;" align="right"><?= $other_charges_amt; ?></td>
					</tr>
					<tr>
						<td style=" padding-left: 2px; padding-right: 2px;" colspan="2" align="right"><?= $this->lang->line('total'); ?></td>
						<td style=" padding-left: 2px; padding-right: 2px;" align="right"><?= $grand_total; ?></td>
					</tr>
					<tr>
						<td colspan="3">
							<span >Amount in words: <i style='font-weight:bold;'><?= no_to_words(round($grand_total)); ?> Only</i></span>
						</td>
					</tr>
					
					</tfoot>
				</table>
			</td>
		</tr>
	</table>
	<center >
  <div class="row no-print" style="padding-top: 15px;">
  <div class="col-md-12">
  <div class="col-md-2 col-md-offset-5 col-xs-4 col-xs-offset-4 form-group">
    <button type="button" id="" class="btn btn-block btn-success btn-xs" onclick="window.print();" title="Print">Print</button>
    <?php if(isset($_GET['redirect'])){ ?>
		<a href="<?= base_url().$_GET['redirect'];?>"><button type="button" class="btn btn-block btn-danger btn-xs" title="Back">Back</button></a>
	<?php } ?>
   </div>
   </div>
   </div>

</center>
</body>
</html>