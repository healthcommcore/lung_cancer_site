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
    $errMsg.="<b>ERROR:</b> ".mysql_error()."</b><br>\n";
}

$fieldName = array("partID", "startDate", "dateProvdTFR", "datePacket", "packetID", "datePed", "pedID", 
                  "dateWinraf", "dateWithdrew", "withdID", "preferTime", "preferPhone", "heID");
$cellName = array("Study ID", "Start Date","Date Provider TFR#1 Mailed", "Date Packet Sent", 
                  "Reason Packet Sent", "Date Pedometer Sent", "Reason Pedometer Mailed", "Date Won Raffle",
                   "Date Withdrew", "Reason Withdrew", "Preferred Time for Call 1", 
				   "Preferred Phone for Call 1", "Health Educator Assigned");
$timeArray = array("","8am-10am", "10am-12pm", "12pm-2pm", "12-2pm", "2pm-4pm", "4pm-6pm", "6pm-8pm");
$ixDateFlg = "";
$$ixDateErr = "";
$timeErr = "";
$istErr = "";
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
// get the appt info from appt table
$apptArray = getApptInfo($partID);
// get random arm
$ranArm = getRandom($partID); 
// get enrollment info
$enrollInfoArray = getEnrollment($partID);
/*
// moved this code to enrollment page 1 
// find out if the pt's start date based on the ix modality
// if print, start date = appt date
// if web, start date = current date
if(!$enrollInfoArray[11]){
	if($enrollInfoArray[1]==1){
		$startDate = $apptArray[1];
	}elseif($enrollInfoArray[1]==2){
		$today = date('m/d/Y');
		$startDate = $today;
	}
}else{
	$startDate = $enrollInfoArray[11];
}
*/
$startDate = $enrollInfoArray[11]; // Vikki changed this, it's now set on page one and always exists
// get HE names
$heArray = getRaNames("HE");
// get ix modality change reason
//$ixChangeArray = getReason("ix_change_reason", "ixID", "ixReason");
// get reminder change reason
//$remdChangeArray = getReason("remind_change_reason", "remindID", "remindReason");
// get reminder opt-out reason
//$remdOptArray = getReason("remind_opt_reason", "remindOptID", "remindOptReason");
// get packet send reason
$packetArray = getReason("packet_reason", "packetID", "packetReason");
// get ped mailed reason
$pedArray = getReason("ped_reason", "pedID", "pedReason");
// get withdrew reason
$withDArray = getReason("withD_reason", "withdID", "withDReason");
// get HE names
$raArray = getRaNames("HE");

// get the coaching call prefer info
$callPreferArray = getCallPrefer($partID, 1);
// put all phone number in a array
$phoneArray = array("", "H-".$ptInfoArray[11], "W-".$ptInfoArray[12], "C-".$ptInfoArray[13], "O-".$ptInfoArray[14]);

// check form submission
if ($_POST["isSubmittedLogOut"]==1) {
	header("Location: login.php");
	exit();
}

// if form was submitted...
if ($_POST["isSubmittedEnroll"]==1) {
	// put all appt field value in an array
	for($i = 0; $i <count($fieldName); $i++){
		$inputEnrollArray[$i] = $_POST[$fieldName[$i]];
		//echo "The field is ".$fieldName[$i]." and the value is ".$_POST[$fieldName[$i]]."<br>\n";
	}
	
	
	
	// loop throgh all the dates and validate them
	for($i = 2; $i <9; $i++){
		if($i != 4 && $i !=6 ){
			if($inputEnrollArray[$i]){
				$dateFlg = checkValidDate($inputEnrollArray[$i]);
				if ($dateFlg){
				    $dateErr = "<b><font color = 'red'>ERROR: ".$dateErrMsg." for ".$cellName[$i]."!!</font></b><br>\n";
					$i =9;
				}
			}
		}
	}
	
	// check if the reason for packet sent has been entered
	if($_POST["datePacket"]){
		if ($_POST["packetID"] == 0){
			$packFlg = 1;
		    $packErr = "<b><font color = 'red'>ERROR: Please enter the reason for sending the packet!!</font></b><br>\n";
		}
	}
	
	// check if the reason for ped mailed has been entered
	if($_POST["datePed"]){
		if ($_POST["pedID"] == 0){
		    $pedFlg = 1;
		    $pedErr = "<b><font color = 'red'>ERROR: Please enter the reason for mailing the pedometer!!</font></b><br>\n";
		}
	}
	
	// check if the reason for withdrew has been entered
	if ($_POST["dateWithdrew"] != ''){
		if ($_POST["withdID"] == 0){
		    $withdFlg = 1;
		    $withdErr = "<b><font color = 'red'>ERROR: Please enter the reason for withdrawal!!</font></b><br>\n";
		}
	}
	
	// Vikki added reverse check - if they have selected a reason for withdrawing, but didn't enter date, squawk.
	if ($_POST["withdID"] != 0 && ($_POST["dateWithdrew"] == '' || !$_POST["dateWithdrew"])){
		    $withdFlg = 1;
		    $withdErr = "<b><font color = 'red'>ERROR: Please enter date of withdrawal!!</font></b><br>\n";
	}
	
	// check if a health educator has been assigned
	if ($ranArm == "mats+cc" && $_POST["heID"] == ""){
	    $heFlg = 1;
	    $heErr = "<b><font color = 'red'>ERROR: Please assign the health educator!!</font></b><br>\n";
	}
	
	//if all fields are correct, then update the recruitment table
	if (!$dateFlg && !$packFlg && !$pedFlg && !$withdFlg && !$heFlg){
		
		// if there is a withdraw date, then update the pt status
		if($_POST["dateWithdrew"]){
			$statusFlg = updateSatus($partID, 'I');
			if($statusFlg){
				$statusErr="<b><font color = 'red'>ERROR:".$errIstMsg."</font></b><br>\n";
			}
			// if the ix modality is web, then therese's function
			if($enrollInfoArray[1] == 1){
				//echo "call Therese's function when withdrew<br>"; 
				$statusArray = addWebUser($partID, $ptInfoArray[2], $ptInfoArray[16], $enrollInfoArray[4], 0, 0, $ptInfoArray[1]);
			}
		}else{
			$statusFlg = updateSatus($partID, 'A');
		}
		//format all the dates 
		$inputStDate = formatDate($_POST["startDate"], 1);
		$tfrDate = formatDate($_POST["dateProvdTFR"], 1);
		$packDate = formatDate($_POST["datePacket"], 1);
		$pedDate = formatDate($_POST["datePed"], 1);
		$rafDate = formatDate($_POST["dateWinraf"], 1);
		$withdDate = formatDate($_POST["dateWithdrew"], 1);
		
		// update record in the enrollment table 
		$updFlg = updateEnrollInfo2($partID, $inputStDate, $tfrDate, $packDate, $_POST["packetID"],
			                        $pedDate, $_POST["pedID"], $rafDate, $withdDate, $_POST["withdID"],
									$_POST["heID"]);
		if($updFlg){
			$updErr="<b><font color = 'red'>ERROR:".$errIstMsg."</font></b><br>\n";
		}else{
			// update the coaching call prefer info
			if($ranArm == "mats+cc" && ($_POST["preferTime"] || $_POST["preferPhone"])){
				if(count($callPreferArray)<=0){
					$istFlg = inserCallPrefer($partID, 1, $_POST["preferTime"], $_POST["preferPhone"]);
				}else{
					$updFlg = updateCallPrefer($partID, 1, $_POST["preferTime"], $_POST["preferPhone"]);
				}
			}
			if($istFlg){
				$istErr="<b><font color = 'red'>ERROR:".$errIstMsg."</font></b><br>\n";
			}elseif($updFlg){
				$updErr="<b><font color = 'red'>ERROR:".$errIstMsg."</font></b><br>\n";
			}else{
				$succMsg = "<font color = 'green' size = \"+1\">The following enrollment info has been successfully saved in the database!</font><br>\n";
			}
		}
	}
	
}
?>
<!DOCTYPE html PUBLIC "-//W3C//ulD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/ulD/xhtml1-strict.uld">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Edit Enrollment Page 2</title>
<link rel="stylesheet" type="text/css" href="includes/main.css" />
	<script language="javascript">
	
	<!--
	
	// FUNCTION: SUBMIT EDIT ENROLLMENT INFO PAGE 2
	function submitEnroll() {
	    document["form1"]["isSubmittedEnroll"].value=1;
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
if ($_POST["isSubmittedEnroll"]==1) {
	if ($dateErr!="") {
	    echo $dateErr."<br>";;
	}
	if ($packErr!="") {
	    echo $packErr."<br>";;
	}
	if ($pedErr!="") {
	    echo $pedErr."<br>";;
	}
	if($withdErr != ""){
		echo $withdErr."<br>";
	}
	if($heErr != ""){
		echo $heErr."<br>";
	}
	if($statusErr != ""){
		echo $statusErr."<br>";
	}
	if ($updErr!="") {
		echo $updErr ."<br>";
	}
	if ($istErr!="") {
		echo $istErr ."<br>";
	}
	// call therese's function status when pt withdraw
	if($enrollInfoArray[1] == 1){
		// if the ix modality is web and status != 1, then display error message
		if($_POST["dateWithdrew"]){
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
	}else{
		if ($succMsg!="") echo $succMsg."<br>";
	}
}
?>
<form name = form1 method="POST" action="edit_enrollment_two.php?heID=<?php echo $heID; ?>" >
<input type="hidden" name="isSubmittedEnroll" value=0 >
<p align = "right"><font color="#526D6D">Page 2</p>
<p><B>(Please note all the date fields in this page should be in "mm/dd/yyyy" format)</B></font></p>
 <table width = 75% border = 5 align = "left" valign = "top" cellpadding =6 width = 900px>
<?php
$enrollCount = 10;
if($_POST["isSubmittedEnroll"]!=1){
	for($a = 0; $a <count($cellName); $a++){
		if($a == 0){
			echo "<tr>\n";
			echo "<td align = \"left\" colspan = 2> ".$cellName[$a]." <input type = \"text\" name = \"".$fieldName[$a]."\" value = \"".$partID."\" disabled>
					      <input type = \"hidden\" name = \"".$fieldName[$a]."\" value = \"".$partID."\">
						 </td>\n";
			echo "</tr>\n";
		}elseif($a<8){
			if($a%2 != 0){
				$enrollCount++;
				echo "<tr>\n";
				if($cellName[$a] == "Start Date"){
					echo "<td align = \"left\"> ".$cellName[$a]." <br><input type = \"text\"  value = \"".$startDate."\" disabled>
						<input type = \"hidden\" name = \"".$fieldName[$a]."\" value = \"".$startDate."\"></td>\n";
				}else{
					echo "<td align = \"left\"> ".$cellName[$a]." <br><input type = \"text\" name = \"".$fieldName[$a]."\" value = \"".$enrollInfoArray[$enrollCount]."\"></td>\n";
				}
			}else{
				$enrollCount++;
				if($cellName[$a] == "Reason Packet Sent"){
					echo "<td align = \"left\">".$cellName[$a]." <br>\n";
					echo "<select name = \"".$fieldName[$a]."\">\n";
					for($c =0; $c<=count($packetArray); $c++){
						if($c == $enrollInfoArray[$enrollCount]){
							echo "<option value = \"".$c."\" selected> ".$packetArray[$c]." </option>\n";
						}else{
							echo "<option value = \"".$c."\"> ".$packetArray[$c]." </option>\n";
						}
					}
					
					echo "</select>\n";
					echo "</td>\n";
				}elseif($cellName[$a] == "Reason Pedometer Mailed"){
					echo "<td align = \"left\">".$cellName[$a]." <br>\n";
					echo "<select name = \"".$fieldName[$a]."\">\n";
					for($c =0; $c<=count($pedArray); $c++){
						if($c == $enrollInfoArray[$enrollCount]){
							echo "<option value = \"".$c."\" selected> ".$pedArray[$c]." </option>\n";
						}else{
							echo "<option value = \"".$c."\"> ".$pedArray[$c]." </option>\n";
						}
					}
					echo "</select>";
					echo "</td>\n";
				}else{
					echo "<td align = \"left\"> ".$cellName[$a]." <br><input type = \"text\" name = \"".$fieldName[$a]."\" value = \"".$enrollInfoArray[$enrollCount]."\"></td>\n";
				}
			}
		}else{
			if($a%2 == 0){
				echo "<tr>\n";
				if($cellName[$a] == "Preferred Time for Call 1"){
					if($ranArm != "mats+cc"){
						echo "<td><input type = \"hidden\" name = \"".$fieldName[$a]."\" value = \"\"></td>";
					}else{
						echo "<td align = \"left\">".$cellName[$a]." <br>\n";
						echo "<select name = \"".$fieldName[$a]."\">\n";
						for($c =0; $c<count($timeArray); $c++){
							if($timeArray[$c] == $callPreferArray[0]){
								echo "<option value = \"".$timeArray[$c]."\" selected> ".$timeArray[$c]." </option>\n";
							}else{
								echo "<option value = \"".$timeArray[$c]."\"> ".$timeArray[$c]." </option>\n";
							}
						}
						echo "</select>";
						echo "</td>\n";
					}
				}elseif($cellName[$a] == "Health Educator Assigned"){
					$enrollCount++;
					if($ranArm != "mats+cc"){
						echo "<td><input type = \"hidden\" name = \"".$fieldName[$a]."\" value = \"0\"></td>";
					}else{
						echo "<td align = \"left\" colsapn = 2>".$cellName[$a]." <br>\n";
						echo "<select name = \"".$fieldName[$a]."\">\n";
						echo "<option value = \"\">  </option>\n";
						reset($heArray);
						while(list($key,$valArray)=each($heArray)){
							if($key == $enrollInfoArray[$enrollCount]){
								echo "<option value = \"".$key."\" selected> ".$valArray[0]." ".$valArray[1]." </option>\n";
							}else{
								echo "<option value = \"".$key."\"> ".$valArray[0]." ".$valArray[1]." </option>\n";
							}
						}
						echo "</select>";
						echo "</td>\n";
					}
				}else{
					$enrollCount++;
					echo "<td align = \"left\"> ".$cellName[$a]." <br><input type = \"text\" name = \"".$fieldName[$a]."\" value = \"".$enrollInfoArray[$enrollCount]."\"></td>\n";
				}
			}else{
				if($cellName[$a] == "Reason Withdrew"){
					$enrollCount++;
					echo "<td align = \"left\">".$cellName[$a]." <br>\n";
					echo "<select name = \"".$fieldName[$a]."\">\n";
					for($c =0; $c<=count($withDArray); $c++){
						if($c == $enrollInfoArray[$enrollCount]){
							echo "<option value = \"".$c."\" selected> ".$withDArray[$c]." </option>\n";
						}else{
							echo "<option value = \"".$c."\"> ".$withDArray[$c]." </option>\n";
						}
					}
					echo "</select>\n";
					echo "</td>\n";
				}elseif($cellName[$a] == "Preferred Phone for Call 1"){
					if($ranArm != "mats+cc"){
						echo "<td><input type = \"hidden\" name = \"".$fieldName[$a]."\" value = \"\"></td>";
					}else{
						echo "<td align = \"left\">".$cellName[$a]." <br>\n";
						echo "<select name = \"".$fieldName[$a]."\">\n";
						for($c =0; $c<count($phoneArray); $c++){
							if($phoneArray[$c] == $callPreferArray[1]){
								echo "<option value = \"".$phoneArray[$c]."\" selected> ".$phoneArray[$c]." </option>\n";
							}else{
								echo "<option value = \"".$phoneArray[$c]."\"> ".$phoneArray[$c]." </option>\n";
							}
						}
						echo "</select>";
						echo "</td>\n";
					}
				}else{
					echo "<td align = \"left\"> ".$cellName[$a]." <br><input type = \"text\" name = \"".$fieldName[$a]."\" value = \"".$enrollInfoArray[$enrollCount]."\"></td>\n";
				}
			}
		}
	}
	
}elseif($_POST["isSubmittedEnroll"]==1){
	for($a = 0; $a <count($cellName); $a++){
		if($a == 0){
			echo "<tr>\n";
			echo "<td align = \"left\" colspan = 2> ".$cellName[$a]." <input type = \"text\" name = \"".$fieldName[$a]."\" value = \"".$partID."\" disabled>
					      <input type = \"hidden\" name = \"".$fieldName[$a]."\" value = \"".$partID."\">
						 </td>\n";
			echo "</tr>\n";
		}elseif($a<8){
			if($a%2 != 0){
				echo "<tr>\n";
				if($cellName[$a] == "Start Date"){
					echo "<td align = \"left\"> ".$cellName[$a]." <br><input type = \"text\"  value = \"".$inputEnrollArray[$a]."\" disabled>
						<input type = \"hidden\" name = \"".$fieldName[$a]."\" value = \"".$inputEnrollArray[$a]."\"></td>\n";
				}else{
					echo "<td align = \"left\"> ".$cellName[$a]." <br><input type = \"text\" name = \"".$fieldName[$a]."\" value = \"".$inputEnrollArray[$a]."\"></td>\n";
				}
			}else{
				if($cellName[$a] == "Reason Packet Sent"){
					echo "<td align = \"left\">".$cellName[$a]." <br>\n";
					echo "<select name = \"".$fieldName[$a]."\">\n";
					for($c =0; $c<=count($packetArray); $c++){
						if($c == $inputEnrollArray[$a]){
							echo "<option value = \"".$c."\" selected> ".$packetArray[$c]." </option>\n";
						}else{
							echo "<option value = \"".$c."\"> ".$packetArray[$c]." </option>\n";
						}
					}
					
					echo "</select>\n";
					echo "</td>\n";
				}elseif($cellName[$a] == "Reason Pedometer Mailed"){
					echo "<td align = \"left\">".$cellName[$a]." <br>\n";
					echo "<select name = \"".$fieldName[$a]."\">\n";
					for($c =0; $c<=count($pedArray); $c++){
						if($c == $inputEnrollArray[$a]){
							echo "<option value = \"".$c."\" selected> ".$pedArray[$c]." </option>\n";
						}else{
							echo "<option value = \"".$c."\"> ".$pedArray[$c]." </option>\n";
						}
					}
					echo "</select>";
					echo "</td>\n";
				}else{
					echo "<td align = \"left\"> ".$cellName[$a]." <br><input type = \"text\" name = \"".$fieldName[$a]."\" value = \"".$inputEnrollArray[$a]."\"></td>\n";
				}
			}
		}else{
			if($a%2 == 0){
				echo "<tr>\n";
				if($cellName[$a] == "Preferred Time for Call 1"){
					if($ranArm != "mats+cc"){
						echo "<td><input type = \"hidden\" name = \"".$fieldName[$a]."\" value = \"\"></td>";
					}else{
						echo "<td align = \"left\">".$cellName[$a]." <br>\n";
						echo "<select name = \"".$fieldName[$a]."\">\n";
						for($c =0; $c<count($timeArray); $c++){
							if($timeArray[$c] == $inputEnrollArray[$a]){
								echo "<option value = \"".$timeArray[$c]."\" selected> ".$timeArray[$c]." </option>\n";
							}else{
								echo "<option value = \"".$timeArray[$c]."\"> ".$timeArray[$c]." </option>\n";
							}
						}
						echo "</select>";
						echo "</td>";
					}
				}elseif($cellName[$a] == "Health Educator Assigned"){
					// if random arm is not coaching call, then don't display this field
					if($ranArm != "mats+cc"){
						echo "<td><input type = \"hidden\" name = \"".$fieldName[$a]."\" value = \"0\"></td>";
					}else{
						echo "<td align = \"left\" colsapn = 2>".$cellName[$a]." <br>\n";
						echo "<select name = \"".$fieldName[$a]."\">\n";
						echo "<option value = \"\">  </option>\n";
						reset($heArray);
						while(list($key,$valArray)=each($heArray)){
							if($key == $inputEnrollArray[$a]){
								echo "<option value = \"".$key."\" selected> ".$valArray[0]." ".$valArray[1]." </option>\n";
							}else{
								echo "<option value = \"".$key."\"> ".$valArray[0]." ".$valArray[1]." </option>\n";
							}
						}
						echo "</select>";
						echo "</td>";
					}
				}else{
					echo "<td align = \"left\"> ".$cellName[$a]." <br><input type = \"text\" name = \"".$fieldName[$a]."\" value = \"".$inputEnrollArray[$a]."\"></td>\n";
				}
			}else{
				if($cellName[$a] == "Reason Withdrew"){
					echo "<td align = \"left\">".$cellName[$a]." <br>\n";
					echo "<select name = \"".$fieldName[$a]."\">\n";
					for($c =0; $c<=count($withDArray); $c++){
						if($c == $inputEnrollArray[$a]){
							echo "<option value = \"".$c."\" selected> ".$withDArray[$c]." </option>\n";
						}else{
							echo "<option value = \"".$c."\"> ".$withDArray[$c]." </option>\n";
						}
					}
					echo "</select>\n";
					echo "</td>\n";
				}elseif($cellName[$a] == "Preferred Phone for Call 1"){
					// if random arm is not coaching call, then don't display this field
					if($ranArm != "mats+cc"){
						echo "<td><input type = \"hidden\" name = \"".$fieldName[$a]."\" value = \"\"></td>";
					}else{
						echo "<td align = \"left\">".$cellName[$a]." <br>\n";
						echo "<select name = \"".$fieldName[$a]."\">\n";
						for($c =0; $c<count($phoneArray); $c++){
							if($phoneArray[$c] == $inputEnrollArray[$a]){
								echo "<option value = \"".$phoneArray[$c]."\" selected> ".$phoneArray[$c]." </option>\n";
							}else{
								echo "<option value = \"".$phoneArray[$c]."\"> ".$phoneArray[$c]." </option>\n";
							}
						}
						echo "</select>";
						echo "</td>\n";
					}
					
				}else{
					echo "<td align = \"left\"> ".$cellName[$a]." <br><input type = \"text\" name = \"".$fieldName[$a]."\" value = \"".$inputEnrollArray[$a]."\"></td>\n";
				}
			}
		}
	}
	
}


echo "<tr>\n";
echo "<td colspan = 2 align = \"right\"><input type = \"submit\" value = \"Save Record\" onClick=\"submitEnroll();\" >\n";
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
