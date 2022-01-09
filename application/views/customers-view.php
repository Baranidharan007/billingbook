<!DOCTYPE html>
<html>

<head>

<!-- TABLES CSS CODE -->
<?php include"comman/code_css.php"; ?>
<style type="text/css">
.badge {
    color: #190b0b;
    background-color: #c2c2c2;
}
</style>
</head>

<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <!-- Left side column. contains the logo and sidebar -->
  
  <?php include"sidebar.php"; ?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <?=$page_title;?>
        <small>View/Search Customers</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo $base_url; ?>dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a data-toggle='tooltip' title='Do you want Import Customers ?' href="<?php echo $base_url; ?>import/customers"><i class="fa fa-arrow-circle-o-down "></i> <?= $this->lang->line('import_customers'); ?></a></li>
        <li class="active"><?=$page_title;?></li>
        
      </ol>
    </section>

    <div class="bulk_payment_list_modal">
    </div>
    <div class="pay_now_modal">
    </div>
    <div class="pay_return_due_modal">
    </div>
    
    <!-- Main content -->
    <?= form_open('#', array('class' => '', 'id' => 'table_form')); ?>
    <input type="hidden" id='base_url' value="<?=$base_url;?>">
    <section class="content">
      
      <!-- /.row -->
      <div class="row">
         <!-- ********** ALERT MESSAGE START******* -->
        <?php include"comman/code_flashdata.php"; ?>
        <!-- ********** ALERT MESSAGE END******* -->
        <div class="col-xs-12">
          <div class="box box-primary">
            <div class="box-header with-border">
              <div class="col-lg-3 col-xs-6">
                <!-- small box -->
                <div class="checkbox icheck"><label>
                      <input type="checkbox" name="show_account_receivable" id='show_account_receivable'> <?= $this->lang->line('view_account_receivable_customers'); ?>
                      </label>
                    </div>
                </div>

              <?php if($CI->permissions('customers_add')) { ?>
              <div class="box-tools">
                
                <a class="btn btn-block btn-info" href="<?php echo $base_url; ?>customers/add">
                <i class="fa fa-plus"></i> <?= $this->lang->line('new_customer'); ?></a>
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
                  <th><?= $this->lang->line('customer_id'); ?></th>
                  <th><?= $this->lang->line('customer_name'); ?></th>
                  <th><?= $this->lang->line('mobile'); ?></th>
                  <th><?= $this->lang->line('email'); ?></th>
                  <th><?= $this->lang->line('location'); ?></th>
                  <th><?= $this->lang->line('credit_limit'); ?></th>
                  <th><?= $this->lang->line('previous_due'); ?></th>
                  <!-- <th><?= $this->lang->line('sales_due'); ?>(-)</th> -->
                  <th><?= $this->lang->line('sales_return_due'); ?>(+)</th>
                  <!-- <th><?= $this->lang->line('total'); ?>(+)</th> -->
                  <th><?= $this->lang->line('advance'); ?></th>
                  <th><?= $this->lang->line('status'); ?></th>
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
                      <th style="text-align:right">Total</th>
                      <th></th>
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
function load_datatable(show_account_receivable='unchecked'){
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
            "url": "<?php echo site_url('customers/ajax_list')?>",
            "type": "POST",
            "data": {
                      show_account_receivable: show_account_receivable
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
            "targets": [ 0,11,6 ], //first column / numbering column
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
            var invoice_total = api
                .column( 7, { page: 'none'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
            var sales_due = api
                .column( 8, { page: 'none'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
            /*var sales_return_due = api
                .column( 8, { page: 'none'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );*/
            //$( api.column( 0 ).footer() ).html('Total');
            $( api.column( 6 ).footer() ).html(to_Fixed(invoice_total));
            $( api.column( 8 ).footer() ).html(to_Fixed(sales_due));
            //$( api.column( 8 ).footer() ).html((sales_return_due));
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

$('#show_account_receivable').on('ifChanged', function(event) {
   $('#example2').DataTable().destroy();
    if(event.target.checked){
      load_datatable('checked');
    }
    else{
      load_datatable();//default unchecked
    }
});

function show_receipt(id){
  window.open("<?= base_url();?>customers/print_show_receipt/"+id, "_blank", "scrollbars=1,resizable=1,height=500,width=500");
}

</script>

<script src="<?php echo $theme_link; ?>js/customers.js"></script>
<!-- Make sidebar menu hughlighter/selector -->
<script>$(".<?php echo basename(__FILE__,'.php');?>-active-li").addClass("active");</script>

</body>
</html>
