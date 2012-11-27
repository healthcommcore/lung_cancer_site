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
$compeleteArray = array();
$noContactArray = array();
$ptDuConArray = array();
$ptOverConArray = array();
$ptNotDuConArray = array();
$ptIncompConArray = array();

$dueDate = 7;
$overDue = 16;
$overDueEndDate = 0;
$callID = 1;
$call2ID = 2;

// get the list of pts that due for call 2
$ptDueArray = getLastAttpt($heID, $callID, $dueDate, $overDue);

// filter out pts that have completed the call2 or refused or reach 5 attemps
reset($ptDueArray);
while(list($key, $val)=each($ptDueArray)){
	$lastResult = getLastCall2($key, $call2ID);
		// first check the last contact result
	if($lastResult != 10 && $lastResult != 12 && $lastResult != 13){
		//then check number ofattempts
		$attmps = getCallAttp($key, $call2ID);
		if($attmps)$ptDueConArray[$key] = $val;
	}
}


// get the pt list overdue for call2
$ptOverDueArray = getLastAttpt($heID, $callID, $overDue, $overDueEndDate);

// filter out pts that have completed the call2 or refused or or reach 5 attemps
reset($ptOverDueArray);
while(list($key, $val)=each($ptOverDueArray)){
	$lastResult = getLastCall2($key, $call2ID);
	// first check the last contact result
	if($lastResult != 10 && $lastResult != 12 && $lastResult != 13){
		//then check number ofattempts
		$attmps = getCallAttp($key, $call2ID);
		if($attmps)$ptOverConArray[$key] = $val;
	}
}

//print_r($noContactArray);
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
		<h2 align = "center"> List of Participants for Technical Assistant Call #2 </h2>  
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
if (count($ptOverConArray)>0) {?>
	<h5 align = "center"> <font color="#526D6D"><em>List of participants that are overdue for call 2</em></font></h5>
	<img src="images/blank.gif" width = 100% height = 10px>
	<table border = 5 align="center" cellpadding=8 cellspacing = 2 width = 80%>
	<?php
	$header = getHeader(1);
	echo $header;
	getTableContent($ptOverConArray, $heID, $call2ID);
	echo "</table>\n";
}else{
    echo "<h5 align = \"center\"><font color=\"#526D6D\"><em>No cases that overdue for call 2!</h5></font></em>";
}

echo "<br><br>\n";
// print out the cases due for counsing calls
if (count($ptDueConArray)>0) {?>
	<h5 align = "center"> <font color="#526D6D"><em>List of participants that are due for call 2</em></font></h5>
	<img src="images/blank.gif" width = 100% height = 10px>
	<table border = 1 align="center" cellpadding=3 cellspacing = 2 width = 80%>
	<?php
	$header = getHeader(1);
	echo $header;
	getTableContent($ptDueConArray, $heID, $call2ID);
	echo "</table>\n";
}else{
    echo "<h5 align = \"center\"><font color=\"#526D6D\"><em>No cases that due for call 2!</h5></font></em>";
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
