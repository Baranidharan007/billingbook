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
            if(!isset($username)){
              $username=$mobile=$email=$q_id=$role_id=$store_id='';
              $readonly=$warehouses=$last_name='';
            }else{
              $readonly= '';//'readonly="readonly"';
            }
            if(empty($profile_picture)){
              $profile_picture = 'theme/dist/img/avatar5.png';
            }
            
            //  $disabled = ($q_id==1)? 'disabled' : '';
            
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
                     <div class="box box-primary ">
                        <!-- /.box-header -->
                        <!-- form start -->
                        <form class="form-horizontal" id="users-form"  onkeypress="return event.keyCode != 13;">
                           <input type="hidden" id="base_url" value="<?php echo $base_url;; ?>">
                           <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
                           <div class="box-body">
                            <div class="row">
                              <div class="col-md-6">
                                <!-- Store Code -->
                               <?php if(store_module() && is_admin()) {$this->load->view('store/store_code',array('show_store_select_box'=>true,'store_id'=>$store_id,'label_length'=>'col-sm-4','div_length'=>'col-sm-8')); }else{
                                echo "<input type='hidden' name='store_id' id='store_id' value='".get_current_store_id()."'>";
                              }?>
                              <!-- Store Code end -->

                              <div class="form-group">
                                 <label for="new_user" class="col-sm-4 control-label"><?= $this->lang->line('first_name'); ?><label class="text-danger">*</label></label>
                                 <div class="col-sm-8">
                                    <input type="text" class="form-control input-sm" id="new_user" name="new_user" placeholder="" onkeyup="shift_cursor(event,'mobile')" value="<?php print $username; ?>" autocomplete='off' <?=$readonly;?> autofocus>
                                    <span id="new_user_msg" style="display:none" class="text-danger"></span>
                                 </div>
                              </div>
                              <div class="form-group">
                                 <label for="last_name" class="col-sm-4 control-label"><?= $this->lang->line('last_name'); ?><label class="text-danger">*</label></label>
                                 <div class="col-sm-8">
                                    <input type="text" class="form-control input-sm "  id="last_name" name="last_name" autocomplete='off' placeholder="" value="<?= $last_name; ?>"  >
                                    <span id="last_name_msg" style="display:none" class="text-danger"></span>
                                 </div>
                              </div>
                              <div class="form-group">
                                 <label for="mobile" class="col-sm-4 control-label"><?= $this->lang->line('mobile'); ?></label>
                                 <div class="col-sm-8">
                                    <input type="text" class="form-control input-sm no_special_char_no_space"  id="mobile" name="mobile" autocomplete='off' placeholder="" value="<?php print $mobile; ?>" onkeyup="shift_cursor(event,'email')"  >
                                    <span id="mobile_msg" style="display:none" class="text-danger"></span>
                                 </div>
                              </div>
                              <div class="form-group">
                                 <label for="email" class="col-sm-4 control-label"><?= $this->lang->line('email'); ?><label class="text-danger">*</label></label>
                                 <div class="col-sm-8">
                                    <input type="text" autocomplete='off' class="form-control input-sm" value="<?php print $email; ?>"  id="email" name="email" placeholder="" onkeyup="shift_cursor(event,'pass')"  >
                                    <span id="email_msg" style="display:none" class="text-danger"></span>
                                 </div>
                              </div>
                              <?php 
                              $selection = '';
                              if(isset($q_id) && !empty($q_id) && !is_admin()) {
                                $this->db->select("role_id")->where("id",$q_id)->from("db_users");
                                
                                $role_id =$this->db->get()->row()->role_id;
                                if(!empty($role_id)){
                                  $q3 = $this->db->select("store_id,role_name")->from("db_roles")->where("id",$role_id)->get()->row();

                                  if(!empty($q3->store_id) && $q3->store_id!=get_current_store_id()){
                                    $selection = '<option value="'.$role_id.'">'.$q3->role_name."</option>";
                                  }
                                }
                                
                              }

                              if($q_id!=1){
                                ?>
                              
                              <div class="form-group">
                                 <label for="role_id" class="col-sm-4 control-label"><?= $this->lang->line('role'); ?><label class="text-danger">*</label> </label>
                                 <div class="col-sm-8">
                                    <select class="form-control select2" <?=$readonly;?> id="role_id" name="role_id"  style="width: 100%;">
                                       <?php
                                       if(!empty($selection)){
                                        echo $selection;
                                       }
                                       else{
                                        $query2="select * from db_roles where id!=1 and status=1 and store_id=".get_current_store_id();

                                          $q2=$this->db->query($query2);
                                          if($q2->num_rows()>0)
                                           {
                                            echo "<option value=''>-Select-</option>";
                                            foreach($q2->result() as $res1)
                                             {
                                               if((isset($role_id) && !empty($role_id)) && $role_id==$res1->id){$selected='selected';}else{$selected='';}
                                               echo "<option ".$selected." value='".$res1->id."'>".$res1->role_name."</option>";
                                             }
                                           }
                                           else
                                           {
                                              ?>
                                       <option value="">No Records Found</option>
                                       <?php
                                          }
                                       }
                                          
                                          ?>
                                    </select>
                                    <span id="role_id_msg" style="display:none" class="text-danger"></span>
                                 </div>
                              </div>
                            <?php } ?>

                              <div class="form-group">
                                 <label for="pass" class="col-sm-4 control-label"><?= $this->lang->line('password'); ?>
                                 <?php if(empty($username)){ ?>
                                 <label class="text-danger">*</label>
                                <?php } ?>
                               </label>
                                 <div class="col-sm-8">
                                    <input type="password" autocomplete='off' class="form-control input-sm"  <?php print $readonly; ?> id="pass" name="pass" placeholder="" onkeyup="shift_cursor(event,'confirm')"  >
                                    <span id="pass_msg" style="display:none" class="text-danger"></span>
                                 </div>
                              </div>
                              <div class="form-group">
                                 <label for="confirm" class="col-sm-4 control-label"><?= $this->lang->line('confirm_password'); ?>
                                 <?php if(empty($username)){ ?>
                                 <label class="text-danger">*</label>
                                <?php } ?></label>
                                 <div class="col-sm-8">
                                    <input type="password" autocomplete="off" class="form-control input-sm" <?php print $readonly; ?> id="confirm" name="confirm" placeholder="">
                                    <span id="confirm_msg" style="display:none" class="text-danger"></span>
                                 </div>
                              </div>

                              <!-- Warehouse Code -->
                              <?php 

                              //For Selection Box while updating
                              if($q_id!=1){
                                $ids = array();
                                if(!empty($username) && warehouse_module()){
                                    $q1 = $this->db->select("warehouse_id")->where("user_id",$q_id)->get("db_userswarehouses");
                                    if($q1->num_rows()>0){
                                      foreach ($q1->result() as $res1) { 
                                        $ids[] = $res1->warehouse_id; 
                                      }
                                    } 
                                  }

                                  $store_id = (isset($store_id) && !empty($store_id)) ? $store_id : get_current_store_id();
                                 if(warehouse_module()) {$this->load->view('warehouse/warehouse_code',
                                    array(
                                          'show_warehouse_select_box'=>true,
                                          'store_id'=>$store_id,
                                          'custom_id'=>'warehouses',
                                          'custom_name'=>'warehouses[]',
                                          'label'=>$this->lang->line('warehouses'),
                                          'div_length'=>'col-sm-8',
                                          'label_length'=>'col-sm-4',
                                          'show_select_option'=>true,
                                          'multiple'=>'multiple',
                                          'show_select_option'=>false,
                                          'data_placeholder'=>'Multiple',
                                          'ids' => $ids
                                        )); 
                               }else{
                                  echo "<input type='hidden' name='warehouses' id='warehouses' value='".get_store_warehouse_id()."'>";
                                 }
                             }
                              ?>
                              <!-- Warehouse Code end -->
                              </div>
                              <div class="col-md-6">
                                  <div class="form-group">
                                     <label for="address" class="col-sm-4 control-label"><?= $this->lang->line('profile_picture'); ?></label>
                                     <div class="col-sm-8">
                                        <input type="file" id="profile_picture" name="profile_picture">
                                        <span id="logo_msg" style="display:block;" class="text-danger">Max Width/Height: 500px * 500px & Size: 500Kb </span>
                                     </div>
                                  </div>
                               </div>
                               <div class="col-md-6 ">
                                  <div class="form-group">
                                     <div class="col-sm-8 col-sm-offset-4">
                                        <img width="200px" height="200px" class='img-responsive' style='border:3px solid #d2d6de;' src="<?php echo base_url($profile_picture);?>">
                                     </div>
                                  </div>
                               </div>
                              </div>
                              
                            </div>
                              

                            <div class="box-footer">
                              <div class="col-sm-8 col-sm-offset-2 text-center">
                                 <!-- <div class="col-sm-4"></div> -->
                                 <?php
                                    if($username!=""){
                                         $btn_name="Update";
                                         $btn_id="update";
                                    
                                    }
                                              else{
                                                  $btn_name="Save";
                                                  $btn_id="save";
                                              }
                                    
                                              ?>
                                 <input type="hidden" name="q_id" id="q_id" value="<?php echo $q_id;?>"/>
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
                           
                        </form>
                           </div>
                           <!-- /.box-body -->
                           
                           <!-- /.box-footer -->
                     </div>
                     <!-- /.box -->
                  </div>
                  <!--/.col (right) -->
              
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
      <script src="<?php echo $theme_link; ?>js/users.js"></script>
      <script type="text/javascript">
        <?php if(isset($q_id) && !empty($q_id)){ ?>
          $("#store_id").attr('readonly',true);
        <?php }?>
      </script>
      <script type="text/javascript">
        var base_url=$("#base_url").val();
        $("#store_id").on("change",function(){
          var store_id=$(this).val();
          $.post(base_url+"sales/get_warehouse_select_list",{store_id:store_id},function(result){
              $("#warehouses").html('').append(result).select2();
          });
        });

        function hide_if_admin_and_store_admin(){
          var role_id=$("#role_id").val();
          if(role_id==<?=store_admin_id()?>){
            $(".warehouse_parent").hide()
          }
          else{
            $(".warehouse_parent").show()
          }
        }
        $("#role_id").on("change",function(){
          hide_if_admin_and_store_admin();
        });
        hide_if_admin_and_store_admin();
        <?php 

        ?>
      </script>
      <!-- Make sidebar menu hughlighter/selector -->
      <script>$(".<?php echo basename(__FILE__,'.php');?>-active-li").addClass("active");</script>
   </body>
</html>
