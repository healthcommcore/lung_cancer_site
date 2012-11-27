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

$fieldName = array("partID", "dateSurgeonNotify", "surgeonOptOut", "dateRecrutLttr", "dateReceived", "ptOptOut", "giveClipbd", 
                  "blResult", "raID1", "raID2");
$cellName = array("Study ID", "Date Surgeon Notified","Surgeon Opt-Out", "Date Recruitment Letter Sent<br>(mm/dd/yyyy)", "Date Recruitment Letter Returned<br>(mm/dd/yyyy)", "Patient Opt-Out", "Completed baseline survey", "Baseline Result", "Enrolling staff", "Baseline RA2");
$weekArray = array("","Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday");
$optArray = array("", "Opt-out");
$booleanArray = array("", "Yes", "No");
$apptFieldErr= "";
$dateErr = "";
$istErr = "";
$istFlg = "";
$apptFieldFlg =0;
$altFieldFlg =0;
$dateFlg =0;

// get the participant ID
if($_GET['partID']){
	$partID = $_GET['partID'];
}else{
	$partID = $_POST["partID"];
}

// get the recruitment info 
$recruitArray = getRecruitInfo($partID);
// get the pt's name
$ptInfoArray = getPartInfo($partID);
// get baseline result
$resultArray = getBlResult();
// get the appt info from appt table
$apptArray = getApptInfo($partID);
// get RA names
$raArray = getRaNames("RA");
// get random arm
$ranArm = getRandom($partID);
// Vikki added - get enrollment record 
$enrArray = getEnrollment($partID);

// check form submission
if ($_POST["isSubmittedLogOut"]==1) {
	header("Location: login.php");
	exit();
}

// if no appt info has beeen entered, then display error msg and exit
if(count($apptArray)<=0){
	$pageError = "<b><font color = 'red'>ERROR: This participant does not have any appointment information! <br>Therefore, you can not enter recruitment information!!</font></b><br>\n";
}

// if form was submitted...
if ($_POST["isSubmittedRecruit"]==1) {
    
	// put all appt field value in an array
	for($i = 0; $i <count($fieldName); $i++){
		$inputRecruitArray[$i] = $_POST[$fieldName[$i]];
		//echo "The field is ".$fieldName[$i]." and the value is ".$_POST[$fieldName[$i]]."<br>\n";
	}
	
	//check to see if Date Surgeon Notified is correct
	if ($_POST["dateSurgeonNotify"] != ""){
		$dateFlg = checkValidDate($_POST["dateSurgeonNotify"]);
		if ($dateFlg){
		    $dateErr = "<b><font color = 'red'>ERROR: ".$dateErrMsg." for Date Surgeon Notified!!</font></b><br>\n";
		}
	}
	
	//check to see if date Recruitment send is correct
	if ($_POST["dateRecrutLttr"] != ""){
		$lttrDateFlg = checkValidDate($_POST["dateRecrutLttr"]);
		if ($lttrDarteFlg){
		    $lttrDateErr = "<b><font color = 'red'>ERROR: ".$dateErrMsg." for date recruitment letter sent!!</font></b><br>\n";
		}
	}
	
	//check to see if date Recruitment letter received is correct
	if ($_POST["dateReceived"] != ""){
		$lttrRecievFlg = checkValidDate($_POST["dateReceived"]);
		if ($lttrRecievFlg){
		    $lttrRecievErr = "<b><font color = 'red'>ERROR: ".$dateErrMsg." for date recruitment letter returned!!</font></b><br>\n";
		}else{
			// compare the letter send date and return date
			if($_POST["dateReceived"]){
				$lttrRecievFlg = compareDates($_POST["dateRecrutLttr"], $_POST["dateReceived"]);
				if ($lttrRecievFlg){
				    $lttrRecievErr = "<b><font color = 'red'>ERROR: The recruitment letter return date is before the recruitment letter send date!!</font></b><br>\n";
				}
			}
		}
	}
	// Vikki updated this to check for opt-out
	if(!$_POST["giveClipbd"] && $_POST["ptOptOut"] != $optArray[1] && $_POST["surgeonOptOut"] != $optArray[1]){
		$clipFlg = 1;
		$clipErr = "<b><font color = 'red'>ERROR: Please enter completed baseline survey information!!</font></b><br>\n";
	}
	// check if the bsaeline RA field have been entered
	// Vikki updated this to check for opt-out
	if ( ($_POST["raID1"] == "" || $_POST["raID2"] == "") && $_POST["ptOptOut"] != $optArray[1] && $_POST["surgeonOptOut"] != $optArray[1] ){
		$raFlg = 1;
		$raErr = "<b><font color = 'red'>ERROR: Please enter both Enrolling staff and RA2 fields!!</font></b><br>\n";
	}
	// Vikki updated this to check for opt-out
	if($_POST["blResult"] ==  0 && $_POST["ptOptOut"] != $optArray[1] && $_POST["surgeonOptOut"] != $optArray[1]){
		$blFlg = 1;
		$blErr = "<b><font color = 'red'>ERROR: Please enter the baseline result!!</font></b><br>\n";
	}
	
	//if all fields are correct, then update the recruitment table
	if (!$dateFlg && !$lttrDarteFlg && !$lttrRecievFlg && !$raFlg && !$clipFlg && !$blFlg){
		//format Date Surgeon Notified date
		$dateSurgeonNotify = formatDate($_POST["dateSurgeonNotify"], 1);
		//format the date recruitment letter send
		$dateRecrutLttr = formatDate($_POST["dateRecrutLttr"], 1);
		//format the date recruitment letter returned
		$dateReceived = formatDate($_POST["dateReceived"], 1);
		// insert record into the appt table if no record for appt
		if( count($recruitArray) < 1 ){
			$istFlg = insertRecuitInfo($partID, $dateSurgeonNotify, $_POST["surgeonOptOut"], $dateRecrutLttr, $dateReceived, 
			                        $_POST["ptOptOut"], $_POST["giveClipbd"], $_POST["blResult"], 
									$_POST["raID1"], $_POST["raID2"]);
			// check to see if this appt provider is uc, if so, add enrollment record with appt date as start date 
			
		} else {
			$updFlg = updateRecruitInfo($partID, $dateSurgeonNotify, $_POST["surgeonOptOut"], $dateRecrutLttr, $dateReceived, 
			                        $_POST["ptOptOut"], $_POST["giveClipbd"], $_POST["blResult"], 
									$_POST["raID1"], $_POST["raID2"]);
		}
		if($istFlg == 1 || $updFlg == 1){
			$istErr="<b><font color = 'red'>ERROR:".$errIstRecuMsg."</font></b><br>\n";
		}else{
			// if this pt is in control group, and no enrollment record exists, insert start date into the enrollment table
			if($ranArm == "uc" && $_POST["blResult"] == '1' && count($enrArray) < 1 ) {
				// there isn't an enrollment record, insert one 
 				$apptDate = formatDate($apptArray[1], 1);
				$istSTFlg = insertStartDate($partID, $apptDate); // throws an error duplicate entry... 
				if($istSTFlg){
					$istSTErr="<b><font color = 'red'>ERROR:".$errIstSTMsg."</font></b><br>\n";
				}
			}
			// change the pt status if pcp or pt opt out or baseline result is not "consent"
			if($_POST["surgeonOptOut"] == "Opt-out" ||  $_POST["ptOptOut"] == "Opt-out" || $_POST["blResult"] != 1){
				$istFlg = updateSatus($partID, 'I');
				if($istFlg){
					$istErr="<b><font color = 'red'>ERROR:".$errIstMsg."</font></b><br>\n";
				}
			}else{
				$istFlg = updateSatus($partID, 'A');
				if($istFlg){
					$istErr="<b><font color = 'red'>ERROR:".$errIstMsg."</font></b><br>\n";
				}
			}
		}
		if(!$istFlg && !$updFlg && !$istSTFlg){
			$succMsg = "<font color = 'green' size = \"+1\">The following recruitment info has been successfully saved in the database!<br>
			            If this participant consented to the study, please don't forget to enter the enrollment information!</font><br>\n";
		}
	}
	
}elseif ($_POST["isSubmittedContact"]==1) {
	header("Location: edit_contact.php?partID=".$partID);
	exit();
}
?>
<!DOCTYPE html PUBLIC "-//W3C//ulD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/ulD/xhtml1-strict.uld">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Edit Recruitment</title>
<link rel="stylesheet" type="text/css" href="includes/main.css" />
	<script language="javascript">
	<!--
	
	// FUNCTION: SUBMIT EDIT RECRUITMENT INFO
	function submitRecruit() {
		    document["form1"]["isSubmittedRecruit"].value=1;
			document["form1"]["isSubmittedContact"].value=0;
	    	document["form1"].submit();
	}
	//-->
	
	// FUNCTION: SUBMIT EDIT ENROLLMENT INFO
	function submitContact() {
	    document["form1"]["isSubmittedContact"].value=1;
		 document["form1"]["isSubmittedRecruit"].value=0;
	    document["form1"].submit();
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
		<h2 align = "center"> Edit Recruitment Info for <?php echo $ptInfoArray[2] . " ". $ptInfoArray[3];?></h2>  
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
if($pageError){
	echo $pageError."<br>";
	exit();
}
if ($dateErr!="") {
    echo $dateErr."<br>";
}
if ($lttrDateErr!="") {
    echo $lttrDateErr."<br>";
}
if ($lttrRecievErr!="") {
    echo $lttrRecievErr."<br>";
}
if($raErr){
	echo $raErr."<br>";
}
if($clipErr){
	echo $clipErr."<br>";
}
if($blErr){
	echo $blErr."<br>";
}
if ($istErr!="") {
	echo $istErr ."<br>";
}
if ($istSTErr != ""){
	echo $istSTErr;
}
//report success msg
if ($succMsg!="") {
    echo $succMsg."<br>";
}?>
<form name = form1 method="POST" action="edit_recruitment.php" >
<input type="hidden" name="isSubmittedRecruit" value=0 >
<input type="hidden" name="isSubmittedContact" value=0 >
 <table border = 5 align = "left" valign = "top" cellpadding =10>
<?php
if($_POST["isSubmittedRecruit"]!=1){
	for($a = 0; $a <count($cellName); $a++){
		if($a == 0){
			echo "<tr>\n";
			echo "<td align = \"left\"> ".$cellName[$a]." <input type = \"text\" name = \"".$fieldName[$a]."\" value = \"".$partID."\" disabled>
					      <input type = \"hidden\" name = \"".$fieldName[$a]."\" value = \"".$partID."\"></td>\n";
			echo "</tr>\n";
		}elseif($a %2 != 0){
			echo "<tr>\n";
			if($cellName[$a] == "Patient Opt-Out"){
				echo "<td align = \"left\">".$cellName[$a]." <br>\n";
				echo "<select name = \"".$fieldName[$a]."\">\n";
				for($c =0; $c<count($optArray); $c++){
					if($optArray[$c] == $recruitArray[$a]){
						echo "<option value = \"".$optArray[$c]."\" selected>".$optArray[$c]." &nbsp;&nbsp;&nbsp;&nbsp;</option>\n";
					}else{
						echo "<option value = \"".$optArray[$c]."\">".$optArray[$c]." &nbsp;&nbsp;&nbsp;&nbsp;</option>\n";
					}
				}
				echo "</td>\n";
			}elseif($cellName[$a] == "Baseline Result"){
				echo "<td align = \"left\">".$cellName[$a]." <br>\n";
				echo "<select name = \"".$fieldName[$a]."\">\n";
				for($c =0; $c<count($resultArray); $c++){
					if($c == $recruitArray[$a]){
						echo "<option value = \"".$c."\" selected>".$resultArray[$c]." </option>\n";
					}else{
						echo "<option value = \"".$c."\">".$resultArray[$c]." </option>\n";
					}
				}
				echo "</td>\n";
			}elseif($cellName[$a] == "Baseline RA2"){
				echo "<td align = \"left\">".$cellName[$a]."<br>";
				echo "<select name = \"".$fieldName[$a]."\">\n";
				echo "<option value = \"Not applicable\">Not applicable</option>\n";
				echo "</select>";
				echo "</td>\n";
			}else{
				echo "<td align = \"left\"> ".$cellName[$a]." <br><input type = \"text\" name = \"".$fieldName[$a]."\" value = \"".$recruitArray[$a]."\"></td>\n";
			}
		}else{
			if($cellName[$a] == "Surgeon Opt-Out"){
				echo "<td align = \"left\">".$cellName[$a]." <br>\n";
				echo "<select name = \"".$fieldName[$a]."\">\n";
				for($c =0; $c<count($optArray); $c++){
					if($optArray[$c] == $recruitArray[$a]){
						echo "<option value = \"".$optArray[$c]."\" selected>".$optArray[$c]." &nbsp;&nbsp;&nbsp;&nbsp;</option>\n";
					}else{
						echo "<option value = \"".$optArray[$c]."\">".$optArray[$c]." &nbsp;&nbsp;&nbsp;&nbsp;</option>\n";
					}
				}
				echo "</td>\n";
			}elseif($cellName[$a] == "completed baseline survey"){
				echo "<td align = \"left\">".$cellName[$a]." <br>\n";
				echo "<select name = \"".$fieldName[$a]."\">\n";
				for($c =0; $c<count($booleanArray); $c++){
					if($booleanArray[$c] == $recruitArray[$a]){
						echo "<option value = \"".$booleanArray[$c]."\" selected>".$booleanArray[$c]." &nbsp;&nbsp;&nbsp;&nbsp;</option>\n";
					}else{
						echo "<option value = \"".$booleanArray[$c]."\">".$booleanArray[$c]." &nbsp;&nbsp;&nbsp;&nbsp;</option>\n";
					}
				}
				echo "</td>\n";
			}elseif($cellName[$a] == "Enrolling staff"){
				echo "<td align = \"left\">".$cellName[$a]."<br>";
				echo "<select name = \"".$fieldName[$a]."\">\n";
				echo "<option value = \"\">  </option>\n";
				reset($raArray);
				while(list($key,$valArray)=each($raArray)){
					if($key == $recruitArray[$a]){
						echo "<option value = \"".$key."\" selected> ".$valArray[0]." ".$valArray[1]." </option>\n";
					}else{
						echo "<option value = \"".$key."\"> ".$valArray[0]." ".$valArray[1]." </option>\n";
					}
				}
				echo "</td>\n";
			}else{
				echo "<td align = \"left\"> ".$cellName[$a]."<br><input type = \"text\" name = \"".$fieldName[$a]."\" value = \"".$recruitArray[$a]."\"></td>\n";
			}
			echo "</tr>\n";
		}
	}
	
}elseif($_POST["isSubmittedRecruit"]==1){
	for($a = 0; $a <count($cellName); $a++){
		if($a == 0){
			echo "<tr>\n";
			echo "<td align = \"left\"> ".$cellName[$a]." <input type = \"text\" name = \"".$fieldName[$a]."\" value = \"".$inputRecruitArray[$a]."\" disabled>
					      <input type = \"hidden\" name = \"".$fieldName[$a]."\" value = \"".$inputRecruitArray[$a]."\"></td>\n";
			echo "</tr>\n";
		}elseif($a %2 != 0){
			echo "<tr>\n";
			if($cellName[$a] == "Patient Opt-Out"){
				echo "<td align = \"left\">".$cellName[$a]." <br>\n";
				echo "<select name = \"".$fieldName[$a]."\">\n";
				for($c =0; $c<count($optArray); $c++){
					if($optArray[$c] == $inputRecruitArray[$a]){
						echo "<option value = \"".$optArray[$c]."\" selected>".$optArray[$c]." &nbsp;&nbsp;&nbsp;&nbsp;</option>\n";
					}else{
						echo "<option value = \"".$optArray[$c]."\">".$optArray[$c]." &nbsp;&nbsp;&nbsp;&nbsp;</option>\n";
					}
				}
				echo "</td>\n";
			}elseif($cellName[$a] == "Baseline Result"){
				echo "<td align = \"left\">".$cellName[$a]." <br>\n";
				echo "<select name = \"".$fieldName[$a]."\">\n";
				for($c =0; $c<count($resultArray); $c++){
					if($c == $inputRecruitArray[$a]){
						echo "<option value = \"".$c."\" selected> ".$resultArray[$c]." </option>\n";
					}else{
						echo "<option value = \"".$c."\"> ".$resultArray[$c]." </option>\n";
					}
				}
				echo "</td>\n";
			}elseif($cellName[$a] == "Baseline RA2"){
				echo "<td align = \"left\">".$cellName[$a]."<br>";
				echo "<select name = \"".$fieldName[$a]."\">\n";
				echo "<option value = \"\">  </option>\n";
				reset($raArray);
				while(list($key,$valArray)=each($raArray)){
					if($key == $inputRecruitArray[$a]){
						echo "<option value = \"".$key."\" selected> ".$valArray[0]." ".$valArray[1]." </option>\n";
					}else{
						echo "<option value = \"".$key."\"> ".$valArray[0]." ".$valArray[1]." </option>\n";
					}
				}
				echo "</td>\n";
			}else{
				echo "<td align = \"left\"> ".$cellName[$a]." <br><input type = \"text\" name = \"".$fieldName[$a]."\" value = \"".$inputRecruitArray[$a]."\"></td>\n";
			}
		}else{
			if($cellName[$a] == "Surgeon Opt-Out"){
				echo "<td align = \"left\">".$cellName[$a]." <br>\n";
				echo "<select name = \"".$fieldName[$a]."\">\n";
				for($c =0; $c<count($optArray); $c++){
					if($optArray[$c] == $inputRecruitArray[$a]){
						echo "<option value = \"".$optArray[$c]."\" selected>".$optArray[$c]." &nbsp;&nbsp;&nbsp;&nbsp;</option>\n";
					}else{
						echo "<option value = \"".$optArray[$c]."\">".$optArray[$c]." &nbsp;&nbsp;&nbsp;&nbsp;</option>\n";
					}
				}
				echo "</td>\n";
			}elseif($cellName[$a] == "completed baseline survey"){
				echo "<td align = \"left\">".$cellName[$a]." <br>\n";
				echo "<select name = \"".$fieldName[$a]."\">\n";
				for($c =0; $c<count($booleanArray); $c++){
					if($booleanArray[$c] == $inputRecruitArray[$a]){
						echo "<option value = \"".$booleanArray[$c]."\" selected>".$booleanArray[$c]." &nbsp;&nbsp;&nbsp;&nbsp;</option>\n";
					}else{
						echo "<option value = \"".$booleanArray[$c]."\">".$booleanArray[$c]." &nbsp;&nbsp;&nbsp;&nbsp;</option>\n";
					}
				}
				echo "</td>\n";
			}elseif($cellName[$a] == "Enrolling staff"){
				echo "<td align = \"left\">".$cellName[$a]."<br>";
				echo "<select name = \"".$fieldName[$a]."\">\n";
				echo "<option value = \"\">  </option>\n";
				reset($raArray);
				while(list($key,$valArray)=each($raArray)){
					if($key == $inputRecruitArray[$a]){
						echo "<option value = \"".$key."\" selected> ".$valArray[0]." ".$valArray[1]." </option>\n";
					}else{
						echo "<option value = \"".$key."\"> ".$valArray[0]." ".$valArray[1]." </option>\n";
					}
				}
				echo "</td>\n";
			}else{
				echo "<td align = \"left\"> ".$cellName[$a]."<br><input type = \"text\" name = \"".$fieldName[$a]."\" value = \"".$inputRecruitArray[$a]."\"></td>\n";
			}
			echo "</tr>\n";
		}
	}
	
}
// if the baseline result is not consented or random arm is uc, then disabled the enrollment button
if($_POST["isSubmittedRecruit"]==1){
	if($inputRecruitArray[7] != 1){
		$enrollButton = 'disabled';
	}
}else{
	if($recruitArray[7] != 1){
		$enrollButton = 'disabled';
	}
}

echo "<tr>\n";
echo "<td><input type = \"submit\" value = \"Save Record\" onClick=\"submitRecruit();\" ></td>\n";
echo "<td><input type = \"submit\" value = \"Edit Alternative Contact\" onClick=\"submitContact();\" ".$enrollButton.">\n";
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
