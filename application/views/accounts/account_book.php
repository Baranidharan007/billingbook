<!DOCTYPE html>
<html>

<head>
<!-- TABLES CSS CODE -->
<?php $this->load->view('comman/code_css.php');?>
</head>

<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">
  <?php
  if(isset($account_id)){
      $q2 = $this->db->query("select * from ac_accounts where id=$account_id");
      $account_code=$q2->row()->account_code;
      $created_date=show_date($q2->row()->created_date);
      $account_name=$q2->row()->account_name;
      $balance=store_number_format($q2->row()->balance);
    }
  ?>

  <!-- Left side column. contains the logo and sidebar -->
  
  <?php $this->load->view('sidebar');?>
  <?php $CI =& get_instance(); ?>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <?= $this->lang->line('account_book'); ?>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo $base_url; ?>dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active"><?= $this->lang->line('accounts_list'); ?></li>
      </ol>
    </section>

    <!-- Main content -->
    <?= form_open('#', array('class' => '', 'id' => 'table_form')); ?>
    <input type="hidden" id='base_url' value="<?=$base_url;?>">
    <section class="content">
      <div class="row">
        <!-- ********** ALERT MESSAGE START******* -->
          <?php $this->load->view('comman/code_flashdata');?>
            <!-- ********** ALERT MESSAGE END******* -->
        <div class="col-md-12">
                     <!-- Horizontal Form -->
                     <div class="box box-primary ">
                        
                        <!-- /.box-header -->
                        <!-- form start -->
                        <form class="form-horizontal" id="accounts-form" >
                           <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
                           <input type="hidden" id="base_url" value="<?php echo $base_url;; ?>">
                           <div class="box-body">
                              <div class="row">
                                 <div class="col-md-5">
                                    <div class="form-group">
                                       <label for="account_code" class="col-sm-5 control-label text-right"><?= $this->lang->line('account_code'); ?> :</label>
                                       <div class="col-sm-7">
                                          <label for="account_code" class=" control-label text-right"><?=$account_code?></label>
                                       </div>
                                    </div>
                                    <div class="form-group">
                                       <label for="account_name" class="col-sm-5 control-label text-right"><?= $this->lang->line('account_name'); ?> :</label>
                                       <div class="col-sm-7">
                                          <label for="account_name" class=" control-label text-right"><?=$account_name?></label>
                                       </div>
                                    </div>
                                    <!-- ########### -->
                                 </div>
                                 <div class="col-md-5">
                                    <div class="form-group">
                                       <label for="balance" class="col-sm-5 control-label text-right"><?= $this->lang->line('balance'); ?> :</label>
                                       <div class="col-sm-7">
                                          <label for="balance" class=" control-label text-right"><?=$balance?></label>
                                       </div>                                    
                                    </div>
                                    <div class="form-group">
                                       <label for="balance" class="col-sm-5 control-label text-right"><?= $this->lang->line('created_date'); ?> :</label>
                                       <div class="col-sm-7">
                                          <label for="balance" class=" control-label text-right"><?=$created_date?></label>
                                       </div>                                    
                                    </div>
                                 </div>
                                 <!-- ########### -->
                              </div>
                           </div>
                           <!-- /.box-body -->
                           
                        </form>
                     </div>
                     <!-- /.box -->
                  </div>

        <div class="col-xs-12">
          <div class="box box-primary">
            <div class="box-header ">
              <h3 class="box-title"><?= $this->lang->line('transactions_list'); ?></h3>
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

                  

                  <div class="col-md-3 hide">
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
                  <th><?= $this->lang->line('description'); ?></th>
                  <!-- <th><?= $this->lang->line('debit_account'); ?></th>
                  <th><?= $this->lang->line('credit_account'); ?></th>  -->
                  <th><?= $this->lang->line('debit_amount'); ?></th>
                  <th><?= $this->lang->line('credit_amount'); ?></th>
                  <th><?= $this->lang->line('balance'); ?></th>
                  <th><?= $this->lang->line('note'); ?></th>
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
            "url": "<?php echo site_url('account_transactions/ajax_list')?>",
            "type": "POST",
            "data": {
                      account_id : '<?=$account_id?>',
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
            "targets": [ 0,1,2,3,4 ], //first column / numbering column
            "orderable": false, //set not orderable
            
        },
        {
            "targets" :[0],
            "className": "text-center",
        },
        {
            "targets" :[2,3,4],
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
</script>

<script>
  //Delete Record start
function delete_transaction(q_id,entry_of)
{   
    var base_url = $("#base_url").val();
    //entry_of=(entry_of==1) ? 'transfer' : 'deposit';

   if(confirm("Are you Sure ?\nIt will Delete Real Payments entry as well!!")){
    $(".box").append('<div class="overlay"><i class="fa fa-refresh fa-spin"></i></div>');
   $.post(base_url+"account_transactions/delete_transaction",{q_id:q_id,entry_of:entry_of},function(result){
   result=result;
     if(result=="success")
        {
          toastr["success"]("Record Deleted Successfully!");
          success.currentTime = 0; 
          success.play();
          $('#example2').DataTable().ajax.reload();
        }
        else if(result=="failed"){
          toastr["error"]("Failed to Delete .Try again!");
          failed.currentTime = 0; 
          failed.play();
        }
        else{
          toastr["error"](result);
          failed.currentTime = 0; 
          failed.play();
        }
        $(".overlay").remove();
        return false;
   });
   }//end confirmation
}
</script>
<!-- Make sidebar menu hughlighter/selector -->
<script>$(".accounts_list-active-li").addClass("active");</script>		
</body>
</html>
