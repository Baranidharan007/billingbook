<!DOCTYPE html>
<html>
<head>
	<title>Install/Update</title>
	<link rel="shortcut icon" href="../theme/images/favicon.ico">
	<!-- <link href="https://maxcdn.bootstrapcdn.com/bootswatch/3.3.7/cosmo/bootstrap.min.css" rel="stylesheet"> -->
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" >
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" >

</head>
<body>
<div class="container">
	<div class="row">
		<div class="col-12">&nbsp;</div>
	</div>
	<div class="row justify-content-md-center">
		<div class="col-md-8">
		<?php 
		$flag=true;
		 if ( ini_get( 'allow_url_fopen' ) ) {
				/*echo '<div class="alert alert-success alert-dismissible">
                <i class="fa fa-check-circle"></i> allow_url_fopen is Checked!
              </div>';*/
              
		 } else {    
				echo '<div class="alert alert-danger alert-dismissible">
                <i class="fa fa-times-circle"></i> allow_url_fopen is DISABLED! Please Enable it
              </div>';
              $flag=false;
		 }

		 $phpversion = phpversion();
		 if($phpversion>7.4){
		 	echo '<div class="alert alert-danger alert-dismissible">
                <i class="fa fa-times-circle"></i> Application required PHP Version 7.4 or 7.3, Your server loaded with PHP Version '.$phpversion.' 
              </div>';
              $flag=false;
		 }

		 

		 echo '<div class="alert alert-info alert-dismissible">';
		 echo "<ul>";
		 echo "CHECKLIST:";
		 echo "<li>";
		 echo  'Please make sure your database should not be enabled with SQL_FULL_GROUP_BY.
                <br>
                For More information Click on Given link:
                <a href="../help/#full_group_by" target="_blank">Click here to check!</a>(Full Group By Check)';
		 echo "</li>";
		 echo "<li>";
		 echo "If you are installing application on Local Machine, then please start your Server in <b>Administration mode</b>.";
		 echo "</li>";
		 echo "</ul>";

		
             echo '</div>';

		?>
			<form class="border border-light p-5">
				<legend>Select Options to Continue</legend>



   <a href="install">
    <button class="btn btn-success btn-block my-4" <?=(!$flag) ? 'Disabled' : '';?> type="button">Install</button>
   </a>
   &nbsp;
   <a href="update">
    <button class="btn btn-info btn-block my-4" <?=(!$flag) ? 'Disabled' : '';?> type="button">Update</button>
   </a>

   
</form>
		</div>
	</div>
</div>

</body>
</html>