<?php
  function demo_app(){
    return false;
  }
  function app_version(){
    return '2.3';
  }
  function app_token(){
    return 'e76nqdrlv405s1tijxagkh9fybwm38';
  }

  function app_front_tag_line(){
    $site_rec = get_site_details();
    return '<div class="col-xl-8 col-lg-7 col-md-12 bg" style="background-color: #001cb0">
                <div class="info">
                    <h1>'.$site_rec->site_name.'</h1>
                    <p>POS, Inventory, Accounting, Multi Warehouses, Multi User</p>
                </div>
            </div>';
  }
  function store_demo_logo(){
    return 'uploads/no_logo/yourlogo.png';
  }
  function get_site_logo(){
    $CI =& get_instance();
    return $CI->db->query("select logo from db_sitesettings")->row()->logo;
  }
  function sql_mode(){
    $CI =& get_instance();
    $q1 = $CI->db->query("SELECT @@sql_mode AS sql_mode")->row();
    return $q1->sql_mode;
  }
  function is_sql_full_group_by_enabled(){
    $sql_mode = sql_mode();
    $sql_mode = strtoupper($sql_mode);

    $mode = 'ONLY_FULL_GROUP_BY';
    return (strpos($sql_mode, $mode) !== false) ? show_sql_mode_page() : false;
  }

  function show_sql_mode_page(){
    $CI =& get_instance();
    if(!$CI->db->query(" SET GLOBAL sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))")){
      show_error("Please make sure your database should not be enabled with SQL_FULL_GROUP_BY, For More information Click on Given link: <a href='".base_url()."/help/#full_group_by' target='_blank'>Click here to check!</a>(Full Group By Check)", 403, $heading = "SQL_FULL_GROUP_BY ENABLED!!");
    }else{
      return true;
    }
  }
  
  function decimals(){
    $CI =& get_instance();
    return $CI->session->userdata('decimals');
  }

  function store_number_format($value=0,$comma=true){
    return ($comma) ? number_format($value,decimals()) : number_format($value,decimals(),".","");
  }

  function system_fromatted_date($date=''){
  $CI =& get_instance();
    if ($CI->session->userdata('view_date')=='dd/mm/yyyy') {
      return date('Y-m-d',strtotime(str_replace('/', '-', $date)));
    }
    elseif($CI->session->userdata('view_date')=='mm/dd/yyyy'){
      return date("Y-m-d",strtotime($date));
    }
    else{
      return date("Y-m-d",strtotime($date));
    }
  }
	function show_date($date=''){
	$CI =& get_instance();
    if ($CI->session->userdata('view_date')=='dd/mm/yyyy') {
      return date('d/m/Y',strtotime(str_replace('/', '-', $date)));
    }
    elseif($CI->session->userdata('view_date')=='mm/dd/yyyy'){
      return date("m/d/Y",strtotime($date));
    }
    else{
      return date("d-m-Y",strtotime($date));
    }
  }
  function show_time($time=''){
    if(empty($time)){
      return $time;
    }
    $CI =& get_instance();
    if($CI->session->userdata('view_time')=='24') {
      return date('h:i',strtotime($time));
    }
    else{
      return date('h:i a',strtotime($time));
    }
  }

  function return_item_image_thumb($path=''){
    return str_replace(".", "_thumb.", $path);
  }

  /*Find the change return show in pos or not*/
  function change_return_status(){
    $CI =& get_instance();
    return $CI->db->select('change_return')->where("id",get_current_store_id())->get('db_store')->row()->change_return;
  }

  function get_change_return_amount($sales_id){
    $CI =& get_instance();
    return $CI->db->select('coalesce(sum(change_return),0) as change_return_amount')->where('sales_id',$sales_id)->get('db_salespayments')->row()->change_return_amount;
  }

  function get_invoice_format_id(){
    $CI =& get_instance();
    return $CI->db->select('sales_invoice_format_id')->where('id',get_current_store_id())->get('db_store')->row()->sales_invoice_format_id;
  }
  function get_pos_invoice_format_id(){
    $CI =& get_instance();
    return $CI->db->select('pos_invoice_format_id')->where('id',get_current_store_id())->get('db_store')->row()->pos_invoice_format_id;
  }
  function is_enabled_round_off(){
    $CI =& get_instance();
    $round_off=$CI->db->select('round_off')->where('id',get_current_store_id())->get('db_store')->row()->round_off;
    if($round_off==1){
      return true;
    }
    return false;
  }
  function numberTowords($num)
  {
    $CI =& get_instance();
          $ones = array(
          '0'=> $CI->lang->line('Zero'),
          '1'=> $CI->lang->line('One'),
          '2'=> $CI->lang->line('Two') ,
          '3'=> $CI->lang->line('Three') ,
          '4'=> $CI->lang->line('Four') ,
          '5'=> $CI->lang->line('Five') ,
          '6'=> $CI->lang->line('Six') ,
          '7'=> $CI->lang->line('Seven') ,
          '8'=> $CI->lang->line('Eight') ,
          '9'=> $CI->lang->line('Nine') ,
          '10'=> $CI->lang->line('Ten') ,
          '11'=> $CI->lang->line('Eleven') ,
          '12'=> $CI->lang->line('Twelve') ,
          '13'=> $CI->lang->line('Thirteen') ,
          '14'=> $CI->lang->line('Fouteen') ,
          '15'=> $CI->lang->line('Fifteen') ,
          '16'=> $CI->lang->line('Sixteen') ,
          '17'=> $CI->lang->line('Seventeen') ,
          '18'=> $CI->lang->line('Eighteen') ,
          '19'=> $CI->lang->line('Nineteen') ,
          "014" => "FOURTEEN"
          );
          $tens = array( 
          '0'=> $CI->lang->line('Zero'),
          '1'=> $CI->lang->line('Ten') ,
          '2'=> $CI->lang->line('Twenty') ,
          '3'=> $CI->lang->line('Thirty') ,
          '4'=> $CI->lang->line('Fourty') ,
          '5'=> $CI->lang->line('Fifty') ,
          '6'=> $CI->lang->line('Sixty') ,
          '7'=> $CI->lang->line('Seventy') ,
          '8'=> $CI->lang->line('Eighty') ,
          '9'=> $CI->lang->line('Ninty') ,
          ); 
          $hundreds = array( 
          $CI->lang->line('Hundred'),
          $CI->lang->line('Thousand') ,
          $CI->lang->line('Million') ,
          $CI->lang->line('Billion') ,
          $CI->lang->line('Trillion') ,
          $CI->lang->line('Quadrillion') ,
          ); /*limit t quadrillion */

            $num = number_format($num,2,".",","); 
            $num_arr = explode(".",$num); 
            $wholenum = $num_arr[0]; 
            $decnum = $num_arr[1]; 
            $whole_arr = array_reverse(explode(",",$wholenum)); 
            krsort($whole_arr,1); 
            $rettxt = ""; 

  foreach($whole_arr as $key => $i){
    
          while(substr($i,0,1)=="0")
              $i=substr($i,1,5);
         
                if($i < 20){  
                  if(isset($ones[$i])){
                    $rettxt .= $ones[$i]; 
                  }
                  }elseif($i < 100){ 
                    if(substr($i,0,1)!="0")  $rettxt .= $tens[substr($i,0,1)]; 
                    if(substr($i,1,1)!="0") $rettxt .= " ".$ones[substr($i,1,1)]; 
                  }else{ 
                    if(substr($i,0,1)!="0") $rettxt .= $ones[substr($i,0,1)]." ".$hundreds[0]; 
                    if(substr($i,1,1)!="0")$rettxt .= " ".$tens[substr($i,1,1)]; 
                    if(substr($i,2,1)!="0")$rettxt .= " ".$ones[substr($i,2,1)]; 
                  } 
                  if($key > 0){ 
                    $rettxt .= " ".$hundreds[$key]." "; 
                  }
  }//foreach

      if($decnum > 0){
            $rettxt .= " and ";
          if($decnum < 20){
            $rettxt .= $ones[$decnum];
          }elseif($decnum < 100){
            $rettxt .= $tens[substr($decnum,0,1)];
            $rettxt .= " ".$ones[substr($decnum,1,1)];
          }
      }
      return $rettxt;
  }//function end


  function no_to_words($no){ 
  return numberTowords($no);

    $CI =& get_instance();
     $words = array('0'=> '' ,
                    '1'=> $CI->lang->line('One'),
                    '2'=> $CI->lang->line('Two') ,
                    '3'=> $CI->lang->line('Three') ,
                    '4'=> $CI->lang->line('Four') ,
                    '5'=> $CI->lang->line('Five') ,
                    '6'=> $CI->lang->line('Six') ,
                    '7'=> $CI->lang->line('Seven') ,
                    '8'=> $CI->lang->line('Eight') ,
                    '9'=> $CI->lang->line('Nine') ,
                    '10'=> $CI->lang->line('Ten') ,
                    '11'=> $CI->lang->line('Eleven') ,
                    '12'=> $CI->lang->line('Twelve') ,
                    '13'=> $CI->lang->line('Thirteen') ,
                    '14'=> $CI->lang->line('Fourteen') ,
                    '15'=> $CI->lang->line('Fifteen') ,
                    '16'=> $CI->lang->line('Sixteen') ,
                    '17'=> $CI->lang->line('Seventeen') ,
                    '18'=> $CI->lang->line('Eighteen') ,
                    '19'=> $CI->lang->line('Nineteen') ,
                    '20'=> $CI->lang->line('Twenty') ,
                    '30'=> $CI->lang->line('Thirty') ,
                    '40'=> $CI->lang->line('Fourty') ,
                    '50'=> $CI->lang->line('Fifty') ,
                    '60'=> $CI->lang->line('Sixty') ,
                    '70'=> $CI->lang->line('Seventy') ,
                    '80'=> $CI->lang->line('Eighty') ,
                    '90'=> $CI->lang->line('Ninty') ,
                    '100'=> $CI->lang->line('Hundred &') ,
                    '1000'=> $CI->lang->line('Thousand') ,
                    '100000'=> $CI->lang->line('Lakh') ,
                    '10000000'=> $CI->lang->line('Crore') ,
                  );
      if($no == 0)
        return ' ';
      else {
      $novalue='';
      $highno=$no;
      $remainno=0;
      $value=100;
      $value1=1000;       
          while($no>=100)    {
            if(($value <= $no) &&($no  < $value1))    {
            $novalue=$words["$value"];
            $highno = (int)($no/$value);
            $remainno = $no % $value;
            break;
            }
            $value= $value1;
            $value1 = $value * 100;
          }       
          if(array_key_exists("$highno",$words))
            return $words["$highno"]." ".$novalue." ".no_to_words($remainno);
          else {
           $unit=$highno%10;
           $ten =(int)($highno/10)*10;            
           return $words["$ten"]." ".$words["$unit"]." ".$novalue." ".no_to_words($remainno);
           }
      }
  }

 
  function get_current_store_id(){
    $CI =& get_instance();
    return $CI->session->userdata('store_id');
  }

  function get_customer_store_id($customer_id){
    $CI =& get_instance();
    return $CI->db->select('store_id')->from('db_customers')->where('id',$customer_id)->get()->row()->store_id;
  }
  function get_customer_details($customer_id){
    $CI =& get_instance();
    return $CI->db->select('*')->from('db_customers')->where('id',$customer_id)->get()->row();
  }
  function get_shipping_address_details($id){
    $CI =& get_instance();
    return $CI->db->select('*')->from('db_shippingaddress')->where('id',$id)->get()->row();
  }
  function get_supplier_details($supplier_id){
    $CI =& get_instance();
    return $CI->db->select('*')->from('db_suppliers')->where('id',$supplier_id)->get()->row();
  }
  function get_supplier_store_id($supplier_id){
    $CI =& get_instance();
    return $CI->db->select('store_id')->from('db_suppliers')->where('id',$supplier_id)->get()->row()->store_id;
  }

  function get_count_id($table,$store_id=''){
    $CI =& get_instance();
    $store_id = (!empty($store_id)) ? $store_id : get_current_store_id();
    return $CI->db->select('(coalesce(max(count_id),0)+1) as count_id')->where('store_id',$store_id)->get($table)->row()->count_id;
  }

  /*Warehouse*/
  function warehouse_count(){
    $CI =& get_instance();
    return $CI->db->select('count(*) as warehouse_count')->where('store_id',get_current_store_id())->where('status',1)->get('db_warehouse')->row()->warehouse_count;
  }
  function get_store_warehouse_id(){
    $CI =& get_instance();
    return $CI->db->select('id')->where('store_id',get_current_store_id())->where('warehouse_type','System')->get('db_warehouse')->row()->id;
  }
  /*end*/
  function get_init_code($value,$store_id=''){
    $store_id = (!empty($store_id)) ? $store_id : get_current_store_id();

    $CI =& get_instance();
    if($value=='category')
      $CI->db->select("category_init");
    if($value=='item')
      $CI->db->select("item_init");
    if($value=='supplier')
      $CI->db->select("supplier_init");
    if($value=='purchase')
      $CI->db->select("purchase_init");
    if($value=='purchase_return')
      $CI->db->select("purchase_return_init");
    if($value=='customer')
      $CI->db->select("customer_init");
    if($value=='sales')
      $CI->db->select("sales_init");
    if($value=='sales_return')
      $CI->db->select("sales_return_init");
    if($value=='expense')
      $CI->db->select("expense_init");
    if($value=='accounts')
      $CI->db->select("accounts_init");
    /*if($value=='journal')
      $CI->db->select("journal_init");*/
    if($value=='quotation')
      $CI->db->select("quotation_init");
    if($value=='money_transfer')
      $CI->db->select("money_transfer_init");
    if($value=='sales_payment')
      $CI->db->select("sales_payment_init");
    if($value=='sales_return_payment')
      $CI->db->select("sales_return_payment_init");
    if($value=='purchase_payment')
      $CI->db->select("purchase_payment_init");
    if($value=='purchase_return_payment')
      $CI->db->select("purchase_return_payment_init");
     if($value=='expense_payment')
      $CI->db->select("expense_payment_init");
    if($value=='custadvance')
      $CI->db->select("cust_advance_init");

    $query = $CI->db->where('id',$store_id)->get('db_store')->row();
    if($value=='category'){
      $maxid=get_count_id('db_category');
      return $query->category_init.str_pad($maxid, 4, '0', STR_PAD_LEFT);
    }

    if($value=='item'){
      $maxid=get_count_id('db_items');
      return $query->item_init.str_pad($maxid, 4, '0', STR_PAD_LEFT);
    }
    if($value=='supplier'){
      $maxid=get_count_id('db_suppliers');
      return $query->supplier_init.str_pad($maxid, 4, '0', STR_PAD_LEFT);
    }
    if($value=='purchase'){
      $maxid=get_count_id('db_purchase');
      return $query->purchase_init.str_pad($maxid, 4, '0', STR_PAD_LEFT);
    }
    if($value=='purchase_return'){
      $maxid=get_count_id('db_purchasereturn');
      return $query->purchase_return_init.str_pad($maxid, 4, '0', STR_PAD_LEFT);
    }
    if($value=='customer'){
      $maxid=get_count_id('db_customers');
      return $query->customer_init.str_pad($maxid, 4, '0', STR_PAD_LEFT);
    }
    if($value=='sales'){
      $maxid=get_count_id('db_sales');
      return $query->sales_init.str_pad($maxid, 4, '0', STR_PAD_LEFT);
    }
    if($value=='sales_return'){
      $maxid=get_count_id('db_salesreturn');
      return $query->sales_return_init.str_pad($maxid, 4, '0', STR_PAD_LEFT);
    }
    if($value=='expense'){
      $maxid=get_count_id('db_expense');
      return $query->expense_init.str_pad($maxid, 4, '0', STR_PAD_LEFT);
    }
    if($value=='accounts'){
      $maxid=get_count_id('ac_accounts');
      return $query->accounts_init.str_pad($maxid, 4, '0', STR_PAD_LEFT);
    }
   /* if($value=='journal'){
      $maxid=get_count_id('ac_journal');
      //return $query->accounts_init.str_pad($maxid, 4, '0', STR_PAD_LEFT);
      return str_pad($maxid, 4, '0', STR_PAD_LEFT);
    }*/
    if($value=='quotation'){
      $maxid=get_count_id('db_quotation');
      return $query->quotation_init.str_pad($maxid, 4, '0', STR_PAD_LEFT);
    }
    if($value=='money_transfer'){
      $maxid=get_count_id('ac_moneytransfer');
      return $query->money_transfer_init.str_pad($maxid, 4, '0', STR_PAD_LEFT);
    }
    if($value=='sales_payment'){
      $maxid=get_count_id('db_salespayments');
      return $query->sales_payment_init.str_pad($maxid, 4, '0', STR_PAD_LEFT);
    }
    if($value=='sales_return_payment'){
      $maxid=get_count_id('db_salespaymentsreturn');
      return $query->sales_return_payment_init.str_pad($maxid, 4, '0', STR_PAD_LEFT);
    }
    if($value=='purchase_payment'){
      $maxid=get_count_id('db_purchasepayments');
      return $query->purchase_payment_init.str_pad($maxid, 4, '0', STR_PAD_LEFT);
    }
    if($value=='purchase_return_payment'){
      $maxid=get_count_id('db_purchasepaymentsreturn');
      return $query->purchase_return_payment_init.str_pad($maxid, 4, '0', STR_PAD_LEFT);
    }
    if($value=='expense_payment'){
      $maxid=get_count_id('db_expense');
      return $query->expense_payment_init.str_pad($maxid, 4, '0', STR_PAD_LEFT);
    }
    if($value=='custadvance'){
      $maxid=get_count_id('db_custadvance');
      return $query->cust_advance_init.str_pad($maxid, 4, '0', STR_PAD_LEFT);
    }
  }
  function get_store_name($id=''){
    if(empty($id)){ return true;}
    $CI =& get_instance();
    if(empty($id)){
      $id=get_current_store_id();
    }
    $q1 = $CI->db->select('store_name')->where('id',$id)->get('db_store');
    if($q1->num_rows()>0){
      return $q1->row()->store_name;
    }
    else{
      return null;
    }
  }
  function get_role_name(){
      $CI =& get_instance();
      return $CI->session->userdata('role_name');
  }
  function store_admin_id(){
    return 2;
  }
  function is_store_admin(){
    $CI =& get_instance();
    if($CI->session->userdata('role_id')==store_admin_id()){
      return true;
    }
    return false;
  }
  function is_admin(){
    if(strtoupper(get_role_name())==strtoupper('admin')){
      return true;
    }
    return false;
  }
  function is_user(){
    return is_admin();
  }
  function set_status_of_table($col_id,$status,$table){
    $CI =& get_instance();
    $CI->db->where("id",$col_id);
    //if not admin
    if(!is_admin()){
      $CI->db->where("store_id",get_current_store_id());
    }
    $CI->db->set("status",$status);
    $query1=$CI->db->update($table);
        if ($query1){
            return true;
        }
        return false;
  }

  function get_walk_in_customer_name(){
    return 'Walk-in customer';
  }
  
  function get_warehouse_name($id){
    $CI =& get_instance();
    return $CI->db->select('warehouse_name')->where('id',$id)->get('db_warehouse')->row()->warehouse_name;
  }
  function get_total_qty_of_warehouse_item($item_id,$warehouse_id='',$store_id=''){
    if(empty($warehouse_id)){
      $warehouse_id= get_store_warehouse_id();
    }
    if(empty($store_id)){
      $store_id= get_current_store_id();
    }
    $CI =& get_instance();
    /*Sum purchase quantity of purchase entry*/
    $purchase_qty=$CI->db->query("SELECT COALESCE(SUM(a.purchase_qty), 0) AS purchase_qty FROM 
                              db_purchaseitems AS a,
                              db_purchase AS b
                              WHERE 
                              a.`item_id`=$item_id AND a.`purchase_id`=b.id AND 
                              b.`store_id`=$store_id AND b.`warehouse_id`=$warehouse_id and b.purchase_status='Received'")->row()->purchase_qty;

    /*Sum purchase quantity of purchase entry*/
    $purchase_return_qty=$CI->db->query("SELECT COALESCE(SUM(a.return_qty), 0) AS purchase_return_qty FROM 
                              db_purchaseitemsreturn AS a,
                              db_purchasereturn AS b
                              WHERE 
                              a.`item_id`=$item_id AND a.`return_id`=b.id AND 
                              b.`store_id`=$store_id AND b.`warehouse_id`=$warehouse_id")->row()->purchase_return_qty;

    /*Sum sales quantity of sales entry*/
    $sales_qty=$CI->db->query("SELECT COALESCE(SUM(a.sales_qty), 0) AS sales_qty FROM 
                              db_salesitems AS a,
                              db_sales AS b
                              WHERE 
                              a.`item_id`=$item_id AND a.`sales_id`=b.id AND 
                              b.`store_id`=$store_id AND b.`warehouse_id`=$warehouse_id")->row()->sales_qty;

    /*Sum sales return quantity of invoice*/
    $sales_return_qty=$CI->db->query("SELECT COALESCE(SUM(a.return_qty), 0) AS sales_return_qty FROM 
                              db_salesitemsreturn AS a,
                              db_salesreturn AS b
                              WHERE 
                              a.`item_id`=$item_id AND a.`return_id`=b.id AND 
                              b.`store_id`=$store_id AND b.`warehouse_id`=$warehouse_id")->row()->sales_return_qty;


    $stock_entry_qty=$CI->db->query("SELECT COALESCE(SUM(adjustment_qty),0) AS adjustment_qty FROM db_stockadjustmentitems 
                              WHERE 
                              store_id=$store_id AND 
                              warehouse_id=$warehouse_id AND
                              item_id=$item_id")->row()->adjustment_qty;
    /*Add Stock Transfer*/
    $stocktransfer_qty_add=$CI->db->query("SELECT COALESCE(SUM(transfer_qty),0) AS stocktransfer_qty FROM db_stocktransferitems 
                              WHERE 
                              store_id=$store_id AND 
                              warehouse_to=$warehouse_id AND
                              item_id=$item_id")->row()->stocktransfer_qty;
    /*Deduct Stock from warerhouse*/
    $stocktransfer_qty_deduct=$CI->db->query("SELECT COALESCE(SUM(transfer_qty),0) AS stocktransfer_qty FROM db_stocktransferitems 
                              WHERE 
                              store_id=$store_id AND 
                              warehouse_from=$warehouse_id AND
                              item_id=$item_id")->row()->stocktransfer_qty;
    
    return ($stock_entry_qty + $purchase_qty + $stocktransfer_qty_add - $stocktransfer_qty_deduct + $sales_return_qty - $purchase_return_qty)-$sales_qty;
  }
  function update_warehousewise_items_qty($item_id,$warehouse_id,$store_id){
    $CI =& get_instance();
    //If item id exist
      $CI->db->where("store_id",$store_id)->where("warehouse_id",$warehouse_id)->where('item_id',$item_id)->delete("db_warehouseitems");
      $available_qty = get_total_qty_of_warehouse_item($item_id,$warehouse_id,$store_id);
      if($available_qty>0){
        $info=array(  'store_id'      =>  $store_id,
                      'warehouse_id'  =>  $warehouse_id,
                      'item_id'       =>  $item_id,
                      'available_qty' =>  $available_qty,
         );
        $q1 = $CI->db->insert('db_warehouseitems', $info);
        if(!$q1){
          return false;
        }
      }      
    return true;
  }

  function update_warehousewise_items_qty_by_store($store_id='',$item_ids=''){
    $CI =& get_instance();
    $store_id = (!empty($store_id)) ? $store_id : get_current_store_id();
      $q3=$CI->db->select("id")->where("store_id",$store_id)->get("db_warehouse");
      foreach($q3->result() as $res3) {
        $warehouse_id = $res3->id;
        if(!empty($item_ids)){
          $CI->db->where("id in ($item_ids)");
        }
        $CI->db->where("service_bit!=1");
        $q1=$CI->db->select("id")->where('store_id',$store_id)->get("db_items");  
        foreach($q1->result() as $res1) {
            $q1 = update_warehousewise_items_qty($res1->id,$warehouse_id,$store_id);
            if(!$q1){
              return false;
            }
        }//items foreach
      }//Warehouse foreach
    return true;
  }

   function update_warehouse_items($two_array){
    if(!is_array($two_array)){
      $two_array = array(array($two_array));
    }
    $unique_array = array_unique($two_array,SORT_REGULAR);

    $tmpArr = array();
    foreach ($unique_array as $sub) {
      $tmpArr[] = implode(',', $sub);
    }
    $item_ids = implode(',', $tmpArr);

    /*Update items in all warehouses of the item*/
    $q7=update_warehousewise_items_qty_by_store(null,$item_ids);
    if(!$q7){
      return false;
    }
    return true;
  }


  function total_items_of_warehouse($warehouse_id,$store_id=''){
    $CI =& get_instance();
    if(empty($store_id)){
      $store_id= get_current_store_id();
    }
    
    return $CI->db->select("count(*) as total_items")->where("warehouse_id",$warehouse_id)->where("store_id",$store_id)->get("db_warehouseitems")->row()->total_items;
  }
  function total_available_qty_items_of_warehouse($warehouse_id='',$store_id='',$item_id=''){
    $CI =& get_instance();
    if(empty($store_id)){
      $store_id= get_current_store_id();
    }
    if(!empty($item_id)){
      $CI->db->where("item_id",$item_id);
    }
    if(!empty($warehouse_id)){
      $CI->db->where("warehouse_id in ($warehouse_id)");
    }
    $CI->db->select("COALESCE(sum(available_qty),0) as available_qty")->where("store_id",$store_id)->from("db_warehouseitems");
    //echo $CI->db->get_compiled_select();exit;
    return $CI->db->get()->row()->available_qty;
  }
  function total_worth_of_warehouse_items($warehouse_id,$store_id=''){
    $CI =& get_instance();
    if(empty($store_id)){
      $store_id= get_current_store_id();
    }
    $CI->db->select("COALESCE(sum(available_qty),0) as available_qty,item_id")->where("warehouse_id",$warehouse_id)->where("store_id",$store_id)->from("db_warehouseitems")->group_by("item_id");
    $q1 = $CI->db->get();
    $tot_sales_price=0;
      foreach ($q1->result() as $res1) {
        $item_price = $CI->db->select("coalesce((sales_price),0) as sales_price")->where("id",$res1->item_id)->get("db_items")->row()->sales_price;
        $tot_sales_price+=$item_price*$res1->available_qty;
      }
    return $tot_sales_price;
  }

  function get_total_stocktranfer_items($stocktransfer_id){
    $CI =& get_instance();
    return $CI->db->select("count(item_id) as tot_items")->where("store_id",get_current_store_id())->where("stocktransfer_id",$stocktransfer_id)->get("db_stocktransferitems")->row()->tot_items;
  }
  function get_total_stocktranfer_items_qty($stocktransfer_id){
    $CI =& get_instance();
    return $CI->db->select("coalesce(sum(transfer_qty),0) as transfer_qty")->where("store_id",get_current_store_id())->where("stocktransfer_id",$stocktransfer_id)->get("db_stocktransferitems")->row()->transfer_qty;
  }

  function get_current_user_id(){
    $CI =& get_instance();
    return $CI->session->userdata('inv_userid');
  }

  function get_paid_cob($customer_id){//Customer Opening Balance Paid Total
    $CI =& get_instance();
    return $CI->db->select("coalesce(sum(payment),0) as payment")
            ->where("store_id",get_current_store_id())
            ->where("customer_id",$customer_id)
            ->where("short_code","OPENING BALANCE PAID")
            ->get("db_salespayments")->row()->payment;
  }
  function get_paid_sob($supplier_id){//supplier Opening Balance Paid Total
    $CI =& get_instance();
    return $CI->db->select("coalesce(sum(payment),0) as payment")
            ->where("store_id",get_current_store_id())
            ->where("supplier_id",$supplier_id)
            ->where("short_code","OPENING BALANCE PAID")
            ->get("db_purchasepayments")->row()->payment;
  }
  function get_account_name($id){
    if(empty($id)) {return "";}
    $CI =& get_instance();
    return $CI->db->select("account_name")->where("store_id",get_current_store_id())->where("id",$id)->get("ac_accounts")->row()->account_name;
  }
  function get_seller_points($item_id){
    $CI =& get_instance();
    return $CI->db->select("seller_points")->where("id",$item_id)->get("db_items")->row()->seller_points;
  }
  function get_item_name($item_id){
    $CI =& get_instance();
    return $CI->db->select("item_name")->where("id",$item_id)->get("db_items")->row()->item_name;
  }
  function get_current_store_language(){
    $CI =& get_instance();
    return $CI->db->select("language_id")->where("id",get_current_store_id())->get("db_store")->row()->language_id;
  }

  function get_price_level_price($customer_id,$price){
    $CI =& get_instance();
    $q1=$CI->db->select("price_level_type,price_level")->where("store_id",get_current_store_id())->where("id",$customer_id)->get("db_customers")->row();
    if($q1->price_level!=0){
      return ($q1->price_level_type=='Increase') ? $price + ($price*$q1->price_level)/100 : $price - ($price*$q1->price_level)/100;
    }
    else{
      return $price;
    }
  }
  /*Customer Calculate Opening Balance of the invoice, before and after*/
  function calculate_ob_of_customer($sales_id,$customer_id){
    /*
    Note: Run this Function after customer and sales record updates
    */

    $CI =& get_instance();
    //Sales grand total & paid amount
    $CI->db->select("coalesce(sum(grand_total)) as grand_total, coalesce(sum(paid_amount)) as paid_amount");
    $CI->db->from("db_sales");
    $CI->db->where("id",$sales_id);
    $q1 = $CI->db->get()->row(); 
    $grand_total = $q1->grand_total;
    $paid_amount = $q1->paid_amount;
    
    //Pending invoice payment + Opening balance
    $CI->db->select("coalesce(sum(sales_due),0)+coalesce(sum(opening_balance),0) as tot");
    $CI->db->from("db_customers");
    $CI->db->where("id",$customer_id);
    $invoice_ob = $CI->db->get()->row()->tot; //Current
    
    //Update Sales invoice
    $customer_previous_due = $invoice_ob - ($grand_total - $paid_amount); //Previous
    $CI->db->set("customer_previous_due",$customer_previous_due);
    $CI->db->set("customer_total_due",$invoice_ob);
    $CI->db->where("id",$sales_id);
    $q3 = $CI->db->update("db_sales");
    if(!$q3){
      return false;
    }
    return true;
  }
  function get_privileged_warehouses_ids(){
    $CI =& get_instance();
    //Find the previllaged wareshouses to the user
    $CI->db->select("warehouse_id")->where("user_id",get_current_user_id())->from("db_userswarehouses");
    $q3 = $CI->db->get();
    $privileged_warehouses = array();
    foreach ($q3->result() as $res3) {
      $privileged_warehouses[] = $res3->warehouse_id;
    }
    $privileged_warehouses = implode(',', $privileged_warehouses);
    return $privileged_warehouses;
  }
   function calculate_inclusive($amount,$tax){
  $tot = ($amount/(($tax/100)+1)/10);
    return number_format($tot,2,".","");
  }
  function calculate_exclusive($amount,$tax){
    $tot = (($amount*$tax)/(100));
    return number_format($tot,2,".","");
  }
  
  //08-09-2020
  function get_profile_picture(){
    $CI =& get_instance();
    $profile_picture = $CI->db->select('profile_picture')->where("id",$CI->session->userdata('inv_userid'))->get('db_users')->row()->profile_picture;
    if(!empty($profile_picture)){
      $profile_picture = base_url($profile_picture);
    }
    else{
      $profile_picture = base_url("theme/dist/img/avatar5.png");
    }
    return $profile_picture;
  }

  function get_sales_id_of_quotation($quotation_id){
    $CI =& get_instance();
    return $CI->db->select('id')->where('quotation_id',$quotation_id)->get('db_sales')->row()->id;
  }
  function get_quotation_code($quotation_id){
    $CI =& get_instance();
    return $CI->db->select('quotation_code')->where('id',$quotation_id)->get('db_quotation')->row()->quotation_code;
  }
  function get_sales_code($sales_id){
    $CI =& get_instance();
    return $CI->db->select('sales_code')->where('id',$sales_id)->get('db_sales')->row()->sales_code;
  }
  function get_sales_details($sales_id){
    $CI =& get_instance();
    return $CI->db->select('*')->from('db_sales')->where('id',$sales_id)->get()->row();
  }
  function get_state_details($state_id){
    $CI =& get_instance();
    return $CI->db->select('*')->from('db_states')->where('id',$state_id)->get()->row();
  }
  function get_country_details($country_id){
    $CI =& get_instance();
    return $CI->db->select('*')->from('db_country')->where('id',$country_id)->get()->row();
  }
  function get_tax_details($tax_id){
    $CI =& get_instance();
    return $CI->db->select('*')->from('db_tax')->where('id',$tax_id)->get()->row();
  }
  function is_it_belong_to_store($table,$rec_id){
    $CI =& get_instance();
    $store_id = get_current_store_id();
    return $CI->db->select('count(*) as tot_rec')->where('id',$rec_id)->where('store_id',$store_id)->get($table)->row()->tot_rec;
  }
  function get_item_details($item_id){
    $CI =& get_instance();
    return $CI->db->select("*")
            ->from("db_items")
            ->where('store_id',get_current_store_id())
            ->where("id=",$item_id)->get()->row();
  }
  
  function permissions($permissions=''){
    $CI =& get_instance();
    //If he the Admin
    if($CI->session->userdata('inv_userid')==1){
      return true;
    }
    $tot=$CI->db->query('SELECT count(*) as tot FROM db_permissions where permissions="'.$permissions.'" and role_id='.$CI->session->userdata('role_id'))->row()->tot;
    if($tot==1){
      return true;
    }
     return false;
  }

  function get_current_subcription_id(){
    $CI =& get_instance();
    $store_id = get_current_store_id();
    $subscription_id = $CI->db->select('current_subscriptionlist_id as subscription')->where('id',$store_id)->get('db_store')->row()->subscription;
    if(!$subscription_id){
      return false;
    }
    return $subscription_id;
  }
  function get_subscription_rec($sub_id){
    $CI =& get_instance();
    return $CI->db->select('*')->where('id',$sub_id)->get('db_subscription')->row();
  }

  function get_tot_table_rec($table,$store_id=''){
    $CI =& get_instance();
    $store_id = (!empty($store_id)) ? $store_id : get_current_store_id();
    return $CI->db->select('count(*) as count_id')->where('store_id',$store_id)->get($table)->row()->count_id;
  }

  function validate_package_offers($column,$table_name){
    if(!store_module()){
      return true;
    }
    if(empty($column)){
      echo "Missing!! Package Validation";exit;
    }
    $CI =& get_instance();
    $sub_id = get_current_subcription_id();
    if(!$sub_id && !is_admin() ){
      echo $CI->lang->line('subscription_msg_1');exit;
    }
    else{
      if($column=='max_invoices'){
        if(get_tot_table_rec($table_name) >= get_subscription_rec($sub_id)->max_invoices){
          echo $CI->lang->line('max_invoices_used');exit;
        }  
      }
      if($column=='max_items'){
        if(get_tot_table_rec($table_name) >= get_subscription_rec($sub_id)->max_items){
          echo $CI->lang->line('max_items_used');exit;
        }  
      }
      if($column=='max_warehouses'){
        if(get_tot_table_rec($table_name) >= get_subscription_rec($sub_id)->max_warehouses){
          echo $CI->lang->line('max_warehouses_used');exit;
        }  
      }
      
    }
  }

  function date_difference($start_date,$end_date){
    // Declare two dates 
    $start_date = strtotime(date("Y-m-d",strtotime($start_date))); 
    $end_date = strtotime(date("Y-m-d",strtotime($end_date)));   
    // Get the difference and divide into  
    // total no. seconds 60/60/24 to get  
    // number of days 
    return ($end_date - $start_date)/60/60/24; 
  }

  function get_site_details(){
    $CI =& get_instance();
    return $CI->db->select('*')->where('id',1)->get('db_sitesettings')->row();
  }

  function get_store_details($store_id=''){
    $CI =& get_instance();
    $store_id = (!empty($store_id)) ? $store_id : get_current_store_id();
    return $CI->db->select('*')->where('id',$store_id)->get('db_store')->row();
  }

  function get_user_details($user_id=''){
    $CI =& get_instance();
    $user_id = (!empty($user_id)) ? $user_id : get_current_user_id();
    return $CI->db->select('*')->where('id',$user_id)->get('db_users')->row();
  }

  function check_credit_limit_with_invoice($customer_id,$sales_id){
    $credit_limit = get_customer_details($customer_id)->credit_limit;
    $sales_details = get_sales_details($sales_id);
    $balance = $sales_details->grand_total -$sales_details->paid_amount;
    if($credit_limit > 0 && $balance>$credit_limit){
      echo 'This Customer Credit Limit exceeds! Credit Limit :'.store_number_format($credit_limit)."\nInvoice generated balance :".store_number_format($balance);
      exit;
    }
    return true;
  }

  function xss_html_filter($input){
        $CI =& get_instance();
        return $CI->security->xss_clean(html_escape($input));
    }

  #----------------------------------------------------------------
  function cheque_name(){
    return "Cheque";
  }
  function cash_name(){
    return "Cash";
  }
  #----------------------------------------------------------------

  function gst_number(){
    return true;
  }
  function vat_number(){
    return true;
  }
  function pan_number(){
    return true;
  }
  
  /*Module*/
  
  function warehouse_module(){
    return true;//true or false
  }
  function accounts_module(){
    return true;//true or false
  } 
  function service_module(){
    return true;
  }
 