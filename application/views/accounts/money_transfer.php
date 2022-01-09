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
               $debit_account_id =$credit_account_id =$note=$q_id=$store_id=$reference_no='';
               $transfer_code = get_init_code("money_transfer");
               $amount=0;
               $transfer_date=show_date(date("d-m-Y"));
            }
            ?>
         <!-- Content Wrapper. Contains page content -->
         <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
               <h1>
                  <?= $page_title ?>
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
                        <form class="form-horizontal" id="money_transfer-form" >
                           <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
                           <input type="hidden" id="base_url" value="<?php echo $base_url;; ?>">
                           <input type="hidden" id="store_id" name="store_id" value="<?php echo get_current_store_id(); ?>">

                           <div class="box-body">
                              <div class="form-group">
                                 <label for="transfer_date" class="col-sm-2 control-label"><?= $this->lang->line('transfer_date'); ?> <label class="text-danger">*</label></label>
                                    <div class="col-sm-3">
                                       <div class="input-group date">
                                          <div class="input-group-addon">
                                             <i class="fa fa-calendar"></i>
                                          </div>
                                          <input type="text" class="form-control pull-right datepicker"  id="transfer_date" name="transfer_date" readonly onkeyup="shift_cursor(event,'sales_status')" value="<?= $transfer_date;?>">
                                       </div>
                                       <span id="transfer_date_msg" style="display:none" class="text-danger"></span>
                                    </div>
                                    <label for="transfer_code" class="col-sm-2 control-label"><?= $this->lang->line('transfer_code'); ?> <label class="text-danger">*</label></label>
                                       <div class="col-sm-3">
                                          <input type="text" class="form-control" id="transfer_code" name="transfer_code" placeholder=""  value="<?php print $transfer_code; ?>" >
                                          <span id="transfer_code_msg" style="display:none" class="text-danger"></span>
                                       </div>
                              </div>

                              <div class="form-group">
                                     <label for="debit_account_id" class="col-sm-2 control-label"><?= $this->lang->line('debit_account'); ?> <label class="text-danger">*</label></label>
                                       <div class="col-sm-3">
                                          <select class="form-control select2" id="debit_account_id" name="debit_account_id"  style="width: 100%;">
                                          <?php
                                             echo '<option value="">Select</option>'; 
                                             echo get_accounts_select_list($debit_account_id);
                                             ?>
                                          </select>
                                          <span id="debit_account_id_msg" style="display:none" class="text-danger"></span>
                                       </div>  

                                       <label for="credit_account_id" class="col-sm-2 control-label"><?= $this->lang->line('credit_account'); ?> <label class="text-danger">*</label></label>
                                       <div class="col-sm-3">
                                          <select class="form-control select2" id="credit_account_id" name="credit_account_id"  style="width: 100%;">
                                          <?php
                                             echo '<option value="">Select</option>'; 
                                             echo get_accounts_select_list($credit_account_id);
                                             ?>
                                          </select>
                                          <span id="credit_account_id_msg" style="display:none" class="text-danger"></span>
                                       </div>
                              </div>

                              <div class="form-group">
                                       <label for="amount" class="col-sm-2 control-label"><?= $this->lang->line('amount'); ?> <label class="text-danger">*</label></label>
                                       <div class="col-sm-3">
                                          <input type="text" class="form-control only_currency" id="amount" name="amount" placeholder="" value="<?php print store_number_format($amount,0); ?>">
                                          <span id="amount_msg" style="display:none" class="text-danger"></span>
                                       </div>
                                       <label for="reference_no" class="col-sm-2 control-label"><?= $this->lang->line('reference_no'); ?></label>
                                       <div class="col-sm-3">
                                          <input type="text" class="form-control" id="reference_no" name="reference_no" placeholder=""  value="<?php print $reference_no; ?>" >
                                          <span id="reference_no_msg" style="display:none" class="text-danger"></span>
                                       </div>

                                       
                                    
                              </div>
                              <div class="form-group">
                                 <label for="note" class="col-sm-2 control-label"><?= $this->lang->line('note'); ?></label>
                                       <div class="col-sm-3">
                                          <textarea type="text" class="form-control" id="note" name="note" placeholder="" ><?php print $note; ?></textarea>
                                          <span id="note_msg" style="display:none" class="text-danger"></span>
                                       </div>
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
      <script src="<?php echo $theme_link; ?>js/accounts/money_transfer.js"></script>
      <script type="text/javascript">
         <?php if(isset($q_id)){ ?>
           $("#store_id").attr('readonly',true);
         <?php }?>
         
      </script>
      <!-- Make sidebar menu hughlighter/selector -->
      <script>$(".<?php echo basename(__FILE__,'.php');?>-active-li").addClass("active");</script>
   </body>
</html>
