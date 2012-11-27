<?php
include("includes/connection.php");
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
if (isset($_POST["isSubmitted"]) && $_POST["isSubmitted"]==1) {
	//check if the study ID is valid
	$idFlg = checkPartID(trim($_POST["partID"]));
	if ($idFlg){
	    $idErr = "<b><font color = 'red'>ERROR: ".$errIDMsg."!!</font></b><br>\n";
	}
	//check to see if appt date is correct
	$dateFlg = checkValidDate(trim($_POST["startDate"]));
	if ($dateFlg){
	    $dateErr = "<b><font color = 'red'>ERROR: ".$dateErrMsg." for start date!!</font></b><br>\n";
	}
	
	if(!$idErr && !$dateErr){
		// format the start date
		$startDate = formatDate(trim($_POST["startDate"]), 1);
		// update the start date
		$updFlg = updateStartDate(trim($_POST["partID"]), $startDate);
		if($updFlg){
			$updErr="<b><font color = 'red'>ERROR:".$errIstMsg."</font><br>\n";
		}else{
			$succMsg = "<font color = 'green' size = \"+1\">The startDate has been modified for this participant!</font><br>\n";
		}
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Change Start Date</title>
<link rel="stylesheet" type="text/css" href="includes/main.css" />
<script language="javascript">
	<!-- 
	// FUNCTION TO CHECK IF ALL FIELDS ARE FILLED
	function checkForm()
	{
		var partID = document["form"]["partID"].value;
		var startDate = document["form"]["startDate"].value;
		if(partID == "" || startDate == ""){
	 		alert('Please enter both the study ID and the start date!');
			return false;
		}else{
			 document["form"]["isSubmitted"].value =1;
			return true;
		}
	}
//-->
</script>
</head>

<body>
  <!-- Begin Wrapper -->
   <div id="wrapper">
   
         <!-- Begin Header -->

         <div id="header">
		 	<br><br>
		<h2 align = "center">This form is used for changing the start date</h2>  
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
   <?php
   if ($idErr!="") {
   		echo $idErr."<br>";
	}
   if ($dateErr!="") {
   		echo $dateErr."<br>";
	}
	if ($updErr!="") {
   		echo $updErr."<br>";
	}
	if ($succMsg!="") {
   		echo $succMsg."<br>";
	}
	?>
	<form name = "form" method="POST" >
	<input type="hidden" name="isSubmitted" value=0 >
	<p align = "center">Please enter the study ID and the start date</p>
	<br><br>
	    <p align = "center">Study ID:<input type = "text" name="partID"  value = "<?php echo trim($_POST["partID"]);?>" size = 13></p>
	<br><br>
		<p align = "center">Start Date: <input type="test" name="startDate" value="<?php echo trim($_POST["startDate"]);?>" size = 13></p>
		<br><br><br>
	    <p align = "right"><input type = "submit" value = "save" size = 100 onClick="checkForm();"></p>
	
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
