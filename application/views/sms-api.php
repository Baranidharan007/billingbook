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
         <?php 
            $store_rec = get_store_details();
            ?>
         <!-- Content Wrapper. Contains page content -->
         <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
               <h1>
                  <?= $this->lang->line('sms_api'); ?>
                  <small>Add/Update SMS API</small>
               </h1>
               <ol class="breadcrumb">
                  <li><a href="<?php echo $base_url; ?>dashboard"><i class="fa fa-dashboard"></i>Home</a></li>
                  <li class="active"> <?= $this->lang->line('sms_api'); ?></li>
               </ol>
            </section>
            <!-- Main content -->
            <section class="content">
               <div class="row">
                  <!-- right column -->
                  <!-- ********** ALERT MESSAGE START******* -->
                  <?php include"comman/code_flashdata.php"; ?>
                  <!-- ********** ALERT MESSAGE END******* -->
                  <div class="col-md-12">
                     <!-- Horizontal Form -->
                     <form class="form-horizontal" id="api-form" method="post">
                     <div class="nav-tabs-custom">
                        <ul class="nav nav-tabs">
                           <li class="active"><a href="#tab_1" data-toggle="tab">HTTP/URL SMS API</a></li>
                           <li><a href="#tab_2" data-toggle="tab">Twilio SMS API</a></li>
                           <li><a href="#tab_3" data-toggle="tab">Action</a></li>
                          
                        </ul>
                        <div class="tab-content">
                           <div class="tab-pane active" id="tab_1">
                              
                           <input type="hidden" name="hidden_rowcount" id="hidden_rowcount" value="">
                           <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
                           <input type="hidden" id="base_url" value="<?php echo $base_url;; ?>">
                           <!-- <div class="box-body"> -->
                              <div class="form-group " >

                                 <div class="col-sm-12">
                                  <div class="callout callout-info">
                                    <h4>HTTP/URL</h4>
                                      <p>
                                        <u>Example</u> : <br>https://www.example.com/api/mt/SendSMS?APIKey=QWERTYUIOP123456&senderid=ABCDEF&channel=2&DCS=0&flashsms=0&<b class='bg-yellow'>mobiles</b>=91989xxxxxxx&<b class='bg-yellow'>message</b>=test message&route=1
                                        <br>
                                        Note: You need to verify the message key & mobile number key from your API, each SMS service providers may have different keys.<br>
                                        In above example API 'message' & 'mobiles' keys refers Message & Mobile number, where you need to change the value of the Message & Number.
                                        <br>
                                        <u>Example:</u>
                                        <ol>
                                          <li>URL : https://www.example.com/api/mt/SendSMS?</li>
                                          <li>Mobile Key : mobiles</li>
                                          <li>Message Key : message</li>
                                          <li>APIKey : QWERTYUIOP123456</li>
                                          <li>senderid : ABCDEF</li>
                                          <li>channel : 2</li>
                                          <li>DCS : 0</li>
                                          <li>flashsms : 0</li>
                                          <li>route : 1</li>
                                        </ol>
                                       

                                      </p>
                                  </div>
                                 </div>
                                 <div class="col-sm-12  table-responsive">
                                  
                                    <table class="table" id='api_table'>
                                       <thead>
                                          <th width="15%"></th>
                                          <th width="20%" class="text-center">Key</th>
                                          <th width="40%" class="text-center">Key Value</th>
                                          <th><input type="button" class="btn btn-success" name="new_row" onclick="addrow();" value="+" title="New Line"  ></th>
                                       </thead>
                                       <tbody>
                                          <?php 
                                             $q2=$this->db->select("*")->where("store_id",get_current_store_id())->get("db_smsapi");
                                             $i=0; 
                                             foreach($q2->result() as $res2){
                                               $i++;
                                               if($res2->info == 'url'){
                                             ?>
                                          <tr id="row_<?= $i; ?>" data-row='<?= $i; ?>'>
                                             <td  class="text-right"><label class="control-label">URL<label class="text-danger">*</label></label>
                                                <input type="hidden" id="info_<?= $i; ?>" name="info_<?= $i; ?>" value="<?php echo  $res2->info; ?>">
                                             </td>
                                             <td >
                                                <input id="key_<?= $i; ?>" name="key_<?= $i; ?>"  type="text" class="form-control " placeholder="" value='<?= $res2->key; ?>' readonly="true" />
                                             </td>
                                             <td><input id="key_val_<?= $i; ?>" name="key_val_<?= $i; ?>"  type="text" class="form-control " placeholder=""  value='<?= $res2->key_value; ?>' /></td>
                                             <td><input type="button" class="btn btn-danger" name="btn_<?= $i; ?>" id="btn_<?= $i; ?>" value="-" title="Cant' Remove" disabled='true' ></td">
                                          </tr>
                                          <?php 
                                             }//url end
                                             else if($res2->info == 'mobile'){
                                               ?>
                                          <tr id="row_<?= $i; ?>" data-row='<?= $i; ?>'>
                                             <td  class="text-right"><label class="control-label">Mobile Key<label class="text-danger">*</label></label>
                                                <input type="hidden" id="info_<?= $i; ?>" name="info_<?= $i; ?>" value="<?php echo  $res2->info; ?>">
                                             </td>
                                             <td >
                                                <input id="key_<?= $i; ?>" name="key_<?= $i; ?>"  type="text" class="form-control " placeholder="" value='<?= $res2->key; ?>' />
                                             </td>
                                             <td><input id="key_val_<?= $i; ?>" name="key_val_<?= $i; ?>"  type="text" class="form-control " placeholder="" readonly="true" value='<?= $res2->key_value; ?>' /></td>
                                             <td><input type="button" class="btn btn-danger" name="btn_<?= $i; ?>" id="btn_<?= $i; ?>" value="-" title="Cant' Remove" disabled='true' ></td">
                                          </tr>
                                          <?php 
                                             }//mobile end
                                             else if($res2->info == 'message'){
                                               ?>
                                          <tr id="row_<?= $i; ?>" data-row='<?= $i; ?>'>
                                             <td  class="text-right"><label class="control-label">Message Key<label class="text-danger">*</label></label>
                                                <input type="hidden" id="info_<?= $i; ?>" name="info_<?= $i; ?>" value="<?php echo  $res2->info; ?>">
                                             </td>
                                             <td >
                                                <input id="key_<?= $i; ?>" name="key_<?= $i; ?>"  type="text" class="form-control " placeholder="" value='<?= $res2->key; ?>' />
                                             </td>
                                             <td><input id="key_val_<?= $i; ?>" name="key_val_<?= $i; ?>"  type="text" class="form-control " placeholder="" readonly="true" value='<?= $res2->key_value; ?>' /></td>
                                             <td><input type="button" class="btn btn-danger" name="btn_<?= $i; ?>" id="btn_<?= $i; ?>" value="-" title="Cant' Remove" disabled='true' ></td">
                                          </tr>
                                          <?php 
                                             }//mobile end
                                             else{
                                               ?>
                                          <tr id="row_<?= $i; ?>" data-row='<?= $i; ?>'>
                                             <td>
                                                <input type="hidden" id="info_<?= $i; ?>" name="info_<?= $i; ?>" value="<?php echo  $res2->info; ?>">
                                             </td>
                                             <td >
                                                <input id="key_<?= $i; ?>" name="key_<?= $i; ?>"  type="text" class="form-control " placeholder="" value='<?= $res2->key; ?>' />
                                             </td>
                                             <td><input id="key_val_<?= $i; ?>" name="key_val_<?= $i; ?>"  type="text" class="form-control " placeholder="" value='<?= $res2->key_value; ?>' /></td>
                                             <td><input type="button" class="btn btn-danger" name="btn_<?= $i; ?>" id="btn_<?= $i; ?>" value="-" title="Remove ?" onclick="removerow('<?= $i; ?>')"  ></td">
                                          </tr>
                                          <?php 
                                             }//mobile end
                                             }//foreach end
                                             ?>
                                       </tbody>
                                    </table>
                                 </div>
                                 

                                 <?php
                              $btn_name="Update";
                                  $btn_id="update";
                              ?>
                        

                              </div>
                              <!-- server code -->
                           <!-- </div> -->
                           <!-- /.box-body -->
                           
                           <!-- /.box-footer -->
                        
                           </div>
                           <!-- /.tab-pane -->
                           <div class="tab-pane" id="tab_2">
                            <?php
                            $account_sid = $auth_token = $twilio_phone =''; 
                            $q1=$this->db->select("*")->where("store_id",get_current_store_id())->get("db_twilio");
                            if($q1->num_rows()>0){
                              $account_sid = $q1->row()->account_sid;
                              $auth_token = $q1->row()->auth_token;
                              $twilio_phone = $q1->row()->twilio_phone;
                            } 
                            ?>
                              <div class="row">
                                 <!-- right column -->
                                 <div class="col-md-12">
                                       <div class="box-body">
                                          <div class="row">
                                            <div class="callout callout-info">
                                    <h4>Twilio SMS API</h4>
                                      Website Link: <a href='https://www.twilio.com/' target="_blank">https://www.twilio.com/</a>

                                    <p>Where <b>Account SID, Auth Token</b> and <b>Twilio Phone</b> number are neccessary for SMS Sending Feature.</p>
                                    <p><b>Note: Twilio requires the complete number to send SMS. Ex: +919876543210, +188888888888.</b></p>
                                  </div>
                                             <div class="col-md-8">
                                                <div class="form-group">
                                                   <label for="account_sid" class="col-sm-4 control-label">Account SID</label>
                                                   <div class="col-sm-4">
                                                      <input type="text" class="form-control" id="account_sid" name="account_sid" placeholder="" value="<?php print $account_sid; ?>" >
                                                      <span id="account_sid_msg" style="display:none" class="text-danger"></span>
                                                   </div>
                                                </div>
                                             </div>
                                             <div class="col-md-8">
                                                <div class="form-group">
                                                   <label for="auth_token" class="col-sm-4 control-label">Auth Token</label>
                                                   <div class="col-sm-4">
                                                      <input type="text" class="form-control" id="auth_token" name="auth_token" placeholder="" value="<?php print $auth_token; ?>" >
                                                      <span id="auth_token_msg" style="display:none" class="text-danger"></span>
                                                   </div>
                                                </div>
                                             </div>
                                             <div class="col-md-8">
                                                <div class="form-group">
                                                   <label for="twilio_phone" class="col-sm-4 control-label">Twilio Phone Number</label>
                                                   <div class="col-sm-4">
                                                      <input type="text" class="form-control" id="twilio_phone" name="twilio_phone" placeholder="" value="<?php print $twilio_phone; ?>" >
                                                      <span id="twilio_phone_msg" style="display:none" class="text-danger"></span>
                                                   </div>
                                                </div>
                                             </div>
                                             


                                          </div>
                                       </div>
                                 </div>
                                 <!--/.col (right) -->
                              </div>
                           </div>
                           <!-- /.tab-pane -->
                           <div class="tab-pane" id="tab_3">
                           
                              <div class="row">
                                 <!-- right column -->
                                 <div class="col-md-12">
                                       <div class="box-body">
                                          <div class="row">
                                            <div class="callout callout-info">
                                    <h4>Enable or Disable the SMS Sending Feature.</h4>
                                    <p>
                                      <ul>
                                        <li>Disable: If you don't want to send SMS</li>
                                        <li>HTTP/URL API : Which will allow to send SMS by using HTTP/URL Based SMS API.</li>
                                        <li>Twilio API : Which will allow to send SMS by using Twilio SMS API.</li>
                                      </ul>
                                    </p>
                                  </div>
                                             <div class="col-md-8">
                                                <div class="form-group">
                                                   <label for="sms_status" class="col-sm-4 control-label">SMS Status</label>
                                                   <div class="col-sm-4">
                                                      <select class="form-control select2" id="sms_status" name="sms_status"  style="width: 100%;" >
                                                      <option value="0">Disable</option>
                                                      <option value="1">HTTP/URL API</option>
                                                      <option value="2">Twilio API</option>
                                                   </select>
                                                   </div>
                                                </div>
                                             </div>
                                          </div>
                                       </div>
                                 </div>
                                 <!--/.col (right) -->
                              </div>
                           </div>
                           <!-- /.tab-pane -->
                        </div>
                        <!-- /.tab-content -->

                     </div>
                    <div class="col-sm-8 col-sm-offset-2 text-center">
                           <center>
                            <?php
                              $btn_name="Update";
                                  $btn_id="update";
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
      <!-- SOUND CODE -->
      <?php include"comman/code_js_sound.php"; ?>
      <!-- TABLES CODE -->
      <?php include"comman/code_js.php"; ?>
      <script src="<?php echo $theme_link; ?>js/sms.js"></script>
      
      <!-- Make sidebar menu hughlighter/selector -->
      <script>$(".<?php echo basename(__FILE__,'.php');?>-active-li").addClass("active");</script>
      <script>
         //UPDATE ROW COUNT
         $("#hidden_rowcount").val("<?= $i;?>");
         
         //UPDATE current sms_status
           $("#sms_status").val(<?= $store_rec->sms_status;?>).select2();  
        
      </script>
      <script type="text/javascript">
         function removerow(id){//id=Rowid
         
             $("#row_"+id).remove();
            // final_total();
             }
           function addrow(id){
             
               var rowcount=$("#hidden_rowcount").val();
               rowcount=parseInt(rowcount)+1;
                 $("#hidden_rowcount").val(rowcount);
         
                 var str='<tr id="row_'+rowcount+'" data-row="'+rowcount+'">';
                 
                 
                 str+='<td><input type="hidden" id="info_'+rowcount+'" name="info_'+rowcount+'" value=""></td>';
               str+='<td><input id="key_'+rowcount+'" name="key_'+rowcount+'"  type="text" class="form-control" /></td>';
               str+='<td><input id="key_val_'+rowcount+'" name="key_val_'+rowcount+'"  type="text" class="form-control" /></td>';
               str+='<td><input type="button" class="btn btn-danger" name="btn_'+rowcount+'" id="btn_'+rowcount+'" value="-" title="Remove Record" onclick="removerow('+rowcount+')"></td>';
                 str+='</tr>';
                  //console.log(str);
                       $('#api_table tbody').append(str);
            
             //return;
           }
         
         
      </script>
   </body>
</html>
