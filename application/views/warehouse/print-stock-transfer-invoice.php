<!DOCTYPE html>
<html>
<title><?= $page_title;?></title>
<head>
<link rel='shortcut icon' href='<?php echo $theme_link; ?>images/favicon.ico' />

<style>
table, th, td {
    border: 1px solid black;
    border-collapse: collapse;
    font-family: 'Open Sans', 'Martel Sans', sans-serif;
}
th, td {
    padding: 5px;
    text-align: left;   
    vertical-align:top 
}
</style>
</head>
<body onload="window.print();"><!--  -->
<?php
  $sales_id=$stocktransfer_id;
    $CI =& get_instance();

     $q3=$this->db->query("SELECT * from db_stocktransfer where `id`='$stocktransfer_id' and store_id=".get_current_store_id());
                        
    
      $res3=$q3->row();
      $transfer_date=$res3->transfer_date;
      $note=$res3->note;
      $warehouse_from=$res3->warehouse_from;
      $warehouse_to=$res3->warehouse_to;
      $created_time=$res3->created_time;
      

      $q1=$this->db->query("select * from db_store where id=".get_current_store_id());
      $res1=$q1->row();
      $store_name=$res1->store_name;
      $company_mobile=$res1->mobile;
      $company_phone=$res1->phone;
      $company_email=$res1->email;
      $company_country=$res1->country;
      $company_state=$res1->state;
      $company_city=$res1->city;
      $company_address=$res1->address;
      $company_gst_no=$res1->gst_no;
      $company_vat_no=$res1->vat_no;
      $company_pan_no=$res1->pan_no;

    ?>

<table align="center" width="100%" height='100%'>
    <thead>
      
      <tr>
          <th colspan="5" rowspan="2" style="padding-left: 15px;">
            <b><?php echo $store_name; ?></b><br/>
            <?php echo $this->lang->line('address')." : ".$company_address; ?><br/>
            <?php echo $company_country; ?><br/>
            <?php echo $this->lang->line('mobile').":".$company_mobile; ?><br/>
            <?php echo (!empty(trim($company_email))) ? $this->lang->line('email').": ".$company_email."<br>" : '';?>
            <?php echo (!empty(trim($company_gst_no)) && gst_number()) ? $this->lang->line('gst_number').": ".$company_gst_no."<br>" : '';?>
            <?php echo (!empty(trim($company_vat_no)) && vat_number()) ? $this->lang->line('vat_number').": ".$company_vat_no."<br>" : '';?>
          </th>
          <th colspan="5" rowspan="1"><b style="text-transform: capitalize;"><?= $this->lang->line('stock_transfer_invoice'); ?> </th>
            
      </tr>
      <tr>
          
          <th colspan="5" rowspan="1"><?= $this->lang->line('date'); ?> : <?php echo show_date($transfer_date)." ".$created_time; ?></th>
      </tr>
    


      <tr>
    <td colspan="10" style="padding-left: 15px;">
    <strong>
              <?= $this->lang->line('from_warehouse'); ?> : 
              <?= get_warehouse_name($warehouse_from); ?><br>
              <?= $this->lang->line('to_warehouse'); ?> : 
              <?= get_warehouse_name($warehouse_to); ?><br>
              </strong>
  </td>
  </tr>
  
    
  <tr>
    <th colspan='2' style="border-bottom: 1px solid;">#</th>
    <th colspan='4'><?= $this->lang->line('item_name'); ?></th>
    <th colspan='4'><?= $this->lang->line('quantity'); ?></th>
  </tr>
  </thead>
<tbody>

 <?php
      $i=0;
      $tot_qty=0;
   
      $q2=$this->db->query("SELECT c.item_name, a.transfer_qty
                          FROM 
                          db_stocktransferitems AS a,db_items AS c 
                          WHERE 
                          c.id=a.item_id AND a.stocktransfer_id='$stocktransfer_id'");
      foreach ($q2->result() as $res2) {
          
          echo "<tr>";  
          echo "<td colspan='2'>".++$i."</td>";
          echo "<td colspan='4'>".$res2->item_name."</td>";
        
          echo "<td colspan='4'>".$res2->transfer_qty."</td>";
          echo "</tr>";  
          $tot_qty +=$res2->transfer_qty;
      }
      ?>

  </tbody>
  <tfoot>
    
  </tfoot>
</table>



</body>
</html>
