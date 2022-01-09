<!DOCTYPE html>
<html>
<head>
<!-- TABLES CSS CODE -->
<?php $this->load->view('comman/code_css.php');?>
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
<!-- bootstrap datepicker -->
<link rel="stylesheet" href="<?php echo $theme_link; ?>plugins/datepicker/datepicker3.css">
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <!-- Left side column. contains the logo and sidebar -->

  <?php $this->load->view('sidebar.php');?>

  <?php $CI =& get_instance(); ?>
 
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <?=$page_title;?>
       
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo $base_url; ?>dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active"><?=$page_title;?></li>
      </ol>
    </section>

    <div class="pay_now_modal">
    </div>
   

    <!-- Main content -->
    <?=form_open('#', array('class' => '', 'id' => 'table_form'));?>
    <input type="hidden" id='base_url' value="<?=$base_url;?>">
    <section class="content">
      <!-- Small boxes (Stat box) -->


<div class="row">
  <!-- ********** ALERT MESSAGE START******* -->
        <?php $this->load->view('comman/code_flashdata.php');?>
        <!-- ********** ALERT MESSAGE END******* -->
</div>






      <div class="row">
        
        <div class="col-xs-12 ">
          <div class="box box-primary">
            <div class="box-header with-border">
              <!-- <h3 class="box-title"><?=$page_title;?></h3> -->

             
              <div class="row">

                <div class="col-md-12">

                


                  <div class="col-md-3">
                    <div class="form-group">
                       <label for="payment_type_search"><?=$this->lang->line('payment_type');?> </label></label>
                       <select class="form-control select2" id="payment_type_search" name="payment_type_search"  style="width: 100%;">
                        <?php
                          $q1=$this->db->query("select * from db_paymenttypes where status=1 and store_id=".get_current_store_id());
                           if($q1->num_rows()>0){
                              echo "<option value=''>-Select-</option>";
                               foreach($q1->result() as $res1){
                               echo "<option value='".$res1->payment_type."'>".$res1->payment_type ."</option>";
                             }
                           }
                           else{
                              echo "<option>None</option>";
                           }
                          ?>
                     </select>
                       <span id="payment_type_search_msg" style="display:none" class="text-danger"></span>
                    </div>
                  </div>

                   <div class="col-md-3">
                    <div class="form-group">
                       <label for="cheque_status_search"><?=$this->lang->line('payment_type');?> </label></label>
                       <select class="form-control select2" id="cheque_status_search" name="cheque_status_search"  style="width: 100%;">
                        <?=get_cheque_status_select_list()?>
                     </select>
                       <span id="cheque_status_search_msg" style="display:none" class="text-danger"></span>
                    </div>
                  </div>
                  


                </div>
              </div>

            </div>
            <!-- /.box-header -->
            <div class="box-body ">
              <table id="example2" class="table custom_hover " width="100%">
                <thead class="bg-gray ">
                <tr>
                  
                  <th><?=$this->lang->line('payment_code');?></th>
                  <th><?=$this->lang->line('payment_date');?></th>
                  <th><?=$this->lang->line('sales_code');?></th>
                  <th><?=$this->lang->line('customer_name');?></th>
                  <th><?=$this->lang->line('payment');?></th>
                  <th><?=$this->lang->line('payment_type');?></th>
                  <th><?=$this->lang->line('payment_note');?></th>
                  <th><?=$this->lang->line('created_by');?></th>
                  <th><?=$this->lang->line('action');?></th>
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
    <?=form_close();?>
  </div>
  <!-- /.content-wrapper -->
  <?php $this->load->view('footer.php');?>
  <!-- Add the sidebar's background. This div must be placed
       immediately after the control sidebar -->
  <div class="control-sidebar-bg"></div>
</div>
<!-- ./wrapper -->

<!-- SOUND CODE -->
<?php $this->load->view('comman/code_js_sound.php');?>
<!-- TABLES CODE -->
<?php $this->load->view('comman/code_js.php');?>
<!-- bootstrap datepicker -->
<script src="<?php echo $theme_link; ?>plugins/datepicker/bootstrap-datepicker.js"></script>
<script src="<?php echo $theme_link; ?>js/sales_payments/create.js"></script>
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
                  { extend: 'copy', className: 'btn bg-teal color-palette btn-flat',footer: true, exportOptions: { columns: [0,1,2,3,4,5,6,7,8]} },
                  { extend: 'excel', className: 'btn bg-teal color-palette btn-flat',footer: true, exportOptions: { columns: [0,1,2,3,4,5,6,7,8]} },
                  { extend: 'pdf', className: 'btn bg-teal color-palette btn-flat',footer: true, exportOptions: { columns: [0,1,2,3,4,5,6,7,8]} },
                  { extend: 'print', className: 'btn bg-teal color-palette btn-flat',footer: true, exportOptions: { columns: [0,1,2,3,4,5,6,7,8]} },
                  { extend: 'csv', className: 'btn bg-teal color-palette btn-flat',footer: true, exportOptions: { columns: [0,1,2,3,4,5,6,7,8]} },
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
                  "url": "<?php echo site_url('sales_payments/ajax_list') ?>",
                  "type": "POST",
                  "data": {
                      
                      cheque_status_search: $("#cheque_status_search").val(),
                      payment_type_search: $("#payment_type_search").val(),
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
                  "targets": [ 0,8 ], //first column / numbering column
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
     
      $("#cheque_status_search,#payment_type_search").on("change",function(){
        $('#example2').DataTable().destroy();
        load_datatable();
      });


</script>

<!-- Make sidebar menu hughlighter/selector -->
<script>$(".sales-payments-list-active-li").addClass("active");</script>

</body>
</html>
