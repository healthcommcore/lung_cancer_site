<?php
// LOAD mySQL FUNCTIONS
include("includes/connection.php");
include("includes/addwebuser.php");
$errMsg="";

// CONNECT TO DATABASE
if ($mysqlID=dbConnect()) {
    selectDatabase("lung_cancer_user");
} else {
    $errMsg.="<b>ERROR: ".mysql_error()."</b><br>\n";
}
define('SMOKER',21);
define('NUM_SMOKED', 22);
$fieldName = array("partID", "ixModality",  "dateIXChange", "reasonIX", "webPwd", "reenterPwd", "remindRand", 
                  "reminModality", "dateRemChang", "reasonRemind", "ptRPhone", "dateRemOpt", "reasonRemOpt");
$cellName = array("Study ID", "Ix Modality", "Date Switched to Print", "Reason Switch to Print", 
                  "Web Password", "Re-enter Password", "Reminder Randomization", "Reminder Modality",
                   "Date Reminder Modality Changed", "Reason Reminder Modality Changed", "Preferred Phone for Reminder\n(unnecessary)", 
				   "Date Reminder Opt-out (unnecessary)", "Reason Reminder Opt-out");
$timeArray = array("","8am-10am", "10am-12pm", "12pm-2pm", "12-2pm", "2pm-4pm", "4pm-6pm", "6pm-8pm");
$ixModArray = array("", "Web", "Print");
$remModArray = array("", "Voice", "Text");
$booleanArray = array("", "Yes", "No");
$ixDateFlg = "";
$$ixDateErr = "";
$timeErr = "";
$istErr = "";
$smokingErr = array("", "", "");
$istFlg = "";
$apptFieldFlg =0;
$altFieldFlg =0;
$dateFlg =0;
$timeFlg =0;

// get the participant ID
if($_GET['partID']){
	$partID = $_GET['partID'];
}else{
	$partID = $_POST["partID"];
}
// get heID if exist
if($_GET['heID']) $heID = $_GET['heID'];
// get the pt's name
$ptInfoArray = getPartInfo($partID);
$apptArray = getApptInfo($partID); // Vikki added grabbing apptInfo, so StartDate can be entered on page one 
// get enrollment info
$enrollInfoArray = getEnrollment($partID);
// find out if the pt is currently print
if($_POST["isSubmittedEmroll1"]!=1){
	if($enrollInfoArray[1] == 2) $ixFlg = 1;
}
// get the appt info from appt table
$recruitArray = getRecruitInfo($partID);
// get random arm
$ranArm = getRandom($partID); 
// if(!$ranArm) $randError = 1;
// if baseline result is not consented, then display error msg; removed  || $ranArm == "uc", they can enter alt contact for uc group 
// also removed  || $randError for the same reason. 
if($recruitArray[7] != 1){
	$pageError = "<b><font color = 'red'>ERROR: This participant did not consent to the study! <br>Therefore, you should not enter enrollment information!!</font></b><br>\n";
}else{
	// get ix modality change reason
	$ixChangeArray = getReason("ix_change_reason", "ixID", "ixReason");
	// get reminder change reason
	$remdChangeArray = getReason("remind_change_reason", "remindID", "remindReason");
	// get reminder opt-out reason
	$remdOptArray = getReason("remind_opt_reason", "remindOptID", "remindOptReason");
	// get packet send reason
	//$packetArray = getReason("packet_reason", "packetID", "packetReason");
	// get ped mailed reason
	//$pedArray = getReason("ped_reason", "pedID", "pedReason");
	// get withdrew reason
	//$withDArray = getReason("withD_reason", "withdID", "withDReason");
	// get HE names
	//$raArray = getRaNames("HE");
	// put all phone number in a array
	$phoneArray = array("", "H-".$ptInfoArray[11], "W-".$ptInfoArray[12], "C-".$ptInfoArray[13], "O-".$ptInfoArray[14]);
}

// check form submission
if ($_POST["isSubmittedLogOut"]==1) {
	header("Location: login.php");
	exit();
}
// if form was submitted...
if ($_POST["isSubmittedEnroll1"]==1) {
//	echo "smoked: " . $_POST["smoked"] . "<br />";
	// put all enrollment field value in an array
	for($i = 0; $i <count($fieldName); $i++){
		$inputEnrollArray[$i] = $_POST[$fieldName[$i]];
		//echo $_POST[$fieldName[$i]] . "<br />";
		//echo $inputEnrollArray[$i] . "&nbsp;&nbsp;-&nbsp;&nbsp;" . $_POST[$fieldName[$i]] . "<br />";
		
		if($i == 1 || $i == 8){
			if($inputEnrollArray[$i] == 'Not applicable')
				break;
			else{
				if($inputEnrollArray[$i] == 0){
					$fieldFlg = 1;
					$fieldErr = "<b><font color = 'red'>ERROR: Please enter ".$cellName[$i]."!!</font></b><br>\n";
				}
			}
		}
		//echo "The field is ".$fieldName[$i]." and the value is ".$_POST[$fieldName[$i]]."<br>\n";
	}
	$inputEnrollArray[SMOKER] = $_POST['smoked'];
	$inputEnrollArray[NUM_SMOKED] = $_POST['numCigs'];
	// check to see if the input value is print and if the ix Modality has been changed
	if($enrollInfoArray[1] == 2){
		$ixFlg = 1;
		$changeFlg = 0;
	}elseif($enrollInfoArray[1] == 1){
		if($inputEnrollArray[1] == 2){
			$ixFlg = 1;
			$changeFlg = 1;
		}else{
			$ixFlg = 0;
			$changeFlg = 0;
		}
	}else{
		if($inputEnrollArray[1] == 2){
			$ixFlg = 1;
			$changeFlg = 0;
		}else{
			$ixFlg = 0;
			$changeFlg = 0;
		}
	}
	
	//check the date switch to print field is correct if ix modality is web
	if($changeFlg == 1){
		if ($_POST["dateIXChange"] != ""){
			$ixDateFlg = checkValidDate($_POST["dateIXChange"]);
			if ($ixDateFlg){
			    $ixDateErr = "<b><font color = 'red'>ERROR: ".$dateErrMsg." for date switched to print!!</font></b><br>\n";
			}else{
				// the switch date can not be in the futture
				$today = date("m/d/Y");
				$switchDate = formateToDate($_POST["dateIXChange"]);
				$ixDateFlg = compareDates($switchDate, $today);
				if ($ixDateFlg){
				    $ixDateErr = "<b><font color = 'red'>ERROR: Date switched to print can not be in the future!!</font></b><br>\n";
				}else{
					// check if the reason of switch has been entered
					if(!$_POST["reasonIX"]){
						$ixDateFlg = 1;
						$ixDateErr = "<b><font color = 'red'>ERROR: Please enter the reason switched to print!!</font></b><br>\n";
					}
				}
			}
		}else{
			$ixDateFlg = 1;
			$ixDateErr = "<b><font color = 'red'>ERROR: Please enter the date switched to print!!</font></b><br>\n";
		}
	}else{
		if($enrollInfoArray[1] == 1 && ($_POST["dateIXChange"] != "" || $_POST["reasonIX"] != 0)){
			$ixDateFlg = 1;
			$ixDateErr = "<b><font color = 'red'>ERROR: The participant did not switch from web to print, therefore, you should not enter the date switched or reason switched to print!!</font></b><br>\n";
		}
	}
	
	//check if the email has been entered,  and the web password if the ix modality is web
	if (!$ixFlg){
		if($ptInfoArray[16] == ""){
			$emailFlg = 1;
		    $emailErr = "<b><font color = 'red'>ERROR: You have not entered the email for this participant, please go back to enter the email!!</font></b><br>\n";
		}
		if(!$emailFlg){
			if (!$_POST["webPwd"]){
				$pwdFlg = 1;
			    $pwdErr = "<b><font color = 'red'>ERROR: Please enter the password!!</font></b><br>\n";
			}else{
				if ($_POST["webPwd"] != $_POST["reenterPwd"] ){
					$pwdFlg = 1;
			    	$pwdErr = "<b><font color = 'red'>ERROR: The password and re-entered password are not the same!!</font></b><br>\n";
				}else{
					if(strlen($_POST["webPwd"])>25){
						$pwdFlg = 1;
			    		$pwdErr = "<b><font color = 'red'>ERROR: The password can not be more then 25 characters!!</font></b><br>\n";
					}else{
						if(ereg('[^A-Za-z0-9]', $_POST["webPwd"])){
							$pwdFlg = 1;
			    			$pwdErr = "<b><font color = 'red'>ERROR: The password contains characters other then letters and numbers!!</font></b><br>\n";
						}
					}
				}
			}
		}
	}
	
	// Check if smoking values are entered correctly
	
	if($_POST['numCigs'] < 0 || is_nan($_POST['numCigs']))
		$smokingErr[0] = "<b><font color = 'red'>ERROR: Number of cigarettes smoked must be a positive number !!</font></b><br>\n";
	if($_POST['smoked'] == 0 && $_POST['numCigs'] > 0)
		$smokingErr[1] = "<b><font color = 'red'>ERROR: If participant is a non-smoker, Number of cigarettes smoked must be zero!</font></b><br>\n";
	if($_POST['smoked'] == 1 && $_POST['numCigs'] == 0)
		$smokingErr[2] = "<b><font color = 'red'>ERROR: If participant has smoked in the past 30 days, you must enter a number greater than zero for Number of cigarettes smoked!</font></b><br>\n";
		
	//check reminder modality and reminder phone
	if ($_POST["remindRand"] == 1){
		if (!$_POST["reminModality"]){
			$remindFlg = 1;
		    $remindErr = "<b><font color = 'red'>ERROR: Please enter the reminder modality!!</font></b><br>\n";
		}else{
			if($_POST["ptRPhone"] == ""){
				$remindFlg = 1;
			    $remindErr = "<b><font color = 'red'>ERROR: Please enter the preferred phone for reminder!!</font></b><br>\n";
			}
		}
	}
	
	// check the reminder modality change date
	if($_POST["dateRemChang"]){
		$rmdMDateFlg = checkValidDate($_POST["dateRemChang"]);
		if ($rmdMDateFlg){
		    $rmdMDateErr = "<b><font color = 'red'>ERROR: ".$dateErrMsg." for date switched to print!!</font></b><br>\n";
		}else{
			// the switch date can not be in the futture
			$today = date("m/d/Y");
			$rmdMDate = formateToDate($_POST["dateRemChang"]);
			$rmdMFlg = compareDates($remindDate, $today);
			if ($rmdMFlg){
			    $rmdMErr = "<b><font color = 'red'>ERROR: Date reminder modality changed can not be in the future!!</font></b><br>\n";
			}else{
				// check if the reason of switch has been entered
				if(!$_POST["reasonRemind"]){
					$rmdMFlg = 1;
					$rmdMErr = "<b><font color = 'red'>ERROR: Please enter the reason reminder modality changed!!</font></b><br>\n";
				}
			}
		}
	}
	// check the reminder optout date
	if($_POST["dateRemOpt"]){
		$rmdOptFlg = checkValidDate($_POST["dateRemOpt"]);
		if ($rmdOptFlg){
		    $rmdOptErr = "<b><font color = 'red'>ERROR: ".$dateErrMsg." for date switched to print!!</font></b><br>\n";
		}else{
			// the switch date can not be in the futture
			$today = date("m/d/Y");
			$remindDate = formateToDate($_POST["dateRemOpt"]);
			$rmdOptFlg = compareDates($remindDate, $today);
			if ($rmdOptFlg){
			    $rmdOptErr = "<b><font color = 'red'>ERROR: Date reminder modality changed can not be in the future!!</font></b><br>\n";
			}else{
				// check if the reason has been entered
				if(!$_POST["reasonRemOpt"]){
					$rmdOptFlg = 1;
					$rmdOptErr = "<b><font color = 'red'>ERROR: Please enter the reason for reminder opt-out!!</font></b><br>\n";
				}
			}
		}
	}
	//if all fields are correct, then update the recruitment table
	if (!$fieldFlg && !$ixDateFlg && !$remindFlg && !$rmdMFlg && !$rmdOptFlg && !$pwdFlg && !$emailFlg){
		//format date PCP date ix change
		if($_POST["dateIXChange"]) $dateIXChange = formatDate($_POST["dateIXChange"], 1);
		//format the date reminder modality changed
		if($_POST["dateRemChang"]) $dateRemChang = formatDate($_POST["dateRemChang"], 1);
		//format the date reminder optout
		if($_POST["dateRemOpt"]) $dateRemOpt = formatDate($_POST["dateRemOpt"], 1);
		// call function to update the web site info
		if ($ixFlg != 1 ){
			if(count($enrollInfoArray)== 0){
				$statusArray = addWebUser($partID, $ptInfoArray[2], $ptInfoArray[16], $_POST["webPwd"], 1, 1, $ptInfoArray[1]);
			}
			else {
				$statusArray = addWebUser($partID, $ptInfoArray[2], $ptInfoArray[16], $_POST["webPwd"], 1, 0, $ptInfoArray[1]);
			}
		}elseif($changeFlg == 1){
			$statusArray = addWebUser($partID, $ptInfoArray[2], $ptInfoArray[16], $_POST["webPwd"], 0, 0, $ptInfoArray[1]);
		}
		//print_r($statusArray);
		// insert record into the appt table if no record for appt
		if(count($enrollInfoArray)== 0){
		/* Vikki adjusted this section, to enter start date on page one */
		// find out if the pt's start date based on the ix modality
		// if print, start date = appt date
		// if web, start date = current date
			if($_POST["ixModality"]=='1'){
				$today = date('m/d/Y'); // this is print, should be appt 
				$startDate = $today; // this is web, should be today 
			}elseif($_POST["ixModality"]=='2'){
				$startDate = $apptArray[1];
			}else{
				$today = date('m/d/Y');
				$startDate = $today;
			}
		//format all the dates 
		$inputStDate = formatDate($startDate, 1);
		/* End of code Vikki moved */
			$istFlg = insertEnrollInfo($partID, $_POST["ixModality"], $dateIXChange, $_POST["reasonIX"], $_POST["smoked"], $_POST["numCigs"], 
			                        $_POST["webPwd"], $_POST["remindRand"], $_POST["reminModality"], 
									$dateRemChang, $_POST["reasonRemind"], $dateRemOpt, $_POST["reasonRemOpt"], $inputStDate);
		}
		else{
			$updFlg = updateEnrollInfo($partID, $_POST["ixModality"], $dateIXChange, $_POST["reasonIX"],  $_POST["smoked"], $_POST["numCigs"], 
			                        $_POST["webPwd"], $_POST["remindRand"], $_POST["reminModality"], 
									$dateRemChang, $_POST["reasonRemind"], $dateRemOpt, $_POST["reasonRemOpt"]);
		}
		if($istFlg || $updFlg){
			$istErr="<b><font color = 'red'>ERROR:".$errIstMsg."</font></b><br>\n";
		}else{
			// update the pt_info table for field ptRPrefer
			$updFlg = updateRphone($partID, $_POST["ptRPhone"]);
			if($istFlg){
			$istErr="<b><font color = 'red'>ERROR:".$errIstMsg."</font></b><br>\n";
			}else{
				$succMsg = "<font color = 'green' size = \"+1\">The following enrollment info has been successfully saved in the database!</font><br>\n";
			}
		}
	}
	
}elseif ($_POST["isSubmittedEnroll2"]==1) {
	header("Location: edit_enrollment_two.php?partID=".$partID."&heID=".$heID);
	exit();
}

// Vikki added success message for uc appts
if ($ranArm == "uc") {
	$succMsg = "<font color = 'green' size = \"+1\">RECORD COMPLETE!!<br>This participant's enrollment was auto-saved (uc arm)!</font><br>\n";
}
?>
<!DOCTYPE html PUBLIC "-//W3C//ulD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/ulD/xhtml1-strict.uld">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Edit Enrollment Page 1</title>
<link rel="stylesheet" type="text/css" href="includes/main.css" />
	<script language="javascript">
	
	<!--
	
	// FUNCTION: SUBMIT EDIT ENROLLMENT INFO PAGE 1
	function submitEnroll1() {
	    document["form1"]["isSubmittedEnroll1"].value=1;
	    document["form1"].submit();
	}
	//-->
	
	// FUNCTION: SUBMIT EDIT ENROLLMENT INFO PAGE 2
	function submitEnroll2() {
	    document["form1"]["isSubmittedEnroll2"].value=1;
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
		<h2 align = "center"> Edit Enrollment Info for <?php echo $ptInfoArray[2] . " ". $ptInfoArray[3];?></h2>
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
						}else{
							$menu = getMenu();
						}
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
if($_POST["isSubmittedEnroll1"]==1){

	if($fieldErr){
		echo $fieldErr."<br>";
	}
	if ($ixDateErr!="") {
	    echo $ixDateErr."<br>";;
	}
	if ($remindErr!="") {
		echo $remindErr."<br>";;
	}
	if ($rmdMErr!="") {
	    echo $rmdMErr."<br>";;
	}
	if($rmdOptErr != ""){
		echo $rmdOptErr."<br>";
	}
	if($emailErr != ""){
		echo $emailErr."<br>";
	}
	if($pwdErr != ""){
		echo $pwdErr."<br>";
	}
	if ($istErr!="") {
		echo $istErr ."<br>";
	}
	foreach($smokingErr as $err){
		if($err !="")
			echo $err;
	}
	
	// check the therese's function status
	if($ixFlg != 1){
		if($succMsg!="" && $statusArray[0] != 1){
			echo "<b><font color = 'red' >The following enrollment info has been successfully saved in the PTS database!
						However, there seams to a problem creating account in the web database: ".$statusArray[1]."</font></b><br>\n";
		}elseif( $statusArray[0] != 1 ){
			echo "<b><font color = 'red'>ERROR: for web user".$statusArray[1]."</font></b><br>\n";
		}else{
			echo $succMsg."<br>";
		}
		//echo $statusArray[1];
	}elseif($changeFlg == 1){
		if($succMsg!=""  && $statusArray[0] != 1){
			echo"<b><font color = 'red' >The following enrollment info has been successfully saved in the PTS database!
						However, there seams to a problem creating account for the web database: ".$statusArray[1]."</font></b><br>\n";
		}elseif($statusArray[0] != 1){
			echo "<b><font color = 'red'>ERROR: for web user".$statusArray[1]."</font></b><br>\n";
		}else{
			echo $succMsg."<br>";
		}
		//echo $statusArray[1];
	}else{
		//report success msg
		if ($succMsg!="") {
		    echo $succMsg."<br>";
		}
	}
}
if ($ranArm == "uc") {
	echo $succMsg."<br>";
}
?>
<form name = form1 method="POST" action="edit_enrollment_one.php?partID=<?php echo $partID; ?>&heID=<?php echo $heID; ?>" >
<input type="hidden" name="isSubmittedEnroll1" value=0 >
<input type="hidden" name="isSubmittedEnroll2" value=0 >
<p align = "right"><font color="#526D6D">Page 1</p>
<p><B>(Please note all the date fields in this page should be in "mm/dd/yyyy" format)</B></font></p>
 <table width = 75% border = 5 align = "left" valign = "top" cellpadding =6>
<?php
if($_POST["isSubmittedEnroll1"]!=1){
	echo '<tr>';
	echo "<td>Is participant a smoker? <br />";
	if($enrollInfoArray[SMOKER] == 1)
		echo " <input type='radio' name='smoked' value='0' />&nbsp;No&nbsp;&nbsp;&nbsp;<input type='radio' name='smoked' value='1' checked/>&nbsp;Yes";
	else
		echo " <input type='radio' name='smoked' value='0' checked/>&nbsp;No&nbsp;&nbsp;&nbsp;<input type='radio' name='smoked' value='1' />&nbsp;Yes";
	echo "</td>";
	echo "<td>How many cigarettes in the past 30 days? <br />";
	echo "<input type='text' name='numCigs' value='" . $enrollInfoArray[NUM_SMOKED] . "' />";
	echo "</td>";
	echo "</tr>";
	for($a = 0; $a <count($cellName); $a++){
		//echo $a . ") " . $cellName[$a] . ": " . $enrollInfoArray[$a] . "<br />"; 
		if($a == 0){
			echo "<tr>\n";
			echo "<td align = \"left\" colspan = 2> ".$cellName[$a]." <input type = \"text\" name = \"".$fieldName[$a]."\" value = \"".$partID."\" disabled>
					      <input type = \"hidden\" name = \"".$fieldName[$a]."\" value = \"".$partID."\"></td>\n";
			echo "</tr>\n";
		}elseif($a <4){
			if($a%2 != 0){
				echo "<tr>\n";
				if($cellName[$a] == "Ix Modality"){
					echo "<td align = \"left\">".$cellName[$a]." <br>\n";
					// if the pt already in print, he/she can not change to web
					if($ixFlg == 1){
						echo "<input type = \"text\"  value = \"Print\" disabled>
						<input type = \"hidden\" name = \"".$fieldName[$a]."\" value = \"2\">\n";
					}else{
						echo "<select name = \"".$fieldName[$a]."\">\n";
						for($c =0; $c<count($ixModArray); $c++){
							if($c == $enrollInfoArray[$a]){
								echo "<option value = \"".$c."\" selected> &nbsp;&nbsp;&nbsp;&nbsp;".$ixModArray[$c]." &nbsp;&nbsp;&nbsp;&nbsp;</option>\n";
							}else{
								echo "<option value = \"".$c."\"> &nbsp;&nbsp;&nbsp;&nbsp;".$ixModArray[$c]." &nbsp;&nbsp;&nbsp;&nbsp;</option>\n";
							}
						}
						echo "</select>\n";
					}
					echo "</td>\n";
				}elseif($cellName[$a] == "Reason Switch to Print"){
					echo "<td align = \"left\" colspan = 2>".$cellName[$a]." <br>\n";
					echo "<select name = \"".$fieldName[$a]."\">\n";
					for($c =0; $c<=count($ixChangeArray); $c++){
						if($c == $enrollInfoArray[$a]){
							echo "<option value = \"".$c."\" selected> ".$ixChangeArray[$c]." </option>\n";
						}else{
							echo "<option value = \"".$c."\"> ".$ixChangeArray[$c]." </option>\n";
						}
					}
					echo "</select>\n";
					echo "</td>\n";
				}
			}else{
				
				
				echo "<td align = \"left\"> ".$cellName[$a]." <br><input type = \"text\" name = \"".$fieldName[$a]."\" value = \"".$enrollInfoArray[$a]."\"></td>\n";
				
			}
		// END $a < 4
		}else{
			if($a%2 == 0){
				echo "<tr>\n";
				/*
				if($fieldName[$a] == 'smoked'){
					echo "<td>" . $cellName[$a] . "<br /><select name= \"" . $fieldName[$a] . "\">\n";	
					echo "<option value=\"$enrollInfoArray[$a]\" selected>&nbsp;&nbsp;&nbsp;Yes&nbsp;&nbsp;&nbsp;</option>\n";
					if($enrollInfoArray[$a] == 1){
						echo "<option value=\"0]\" >&nbsp;&nbsp;&nbsp;No&nbsp;&nbsp;&nbsp;</option>\n";
					}
					else{
						echo "<option value=\"1\" selected>&nbsp;&nbsp;&nbsp;No&nbsp;&nbsp;&nbsp;</option>\n";
					}
					$smoker = ($enrollInfoArray[$a] == 0 ? "No" : "Yes");
						echo "<option value=\"" . $enrollInfoArray[$a] . "\" selected>&nbsp;&nbsp;&nbsp;" . $smoker . "&nbsp;&nbsp;&nbsp;</option>\n";
					echo "</select></td>";
				}
				/*
				else if($fieldName[$a] == 'numCigs' && $enrollInfoArray[$a - 1] == 0){
					echo "<td align = \"left\"> ".$cellName[$a]." <br><input type = \"text\" name = \"".$fieldName[$a]."\" value = \"".$enrollInfoArray[$a-1]."\" disabled></td>\n";
				}
				*/
				if($cellName[$a] == "Reminder Randomization"){
					echo "<td align = \"left\">".$cellName[$a]." <br>\n";
					echo "<select name = \"".$fieldName[$a]."\">\n";
					echo "<option value = \"Not applicable\">Not applicable</option>";
					echo "</select>\n";
					echo "</td>\n";
				}elseif($cellName[$a] == "Preferred Phone for Reminder"){
					echo "<td align = \"left\">".$cellName[$a]." <br>\n";
					echo "<select name = \"".$fieldName[$a]."\">\n";
					echo "<option value = \"Not applicable\">Not applicable</option>";
					echo "</select>";
					echo "</td>\n";
				}elseif($cellName[$a] == "Reason Reminder Opt-out"){
					echo "<td align = \"left\">".$cellName[$a]." <br>\n";
					echo "<select name = \"".$fieldName[$a]."\">\n";
					echo "<option value = \"Not applicable\">Not applicable</option>";
					echo "</select>";
					echo "</td>\n";
				}else{
					if($cellName[$a] == "Web Password"){
						echo "<td align = \"left\"> ".$cellName[$a]." <br><input type = \"text\" name = \"".$fieldName[$a]."\" value = \"".$enrollInfoArray[$a]."\" size = 30></td>\n";
					/*
					}else if($cellName[$a] == 'Number of cigarettes smoked'){
						echo "<td align = \"left\"> ".$cellName[$a]." <br><input type = \"text\" name = \"".$fieldName[$a]."\" value = \"".$enrollInfoArray[$a-1]."\" </td>\n";
					*/
					}else{
						echo "<td align = \"left\"> ".$cellName[$a]." <br><input type = \"text\" name = \"".$fieldName[$a]."\" value = \"".$enrollInfoArray[$a-1]."\"></td>\n";
					}
				}
			// END if $a is EVEN
			}else{
				if($cellName[$a] == "Reminder Modality"){
					echo "<td align = \"left\">".$cellName[$a]." <br>\n";
					echo "<select name = \"".$fieldName[$a]."\">\n";
					echo "<option value = \"Not applicable\">Not applicable</option>";
					echo "</select>\n";
					echo "</td>\n";
				}elseif($cellName[$a] == "Reason Reminder Modality Changed"){
					echo "<td align = \"left\">".$cellName[$a]." <br>\n";
					echo "<select name = \"".$fieldName[$a]."\">\n";
					echo "<option value = \"Not applicable\">Not applicable</option>";
					echo "</select>";
					echo "</td>\n";
				}else{
					if($cellName[$a] == "Re-enter Password"){
						echo "<td align = \"left\"> ".$cellName[$a]." <br><input type = \"text\" name = \"".$fieldName[$a]."\" value = \"".$enrollInfoArray[$a-1]."\" size = 30></td>\n";
					}else{
						echo "<td align = \"left\"> ".$cellName[$a]." <br><input type = \"text\" name = \"".$fieldName[$a]."\" value = \"".$enrollInfoArray[$a-2]."\"></td>\n";
					}
				}
			}
		}
	}
	
}elseif($_POST["isSubmittedEnroll1"]==1){
	echo '<tr>';
	echo "<td>Is participant a smoker? <br />";
	if($inputEnrollArray[SMOKER] == 1)
		echo " <input type='radio' name='smoked' value='0' />&nbsp;No&nbsp;&nbsp;&nbsp;<input type='radio' name='smoked' value='1' checked/>&nbsp;Yes";
	else
		echo " <input type='radio' name='smoked' value='0' checked/>&nbsp;No&nbsp;&nbsp;&nbsp;<input type='radio' name='smoked' value='1' />&nbsp;Yes";
	echo "</td>";
	echo "<td>How many cigarettes in the past 30 days? <br />";
	echo "<input type='text' name='numCigs' value='" . $inputEnrollArray[NUM_SMOKED] . "' />";
	echo "</td>";
	echo "</tr>";
	for($a = 0; $a <count($cellName); $a++){
		if($a == 0){
			echo "<tr>\n";
			echo "<td align = \"left\" colspan = 2> ".$cellName[$a]." <input type = \"text\" name = \"".$fieldName[$a]."\" value = \"".$partID."\" disabled>
					      <input type = \"hidden\" name = \"".$fieldName[$a]."\" value = \"".$partID."\"></td>\n";
			echo "</tr>\n";
		}elseif($a <4){
			if($a%2 != 0){
				echo "<tr>\n";
				if($cellName[$a] == "Ix Modality"){
					echo "<td align = \"left\">".$cellName[$a]." <br>\n";
					if($ixFlg == 1){
						echo "<input type = \"text\"  value = \"Print\" disabled>
						<input type = \"hidden\" name = \"".$fieldName[$a]."\" value = \"2\">\n";
					}else{
						echo "<select name = \"".$fieldName[$a]."\">\n";
						for($c =0; $c<count($ixModArray); $c++){
							if($c == $inputEnrollArray[$a]){
								echo "<option value = \"".$c."\" selected> &nbsp;&nbsp;&nbsp;&nbsp;".$ixModArray[$c]." &nbsp;&nbsp;&nbsp;&nbsp;</option>\n";
							}else{
								echo "<option value = \"".$c."\"> &nbsp;&nbsp;&nbsp;&nbsp;".$ixModArray[$c]." &nbsp;&nbsp;&nbsp;&nbsp;</option>\n";
							}
						}
						echo "</select>\n";
					}
					echo "</td>\n";
				}elseif($cellName[$a] == "Reason Switch to Print"){
					echo "<td align = \"left\" colspan = 2>".$cellName[$a]." <br>\n";
					//if($ixFlg ==1){
						//echo "<input type = \"text\" value = \"\" disabled>
						//<input type = \"hidden\" name = \"".$fieldName[$a]."\" value = \"\">\n";
					//}else{
						echo "<select name = \"".$fieldName[$a]."\">\n";
						for($c =0; $c<=count($ixChangeArray); $c++){
							if($c == $inputEnrollArray[$a]){
								echo "<option value = \"".$c."\" selected> ".$ixChangeArray[$c]." </option>\n";
							}else{
								echo "<option value = \"".$c."\"> ".$ixChangeArray[$c]." </option>\n";
							}
						}
						echo "</select>\n";
					//}
					echo "</td>\n";
				}
			}else{
				//if($ixFlg == 1){
					//echo "<td align = \"left\"> ".$cellName[$a]." <br><input type = \"text\" value = \"\" disabled>
					//<input type = \"hidden\" name = \"".$fieldName[$a]."\" value = \"\"></td>\n";
				//}else{
					echo "<td align = \"left\"> ".$cellName[$a]." <br><input type = \"text\" name = \"".$fieldName[$a]."\" value = \"".$inputEnrollArray[$a]."\"></td>\n";
				//}
			}
		}else{
			if($a%2 == 0){
				echo "<tr>\n";
				/*
				if($fieldName[$a] == 'smoked'){
					echo "<td>" . $cellName[$a] . "<br /><select name= \"" . $fieldName[$a] . "\">\n";
					echo "<option value=\"$inputEnrollArray[$a]\" selected>&nbsp;&nbsp;&nbsp;Yes&nbsp;&nbsp;&nbsp;</option>\n";
					if($inputEnrollArray[$a] == 1){
						echo "<option value=\"0]\" >&nbsp;&nbsp;&nbsp;No&nbsp;&nbsp;&nbsp;</option>\n";
					}
					else{
						echo "<option value=\"1\" selected>&nbsp;&nbsp;&nbsp;No&nbsp;&nbsp;&nbsp;</option>\n";
					}
					$smoker = ($enrollInfoArray[$a] == 0 ? "No" : "Yes");
						echo "<option value=\"" . $enrollInfoArray[$a] . "\" selected>&nbsp;&nbsp;&nbsp;" . $smoker . "&nbsp;&nbsp;&nbsp;</option>\n";
					echo "</select></td>";
				}
				/*
				else if($fieldName[$a] == 'numCigs' && $enrollInfoArray[$a - 1] == 0){
					echo "<td align = \"left\"> ".$cellName[$a]." <br><input type = \"text\" name = \"".$fieldName[$a]."\" value = \"".$enrollInfoArray[$a-1]."\" disabled></td>\n";
				}
				*/
				if($cellName[$a] == "Reminder Randomization"){
					echo "<td align = \"left\">".$cellName[$a]." <br>\n";
					echo "<select name = \"".$fieldName[$a]."\">\n";
					echo "<option value = \"Not applicable\">Not applicable</option>";
					echo "</select>\n";
					echo "</td>\n";
				}elseif($cellName[$a] == "Preferred Phone for Reminder"){
					echo "<td align = \"left\">".$cellName[$a]." <br>\n";
					echo "<select name = \"".$fieldName[$a]."\">\n";
					echo "<option value = \"Not applicable\">Not applicable</option>";
					echo "</select>";
					echo "</td>\n";
				}elseif($cellName[$a] == "Reason Reminder Opt-out"){
					echo "<td align = \"left\">".$cellName[$a]." <br>\n";
					echo "<select name = \"".$fieldName[$a]."\">\n";
					echo "<option value = \"Not applicable\">Not applicable</option>";
					echo "</select>";
					echo "</td>\n";
				}else{
					if($cellName[$a] == "Web Password"){
						echo "<td align = \"left\"> ".$cellName[$a]." <br><input type = \"text\" name = \"".$fieldName[$a]."\" value = \"".$inputEnrollArray[$a]."\" size = 30></td>\n";
					}else{
						echo "<td align = \"left\"> ".$cellName[$a]." <br><input type = \"text\" name = \"".$fieldName[$a]."\" value = \"".$inputEnrollArray[$a]."\"></td>\n";
					}
				}
			}// end if $a is even
			else{
				
				if($cellName[$a] == 'Number of cigarettes smoked'){
						echo "<td align = \"left\"> ".$cellName[$a]." <br><input type = \"text\" name = \"".$fieldName[$a]."\" value = \"".$inputEnrollArray[$a-1]."\" </td>\n";
				}else if($cellName[$a] == "Reminder Modality"){
					echo "<td align = \"left\">".$cellName[$a]." <br>\n";
					echo "<select name = \"".$fieldName[$a]."\">\n";
					echo "<option value = \"Not applicable\">Not applicable</option>";
					echo "</select>\n";
					echo "</td>\n";
				}elseif($cellName[$a] == "Reason Reminder Modality Changed"){
					echo "<td align = \"left\">".$cellName[$a]." <br>\n";
					echo "<select name = \"".$fieldName[$a]."\">\n";
					echo "<option value = \"Not applicable\">Not applicable</option>";
					echo "</select>";
					echo "</td>\n";
				}else{
					if($cellName[$a] == "Re-enter Password"){
						echo "<td align = \"left\"> ".$cellName[$a]." <br><input type = \"text\" name = \"".$fieldName[$a]."\" value = \"".$inputEnrollArray[$a]."\" size = 30></td>\n";
					/*
					}else if($cellName[$a] == 'Number of cigarettes smoked'){
						echo "<td align = \"left\"> ".$cellName[$a]." <br><input type = \"text\" name = \"".$fieldName[$a]."\" value = \"".$inputEnrollArray[$a-1]."\" </td>\n";
					*/
					}else{
						echo "<td align = \"left\"> ".$cellName[$a]." <br><input type = \"text\" name = \"".$fieldName[$a]."\" value = \"".$inputEnrollArray[$a]."\"></td>\n";
					}
				}
			}
		}
	}
	
}

echo "<tr>\n";
echo "<td><input type = \"submit\" value = \"Save Record\" onClick=\"submitEnroll1();\"";
//if ($ranArm == "uc") { echo ' disabled /'; }
echo "></td>\n";
// VP added disabled Next Page if they have not successfully finished the first step
echo "<td><input type = \"submit\" value = \"Next Page\" onClick=\"submitEnroll2();\"";
if ($succMsg == "" || $ranArm == "uc") { echo ' disabled /'; }
echo " >\n";
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
