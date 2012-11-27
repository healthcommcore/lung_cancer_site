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
<title>Quality Control Reports</title>
<link rel="stylesheet" type="text/css" href="includes/main.css" />
<script>
<!-- 
function checkForm(theForm)
{
   error = false
   errorMsg = ""
   if (theForm.startdate.value == ""){
   		error = true
   		errorMsg += "Please enter a starting date.\n"
   }
   if (theForm.enddate.value == ""){
   		error = true
   		errorMsg += "Please enter an ending date.\n"
   }
   if (error){
   		alert(errorMsg)
        return false
   } else {
   		return true
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
		<h2 align = "center">Generate quality control reports</h2>  
		<br><p align = "right">
		<form method="POST" action="login.php" >
	    <input type = "submit" value = "log out" size = 100></form></p>  
		 </div>
		 <!-- End Header -->
		 
         <!-- Begin Faux Columns -->
		 <div id="faux">
		 
		       <!-- Begin Left Column -->
		       <div id="leftcolumn"> 
<?php 
$menu = getMenu();
echo $menu;?>
		       </div>

		       <!-- End Left Column -->
		 
		       <!-- Begin Right Column -->
		       <div id="rightcolumn">
	<br><br>
	
	<?php 
		if (isset($_GET['err']) && $_GET['err'] != '') {
			echo '<p><strong><font color="#FF0000">'.$_GET['err'].'</p>';
		}
	?>
			   
    <p>To generate a list of web participants that you entered today, click the "Today's Web Users" button.</p>
	<form method="POST" action="download_qc.php"><input type="hidden" name="mode" value="webuser">
	<input type="submit" value="Today's Web Users">
	</form>
	<br>
	<p>To generate a report based on appointment date, select a date range below and click on "Generate List."</p>
<?php if (isset($_GET['msg1'])) { echo '<strong><font color="#FF0000">Please enter a start date.</font></strong><br>'; } ?>
<?php if (isset($_GET['msg2'])) { echo '<strong><font color="#FF0000">Please enter an end date.</font></strong><br>'; } ?>
<?php if (isset($_GET['msg3'])) { echo '<strong><font color="#FF0000">Please enter the start date as mm/dd/yyyy.</font></strong><br>'; } ?>
<?php if (isset($_GET['msg4'])) { echo '<strong><font color="#FF0000">Please enter the end date as mm/dd/yyyy.</font></strong><br>'; } ?>
	
<form method="POST" id="myform" action="download_qc.php" onSubmit="return checkForm(this);">
<input type="hidden" name="mode" value="blresult">
<table cellspacing="5" cellpadding="0" border="0">
<tr><td>Enter Start Date</td><td><input type="input" name="startdate" id="startdate" value="<?php if (isset($_GET['startdate'])) { echo $_GET['startdate']; } ?>"> (enter as mm/dd/yyyy)</td></tr>
<tr><td>Enter End Date</td><td><input type="input" name="enddate" id="enddate" value="<?php if (isset($_GET['enddate'])) { echo $_GET['enddate']; } ?>"> (enter as mm/dd/yyyy)</td></tr>
<tr><td colspan="2"><input type="submit" value="Generate List" size="100" onclick="return javascript:checkdates();"></td></tr>
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
