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

$apptCellName = array("MRN", "Recruitment Date (mm/dd/yyyy)", "Recruitment Time (hh:mm am/pm)", "Appointment Type", "Appointment Week Day", "Appointment Doctor Name", "Current PCP Name", "Study Notes");
$apptFieldName = array("MRN","apptDate", "apptTime", "apptType", "apptWDay", "provdID", "pcpID", "hvmaNotes");
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

// get the participant ID
if(isset($_GET['partID']) && $_GET['partID'] != ''){
	$partID = $_GET['partID'];
}else{
	$partID = $_POST['partID'];
}

// get the appt info from appt table
$apptArray = getApptInfo($partID);
// get the pt's name
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
		// if required field has not been filled, then display error mag
		if($i != 6){
			if($inputApptArray[$i] == "") {
				$apptFieldFlg = 1;
				$apptFieldErr = $apptFieldErr."<b><font color = 'red'>ERROR: Please enter ".$apptCellName[$i]."!!</font></b><br>\n";
			}
		}
		//echo "The ".$apptFieldName[$i]." is ".$_POST[$apptFieldName[$i]]."<br>\n";
	}
	
	// check if the MRN valid
	$mrnFlg = checkMRN($_POST["MRN"]);
	if ($mrnFlg){
	    $mrnErr = "<b><font color = 'red'>ERROR: ".$errMRNMsg."</font></b><br>\n";
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
		// insert record into the appt table if no record for appt
		if(count($apptArray)== 0){
			$istFlg = insertApptInfo($_POST["MRN"], $apptDate, $apptTime, $_POST["apptType"], $_POST["apptWDay"], 
		                     $_POST["provdID"], $_POST["pcpID"], $hvmaNotes);
		}else{
			$updFlg = updateApptInfo($_POST["MRN"], $apptDate, $apptTime, $_POST["apptType"], $_POST["apptWDay"], 
		                      $_POST["provdID"], $_POST["pcpID"], $hvmaNotes);
		}
		if($istFlg || $updFlg){
			$istErr="<b><font color = 'red'>ERROR:".$errIstMsg."</font></b><br>\n";
		}else{
			$succMsg = "<font color = 'green' size = \"+1\">The following appointment and alternate contact info has been successfully saved in the database!</font><br>\n";
		}
	}
	
}elseif ($_POST["isSubmittedRecruit"]==1) {
	header("Location: edit_recruitment.php?partID=".$partID);
	exit();
}
?>
<!DOCTYPE html PUBLIC "-//W3C//ulD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/ulD/xhtml1-strict.uld">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Edit Appointment</title>
<link rel="stylesheet" type="text/css" href="includes/main.css" />
	<script language="javascript">
	
	<!--
	// FUNCTION: SUBMIT EDIT APPONITMENT INFO
	function submitAppt() {
	    document["form1"]["isSubmittedAppt"].value=1;
		document["form1"]["isSubmittedRecruit"].value=0;
	    document["form1"].submit();
	}
	//-->
	
	// FUNCTION: SUBMIT EDIT RECRUITMENT INFO
	function submitRecruit(succ) {
		if (succ == 'N') {
		
			var cont = checkFirst();
			if (cont == true) {
			    document["form1"]["isSubmittedRecruit"].value=1;
				document["form1"]["isSubmittedAppt"].value=0;
	    		document["form1"].submit();
			} else {
				return false;
			}
		
		} else {
		    document["form1"]["isSubmittedRecruit"].value=1;
			document["form1"]["isSubmittedAppt"].value=0;
	    	document["form1"].submit();
		}
	}
	//-->
	
	<!-- 
	// FUNCTION: Asks them to confirm before continuing
	function checkFirst() {
		var cont = confirm('You have not clicked on the save button. Are you sure you want to continue?');
		if (cont == true) { 
			return true;
		} else {
			return false;
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
		<h2 align = "center"> Edit Participant's Appointment for <?php echo $ptInfoArray[2] . " ". $ptInfoArray[3]?></h2>  
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
<form name = form1 method="POST" action="edit_appt.php" >
<input type="hidden" name="isSubmittedAppt" value=0 >
<input type="hidden" name="isSubmittedRecruit" value=0 >
 <table border = 5 align = "left" valign = "top" cellpadding =10 width = 70%>
<?php
if($_POST["isSubmittedAppt"]!=1){
	
	for($a = 0; $a <count($apptCellName); $a++){
		if($a == 0){
			echo "<tr>\n";
			// if the appt info already in the database
			if(count($apptArray)>0){
				echo "<td align = \"left\"> ".$apptCellName[$a]." <input type = \"text\" name = \"".$apptFieldName[$a]."\" value = \"".$apptArray[$a]."\" disabled>
						      <input type = \"hidden\" name = \"".$apptFieldName[$a]."\" value = \"".$apptArray[$a]."\">
							  <input type = \"hidden\" name = \"partID\" value = \"".$partID."\"></td>\n";
			}else{
				echo "<td align = \"left\"> ".$apptCellName[$a]." <input type = \"text\" name = \"".$apptFieldName[$a]."\" value = \"".$ptInfoArray[1]."\" disabled>
						      <input type = \"hidden\" name = \"".$apptFieldName[$a]."\" value = \"".$ptInfoArray[1]."\">
							  <input type = \"hidden\" name = \"partID\" value = \"".$partID."\"></td>\n";
			}
			echo "</tr>\n";
		}elseif($a %2 != 0){
			echo "<tr>\n";
			if($apptCellName[$a] == "Appointment Type"){
				echo "<td align = \"left\">".$apptCellName[$a]." <br>\n";
				echo "<select name = \"".$apptFieldName[$a]."\">\n";
				for($c =0; $c<count($apptTypeArray); $c++){
					if($apptTypeArray[$c] == $apptArray[$a]){
						echo "<option value = \"".$apptTypeArray[$c]."\" selected>".$apptTypeArray[$c]." &nbsp;&nbsp;&nbsp;&nbsp;</option>\n";
					}else{
						echo "<option value = \"".$apptTypeArray[$c]."\">".$apptTypeArray[$c]." &nbsp;&nbsp;&nbsp;&nbsp;</option>\n";
					}
				}
				echo "</td>\n";
			}elseif($apptCellName[$a] == "Appointment Doctor Name"){
				echo "<td align = \"left\">".$apptCellName[$a]."<br>";
				echo "<select name = \"".$apptFieldName[$a]."\">\n";
				echo "<option value = \"\">  </option>\n";
				reset($provdArray);
				while(list($key,$valArray)=each($provdArray)){
					if($key == $apptArray[$a]){
						echo "<option value = \"".$key."\" selected> ".$valArray[0]." ".$valArray[1]." </option>\n";
					}else{
						echo "<option value = \"".$key."\"> ".$valArray[0]." ".$valArray[1]." </option>\n";
					}
				}
				echo "</td>\n";
			}elseif($apptCellName[$a] == ""){
				echo "<td align = \"left\" colspan = 2> ".$apptCellName[$a]." <textarea name = \"".$apptFieldName[$a]."\"  cols=30 rows=3 wrap> ".str_replace('\\', '', $apptArray[$a])."</textarea></td>";
			}else{
				echo "<td align = \"left\" > ".$apptCellName[$a]." <br><input type = \"text\" name = \"".$apptFieldName[$a]."\" value = \"".$apptArray[$a]."\"></td>\n";
			}
		}else{
			if($apptCellName[$a] == "Appointment Week Day"){
				echo "<td align = \"left\">".$apptCellName[$a]." <br>\n";
				echo "<select name = \"".$apptFieldName[$a]."\">\n";
				for($c =0; $c<count($weekArray); $c++){
					if($weekArray[$c] == makeStrUc($apptArray[$a])){
						echo "<option value = \"".$weekArray[$c]."\" selected> ".$weekArray[$c]." </option>\n";
					}else{
						echo "<option value = \"".$weekArray[$c]."\"> ".$weekArray[$c]." </option>\n";
					}
				}
				echo "</td>\n";
			}elseif($apptCellName[$a] == "Current PCP Name"){
				echo "<td align = \"left\">".$apptCellName[$a]."<br>\n";
				echo "<select name = \"".$apptFieldName[$a]."\">\n";
				echo "<option value = \"Not applicable\">Not applicable</option>\n";
				// Vikki updated this code to use foreach 
								
				echo "</select>";
				echo "</td>\n";
			// end of code block 
			}else{
				echo "<td align = \"left\"> ".$apptCellName[$a]."<br><input type = \"text\" name = \"".$apptFieldName[$a]."\" value = \"".$apptArray[$a]."\"></td>\n";
			}
			echo "</tr>\n";
		}
	}
	
}else{
	for($a = 0; $a <count($apptCellName); $a++){
		if($a == 0){
			echo "<tr>\n";
			echo "<td align = \"left\"> ".$apptCellName[$a]." <input type = \"text\" name = \"".$apptFieldName[$a]."\" value = \"".$inputApptArray[$a]."\" disabled>
					      <input type = \"hidden\" name = \"".$apptFieldName[$a]."\" value = \"".$inputApptArray[$a]."\">
						  <input type = \"hidden\" name = \"partID\" value = \"".$partID."\"></td>\n";
			echo "</tr>\n";
		}elseif($a %2 != 0){
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
				echo "</td>\n";
			}elseif($apptCellName[$a] == "Study Notes"){
				echo "<td align = \"left\" colspan = 2> ".$apptCellName[$a]." <textarea name=\"".$apptFieldName[$a]."\"  cols=30 rows=3 wrap>". str_replace('\\', '', $inputApptArray[$a])." </textarea></td>";
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
				echo "</td>\n";
			}elseif($apptCellName[$a] == "Current PCP Name"){
				echo "<td align = \"left\">".$apptCellName[$a]." <br>\n";
				echo "<select name = \"".$apptFieldName[$a]."\">\n";
				echo "<option value = \"Not applicable\"> Not Applicable</option>";
				echo "</select>";
				echo "</td>\n";
			}else{
				echo "<td align = \"left\"> ".$apptCellName[$a]."<br><input type = \"text\" name = \"".$apptFieldName[$a]."\" value = \"".$inputApptArray[$a]."\"></td>\n";
			}
			echo "<tr>\n";
		}
	}
	
}

echo "<tr>\n";
echo "<td><input type = \"submit\" value = \"Save Record\" onClick=\"submitAppt();\" ></td>\n";
echo "<td><input type = \"submit\" value = \"Edit Recruitment\" onClick=\"submitRecruit(";
if (!$succMsg || $succMsg == '') { echo "'N'";} else { echo "'Y'";}
echo ");\" >\n";
echo "</tr>\n";
?>
</table>
</form>	 
<?php
dbClose(); ?>

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
