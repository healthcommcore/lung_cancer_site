<?php
// LOAD mySQL FUNCTIONS
include("includes/connection.php");
//include("includes/handler.js");
$errMsg="";

// CONNECT TO DATABASE
if ($mysqlID=dbConnect()) {
    selectDatabase("lung_cancer_user");
} else {
    $errMsg.="<b>ERROR: ".mysql_error()."</b><br>\n";
}
$altCellName = array("Alternate Contact First Name", "Alternate Contact Last Name", "Street", "City", "State", "Zip", 
                     "Alternate Contact Phone", "Relation to Participant");
$altFieldName = array("conFName", "conLName", "conAddress", "conCity", "conState", "conZip", "conPhone", "conRelation");
$stateFullArray = array("", "Alabama", "Alaska", "Arizona", "Arkansas", "California", "Colorado", "Connecticut", "Delaware", "District of Columbia", "Florida",
                   "Georgia", "Hawaii", "Idaho", "Illinois", "Indiana", "Iowa", "Kansas", "Kentucky", "Louisiana", "Maine", "Maryland", "Massachusetts", 
				   "Michigan", "Minnesota", "Mississippi", "Missouri", "Montana", "Nebraska", "Nevada", "New Hampshire", "New Jersey", "New Mexico", "New York", 
				   "North Carolina", "North Dakota", "Ohio", "Oklahoma", "Oregon", "Pennsylvania", "Rhode Island", "South Carolina", "South Dakota", "Tennessee", 
				   "Texas", "Utah", "Vermont", "Virginia", "Washington", "West Virginia", "Wisconsin", "Wyoming");
foreach ($stateFullArray as $st){
	$stateArray[] = convertState2Abbrev($st);
}
// get the participant ID
if($_GET['partID']){
	$partID = $_GET['partID'];
}else{
	$partID = $_POST["partID"];
}
// get heID if exist
if($_GET['heID']) $heID = $_GET['heID'];
// check form submission
if ($_POST["isSubmittedLogOut"]==1) {
	header("Location: login.php");
	exit();
}


// get the pt's name
$ptInfoArray = getPartInfo($partID);
// get alt contact info
$conInfoArray = getConInfo($partID);
// get the appt info from appt table
$recruitArray = getRecruitInfo($partID);
// get random arm
$ranArm = getRandom($partID);
//if(!$ranArm) $randError = 1;
// if bseline result is not consented, then display error msg - took out  || $ranArm == 'uc', they can add a record for the uc group 
// also removed  || $randError for same reason. 
// Vikki removed this error. Should be able to edit alt contact info, even if they didn't consent
/*
if($recruitArray[7] != 1){
	$pageError = "<b><font color = 'red'>ERROR: This participant did not consent to the study! <br>Therefore, you should not enter alternative contact information!!</font></b><br>\n";
}
*/
// if form was submitted...
if ($_POST["isSubmittedContact"]==1) {

	// put all alt contact field value in an array
	for($j = 0; $j <count($altFieldName); $j++){
		$inputAltArray[$j] = $_POST[$altFieldName[$j]];
	}
	//check to see if zip code is valid
	if($_POST["conZip"]){
		$zipFlg = checkValidZip($_POST["conZip"]);
		if ($zipFlg){
		    $zipErr = "<b><font color = 'red'>ERROR: ".$zipErrMsg."!!</font></b><br>\n";
		}
	}
	// check if the phone number entered is a valid format
	if($_POST["conPhone"]){
		$phoneFlg = checkValidPhone($_POST["conPhone"]);
		if($phoneFlg){
			$phoneErr = "<b><font color = 'red'>ERROR: ".$phoneErrMsg."!!</font></b><br>\n";
		}
	}
	
	if(!$zipFlg && !$phoneFlg){
		if($_POST["conFName"])$conFname = mysql_real_escape_string($_POST["conFName"]);
		if($_POST["conLName"])$conLname = mysql_real_escape_string($_POST["conLName"]);
		// insert into alt contact table
		
		if(count($conInfoArray)<=0){
			$istFlg = insertAltInfo($partID, strtoupper($conFname), strtoupper($conLname), 
	                      $_POST["conAddress"], $_POST["conCity"], $_POST["conState"], 
						  $_POST["conZip"], $_POST["conPhone"], $_POST["conRelation"]);
		}else{
			$istFlg = updateAltInfo($partID, strtoupper($_POST["conFName"]), strtoupper($_POST["conLName"]), 
	                      $_POST["conAddress"], $_POST["conCity"], $_POST["conState"], 
						  $_POST["conZip"], $_POST["conPhone"], $_POST["conRelation"]);
		}
		if($istFlg){
			$istErr="<b><font color = 'red'>ERROR:".$errIstMsg."</font></b><br>\n";
		}else{
			$succMsg = "<font color = 'green' size = \"+1\">The following appointment and alternate contact info has been successfully saved in the database!</font><br>\n";
		}
	}
}elseif($_POST["isSubmittedEnroll"]==1){
	//echo "the partID is ".$partID;
	
	header("Location: edit_enrollment_one.php?partID=".$partID."&heID=".$heID);
	exit();
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<!DOCTYPE html PUBLIC "-//W3C//ulD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/ulD/xhtml1-strict.uld">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Edit Alternative Contact</title>
<link rel="stylesheet" type="text/css" href="includes/main.css" />
<?php if (!$succMsg || $succMsg == '') { ?>
	<script language="JavaScript" type="text/javascript">
	function submitContact() {
	    document["form1"]["isSubmittedContact"].value=1;
		document["form1"]["isSubmittedEnroll"].value=0;
	    document["form1"].submit();
	}

	function submitEnroll() {
	var checkit;
	checkit = isFilled();
	if (checkit === 'filled') { 
		var cont = confirm('Any changes made to Alternate Contact have not been saved. Do you want to continue to the Edit Enrollment form without saving Alternate Contact?');
		if (cont == true) { 
	    	document["form1"]["isSubmittedEnroll"].value=1;
			document["form1"]["isSubmittedContact"].value=0;
		    document["form1"].submit();
		} else { return false; }
	} else {
	    document["form1"]["isSubmittedEnroll"].value=1;
		document["form1"]["isSubmittedContact"].value=0;
	    document["form1"].submit();
	}
}

function isFilled() {
	var fname = document["form1"]["conFName"].value;
	var lname = document["form1"]["conLName"].value;
	if (fname.length == 0 || lname.length == 0) { return 'empty'; } else { return 'filled'; }
}
</script>
<?php } else { ?>
	<script language="javascript">
	<!--
	// FUNCTION: SUBMIT EDIT ENROLLMENT INFO
	function submitEnroll() {
	    document["form1"]["isSubmittedEnroll"].value=1;
		document["form1"]["isSubmittedContact"].value=0;
	    document["form1"].submit();
	}
	//-->
	
	// FUNCTION: SUBMIT EDIT ALT CONTACT INFO
	function submitContact() {
	    document["form1"]["isSubmittedContact"].value=1;
		document["form1"]["isSubmittedEnroll"].value=0;
	    document["form1"].submit();
	}
	//-->
	</script>
<?php } ?>
</head>

<body>
<!-- Begin Wrapper -->
   <div id="wrapper">
   
         <!-- Begin Header -->

         <div id="header">
		 <br><br>
		<h2 align = "center"> Edit Alternative Contact for <?php echo $ptInfoArray[2] . " ". $ptInfoArray[3];?></h2>  
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
	echo $pageError;
	exit();
}
if ($zipErr!="") {
    echo $zipErr;
	
}
if ($phoneErr!="") {
    echo $phoneErr;
	
}
if ($istErr!="") {
	echo $istErr;
}

//report success msg
if ($succMsg!="") {
    echo $succMsg;
}?>
<form name="form1" id="form1" method="POST" action="edit_contact.php?partID=<?php echo $partID;?>&heID=<?php echo $heID;?>" >
<input type="hidden" name="isSubmittedContact" value=0 >
<input type="hidden" name="isSubmittedEnroll" value=0 >
<table border = 5 align = "left"  cellpadding =14>
<?php
if($_POST["isSubmittedContact"]!=1){
	echo "<tr>\n";
	echo "<td align=\"left\" colspan=2> Study ID <input type=\"text\" value=\"".$partID."\" disabled>
			      <input type=\"hidden\" name=\"partID\" value=\"".$partID."\"></td>\n";
	echo "</tr>\n";
	for($a = 0; $a <count($altCellName); $a++){
			
		if($a %2 == 0){
			echo "<tr>\n";
			if($altCellName[$a] == "State"){
				echo "<td align=\"left\">".$altCellName[$a]." <br>\n";
				echo "<select name=\"".$altFieldName[$a]."\">\n";
				for($c =0; $c<count($stateArray); $c++){
					if($stateArray[$c] == $conInfoArray[$a]){
						echo "<option value=\"".$stateArray[$b]."\" selected> ".$stateArray[$c]." </option>\n";
					}else{
						echo "<option value=\"".$stateArray[$c]."\"> ".$stateArray[$c]." </option>\n";
					}
				}
				echo "</td>\n";
			}else{
				echo "<td align = \"left\"> ".$altCellName[$a]." <br><input type=\"text\" name=\"".$altFieldName[$a]."\" value=\"".$conInfoArray[$a]."\"></td>\n";
			}
		}else{
			echo "<td align=\"left\"> ".$altCellName[$a]." <br><input type=\"text\" name=\"".$altFieldName[$a]."\" value=\"".$conInfoArray[$a]."\"></td>\n";
			echo "<tr>\n";
		}
	}
}else{
	echo "<tr>\n";
	echo "<td align=\"left\" colspan=2> Study ID <input type=\"text\" value=\"".$partID."\" disabled>
			      <input type=\"hidden\" name=\"".$partID."\" value=\"".$partID."\"></td>\n";
	echo "</tr>\n";
	for($a = 0; $a <count($altCellName); $a++){
		if($a %2 == 0){
			echo "<tr>\n";
			if($altCellName[$a] == "State"){
				echo "<td align=\"left\">".$altCellName[$a]." <br>\n";
				echo "<select name=\"".$altFieldName[$a]."\">\n";
				for($c =0; $c<count($stateArray); $c++){
					if($stateArray[$c] == $inputAltArray[$a]){
						echo "<option value=\"".$stateArray[$c]."\" selected> ".$stateArray[$c]." </option>\n";
					}else{
						echo "<option value=\"".$stateArray[$c]."\"> ".$stateArray[$c]." </option>\n";
					}
				}
				echo "</td>\n";
			}else{
				echo "<td align=\"left\"> ".$altCellName[$a]." <br><input type=\"text\" name=\"".$altFieldName[$a]."\" value=\"".$inputAltArray[$a]."\"></td>\n";
			}
		}else{
			echo "<td align = \"left\"> ".$altCellName[$a]." <br><input type = \"text\" name = \"".$altFieldName[$a]."\" value = \"".$inputAltArray[$a]."\"></td>\n";
			echo "<tr>\n";
		}
	}
}

echo "<tr>\n";
echo "<td><input type = \"submit\" value = \"Save Record\" onClick=\"submitContact();\" ></td>\n";
echo "<td><input type = \"submit\" value = \"Edit Enrollment\" onClick=\"";
if (!$succMsg || $succMsg == '') { echo 'return'; }
echo " submitEnroll();\" >\n";
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
