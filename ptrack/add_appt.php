<?php
// LOAD mySQL FUNCTIONS
include("includes/connection.php");
//include("includes/handler.js");
$errMsg="";

// CONNECT TO DATABASE
if ($mysqlID=dbConnect()) {
    selectDatabase("lung_cancer_user");
} else {
    $errMsg.="<b>ERROR:</b> ".mysql_error()."</b><br>\n";
}

$apptCellName = array("Recruitment Date (mm/dd/yyyy)", "Recruitment Time (hh:mm am/pm)", "Appointment Type", "Appointment Week Day", "Appointment Doctor Name", 
                  "Current PCP Name", "Study Notes");
$apptFieldName = array("apptDate", "apptTime", "apptType", "apptWDay", "provdID", "pcpID", "hvmaNotes");
$weekArray = array("Not applicable");
$apptTypeArray = array("Not applicable");

$apptFieldErr= "";
$dateErr = "";
$timeErr = "";
$zipErr ="";
$phoneErr ="";
$istErr = "";
$istFlg = "";
$apptFieldFlg =0;
$altFieldFlg =0;
$dateFlg =0;
$timeFlg =0;
$zipFlg =0;
$phoneFlg =0;
$istErr = "";
// get the MRN
if($_POST["MRN"]){
	$MRN = $_POST["MRN"];
}else{
	$MRN = $_GET['MRN']; 
}

$partID = getPartID($MRN); // Vikki added this, to add  recruitment button 
$ptInfoArray = getPartInfo($partID);

// get doctor list
$provdArray = getProvider();

// check form submission
if ($_POST["isSubmittedLogOut"]==1) {
	header("Location: login.php");
	exit();
}

// if form was submitted...
if ($_POST["isSubmittedAppt"]==1) {
    
	// put all appt field value in an array
	for($i = 0; $i <count($apptFieldName); $i++){
		$inputApptArray[$i] = $_POST[$apptFieldName[$i]];
		// if required field has not been filled, then display error msg
		if($i != 6){
			if($inputApptArray[$i] == "") {
				$apptFieldFlg = 1;
				$apptFieldErr = $apptFieldErr."<b><font color = 'red'>ERROR: Please enter ".$apptCellName[$i]."!!</font></b><br>\n";
			}
		}
		//echo "The ".$apptFieldName[$i]." is ".$_POST[$apptFieldName[$i]]."<br>\n";
	}
	
	//check to see if appt date is correct
	if ($_POST["apptDate"] != ""){
		$dateFlg = checkValidDate($_POST["apptDate"]);
		if ($dateFlg){
		    $dateErr = "<b><font color = 'red'>ERROR: ".$dateErrMsg." for Recruitment Date!!</font></b><br>\n";
		}
	}
	//check to see if appt time is correct
	if ($_POST["apptTime"] != ""){
		$timeFlg = checkValidTime($_POST["apptTime"]);
		if ($timeFlg){
		    $timeErr = "<b><font color = 'red'>ERROR: ".$timeErrMsg." for Recruitment Time!!</font></b><br>\n";
		}
	}
	

	//if all fields are correct, then update the part_info table
	if (!$apptFieldFlg && !$dateFlg && !$timeFlg && !$zipFlg && !$phoneFlg ){
		$hvmaNotes = mysql_escape_string($_POST["hvmaNotes"]);
		//format time date
		$apptDate = formatDate($_POST["apptDate"], 1);
		//format the time
		$apptTime = formatTime($_POST["apptTime"], 1);
		// insert record into the appt table
		$istFlg = insertApptInfo($MRN, $apptDate, $apptTime, $_POST["apptType"], $_POST["apptWDay"], 
		                      $_POST["provdID"], $_POST["pcpID"], $hvmaNotes);
		if($istFlg){
			$istErr="<b><font color = 'red'>ERROR:".$errIstMsg."</font><br>\n";
		}else{
			$succMsg = "<font color = 'green' size = \"+1\">The following appointment contact info has been successfully saved in the database!</font><br>\n";
		}
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//ulD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/ulD/xhtml1-strict.uld">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Add Participant</title>
<link rel="stylesheet" type="text/css" href="includes/main.css" />
	<script language="javascript">
	<!--
	// FUNCTION: SUBMIT ADD APPONITMENT INFO
	function submitAppt() {
	    document["form1"]["isSubmittedAppt"].value=1;
	    document["form1"].submit();
	}
	
	document["form1"]["isSubmittedAppt"].value=0;
	//-->
	</script>
</head>

<body>
<!-- Begin Wrapper -->
   <div id="wrapper">
   
         <!-- Begin Header -->

         <div id="header">
		 <br><br>
		<h2 align = "center"> Add Participant's Appointment Information</h2>  
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
<?php
if ($apptFieldErr!="") {
    echo $apptFieldErr."<br>";
	
}
if ($altFieldErr!="") {
    echo $altFieldErr."<br>";
	
}
if ($dateErr!="") {
    echo $dateErr."<br>";
	
}
if ($timeErr!="") {
    echo $timeErr."<br>";
	
}
if ($zipErr!="") {
    echo $zipErr."<br>";
	
}
if ($phoneErr!="") {
    echo $phoneErr."<br>";
	
}
if ($istErr!="") {
	echo $istErr ."<br>";
}

//report success msg
if ($succMsg!="") {
    echo $succMsg."<br>";
}?>
<form name = form1 method="POST" action="add_appt.php">
<input type="hidden" name="isSubmittedAppt" value="0" />
 <table border=5 valign="top" cellpadding=10 width=70%>
<?php
if($_POST["isSubmittedAppt"]!=1){
	// first print out the appointment info
	echo "<tr>\n";
	echo "<td align = \"center\" colspan = 2> <b><em>Participant Appointment Info</em></b></td>\n";
	echo "</tr>\n";
	echo "<tr>\n";
	echo "<td align = \"left\" colspan = 2> MRN <input type = \"text\"  value = \"".$MRN."\" disabled> 
	      <input type = \"hidden\" name = \"MRN\" value = \"".$MRN."\"></td>\n";
	echo "</tr>\n";
	for($a = 0; $a <count($apptCellName); $a++){
		if($a %2 == 0){
			echo "<tr>\n";
			if($apptCellName[$a] == "Appointment Type"){
				echo "<td align = \"left\">".$apptCellName[$a]." <br>\n";
				echo "<select name = \"".$apptFieldName[$a]."\">\n";
				for($c =0; $c<count($apptTypeArray); $c++){
					echo "<option value = \"".$apptTypeArray[$c]."\">&nbsp;&nbsp;&nbsp;&nbsp; ".$apptTypeArray[$c]." &nbsp;&nbsp;&nbsp;&nbsp;</option>\n";
				}
				echo "</select>\n";
				echo "</td>\n";
			}elseif($apptCellName[$a] == "Appointment Doctor Name"){
				echo "<td align = \"left\">".$apptCellName[$a]."<br>";
				echo "<select name = \"".$apptFieldName[$a]."\">\n";
				echo "<option value = \"\">  </option>\n";
				reset($provdArray);
				while(list($key,$valArray)=each($provdArray)){
					echo "<option value = \"".$key."\"> ".$valArray[0]." ".$valArray[1]." </option>\n";
				}
				echo "</select>\n";
				echo "</td>\n";
			}elseif($apptCellName[$a] == "Study Notes"){
				echo "<td align = \"left\" colspan = 2> ".$apptCellName[$a]." <textarea name = \"".$apptFieldName[$a]."\"  cols=30 rows=3 wrap> </textarea></td>";
			}else{
				echo "<td align = \"left\"> ".$apptCellName[$a]." <br><input type = \"text\" name = \"".$apptFieldName[$a]."\" value = \"\"></td>\n";
			}
		}else{
			if($apptCellName[$a] == "Appointment Week Day"){
				echo "<td align = \"left\">".$apptCellName[$a]." <br>\n";
				echo "<select name = \"".$apptFieldName[$a]."\">\n";
				for($c =0; $c<count($weekArray); $c++){
					echo "<option value = \"".$weekArray[$c]."\"> ".$weekArray[$c]." </option>\n";
				}
				echo "</select>\n";
				echo "</td>\n";
			}elseif($apptCellName[$a] == "Current PCP Name"){
				echo "<td align = \"left\">".$apptCellName[$a]." <br>\n";
				echo "<select name = \"".$apptFieldName[$a]."\">\n";
				echo "<option value = \"\">  </option>\n";
				
				echo "<option value = \"Not applicable\"> Not Applicable</option>";
				
				echo "</select>\n";
				echo "</td>\n";
			}else{
				echo "<td align = \"left\"> ".$apptCellName[$a]."<br><input type = \"text\" name = \"".$apptFieldName[$a]."\" value = \"\"></td>\n";
			}
			echo "</tr>\n";
		}
	}
	
}else{
	echo "<tr>\n";
	echo "<td align = \"center\" colspan = 2> <b><em>Participant Appointment Info</em></b></td>\n";
	echo "</tr>\n";
	echo "<tr>\n";
	echo "<td align = \"left\" colspan = 2> MRN <input type = \"text\"  value = \"".$MRN."\" disabled> 
	      <input type = \"hidden\" name = \"MRN\" value = \"".$MRN."\"></td>\n";
	echo "</tr>\n";
	for($a = 0; $a <count($apptCellName); $a++){
		if($a %2 == 0){
			echo "<tr>\n";
			if($apptCellName[$a] == "Appointment Type"){
				echo "<td align = \"left\">".$apptCellName[$a]." <br>\n";
				echo "<select name = \"".$apptFieldName[$a]."\">\n";
				for($c =0; $c<count($apptTypeArray); $c++){
					if($apptTypeArray[$c] == $inputApptArray[$a]){
						echo "<option value = \"".$apptTypeArray[$c]."\" selected>".$apptTypeArray[$c]." &nbsp;&nbsp;&nbsp;&nbsp;</option>\n";
					}else{
						echo "<option value = \"".$apptTypeArray[$c]."\">".$apptTypeArray[$c]." &nbsp;&nbsp;&nbsp;&nbsp;</option>\n";
					}
				}
				echo "</select>\n";
				echo "</td>\n";
			}elseif($apptCellName[$a] == "Appointment Doctor Name"){
				echo "<td align = \"left\">".$apptCellName[$a]."<br>\n";
				echo "<select name = \"".$apptFieldName[$a]."\">\n";
				echo "<option value = \"\">  </option>\n";
				reset($provdArray);
				while(list($key,$valArray)=each($provdArray)){
					if($key == $inputApptArray[$a]){
						echo "<option value = \"".$key."\" selected> ".$valArray[0]." ".$valArray[1]." </option>\n";
					}else{
						echo "<option value = \"".$key."\"> ".$valArray[0]." ".$valArray[1]." </option>\n";
					}
				}
				echo "</select>\n";
				echo "</td>\n";
			}elseif($apptCellName[$a] == "Study Notes"){
				$inputApptArray[$a] = ereg_replace ('[\]', '', $inputApptArray[$a]);
				echo "<td align = \"left\" colspan = 2> ".$apptCellName[$a]." <textarea name = \"".$apptFieldName[$a]."\"  cols=30 rows=3 wrap>".$inputApptArray[$a]." </textarea></td>";
			}else{
				echo "<td align = \"left\"> ".$apptCellName[$a]." <br><input type = \"text\" name = \"".$apptFieldName[$a]."\" value = \"".$inputApptArray[$a]."\"></td>\n";
			}
		}else{
			if($apptCellName[$a] == "Appointment Week Day"){
				echo "<td align = \"left\">".$apptCellName[$a]." <br>\n";
				echo "<select name = \"".$apptFieldName[$a]."\">\n";
				for($c =0; $c<count($weekArray); $c++){
					if($weekArray[$c] == $inputApptArray[$a]){
						echo "<option value = \"".$weekArray[$c]."\" selected> ".$weekArray[$c]." </option>\n";
					}else{
						echo "<option value = \"".$weekArray[$c]."\"> ".$weekArray[$c]." </option>\n";
					}
				}
				echo "</select>\n";
				echo "</td>\n";
			}elseif($apptCellName[$a] == "Current PCP Name"){
				echo "<td align = \"left\">".$apptCellName[$a]." <br>\n";
				echo "<select name = \"".$apptFieldName[$a]."\">\n";
				echo "<option value = \"Not applicable\">Not applicable </option>\n";
				
				echo "</select>\n";
				echo "</td>\n";
			}else{
				echo "<td align = \"left\"> ".$apptCellName[$a]."<br><input type = \"text\" name = \"".$apptFieldName[$a]."\" value = \"".$inputApptArray[$a]."\"></td>\n";
			}
			echo "<tr>\n";
		}
	}
	
}

echo "<tr>\n";
// VP added piece to disable add appointment button upon success
echo "<td colspan = 2 align = \"right\"><input type = \"submit\" value = \"Save Record\" onClick=\"submitAppt();\"";
if ($succMsg!="") { echo ' disabled'; }
echo " ></td>\n";
echo "</tr>\n";
?>
</table>
</form>	 
<?php if ($succMsg!="") { ?>
<div style="margin-top:24px;">
<form name=form1 method="POST" action="edit_recruitment.php?partID=<?php echo $partID; ?>">
<input type="hidden" name="partID" value="<?php echo $partID;?>">
<input type="hidden" name="ptFName" value="<?php echo $ptInfoArray[2];?>">
<input type="hidden" name="ptLName" value="<?php echo $ptInfoArray[3];?>">
<input type="submit" name="action" value="Enter a Recruitment Record">
</form>
</div>
<?php } ?>	

<?php dbClose(); ?>

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
