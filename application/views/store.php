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
                
                $store_name=$logo=$currency_id=$currency_placement=$timezone=
                $date_format=$time_format=
                $round_off='';
                $mobile=$phone=$email=$country=$state=$city=
                $postcode=$address=$gst_no=$vat_no=
                $store_website=$pan_no=$bank_details=$store_logo='';

                $decimals=2;
                
                
               
                
              }
          ?>

         <!-- Content Wrapper. Contains page content -->
         <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
               <h1>
                  <?= $page_title; ?>
               </h1>
               <ol class="breadcrumb">
                  <li><a href="<?php echo $base_url; ?>dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
                  <li class="active"><?= $page_title; ?></li>
               </ol>
            </section>
            
            <!-- Main content -->
  <?= form_open('#', array('class' => 'form-horizontal', 'id' => 'store-form', 'enctype'=>'multipart/form-data', 'method'=>'POST'));?>
            <section class="content">
               <div class="row">
                  <!-- ********** ALERT MESSAGE START******* -->
                <?php $this->load->view('comman/code_flashdata');?>
                  <!-- ********** ALERT MESSAGE END******* -->

                  <div class="col-md-12">
                     <!-- Custom Tabs -->
                     <div class="nav-tabs-custom">
                        <ul class="nav nav-tabs">
                           <li class="active"><a href="#tab_4" id='tab_4_btn' data-toggle="tab"><?= $this->lang->line('store'); ?></a></li>
                           <li><a href="#tab_1" id='tab_1_btn' data-toggle="tab"><?= $this->lang->line('system'); ?></a></li>
                           <?php if(!is_user()){?>
                           <li><a href="#tab_2" id='tab_2_btn' data-toggle="tab"><?= $this->lang->line('sales'); ?></a></li>
                           <li><a href="#tab_3" id='tab_3_btn' data-toggle="tab"><?= $this->lang->line('prefixes'); ?></a></li>
                         <?php }?>
                           
                        </ul>
                        <div class="tab-content">
                          <div class="tab-pane active" id="tab_4">
                              <div class="row">
                                 <!-- right column -->
                                 <div class="col-md-12">
                                    <!-- form start -->
                                       <input type="hidden" id="base_url" value="<?php echo $base_url;; ?>">
                                       <div class="box-body">
                                          <div class="row">
                                             <div class="col-md-5">
                                                <div class="form-group">
                                                   <label for="store_code" class="col-sm-4 control-label"><?= $this->lang->line('store_code'); ?><label class="text-danger">*</label></label>
                                                   <div class="col-sm-8">
                                                      <input type="text" class="form-control" id="store_code" name="store_code" readonly=""  placeholder="" onkeyup="shift_cursor(event,'mobile')" value="<?php print $store_code; ?>" >
                                                      <span id="store_code_msg" style="display:none" class="text-danger"></span>
                                                   </div>
                                                </div>
                                                <div class="form-group">
                                                   <label for="store_name" class="col-sm-4 control-label"><?= $this->lang->line('store_name'); ?><label class="text-danger">*</label></label>
                                                   <div class="col-sm-8">
                                                      <input type="text" class="form-control" id="store_name" name="store_name" placeholder="" onkeyup="shift_cursor(event,'mobile')" value="<?php print $store_name; ?>" >
                                                      <span id="store_name_msg" style="display:none" class="text-danger"></span>
                                                   </div>
                                                </div>
                                                <div class="form-group">
                                                   <label for="mobile" class="col-sm-4 control-label"><?= $this->lang->line('mobile'); ?><label class="text-danger">*</label></label>
                                                   <div class="col-sm-8">
                                                      <input type="text" class="form-control no_special_char_no_space" id="mobile" name="mobile" placeholder="" value="<?php print $mobile; ?>" onkeyup="shift_cursor(event,'email')" >
                                                      <span id="mobile_msg" style="display:none" class="text-danger"></span>
                                                   </div>
                                                </div>
                                                <div class="form-group">
                                                   <label for="email" class="col-sm-4 control-label"><?= $this->lang->line('email'); ?><label class="text-danger">*</label></label>
                                                   <div class="col-sm-8">
                                                      <input type="text" class="form-control" id="email" name="email" placeholder="" value="<?php print $email; ?>" onkeyup="shift_cursor(event,'phone')">
                                                      <span id="email_msg" style="display:none" class="text-danger"></span>
                                                   </div>
                                                </div>
                                                <div class="form-group">
                                                   <label for="phone" class="col-sm-4 control-label"><?= $this->lang->line('phone'); ?></label>
                                                   <div class="col-sm-8">
                                                      <input type="text" class="form-control no_special_char_no_space" id="phone" name="phone" placeholder="" value="<?php print $phone; ?>" onkeyup="shift_cursor(event,'gst_no')" >
                                                      <span id="phone_msg" style="display:none" class="text-danger"></span>
                                                   </div>
                                                </div>

                                                <?php if(gst_number()){ ?>
                                                <div class="form-group">
                                                   <label for="gst_no" class="col-sm-4 control-label"><?= $this->lang->line('gst_number'); ?></label>
                                                   <div class="col-sm-8">
                                                      <input type="text" class="form-control" id="gst_no" name="gst_no" placeholder="" value="<?php print $gst_no; ?>" onkeyup="shift_cursor(event,'vat_no')">
                                                      <span id="gstin_msg" style="display:none" class="text-danger"></span>
                                                   </div>
                                                </div>
                                                <?php } ?>

                                                <?php if(vat_number()){ ?>
                                                <div class="form-group">
                                                   <label for="vat_no" class="col-sm-4 control-label"><?= $this->lang->line('vat_number'); ?></label>
                                                   <div class="col-sm-8">
                                                      <input type="text" class="form-control" id="vat_no" name="vat_no" placeholder="" value="<?php print $vat_no; ?>" onkeyup="shift_cursor(event,'store_website')">
                                                      <span id="vat_msg" style="display:none" class="text-danger"></span>
                                                   </div>
                                                </div>
                                                <?php } ?>
                                                <?php if(pan_number()){ ?>
                                                <div class="form-group">
                                                   <label for="pan_no" class="col-sm-4 control-label"><?= $this->lang->line('pan_number'); ?></label>
                                                   <div class="col-sm-8">
                                                      <input type="text" class="form-control" id="pan_no" name="pan_no" placeholder="" value="<?php print $pan_no; ?>" onkeyup="shift_cursor(event,'store_website')">
                                                      <span id="pan_msg" style="display:none" class="text-danger"></span>
                                                   </div>
                                                </div>
                                                <?php } ?>
                                                <div class="form-group">
                                                   <label for="store_website" class="col-sm-4 control-label"><?= $this->lang->line('store_website'); ?></label>
                                                   <div class="col-sm-8">
                                                      <input type="text" class="form-control" id="store_website" name="store_website" placeholder="" value="<?php print $store_website; ?>" onkeyup="shift_cursor(event,'country')">
                                                      <span id="website_msg" style="display:none" class="text-danger"></span>
                                                   </div>
                                                </div>
                                                <!-- ########### -->
                                             </div>
                                             <div class="col-md-5">
                                                <div class="form-group">
                                                   <label for="bank_details" class="col-sm-4 control-label"><?= $this->lang->line('bank_details'); ?></label>
                                                   <div class="col-sm-8">
                                                      <textarea type="text" class="form-control" id="bank_details" name="bank_details" placeholder="" ><?php print $bank_details; ?></textarea>
                                                      <span id="bank_details_msg" style="display:none" class="text-danger"></span>
                                                   </div>
                                                </div>
                                                <div class="form-group">
                                                   <label for="country" class="col-sm-4 control-label"><?= $this->lang->line('country'); ?></label>
                                                   <div class="col-sm-8">
                                                      <select class="form-control select2" id="country" name="country"  style="width: 100%;" onkeyup="shift_cursor(event,'state')" value="<?php print $country; ?>">
                                                         <?php
                                                            $query1="select * from db_country where status=1";
                                                            $q1=$this->db->query($query1);
                                                            if($q1->num_rows($q1)>0)
                                                             {
                                                              //echo '<option value="">-Select-</option>'; 
                                                                 foreach($q1->result() as $res1)
                                                               {
                                                                 $selected = ($res1->country ==$country) ? 'selected' : '';
                                                                 echo "<option $selected value='".$res1->country."'>".$res1->country."</option>";
                                                               }
                                                             }
                                                             else
                                                             {
                                                                ?>
                                                         <option value="">No Records Found</option>
                                                         <?php
                                                            }
                                                            ?>
                                                      </select>
                                                      <span id="country_msg" style="display:none" class="text-danger"></span>
                                                   </div>
                                                </div>
                                                <div class="form-group">
                                                   <label for="state" class="col-sm-4 control-label"><?= $this->lang->line('state'); ?></label>
                                                   <div class="col-sm-8">
                                                      <select class="form-control select2" id="state" name="state"  style="width: 100%;" onkeyup="shift_cursor(event,'city')">
                                                         <?php
                                                            $query2="select * from db_states where status=1";
                                                            $q2=$this->db->query($query2);
                                                            if($q2->num_rows()>0)
                                                             {
                                                              echo '<option value="">-Select-</option>'; 
                                                              foreach($q2->result() as $res1)
                                                               {
                                                                 $selected = ($res1->state ==$state) ? 'selected' : '';
                                                                 echo "<option $selected value='".$res1->state."'>".$res1->state."</option>";
                                                               }
                                                             }
                                                             else
                                                             {
                                                                ?>
                                                         <option value="">No Records Found</option>
                                                         <?php
                                                            }
                                                            ?>
                                                      </select>
                                                      <span id="state_msg" style="display:none" class="text-danger"></span>
                                                   </div>
                                                </div>

                                                <div class="form-group">
                                                   <label for="city" class="col-sm-4 control-label"><?= $this->lang->line('city'); ?><label class="text-danger">*</label></label>
                                                   <div class="col-sm-8">
                                                      <input type="text" class="form-control" id="city" name="city" placeholder="" value="<?php print $city; ?>" onkeyup="shift_cursor(event,'postcode')" >
                                                      <span id="city_msg" style="display:none" class="text-danger"></span>
                                                   </div>
                                                </div>
                                                <div class="form-group">
                                                   <label for="postcode" class="col-sm-4 control-label"><?= $this->lang->line('postcode'); ?></label>
                                                   <div class="col-sm-8">
                                                      <input type="text" class="form-control no_special_char_no_space" id="postcode" name="postcode" placeholder="" value="<?php print $postcode; ?>" onkeyup="shift_cursor(event,'address')">
                                                      <span id="postcode_msg" style="display:none" class="text-danger"></span>
                                                   </div>
                                                </div>
                                                <div class="form-group">
                                                   <label for="address" class="col-sm-4 control-label"><?= $this->lang->line('address'); ?><label class="text-danger">*</label></label>
                                                   <div class="col-sm-8">
                                                      <textarea type="text" class="form-control" id="address" name="address" placeholder="" ><?php print $address; ?></textarea>
                                                      <span id="address_msg" style="display:none" class="text-danger"></span>
                                                   </div>
                                                </div>
                                                <div class="form-group">
                                                   <label for="store_logo" class="col-sm-4 control-label"><?= $this->lang->line('store_logo'); ?></label>
                                                   <div class="col-sm-8">
                                                      <input type="file" id="store_logo" name="store_logo">
                                                      <span id="store_logo_msg" style="display:block;" class="text-danger">Max Width/Height: 1000px * 1000px & Size: 1024kb </span>
                                                   </div>
                                                </div>
                                                <?php 
                                                if(empty($store_logo)){
                                                  $logo = base_url('uploads/no_logo/nologo.png');
                                                }
                                                else{
                                                  $logo = base_url($store_logo);
                                                }
                                                ?>
                                                <div class="form-group">
                                                   <div class="col-sm-8 col-sm-offset-4">
                                                      <img class='img-responsive' style='border:3px solid #d2d6de;' src="<?=$logo;?>">
                                                   </div>
                                                </div>
                                             </div>
                                             <!-- ########### -->
                                          </div>
                                       </div>
                                       <!-- /.box-body -->
                                       <!-- /.box-footer -->
                                    
                                 </div>
                                 <!--/.col (right) -->
                              </div>
                              <!-- /.row -->
                           </div>
                           <div class="tab-pane" id="tab_1">
                              <div class="row">
                                 <!-- right column -->
                                 <div class="col-md-12">
                                    <!-- form start -->
                                       <input type="hidden" id="base_url" value="<?php echo $base_url;; ?>">
                                       <div class="box-body">
                                          <div class="row">
                                             <div class="col-md-5">
                                                
                                                <div class="form-group">
                                                   <label for="timezone" class="col-sm-4 control-label"><?= $this->lang->line('timezone'); ?><label class="text-danger">*</label> </label>
                                                   <div class="col-sm-8">
                                                      <select class="form-control select2" id="timezone" name="timezone"  style="width: 100%;">
                                                         <?php
                                                            $query2="select * from db_timezone where status=1";
                                                            $q2=$this->db->query($query2);
                                                            if($q2->num_rows()>0)
                                                             {
                                                              
                                                              foreach($q2->result() as $res1)
                                                               {
                                                                 if((isset($timezone) && !empty($timezone)) && trim($timezone)==trim($res1->timezone)){$selected='selected';}else{$selected='';}
                                                                 echo "<option ".$selected." value='".$res1->timezone."'>".$res1->timezone."</option>";
                                                               }
                                                             }
                                                             else
                                                             {
                                                                ?>
                                                         <option value="">No Records Found</option>
                                                         <?php
                                                            }
                                                            ?>
                                                      </select>
                                                      <span id="timezone_msg" style="display:none" class="text-danger"></span>
                                                   </div>
                                                </div>
                                                <div class="form-group">
                                                   <label for="date_format" class="col-sm-4 control-label"><?= $this->lang->line('date_format'); ?><label class="text-danger">*</label> </label>
                                                   <div class="col-sm-8">
                                                      <select class="form-control select2" id="date_format" name="date_format"  style="width: 100%;">
                                                         <option value="dd-mm-yyyy">dd-mm-yyyy</option>
                                                         <!-- <option value="dd/mm/yyyy">dd/mm/yyyy</option> -->
                                                         <option value="mm/dd/yyyy">mm/dd/yyyy</option>
                                                      </select>
                                                      <span id="date_format_msg" style="display:none" class="text-danger"></span>
                                                   </div>
                                                </div>
                                                <div class="form-group">
                                                   <label for="time_format" class="col-sm-4 control-label"><?= $this->lang->line('time_format'); ?><label class="text-danger">*</label> </label>
                                                   <div class="col-sm-8">
                                                      <select class="form-control select2" id="time_format" name="time_format"  style="width: 100%;">
                                                         <option value="12">12 Hours</option>
                                                         <option value="24">24 Hours</option>
                                                      </select>
                                                      <span id="time_format_msg" style="display:none" class="text-danger"></span>
                                                   </div>
                                                </div>
                                                <div class="form-group">
                                                   <label for="currency" class="col-sm-4 control-label"><?= $this->lang->line('currency'); ?><label class="text-danger">*</label> </label>
                                                   <div class="col-sm-8">
                                                      <select class="form-control select2" id="currency" name="currency"  style="width: 100%;">
                                                         <?php
                                                            $query2="select * from db_currency where status=1";
                                                            $q2=$this->db->query($query2);
                                                            if($q2->num_rows()>0)
                                                             {
                                                              
                                                              foreach($q2->result() as $res1)
                                                               {
                                                                 if((isset($currency_id) && !empty($currency_id)) && $currency_id==$res1->id){$selected='selected';}else{$selected='';}
                                                                 echo "<option ".$selected." value='".$res1->id."'>".$res1->currency_name.' '.$res1->currency_code.' ('.$res1->currency.")</option>";
                                                               }
                                                             }
                                                             else
                                                             {
                                                                ?>
                                                         <option value="">No Records Found</option>
                                                         <?php
                                                            }
                                                            ?>
                                                      </select>
                                                      <span id="currency_msg" style="display:none" class="text-danger"></span>
                                                   </div>
                                                </div>
                                                <div class="form-group">
                                                   <label for="currency_placement" class="col-sm-4 control-label"><?= $this->lang->line('currency_symbol_placement'); ?><label class="text-danger">*</label> </label>
                                                   <div class="col-sm-8">
                                                      <select class="form-control select2" id="currency_placement" name="currency_placement"  style="width: 100%;">
                                                         <option value="Right">After Amount</option>
                                                         <option value="Left">Before Amount</option>
                                                      </select>
                                                      <span id="currency_placement_msg" style="display:none" class="text-danger"></span>
                                                   </div>
                                                </div>
                                                <div class="form-group">
                                                   <label for="decimals" class="col-sm-4 control-label"><?= $this->lang->line('decimals'); ?><label class="text-danger">*</label> </label>
                                                   <div class="col-sm-8">
                                                      <select class="form-control select2" id="decimals" name="decimals"  style="width: 100%;">
                                                         <option value="0">0</option>
                                                         <option value="1">1</option>
                                                         <option value="2">2</option>
                                                         <option value="3">3</option>
                                                         <option value="4">4</option>
                                                      </select>
                                                      <span id="decimals_msg" style="display:none" class="text-danger"></span>
                                                   </div>
                                                </div>

                                                
                                             </div>
                                             <!-- ########### -->
                                             <div class="col-md-5">
                                                
                                                <div class="form-group">
                                                   <label for="language_id" class="col-sm-4 control-label"><?= $this->lang->line('language'); ?><label class="text-danger">*</label> </label>
                                                   <div class="col-sm-8">
                                                      <select class="form-control select2" id="language_id" name="language_id"  style="width: 100%;">
                                                         <?php
                                                            $query2="select * from db_languages where status=1";
                                                            $q2=$this->db->query($query2);
                                                            if($q2->num_rows()>0)
                                                             {
                                                              
                                                              foreach($q2->result() as $res1)
                                                               {
                                                                 $selected = ($res1->id ==$language_id) ? 'selected' : '';
                                                                 echo "<option ".$selected." value='".$res1->id."'>".$res1->language."</option>";
                                                               }
                                                             }
                                                             else
                                                             {
                                                                ?>
                                                         <option value="">No Records Found</option>
                                                         <?php
                                                            }
                                                            ?>
                                                      </select>
                                                      <span id="language_id_msg" style="display:none" class="text-danger"></span>
                                                   </div>
                                                </div>
                                                
                                                <?php 
                                               $round_off_checkbox ='';
                                               if($round_off==1){
                                                $round_off_checkbox='checked';
                                               }
                                               ?>
                                                <div class="form-group">
                                                   <label for="round_off" class="col-sm-4 control-label"><?= $this->lang->line('enable_round_off'); ?> ?</label>
                                                   <div class="col-sm-4">
                                                      <input type="checkbox" <?=$round_off_checkbox;?> class="form-control" id="round_off" name="round_off" >
                                                      <span id="round_off_msg" style="display:none" class="text-danger"></span>
                                                   </div>
                                                </div>
                                               

                                             </div>
                                          </div>
                                       </div>
                                       <!-- /.box-body -->
                                       <!-- /.box-footer -->
                                    
                                 </div>
                                 <!--/.col (right) -->
                              </div>
                              <!-- /.row -->
                           </div>
                           <!-- /.tab-pane -->
                           <?php 
                           //Change Return
                           $change_return_checkbox ='';
                           if($change_return==1){
                            $change_return_checkbox='checked';
                           }

                          
                            ?>
                           <div class="tab-pane" id="tab_2">
                              <div class="row">
                                 <!-- right column -->
                                 <div class="col-md-12">
                                       <div class="box-body">
                                          <div class="row">
                                             <div class="col-md-8">
                                                <div class="form-group">
                                                   <label for="sales_discount" class="col-sm-4 control-label"><?= $this->lang->line('default_sales_discount'); ?></label>
                                                   <div class="col-sm-4">
                                                      <input type="text" class="form-control" id="sales_discount" name="sales_discount" placeholder="" value="<?php print store_number_format($sales_discount,0); ?>" >
                                                      <span id="sales_discount_msg" style="display:none" class="text-danger"></span>
                                                   </div>
                                                </div>
                                             </div>
                                             <div class="col-md-8">
                                                <div class="form-group">
                                                   <label for="sales_discount" class="col-sm-4 control-label"><?= $this->lang->line('show_paid_amount_and_change_return_in_pos'); ?></label>
                                                   <div class="col-sm-4">
                                                      <input type="checkbox" <?=$change_return_checkbox;?> class="form-control" id="change_return" name="change_return" >
                                                      <span id="change_return_msg" style="display:none" class="text-danger"></span>
                                                   </div>
                                                </div>
                                             </div>
                                            

                                             <div class="col-md-8">
                                             <div class="form-group">
                                                   <label for="sales_invoice_formats" class="col-sm-4 control-label"><?= $this->lang->line('sales_invoice_formats'); ?><label class="text-danger">*</label> </label>
                                                   <div class="col-sm-4">
                                                      <select class="form-control select2" id="sales_invoice_format_id" name="sales_invoice_format_id"  style="width: 100%;">
                                                         <!-- <option value="1">Default</option>
                                                         <option value="2">Format 2</option> -->
                                                         <option value="3">Default</option><!-- Format 3 -->
                                                         <option value="4">GST Format</option><!-- Format 4 -->
                                                      </select>
                                                      <span id="sales_invoice_format_id_msg" style="display:none" class="text-danger"></span>
                                                   </div>
                                                </div>
                                              </div>
                                            <div class="col-md-8">
                                             <div class="form-group">
                                                   <label for="pos_invoice_formats" class="col-sm-4 control-label"><?= $this->lang->line('pos_invoice_formats'); ?><label class="text-danger">*</label> </label>
                                                   <div class="col-sm-4">
                                                      <select class="form-control select2" id="pos_invoice_format_id" name="pos_invoice_format_id"  style="width: 100%;">
                                                         <option value="1">Default</option>
                                                         <!-- <option value="2">Format 2</option> -->
                                                      </select>
                                                      <span id="pos_invoice_format_id_msg" style="display:none" class="text-danger"></span>
                                                   </div>
                                                </div>
                                              </div>

                                              <div class="col-md-8">
                                                <div class="form-group">
                                                   <label for="sales_invoice_footer_text" class="col-sm-4 control-label"><?= $this->lang->line('sales_invoice_footer_text'); ?></label>
                                                   <div class="col-sm-8">
                                                      <textarea class="form-control" id="sales_invoice_footer_text" name="sales_invoice_footer_text"><?= $sales_invoice_footer_text;?></textarea>
                                                      <span id="sales_invoice_footer_text_msg" style="display:none" class="text-danger"></span>
                                                   </div>
                                                </div>
                                             </div>

                                             


                                          </div>
                                       </div>
                                 </div>
                                 <!--/.col (right) -->
                              </div>
                              <!-- /.row -->
                           </div>
                           <!-- /.tab-pane -->
                           <div class="tab-pane" id="tab_3">
                             <div class="row">
                                 <!-- right column -->
                                 <div class="col-md-12">
                                       <div class="box-body">
                                          <div class="row">
                                             <div class="col-md-6">
                                                <div class="form-group">
                                                   <label for="category_init" class="col-sm-4 control-label"><?= $this->lang->line('category'); ?><label class="text-danger">*</label></label>
                                                   <div class="col-sm-8">
                                                      <input type="text" class="form-control" id="category_init" name="category_init" placeholder="" value="<?php print $category_init; ?>" >
                                                      <span id="category_init_msg" style="display:none" class="text-danger"></span>
                                                   </div>
                                                </div>
                                             </div>
                                             <div class="col-md-6">
                                                <div class="form-group">
                                                   <label for="item_init" class="col-sm-4 control-label"><?= $this->lang->line('item'); ?><label class="text-danger">*</label></label>
                                                   <div class="col-sm-8">
                                                      <input type="text" class="form-control no_special_no_space" id="item_init" name="item_init" placeholder="" value="<?php print $item_init; ?>" >
                                                      <span id="item_init_msg" style="display:none" class="text-danger"></span>
                                                   </div>
                                                </div>
                                             </div>
                                             <div class="col-md-6">
                                                <div class="form-group">
                                                   <label for="supplier_init" class="col-sm-4 control-label"><?= $this->lang->line('supplier'); ?><label class="text-danger">*</label></label>
                                                   <div class="col-sm-8">
                                                      <input type="text" class="form-control" id="supplier_init" name="supplier_init" placeholder="" value="<?php print $supplier_init; ?>" >
                                                      <span id="supplier_init_msg" style="display:none" class="text-danger"></span>
                                                   </div>
                                                </div>
                                             </div>
                                             <div class="col-md-6">
                                                <div class="form-group">
                                                   <label for="purchase_init" class="col-sm-4 control-label"><?= $this->lang->line('purchase'); ?><label class="text-danger">*</label></label>
                                                   <div class="col-sm-8">
                                                      <input type="text" class="form-control" id="purchase_init" name="purchase_init" placeholder="" value="<?php print $purchase_init; ?>" >
                                                      <span id="purchase_init_msg" style="display:none" class="text-danger"></span>
                                                   </div>
                                                </div>
                                             </div>
                                             <div class="col-md-6">
                                                <div class="form-group">
                                                   <label for="purchase_return_init" class="col-sm-4 control-label"><?= $this->lang->line('purchase_return'); ?><label class="text-danger">*</label></label>
                                                   <div class="col-sm-8">
                                                      <input type="text" class="form-control" id="purchase_return_init" name="purchase_return_init" placeholder="" value="<?php print $purchase_return_init; ?>" >
                                                      <span id="purchase_return_init_msg" style="display:none" class="text-danger"></span>
                                                   </div>
                                                </div>
                                             </div>
                                             <div class="col-md-6">
                                                <div class="form-group">
                                                   <label for="customer_init" class="col-sm-4 control-label"><?= $this->lang->line('customer'); ?><label class="text-danger">*</label></label>
                                                   <div class="col-sm-8">
                                                      <input type="text" class="form-control" id="customer_init" name="customer_init" placeholder="" value="<?php print $customer_init; ?>" >
                                                      <span id="customer_init_msg" style="display:none" class="text-danger"></span>
                                                   </div>
                                                </div>
                                             </div>
                                             <div class="col-md-6">
                                                <div class="form-group">
                                                   <label for="sales_init" class="col-sm-4 control-label"><?= $this->lang->line('sales'); ?><label class="text-danger">*</label></label>
                                                   <div class="col-sm-8">
                                                      <input type="text" class="form-control" id="sales_init" name="sales_init" placeholder="" value="<?php print $sales_init; ?>" >
                                                      <span id="sales_init_msg" style="display:none" class="text-danger"></span>
                                                   </div>
                                                </div>
                                             </div>
                                             <div class="col-md-6">
                                                <div class="form-group">
                                                   <label for="sales_return_init" class="col-sm-4 control-label"><?= $this->lang->line('sales_return'); ?><label class="text-danger">*</label></label>
                                                   <div class="col-sm-8">
                                                      <input type="text" class="form-control" id="sales_return_init" name="sales_return_init" placeholder="" value="<?php print $sales_return_init; ?>" >
                                                      <span id="sales_return_init_msg" style="display:none" class="text-danger"></span>
                                                   </div>
                                                </div>
                                             </div>
                                             <div class="col-md-6">
                                                <div class="form-group">
                                                   <label for="expense_init" class="col-sm-4 control-label"><?= $this->lang->line('expense'); ?><label class="text-danger">*</label></label>
                                                   <div class="col-sm-8">
                                                      <input type="text" class="form-control" id="expense_init" name="expense_init" placeholder="" value="<?php print $expense_init; ?>" >
                                                      <span id="expense_init_msg" style="display:none" class="text-danger"></span>
                                                   </div>
                                                </div>
                                             </div>
                                             <div class="col-md-6">
                                                <div class="form-group">
                                                   <label for="accounts_init" class="col-sm-4 control-label"><?= $this->lang->line('accounts'); ?><label class="text-danger">*</label></label>
                                                   <div class="col-sm-8">
                                                      <input type="text" class="form-control" id="accounts_init" name="accounts_init" placeholder="" value="<?php print $accounts_init; ?>" >
                                                      <span id="accounts_init_msg" style="display:none" class="text-danger"></span>
                                                   </div>
                                                </div>
                                             </div>
                                             <div class="col-md-6">
                                                <div class="form-group">
                                                   <label for="quotation_init" class="col-sm-4 control-label"><?= $this->lang->line('quotation'); ?><label class="text-danger">*</label></label>
                                                   <div class="col-sm-8">
                                                      <input type="text" class="form-control" id="quotation_init" name="quotation_init" placeholder="" value="<?php print $quotation_init; ?>" >
                                                      <span id="quotation_init_msg" style="display:none" class="text-danger"></span>
                                                   </div>
                                                </div>
                                             </div>
                                             <div class="col-md-6">
                                                <div class="form-group">
                                                   <label for="money_transfer_init" class="col-sm-4 control-label"><?= $this->lang->line('money_transfer'); ?><label class="text-danger">*</label></label>
                                                   <div class="col-sm-8">
                                                      <input type="text" class="form-control" id="money_transfer_init" name="money_transfer_init" placeholder="" value="<?php print $money_transfer_init; ?>" >
                                                      <span id="money_transfer_init_msg" style="display:none" class="text-danger"></span>
                                                   </div>
                                                </div>
                                             </div>
                                             <div class="col-md-6">
                                                <div class="form-group">
                                                   <label for="sales_payment_init" class="col-sm-4 control-label"><?= $this->lang->line('sales_payment'); ?><label class="text-danger">*</label></label>
                                                   <div class="col-sm-8">
                                                      <input type="text" class="form-control" id="sales_payment_init" name="sales_payment_init" placeholder="" value="<?php print $sales_payment_init; ?>" >
                                                      <span id="sales_payment_init_msg" style="display:none" class="text-danger"></span>
                                                   </div>
                                                </div>
                                             </div>
                                             <div class="col-md-6">
                                                <div class="form-group">
                                                   <label for="sales_return_payment_init" class="col-sm-4 control-label"><?= $this->lang->line('sales_return_payment'); ?><label class="text-danger">*</label></label>
                                                   <div class="col-sm-8">
                                                      <input type="text" class="form-control" id="sales_return_payment_init" name="sales_return_payment_init" placeholder="" value="<?php print $sales_return_payment_init; ?>" >
                                                      <span id="sales_return_payment_init_msg" style="display:none" class="text-danger"></span>
                                                   </div>
                                                </div>
                                             </div>
                                             <div class="col-md-6">
                                                <div class="form-group">
                                                   <label for="purchase_payment_init" class="col-sm-4 control-label"><?= $this->lang->line('purchase_payment'); ?><label class="text-danger">*</label></label>
                                                   <div class="col-sm-8">
                                                      <input type="text" class="form-control" id="purchase_payment_init" name="purchase_payment_init" placeholder="" value="<?php print $purchase_payment_init; ?>" >
                                                      <span id="purchase_payment_init_msg" style="display:none" class="text-danger"></span>
                                                   </div>
                                                </div>
                                             </div>
                                             
                                             <div class="col-md-6">
                                                <div class="form-group">
                                                   <label for="purchase_return_payment_init" class="col-sm-4 control-label"><?= $this->lang->line('purchase_return_payment'); ?><label class="text-danger">*</label></label>
                                                   <div class="col-sm-8">
                                                      <input type="text" class="form-control" id="purchase_return_payment_init" name="purchase_return_payment_init" placeholder="" value="<?php print $purchase_return_payment_init; ?>" >
                                                      <span id="purchase_return_payment_init_msg" style="display:none" class="text-danger"></span>
                                                   </div>
                                                </div>
                                             </div>

                                             <div class="col-md-6">
                                                <div class="form-group">
                                                   <label for="expense_payment_init" class="col-sm-4 control-label"><?= $this->lang->line('expense_payment'); ?><label class="text-danger">*</label></label>
                                                   <div class="col-sm-8">
                                                      <input type="text" class="form-control" id="expense_payment_init" name="expense_payment_init" placeholder="" value="<?php print $expense_payment_init; ?>" >
                                                      <span id="expense_payment_init_msg" style="display:none" class="text-danger"></span>
                                                   </div>
                                                </div>
                                             </div>
                                             <div class="col-md-6">
                                                <div class="form-group">
                                                   <label for="cust_advance_init" class="col-sm-4 control-label"><?= $this->lang->line('customers_advance_payments'); ?><label class="text-danger">*</label></label>
                                                   <div class="col-sm-8">
                                                      <input type="text" class="form-control" id="cust_advance_init" name="cust_advance_init" placeholder="" value="<?php print $cust_advance_init; ?>" >
                                                      <span id="cust_advance_init_msg" style="display:none" class="text-danger"></span>
                                                   </div>
                                                </div>
                                             </div>
                                          </div>
                                       </div>
                                 </div>
                                 <!--/.col (right) -->
                              </div>
                              <!-- /.row -->
                           </div>
                           <!-- /.tab-pane -->
                        </div>
                        <!-- /.tab-content -->
                     </div>
                     <!-- nav-tabs-custom -->
                     <div>
                        <div class="col-sm-8 col-sm-offset-2 text-center">
                           <center>
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
                           </center>
                        </div>
                     </div>
                  </div>
                  <!-- /.col -->
               </div>
               <!-- /.row -->
            </section>
            <!-- /.content -->
            <?= form_close(); ?>
         </div>
         <!-- /.content-wrapper -->
         <?php $this->load->view('footer.php');?>
         <!-- Add the sidebar's background. This div must be placed
            immediately after the control sidebar -->
         <div class="control-sidebar-bg"></div>
      </div>
      <!-- ./wrapper -->
      <?php $this->load->view('comman/code_js_language.php');?>
      <!-- SOUND CODE -->
      <?php $this->load->view('comman/code_js_sound.php');?>
      <!-- TABLES CODE -->
      <?php $this->load->view('comman/code_js.php');?>

      <script type="text/javascript">
         $(document).submit(function(event) {
           event.preventDefault();
           if($("#update").length){
             $("#update").trigger('click');
           }
         });
      </script>
      <script src="<?php echo $theme_link; ?>js/store_profile.js"></script>
      <script src="<?php echo $theme_link; ?>js/store/store.js"></script>
     
      <script type="text/javascript">
        <?php if(!empty($currency_placement)) {?>
         $("#currency_placement").val('<?= $currency_placement;?>').select2();
       <?php }else{ ?>
         $("#currency_placement").select2();
       <?php } ?>

       <?php if(!empty($date_format)) {?>
         $("#date_format").val('<?= $date_format;?>').select2();
       <?php }else{ ?>
         $("#date_format").select2();
       <?php } ?>

       <?php if(!empty($decimals)) {?>
         $("#decimals").val('<?= $decimals;?>').select2();
       <?php }else{ ?>
         $("#decimals").select2();
       <?php } ?>
       
       <?php if(!empty($time_format)) {?>
         $("#time_format").val('<?= $time_format;?>').select2();
       <?php }else{ ?>
         $("#time_format").select2();
       <?php } ?>

       <?php if(!empty($sales_invoice_format_id)) {?>
         $("#sales_invoice_format_id").val('<?= $sales_invoice_format_id;?>').select2();
       <?php }else{ ?>
         $("#sales_invoice_format_id").select2();
       <?php } ?>

       <?php if(!empty($pos_invoice_format_id)) {?>
         $("#pos_invoice_format_id").val('<?= $pos_invoice_format_id;?>').select2();
       <?php }else{ ?>
         $("#pos_invoice_format_id").select2();
       <?php } ?>
      </script>
      <!-- Make sidebar menu hughlighter/selector -->
      <?php if( isset($q_id) && !empty($q_id) && get_current_store_id()==$q_id){ ?>
        <script>$(".store_profile-active-li").addClass("active");</script>
      <?php }else{?>
        <script>$(".<?php echo basename(__FILE__,'.php');?>-active-li").addClass("active");</script>
      <?php } ?>
   </body>
</html>
