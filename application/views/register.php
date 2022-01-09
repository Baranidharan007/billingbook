<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title><?php print $SITE_TITLE; ?> | Log in</title>
  <link rel='shortcut icon' href='<?php echo $theme_link; ?>images/favicon.ico' />
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.6 -->
  <link rel="stylesheet" href="<?php echo $theme_link; ?>bootstrap/css/bootstrap.min.css">
   <!-- Font Awesome -->
  <link rel="stylesheet" href="<?php echo $theme_link; ?>css/font-awesome-4.7.0/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="<?php echo $theme_link; ?>css/ionicons-2.0.1/css/ionicons.min.css">
  <!-- Select2 -->
  <link rel="stylesheet" href="<?php echo $theme_link; ?>plugins/select2/select2.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?php echo $theme_link; ?>dist/css/AdminLTE.min.css">
  <!-- iCheck -->
  <link rel="stylesheet" href="<?php echo $theme_link; ?>plugins/iCheck/square/blue.css">

  
  

</head>
<body class="hold-transition login-page">
<div class="login-box">
  <div class="login-logo">
    <a href="#"><b>
      <img src="<?php echo base_url(get_site_logo());?>" >
    </b></a>
  </div>
  <!-- /.login-logo -->
  <div class="login-box-body">
    <p class="login-box-msg">Sign in to start your session</p>

    <?php
      if($this->session->flashdata('success')!=''):
        ?>
        <div class="form-group form-fg">
        <div class="alert alert-success alert-dismissable text-left">
           <a href="javascript:void()" class="close" data-dismiss="alert" aria-label="close">&times;</a>
          <strong><?= $this->session->flashdata('success') ?></strong>
        </div>
      </div> 
         <?php 
      endif;
      if($this->session->flashdata('error')!=''):
        ?>
        <div class="form-group form-fg">
        <div class="alert alert-danger alert-dismissable text-left">
           <a href="javascript:void()" class="close" data-dismiss="alert" aria-label="close">&times;</a>
          <strong><?= $this->session->flashdata('error') ?></strong>
        </div>
         <?php
      endif;
      ?>
         
    
    <?= form_open(base_url('login/verify'), array('method'=>'POST', 'id'=>'registration_form'));?>
      <div class="div1">
        <div class="form-group has-feedback">
          <input type="text" class="form-control" name="first_name" class="input-text" placeholder="First Name*" autofocus="">
          <span class="glyphicon glyphicon-user form-control-feedback"></span>
          <small class="pull-left text-danger form-text hide first_name_msg"></small>
        </div>
        <div class="form-group has-feedback">
          <input type="text" class="form-control" name="last_name" class="input-text" placeholder="Last Name*">
          <span class="glyphicon glyphicon-user form-control-feedback"></span>
          <small class="pull-left text-danger form-text hide last_name_msg"></small>
        </div>

        <div class="form-group has-feedback">
          <input type="text" class="form-control" placeholder="Email" id="email" name="email">
          <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
          <small class="pull-left text-danger form-text hide email_msg"></small>
        </div>
        <div class="form-group has-feedback">
          <input type="mobile" class="form-control" name="mobile" class="input-text" placeholder="Mobile">
          <span class="glyphicon glyphicon-phone form-control-feedback"></span>
          <small class="pull-left text-danger form-text hide mobile_msg"></small>
        </div>

        <div class="form-group has-feedback">
          <input type="password" class="form-control" placeholder="Password" id="password" name="password">
          <span class="glyphicon glyphicon-lock form-control-feedback"></span>
          <small class="pull-left text-danger form-text hide password_msg"></small>
        </div>
        <div class="form-group has-feedback">
          <input type="password" class="form-control" placeholder="Confirm Password" id="cpassword" name="cpassword">
          <span class="glyphicon glyphicon-lock form-control-feedback"></span>
          <small class="pull-left text-danger form-text hide cpassword_msg"></small>
        </div>
        <br>
        <div class="row">
          <div class="col-xs-6 col-xs-offset-6">
            <button type="button" class="btn btn-primary btn-block btn-flat pull-right" onclick="show_second()">Next</button>
          </div>
        </div>
      </div> <!-- Div 1 end -->

      <div class="div2 hide">
        <div class="form-group has-feedback">
          <input type="text" class="form-control" name="store_name" class="input-text" placeholder="Store Name*">
          <span class="fa fa-suitcase form-control-feedback"></span>
          <small class="pull-left text-danger form-text hide store_name_msg"></small>
        </div>
        <div class="form-group">
            <select class="form-control select2" name="country" style="width: 100%;">
              <option value="">Select Country*</option>
              <?=get_country_select_list()?>
            </select>
            <small class="pull-left text-danger form-text hide country_msg"></small>
        </div>

        <div class="form-group">
            <select class="form-control select2" name="state" style="width: 100%;">
              <option value="">Select State</option>
              <?=get_state_select_list()?>
            </select>
            <small class="pull-left text-danger form-text hide state_msg"></small>
        </div>
        <div class="form-group has-feedback">
          <input type="text" class="form-control" name="city" class="input-text" placeholder="City*">
          <span class="fa fa-location-arrow form-control-feedback"></span>
          <small class="pull-left text-danger form-text hide city_msg"></small>
        </div>
        <br>
        <div class="row">
          <div class="col-xs-6">
            <button type="button" class="btn btn-default btn-block btn-flat pull-right" onclick="show_first()">Previous</button>
          </div>
          <div class="col-xs-6">
            <button type="button" class="btn btn-primary btn-block btn-flat pull-right" onclick="show_third()">Next</button>
          </div>
        </div>
      </div> <!-- Div 2 end -->
      <div class="div3 hide">
        
        <div class="form-group">
            <select class="form-control select2" id="timezone" name="timezone"  style="width: 100%;">
              <option value="">Timezone*</option>
               <?php
                  $query2="select * from db_timezone where status=1";
                  $q2=$this->db->query($query2);
                  if($q2->num_rows()>0)
                   {
                    
                    foreach($q2->result() as $res1)
                     {
                       
                       echo "<option value='".$res1->timezone."'>".$res1->timezone."</option>";
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
            <small class="pull-left text-danger form-text hide timezone_msg"></small>
        </div>

        <div class="form-group">
            <select class="form-control select2" id="date_format" name="date_format"  style="width: 100%;">
               <option value="dd-mm-yyyy">dd-mm-yyyy</option>
               <option value="mm/dd/yyyy">mm/dd/yyyy</option>
            </select>
            <small class="pull-left text-danger form-text hide date_format_msg"></small>
        </div>
        <div class="form-group">
            <select class="form-control select2" id="time_format" name="time_format"  style="width: 100%;">
               <option value="12">12 Hours</option>
              <option value="24">24 Hours</option>
            </select>
            <small class="pull-left text-danger form-text hide time_format_msg"></small>
        </div>
        <div class="form-group">
            <select class="form-control select2" id="currency" name="currency"  style="width: 100%;">
              <option value="">Currency*</option>
               <?php
                  $query2="select * from db_currency where status=1";
                  $q2=$this->db->query($query2);
                  if($q2->num_rows()>0)
                   {
                    
                    foreach($q2->result() as $res1)
                     {
                       echo "<option value='".$res1->id."'>".$res1->currency_name.' '.$res1->currency_code.' ('.$res1->currency.")</option>";
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
            <small class="pull-left text-danger form-text hide currency_msg"></small>
        </div>
        <div class="form-group">
            <select class="form-control select2" id="currency_placement" name="currency_placement"  style="width: 100%;">
              <option value="">Currency Placement*</option>
               <option value="Right">After Amount</option>
                <option value="Left">Before Amount</option>
            </select>
            <small class="pull-left text-danger form-text hide currency_placement_msg"></small>
        </div>
        <div class="form-group">
            <select class="form-control select2" id="decimals" name="decimals"  style="width: 100%;">
               <option value="">Decimals*</option>
               <option value="1">1</option>
               <option value="2">2</option>
               <option value="3">3</option>
               <option value="4">4</option>
            </select>
            <small class="pull-left text-danger form-text hide decimals_msg"></small>
        </div>

        
        <br>
        <div class="row">
          <div class="col-xs-6">
            <button type="button" class="btn btn-default btn-block btn-flat pull-right" onclick="show_second(false)">Previous</button>
          </div>
          <div class="col-xs-6">
            <button type="button" class="btn btn-primary btn-block btn-flat pull-right" onclick="submit_form()">Submit</button>
          </div>
        </div>
      </div> <!-- Div 3 end -->

      <div class="row">        
        <div class="col-xs-6 text-right pull-right"><br>
          <a href="<?=base_url('login')?>">I have account</a>
        </div>
      </div>
    
    <?php form_close(); ?>

    <div class="row">
      <div class="col-md-12 text-center">
        <p style='font-style: italic;'>Version <?=app_version();?></p>   
      </div>
    </div>
  </div>
  <!-- /.login-box-body -->
 <?php if(demo_app()){ ?>
  <div class="box-body">
    <label>Click to Start Session!</label>
    <div class="row">
     <div class="col-md-12">
       <table class="table table-bordered table-condensed text-center">         
            <tr>
              <td>admin@example.com</td>
              <td>123456</td>
              <td><button type="button" class="btn btn-info btn-block btn-flat admin">Apply</button></td>
            </tr>
            </tbody>
          </table>
     </div>
    </div>
    <i><i class="fa fa-fw fa-info-circle text-warning"></i>Some of the features are disabled in demo and it will be reset after each hour.</i>
  </div>
<?php } ?>
         
  
</div>

<!-- /.login-box -->

<!-- jQuery 2.2.3 -->
<script src="<?php echo $theme_link; ?>plugins/jQuery/jquery-2.2.3.min.js"></script>
<!-- Bootstrap 3.3.6 -->
<script src="<?php echo $theme_link; ?>bootstrap/js/bootstrap.min.js"></script>
<!-- Select2 -->
<script src="<?php echo $theme_link; ?>plugins/select2/select2.full.min.js"></script>
<!-- Initialize Select2 Elements -->
<script type="text/javascript"> $(".select2").select2(); </script>
<!-- iCheck -->
<script src="<?php echo $theme_link; ?>plugins/iCheck/icheck.min.js"></script>
<script>
  $(function () {
    $('input').iCheck({
      checkboxClass: 'icheckbox_square-blue',
      radioClass: 'iradio_square-blue',
      increaseArea: '20%' // optional
    });
  });
</script>
<script type="text/javascript" >
$(function($) { // this script needs to be loaded on every page where an ajax POST may happen
    $.ajaxSetup({ data: {'<?php echo $this->security->get_csrf_token_name(); ?>' : '<?php echo $this->security->get_csrf_hash(); ?>' }  }); });
</script>

<script type="text/javascript">
  /*Check XSS Code*/
    function xss_validation(data) {
      if(typeof data=='object'){
        for (var value of data.values()) {
           if(typeof value!='object' && (value!='' && value.indexOf("<script>") != -1)){
            toastr["error"]("Failed to Continue! XSS Code found as Input!");
            return false;
           }
        }
        return true;
      }
      else{
        if(typeof value!='object' && (data!='' && data.indexOf("<script>") != -1)){
            toastr["error"]("Failed to Continue! XSS Code found as Input!");
            return false;
        }
        return true;
      }
    }
    //end
  
  function validateEmail(sEmail) {
      var filter = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
      if (filter.test(sEmail)) {
          return true;
      }
      else {
          return false;
      }
  }
  function get_name_value(name){
    return $("input[name='"+name+"']").val();
  }
  function get_select_value(name){
    return $("select[name='"+name+"']").val();
  }
  /*Show First Div*/
  function show_div1(){
    $('.div1').removeClass('hide');
  }
  function hide_div1(){
    $('.div1').addClass('hide');
  }

  function show_div2(){
    $('.div2').removeClass('hide');
  }
  function hide_div2(){
    $('.div2').addClass('hide');
  }

  function show_div3(){
    $('.div3').removeClass('hide');
  }
  function hide_div3(){
    $('.div3').addClass('hide');
  }


  function validate_first(){
    var flag=true;
    $(".email_msg, .password_msg, cpassword_msg").addClass('hide').text("");
    if(!get_name_value('first_name')){
      $(".first_name_msg").removeClass('hide').text("Required Field!");
      flag=false;
    }
    if(!get_name_value('last_name')){
      $(".last_name_msg").removeClass('hide').text("Required Field!");
      flag=false;
    }
    if(!get_name_value('email')){
      $(".email_msg").removeClass('hide').text("Required Field!");
      flag=false;
    }
    if(get_name_value('email')){
      if(!validateEmail(get_name_value('email'))){
        $(".email_msg").removeClass('hide').text("Invalid Email Format!");
        flag=false;
      }
    }
    if(!get_name_value('password')){
      $(".password_msg").removeClass('hide').text("Required Field!");
      flag=false;
    }
    if(!get_name_value('cpassword')){
      $(".cpassword_msg").removeClass('hide').text("Required Field!");
      flag=false;
    }
    if(get_name_value('password')){
      if(get_name_value('password')!=get_name_value('cpassword')){
        $(".password_msg").removeClass('hide').text("Mismatched Password!");
        $(".cpassword_msg").addClass('hide').text("");
        flag=false;
      }
      else{
        if(parseFloat(get_name_value('password').length) < 6){
          $(".password_msg").removeClass('hide').text("Passwprd must be atleast 6 characters!");
          $(".cpassword_msg").addClass('hide').text("");
          flag=false;
        }
      }
    }
    return flag;
  }
  function validate_second(){
    var flag=true;
    $(".store_name_msg, .country_msg, .city_msg").addClass('hide').text("");
    if(!get_name_value('store_name')){
      $(".store_name_msg").removeClass('hide').text("Required Field!");
      flag=false;
    }
    
    if(!get_select_value('country')){
      $(".country_msg").removeClass('hide').text("Please Select Country!");
      flag=false;
    }
    if(!get_name_value('city')){
      $(".city_msg").removeClass('hide').text("Required Field!");
      flag=false;
    }
    return flag;
  }
  function validate_third(){
    var flag=true;
    $(".timezone_msg, .currency_msg, .currency_placement_msg, .decimals_msg").addClass('hide').text("");
    if(!get_select_value('timezone')){
      $(".timezone_msg").removeClass('hide').text("Please Select Timezone!");
      flag=false;
    }
    if(!get_select_value('currency')){
      $(".currency_msg").removeClass('hide').text("Please Select Currency!");
      flag=false;
    }
    if(!get_select_value('currency_placement')){
      $(".currency_placement_msg").removeClass('hide').text("Please Select Currency Placement!");
      flag=false;
    }
    if(!get_select_value('decimals')){
      $(".decimals_msg").removeClass('hide').text("Please Select Decimals!");
      flag=false;
    }
  
    return flag;
  }
  function show_first(validate=true){
    show_div1();
    hide_div2();
    hide_div3();
  }
  function show_second(validate=true){
    if(validate){
      if(!validate_first()){
        return false;
      }
    }
    show_div2();
    hide_div1();
    hide_div3();
  }
  function show_third(validate=true){
    if(validate){
      if(!validate_second()){
        return false;
      }
    }
    show_div3();
    hide_div1();
    hide_div2();
    
  }
  function submit_form(validate=true){
    if(validate){
      if(!validate_third()){
        return false;
      }
    }

    
        
        data = new FormData($('#registration_form')[0]);//form name
        /*Check XSS Code*/
        if(!xss_validation(data)){ return false; }
        
        $(".submit_btn").html('<i class="fa fa-spinner fa-spin"></i>Loading').attr('disabled',true);  //Enable Save or Update button
        $.ajax({
        type: 'POST',
        url: '<?=base_url()?>register/register_store',
        data: data,
        cache: false,
        contentType: false,
        processData: false,
        success: function(result){
          result = result.trim();
          if(result=='success'){
            location.href = '<?=base_url()?>login';
          }
          else{
            $(".error_div").removeClass('hide');
            $(".error_ul").text(result); 
          }          
          $(".submit_btn").html('Submit').attr('disabled',false);  //Enable Save or Update button
          
         }
         });
   

  }
 
</script>

</body>
</html>
