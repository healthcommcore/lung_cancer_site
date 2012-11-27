<?php


// BETTER  ERROR HANDLING
define ( "JPATH_SITE", getcwd() . '/../');
require( 'includes/connection.php' );
require( 'includes/survey.php' );
require_once( JPATH_SITE .'/includes/hd2/shared.php' );

$export = $_REQUEST['export'];
if ($export != '') {
	$today = date('Ymd');
	$filename = 'TFR'.$today;
	header("Content-disposition: filename=$filename.xls");
	header("Content-type: application/vnd.ms-excel");
	header("Expires: 0");
	// header("Content-disposition: attachment;filename=$filename.txt");
	// header("Content-type: text/plain");
	getSurveyData();
	
	// print_r($userlist);
}


else {
?>
<!DOCTYPE html PUBLIC "-//W3C//ulD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/ulD/xhtml1-strict.uld">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>TFR export</title>
<link rel="stylesheet" type="text/css" href="includes/main.css" />
</head>

<body>
<!-- Begin Wrapper -->
   <div id="wrapper">
   
         <!-- Begin Header -->

         <div id="header">
		 <br><br>
		<h2 align = "center">TFR 1 Export</h2>  
			<br><p align = "right"><form method="POST" action="login.php" >
	     <input type = "submit" value = "log out" size = 100></form></p>   
		 </div>
		 <!-- End Header -->
		 
         <!-- Begin Faux Columns -->
		 <div id="faux">
		 
		       <!-- Begin Left Column -->
		       <div id="leftcolumn1">
		 			<?php
						$menu = getMenu();
						echo $menu;
					?>
		       </div>

		       <!-- End Left Column -->
			   
			   <!-- Begin Right Column -->
		       <div id="rightcolumn">
		<form action="exportTFR.php?export=true" method="post" >
		
		<h3>Reminder!</h3>
		<?php getSurveyPreview() ?>
		<br/>
		<p>Be sure to <b>save the exported data file</b>.
		</p>
<br/>		<p>  
		Once a participant's survey data has been exported, it is marked as such and cannot be re-exported on any subsequent exports.
		
		</p>
		<br/>
		<br/>
		
		<input class="right" type="submit" value="Export TFR data">
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
<?php
}
?>

