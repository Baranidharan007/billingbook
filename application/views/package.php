<!DOCTYPE html>
<html>
   <head>
      <!-- TABLES CSS CODE -->
      <?php include"comman/code_css.php"; ?>
      <!-- </copy> -->  
   </head>
   <body class="hold-transition skin-blue sidebar-mini">
      <div class="wrapper">
         <?php include"sidebar.php"; ?>
         <?php
            if(!isset($package_name)){
               $package_name=$mobile=$trial_days=$monthly_price=$annual_price=
               $max_warehouses=
               $max_users=
               $max_items=$max_invoices=
               $description=$package_code=
               $expire_date='';
               $package_type='Free';
            }
            ?>
         <!-- Content Wrapper. Contains page content -->
         <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
               <h1>
                  <?=$page_title;?>
                  <small>Add/Update Package</small>
               </h1>
               <ol class="breadcrumb">
                  <li><a href="<?php echo $base_url; ?>dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
                  <li><a href="<?php echo $base_url; ?>package"><?= $this->lang->line('package_list'); ?></a></li>
                  <li class="active"><?=$page_title;?></li>
               </ol>
            </section>
            <!-- Main content -->
            <section class="content">
               <div class="row">
                  <!-- ********** ALERT MESSAGE START******* -->
                  <?php include"comman/code_flashdata.php"; ?>
                  <!-- ********** ALERT MESSAGE END******* -->
                  <!-- right column -->
                  <div class="col-md-12">
                     <!-- Horizontal Form -->
                     <div class="box box-primary ">
                        <!-- form start -->
                        <?= form_open('#', array('class' => 'form-horizontal', 'id' => 'package-form', 'enctype'=>'multipart/form-data', 'method'=>'POST', 'accept-charset'=>'UTF-8', 'novalidate'=>'novalidate' ));?>
                        <input type="hidden" id="base_url" value="<?php echo $base_url;; ?>">
                        <div class="box-body">
                            <div class="row">
                              <div class="col-md-5">
                                <!-- Store Code -->
                                <?php /*if(store_module() && is_admin()) {$this->load->view('store/store_code',array('show_store_select_box'=>true,'store_id'=>$store_id,'label_length'=>'col-sm-4','div_length'=>'col-sm-8')); }else{*/
                                echo "<input type='hidden' name='store_id' id='store_id' value='".get_current_store_id()."'>";
                              /*}*/ ?>
                                <!-- Store Code end -->
                              </div>
                            </div>
                           <div class="row">
                              <div class="col-md-5">
                                 <div class="form-group">
                                    <label for="package_type" class="col-sm-4 control-label"><?= $this->lang->line('package_type'); ?><label class="text-danger">*</label></label>
                                    <div class="col-sm-8">
                                       <select class="form-control select2" id="package_type" name="package_type"  style="width: 100%;"  >
                                             <option value="Free">Free</option>
                                             <option value="Paid">Paid</option>
                                          </select>
                                    </div>
                                 </div>
                                 <div class="form-group">
                                    <label for="package_name" class="col-sm-4 control-label"><?= $this->lang->line('package_name'); ?><label class="text-danger">*</label></label>
                                    <div class="col-sm-8">
                                       <input type="text" class="form-control" id="package_name" name="package_name" placeholder=""  value="<?php print $package_name; ?>" autofocus>
                                       <span id="package_name_msg" style="display:none" class="text-danger"></span>
                                    </div>
                                 </div>
                                 <div class="form-group">
                                    <label for="description" class="col-sm-4 control-label"><?= $this->lang->line('description'); ?></label>
                                    <div class="col-sm-8">
                                       <textarea type="text" class="form-control" id="description" name="description" placeholder="" ><?php print $description; ?></textarea>
                                       <span id="description_msg" style="display:none" class="text-danger"></span>
                                    </div>
                                 </div>
                                 <div class="form-group">
                                    <label for="monthly_price" class="col-sm-4 control-label"><?= $this->lang->line('monthly_price'); ?><label class="text-danger">*</label></label>
                                    <div class="col-sm-8">
                                       <input type="text" class="form-control only_currency" id="monthly_price" name="monthly_price" placeholder="" value="<?php print $monthly_price; ?>" >
                                       <span class='text-warning pull-right'>Note: Minimum '0'</span>
                                       <span id="monthly_price_msg" style="display:none" class="text-danger"></span>
                                    </div>
                                 </div>
                                 <div class="form-group">
                                    <label for="annual_price" class="col-sm-4 control-label"><?= $this->lang->line('annual_price'); ?><label class="text-danger">*</label></label>
                                    <div class="col-sm-8">
                                       <input type="text" class="form-control only_currency" id="annual_price" name="annual_price" placeholder="" value="<?php print $annual_price; ?>" >
                                       <span class='text-warning pull-right'>Note: Minimum '0'</span>
                                       <span id="annual_price_msg" style="display:none" class="text-danger"></span>
                                    </div>
                                 </div>
                                 <div class="form-group">
                                    <label for="trial_days" class="col-sm-4 control-label"><?= $this->lang->line('trial_days'); ?><label class="text-danger">*</label></label>
                                    <div class="col-sm-8">
                                       <input type="text" class="form-control only_currency" id="trial_days" name="trial_days" placeholder="" value="<?php print $trial_days; ?>" >
                                       <span class='text-warning pull-right'>Note: '-1' for Unlimited</span>
                                       <span id="trial_days_msg" style="display:none" class="text-danger"></span>
                                    </div>
                                 </div>
                                 
                                 <div class="form-group">
                                    <label for="expire_date" class="col-sm-4 control-label"><?= $this->lang->line('package_expire_date'); ?></label>
                                    <div class="col-sm-8">
                                       <div class="input-group date">
                                       <div class="input-group-addon">
                                          <i class="fa fa-calendar"></i>
                                       </div>
                                       <input type="text" class="form-control pull-right datepicker"  id="expire_date" name="expire_date" readonly value="<?= $expire_date;?>">
                                    </div>
                                    </div>
                                 </div>
                                 <!-- ########### -->
                              </div>
                              <div class="col-md-5">
                                 <div class="form-group">
                                    <label for="max_warehouses" class="col-sm-4 control-label"><?= $this->lang->line('max_warehouses'); ?><label class="text-danger">*</label></label>
                                    <div class="col-sm-8">
                                       <input type="text" class="form-control only_currency" id="max_warehouses" name="max_warehouses" placeholder="" value="<?=$max_warehouses?>" >
                                       <span class='text-warning pull-right'>Note: '-1' for Unlimited</span>
                                       <span id="max_warehouses_msg" style="display:none" class="text-danger"></span>
                                    </div>
                                 </div>
                                 <div class="form-group">
                                    <label for="max_users" class="col-sm-4 control-label"><?= $this->lang->line('max_users'); ?><label class="text-danger">*</label></label>
                                    <div class="col-sm-8">
                                       <input type="text" class="form-control only_currency" id="max_users" name="max_users" placeholder="" value="<?=$max_users?>" >
                                       <span class='text-warning pull-right'>Note: '-1' for Unlimited</span>
                                       <span id="max_users_msg" style="display:none" class="text-danger"></span>
                                    </div>
                                 </div>
                                 <div class="form-group">
                                    <label for="max_items" class="col-sm-4 control-label"><?= $this->lang->line('max_items'); ?><label class="text-danger">*</label></label>
                                    <div class="col-sm-8">
                                       <input type="text" class="form-control only_currency" id="max_items" name="max_items" placeholder="" value="<?=$max_items?>" >
                                       <span class='text-warning pull-right'>Note: '-1' for Unlimited</span>
                                       <span id="max_items_msg" style="display:none" class="text-danger"></span>
                                    </div>
                                 </div>
                                 <div class="form-group">
                                    <label for="max_invoices" class="col-sm-4 control-label"><?= $this->lang->line('max_invoices'); ?><label class="text-danger">*</label></label>
                                    <div class="col-sm-8">
                                       <input type="text" class="form-control only_currency" id="max_invoices" name="max_invoices" placeholder="" value="<?=$max_invoices?>" >
                                       <span class='text-warning pull-right'>Note: '-1' for Unlimited</span>
                                       <span id="max_invoices_msg" style="display:none" class="text-danger"></span>
                                    </div>
                                 </div>
                                 
                              </div>
                              <!-- ########### -->
                           </div>
                        </div>
                        <!-- /.box-body -->
                        <div class="box-footer">
                           <div class="col-sm-8 col-sm-offset-2 text-center">
                              <!-- <div class="col-sm-4"></div> -->
                              <?php
                                 if($package_name!=""){
                                      $btn_name="Update";
                                      $btn_id="update";
                                      ?>
                              <input type="hidden" name="q_id" id="q_id" value="<?php echo $q_id;?>"/>
                              <?php
                                 }
                                           else{
                                               $btn_name="Save";
                                               $btn_id="save";
                                           }
                                 
                                           ?>
                              <div class="col-md-3 col-md-offset-3">
                                 <button type="button" id="<?php echo $btn_id;?>" class=" btn btn-block btn-success" title="Save Data"><?php echo $btn_name;?></button>
                              </div>
                              <div class="col-sm-3">
                                    <a href="<?=base_url('dashboard');?>">
                                    <button type="button" class="col-sm-3 btn btn-block btn-warning close_btn" title="Go Dashboard">Close</button>
                                    </a>
                                 </div>
                           </div>
                        </div>
                        <!-- /.box-footer -->
                        <?= form_close(); ?>
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
      <script src="<?php echo $theme_link; ?>js/package.js"></script>
      <script type="text/javascript">
        <?php if(isset($q_id)){ ?>
          $("#store_id").attr('readonly',true);
        <?php }?>
          $("#package_type").val("<?=$package_type?>").select2();
      </script>
      <!-- Make sidebar menu hughlighter/selector -->
      <script>$(".<?php echo basename(__FILE__,'.php');?>-active-li").addClass("active");</script>
 </body>
</html>
