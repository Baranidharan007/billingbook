<!DOCTYPE html>
<html>
   <head>
      <!-- TABLES CSS CODE -->
      <?php include"comman/code_css.php"; ?>
      <style type="text/css">
     
      </style>
      <!-- </copy> -->  
   </head>
   <body class="hold-transition skin-blue sidebar-mini">
      <div class="wrapper">
         <?php include"sidebar.php"; ?>
         <?php
            if(!isset($customer_name)){
               $customer_name=$mobile=$phone=$email=
               $country_id=$state_id=$city=$postcode=$address=$shipping_location_link=
               $shipping_country=$shipping_state=$shipping_city=$shipping_postcode=$shipping_address=
               $supplier_code=$gstin=$tax_number=$location_link=$attachment_1=
               $state_code=$customer_code=$store_name=$company_mobile=$store_id='';
               $price_level_type ='Increase';
               $price_level ='0';
               $opening_balance=0;
               $credit_limit='-1';
            }
            ?>
         <!-- Content Wrapper. Contains page content -->
         <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
               <h1>
                  <?= $page_title; ?>
                  <small>Enter User Information</small>
               </h1>
               <ol class="breadcrumb">
                  <li><a href="<?php echo $base_url; ?>dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
                  <li><a href="<?php echo $base_url; ?>users/view"><?= $this->lang->line('view_users'); ?></a></li>
                  <li class="active"><?= $page_title; ?></li>
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
                     <div class="">
                                          <form class="form-horizontal" id="customers-form" method="post">
                     <div class="nav-tabs-custom">
                        <ul class="nav nav-tabs">
                           <li class="active"><a href="#tab_1" data-toggle="tab"><i class="fa  fa-pencil-square text-red"></i> <?= $this->lang->line('add/edit'); ?></a></li>
                           <li><a href="#tab_2" data-toggle="tab"><i class="fa fa-gears text-red"></i> <?= $this->lang->line('advanced'); ?></a></li>
                           
                        </ul>
                        <div class="tab-content">
                           <div class="tab-pane active" id="tab_1">
                            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
                           <input type="hidden" id="base_url" value="<?php echo $base_url;; ?>">
                           <div class="box-body">
                              <div class="row">
                                 <div class="col-md-5">
                                    <!-- Store Code -->
                                    <?php 
                                    /*if(store_module() && is_admin()) {$this->load->view('store/store_code',array('show_store_select_box'=>true,'store_id'=>$store_id,'label_length'=>'col-sm-4','div_length'=>'col-sm-8')); }else{*/
                                echo "<input type='hidden' name='store_id' id='store_id' value='".get_current_store_id()."'>";
                              /*}*/
                              ?>
                                    <!-- Store Code end -->
                                 </div>
                              </div>
                              <div class="row">
                                 

                                 <div class="col-md-10">
                                    <div class="form-group">
                                       <label for="customer_name" class="col-sm-2 control-label"><?= $this->lang->line('customer_name'); ?><label class="text-danger">*</label></label>
                                       <div class="col-sm-4">
                                          <input type="text" class="form-control" id="customer_name" name="customer_name" placeholder=""  value="<?php print $customer_name; ?>" >
                                          <span id="customer_name_msg" style="display:none" class="text-danger"></span>
                                       </div>
                                    <!-- </div>
                                    <div class="form-group"> -->
                                       <label for="mobile" class="col-sm-2 control-label"><?= $this->lang->line('mobile'); ?></label>
                                       <div class="col-sm-4">
                                          <input type="text" class="form-control no_special_char_no_space" id="mobile" name="mobile" placeholder="+1234567890" value="<?php print $mobile; ?>"  >
                                          <span id="mobile_msg" style="display:none" class="text-danger"></span>
                                       </div>
                                    </div>
                                    <div class="form-group">
                                       <label for="email" class="col-sm-2 control-label"><?= $this->lang->line('email'); ?></label>
                                       <div class="col-sm-4">
                                          <input type="text" class="form-control" id="email" name="email" placeholder="" value="<?php print $email; ?>" >
                                          <span id="email_msg" style="display:none" class="text-danger"></span>
                                       </div>
                                    <!-- </div>
                                    <div class="form-group"> -->
                                       <label for="phone" class="col-sm-2 control-label"><?= $this->lang->line('phone'); ?></label>
                                       <div class="col-sm-4">
                                          <input type="text" class="form-control no_special_char_no_space" id="phone" name="phone" placeholder="" value="<?php print $phone; ?>"  >
                                          <span id="phone_msg" style="display:none" class="text-danger"></span>
                                       </div>
                                    </div>
                                   
                                    <div class="form-group">
                                       <label for="gstin" class="col-sm-2 control-label"><?= $this->lang->line('gst_number'); ?></label>
                                       <div class="col-sm-4">
                                          <input type="text" class="form-control" id="gstin" name="gstin" placeholder="" value="<?php print $gstin; ?>" >
                                          <span id="gstin_msg" style="display:none" class="text-danger"></span>
                                       </div>
                                    <!-- </div>
                                  
                                    <div class="form-group"> -->
                                       <label for="tax_number" class="col-sm-2 control-label"><?= $this->lang->line('tax_number'); ?></label>
                                       <div class="col-sm-4">
                                          <input type="text" class="form-control" id="tax_number" name="tax_number" placeholder="" value="<?php print $tax_number; ?>" >
                                          <span id="tax_number_msg" style="display:none" class="text-danger"></span>
                                       </div>
                                    </div>
                                    
                                    <!-- ########### -->
                                 <!-- </div>
                                 <div class="col-md-5"> -->
                                    <div class="form-group">
                                       <label for="credit_limit" class="col-sm-2 control-label"><?= $this->lang->line('credit_limit'); ?></label>
                                       <div class="col-sm-4">
                                          <input type="text" class="form-control only_currency" id="credit_limit" name="credit_limit" placeholder="" value="<?php print store_number_format($credit_limit,0); ?>" >
                                          <span id="" style="" class="text-success">-1 for No Limit</span>
                                          <span id="credit_limit_msg" style="display:none" class="text-danger"></span>
                                       </div>
                                    <!-- </div>
                                    <div class="form-group"> -->
                                       <label for="opening_balance" class="col-sm-2 control-label"><?= $this->lang->line('previous_due'); ?></label>
                                       <div class="col-sm-4">
                                          <input type="text" class="form-control only_currency" id="opening_balance" name="opening_balance" placeholder="" value="<?php print store_number_format($opening_balance,0); ?>" >
                                          <span id="opening_balance_msg" style="display:none" class="text-danger"></span>
                                       </div>
                                    </div>
                                    <div class="form-group">
                                       <label for="attachment_1" class="col-sm-2 control-label"><?= $this->lang->line('attachment_1'); ?></label>
                                       <div class="col-sm-4">
                                          <input type="file" name="attachment_1" id="attachment_1">
                                            <span id="attachment_1_msg" style="display:block;" class="text-danger">Size: 2MB </span>
                                            <span onclick="show_attachment('<?= (empty($attachment_1)) ? "" : base_url($attachment_1) ?>')" class="label label-success" style="cursor:pointer">Click to view</span>
                                       </div>
                                    </div>
                                  

                                    <div class="form-group">
                                       <h3 class="box-title text-uppercase text-success " id="">
                                          <i class="fa fa-fw fa-map-marker"></i>
                                          <ins><?= $this->lang->line('address_details'); ?></ins>
                                          </h3>
                                    </div>
                                    <div class="form-group">
                                       <label for="country" class="col-sm-2 control-label"><?= $this->lang->line('country'); ?></label>
                                       <div class="col-sm-4">
                                          <select class="form-control select2" id="country" name="country"  style="width: 100%;"  >
                                             <?= get_country_select_list($country_id,true); ?>
                                          </select>
                                          <span id="country_msg" style="display:none" class="text-danger"></span>
                                       </div>
                                    <!-- </div>
                                    <div class="form-group"> -->
                                       <label for="state" class="col-sm-2 control-label"><?= $this->lang->line('state'); ?></label>
                                       <div class="col-sm-4">
                                          <select class="form-control select2" id="state" name="state"  style="width: 100%;" >
                                          <?= get_state_select_list($state_id); ?>
                                         </select>
                                          <span id="state_msg" style="display:none" class="text-danger"></span>
                                       </div>
                                    </div>
                                    <div class="form-group">
                                       <label for="city" class="col-sm-2 control-label"><?= $this->lang->line('city'); ?></label>
                                       <div class="col-sm-4">
                                          <input type="text" class="form-control" id="city" name="city" placeholder="" value="<?php print $city; ?>" >
                                          <span id="city_msg" style="display:none" class="text-danger"></span>
                                       </div>
                                    <!-- </div>
                                    <div class="form-group"> -->
                                       <label for="postcode" class="col-sm-2 control-label"><?= $this->lang->line('postcode'); ?></label>
                                       <div class="col-sm-4">
                                          <input type="text" class="form-control no_special_char_no_space" id="postcode" name="postcode" placeholder="" value="<?php print $postcode; ?>" >
                                          <span id="postcode_msg" style="display:none" class="text-danger"></span>
                                       </div>
                                    </div>
                                    <div class="form-group">
                                       <label for="address" class="col-sm-2 control-label"><?= $this->lang->line('address'); ?></label>
                                       <div class="col-sm-4">
                                          <textarea type="text" class="form-control" id="address" name="address" placeholder="" ><?php print $address; ?></textarea>
                                          <span id="address_msg" style="display:none" class="text-danger"></span>
                                       </div>
                                       <label for="location_link" class="col-sm-2 control-label"><?= $this->lang->line('location_link'); ?></label>
                                       <div class="col-sm-4">
                                          <input type="text" class="form-control" id="location_link" name="location_link" placeholder="" value="<?php print $location_link; ?>" >
                                          <span id="location_link_msg" style="display:none" class="text-danger"></span>
                                       </div>
                                    </div>


                                    <div class="form-group">
                                       <h3 class="box-title text-uppercase text-success " id="">
                                          <i class="fa fa-fw fa-truck"></i>
                                          <ins><?= $this->lang->line('shipping_address'); ?></ins>
                                          </h3>
                                    </div>
                                   
                                    <div class="form-group">
                                       <label for="copy_address" class="col-sm-2 control-label"><?= $this->lang->line('copy_address'); ?> ?</label>
                                       <div class="col-sm-4">
                                          <input type="checkbox" class="form-control" id="copy_address" name="copy_address" >
                                          <span id="copy_address_msg" style="display:none" class="text-danger"></span>
                                       </div>
                                    </div>

                                    <div class="form-group">
                                       <label for="shipping_country" class="col-sm-2 control-label"><?= $this->lang->line('country'); ?></label>
                                       <div class="col-sm-4">
                                          <select class="form-control select2" id="shipping_country" name="shipping_country"  style="width: 100%;"  >
                                             <?= get_country_select_list($shipping_country,true); ?>
                                          </select>
                                          <span id="shipping_country_msg" style="display:none" class="text-danger"></span>
                                       </div>
                                    <!-- </div>
                                    <div class="form-group"> -->
                                       <label for="shipping_state" class="col-sm-2 control-label"><?= $this->lang->line('state'); ?></label>
                                       <div class="col-sm-4">
                                          <select class="form-control select2" id="shipping_state" name="shipping_state"  style="width: 100%;" >
                                          <?= get_state_select_list($shipping_state); ?>
                                         </select>
                                          <span id="shipping_state_msg" style="display:none" class="text-danger"></span>
                                       </div>
                                    </div>
                                    <div class="form-group">
                                       <label for="shipping_city" class="col-sm-2 control-label"><?= $this->lang->line('city'); ?></label>
                                       <div class="col-sm-4">
                                          <input type="text" class="form-control" id="shipping_city" name="shipping_city" placeholder="" value="<?php print $shipping_city; ?>" >
                                          <span id="shipping_city_msg" style="display:none" class="text-danger"></span>
                                       </div>
                                    <!-- </div>
                                    <div class="form-group"> -->
                                       <label for="shipping_postcode" class="col-sm-2 control-label"><?= $this->lang->line('postcode'); ?></label>
                                       <div class="col-sm-4">
                                          <input type="text" class="form-control no_special_char_no_space" id="shipping_postcode" name="shipping_postcode" placeholder="" value="<?php print $shipping_postcode; ?>" >
                                          <span id="shipping_postcode_msg" style="display:none" class="text-danger"></span>
                                       </div>
                                    </div>
                                    <div class="form-group">
                                       <label for="shipping_address" class="col-sm-2 control-label"><?= $this->lang->line('address'); ?></label>
                                       <div class="col-sm-4">
                                          <textarea type="text" class="form-control" id="shipping_address" name="shipping_address" placeholder="" ><?php print $shipping_address; ?></textarea>
                                          <span id="shipping_address_msg" style="display:none" class="text-danger"></span>
                                       </div>
                                       <label for="shipping_location_link" class="col-sm-2 control-label"><?= $this->lang->line('location_link'); ?></label>
                                       <div class="col-sm-4">
                                          <input type="text" class="form-control" id="shipping_location_link" name="shipping_location_link" placeholder="" value="<?php print $shipping_location_link; ?>" >
                                          <span id="shipping_location_link_msg" style="display:none" class="text-danger"></span>
                                       </div>
                                    </div>

                                 </div>
                                 <!-- ########### -->
                              </div>
                           </div>
                           </div>
                           <!-- /.tab-pane -->
                           <div class="tab-pane" id="tab_2">
                           <div class="box-body">
                              
                              <div class="row">
                                
                                 <div class="col-md-5">
                                    <div class="form-group">
                                       <label for="price_level_type" class="col-sm-4 control-label"><?= $this->lang->line('price_level_type'); ?></label>
                                       <div class="col-sm-8">
                                          <select class="form-control select2" id="price_level_type" name="price_level_type"  style="width: 100%;"  >
                                             <option value="Increase">Increase</option>
                                             <option value="Decrease">Decrease</option>
                                          </select>
                                          <span id="price_level_type_msg" style="display:none" class="text-danger"></span>
                                       </div>
                                    </div>
                                    <div class="form-group">
                                       <label for="price_level" class="col-sm-4 control-label"><?= $this->lang->line('price_level'); ?></label>
                                       <div class="col-sm-8">
                                        <div class="input-group">
                                           <input type="text" class="form-control" id="price_level" name="price_level" placeholder="" value="<?php print $price_level; ?>" >
                                           <span class="input-group-addon "><i class="fa fa-percent text-primary fa-lg"></i></span>
                                        </div>
                                        <span id="price_level_msg" style="display:none" class="text-danger"></span>
                                     </div>
                                    </div>

                                 </div>
                                 <!-- ########### -->
                              </div>
                           </div>
                           </div>
                           <!-- /.tab-pane -->
                        </div>
                        <!-- /.tab-content -->

                     </div>
                    <div class="col-sm-8 col-sm-offset-2 text-center">
                           <center>
                            <?php
                                    if($customer_name!=""){
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
                     <!-- /.box -->
                   </form>
                     </div>
                     <!-- /.box -->
                  </div>
                  <!--/.col (right) -->
                </div>

                <div class='row' style='padding-top:1%;'>
                  <div class="col-md-12">
                     <div class="box box-primary">
                        <div class="box-header">
                           <h3 class="box-title text-blue"><?= $this->lang->line('opening_balance_payments'); ?></h3>
                        </div>
                        <div class="box-body table-responsive no-padding">
                           <table class="table table-bordered table-hover custom_hover" id="report-data" >
                              <thead>
                                 <tr class="bg-gray">
                                    <th style="">#</th>
                                    <th style=""><?= $this->lang->line('payment_date'); ?></th>
                                    <th style=""><?= $this->lang->line('payment'); ?></th>
                                    <th style=""><?= $this->lang->line('payment_type'); ?></th>
                                    <th style=""><?= $this->lang->line('payment_note'); ?></th>
                                    <th style=""><?= $this->lang->line('action'); ?></th>
                                 </tr>
                              </thead>
                              <tbody>
                                 <?php 
                                    if(isset($q_id)){
                                      $q3 = $this->db->query("select * from db_salespayments where customer_id=$q_id and short_code='OPENING BALANCE PAID'");
                                      if($q3->num_rows()>0){
                                        $i=1;
                                        $total_paid = 0;
                                        foreach ($q3->result() as $res3) {
                                          $total_paid +=$res3->payment;
                                          echo "<td>".$i."</td>";
                                          echo "<td>".show_date($res3->payment_date)."</td>";
                                          echo "<td class='text-right'>".$CI->currency($res3->payment)."</td>";
                                          echo "<td>".$res3->payment_type."</td>";
                                          echo "<td>".$res3->payment_note."</td>";
                                          echo '<td><i class="fa fa-trash text-red pointer" onclick="delete_opening_balance_entry('.$res3->id.')"> Delete</i></td>';
                                          echo "</tr>";
                                          $i++;
                                        }
                                        echo "<tr class='text-bold'>
                                                <td colspan=2 class='text-right '>Total</td>
                                                <td class='text-right'>".$CI->currency($total_paid)."</td>
                                                <td colspan=3></td>
                                              </tr>";
                                      }
                                      else{
                                        echo "<tr><td colspan='6' class='text-center text-bold'>No Previous Stock Entry Found!!</td></tr>";
                                      }
                                    }
                                    else{
                                      echo "<tr><td colspan='6' class='text-center text-bold'>No Previous Stock Entry Found!!</td></tr>";
                                    }
                                    ?>
                              </tbody>
                           </table>
                        </div>
                     </div>
                  </div>
               </div> 
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
      <script src="<?php echo $theme_link; ?>js/customers.js"></script>
      <!-- Make sidebar menu hughlighter/selector -->
      <script>$(".<?php echo basename(__FILE__,'.php');?>-active-li").addClass("active");</script>
      <script type="text/javascript">
        <?php if(isset($q_id)){ ?>
          $("#store_id").attr('readonly',true);
        <?php }?>
        $("#price_level_type").val('<?= $price_level_type;?>').select2();
      </script>
      <script type="text/javascript">
        function show_attachment(imagepath=''){
          if(imagepath==''){
              toastr["warning"]("No Attachment Availble!");
              failed.currentTime = 0; 
              failed.play();
              return false;
          }
          else{
            window.open(imagepath, "_blank");
          }
        }
        $("#copy_address").on("ifChanged",function(event){
          if(event.target.checked){
           $("#shipping_country").val($("#country").val()).select2();
           $("#shipping_state").val($("#state").val()).select2();
           $("#shipping_postcode").val($("#postcode").val());
           $("#shipping_city").val($("#city").val());
           $("#shipping_address").val($("#address").val());
           $("#shipping_location_link").val($("#location_link").val());
          }
          else{
           $("#shipping_country").val('').select2();
           $("#shipping_state").val('').select2();
           $("#shipping_postcode").val('');
           $("#shipping_city").val('');
           $("#shipping_address").val('');
           $("#shipping_location_link").val('');
          }
        });
      </script>
   </body>
</html>
