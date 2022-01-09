<!DOCTYPE html>
<html>
<head>
<!-- FORM CSS CODE -->
<?php include"comman/code_css.php"; ?>
<!-- </copy> -->  
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

 <?php include"sidebar.php"; ?>


  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
       <?= $page_title ?>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo $base_url; ?>dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active"><?= $page_title; ?></li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <!-- right column -->
        <div class="col-md-12">
                     <!-- Horizontal Form -->
                     <div class="box box-primary ">
                        <!-- /.box-header -->
                        <!-- form start -->
                        <form class="form-horizontal" id="smtp-form" onkeypress="return event.keyCode != 13;">
                           <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
                           <input type="hidden" id="base_url" value="<?php echo $base_url;; ?>">
                           <div class="box-body">
                              <!-- Store Code -->
                               <?php 
                                echo "<input type='hidden' name='store_id' id='store_id' value='".get_current_store_id()."'>";
                                ?>
                              <!-- Store Code end -->
                              <div class="form-group">
                                 <label for="smtp_status" class="col-sm-2 control-label"><?= $this->lang->line('smtp_status'); ?></label>
                                 <div class="col-sm-4">
                                    <select class='form-control select2' name='smtp_status' id="smtp_status">
                                      <option value="1">Enable</option>
                                      <option value="0">Disable</option>
                                    </select>
                                    <span id="smtp_host_msg" style="display:none" class="text-danger"></span>
                                 </div>
                              </div>
                              <div class="form-group">
                                 <label for="smtp_host" class="col-sm-2 control-label"><?= $this->lang->line('smtp_host'); ?></label>
                                 <div class="col-sm-4">
                                    <input type="text" class="form-control input-sm" id="smtp_host" name="smtp_host" placeholder=""  value="<?php print $smtp_host; ?>" autofocus >
                                    <span id="smtp_host_msg" style="display:none" class="text-danger"></span>
                                 </div>
                              </div>
                              <div class="form-group">
                                 <label for="smtp_port" class="col-sm-2 control-label"><?= $this->lang->line('smtp_port'); ?></label>
                                 <div class="col-sm-4">
                                    <input type="text" class="form-control input-sm" id="smtp_port" name="smtp_port" placeholder=""  value="<?php print $smtp_port; ?>" autofocus >
                                    <span id="smtp_port_msg" style="display:none" class="text-danger"></span>
                                 </div>
                              </div>
                              <div class="form-group">
                                 <label for="smtp_user" class="col-sm-2 control-label"><?= $this->lang->line('smtp_user'); ?></label>
                                 <div class="col-sm-4">
                                    <input type="text" class="form-control input-sm" id="smtp_user" name="smtp_user" placeholder="Email"  value="<?php print $smtp_user; ?>" autofocus >
                                    <span id="smtp_user_msg" style="display:none" class="text-danger"></span>
                                 </div>
                              </div>
                              <div class="form-group">
                                 <label for="smtp_pass" class="col-sm-2 control-label"><?= $this->lang->line('smtp_pass'); ?></label>
                                 <div class="col-sm-4">
                                    <input type="password" class="form-control input-sm" id="smtp_pass" name="smtp_pass" placeholder=""  value="<?php print $smtp_pass; ?>" autofocus >
                                    <span id="smtp_pass_msg" style="display:none" class="text-danger"></span>
                                 </div>
                              </div>
                           </div>
                           <!-- /.box-body -->
                           <div class="box-footer">
                              <div class="col-sm-8 col-sm-offset-2 text-center">
                                 <div class="col-md-3 col-md-offset-3">
                                    <button type="button" id="update" class=" btn btn-block btn-success" title="Save Data">Update</button>
                                 </div>
                                 <div class="col-sm-3">
                                    <a href="<?=base_url('dashboard');?>">
                                    <button type="button" class="col-sm-3 btn btn-block btn-warning close_btn" title="Go Dashboard">Close</button>
                                    </a>
                                 </div>
                              </div>
                           </div>
                           <!-- /.box-footer -->
                        </form>
                     </div>
                     <!-- /.box -->
                  </div>
        <!--/.col (right) -->
      </div>
      <!-- /.row -->

    </section>
    <!-- /.content -->
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

<script>
$("#smtp_status").val(<?=$smtp_status?>).select2();
/*Email validation code end*/

$('#update').on("click",function (e) {
  var base_url=$("#base_url").val();
    
    var this_id=this.id;
    
   // swal({ title: "Are you sure?",icon: "warning",buttons: true,dangerMode: true,}).then((sure) => {
 //if(confirm("Are you sure ?")) {//confirmation start
    $(".box").append('<div class="overlay"><i class="fa fa-refresh fa-spin"></i></div>');
    $("#"+this_id).attr('disabled',true);  //Enable Save or Update button
    e.preventDefault();
    data = new FormData($('#smtp-form')[0]);//form name
    $.ajax({
      type: 'POST',
      url: base_url+'smtp/update_smtp',
      data: data,
      cache: false,
      contentType: false,
      processData: false,
      success: function(result){
        //alert(result);//return;

          if(result=="success")
          {
            toastr["success"]("Success! Record Saved Successfully!!");
          }
          else if(result=="failed")
          {
             toastr['error']("Sorry! Failed to save Record.Try again");
          }
          else
          {
            toastr['warning'](result);
          }
        
        $("#"+this_id).attr('disabled',false);  //Enable Save or Update button
        $(".overlay").remove();
       }
     });
 // } //confirmation sure
  //}); //confirmation end

});
</script>
<!-- Make sidebar menu hughlighter/selector -->
<script>$(".<?php echo basename(__FILE__,'.php');?>-active-li").addClass("active");</script>

</body>
</html>
