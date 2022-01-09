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
                 $payment_type=$amount=$note="";
                 $customer_id='';
                 $payment_date =show_date(date("d-m-Y"));
            }
            ?>
         <!-- Content Wrapper. Contains page content -->
         <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
               <h1>
                  <?=$page_title;?>
                  <small>Add/Update Brand</small>
               </h1>
               <ol class="breadcrumb">
                  <li><a href="<?php echo $base_url; ?>dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
                  <li><a data-toggle='tooltip' title='Do you want Import Customers ?' href="<?php echo $base_url; ?>customers"> <?= $this->lang->line('customers_list'); ?></a></li>
                  <li><a href="<?php echo $base_url; ?>customers_advance"><?= $this->lang->line('advance_payments_list'); ?></a></li>
                  <li class="active"><?=$page_title;?></li>
               </ol>
            </section>

            <!-- **********************MODALS***************** -->
            <?php $this->load->view('modals/modal_customer');?>
            <!-- **********************MODALS END***************** -->
            <!-- Main content -->
            <section class="content">
               <div class="row">
                  <!-- right column -->
                  <div class="col-md-12">
                     <!-- Horizontal Form -->
                     <div class="box box-primary ">
                        <div class="box-header with-border">
                           <h3 class="box-title">Please Enter Valid Data</h3>
                        </div>
                        <!-- /.box-header -->
                        <!-- form start -->
                        <form class="form-horizontal" id="advance-form">
                           <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
                           <input type="hidden" id="base_url" value="<?php echo $base_url;; ?>">
                           <div class="box-body">
                              <!-- Store Code -->
                               <?php 
                                echo "<input type='hidden' name='store_id' id='store_id' value='".get_current_store_id()."'>";
                               ?>
                              <!-- Store Code end -->
                              <div class="form-group">
                                 <label for="payment_date" class="col-sm-2 control-label"><?= $this->lang->line('date'); ?><label class="text-danger">*</label></label>
                                 <div class="col-sm-4">
                                    <div class="input-group date">
                                       <div class="input-group-addon">
                                          <i class="fa fa-calendar"></i>
                                       </div>
                                       <input type="text" class="form-control pull-right datepicker"  id="payment_date" name="payment_date" readonly onkeyup="shift_cursor(event,'sales_status')" value="<?= $payment_date;?>">
                                    </div>
                                    <span id="payment_date_msg" style="display:none" class="text-danger"></span>
                                 </div>
                              </div>
                              <div class="form-group">
                                 <label for="customer_id" class="col-sm-2 control-label"><?= $this->lang->line('customer_name'); ?><label class="text-danger">*</label></label>
                                 <div class="col-sm-4">
                                    <div class="input-group">
                                       <select class="form-control select2" id="customer_id" name="customer_id"  style="width: 100%;">
                                        <option value="">Select</option>
                                          <?= get_customers_select_list($customer_id,get_current_store_id()); ?>
                                       </select>
                                       <span class="input-group-addon pointer" data-toggle="modal" data-target="#customer-modal" title="New Customer?"><i class="fa fa-user-plus text-primary fa-lg"></i></span>
                                    </div>
                                    <span id="customer_id_msg" style="display:none" class="text-danger"></span>
                                    
                                 </div>
                              </div>
                              <div class="form-group">
                                 <label for="amount" class="col-sm-2 control-label"><?= $this->lang->line('amount'); ?><label class="text-danger">*</label></label>
                                 <div class="col-sm-4">
                                    <input type="text" class="form-control input-sm" id="amount" name="amount" placeholder="" value="<?php print $amount; ?>" autofocus >
                                    <span id="amount_msg" style="display:none" class="text-danger"></span>
                                 </div>
                              </div>
                              <div class="form-group">
                                <label for="payment_type" class="col-sm-2 control-label"><?= $this->lang->line('payment_type'); ?><label class="text-danger">*</label></label>
                                <div class="col-sm-4">
                                  <select class="form-control select2" id='payment_type' name="payment_type">
                                    <?php
                                      $q1=$this->db->query("select * from db_paymenttypes where status=1 and store_id=".get_current_store_id());
                                       if($q1->num_rows()>0){
                                          echo "<option value=''>-Select-</option>";
                                           foreach($q1->result() as $res1){
                                            $selected = (!empty($payment_type) && ($res1->payment_type==$payment_type)) ? 'selected' : '';
                                           echo "<option $selected value='".$res1->payment_type."'>".$res1->payment_type ."</option>";
                                         }
                                       }
                                       else{
                                          echo "<option>None</option>";
                                       }
                                      ?>
                                  </select>
                                  <span id="payment_type_msg" style="display:none" class="text-danger"></span>
                                </div>
                              </div>
                              <div class="form-group">
                                 <label for="note" class="col-sm-2 control-label"><?= $this->lang->line('note'); ?></label>
                                 <div class="col-sm-4">
                                    <textarea type="text" class="form-control" id="note" name="note" placeholder=""><?php print $note; ?></textarea>
                                    <span id="note_msg" style="display:none" class="text-danger"></span>
                                 </div>
                              </div>
                           </div>
                           <!-- /.box-footer -->
                           <div class="box-footer">
                              <div class="col-sm-8 col-sm-offset-2 text-center">
                                 <!-- <div class="col-sm-4"></div> -->
                                 <?php
                                    if(isset($q_id)){
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
         <?php $this->load->view('footer');?>
         <!-- Add the sidebar's background. This div must be placed
            immediately after the control sidebar -->
         <div class="control-sidebar-bg"></div>
      </div>
      <!-- ./wrapper -->
      <!-- SOUND CODE -->
      <?php $this->load->view('comman/code_js_sound.php');?>
      <!-- TABLES CODE -->
      <?php $this->load->view('comman/code_js.php');?>
      
      <script src="<?php echo $theme_link; ?>js/customers_advance/advance.js"></script>
      <script src="<?php echo $theme_link; ?>js/modals.js"></script>
      <script type="text/javascript">
        <?php if(isset($q_id)){ ?>
          $("#store_id").attr('readonly',true);
        <?php }?>
      </script>
      <!-- Make sidebar menu hughlighter/selector -->
      <script>$(".<?php echo basename(__FILE__,'.php');?>-active-li").addClass("active");</script>
   </body>
</html>
