<!DOCTYPE html>
<html>
<title><?= $page_title;?>- GST Format</title>
<head>
<link rel='shortcut icon' href='<?php echo $theme_link; ?>images/favicon.ico' />

<style>

table, th, td {
    border: 0.5pt solid #0070C0;
    border-collapse: collapse;   

}
th, td {
    /*padding: 5px;*/
    text-align: left;   
    vertical-align:top 
}
body{
  word-wrap: break-word;
  font-family:  'sans-serif','Arial';
  font-size: 11px;
  /*height: 210mm;*/
}
.style_hidden{
  border-style: hidden;
}
.fixed_table{
  table-layout:fixed;
}
.text-center{
  text-align: center;
}
.text-left{
  text-align: left;
}
.text-right{
  text-align: right;
}
.text-bold{
  font-weight: bold;
}
.bg-sky{
  background-color: #E8F3FD;
}
@page { size: A4 margin: 5px; }
body { margin: 5px; }

 #clockwise {
       rotate: 90;
    }

    #counterclockwise {
       rotate: -90;
    }
</style>
</head>
<body onload=""><!-- window.print(); -->
<?php
    
    $store_rec = get_store_details();
    $sales_rec = get_sales_details($sales_id);
    $customer_rec = get_customer_details($sales_rec->customer_id);
    
    $state_rec = (!empty($customer_rec->state_id)) ? get_state_details($customer_rec->state_id) : '';
    

    $store_logo=(!empty($store_rec->store_logo)) ? $store_rec->store_logo : store_demo_logo();

    //Customer state name
    $customer_state_name = (!empty($state_rec)) ? $state_rec->state : $store_rec->state;
    

    $shipping_country='';
    $shipping_state='';
    $shipping_city='';
    $shipping_address='';
    $shipping_postcode='';
    if(!empty($customer_rec->shippingaddress_id)){
        $Q2 = $this->db->select("c.country,s.state,a.city,a.postcode,a.address")
                        ->where("a.id",$customer_rec->shippingaddress_id)
                        ->from("db_shippingaddress a")
                        ->join("db_country c","c.id = a.country_id",'left')
                        ->join("db_states s","s.id = a.state_id",'left')
                        ->get();                    
        if($Q2->num_rows()>0){
          $shipping_country=$Q2->row()->country;
          $shipping_state=$Q2->row()->state;
          $shipping_city=$Q2->row()->city;
          $shipping_address=$Q2->row()->address;
          $shipping_postcode=$Q2->row()->postcode;
        }
      }

    ?>

<caption>
      <center>
        <span style="font-size: 18px;text-transform: uppercase;">
          Tax Invoice
        </span>
      </center>
</caption>

<table autosize="1" style="overflow: wrap" id='mytable' align="center" width="100%" height='100%'  cellpadding="0" cellspacing="0"  >
<!-- <table align="center" width="100%" height='100%'   > -->
    <thead>

      <tr>
        <th colspan="16">
          <table width="100%" height='100%' class="style_hidden fixed_table">
              <tr>
                <!-- First Half -->
                <td colspan="2">
                  <img src="<?= base_url($store_logo);?>" width='100%'>
                </td>

                <td colspan=5>
                <span style="font-size: 12px;">
                  <b><?php echo $store_rec->store_name; ?></b><br/>
                    <?php echo $this->lang->line('address')." : ".$store_rec->address; ?><br/>
                    <?php echo $this->lang->line('mobile')." : ".$store_rec->mobile; ?><br/>
                   <!--  <?php echo $store_rec->country; ?><br/> -->
                    
                    <?php echo (!empty(trim($store_rec->email))) ? $this->lang->line('email')." : ".$store_rec->email."<br>" : '';?>
                    <?php echo (!empty(trim($store_rec->gst_no))) ? $this->lang->line('gst_number')." : ".$store_rec->gst_no."<br>" : '';?>
                    <!-- <?php echo (!empty(trim($store_rec->vat_no))) ? $this->lang->line('tax_number')." : ".$store_rec->vat_no."<br>" : '';?> -->
                  </span>
                </td>

                <!-- Second Half -->
                <td colspan="6" rowspan="1">
                  <span>
                    <table style="width: 100%;" class="style_hidden fixed_table">
                    
                        <tr>
                          <td colspan="4">
                            Invoice No.<br>
                            <span style="font-size: 100%;">
                              <b><?php echo "$sales_rec->sales_code"; ?></b>
                            </span>
                          </td>
                          <td colspan="4">
                            Dated<br>
                            <span style="font-size: 12px;">
                              <b><?php echo show_date($sales_rec->sales_date); ?></b>
                            </span>
                          </td>
                        </tr>
                        <tr>
                          <td colspan="8">
                            Reference No.<br>
                            <span style="font-size: 12px;">
                              <b><?php echo "$sales_rec->reference_no"; ?></b>
                            </span>
                          </td>
                          
                        </tr>
                        
                        <tr>
                          <td colspan="8">
                            <span>
                                <b><?= $this->lang->line('bank_details'); ?></b><br/>
                              </span>
                              <span style="font-size: 12px;">
                                  <?= nl2br($store_rec->bank_details);  ?><br><br/>
                                 
                                </span>
                                 
                          </td>
                        </tr>
                        

                    
                    </table>
                  </span>
                </td>
              </tr>

              <tr>
                <!-- Bottom Half -->
                <td colspan="7">
                  <b><?= $this->lang->line('customer_details'); ?></b><br/>
                  <span style="font-size: 12px;">
                      <?php echo $this->lang->line('name')." : ".$customer_rec->customer_name; ?><br/>
                        <?php echo (!empty(trim($customer_rec->address))) ? $this->lang->line('address')." : ".$customer_rec->address."<br>" : '';?>
                        <?php echo (!empty(trim($customer_rec->gstin))) ? $this->lang->line('gstin')." : ".$customer_rec->gstin."<br>" : '';?>
                        <?php 
                                if(!empty($customer_rec->customer_mobile)){
                                  echo $customer_rec->customer_mobile;
                                }
                                
                                /*
                                if(!empty($customer_rec->state_id)){
                                  echo ",".$customer_rec->state_id;
                                }
                                if(!empty($customer_rec->city)){
                                  echo ",".$customer_rec->city;
                                }
                                if(!empty($customer_rec->postcode)){
                                  echo "-".$customer_rec->postcode;
                                }*/
                              ?>
                              
                        
                        <?php echo (!empty(trim($customer_rec->email))) ? $this->lang->line('email')." : ".$customer_rec->email."<br>" : '';?>
                        <!--<?php echo (!empty(trim($customer_rec->tax_number))) ? $this->lang->line('tax_number').": ".$customer_rec->tax_number."<br>" : '';?> -->
                  </span>
                </td>

                <td colspan="6">
                  <b><?= $this->lang->line('shipping_address'); ?></b><br/>
                  <span style="font-size: 12px;">
                      <?php echo $this->lang->line('name')." : ".$customer_rec->customer_name; ?><br/>
                        <?php echo (!empty(trim($customer_rec->address))) ? $this->lang->line('address')." : ".$customer_rec->address."<br>" : '';?>
                       <?php 
                                    echo $this->lang->line('address') .":".$shipping_address;
                                    echo "<br>".$this->lang->line('country') .":".$shipping_country;
                                    echo ", ".$this->lang->line('state') .":".$shipping_state;
                                    echo "<br>".$this->lang->line('city') .":".$shipping_city;
                                    echo ", ".$this->lang->line('postcode') .":".$shipping_postcode;

                                  ?>
                  </span>
                </td>

                
              </tr>




            
          </table>
      </th>
      </tr>

      <tr>
        <td colspan="16">&nbsp; </td>
      </tr>
      <tr class="bg-sky"><!-- Colspan 10 -->
        <th colspan='1' class="text-center"><?= $this->lang->line('sl_no'); ?></th>
        <th colspan='5' class="text-center" ><?= $this->lang->line('description_of_goods'); ?></th>
        <th colspan='2' class="text-center"><?= $this->lang->line('hsn/sac'); ?></th>
        <th colspan='1' class="text-center"><?= $this->lang->line('gst_rate'); ?></th>
        <th colspan='1' class="text-center"><?= $this->lang->line('qty'); ?></th>
        <th colspan='2' class="text-center"><?= $this->lang->line('rate'); ?></th>
        <th colspan='2' class="text-center"><?= $this->lang->line('discount'); ?></th>
        <th colspan='2' class="text-center"><?= $this->lang->line('amount'); ?></th>
      </tr>
  </thead>
<tbody>
  <tr>
    <td colspan='16'>
 <?php
              $i=0;
              $tot_qty=0;
              $tot_sales_price=0;
              $tot_tax_amt=0;
              $tot_discount_amt=0;
              $tot_unit_total_cost=0;
              $tot_total_cost=0;
              $tot_before_tax=0;
              /*$q2=$this->db->query("SELECT a.description,c.item_name, a.sales_qty,
                                  a.price_per_unit, b.tax,b.tax_name,a.tax_amt,
                                  a.discount_input,a.discount_amt, a.unit_total_cost,
                                  a.total_cost , d.unit_name,c.sku,c.hsn
                                  FROM 
                                  db_salesitems AS a,db_tax AS b,db_items AS c , db_units as d
                                  WHERE 
                                  d.id = c.unit_id and
                                  c.id=a.item_id AND b.id=a.tax_id AND a.sales_id='$sales_id'");*/


              $this->db->select(" a.description,c.item_name, a.sales_qty,
                                  a.price_per_unit, b.tax,b.tax_name,a.tax_amt,
                                  a.discount_input,a.discount_amt, a.unit_total_cost,
                                  a.total_cost , d.unit_name,c.sku,c.hsn
                              ");
              $this->db->where("a.sales_id",$sales_id);
              $this->db->from("db_salesitems a");
              $this->db->join("db_tax b","b.id=a.tax_id","left");
              $this->db->join("db_items c","c.id=a.item_id","left");
              $this->db->join("db_units d","d.id = c.unit_id","left");
              $q2=$this->db->get();

              foreach ($q2->result() as $res2) {
                  $discount = (empty($res2->discount_input)||$res2->discount_input==0)? '0':$res2->discount_input."%";
                  $discount_amt = (empty($res2->discount_amt)||$res2->discount_input==0)? '0':$res2->discount_amt."";
                  $before_tax=$res2->price_per_unit;// * $res2->sales_qty;
                  $tot_cost_before_tax=$res2->total_cost;//$before_tax * $res2->sales_qty;
                  echo "<tr>";  
                  echo "<td colspan='1' class='text-center'>".++$i."</td>";
                  echo "<td colspan='5'>";
                  echo $res2->item_name;
                  echo (!empty($res2->description)) ? "<br><i>[".nl2br($res2->description)."]</i>" : '';
                  echo "</td>";
                  echo "<td colspan='2' class='text-center'>".$res2->hsn."</td>";
                  
                  echo "<td colspan='1' class='text-right'>".store_number_format($res2->tax)."%</td>";
                  echo "<td colspan='1' class='text-center'>".$res2->sales_qty."</td>";
                  //echo "<td style='text-align: right;'>".store_number_format($res2->tax_amt)."</td>";
                  //echo "<td style='text-align: right;'>".store_number_format($discount)."</td>";
                  //echo "<td style='text-align: right;'>".store_number_format($discount_amt)."</td>";
 
                  echo "<td colspan='2' class='text-right'>".store_number_format($before_tax)."</td>";
                 echo "<td colspan='2' class='text-right'>".store_number_format($discount)."</td>";
                  //echo "<td class='text-right'>".store_number_format($res2->price_per_unit)."</td>";
                  
                  echo "<td colspan='2' class='text-right'>".store_number_format($tot_cost_before_tax)."</td>";
                  echo "</tr>";  
                  $tot_qty +=$res2->sales_qty;
                  $tot_sales_price +=$res2->price_per_unit;
                  $tot_tax_amt +=$res2->tax_amt;
                  $tot_discount_amt +=$res2->discount_amt;
                  $tot_unit_total_cost +=$res2->unit_total_cost;
                  $tot_before_tax +=$before_tax;
                  $tot_total_cost +=$tot_cost_before_tax;
              }
              ?>
      </td>
  </tr>
  </tbody>
<tfoot>
  <?php
                        $tot_price_before_tax = $tot_price_after_tax = $tot_cgst_amt =$tot_sgst_amt=$tot_sgst_amt=$tot_igst_amt = 0;


                        /*$q2=$this->db->query(" SELECT c.item_name,  COALESCE(SUM(a.price_per_unit),0) AS price_before_tax, 
                                               b.tax,b.tax_name,
                                               COALESCE(SUM(a.tax_amt),0) AS sum_of_tax_amt,
                                               COALESCE(SUM(a.total_cost),0) AS price_after_tax,c.tax_type,
                                               c.sku,c.hsn
                                               FROM 
                                               db_salesitems AS a,db_tax AS b,db_items AS c , db_units AS d 
                                               WHERE 
                                               d.id = c.unit_id AND c.id=a.item_id AND b.id=a.tax_id AND a.sales_id='$sales_id' GROUP BY b.id");
*/
                        $this->db->select(" c.item_name,
                                            COALESCE(SUM(a.price_per_unit),0) AS price_before_tax, 
                                            b.tax,
                                            b.tax_name,
                                            COALESCE(SUM(a.tax_amt),0) AS sum_of_tax_amt,
                                            COALESCE(SUM(a.total_cost),0) AS price_after_tax,
                                            c.tax_type,
                                            c.sku,c.hsn
                                            ");

                        $this->db->where("a.sales_id",$sales_id);
                        $this->db->from("db_salesitems a");
                        $this->db->join("db_tax b","b.id=a.tax_id","left");
                        $this->db->join("db_items c","c.id=a.item_id","left");
                        $this->db->join("db_units d","d.id = c.unit_id","left");

                        //echo $this->db->get_compiled_select();exit();
                        $q2=$this->db->get();
                        foreach ($q2->result() as $res2) {
                          $hsn = $res2->hsn;
                          //$price_before_tax = $res2->price_before_tax;
                          $price_before_tax = $res2->price_before_tax;
                          $price_after_tax = $res2->price_after_tax;

                          $tax_per = $res2->tax;
                          $sum_of_tax_amt = $res2->sum_of_tax_amt;

                          $price_before_tax = $price_after_tax - $sum_of_tax_amt;

                          $tax_type='';
                          //$tax_type = ($res2->tax_type=='Exclusive') ? 'Exc.' : 'Inc.';
                          if( $customer_rec->id==1 || (strtoupper($customer_state_name) == strtoupper($store_rec->state))){
                            $sgst_per = $cgst_per = $tax_per."%";
                            $sgst_amt = $cgst_amt = $sum_of_tax_amt / 2;
                            $igst_per = $igst_amt = '';
                          }else{
                            $sgst_per = $cgst_per = '';
                            $sgst_amt = $cgst_amt = '';
                            $igst_per = $tax_per."%";
                            $igst_amt = $sum_of_tax_amt;
                          }

                       $tot_price_before_tax +=$price_before_tax;
                       $tot_price_after_tax +=(!empty($price_after_tax)) ? $price_after_tax : 0;
                       $tot_cgst_amt +=(!empty($cgst_amt)) ? $cgst_amt : 0;
                       $tot_sgst_amt +=(!empty($sgst_amt)) ? $sgst_amt : 0;
                       $tot_igst_amt +=(!empty($igst_amt)) ? $igst_amt : 0;
                       
                     } ?>

  <tr class="bg-sky">
    <td colspan="8" class='text-center text-bold'><?= $this->lang->line('total'); ?></td>
    <td colspan="" class='text-bold text-center'></td>
    <td colspan="" class='text-bold text-center'><?=store_number_format($tot_qty); ?></td>
    <td colspan="2" class='text-right' ><b><?= store_number_format($tot_before_tax); ?></b></td>
    <td colspan="2" class='text-right' ><b><?= store_number_format($discount_amt); ?></b></td>
    <td colspan="2" class='text-right' ><b><?= store_number_format($tot_total_cost); ?></b></td>
  </tr>
  <!--tr>
    <td colspan="14" class='text-right'><b><?= $this->lang->line('subtotal'); ?></b></td>
    <td colspan="2" class='text-right' ><b><?= store_number_format($tot_total_cost); ?></b></td>
  </tr-->
  <?php if($tot_cgst_amt!=0 && !empty($tot_cgst_amt)){ ?>
  <tr>
    <td colspan="14" class='text-right'><b><?= $this->lang->line('cgst'); ?></b></td>
    <td colspan="2" class='text-right' ><b><?= store_number_format($tot_cgst_amt); ?></b></td>
  </tr>
  <tr>
    <td colspan="14" class='text-right'><b><?= $this->lang->line('sgst'); ?></b></td>
    <td colspan="2" class='text-right' ><b><?= store_number_format($tot_sgst_amt); ?></b></td>
  </tr>
  <?php } else{?>
  <tr>
    <td colspan="14" class='text-right'><b><?= $this->lang->line('igst'); ?></b></td>
    <td colspan="2" class='text-right' ><b><?= store_number_format($tot_igst_amt); ?></b></td>
  </tr>
<?php } ?>
<?php if($sales_rec->other_charges_amt!=0 && !empty($sales_rec->other_charges_amt)){ ?>
  <tr>
    <td colspan="14" class='text-right'><b><?= $this->lang->line('other_charges'); ?></b></td>
    <td colspan="2" class='text-right' ><b><?= store_number_format($sales_rec->other_charges_amt); ?></b></td>
  </tr>
  <?php } ?>
  <?php if($sales_rec->tot_discount_to_all_amt!=0 && !empty($sales_rec->tot_discount_to_all_amt)){ ?>
  <tr>
    <td colspan="14" class='text-right'><b><?= $this->lang->line('discount_on_all'); ?>(<?php echo $sales_rec->discount_to_all_input." "; echo ($sales_rec->discount_to_all_type=='in_percentage') ? '%' : 'Fixed'; ?>)</b></td>
    <td colspan="2" class='text-right' ><b><?= store_number_format($sales_rec->tot_discount_to_all_amt); ?></b></td>
  </tr>
  <?php } ?>
    <tr>
    <td colspan="14" class='text-right'><b><?= $this->lang->line('round_off'); ?></b></td>
    <td colspan="2" class='text-right' ><b><?= store_number_format($sales_rec->round_off); ?></b></td>
  </tr>
   <?php  ?>
  <tr>
    <td colspan="14" class='text-right'><b><?= $this->lang->line('grand_total'); ?></b></td>
    <td colspan="2" class='text-right' ><b><?= store_number_format($sales_rec->grand_total); ?></b></td>
  </tr>
  <tr>
    <td colspan="16">
<?php
     
     
      echo "<span class='amt-in-word'>Amount in words: <i style='font-weight:bold;'>".$this->session->userdata('currency_code')." ".no_to_words($sales_rec->grand_total)." Only</i></span>";

      ?>
  
</td>
  </tr>

  <!-- Tax Table -->

  <tr>
        <td colspan="16">
          <table width="100%" class="style_hidden fixed_table">
            <tbody>
              <tr>
                <td colspan="16">
                  <span>
                    <table style="width: 100%;" class="style_hidden fixed_table">
                      <tbody>

                        <tr class="bg-sky text-bold">
                          <td colspan='1' class='text-center' rowspan="2" width="15%"><?= $this->lang->line('hsn/sac'); ?></td>
                          <td colspan='1' class='text-center' rowspan="2" width="15%"><?= $this->lang->line('taxable_amount'); ?></td>
                          <td colspan='4' class='text-center' colspan="2"  width="20%">
                            <?= $this->lang->line('cgst'); ?>
                          </td>
                          <td colspan='4' class='text-center' colspan="2" width="20%">
                            <?= $this->lang->line('sgst'); ?>
                          </td>
                          <td colspan='4' class='text-center' colspan="2" width="20%">
                            <?= $this->lang->line('igst'); ?>
                          </td>
                          <td colspan='2' class='text-center' width="10%" rowspan="2">
                            <?= $this->lang->line('total'); ?>
                          </td>
                        </tr>

                        
                        <tr class="bg-sky text-bold">
                          <td colspan='2' class='text-center'>Rate</td><td colspan='2' class='text-center'>Amt</td>
                          <td colspan='2' class='text-center'>Rate</td><td colspan='2' class='text-center'>Amt</td>
                          <td colspan='2' class='text-center'>Rate</td><td colspan='2' class='text-center'>Amt</td>
                        </tr>
                        <?php
                        $tot_price_before_tax = $tot_price_after_tax = $tot_cgst_amt =$tot_sgst_amt=$tot_sgst_amt=$tot_igst_amt = 0;
                        /*$q2=$this->db->query(" SELECT c.item_name,  COALESCE(SUM(a.price_per_unit),0) AS price_before_tax, 
                                               b.tax,b.tax_name,
                                               COALESCE(SUM(a.tax_amt),0) AS sum_of_tax_amt,
                                               COALESCE(SUM(a.total_cost),0) AS price_after_tax,c.tax_type,
                                               c.sku 
                                               FROM 
                                               db_salesitems AS a,db_tax AS b,db_items AS c , db_units AS d 
                                               WHERE 
                                               d.id = c.unit_id AND c.id=a.item_id AND b.id=a.tax_id AND a.sales_id='$sales_id' GROUP BY b.id");*/

                        $this->db->select(" c.item_name,  COALESCE(SUM(a.price_per_unit),0) AS price_before_tax, 
                                               b.tax,b.tax_name,c.hsn,
                                               COALESCE(SUM(a.tax_amt),0) AS sum_of_tax_amt,
                                               COALESCE(SUM(a.total_cost),0) AS price_after_tax,c.tax_type,
                                               c.sku 
                                            ");

                        $this->db->where("a.sales_id",$sales_id);
                        $this->db->from("db_salesitems a");
                        $this->db->join("db_tax b","b.id=a.tax_id","left");
                        $this->db->join("db_items c","c.id=a.item_id","left");
                        $this->db->join("db_units d","d.id = c.unit_id","left");
                        //echo $this->db->get_compiled_select();exit();
                        $q2=$this->db->get();
                        
                        foreach ($q2->result() as $res2) {
                          $hsn = $res2->hsn;
                          //$price_before_tax = $res2->price_before_tax;
                          $price_before_tax = $res2->price_before_tax;
                          $price_after_tax = $res2->price_after_tax;

                          $tax_per = $res2->tax;
                          $sum_of_tax_amt = $res2->sum_of_tax_amt;

                          $price_before_tax = $price_after_tax - $sum_of_tax_amt;

                          $tax_type='';
                          //$tax_type = ($res2->tax_type=='Exclusive') ? 'Exc.' : 'Inc.';
                          if( $customer_rec->id==1 || (strtoupper($customer_state_name) == strtoupper($store_rec->state))){
                            $sgst_per = $cgst_per = $tax_per;
                            $sgst_amt = $cgst_amt = $sum_of_tax_amt / 2;
                            $igst_per = $igst_amt = 0;
                          }else{
                            $sgst_per = $cgst_per = 0;
                            $sgst_amt = $cgst_amt = 0;
                            $igst_per = $tax_per;
                            $igst_amt = $sum_of_tax_amt;
                          }
                          

                         ?>
                         <tr>
                          <td colspan='1' class='text-center'><?= $hsn ?></td>
                          <td colspan='1' class='text-center'><?= store_number_format($price_before_tax)." ".$tax_type ?></td>
                          <td colspan='2' class='text-center'><?= (!empty($cgst_per))? store_number_format($cgst_per/2):''; ?>%</td>
                            <td colspan='2' class='text-center'><?= store_number_format($cgst_amt) ?></td>
                          <td colspan='2' class='text-center'><?= (!empty($sgst_per))? store_number_format($sgst_per/2):''; ?>%</td>
                            <td colspan='2' class='text-center'><?= store_number_format($sgst_amt) ?></td>
                          <td colspan='2' class='text-center'><?= (!empty($igst_per))? store_number_format($igst_per/2):''; ?>%</td>
                            <td colspan='2' class='text-center'><?= store_number_format($igst_amt) ?></td>
                          <td colspan='2' class='text-center'><?=store_number_format($price_after_tax)?></td>
                        </tr>
                       <?php 
                       $tot_price_before_tax +=$price_before_tax;
                       $tot_price_after_tax +=(!empty($price_after_tax)) ? $price_after_tax : 0;
                       $tot_cgst_amt +=(!empty($cgst_amt)) ? $cgst_amt : 0;
                       $tot_sgst_amt +=(!empty($sgst_amt)) ? $sgst_amt : 0;
                       $tot_igst_amt +=(!empty($igst_amt)) ? $igst_amt : 0;
                       
                     } ?>
                          <tr class='bg-sky text-bold'>
                          <td colspan='1' class='text-center'>Total</td>
                          <td colspan='1' class='text-center'><?= store_number_format($tot_price_before_tax) ?></td>
                          <td colspan='2' class='text-center'></td>
                            <td colspan='2' class='text-center'><?= (!empty($cgst_per)) ? store_number_format($tot_cgst_amt) : '' ?></td>
                          <td colspan='2' class='text-center'></td>
                            <td colspan='2' class='text-center'><?= (!empty($sgst_per)) ? store_number_format($tot_sgst_amt) : '' ?></td>
                          <td colspan='2' class='text-center'></td>
                            <td colspan='2' class='text-center'><?= (!empty($igst_per)) ? store_number_format($tot_igst_amt) : '' ?></td>
                          <td colspan='2' class='text-center'><?=store_number_format($sales_rec->grand_total)?></td>
                        </tr>
                      </tbody>
                    </table>
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
      </td>
      </tr>
      <!-- Tax table end -->

      <!-- T&C & Bank Details & signatories-->
      <tr>
        <td colspan="16">
          <table width="100%" class="style_hidden fixed_table">
           
              <tr>
                <td colspan="16">
                  <span>
                    <table style="width: 100%;" class="style_hidden fixed_table">
                    
                        

                         <tr>
                          <td colspan='8' style="height:80px;">
                            <span><b> <?= $this->lang->line('customer_signature'); ?></b></span>
                          </td>
                          <td colspan='8'>
                            <span><b> <?= $this->lang->line('authorised_signatory'); ?></b></span><br>
                            <!--img width="30%" src="uploads/stamp/stamp.png"><br-->
                          </td>
                        </tr>
                     
                    </table>
                  </span>
                </td>
              </tr>
           
 </table>
      </td>
      </tr>
      <!-- T&C & Bank Details & signatories End -->

      <?php if(!empty($store_rec->sales_invoice_footer_text)) {?>
      <tr>
        <td colspan="16" style="text-align: center;font-size: 11px;">
          <?= $store_rec->sales_invoice_footer_text; ?>
        </td>
      </tr>
      <?php } ?>
      <tr>
        <td colspan="16" style="text-align: center;font-size: 11px;">
          <center>
        <span style="font-size: 11px;text-transform;">
        <!--span style="font-size: 11px;text-transform: uppercase;">
          This is Computer Generated Invoice-->
        <span style="font-size: 11px;text-transform: uppercase;">
          Thank You For Choosing Our Services
        </span>
      </center>
        </td>
      </tr>
</tfoot>
</table>
</body>
</html>