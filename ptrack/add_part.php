<?php
// LOAD mySQL FUNCTIONS
include("includes/connection.php");
$errMsg="";

// if logging out, exit 
if ($_POST["isSubmittedLogOut"]==1) {
	header("Location: login.php");
	exit();
}

if ( isset($_POST['mode']) && $_POST['mode'] == 'submitAppt') {
// send them to the appointment page with the correct MRN attached 
	$url = "Location: add_appt.php?MRN=".$_POST["MRN"];
	header($url);
	exit();
}

// display page header 
?>
<!DOCTYPE html PUBLIC "-//W3C//ulD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/ulD/xhtml1-strict.uld">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Add Participant</title>
<link rel="stylesheet" type="text/css" href="includes/main.css" />
<script>
<!-- 
	function checkMRN() {
		var mrn1 = document["form1"]["MRN"].value;
		var mrn2 = document["form1"]["MRN2"].value;
		if (mrn1 != mrn2) {
			alert('The MRNs you entered do not match. Please check them.');
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
		<h2 align = "center"> Add Participants</h2>  
			<br><p align = "right"><form method="POST" action="login.php" >
	     <input type = "submit" value="log out" size = 100></form></p> 
		 </div>
		 <!-- End Header -->
		 
         <!-- Begin Faux Columns -->
		 <div id="faux">
		 
		       <!-- Begin Left Column -->
		       <div id="leftcolumn1">
<?php $menu = getMenu();
	  echo $menu;
?>
		       </div>

		       <!-- End Left Column -->
			   
			   <!-- Begin Right Column -->
		       <div id="rightcolumn">
<?php
// CONNECT TO DATABASE
if ($mysqlID=dbConnect()) {
    selectDatabase("lung_cancer_user");
} else {
    $errMsg.="<b>ERROR:</b> ".mysql_error()."<br>\n";
}

if ( isset($_POST['mode']) && $_POST['mode'] == 'submitPt') {
// form submitted -> yes: 
// -- declare necessary variables
$cellName = array("MRN" => "MRN", "MRN2" => "Re-enter MRN", "ptFName" => "First Name", "ptLName" => "Last Name", 
"gender" => "Gender", "dob" => "DOB (mm/dd/yyyy)", "ptAddress1" => "Street Address 1", "ptAddress2" => "Street Address 2", "ptCity" => "City", "ptState" => "State", "ptZip" => "Zip", "ptHPhone" => "Home Phone", "ptWPhone" => "Work Phone", "ptCPhone" => "Cell Phone", "ptOPhone" => "Other Phone", 
"ptEmail" => "Email", "re-email" => "Re-enter Email", "notes" => "Notes");
// -- check for required fields, skip initial check for the fields for now 
$notreqd = array('ptAddress2', 'notes', 'ptEmail', 're-email', 'ptHPhone', 'ptWPhone', 'ptCPhone', 'ptOPhone');
foreach ($_POST as $var => $val) {
	if ( !in_array($var, $notreqd) ) {
		if ($val == '') { $errMsg .= "<b><font color='red'>ERROR: Please enter ".$cellName[$var]."!!</font></b><BR>\n"; }
	}
}

// -- grab form data and clean it
foreach ($_POST as $var => $val) {
	$_POST[$var] = cleaned($val);
}// end cleaning of POST vars

// -- -- DOB formatting
if ($errMsg == '') {
// -- -- -- no missing fields, continue validation 
if ( !validDOB($_POST['dob']) ) { 
	$errMsg .= "<b><font color='red'>Please enter the DOB in 'mm/dd/yyyy' format</font></b><br>\n"; }
// -- -- Zip formatting
if ( !validZip($_POST['ptZip']) ) { 
	$errMsg .= "<b><font color='red'>The zip code is invalid.</font></b><br>\n"; }
// -- -- phone formatting
// -- MUST HAVE AT LEAST ONE -- 
if ($_POST['ptHPhone'] == '' && $_POST['ptWPhone'] == '' && $_POST['ptCPhone'] == '' && $_POST['ptOPhone'] == '') {
	$errMsg .= "<b><font color='red'>Please enter at least one phone number. Enter as 111-222-3456</font></b><br>\n"; 
}
if ( isset($_POST['ptHPhone']) && $_POST['ptHPhone'] != '' && !validPhone($_POST['ptHPhone']) ) { 
	$errMsg .= "<b><font color='red'>Please enter the home phone as 111-222-3456</font></b><br>\n"; }
if ( isset($_POST['ptWPhone']) && $_POST['ptWPhone'] != '' &&  !validPhone($_POST['ptWPhone']) ) { 
	$errMsg .= "<b><font color='red'>Please enter the work phone as 111-222-3456</font></b><br>\n"; }
if ( isset($_POST['ptCPhone']) && $_POST['ptCPhone'] != '' &&  !validPhone($_POST['ptCPhone']) ) { 
	$errMsg .= "<b><font color='red'>Please enter the cell phone as 111-222-3456</font></b><br>\n"; }
if ( isset($_POST['ptOPhone']) && $_POST['ptOPhone'] != '' &&  !validPhone($_POST['ptOPhone']) ) { 
	$errMsg .= "<b><font color='red'>Please enter the other phone as 111-222-3456</font></b><br>\n"; }
// -- OPTIONAL -- email formatting 
if ( isset($_POST['ptEmail']) && $_POST['ptEmail'] != '' ) {
	if ( $_POST['re-email'] == '' ) { 
		$errMsg .= "<b><font color='red'>Please re-enter email address.</font></b><br>\n";
	}
	//if ( !validEmail($_POST['ptEmail'], $_POST['re-email']) ) { 
		//$errMsg .= "<b><font color='red'>Please enter a valid email.</font></b><br>\n"; 
	//}
	if(trim($_POST["ptEmail"]) != trim($_POST["re-email"])){
			$errMsg = "<b><font color = 'red'>ERROR: The re-entered email is different from the email!!</font></b><br>\n";
			$emailFlg =1;
	}else{
		//then check to see if email is in correct formate
		$emailFlg = checkValidEmail(trim($_POST["ptEmail"]));
		if ($emailFlg){
		   $errMsg = "<b><font color = 'red'>ERROR: ".$emailErrMsg."!!</font></b><br>\n";
		}else{
			// check if the email already in the db
			$emailFlg = getEmail($partID, trim($_POST["ptEmail"]));
			if ($emailFlg){
			    $errMsg = "<b><font color = 'red'>ERROR: ".$errDupMsg."!!</font></b><br>\n";
			}
		}
	}
}
// double check MRN 
if ( !isset($_POST['MRN2']) || $_POST['MRN2'] == '' ) { 
	$errMsg .= "<b><font color='red'>Please re-enter the MRN.</font></b><br>\n";
}
if ( $_POST['MRN'] != $_POST['MRN2'] ) { 
	$errMsg .= "<b><font color='red'>Please check the MRNs you entered.</font></b><br>\n"; 
}

// -- check data against database 
// -- -- Verify that MRN is not already in the db 
if ( !validMRN($_POST['MRN']) ) { $errMsg .= "<b><font color='red'>This MRN number is already in use.</font></b><br>\n"; } 
// -- OPTIONAL -- Verify that email is not already in the db
if ( isset($_POST['ptEmail']) && $_POST['ptEmail'] != '' ) {
	if ( !validEmail2($_POST['ptEmail']) ) { $errMsg .= "<b><font color='red'>The email address you entered is already in the database.</font></b><br>\n"; }
}
}

if ($errMsg == '') { 
// -- if data is ok:
// -- -- submit new record to database
$today = date("Y-m-d");
if ($_POST["dob"]) {
	$dob = formatDate($_POST["dob"], 1);
} else {
	$dob = "0000-00-00";
}

$gender = substr($_POST["gender"], 0, 1);
$fname = str_replace('\\', '', $_POST["ptFName"]);
$lname = str_replace('\\', '', $_POST["ptLName"]); // remove slashes 
$_POST["ptEmail"] = str_replace('\\', '', $_POST["ptEmail"]);
$_POST["re-email"] = str_replace('\\', '', $_POST["re-email"]);
$state = convertState2Abbrev($_POST["ptState"]);
$notes = str_replace('\\', '', $notes); // remove slashes 
$istFlg = insertPtInfo($_POST["MRN"], $fname, $lname, 
	      $gender, $dob, $_POST["ptAddress1"], $_POST["ptAddress2"], 
          $_POST["ptCity"], $state, $_POST["ptZip"], 
		  $_POST["ptHPhone"], $_POST["ptWPhone"], $_POST["ptCPhone"], 
		  $_POST["ptOPhone"], $_POST["ptEmail"], $notes, $today);

if ($istFlg) {
	$istErr="<b><font color='red'>ERROR:".$errIstMsg."</font><br>\n";
} else {
	$succMsg = "<font color='green' size=\"+1\">The following participant has been successfully saved in the database!</font><br>\n";
}

// -- -- display success message
if ($succMsg!="") { 
	echo $succMsg; 
	echo '<p>If you have entered any of the participant information incorrectly and need to edit the record, please <a href="part_list.php?ptLName='.$_POST['ptLName'].'&action=participants">click here</a>. Otherwise, please click on the "Add Appointment" button below to continue.</p><br>';
}
// -- -- display form and activate "add appointment button"
// -- -- make MRN field disabled and add it to a hidden field
?>
<form name = form1 method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<input type="hidden" name="mode" value="submitAppt" />
<input type="hidden" name="MRN" value="<?php echo $_POST['MRN']; ?>" />
<table border = 5 align = "left" valign = "top" cellpadding =8>
<tr>
<td align = "left"> MRN <input type="text" name="XMRN" value="<?php echo $_POST['MRN']; ?>" disabled></td>
<td align="left"> Re-enter MRN <input type="text" name="MRN2" value="<?php echo $_POST['MRN2']; ?>" disabled>
</tr>
<tr>
<td align = "left"> First Name <input type="text" name="ptFName" value="<?php echo str_replace('\\', '', $_POST['ptFName']); ?>"></td>
<td align = "left"> Last Name <input type="text" name="ptLName" value="<?php echo str_replace('\\', '', $_POST['ptLName']); ?>"></td>
</tr>
<tr>
<td align = "left"> Gender &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<select name="gender">
<option value=" ">&nbsp;&nbsp;   &nbsp;&nbsp;</option>
<option value="Male"<?php if ($_POST['gender'] == 'Male') { echo ' selected'; } ?>>Male &nbsp;&nbsp;</option>
<option value="Female"<?php if ($_POST['gender'] == 'Female') { echo ' selected'; } ?>>Female &nbsp;&nbsp;</option>
</select>
</td>
<td align = "left"> DOB (mm/dd/yyyy)<input type="text" name="dob" value="<?php echo $_POST['dob']; ?>"></td>
</tr>
<tr>
<td rowspan = 5 align = "center">Home Address</td>
<td> Street Address 1 <input type="text" name="ptAddress1" value="<?php echo $_POST['ptAddress1']; ?>" size = 30></td>
</tr>
<tr>
<td> Street Address 2 <input type="text" name="ptAddress2" value="<?php echo $_POST['ptAddress2']; ?>" size = 30></td>
</tr>
<tr>
<td> City &nbsp;&nbsp;&nbsp;<input type="text" name="ptCity" value="<?php echo $_POST['ptCity']; ?>"></td>
</tr>
<tr>
<td align = "left">State 
<select name="ptState">
<option value=""></option>
<option value="Alabama"<?php if ($_POST['ptState'] == 'Alabama') { echo ' selected'; } ?>>AL</option>
<option value="Alaska"<?php if ($_POST['ptState'] == 'Alaska') { echo ' selected'; } ?>>AK</option>
<option value="Arizona"<?php if ($_POST['ptState'] == 'Arizona') { echo ' selected'; } ?>>AZ</option>
<option value="Arkansas"<?php if ($_POST['ptState'] == 'Arkansas') { echo ' selected'; } ?>>AR</option>
<option value="California"<?php if ($_POST['ptState'] == 'California') { echo ' selected'; } ?>>CA</option>
<option value="Colorado"<?php if ($_POST['ptState'] == 'Colorado') { echo ' selected'; } ?>>CO</option>
<option value="Connecticut"<?php if ($_POST['ptState'] == 'Connecticut') { echo ' selected'; } ?>>CT</option>
<option value="Delaware"<?php if ($_POST['ptState'] == 'Delaware') { echo ' selected'; } ?>>DE</option>
<option value="District of Columbia"<?php if ($_POST['ptState'] == 'District of Columbia') { echo ' selected'; } ?>>DC</option>
<option value="Florida"<?php if ($_POST['ptState'] == 'Florida') { echo ' selected'; } ?>>FL</option>
<option value="Georgia"<?php if ($_POST['ptState'] == 'Georgia') { echo ' selected'; } ?>>GA</option>
<option value="Hawaii"<?php if ($_POST['ptState'] == 'Hawaii') { echo ' selected'; } ?>>HI</option>
<option value="Idaho"<?php if ($_POST['ptState'] == 'Idaho') { echo ' selected'; } ?>>ID</option>
<option value="Illinois"<?php if ($_POST['ptState'] == 'Illinois') { echo ' selected'; } ?>>IL</option>
<option value="Indiana"<?php if ($_POST['ptState'] == 'Indiana') { echo ' selected'; } ?>>IN</option>
<option value="Iowa"<?php if ($_POST['ptState'] == 'Iowa') { echo ' selected'; } ?>>IA</option>
<option value="Kansas"<?php if ($_POST['ptState'] == 'Kansas') { echo ' selected'; } ?>>KS</option>
<option value="Kentucky"<?php if ($_POST['ptState'] == 'Kentucky') { echo ' selected'; } ?>>KY</option>
<option value="Louisiana"<?php if ($_POST['ptState'] == 'Louisiana') { echo ' selected'; } ?>>LA</option>
<option value="Maine"<?php if ($_POST['ptState'] == 'Maine') { echo ' selected'; } ?>>ME</option>
<option value="Maryland"<?php if ($_POST['ptState'] == 'Maryland') { echo ' selected'; } ?>>MD</option>
<option value="Massachusetts"<?php if ($_POST['ptState'] == 'Massachusetts') { echo ' selected'; } ?>>MA</option>
<option value="Michigan"<?php if ($_POST['ptState'] == 'Michigan') { echo ' selected'; } ?>>MI</option>
<option value="Minnesota"<?php if ($_POST['ptState'] == 'Minnesota') { echo ' selected'; } ?>>MN</option>
<option value="Mississippi"<?php if ($_POST['ptState'] == 'Mississippi') { echo ' selected'; } ?>>MS</option>
<option value="Missouri"<?php if ($_POST['ptState'] == 'Missouri') { echo ' selected'; } ?>>MO</option>
<option value="Montana"<?php if ($_POST['ptState'] == 'Montana') { echo ' selected'; } ?>>MT</option>
<option value="Nebraska"<?php if ($_POST['ptState'] == 'Nebraska') { echo ' selected'; } ?>>NE</option>
<option value="Nevada"<?php if ($_POST['ptState'] == 'Nevada') { echo ' selected'; } ?>>NV</option>
<option value="New Hampshire"<?php if ($_POST['ptState'] == 'New Hampshire') { echo ' selected'; } ?>>NH</option>
<option value="New Jersey"<?php if ($_POST['ptState'] == 'New Jersey') { echo ' selected'; } ?>>NJ</option>
<option value="New Mexico"<?php if ($_POST['ptState'] == 'New Mexico') { echo ' selected'; } ?>>NM</option>
<option value="New York"<?php if ($_POST['ptState'] == 'New York') { echo ' selected'; } ?>>NY</option>
<option value="North Carolina"<?php if ($_POST['ptState'] == 'North Carolina') { echo ' selected'; } ?>>NC</option>
<option value="North Dakota"<?php if ($_POST['ptState'] == 'North Dakota') { echo ' selected'; } ?>>ND</option>
<option value="Ohio"<?php if ($_POST['ptState'] == 'Ohio') { echo ' selected'; } ?>>OH</option>
<option value="Oklahoma"<?php if ($_POST['ptState'] == 'Oklahoma') { echo ' selected'; } ?>>OK</option>
<option value="Oregon"<?php if ($_POST['ptState'] == 'Oregon') { echo ' selected'; } ?>>OR</option>
<option value="Pennsylvania"<?php if ($_POST['ptState'] == 'Pennsylvania') { echo ' selected'; } ?>>PA</option>
<option value="Rhode Island"<?php if ($_POST['ptState'] == 'Rhode Island') { echo ' selected'; } ?>>RI</option>
<option value="South Carolina"<?php if ($_POST['ptState'] == 'South Carolina') { echo ' selected'; } ?>>SC</option>
<option value="South Dakota"<?php if ($_POST['ptState'] == 'South Dakota') { echo ' selected'; } ?>>SD</option>
<option value="Tennessee"<?php if ($_POST['ptState'] == 'Tennessee') { echo ' selected'; } ?>>TN</option>
<option value="Texas"<?php if ($_POST['ptState'] == 'Texas') { echo ' selected'; } ?>>TX</option>
<option value="Utah"<?php if ($_POST['ptState'] == 'Utah') { echo ' selected'; } ?>>UT</option>
<option value="Vermont"<?php if ($_POST['ptState'] == 'Vermont') { echo ' selected'; } ?>>VT</option>
<option value="Virginia"<?php if ($_POST['ptState'] == 'Virginia') { echo ' selected'; } ?>>VA</option>
<option value="Washington"<?php if ($_POST['ptState'] == 'Washington') { echo ' selected'; } ?>>WA</option>
<option value="West Virginia"<?php if ($_POST['ptState'] == 'West Virginia') { echo ' selected'; } ?>>WV</option>
<option value="Wisconsin"<?php if ($_POST['ptState'] == 'Wisconsin') { echo ' selected'; } ?>>WI</option>
<option value="Wyoming"<?php if ($_POST['ptState'] == 'Wyoming') { echo ' selected'; } ?>>WY</option>
</select>
</td>
</tr>
<tr>
<td> Zip &nbsp;&nbsp;&nbsp;<input type="text" name="ptZip" value="<?php echo $_POST['ptZip']; ?>"></td>
</tr>
<tr>
<td align = "left"> Home Phone <input type="text" name="ptHPhone" value="<?php echo $_POST['ptHPhone']; ?>"></td>
<td align = "left"> Work Phone &nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="ptWPhone" value="<?php echo $_POST['ptWPhone']; ?>"></td>
</tr>
<tr>
<td align = "left"> Cell Phone&nbsp;&nbsp; <input type="text" name="ptCPhone" value="<?php echo $_POST['ptCPhone']; ?>"></td>
<td align = "left"> Other Phone &nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="ptOPhone" value="<?php echo $_POST['ptOPhone']; ?>"></td>
</tr>
<tr>
<td align = "left"> Email<br><input type="text" name="ptEmail" value="<?php echo str_replace('\\', '', $_POST["ptEmail"]); ?>" size = 45></td>
<td align = "left"> Re-enter Email<br><input type="text" name="re-email" value="<?php echo str_replace('\\', '', $_POST['re-email']); ?>" size = 45></td>
</tr>
<tr>
<td align = "left" colspan =2 valign = "top">Notes &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<textarea name="notes" cols=50 rows=3 wrap><?php echo str_replace('\\', '', $_POST['notes']); ?></textarea></td>
</tr>
<tr>
<td><input type = "submit" value="Add participant" onClick="submitPt();" disabled>
<td><input type = "submit" value="Add Appointment" onClick="submitAppt();" enabled></td>
</tr>
</table>
</form>	
<?php
} else {
// -- if data is not ok:
// -- -- display form with error message and pre-populate fields 
echo '<p><b>'.$errMsg.'</b></p>';
?>
<form name = form1 method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<input type="hidden" name="mode" value="submitPt" />
<table border = 5 align = "left" valign = "top" cellpadding =8>
<tr>
<td align="left"> MRN <input type="text" name="MRN" value="<?php echo $_POST['MRN']; ?>"></td>
<td align="left"> Re-enter MRN <input type="text" name="MRN2" value="<?php echo $_POST['MRN2']; ?>" onchange="checkMRN();">
</tr>
<tr>
<td align = "left"> First Name <input type="text" name="ptFName" value="<?php echo str_replace('\\', '', $_POST['ptFName']); ?>"></td>
<td align = "left"> Last Name <input type="text" name="ptLName" value="<?php echo str_replace('\\', '', $_POST['ptLName']); ?>"></td>
</tr>
<tr>
<td align = "left"> Gender &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<select name="gender">
<option value=" ">&nbsp;&nbsp;   &nbsp;&nbsp;</option>
<option value="Male"<?php if ($_POST['gender'] == 'Male') { echo ' selected'; } ?>>Male &nbsp;&nbsp;</option>
<option value="Female"<?php if ($_POST['gender'] == 'Female') { echo ' selected'; } ?>>Female &nbsp;&nbsp;</option></select>
</td>
<td align = "left"> DOB (mm/dd/yyyy)<input type="text" name="dob" value="<?php echo $_POST['dob']; ?>"></td>
</tr>
<tr>
<td rowspan = 5 align = "center">Home Address</td>
<td> Street Address 1 <input type="text" name="ptAddress1" value="<?php echo $_POST['ptAddress1']; ?>" size = 30></td>
</tr>
<tr>
<td> Street Address 2 <input type="text" name="ptAddress2" value="<?php echo $_POST['ptAddress2']; ?>" size = 30></td>
</tr>
<tr>
<td> City &nbsp;&nbsp;&nbsp;<input type="text" name="ptCity" value="<?php echo $_POST['ptCity']; ?>"></td>
</tr>
<tr>
<td align = "left">State 
<select name="ptState">
<option value=""></option>
<option value="Alabama"<?php if ($_POST['ptState'] == 'Alabama') { echo ' selected'; } ?>>AL</option>
<option value="Alaska"<?php if ($_POST['ptState'] == 'Alaska') { echo ' selected'; } ?>>AK</option>
<option value="Arizona"<?php if ($_POST['ptState'] == 'Arizona') { echo ' selected'; } ?>>AZ</option>
<option value="Arkansas"<?php if ($_POST['ptState'] == 'Arkansas') { echo ' selected'; } ?>>AR</option>
<option value="California"<?php if ($_POST['ptState'] == 'California') { echo ' selected'; } ?>>CA</option>
<option value="Colorado"<?php if ($_POST['ptState'] == 'Colorado') { echo ' selected'; } ?>>CO</option>
<option value="Connecticut"<?php if ($_POST['ptState'] == 'Connecticut') { echo ' selected'; } ?>>CT</option>
<option value="Delaware"<?php if ($_POST['ptState'] == 'Delaware') { echo ' selected'; } ?>>DE</option>
<option value="District of Columbia"<?php if ($_POST['ptState'] == 'District of Columbia') { echo ' selected'; } ?>>DC</option>
<option value="Florida"<?php if ($_POST['ptState'] == 'Florida') { echo ' selected'; } ?>>FL</option>
<option value="Georgia"<?php if ($_POST['ptState'] == 'Georgia') { echo ' selected'; } ?>>GA</option>
<option value="Hawaii"<?php if ($_POST['ptState'] == 'Hawaii') { echo ' selected'; } ?>>HI</option>
<option value="Idaho"<?php if ($_POST['ptState'] == 'Idaho') { echo ' selected'; } ?>>ID</option>
<option value="Illinois"<?php if ($_POST['ptState'] == 'Illinois') { echo ' selected'; } ?>>IL</option>
<option value="Indiana"<?php if ($_POST['ptState'] == 'Indiana') { echo ' selected'; } ?>>IN</option>
<option value="Iowa"<?php if ($_POST['ptState'] == 'Iowa') { echo ' selected'; } ?>>IA</option>
<option value="Kansas"<?php if ($_POST['ptState'] == 'Kansas') { echo ' selected'; } ?>>KS</option>
<option value="Kentucky"<?php if ($_POST['ptState'] == 'Kentucky') { echo ' selected'; } ?>>KY</option>
<option value="Louisiana"<?php if ($_POST['ptState'] == 'Louisiana') { echo ' selected'; } ?>>LA</option>
<option value="Maine"<?php if ($_POST['ptState'] == 'Maine') { echo ' selected'; } ?>>ME</option>
<option value="Maryland"<?php if ($_POST['ptState'] == 'Maryland') { echo ' selected'; } ?>>MD</option>
<option value="Massachusetts"<?php if ($_POST['ptState'] == 'Massachusetts') { echo ' selected'; } ?>>MA</option>
<option value="Michigan"<?php if ($_POST['ptState'] == 'Michigan') { echo ' selected'; } ?>>MI</option>
<option value="Minnesota"<?php if ($_POST['ptState'] == 'Minnesota') { echo ' selected'; } ?>>MN</option>
<option value="Mississippi"<?php if ($_POST['ptState'] == 'Mississippi') { echo ' selected'; } ?>>MS</option>
<option value="Missouri"<?php if ($_POST['ptState'] == 'Missouri') { echo ' selected'; } ?>>MO</option>
<option value="Montana"<?php if ($_POST['ptState'] == 'Montana') { echo ' selected'; } ?>>MT</option>
<option value="Nebraska"<?php if ($_POST['ptState'] == 'Nebraska') { echo ' selected'; } ?>>NE</option>
<option value="Nevada"<?php if ($_POST['ptState'] == 'Nevada') { echo ' selected'; } ?>>NV</option>
<option value="New Hampshire"<?php if ($_POST['ptState'] == 'New Hampshire') { echo ' selected'; } ?>>NH</option>
<option value="New Jersey"<?php if ($_POST['ptState'] == 'New Jersey') { echo ' selected'; } ?>>NJ</option>
<option value="New Mexico"<?php if ($_POST['ptState'] == 'New Mexico') { echo ' selected'; } ?>>NM</option>
<option value="New York"<?php if ($_POST['ptState'] == 'New York') { echo ' selected'; } ?>>NY</option>
<option value="North Carolina"<?php if ($_POST['ptState'] == 'North Carolina') { echo ' selected'; } ?>>NC</option>
<option value="North Dakota"<?php if ($_POST['ptState'] == 'North Dakota') { echo ' selected'; } ?>>ND</option>
<option value="Ohio"<?php if ($_POST['ptState'] == 'Ohio') { echo ' selected'; } ?>>OH</option>
<option value="Oklahoma"<?php if ($_POST['ptState'] == 'Oklahoma') { echo ' selected'; } ?>>OK</option>
<option value="Oregon"<?php if ($_POST['ptState'] == 'Oregon') { echo ' selected'; } ?>>OR</option>
<option value="Pennsylvania"<?php if ($_POST['ptState'] == 'Pennsylvania') { echo ' selected'; } ?>>PA</option>
<option value="Rhode Island"<?php if ($_POST['ptState'] == 'Rhode Island') { echo ' selected'; } ?>>RI</option>
<option value="South Carolina"<?php if ($_POST['ptState'] == 'South Carolina') { echo ' selected'; } ?>>SC</option>
<option value="South Dakota"<?php if ($_POST['ptState'] == 'South Dakota') { echo ' selected'; } ?>>SD</option>
<option value="Tennessee"<?php if ($_POST['ptState'] == 'Tennessee') { echo ' selected'; } ?>>TN</option>
<option value="Texas"<?php if ($_POST['ptState'] == 'Texas') { echo ' selected'; } ?>>TX</option>
<option value="Utah"<?php if ($_POST['ptState'] == 'Utah') { echo ' selected'; } ?>>UT</option>
<option value="Vermont"<?php if ($_POST['ptState'] == 'Vermont') { echo ' selected'; } ?>>VT</option>
<option value="Virginia"<?php if ($_POST['ptState'] == 'Virginia') { echo ' selected'; } ?>>VA</option>
<option value="Washington"<?php if ($_POST['ptState'] == 'Washington') { echo ' selected'; } ?>>WA</option>
<option value="West Virginia"<?php if ($_POST['ptState'] == 'West Virginia') { echo ' selected'; } ?>>WV</option>
<option value="Wisconsin"<?php if ($_POST['ptState'] == 'Wisconsin') { echo ' selected'; } ?>>WI</option>
<option value="Wyoming"<?php if ($_POST['ptState'] == 'Wyoming') { echo ' selected'; } ?>>WY</option>
</select>
</td>
</tr>
<tr>
<td> Zip &nbsp;&nbsp;&nbsp;<input type="text" name="ptZip" value="<?php echo $_POST['ptZip']; ?>"></td>
</tr>
<tr>
<td align = "left"> Home Phone <input type="text" name="ptHPhone" value="<?php echo $_POST['ptHPhone']; ?>"></td>
<td align = "left"> Work Phone &nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="ptWPhone" value="<?php echo $_POST['ptWPhone']; ?>"></td>
</tr>
<tr>
<td align = "left"> Cell Phone&nbsp;&nbsp; <input type="text" name="ptCPhone" value="<?php echo $_POST['ptCPhone']; ?>"></td>
<td align = "left"> Other Phone &nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="ptOPhone" value="<?php echo $_POST['ptOPhone']; ?>"></td>
</tr>
<tr>
<td align = "left"> Email <br><input type="text" name="ptEmail" value="<?php echo str_replace('\\', '', $_POST["ptEmail"]); ?>" size = 45></td>
<td align = "left"> Re-enter Email<br><input type="text" name="re-email" value="<?php echo str_replace('\\', '', $_POST['re-email']); ?>" size = 45></td>
</tr>
<tr>
<td align = "left" colspan =2 valign = "top">Notes &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<textarea name="notes" cols=50 rows=3 wrap><?php echo str_replace('\\', '', $_POST['notes']); ?></textarea></td>
</tr>
<tr>
<td><input type = "submit" value="Add participant" onClick="submitPt();">
<td><input type = "submit" value="Add Appointment" onClick="submitAppt();" disabled></td>
</tr>
</table>
</form>	
<?php
} // end of if data is ok or not 

} else {
// form submitted -> no, display empty table 
?>
<form name = form1 method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<input type="hidden" name="mode" value="submitPt" />
<table border = 5 align = "left" valign = "top" cellpadding =8>
<tr>
<td align = "left"> MRN <input type="text" name="MRN" value=""></td>
<td align="left"> Re-enter MRN <input type="text" name="MRN2" value="" onchange="checkMRN();">
</tr>
<tr>
<td align = "left"> First Name <input type="text" name="ptFName" value=""></td>
<td align = "left"> Last Name <input type="text" name="ptLName" value=""></td>
</tr>
<tr>
<td align = "left"> Gender &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<select name="gender">
<option value=" ">&nbsp;&nbsp;   &nbsp;&nbsp;</option><option value="Male">Male &nbsp;&nbsp;</option><option value="Female">Female &nbsp;&nbsp;</option></select>
</td>
<td align = "left"> DOB (mm/dd/yyyy)<input type="text" name="dob" value=""></td>
</tr>
<tr>
<td rowspan = 5 align = "center">Home Address</td>
<td> Street Address 1 <input type="text" name="ptAddress1" value="" size = 30></td>
</tr>
<tr>
<td> Street Address 2 <input type="text" name="ptAddress2" value="" size = 30></td>
</tr>
<tr>
<td> City &nbsp;&nbsp;&nbsp;<input type="text" name="ptCity" value=""></td>
</tr>
<tr>
<td align = "left">State 
<select name="ptState">
<option value=""></option>
<option value="Alabama">AL</option>
<option value="Alaska">AK</option>
<option value="Arizona">AZ</option>
<option value="Arkansas">AR</option>
<option value="California">CA</option>
<option value="Colorado">CO</option>
<option value="Connecticut">CT</option>
<option value="Delaware">DE</option>
<option value="District of Columbia">DC</option>
<option value="Florida">FL</option>
<option value="Georgia">GA</option>
<option value="Hawaii">HI</option>
<option value="Idaho">ID</option>
<option value="Illinois">IL</option>
<option value="Indiana">IN</option>
<option value="Iowa">IA</option>
<option value="Kansas">KS</option>
<option value="Kentucky">KY</option>
<option value="Louisiana">LA</option>
<option value="Maine">ME</option>
<option value="Maryland">MD</option>
<option value="Massachusetts">MA</option>
<option value="Michigan">MI</option>
<option value="Minnesota">MN</option>
<option value="Mississippi">MS</option>
<option value="Missouri">MO</option>
<option value="Montana">MT</option>
<option value="Nebraska">NE</option>
<option value="Nevada">NV</option>
<option value="New Hampshire">NH</option>
<option value="New Jersey">NJ</option>
<option value="New Mexico">NM</option>
<option value="New York">NY</option>
<option value="North Carolina">NC</option>
<option value="North Dakota">ND</option>
<option value="Ohio">OH</option>
<option value="Oklahoma">OK</option>
<option value="Oregon">OR</option>
<option value="Pennsylvania">PA</option>
<option value="Rhode Island">RI</option>
<option value="South Carolina">SA</option>
<option value="South Dakota">SD</option>
<option value="Tennessee">TN</option>
<option value="Texas">TX</option>
<option value="Utah">UT</option>
<option value="Vermont">VT</option>
<option value="Virginia">VA</option>
<option value="Washington">WA</option>
<option value="West Virginia">WV</option>
<option value="Wisconsin">WI</option>
<option value="Wyoming">WY</option>
</select>
</td>
</tr>
<tr>
<td> Zip &nbsp;&nbsp;&nbsp;<input type="text" name="ptZip" value=""></td>
</tr>
<tr>
<td align = "left"> Home Phone <input type="text" name="ptHPhone" value=""></td>
<td align = "left"> Work Phone &nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="ptWPhone" value=""></td>
</tr>
<tr>
<td align = "left"> Cell Phone&nbsp;&nbsp; <input type="text" name="ptCPhone" value=""></td>
<td align = "left"> Other Phone &nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="ptOPhone" value=""></td>
</tr>
<tr>
<td align = "left"> Email<br><input type="text" name="ptEmail" value="" size = 45></td>
<td align = "left"> Re-enter Email<br><input type="text" name="re-email" value="" size = 45></td>
</tr>
<tr>
<td align = "left" colspan =2 valign = "top">Notes &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<textarea name="notes" cols=50 rows=3 wrap></textarea></td>
</tr>
<tr>
<td><input type = "submit" value="Add participant" onClick="submitPt();" enabled>
<td><input type = "submit" value="Add Appointment" onClick="submitAppt();" disabled></td>
</tr>
</table>
</form>	 
<?php
} // end of if else form submitted 

// display the bottom of the page 
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
<?php
function cleaned($str) {
	$str = strip_tags($str); // remove php and html tags 
	$str = trim($str); // remove excess whitespace
	$str = mysql_escape_string($str); // escape for the database 
	return $str;
}
// formatting functions 
function validDOB($str) {
	$ret = checkValidDate($str);
	if ($ret == 1) { return FALSE; } else { return TRUE; }
}

function validZip($str) {
	$ret = checkValidZip($_POST["ptZip"]);
	if ($ret == 1) { return FALSE; } else { return TRUE; }
}

function validPhone($str) {
	$ret = checkValidPhone($str);
	if ($ret == 1) { return FALSE; } else { return TRUE; }
}

function validEmail($str, $str2) {
	if ($str != $str2) {
		return FALSE;
	} else {
		return ( !preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $str)) ? FALSE : TRUE;
	}
}

// database check functions
function validMRN($str) {
	$sql = "SELECT COUNT(*) as total FROM part_info WHERE MRN = '$str'";
	$result = getCountBySQL($sql);
	if ($result > 0) { return FALSE; } else { return TRUE; }
}

function validEmail2($str) {
	$sql = "SELECT COUNT(*) as total FROM part_info WHERE ptEmail = '$str'";
	$result = getCountBySQL($sql);
	if ($result > 0) { return FALSE; } else { return TRUE; }
}
?>
