<?php
// LOAD mySQL FUNCTIONS
include("includes/connection.php");
include("includes/addwebuser.php");
//include("includes/handler.js");
$errMsg="";
// CONNECT TO DATABASE
if ($mysqlID=dbConnect()) {
    selectDatabase("lung_cancer_user");
} else {
    $errMsg.="<b>ERROR:</b> ".mysql_error()."<br>\n";
}
$cellName = array("Study ID", "MRN", "First Name", "Last Name", "Gender", "DOB (mm/dd/yyyy)", "Street Address 1", "Street Address 2", 
					"City", "State", "Zip", "Home Phone", "Work Phone", "Cell Phone", "Other Phone", "Preferred Phone General", "Email", "Re-enter Email ", "Notes");
$fieldName = array("partID", "MRN", "ptFName", "ptLName", "gender", "dob", "ptAddress1", "ptAddress2", "ptCity",
                 "ptState", "ptZip", "ptHPhone", "ptWPhone", "ptCPhone", "ptOPhone", "ptPPhone", "ptEmail", "re-email", "notes");
$stateFullArray = array("", "Alabama", "Alaska", "Arizona", "Arkansas", "California", "Colorado", "Connecticut", "Delaware", "District of Columbia", "Florida",
                   "Georgia", "Hawaii", "Idaho", "Illinois", "Indiana", "Iowa", "Kansas", "Kentucky", "Louisiana", "Maine", "Maryland", "Massachusetts", 
				   "Michigan", "Minnesota", "Mississippi", "Missouri", "Montana", "Nebraska", "Nevada", "New Hampshire", "New Jersey", "New Mexico", "New York", 
				   "North Carolina", "North Dakota", "Ohio", "Oklahoma", "Oregon", "Pennsylvania", "Rhode Island", "South Carolina", "South Dakota", "Tennessee", 
				   "Texas", "Utah", "Vermont", "Virginia", "Washington", "West Virginia", "Wisconsin", "Wyoming");
foreach ($stateFullArray as $st){
	$stateArray[] = convertState2Abbrev($st);
}

$genderArray = array(" ", "Male", "Female");
$fieldErr= "";
$mrnErr= "";
$nameErr= "";
$dateErr = "";
$addErr = "";
$zipErr="";
$phoneErr ="";
$emailErr ="";
$istErr = "";
$istFlg = "";
$mrnFlg =0;
$fieldFlg =0;
$dateFlg =0;
$zipFlg =0;
$phoneFlg =0;
$emailFlg =0;

// get the participant ID
if($_GET['partID']){
	$partID = $_GET['partID'];
}else{
	$partID = $_POST["partID"];
}
// get heID if exist
if($_GET['heID']) $heID = $_GET['heID'];
// get the participart info from the part_info table
$ptInfoArray = getPartInfo($partID);

// get recruitment info
//$recruitArray = getRecruitInfo($partID);
// get the enrollment info
$enrollArray = getEnrollment($partID);
// put all phone number in a array
$phoneArray = array("", "H-".$ptInfoArray[11], "W-".$ptInfoArray[12], "C-".$ptInfoArray[13], "O-".$ptInfoArray[14]);
// check form submission
if ($_POST["isSubmittedLogOut"]==1) {
	header("Location: login.php");
	exit();
}
// if form was submitted...
if ($_POST["isSubmittedPt"]==1) {
	$_POST["ptFName"] = str_replace('\\', '', $_POST["ptFName"]);
	$_POST["ptLName"] = str_replace('\\', '', $_POST["ptLName"]); // remove slashes 
	$_POST["notes"] = str_replace('\\', '', $_POST["notes"]); // remove slashes 
	$_POST["ptEmail"] = str_replace('\\', '', $_POST["ptEmail"]);
	$_POST["re-email"] = str_replace('\\', '', $_POST["re-email"]);
	for($i = 0; $i <count($fieldName); $i++){
		$inputArray[$i] = $_POST[$fieldName[$i]];
		// if required field not been filled, then display error mag
		if(($i != 7) && ($i < 11)){
			if($inputArray[$i] == "") {
				$fieldFlg = 1;
				$fieldErr = $fieldErr."<b><font color = 'red'>ERROR: Please enter ".$cellName[$i]."!!</font></b><br>\n";
				//$i = count($fieldName);
			}
		}
		//echo "The ".$fieldName[$i]." is ".$_POST[$fieldName[$i]]."<br>\n";
	}
	
	// check if the MRN has already in the database
	if($_POST["MRN"]){
		$mrnFlg = checkMRN($_POST["MRN"]);
		if ($mrnFlg){
		    $mrnErr = "<b><font color = 'red'>ERROR: ".$errMRNMsg."</font></b><br>\n";
		}
	}
	
	//check to see if DOB is correct
	if ($_POST["dob"] != ""){
		$dateFlg = checkValidDate($_POST["dob"]);
		if ($dateFlg){
		    $dateErr = "<b><font color = 'red'>ERROR: ".$dateErrMsg." for DOB!!</font></b><br>\n";
		}
	}
	
	//check to see if zip code is valid
	if($_POST["ptZip"]){
		$zipFlg = checkValidZip($_POST["ptZip"]);
		if ($zipFlg){
		    $zipErr = "<b><font color = 'red'>ERROR: ".$zipErrMsg."!!</font></b><br>\n";
		}
	}
	// need to enter at lest one phone #
	if(!trim($_POST["ptHPhone"]) && !trim($_POST["ptWPhone"]) && !trim($_POST["ptCPhone"]) && !trim($_POST["ptOPhone"])){
		$phoneFlg =1;
		$phoneErr = "<b><font color = 'red'>ERROR: Please enter at least one phone number!!</font></b><br>\n";
	}
	// check if the phone number entered is a valid format
	if(!$phoneFlg){
		for($j = 11; $j <15; $j++){
			if(trim($_POST[$fieldName[$j]])) $phoneFlg = checkValidPhone(trim($_POST[$fieldName[$j]]));
			if($phoneFlg){
				$phoneErr = "<b><font color = 'red'>ERROR: ".$phoneErrMsg." for ".$cellName[$j]."!!</font></b><br>\n";
				$j = 15;
			}
		}
	}
	// check if email is the reqired field
	if(!trim($_POST["ptEmail"])){
		$ix_modality = getIXmodality($_POST["partID"]);
		if($ix_modality == 1){
			$emailErr = "<b><font color = 'red'>ERROR: This participant's IX Modality is web, so email field is required!!</font></b><br>\n";
			$emailFlg =1;
		}
	}else{
		if(trim($_POST["ptEmail"]) != trim($_POST["re-email"])){
			$emailErr = "<b><font color = 'red'>ERROR: The re-entered email is different from the email!!</font></b><br>\n";
			$emailFlg =1;
		}else{
			//then check to see if email is in correct formate
			$emailFlg = checkValidEmail(trim($_POST["ptEmail"]));
			if ($emailFlg){
			    $emailErr = "<b><font color = 'red'>ERROR: ".$emailErrMsg."!!</font></b><br>\n";
			}else{
				// check if the email already in the db
				$emailFlg = getEmail($partID, trim($_POST["ptEmail"]));
				if ($emailFlg){
				    $emailErr = "<b><font color = 'red'>ERROR: ".$errDupMsg."!!</font></b><br>\n";
				}
			}
		}
	}
	//if all fields are correct, then update the part_info table
	if (!$fieldFlg && !$mrnFlg && !$dateFlg && !$addFlg && !$zipFlg && !$phoneFlg && !$emailFlg){
		
		if($_POST["dob"]) {
			$dob = formatDate($_POST["dob"], 1);
		}else{
			$dob = "0000-00-00";
		}
		
		if($_POST["gender"]) $gender = substr($_POST["gender"], 0, 1);
		$fname = str_replace('\\', '', $_POST["ptFName"]);
		$lname = str_replace('\\', '', $_POST["ptLName"]); // remove slashes 
		$notes = str_replace('\\', '', $_POST["notes"]); // remove slashes 
		// check if the pt is enrolled and ix modality is web, then call function to update the web site info
		if($enrollArray[1] == 1){
			$statusArray = addWebUser($partID, $fname, $_POST["ptEmail"], $enrollArray[4], 1, 0,$ptInfoArray[1]);
			//print_r($statusArray);
		}
		//echo "<br>The first name and last name are ".$fname." ".$lname."<br>";
		$istFlg = updatePtInfo($partID, $_POST["MRN"], $fname, $lname, 
		                       $gender, $dob, trim($_POST["ptAddress1"]), trim($_POST["ptAddress2"]), 
                 			   trim($_POST["ptCity"]), $_POST["ptState"], trim($_POST["ptZip"]), 
							   trim($_POST["ptHPhone"]), trim($_POST["ptWPhone"]), trim($_POST["ptCPhone"]), 
							   trim($_POST["ptOPhone"]), trim($_POST["ptPPhone"]), trim($_POST["ptEmail"]), $notes);
		if($istFlg){
			$istErr="<b><font color = 'red'>ERROR:".$errIstMsg."</font><br>\n";
		}else{
			$succMsg = "<font color = 'green' size = \"+1\">The following participant has been successfully saved in the database!</font><br>\n";
		}
	}
	
}elseif ($_POST["isSubmittedAppt"]==1) {
	header("Location: edit_appt.php?partID=".$partID);
	exit();
}elseif ($_POST["isSubmittedContact"]==1){
	header("Location: edit_contact.php?partID=".$partID."&heID=".$heID);
	exit();
}
?>
<!DOCTYPE html PUBLIC "-//W3C//ulD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/ulD/xhtml1-strict.uld">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Edit Participant Info</title>
<link rel="stylesheet" type="text/css" href="includes/main.css" />
	<script language="javascript">
	<!--
	// FUNCTION: SUBMIT ADD PARTICIPANT
	function submitPt() {
		document["form1"]["isSubmittedPt"].value=1;
		document["form1"]["isSubmittedAppt"].value=0;
	 	document["form1"].submit();
	}
	
	//-->
	
	<!--
	// FUNCTION: SUBMIT ADD APPONITMENT INFO
	function submitAppt() {
	    document["form2"].submit();
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
		<h2 align = "center"> Edit Participants</h2>  
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
if ($_POST["isSubmittedPt"]==1) {
	if ($fieldErr!="") {
	    echo $fieldErr."<br>";
	}
	if ($mrnErr!="") {
	    echo $mrnErr."<br>";
	}
	if ($dateErr!="") {
	    echo $dateErr."<br>";
	}
	if ($addErr!="") {
	    echo $addErr."<br>";
	}
	if ($zipErr!="") {
	    echo $zipErr."<br>";
	}
	if ($phoneErr!="") {
	   echo $phoneErr."<br>";
	}
	if ($emailErr!="") {
	   echo $emailErr."<br>";
	}
	if ($istErr!="") {
	echo $istErr ."<br>";
	}
	// call therese's function status when pt withdraw
	if($enrollInfoArray[1] == 1){
		// if the ix modality is web and status != 1, then display error message
		if($statusArray[0] != 1  && $succMsg !=""){
			echo"<b><font color = 'red' >The following enrollment info has been successfully saved in the PTS database!
					However, there seams to a problem creating account in the web database: ".$statusArray[1]."</font></b><br>\n";
		}elseif($statusArray[0] != 1){
			echo "<b><font color = 'red'>ERROR:".$statusArray[1]."</font></b><br>\n";
		}else{
			echo $succMsg."<br>";
		}
	}else{
		if ($succMsg!="") echo $succMsg."<br>";
	}
}
?>
<table border = 5 align = "left" valign = "top" cellpadding =8>
<form name=form1 method="POST" action="edit_part.php?heID=<?php echo$heID;?>" >
<input type="hidden" name="isSubmittedPt" value=0 >
<input type="hidden" name="isSubmittedAppt" value=0 >
<input type="hidden" name="isSubmittedContact" value=0 >
<?php
if($_POST["isSubmittedPt"]!=1){
	// print participant info in a table
	for($a = 0; $a <count($cellName); $a++){
		// print out the first the tow rows
		if($a <6 ){
			if($a % 2 ==0){
				echo "<tr>\n";
				if($cellName[$a] == "Study ID"){
					echo "<td align=\"left\"> ".$cellName[$a]." <input type=\"text\" name=\"".$fieldName[$a]."\" value=\"".$ptInfoArray[$a]."\" readonly>
					      </td>\n";
				}elseif($cellName[$a] == "Gender"){
					echo "<td align = \"left\"> ".$cellName[$a]." &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
					echo "<select name = \"".$fieldName[$a]."\">\n";
					for($b =0; $b<count($genderArray); $b++){
						if($genderArray[$b] == $ptInfoArray[$a]){
							echo "<option value = \"".$genderArray[$b]."\" selected>".$genderArray[$b]." &nbsp;&nbsp;</option>\n";
						}else{
							echo "<option value = \"".$genderArray[$b]."\">".$genderArray[$b]." &nbsp;&nbsp;</option>\n";
						}
					}
					echo "</select>\n";
					echo "</td>\n";
				}else{
					echo "<td align = \"left\"> ".$cellName[$a]." <input type = \"text\" name = \"".$fieldName[$a]."\" value = \"".$ptInfoArray[$a]."\"></td>\n";
				}
			}else{
				if($cellName[$a] == "MRN"){
					echo "<td align = \"left\"> ".$cellName[$a]." <input type=\"text\" name=\"".$fieldName[$a]."\" value=\"".$ptInfoArray[$a]."\" readonly>
					      </td>\n";
				}elseif($cellName[$a] == "DOB"){
					echo "<td align = \"left\"> ".$cellName[$a]." <input type = \"text\" name = \"".$fieldName[$a]."\" value = \"".$ptInfoArray[$a]."\"></td>\n";
				}else{
					echo "<td align = \"left\"> ".$cellName[$a]." <input type = \"text\" name = \"".$fieldName[$a]."\" value =\"".$ptInfoArray[$a]."\"></td>\n";
				}
				echo "</tr>\n";
			}
		// print out the address
		}elseif($a>5 && $a <11){
			echo "<tr>\n";
			if ($cellName[$a] == "Street Address 1") {
				echo  "<td rowspan = 5 align = \"center\">Home Address</td>\n"; 
				echo  "<td> ".$cellName[$a]." <input type = \"text\" name = \"".$fieldName[$a]."\" value = \"".makeStrUc($ptInfoArray[$a])."\" size = 30></td>\n";
			}else{
				if($cellName[$a] == "State"){
					echo "<td align = \"left\">".$cellName[$a]." \n";
					echo "<select name = \"".$fieldName[$a]."\">\n";
					for($b =0; $b<count($stateArray); $b++){
						//if($stateArray[$b] == makeStrUc($ptInfoArray[$a])){
						if($stateArray[$b] == $ptInfoArray[$a]){
							echo "<option value = \"".$stateArray[$b]."\" selected>".$stateArray[$b]." </option>\n";
						}else{
							echo "<option value = \"".$stateArray[$b]."\">".$stateArray[$b]." </option>\n";
						}
					}
					echo "</select>\n";
					echo "</td>\n";
				}else{
					if($cellName[$a] != "Street Address 2"){
						echo "<td> ".$cellName[$a]." &nbsp;&nbsp;&nbsp;<input type = \"text\" name = \"".$fieldName[$a]."\" value =\"".makeStrUc($ptInfoArray[$a])."\"></td>\n";
					}else{
						echo "<td> ".$cellName[$a]." <input type = \"text\" name = \"".$fieldName[$a]."\" value = \"".makeStrUc($ptInfoArray[$a])."\" size = 30></td>\n";
					}
				}
			}
			echo "</tr>\n";
		// print out the phone numbers
		}elseif($a>=11 && $a<16){
			if($a % 2 !=0){
				echo "<tr>\n";
				if($cellName[$a] == "Cell Phone"){
					echo "<td align = \"left\"> ".$cellName[$a]."&nbsp;&nbsp; <input type = \"text\" name = \"".$fieldName[$a]."\" value = \"".$ptInfoArray[$a]."\"></td>\n";
				}elseif($cellName[$a] == "Preferred Phone General"){
					echo "<td align = \"left\" colspan = 2> ".$cellName[$a]."\n";
					echo "<select name = \"".$fieldName[$a]."\">\n";
					for($b =0; $b<count($phoneArray); $b++){
						if($phoneArray[$b] == $ptInfoArray[$a]){
							echo "<option value = \"".$phoneArray[$b]."\" selected>".$phoneArray[$b]." </option>\n";
						}else{
							echo "<option value = \"".$phoneArray[$b]."\">".$phoneArray[$b]." </option>\n";
						}
					}
					echo "</select>\n";
					echo "</td>\n";
					echo "</tr>\n";
				}else{
					echo "<td align = \"left\"> ".$cellName[$a]." <input type = \"text\" name = \"".$fieldName[$a]."\" value = \"".$ptInfoArray[$a]."\"></td>\n";
				}
			}else{
				echo "<td align = \"left\"> ".$cellName[$a]." &nbsp;&nbsp;&nbsp;&nbsp;<input type = \"text\" name = \"".$fieldName[$a]."\" value = \"".$ptInfoArray[$a]."\"></td>\n";
				echo "</tr>\n";
			}
		// print out the email and notes
		}else{
			if($cellName[$a] == "Notes"){
				echo "<tr>\n";
				echo "<td align = \"left\" colspan =2 valign = \"top\">".$cellName[$a]." &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				      <textarea name=\"".$fieldName[$a]."\" cols=50 rows=3 wrap>".str_replace('\\', '', $ptInfoArray[$a-1])." </textarea></td>\n";
				echo "</tr>\n";
			}else{
				if($cellName[$a] == "Email"){
					echo "<tr>\n";
					echo "<td align = \"left\"> ".$cellName[$a]."<br><input type = \"text\" name = \"".$fieldName[$a]."\" value = \"".$ptInfoArray[$a]."\" size = 45></td>\n";
				}else{
					echo "<td align = \"left\"> ".$cellName[$a]." <br><input type = \"text\" name = \"".$fieldName[$a]."\" value = \"".$ptInfoArray[$a-1]."\" size = 45></td>\n";
					echo "</tr>\n";
				}
			}
		}
	}
}elseif($_POST["isSubmittedPt"]==1){
	for($a = 0; $a <count($cellName); $a++){
		if($a <6 ){
			if($a % 2 ==0){
				echo "<tr>\n";
				if($cellName[$a] == "Study ID"){
					echo "<td align = \"left\"> ".$cellName[$a]." <input type = \"text\" name = \"".$fieldName[$a]."\" value = \"".$inputArray[$a]."\" disabled>
					      <input type = \"hidden\" name = \"".$fieldName[$a]."\" value = \"".$ptInfoArray[$a]."\"></td>\n";
				}elseif($cellName[$a] == "Gender"){
					echo "<td align = \"left\"> ".$cellName[$a]." &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
					echo "<select name = \"".$fieldName[$a]."\">\n";
					for($b =0; $b<count($genderArray); $b++){
						if($genderArray[$b] == $inputArray[$a]){
							echo "<option value = \"".$genderArray[$b]."\" selected>".$genderArray[$b]." &nbsp;&nbsp;</option>\n";
						}else{
							echo "<option value = \"".$genderArray[$b]."\">".$genderArray[$b]." &nbsp;&nbsp;</option>\n";
						}
					}
					echo "</select>\n";
					echo "</td>\n";
				}else{
					echo "<td align = \"left\"> ".$cellName[$a]." <input type = \"text\" name = \"".$fieldName[$a]."\" value = \"".$inputArray[$a]."\"></td>\n";
				}
			}else{
				if($cellName[$a] == "MRN"){
					echo "<td align = \"left\"> ".$cellName[$a]." <input type = \"text\" name = \"".$fieldName[$a]."\" value = \"".$ptInfoArray[$a]."\" disabled>
					      <input type = \"hidden\" name = \"".$fieldName[$a]."\" value = \"".$inputArray[$a]."\"></td>\n";
				}elseif($cellName[$a] == "DOB"){
					echo "<td align = \"left\"> ".$cellName[$a]." <input type = \"text\" name = \"".$fieldName[$a]."\" value = \"".$inputArray[$a]."\"></td>\n";
				}else{
					echo "<td align = \"left\"> ".$cellName[$a]." <input type = \"text\" name = \"".$fieldName[$a]."\" value = \"".$inputArray[$a]."\"></td>\n";
				}
				echo "</tr>\n";
			}
		// print out the address
		}elseif($a>5 && $a <11){
			echo "<tr>\n";
			if ($cellName[$a] == "Street Address 1") {
				echo  "<td rowspan = 5 align = \"center\">Home Address</td>\n"; 
				// Vikki added code to make address 1 upper and lowercase automatically
				echo  "<td> ".$cellName[$a]." <input type = \"text\" name = \"".$fieldName[$a]."\" value = \"".makeStrUc($inputArray[$a])."\" size = 30></td>\n";
			}else{
				if($cellName[$a] == "State"){
					echo "<td align = \"left\">".$cellName[$a]." \n";
					echo "<select name = \"".$fieldName[$a]."\">\n";
					for($b =0; $b<count($stateArray); $b++){
						if($stateArray[$b] == $inputArray[$a]){
							echo "<option value = \"".$stateArray[$b]."\" selected>".$stateArray[$b]." </option>\n";
						}else{
							echo "<option value = \"".$stateArray[$b]."\">".$stateArray[$b]."</option>\n";
						}
					}
					echo "</select>\n";
					echo "</td>\n";
				}else{
					if($cellName[$a] != "Street Address 2"){
						echo "<td> ".$cellName[$a]." &nbsp;&nbsp;&nbsp;<input type = \"text\" name = \"".$fieldName[$a]."\" value = \"".makeStrUc($inputArray[$a])."\"></td>\n";
					}else{
						echo "<td> ".$cellName[$a]." <input type = \"text\" name = \"".$fieldName[$a]."\" value = \"".makeStrUc($inputArray[$a])."\" size = 30></td>\n";
					}
				}
			}
			echo "</tr>\n";
		// print out the phone numbers
		}elseif($a>=11 && $a<16){
			if($a % 2 !=0){
				echo "<tr>\n";
				if($cellName[$a] == "Cell Phone"){
					echo "<td align = \"left\"> ".$cellName[$a]."&nbsp;&nbsp; <input type = \"text\" name = \"".$fieldName[$a]."\" value = \"".$inputArray[$a]."\"></td>\n";
				}elseif($cellName[$a] == "Preferred Phone General"){
					echo "<td align = \"left\" colspan = 2> ".$cellName[$a]."\n";
					echo "<select name = \"".$fieldName[$a]."\">\n";
					for($b =0; $b<count($phoneArray); $b++){
						if($phoneArray[$b] == $inputArray[$a]){
							echo "<option value = \"".$phoneArray[$b]."\" selected>".$phoneArray[$b]." </option>\n";
						}else{
							echo "<option value = \"".$phoneArray[$b]."\">".$phoneArray[$b]." </option>\n";
						}
					}
					echo "</select>\n";
					echo "</td>\n";
					echo "</tr>\n";
				}else{
					echo "<td align = \"left\"> ".$cellName[$a]." <input type = \"text\" name = \"".$fieldName[$a]."\" value = \"".$inputArray[$a]."\"></td>\n";
				}
			}else{
				echo "<td align = \"left\"> ".$cellName[$a]." &nbsp;&nbsp;&nbsp;&nbsp;<input type = \"text\" name = \"".$fieldName[$a]."\" value = \"".$inputArray[$a]."\"></td>\n";
				echo "</tr>\n";
			}
		// print out the email and notes
		}else{
			if($cellName[$a] == "Notes"){
				echo "<tr>\n";
				echo "<td align = \"left\" colspan =2 valign = \"top\">".$cellName[$a]." &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				      <textarea name=\"".$fieldName[$a]."\" cols=50 rows=3 wrap>".str_replace('\\', '', $inputArray[$a])." </textarea></td>\n";
				echo "</tr>\n";
			}else{
				if($cellName[$a] == "Email"){
					echo "<tr>\n";
					echo "<td align = \"left\"> ".$cellName[$a]." <br>
					      <input type = \"text\" name = \"".$fieldName[$a]."\" value = \"".$inputArray[$a]."\" size = 45></td>\n";
				}else{
					echo "<td align = \"left\"> ".$cellName[$a]." <br><input type = \"text\" name = \"".$fieldName[$a]."\" value = \"".$inputArray[$a]."\" size = 45></td>\n";
					echo "</tr>\n";
				}
			}
		}
	}
}

echo "<tr>\n";
echo "<td><input type = \"submit\" value = \"Save Record\" onClick=\"submitPt();\" ></td>\n";
if($heID){ ?>
</form><td><form action="edit_contact.php" method="GET"><input type="hidden" name="partID" value="<?php echo (isset($_REQUEST['partID']))?$_REQUEST['partID']:'';?>"><input type="hidden" name="heID" value="<?php echo $heID; ?>"><input type="submit" value="Edit Alternative Contact"<?php if (!$succMsg || $succMsg == "") { ?> onclick="return checkFirst();"<?php } ?>></form></td>
<?php } else { ?>
</form><td><form action="edit_appt.php" method="POST">
<input type="hidden" name="partID" value="<?php echo (isset($_REQUEST['partID']))?$_REQUEST['partID']:'';?>">
<input type="hidden" name="ptFName" value="<?php echo (isset($_REQUEST['ptFName']))?makeStrUc($_REQUEST['ptFName']):'';?>">
<input type="hidden" name="ptLName" value="<?php echo (isset($_REQUEST['ptLName']))?makeStrUc($_REQUEST['ptLName']):'';?>">
<input type = "submit" name="action" value="Edit Appointment"<?php if (!$succMsg || $succMsg == "") { ?> onclick="return checkFirst();"<?php } ?>>
</td>
<?php }
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
