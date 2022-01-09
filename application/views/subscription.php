<!DOCTYPE html>
<html>

<head>
<!-- TABLES CSS CODE -->
<?php include"comman/code_css.php"; ?>
<link rel="stylesheet" href="<?php echo $theme_link; ?>css/subscription.css">
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
        <small>View/Search Subscription</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo $base_url; ?>dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
        
        <li class="active"><?=$page_title;?></li>
      </ol>
    </section>
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
          <div class="">
            <div class="planContainer">
              <!--  -->
                      

                        <div class="plan">
                          <div class="titleContainer">
                            <div class="title"><?= $package_list[1]['package_name']; ?></div>
                          </div>
                          <div class="infoContainer">

                            <?php if($package_list[1]['monthly_price']>0){ ?>
                            <div class="price">
                              <p><?= $CI->currency($package_list[1]['monthly_price']) ?> </p><span>/<?= $this->lang->line('month'); ?></span>
                            </div>
                          <?php } else{ ?>
                            <div class="price">
                              <p><?= $CI->currency($package_list[1]['annual_price']) ?> </p><span>/<?= $this->lang->line('annual'); ?></span>
                            </div>
                          <?php } ?>

                            <div class="p desc"><em><?= $CI->currency($package_list[1]['description']) ?></em></div>
                            <ul class="features">
                              <li><strong><?= ($package_list[1]['max_warehouses']) ?></strong> <?= $this->lang->line('warehouses'); ?></li>
                              <li><strong><?= ($package_list[1]['max_users']) ?></strong> <?= $this->lang->line('users'); ?></li>
                              <li><strong><?= ($package_list[1]['max_items']) ?></strong> <?= $this->lang->line('items'); ?></li>
                              <li><strong><?= ($package_list[1]['max_invoices']) ?></strong> <?= $this->lang->line('invoices'); ?></li>
                              
                            </ul>
                        
                            <?php if($package_list[1]['monthly_price']==0 && $package_list[1]['monthly_price']==0) {?>
                                <a class="selectPlan pay_btn"><?= $this->lang->line('subscribe'); ?></a>
                            <?php } else{ ?>

                              <hr>
                            <span class="text-uppercase "><?= $this->lang->line('select_payment_gateway'); ?></span>
                            <div class="price">
                              <label class="pointer text-blue"><input type="radio" name="gateway" value="paypal" checked> <?= $this->lang->line('paypal'); ?></label>
                            </div>

                             <div class="price">
                              <label class="pointer text-blue"><input type="radio" value="instamojo" name="gateway" > <?= $this->lang->line('instamojo'); ?></label>
                            </div>

                                <a class="selectPlan pay_btn"><?= $this->lang->line('pay'); ?></a>

                            <?php } ?>
                            <a href="<?=base_url('subscription')?>">Back</a>
                          </div>
                        </div>

             

              
              <!--  -->
            </div>
          </div>
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
<script>
  $(".pay_btn").on("click",function(){
    if(confirm("Are you sure ?")){
      var base_url = $("#base_url").val();
      if($("input[name='gateway']:checked").val()=='paypal'){
        location.href = base_url+"online_payments/buy_package/paypal/<?=$package_list[1]['id']?>";
      }
      else if($("input[name='gateway']:checked").val()=='instamojo'){
        location.href = base_url+"online_payments/buy_package/instamojo/<?=$package_list[1]['id']?>";
      }
      else{
        location.href = base_url+"online_payments/free_package/<?=$package_list[1]['id']?>";
      }
    }
}); 
</script>


<!-- Make sidebar menu hughlighter/selector -->
<script>$(".<?php echo basename(__FILE__,'.php');?>-active-li").addClass("active");</script>
    
</body>
</html>
