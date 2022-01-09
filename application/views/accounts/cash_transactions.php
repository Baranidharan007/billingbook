<!DOCTYPE html>
<html>

<head>
<!-- TABLES CSS CODE -->
<?php $this->load->view('comman/code_css.php');?>
</head>

<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">
  
  <!-- Left side column. contains the logo and sidebar -->
  
  <?php $this->load->view('sidebar');?>
  <?php $CI =& get_instance(); ?>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <?= $page_title?>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo $base_url; ?>dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active"><?= $this->lang->line('accounts_list'); ?></li>
      </ol>
    </section>

    <!-- **********************MODALS***************** -->
  <?php $this->load->view('modals/modal_account_link');?>
      <!-- **********************MODALS END***************** -->

    <!-- Main content -->
    <?= form_open('#', array('class' => '', 'id' => 'table_form')); ?>
    <input type="hidden" id='base_url' value="<?=$base_url;?>">
    <section class="content">
      <div class="row">
        <!-- ********** ALERT MESSAGE START******* -->
          <?php $this->load->view('comman/code_flashdata');?>
            <!-- ********** ALERT MESSAGE END******* -->
        

        <div class="col-xs-12">
          <div class="box box-primary">
            <div class="box-header ">
             
              <div class="row">

                <div class="col-md-12">
                  
                  <div class="col-md-3">
                    <div class="form-group">
                       <label for="from_date"><?= $this->lang->line('from_date'); ?> </label></label>
                       <div class="input-group date">
                         <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                         </div>
                         <input type="text" class="form-control pull-right datepicker"  id="from_date" name="from_date">
                      </div>
                       <span id="transfer_date_msg" style="display:none" class="text-danger"></span>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group">
                       <label for="to_date"><?= $this->lang->line('to_date'); ?> </label></label>
                       <div class="input-group date">
                         <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                         </div>
                         <input type="text" class="form-control pull-right datepicker"  id="to_date" name="to_date">
                      </div>
                       <span id="transfer_date_msg" style="display:none" class="text-danger"></span>
                    </div>
                  </div>

                  

                  <div class="col-md-3">
                    <div class="form-group">
                       <label for="users"><?= $this->lang->line('users'); ?> </label></label>
                       <select class="form-control select2" id="users" name="users"  style="width: 100%;">
                        <?= get_users_select_list($this->session->userdata("role_id"),get_current_store_id()); ?>
                     </select>
                       <span id="users_msg" style="display:none" class="text-danger"></span>
                    </div>
                  </div>


                  
                  
                </div>
              </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <table id="example2" class="table table-bordered custom_hover" width="100%">
                <thead class="bg-gray ">
                <tr>
                  <th><?= $this->lang->line('date'); ?></th>
                  <th><?= $this->lang->line('payment_code'); ?></th>
                  <th><?= $this->lang->line('payment_type'); ?></th>
                  <th><?= $this->lang->line('payment'); ?></th>
                  <th><?= $this->lang->line('note'); ?></th>
                  <th><?= $this->lang->line('created_by'); ?></th>
                  <th><?= $this->lang->line('account'); ?></th>
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

<script type="text/javascript">
function load_datatable(){
    //datatables
   var table = $('#example2').DataTable({ 

      /* FOR EXPORT BUTTONS START*/
  dom:'<"row margin-bottom-12"<"col-sm-12"<"pull-left"l><"pull-right"fr><"pull-right margin-left-10 "B>>>tip',
      buttons: {
        buttons: [
            {
                className: 'btn bg-red color-palette btn-flat hidden delete_btn pull-left',
                text: 'Delete',
                action: function ( e, dt, node, config ) {
                    multi_delete();
                }
            },
            { extend: 'copy', className: 'btn bg-teal color-palette btn-flat',exportOptions: { columns: [0,1,2,3,4,5,6]} },
            { extend: 'excel', className: 'btn bg-teal color-palette btn-flat',exportOptions: { columns: [0,1,2,3,4,5,6]} },
            { extend: 'pdf', className: 'btn bg-teal color-palette btn-flat',exportOptions: { columns: [0,1,2,3,4,5,6]} },
            { extend: 'print', className: 'btn bg-teal color-palette btn-flat',exportOptions: { columns: [0,1,2,3,4,5,6]} },
            { extend: 'csv', className: 'btn bg-teal color-palette btn-flat',exportOptions: { columns: [0,1,2,3,4,5,6]} },
            { extend: 'colvis', className: 'btn bg-teal color-palette btn-flat',text:'Columns' },  

            ]
        },
        /* FOR EXPORT BUTTONS END */

        "processing": true, //Feature control the processing indicator.
        "serverSide": true, //Feature control DataTables' server-side processing mode.
        "order": [], //Initial no order.
        "responsive": true,
        "searching": false,
        language: {
            processing: '<div class="text-primary bg-primary" style="position: relative;z-index:100;overflow: visible;">Processing...</div>'
        },
        // Load data for the table's content from an Ajax source
        "ajax": {
            "url": "<?php echo site_url('cash_transactions/ajax_list')?>",
            "type": "POST",
            "data": {
                      
                       from_date: $("#from_date").val(),
                       to_date: $("#to_date").val(),
                       users: $("#users").val(),
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
            "targets": [ 0,1,2,3,4,5,6 ], //first column / numbering column
            "orderable": false, //set not orderable
            
        },
        {
            "targets" :[0],
            "className": "text-center",
        },
        {
            "targets" :[3],
            "className": "text-right",
        },
        
        ],
    });
    new $.fn.dataTable.FixedHeader( table );
}

$(document).ready(function() {
    load_datatable();
});
$("#from_date,#to_date,#users").on("change",function(){
      $('#example2').DataTable().destroy();
      load_datatable();
  });

function link_account(account_of,rec_id,prev_acc_id=''){
  if(account_of==0){
    toastr["warning"]("Can't Link");return;
  }

  $('#account-link-modal').modal('toggle');  

  $("#account_id").val(prev_acc_id).select2();
  $("#prev_acc_id").val(prev_acc_id);
  $("#account_of").val(account_of);
  $("#rec_id").val(rec_id);
}

function update_account_link(){
  var base_url=$("#base_url").val();

    //Initially flag set true
    var flag=true;

    function check_field(id)
    {

      if(!$("#"+id).val() ) //Also check Others????
        {

            $('#'+id+'_msg').fadeIn(200).show().html('Required Field').addClass('required');
           // $('#'+id).css({'background-color' : '#E8E2E9'});
            flag=false;
        }
        else
        {
             $('#'+id+'_msg').fadeOut(200).hide();
             //$('#'+id).css({'background-color' : '#FFFFFF'});    //White color
        }
    }


   //Validate Input box or selection box should not be blank or empty
    check_field("account_id");
    var account_id = $("#account_id").val();//New Updating
    var account_of = $("#account_of").val();//Sales,purchase,expense
    var rec_id = $("#rec_id").val();//Payments id

    if(account_of==0 || account_id=='' || rec_id==''){
      toastr["error"]("Account ID or Record ID missed!!");return;
    }

    if(prev_acc_id==account_id){
     toastr["error"]("This Acccount Already Assigned!!");return; 
    }

    $(".box").append('<div class="overlay"><i class="fa fa-refresh fa-spin"></i></div>');
    $(".payment_save").attr('disabled',true);  //Enable Save or Update button
    $.post(base_url+'cash_transactions/link_account', {account_of:account_of,account_id:account_id,rec_id:rec_id}, function(result) {
      
        if(result=="success")
        {
          $("#account_id").val('');
          $("#rec_id").val('');

          $('#account-link-modal').modal('toggle');
          toastr["success"]("Record Updated Successfully!");
          success.currentTime = 0; 
          success.play();
          $('#example2').DataTable().ajax.reload();
        }
        else if(result=="failed")
        {
           toastr["error"]("Sorry! Failed to Update Record.Try again!");
           failed.currentTime = 0; 
           failed.play();
        }
        else
        {
          toastr["error"](result);
          failed.currentTime = 0; 
          failed.play();
        }
        $(".payment_save").attr('disabled',false);  //Enable Save or Update button
        $(".overlay").remove();
    });
}
</script>


<!-- Make sidebar menu hughlighter/selector -->
<script>$(".cash_transactions-active-li").addClass("active");</script>		
</body>
</html>
