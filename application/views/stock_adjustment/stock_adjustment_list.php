<!DOCTYPE html>
<html>
   <head>
      <!-- TABLES CSS CODE -->
      <?php $this->load->view('comman/code_css.php');?>
      <!-- bootstrap datepicker -->
      <link rel="stylesheet" href="<?php echo $theme_link; ?>plugins/datepicker/datepicker3.css">
   </head>
   <body class="hold-transition skin-blue sidebar-mini">
      <!-- Change the theme color if it is set -->
      <div class="wrapper">
         <!-- Left side column. contains the logo and sidebar -->
         <?php $this->load->view('sidebar');?>
        <?php $CI =& get_instance(); ?>
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
               
               <!-- /.row -->
               <div class="row">
                  <!-- ********** ALERT MESSAGE START******* -->
                  <?php $this->load->view('comman/code_flashdata');?>
                  <!-- ********** ALERT MESSAGE END******* -->
                  <div class="col-xs-12">
                     <div class="box box-primary">
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
                           <?php if($CI->permissions('stock_adjustment_add')) { ?>
                           <div class="box-tools">
                              <a class="btn btn-block btn-info" href="<?php echo $base_url; ?>stock_adjustment/add">
                              <i class="fa fa-plus"></i> <?= $this->lang->line('new_stock_adjustment'); ?></a>
                           </div>
                           <?php } ?>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">
                           <table id="example2" class="table table-bordered " width="100%">
                              <thead class="bg-gray ">
                                 <tr>
                                    <th class="text-center">
                                       <input type="checkbox" class="group_check checkbox" >
                                    </th>
                                    <th><?= $this->lang->line('adjustment_date'); ?></th>
                                    <th><?= $this->lang->line('reference_no'); ?></th>
                                    <th><?= $this->lang->line('created_by'); ?></th>
                                    <th><?= $this->lang->line('action'); ?></th>
                                 </tr>
                              </thead>
                              <tbody>
                              </tbody>
                             
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
         <?php $this->load->view('footer');?>
         <!-- Add the sidebar's background. This div must be placed
            immediately after the control sidebar -->
         <div class="control-sidebar-bg"></div>
      </div>
      <!-- ./wrapper -->
      <!-- SOUND CODE -->
      <?php $this->load->view('comman/code_js_sound');?>
      <!-- TABLES CODE -->
      <?php $this->load->view('comman/code_js');?>
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
                     { extend: 'copy', className: 'btn bg-teal color-palette btn-flat',footer: true, exportOptions: { columns: [1,2,3]} },
                     { extend: 'excel', className: 'btn bg-teal color-palette btn-flat',footer: true, exportOptions: { columns: [1,2,3]} },
                     { extend: 'pdf', className: 'btn bg-teal color-palette btn-flat',footer: true, exportOptions: { columns: [1,2,3]} },
                     { extend: 'print', className: 'btn bg-teal color-palette btn-flat',footer: true, exportOptions: { columns: [1,2,3]} },
                     { extend: 'csv', className: 'btn bg-teal color-palette btn-flat',footer: true, exportOptions: { columns: [1,2,3]} },
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
                     "url": "<?php echo site_url('stock_adjustment/ajax_list')?>",
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
                     "targets": [ 0,4 ], //first column / numbering column
                     "orderable": false, //set not orderable
                 },
                 {
                     "targets" :[0],
                     "className": "text-center",
                 },
                 
                 ],
                  
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
      <script src="<?php echo $theme_link; ?>js/stock_adjustment/stock_adjustment.js"></script>
      <!-- Make sidebar menu hughlighter/selector -->
      <script>$(".<?php echo basename(__FILE__,'.php');?>-active-li").addClass("active");</script>
   </body>
</html>
