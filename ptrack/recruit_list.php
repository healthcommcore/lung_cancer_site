<?php
include("includes/connection.php");
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
$departArray = array("","WRXIMA", "WRXIMB", "KENIM4", "KENIM6");
if($_POST["isSubmittedRecruit"]==1 ){
	// first check if the dept info has been entered
	if ($_POST["dept"] == ""){
		$deptFlg =1;
		$deptErr = "<b><font color = 'red'>ERROR: You need to select a department!!</font></b><br>\n";
	}
	// then check the start date and end date
	if ($_POST["startDate"] == "" || $_POST["endDate"] == ""){
		$dateFlg =1;
		$dateErr = "<b><font color = 'red'>ERROR: You need to put in the start date and end date!!</font></b><br>\n";
	}else{
		$dateFlg = checkValidDate($_POST["startDate"]);
		if ($dateFlg){
		    $dateErr = "<b><font color = 'red'>ERROR: ".$dateErrMsg." for start date!!</font></b><br>\n";
		}else{
			$dateFlg = checkValidDate($_POST["endDate"]);
			if ($dateFlg){
			    $dateErr = "<b><font color = 'red'>ERROR: ".$dateErrMsg." for end date!!</font></b><br>\n";
			}
		}
	}
	
	//check the start time and end time is correct
	if ($_POST["startTime"] == "" || $_POST["endTime"] == ""){
		$timeFlg =1;
		$timeErr = "<b><font color = 'red'>ERROR: You need to put in the start time and end time!!</font></b><br>\n";
	}else{
		$timeFlg = checkValidTime($_POST["startTime"]);
		if ($timeFlg){
		    $timeErr = "<b><font color = 'red'>ERROR: ".$timeErrMsg." for start time!!</font></b><br>\n";
		}else{
			$timeFlg = checkValidTime($_POST["endTime"]);
			if ($timeFlg){
			    $timeErr = "<b><font color = 'red'>ERROR: ".$timeErrMsg." for end time!!</font></b><br>\n";
			}
		}
	}
	
	if(!$deptFlg && !$dateFlg && !$timeFlg){
		$startDate = $_POST["startDate"];
		$endDate = $_POST["endDate"];
		$startTime = $_POST["startTime"];
		$endTime = $_POST["endTime"];
		$dept = $_POST["dept"];
		
		$url = "Location: download_recruit_letter.php?dept=".$dept."&startDate=".$startDate."&endDate=".$endDate."&startTime=".$startTime."&endTime=".$endTime;
		header($url);
		//echo $url;
		exit();
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Download PCP Review List</title>
<link rel="stylesheet" type="text/css" href="includes/main.css" />
<script language="javascript">
	
	<!--
	// FUNCTION: SUBMIT DOWNLOAD ADMIN INFO REPORT
	function submitRecruit() {
	     document["form1"]["isSubmittedRecruit"].value=1;
		 //alert("submit");
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
		<h2 align = "center"> Download Recruitment Letter List</h2>  
		<br><p align = "right"><form method="POST" action="login.php" >
	     <input type = "submit" value = "log out" size = 100></form></p>  
		 </div>
		 <!-- End Header -->
		 
         <!-- Begin Faux Columns -->
		 <div id="faux">
		 
		       <!-- Begin Left Column -->
		       <div id="leftcolumn"> 
		 		<?php
						$menu = getMenu();
						echo $menu;
					?>
		       </div>

		       <!-- End Left Column -->
		 
		       <!-- Begin Right Column -->
		       <div id="rightcolumn">
	<?php 
	if($deptErr != ""){
		echo $deptErr;
	}
	if ($dateErr!="") {
	    echo $dateErr;
		
	}
	if ($timeErr!="") {
	    echo $timeErr;
		
	}
	?>
	<form name = form1 method="POST" action="recruit_list.php">
	<input type="hidden" name="isSubmittedRecruit" value=0 >
   <table border = 1 align = "left" valign = "top" cellpadding =4> 
	<?php
	if($_POST["isSubmittedRecruit"]!=1 ){
	?>
	<tr>
	<td colspan = 2>Choose Department <br>
	<select name ="dept">
		<?php for($c =0; $c<count($departArray); $c++){
					echo "<option value = \"".$departArray[$c]."\"> ".$departArray[$c]." </option>\n";
			}
			?>
	</select>
	</td>
	</tr>
	<tr>
	<td> Enter Appointment Start Date <br>(mm/dd/yyyy) <br><input type = "text" name = "startDate" value = ""></td>
	<td> Enter Appointment End Date <br>(mm/dd/yyyy) <br><input type = "text" name = "endDate" value = ""></td>
	</tr>
	<tr>
	<td> Enter Appointment Start Time <br>(hh:mm am/pm) <br><input type = "text" name = "startTime" value = ""></td>
	<td> Enter Appointment End Time <br>(hh:mm am/pm) <br><input type = "text" name = "endTime" value = ""></td>
	</tr>
	<tr>
	<td colspan =2 align = "center"><input type = "submit" value = "download participant list for recruitment letter" onClick="submitRecruit()";></td>
	</tr>
	
	<?php }else{
	?>
	<tr>
	<td colspan = 2>Choose Department <br>
	<select name ="dept">
		<?php for($c =0; $c<count($departArray); $c++){
					if($departArray[$c] == $_POST["dept"]){
						echo "<option value = \"".$departArray[$c]."\" selected> ".$departArray[$c]." </option>\n";
					}else{
						echo "<option value = \"".$departArray[$c]."\"> ".$departArray[$c]." </option>\n";
					}
			}
			?>
	</select>
	</td>
	</tr>
	<tr>
	<td> Enter Appointment Start Date <br>(mm/dd/yyyy) <br><input type = "text" name = "startDate" value = "<?php echo $_POST["startDate"];?>"></td>
	<td> Enter Appointment End Date <br>(mm/dd/yyyy) <br><input type = "text" name = "endDate" value = "<?php echo $_POST["endDate"];?>""></td>
	</tr>
	<tr>
	<td> Enter Appointment Start Time <br>(hh:mm am/pm) <br><input type = "text" name = "startTime" value = "<?php echo $_POST["startTime"];?>"></td>
	<td> Enter Appointment End Time <br>(hh:mm am/pm) <br><input type = "text" name = "endTime" value = "<?php echo $_POST["endTime"];?>"></td>
	</tr>
	<tr>
	<td colspan =2 align = "center"><input type = "submit" value = "download participant list for recruitment letter" onClick="submitRecruit()";></td>
	</tr>
	<?php }?> 
	</table>
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
