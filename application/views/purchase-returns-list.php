<!DOCTYPE html>
<html>

<head>
<!-- TABLES CSS CODE -->
<?php include"comman/code_css.php"; ?>
<!-- bootstrap datepicker -->
<style>
  @media(max-width: 480px){
  .small-box h3 {
    font-size: 23px;
    font-weight: bold;
    margin: 0 0 10px 0;
    white-space: nowrap;
    padding: 0;
}
}
.sectionmenu {
    display: block;
    min-height: 110px;
    background: #fff;
    width: 100%;
    box-shadow: 0 1px 1px rgba(0,0,0,0.1);
    border-radius: 5px;
    margin-bottom: 35px;
}
.sectionmenu .info-box-icon {
    border-top-left-radius: 2px;
    border-top-right-radius: 0;
    border-bottom-right-radius: 0;
    border-bottom-left-radius: 2px;
    display: block;
    float: right;
    height: 80px;
    margin-right: 20px;
    margin-top: 13px;
    margin-left: 10px;
    padding: 8px;
    border-radius: 60px;
    background-color: rgb(0 0 0 / 31%);
    color: white;
    width: 80px;
    text-align: center;
    font-size: 40px;
    line-height: 68px;
}
.sectionmenu .info-box-content {
    padding: 24px 10px;
    margin-left: 27px;
}
.info-box-number {
    display: block;
    font-weight: bold;
    font-size: 14px;
}

</style>
<link rel="stylesheet" href="<?php echo $theme_link; ?>plugins/datepicker/datepicker3.css">
</head>

<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <!-- Left side column. contains the logo and sidebar -->
  
  <?php include"sidebar.php"; ?>

  <?php 
      /*Total Invoices*/
      if (!is_admin()) {
        if ($this->session->userdata('role_id') != '2') {
          $this->db->where("upper(created_by)", strtoupper($this->session->userdata('inv_username')));
        }
      }
      $total_invoice = $this->db->select("COUNT(*) as total")->from("db_purchasereturn")->where("store_id", get_current_store_id())->get()->row()->total;
      /*Total Invoices Total*/
      if (!is_admin()) {
        if ($this->session->userdata('role_id') != '2') {
          $this->db->where("upper(created_by)", strtoupper($this->session->userdata('inv_username')));
        }
      }
      $pur_total = $this->db->select("COALESCE(sum(grand_total),0) AS tot_pur_grand_total")->from("db_purchasereturn")->where("store_id", get_current_store_id())->get()->row()->tot_pur_grand_total;
      /*Total Invoices Return Total*/
      if (!is_admin()) {
        if ($this->session->userdata('role_id') != '2') {
          $this->db->where("upper(created_by)", strtoupper($this->session->userdata('inv_username')));
        }
      }

      $pur_return_total = $this->db->select("COALESCE(SUM(grand_total),0) AS pur_total")->from("db_purchasereturn")->where("store_id", get_current_store_id())->get()->row()->pur_total;

      //Purchase Return Paid Total
       if (!is_admin()) {
        if ($this->session->userdata('role_id') != '2') {
          $this->db->where("upper(created_by)", strtoupper($this->session->userdata('inv_username')));
        }
      }
      $paid_amount = $this->db->select("COALESCE(SUM(paid_amount),0) AS paid_amount")->from("db_purchasereturn")->where("store_id", get_current_store_id())->get()->row()->paid_amount;

      
       //total purchase due
      if (!is_admin()) {
        if ($this->session->userdata('role_id') != '2') {
          $this->db->where("upper(created_by)", strtoupper($this->session->userdata('inv_username')));
        }
      }
      $purchase_due_total = $this->db->select("COALESCE(SUM(purchase_return_due),0) AS purchase_return_due")->from("db_suppliers")->where("store_id", get_current_store_id())->get()->row()->purchase_return_due;


      //$purchase_due_total = $pur_total - $paid_amount;
     
  ?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <?=$page_title;?>
        <small>View/Search Purchase</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo $base_url; ?>dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active"><?=$page_title;?></li>
      </ol>
    </section>

    <div class="pay_now_modal">
    </div>
    <div class="view_payments_modal">
    </div>

    <!-- Main content -->
    <?= form_open('#', array('class' => '', 'id' => 'table_form')); ?>
    <input type="hidden" id='base_url' value="<?=$base_url;?>">
    <section class="content">
      <!-- Small boxes (Stat box) -->
      <div class="row">
  <!-- ********** ALERT MESSAGE START******* -->
        <?php include "comman/code_flashdata.php";?>
        <!-- ********** ALERT MESSAGE END******* -->
</div>
      <div class="row">
        <div class="col-md-6 col-sm-6 col-xs-12">
          <div class="info-box " >
            <span class="info-box-icon bg-5"><i class="ion ion-bag"></i></span>
            <div class="info-box-content">
              <span class="info-box-text"><?=$total_invoice;?></span>
              <span class="info-box-number"><?=$this->lang->line('total_invoices');?></span>
            </div>
            
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        <!-- /.col -->
        <div class="col-md-6 col-sm-6 col-xs-12">
          <div class="info-box " >
            <span class="info-box-icon bg-5"><i class="fa fa-dollar"></i></span>

            <div class="info-box-content">
              <span class="info-box-text"><?=$CI->currency($pur_total, $with_comma = true);?></span>
              <span class="info-box-number"><?=$this->lang->line('total_invoices_amount');?></span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        <!-- /.col -->

        <!-- fix for small devices only -->
        <div class="clearfix visible-sm-block"></div>

        <div class="col-md-6 col-sm-6 col-xs-12">
          <div class="info-box " >
            <span class="info-box-icon bg-5"><i class="fa fa-money"></i></span>

            <div class="info-box-content">
              <span class="info-box-text"><?=$CI->currency($paid_amount, $with_comma = true);?></span>
              <span class="info-box-number"><?=$this->lang->line('total_returned_amount');?></span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        <!-- /.col -->
        <div class="col-md-6 col-sm-6 col-xs-12">
          <div class="info-box " >
            <span class="info-box-icon bg-5"><i class="fa fa-minus-circle"></i></span>

            <div class="info-box-content">
              <span class="info-box-text"><?=$CI->currency($purchase_due_total, $with_comma = true);?></span>
              <span class="info-box-number"><?=$this->lang->line('total_purchase_due');?></span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
      <div class="row">
        
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header with-border">
              <!-- <h3 class="box-title"><?=$page_title;?></h3> -->
              <div class="col-xs-8 input-group">
                  <!-- Warehouse Code -->
                  <?php 
                   if(warehouse_module() && warehouse_count()>1) {$this->load->view('warehouse/warehouse_code',array('show_warehouse_select_box_2'=>true,'show_all_option'=>true)); }else{
                    echo "<input type='hidden' name='warehouse_id' id='warehouse_id' value='".get_store_warehouse_id()."'>";
                    echo '<h3 class="box-title">'.$page_title.'</h3>';
                   }
                  ?>
                  <!-- Warehouse Code end -->
                </div>
              <?php if($CI->permissions('purchase_return_add')) { ?>
              <div class="box-tools">
                <a class="btn btn-block btn-info" href="<?php echo $base_url; ?>purchase_return/create">
                <i class="fa fa-plus"></i> <?= $this->lang->line('create_new'); ?></a>
              </div>
              <?php } ?>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <table id="example2" class="table table-bordered custom_hover" width="100%">
                <thead class="bg-gray ">
                <tr>
                  <th class="text-center">
                    <input type="checkbox" class="group_check checkbox" >
                  </th>
                  <!-- <th><?= $this->lang->line('store_name'); ?></th> -->
                  <th><?= $this->lang->line('date'); ?></th>
                  <th><?= $this->lang->line('purchase_code'); ?></th>
                  <th><?= $this->lang->line('return_code'); ?></th>
				          <th><?= $this->lang->line('return_status'); ?></th>
                  <th><?= $this->lang->line('reference_no'); ?></th>
                  <th><?= $this->lang->line('supplier_name'); ?></th>
                  <!-- <th>Warehouse</th> -->
                  <th><?= $this->lang->line('total'); ?></th>
                  <th><?= $this->lang->line('paid_amount'); ?></th>
                  <th><?= $this->lang->line('payment_status'); ?></th>
                  <th><?= $this->lang->line('created_by'); ?></th>
                  <th><?= $this->lang->line('action'); ?></th>
                </tr>
                </thead>
                <tbody>
				
                </tbody>
                <tfoot>
                  <tr class="bg-gray">
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th style="text-align:right">Total</th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                  </tr>
              </tfoot>
              </table>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </section>
    <!-- /.content -->
    <?= form_close();?>
  </div>
  <!-- /.content-wrapper -->
  <?php include"footer.php"; ?>
  <!-- Add the sidebar's background. This div must be placed
       immediately after the control sidebar -->
  <div class="control-sidebar-bg"></div>
</div>
<!-- ./wrapper -->

<!-- SOUND CODE -->
<?php include"comman/code_js_sound.php"; ?>
<!-- TABLES CODE -->
<?php include"comman/code_js.php"; ?>
<!-- bootstrap datepicker -->
<script src="<?php echo $theme_link; ?>plugins/datepicker/bootstrap-datepicker.js"></script>
<script type="text/javascript">
  //Date picker
    $('.datepicker').datepicker({
      autoclose: true,
    format: 'dd-mm-yyyy',
     todayHighlight: true
    });
</script>
<script type="text/javascript">
      function load_datatable(){
         //datatables
         var table = $('#example2').DataTable({ 

            /* FOR EXPORT BUTTONS START*/
        dom:'<"row margin-bottom-12"<"col-sm-12"<"pull-left"l><"pull-right"fr><"pull-right margin-left-10 "B>>>tip',
       /* dom:'<"row"<"col-sm-12"<"pull-left"B><"pull-right">>> <"row margin-bottom-12"<"col-sm-12"<"pull-left"l><"pull-right"fr>>>tip',*/
            buttons: {
              buttons: [
                  {
                      className: 'btn bg-red color-palette btn-flat hidden delete_btn pull-left',
                      text: 'Delete',
                      action: function ( e, dt, node, config ) {
                          multi_delete();
                      }
                  },
                  { extend: 'copy', className: 'btn bg-teal color-palette btn-flat',footer: true, exportOptions: { columns: [1,2,3,4,5,6,7,8,9,10]} },
                  { extend: 'excel', className: 'btn bg-teal color-palette btn-flat',footer: true, exportOptions: { columns: [1,2,3,4,5,6,7,8,9,10]} },
                  { extend: 'pdf', className: 'btn bg-teal color-palette btn-flat',footer: true, exportOptions: { columns: [1,2,3,4,5,6,7,8,9,10]} },
                  { extend: 'print', className: 'btn bg-teal color-palette btn-flat',footer: true, exportOptions: { columns: [1,2,3,4,5,6,7,8,9,10]} },
                  { extend: 'csv', className: 'btn bg-teal color-palette btn-flat',footer: true, exportOptions: { columns: [1,2,3,4,5,6,7,8,9,10]} },
                  { extend: 'colvis', className: 'btn bg-teal color-palette btn-flat',footer: true, text:'Columns' },  

                  ]
              },
              /* FOR EXPORT BUTTONS END */

              "processing": true, //Feature control the processing indicator.
              "serverSide": true, //Feature control DataTables' server-side processing mode.
              "order": [], //Initial no order.
              "responsive": true,
              language: {
                  processing: '<div class="text-primary bg-primary" style="position: relative;z-index:100;overflow: visible;">Processing...</div>'
              },
              // Load data for the table's content from an Ajax source
              "ajax": {
                  "url": "<?php echo site_url('purchase_return/ajax_list')?>",
                  "type": "POST",
                  "data": {
                            warehouse_id: $("#warehouse_id").val()
                          },
                  complete: function (data) {
                   $('.column_checkbox').iCheck({
                      checkboxClass: 'icheckbox_square-orange',
                      /*uncheckedClass: 'bg-white',*/
                      radioClass: 'iradio_square-orange',
                      increaseArea: '10%' // optional
                    });
                   call_code();
                    //$(".delete_btn").hide();
                   },

              },

              //Set column definition initialisation properties.
              "columnDefs": [
              { 
                  "targets": [ 0,11 ], //first column / numbering column
                  "orderable": false, //set not orderable
              },
              {
                  "targets" :[0],
                  "className": "text-center",
              },
              
              ],
               /*Start Footer Total*/
              "footerCallback": function ( row, data, start, end, display ) {
                  var api = this.api(), data;
                  // Remove the formatting to get integer data for summation
                  var intVal = function ( i ) {
                      return typeof i === 'string' ?
                          i.replace(/[\$,]/g, '')*1 :
                          typeof i === 'number' ?
                              i : 0;
                  };
                  var total = api
                      .column( 7, { page: 'none'} )
                      .data()
                      .reduce( function (a, b) {
                          return intVal(a) + intVal(b);
                      }, 0 );
                  var paid = api
                      .column( 8, { page: 'none'} )
                      .data()
                      .reduce( function (a, b) {
                          return intVal(a) + intVal(b);
                      }, 0 );
                  /*var due = api
                      .column( 8, { page: 'none'} )
                      .data()
                      .reduce( function (a, b) {
                          return intVal(a) + intVal(b);
                      }, 0 );*/
                 
                  //$( api.column( 0 ).footer() ).html('Total');
                  $( api.column( 7 ).footer() ).html(to_Fixed(total));
                  $( api.column( 8 ).footer() ).html(to_Fixed(paid));
                 // $( api.column( 8 ).footer() ).html((due));
                 
              },
              /*End Footer Total*/
          });
          new $.fn.dataTable.FixedHeader( table );
      }
      $(document).ready(function() {
          //datatables
         load_datatable();
      });
      $("#warehouse_id").on("change",function(){
          $('#example2').DataTable().destroy();
          load_datatable();
      });
</script>
<script src="<?php echo $theme_link; ?>js/purchase_return.js"></script>
<!-- Make sidebar menu hughlighter/selector -->
<script>$(".<?php echo basename(__FILE__,'.php');?>-active-li").addClass("active");</script>
		
</body>
</html>
