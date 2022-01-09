<!DOCTYPE html>
<html lang="en">
<head>
  <title><?php print $SITE_TITLE; ?> | Change Password</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel='shortcut icon' href='<?php echo $theme_link; ?>images/favicon.ico' />
  <link rel="stylesheet" href="<?php echo $theme_link; ?>bootstrap452/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo $theme_link; ?>css/font-awesome-4.7.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="<?php echo $theme_link; ?>dist/css/login.css">
  <script src="<?php echo $theme_link; ?>plugins/jQuery/jquery-2.2.3.min.js"></script>
  <script src="<?php echo $theme_link; ?>bootstrap452/js/bootstrap.min.js"></script>
</head>
<body>

<div class="login-fg">
    <div class="container-fluid">
        <div class="row">
            
            <?=app_front_tag_line()?>

            <div class="col-xl-4 col-lg-5 col-md-12 login">
                <div class="login-section">
                    <div class="logo clearfix">
                        <a href="#">
                           <img src="<?php echo base_url(get_site_logo());?>" >
                        </a>
                    </div>
                    
                    <div class="or-login clearfix">
                        <span>Change Password</span>
                    </div>
                    <div class="text-danger tex-center"><?php echo $this->session->flashdata('failed'); ?></div>
                    <div class="text-success tex-center"><?php echo $this->session->flashdata('success'); ?></div>
                    <div class="form-container">
                            <?= form_open(base_url('login/change_password'), array('method'=>'POST','autocomplete'=>'off'));?>

                            <input type="hidden" name="email" id="email" value="<?= $email;?>">
                            <input type="hidden" name="otp" id="otp" value="<?= $otp;?>">

                            <div class="form-group form-fg">
                                <input type="password" name="password" class="input-text" placeholder="New Password">
                                <i class="fa fa-lock"></i>
                            </div>
                            <div class="form-group form-fg">
                                <input type="password" name="cpassword" class="input-text" placeholder="Confirm Password">
                                <i class="fa fa-lock"></i>
                            </div>
                            <div class="checkbox clearfix">
                                <a href="<?=base_url('login/forgot_password')?>">Forgot Password</a>
                            </div>
                            <div class="form-group mt-2">
                                <button type="submit" class="btn-md btn-fg btn-block">Submit</button>
                            </div>
                        <?php form_close(); ?>
                    </div>
                    <?php if(store_module()){?>
                    <p>Don't have an account? <a href="<?=base_url('register')?>" class="linkButton"> Register</a></p>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>  

</body>
</html>
