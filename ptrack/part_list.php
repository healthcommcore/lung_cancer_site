<?php
include("includes/connection.php");
$errMsg="";
//clear the session
$_SESSION = array();
// CONNECT TO DATABASE
if ($mysqlID=dbConnect()) {
    selectDatabase("lung_cancer_user");
} else {
    $errMsg.="<b>ERROR:</b> ".mysql_error()."<br>\n";
}

// get the passing values
$lname = $_GET['ptLName'];
$fname = $_GET['ptFName'];
$action = $_GET['action'];
$heID = $_GET['heID'];

$lname = str_replace('\\', '', $lname);
$fname = str_replace('\\', '', $fname);
// return all possible matches, so RAs can find the record they need 
		if($lname && !$fname){
			// get list of the pts
			$ptListArray = getPtLname($lname, $heID);
		}elseif($fname && !$lname){
			// get list of the pts
			$ptListArray = getPtFname($fname, $heID);
		}elseif($fname && $lname){
			// get list of the pts
			$ptListArray = getPtBname($fname, $lname, $heID);
		}

// check form submission
if ($_POST["isSubmittedLogOut"]==1) {
	header("Location: login.php");
	exit();
}
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
		<h2 align = "center"> List of Participants with the Same <?php if($lname & $fname) {echo " First and Last";} elseif(!$lname){ echo "First";} else{echo "Last";} ?> Name </h2>  
		<br><p align = "right"><form method="POST" action="login.php" >
	     <input type = "submit" value = "log out" size = 100></form></p>   
		 </div>
		 <!-- End Header -->
		 
         <!-- Begin Faux Columns -->
		 <div id="faux">
		 
		       <!-- Begin Left Column -->
		       <div id="leftcolumn1">
		 		<?php
					if($heID){
						$menu = getMenu1($heID);
						echo $menu;
					}else{
						$menu = getMenu();
						echo $menu;
					}
				?>
		       </div>

		       <!-- End Left Column -->
		 
		       <!-- Begin Right Column -->
		       <div id="rightcolumn">
	<?php
	if (count($ptListArray)>0) {?>
	<table border = 5 align = "left" valign = "top" cellpadding =8 cellspacing = 4>
	<tr>
	</tr>
	<tr align = "center">
	<td> <strong>Study ID </strong></td>
	<td> <strong>First Name </strong></td>
	<td> <strong>Last Name </strong></td>
	<td><img src="images/blank.gif"></td>
	</tr>
	<?php
	reset ($ptListArray);
	while(list($keyID, $ptVal)=each($ptListArray)){
		//echo "The site name is " .$sitKey. "<br>";
	    reset($ptVal);
		$ptCount = count($ptVal);
		if($action == "participants"){
			echo "<form name = form1 method=\"POST\" action = \"edit_part.php\">\n";
		}elseif($action == "appointment"){
			echo "<form name = form1 method=\"POST\" action = \"edit_appt.php\">\n";
		}elseif($action == "recruitment"){
			echo "<form name = form1 method=\"POST\" action = \"edit_recruitment.php\">\n";
		}elseif($action == "contact"){
			echo "<form name = form1 method=\"POST\" action = \"edit_contact.php\">\n";
		}else{
			echo "<form name = form1 method=\"POST\" action = \"edit_enrollment_one.php\">\n";
		}
		echo "<tr align = \"center\">\n";
		echo "<td>" .$keyID. "<input type = \"hidden\" name = \"partID\" value = \"".$keyID."\"></td>\n";
		echo "<td>" .$ptVal[0]. "<input type = \"hidden\" name = \"ptFName\" value = \"".$ptVal[0]."\"></td>\n";
		echo "<td>" .$ptVal[1]. "<input type = \"hidden\" name = \"ptLName\" value = \"".$ptVal[1]."\"></td>\n";
		echo "<td><input type = \"submit\" name = \"action\" value = \"   Edit Record   \"></td>\n";
		echo "</tr></form>\n";
		$counter++;
	}
	echo "</table>\n";
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
