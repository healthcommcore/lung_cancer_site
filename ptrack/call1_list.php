<?php
include("includes/connection.php");
include("includes/he_function.php");
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
// get the he ID
$heID = $_GET['heID'];

// declare some variables 
$ptDueArray = array();
$ptOverDueArray = array();
$ptNotDueArray = array();
$dueDate = 8;
$overDue = 14;
$overDueEndDate = 0;
$callID = 1;
//call function to get lis of participants that due for coaching calls
$ptDueArray = getCounsCallDue($heID, $dueDate, $overDue, $dueDate);
// filter the participant in the due list by checking the last contact result
$ptDuConArray = getFinalCallList($ptDueArray, $callID);

//  get list of pt that is a over due
$ptOverArray = getCounsCallOvDu($heID, $overDue, $overDueEndDate, $dueDate);
// filter the participant in the due list by checking the last contact result
$ptOverConArray = getFinalCallList($ptOverArray, $callID);

//call function to get lis of participants that not yet due for coaching calls
//$ptNotDueArray = getCounsCallDue($heID, 0, $dueDate, $dueDate);
//$ptNotDuConArray = getFinalCallList($ptNotDueArray, $callID);

//call function to get list of participants that call 1 incomplete
//$ptIncompArray = getCounsCallOvDu($heID, 0, $overDueEndDate, $dueDate);
//$ptIncompConArray = getFinalCallList($ptIncompArray, $callID);

?>
<!DOCTYPE html PUBLIC "-//W3C//ulD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/ulD/xhtml1-strict.uld">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>HD2 Tracking</title>
<link rel="stylesheet" type="text/css" href="includes/main.css" />
</head>

<body>
	<!-- Begin Wrapper -->
   <div id="wrapper">
   
         <!-- Begin Header -->

         <div id="header">
		 <br><br>
		<h2 align = "center"> List of Participants for Technical Assistant Call #1 </h2>  
		<br><p align = "right"><form method="POST" action="login.php" >
	     <input type = "submit" value = "log out" size = 100></form></p>   
		 </div>
		 <!-- End Header -->
		 
         <!-- Begin Faux Columns -->
		 <div id="faux">
		 
		       <!-- Begin Left Column -->
		       <div id="leftcolumn1">
		 		<?php
						$menu = getMenu1($heID);
						echo $menu;
				?>
		       </div>

		       <!-- End Left Column -->
		 
		       <!-- Begin Right Column -->
		       <div id="rightcolumn">
<?php

// print out the list for over due
$counter = 0;
if (count($ptOverConArray)>0) {?>
	<h5 align = "center"> <font color="#526D6D"><em>List of participants that Overdue for call 1</em></font></h5>
	<img src="images/blank.gif" width = 100% height = 10px>
	<table border = 1 align="center" cellpadding=8 cellspacing = 2 width = 80%>
	<?php
	$header = getHeader(1);
	echo $header;
	getTableContent($ptOverConArray, $heID, $callID);
	echo "</table>\n";
}else{
    echo "<h5 align = \"center\"><font color=\"#526D6D\"><em>No cases that overdue for call 1!</h5></font></em>";
}

echo "<br><br>\n";
// print out the cases due for counsing calls
$counter = 0;
if (count($ptDuConArray)>0) {?>
	<h5 align = "center"> <font color="#526D6D"><em>List of participants that are due for call 1 </em></font></h5>
	<img src="images/blank.gif" width = 100% height = 10px>
	<table border = 1 align="center" cellpadding=3 cellspacing = 2 width = 80%>
	<?php
	$header = getHeader(1);
	echo $header;
	getTableContent($ptDuConArray, $heID, $callID);
	echo "</table>\n";
}else{
    echo "<h5 align = \"center\"><font color=\"#526D6D\"><em>No cases that due for call 1!</h5></font></em>";
}



dbClose();
?>
		       
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
