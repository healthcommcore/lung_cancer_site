<?php
include("includes/connection.php");
include("includes/he_function.php");
include("includes/report_function.php");
$errMsg="";
// CONNECT TO DATABASE
if ($mysqlID=dbConnect()) {
    selectDatabase("lung_cancer_user");
} else {
    $errMsg.="<b>ERROR:</b> ".mysql_error()."<br>\n";
}
// check form submission
if ($_POST["isSubmittedLogOut"]==1) {
	header("Location: login.php");
	exit();
}
$cat = $_GET['cat'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Recruitment Reports</title>
<link rel="stylesheet" type="text/css" href="includes/main.css" />
</head>

<body>
  <!-- Begin Wrapper -->
   <div id="wrapper">
   
         <!-- Begin Header -->

         <div id="header">
		 	<br><br>
		<h2 align = "center"> Download Recruitment Reports by <?php echo $cat; if($cat == "Randomization") echo " Arm";?></h2>  
		<br><p align = "right"><form method="POST" action="login.php" >
	     <input type = "submit" value = "log out" size = 100></form></p>  
		 </div>
		 <!-- End Header -->
		 
         <!-- Begin Faux Columns -->
		 <div id="faux">
		 
		       <!-- Begin Left Column -->
		       <div id="leftcolumn"> 
		 		<?php
						$menu = getAdMenu();
						echo $menu;
					?>
		       </div>

		       <!-- End Left Column -->
		 
		       <!-- Begin Right Column -->
		       <div id="rightcolumn">
    <br><br>
	<p>Click below to generate an up to date report of recruitment results by <?php echo $cat;?>. Today's date: <?php echo date('m-d-Y'); ?></p>
	<br><br>
	<form method="POST" action="download_recruit_report.php?cat=<?php echo $cat; ?>&co=no">
	    <p align = "center"><input type = "submit" value = "Clipboard Offered = No" size = 100></p>
	</form>
	<form method="POST" action="download_recruit_report.php?cat=<?php echo $cat; ?>&co=yes">
	<br><br>
		<p align = "center"><input type = "submit" value = "Clipboard Offered = Yes" size = 100></p>
    </form>
	          
			   <div class="clear"></div>
			   
		       </div>
		       <!-- End Right Column -->
			   
			   <div class="clear"></div>
			   
         </div>	   
         <!-- End Faux Columns --> 
		 
   </div>
   <!-- End Wrapper -->

</body>
</html>
