<!DOCTYPE html>
<html>
   <head>
      <!-- TABLES CSS CODE -->
      <?php $this->load->view('comman/code_css.php');?>
      <!-- </copy> -->  
   </head>
   <body class="hold-transition skin-blue sidebar-mini">
      <div class="wrapper">
         <?php $this->load->view('sidebar');?>
         <?php
            if(!isset($q_id)){
               $parent_id =$account_name=$note=$q_id=$store_id='';
               $account_code = get_init_code("accounts");
               $opening_balance=0;
            }
            ?>
         <!-- Content Wrapper. Contains page content -->
         <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
               <h1>
                  <?= $this->lang->line('accounts'); ?>
                  <small>Add/Update Accounts</small>
               </h1>
               <ol class="breadcrumb">
                  <li><a href="<?php echo $base_url; ?>dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
                  <li><a href="<?php echo $base_url; ?>accounts"><?= $this->lang->line('accounts_list'); ?></a></li>
                  <li class="active"><?= $this->lang->line('accounts'); ?></li>
               </ol>
            </section>
            <!-- Main content -->
            <section class="content">
               <div class="row">
                  <!-- ********** ALERT MESSAGE START******* -->
                  <?php $this->load->view('comman/code_flashdata');?>
                  <!-- ********** ALERT MESSAGE END******* -->
                  <!-- right column -->
                  <div class="col-md-12">
                     <!-- Horizontal Form -->
                     <div class="box box-primary ">
                        <div class="box-header with-border">
                           <h3 class="box-title">Please Enter Valid Data</h3>
                        </div>
                        <!-- /.box-header -->
                        <!-- form start -->
                        <form class="form-horizontal" id="accounts-form" >
                           <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
                           <input type="hidden" id="base_url" value="<?php echo $base_url;; ?>">
                           <div class="box-body">
                              <div class="row">
                                 <div class="col-md-5">
                                    <!-- Store Code -->
                                    <?php 
                                       echo "<input type='hidden' name='store_id' id='store_id' value='".get_current_store_id()."'>";
                                     ?>
                                    <!-- Store Code end -->
                                    <div class="form-group">
                                       <label for="parent_id" class="col-sm-4 control-label"><?= $this->lang->line('parent_account'); ?> <label class="text-danger">*</label></label>
                                       <div class="col-sm-8">
                                          <select class="form-control select2" id="parent_id" name="parent_id"  style="width: 100%;">
                                          <?php
                                             echo '<option value="">-CREATE ACCOUNT HEAD-</option>'; 
                                             echo get_accounts_select_list($parent_id);
                                             ?>
                                          </select>
                                          <span id="parent_id_msg" style="display:none" class="text-danger"></span>
                                       </div>
                                    </div>
                                    <div class="form-group">
                                       <label for="account_code" class="col-sm-4 control-label"><?= $this->lang->line('account_code'); ?> <label class="text-danger">*</label></label>
                                       <div class="col-sm-8">
                                          <input type="text" class="form-control" id="account_code" name="account_code" placeholder=""  value="<?php print $account_code; ?>" >
                                          <span id="account_code_msg" style="display:none" class="text-danger"></span>
                                       </div>
                                    </div>
                                    <div class="form-group">
                                       <label for="account_name" class="col-sm-4 control-label"><?= $this->lang->line('account_name'); ?> <label class="text-danger">*</label></label>
                                       <div class="col-sm-8">
                                          <input type="text" class="form-control" id="account_name" name="account_name" placeholder=""  value="<?php print $account_name; ?>" >
                                          <span id="account_name_msg" style="display:none" class="text-danger"></span>
                                       </div>
                                    </div>
                                    <div class="form-group <?= (!empty($q_id)) ? 'hide' : ''?>">
                                       <label for="opening_balance" class="col-sm-4 control-label"><?= $this->lang->line('opening_balance'); ?> <label class="text-danger">*</label></label>
                                       <div class="col-sm-8">
                                          <input type="text" class="form-control only_currency" id="opening_balance" name="opening_balance" placeholder="" value="<?php print store_number_format($opening_balance,0); ?>">
                                          <span id="opening_balance_msg" style="display:none" class="text-danger"></span>
                                       </div>
                                    </div>
                                    <!-- ########### -->
                                 </div>
                                 <div class="col-md-5">
                                    <div class="form-group">
                                       <label for="note" class="col-sm-4 control-label"><?= $this->lang->line('note'); ?></label>
                                       <div class="col-sm-8">
                                          <textarea type="text" class="form-control" id="note" name="note" placeholder="" ><?php print $note; ?></textarea>
                                          <span id="note_msg" style="display:none" class="text-danger"></span>
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
                                    if($q_id!=""){
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
      <script src="<?php echo $theme_link; ?>js/accounts/accounts.js"></script>
      <script type="text/javascript">
         <?php if(isset($q_id)){ ?>
           $("#store_id").attr('readonly',true);
         <?php }?>
         
      </script>
      <!-- Make sidebar menu hughlighter/selector -->
      <script>$(".<?php echo basename(__FILE__,'.php');?>-active-li").addClass("active");</script>
   </body>
</html>
