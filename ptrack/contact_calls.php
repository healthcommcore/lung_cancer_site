<?php
include("includes/connection.php");
include("includes/he_function.php");
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
// get the callID
$callID = $_GET['callID'];
// get the he ID
$heID = $_GET['heID'];
// get the part ID
if($_GET['partID']){
	$partID =$_GET['partID'];
}else{
	$partID = $_POST["partID"];
}
// declare some variables
$weekArray = array("","Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday");
$contentName = array("goals", "barriers", "strategies", "action", "assigned");
$addCount = 0;
$conArray = array();
$ptInfoArray = array();
$methArray = array();
$rstArray = array();
$inputConNum = array();
$inputConDate = array();
$inputConDay = array();
$inputConSTime = array();
$inputConETime = array();
$inputConRst = array();
$columnHead = array("conNum", "conDate", "conDay", "conPhone", "conTimeS", "conTimeE", "conRst");
$lineErr ="";
$fieldErr = "";
$dateErr ="";
$istErr ="";
$succMsg = "";
// get the participart info from the part_info table
$ptInfoArray = getPartInfo($partID);
// call fuction to get display info
$displayArray = getDisplayInfo($partID);
// get TFR dates
$dateMailed = getTFRDate($partID);
// get coaching call info
$callInfoArray = getCallInfo($partID, $callID);		
// if this is call 1, then find out the call prefer info for call1
if($callID == 1){
	$call1PreferArray = getCallPrefer($partID, $callID);
	$call2PreferArray = getCallPrefer($partID, 2);
}else{
	$call2PreferArray = getCallPrefer($partID, 2);
}
// get the contact result
$resultArray = getContactResult($callID);
// get the review points for this call
$scriptArray = getScript($callID);
// get the review script value
$reviewArray = getReview($partID, $callID);
//get coaching call notes
$callNotes = getCallNotes($partID);
// get all the phone number
$phoneArray = array("", "H-".$ptInfoArray[11], "W-".$ptInfoArray[12], "C-".$ptInfoArray[13], "O-".$ptInfoArray[14]);
//determin which one in the phone array is the prefer phone number for this contact
for ($a = 0; $a<count($phoneArray); $a++){
	// prefer phone for call #1
	if($callID == 1){
		if($call1PreferArray[1]){
			if($call1PreferArray[1] == $phoneArray[$a]){
				$phone1Array[$a] = $phoneArray[$a] . "(prefer)";
			}else{
				$phone1Array[$a] = $phoneArray[$a];
			}
		}else{
			$phone1Array[$a] = $phoneArray[$a];
		}
	}else{
		//prefer phone for call2
		if($call2PreferArray[1]){
			if($call2PreferArray[1] == $phoneArray[$a]){
				$phone2Array[$a] = $phoneArray[$a] . "(prefer)";
			}else{
				$phone2Array[$a] = $phoneArray[$a];
			}
		}else{
			$phone2Array[$a] = $phoneArray[$a];
		}
	}
}
if ($_POST["isSubmittedLog"]==1 || $_POST["isSubmittedAdd"]==1 || $_POST["isSubmittedRemove"] == 1){
	// if add a new line, the line number +1
	if($_POST["isSubmittedAdd"] ==1){
		$lines= $_POST["lineNum"]+1;
	}else{
		$lines= $_POST["lineNum"];
	}
	
	$counter = 1;
	//get the input vals from submmited page
	for($a = 0; $a <$lines; $a++ ){
		if($_POST["conRemove".$a] != "on"){
			$inputConArray[$a]=array($counter, $_POST["conDate".$a], $_POST["conDay".$a],
									$_POST["conPhone".$a], $_POST["conTimeS".$a], $_POST["conTimeE".$a],
									$_POST["conRst".$a],  $_POST["conRemove".$a]);
			//echo "the counter value is ".$counter."The Line muber is ".$lines."<br>";
			$counter++;
		}
	}
	// put review items to a array
	for($a = 1; $a<=count($scriptArray); $a++){
		$fieldName = substr($scriptArray[$a], 0, 5);
		$inputSpArray[$a] = $_POST[$fieldName];
		//echo $fieldName." is ".$inputSpArray[$a]."<br>\n";
	}
	if($_POST["isSubmittedLog"]==1){
		// check if any contact has been entered
		if($lines == 0){
			$lineErr = "<b><font color = 'red'>ERROR: You need to enter at least one form of contact information!!</font></b><br>\n";
			$lineFlg = 1;
		}
		
		// check to see if any field have not been entered
		for($a = 0; $a <$lines; $a++ ){
			for($b = 0; $b<count($columnHead); $b++){
				if($_POST[$columnHead[$b].$a] == ""){
					$fieldFlg = 1;
					$fieldErr = "<b><font color = 'red'>ERROR: You need to enter all contact information for line ".$_POST["conNum".$a]."!!</font></b><br>\n";
					$b=count($columnHead);
					$a = $lines;
				}
			}
		}
		//check to see if all the dates are correct
		for ($a=0; $a<$lines; $a++){
			//first to check if the date is in correct formate
			$dateFlg = checkValidDate($_POST["conDate".$a]);
			if ($dateFlg){
			    $dateErr = "<b><font color = 'red'>ERROR: ".$dateErrMsg." for call number".$_POST["conNum".$a]."!</font></b><br>\n";
				$a=$lines; 
			// then check to see if it's graeter then today
			}else{
				list($month, $day, $year) = split('[/]', $_POST["conDate".$a]);
				$formDate = mktime(0, 0, 0, $month, $day, $year);
				$today = mktime();
				// first check to see if entered date is greater then today
			    if ( $formDate> $today){
				    $dateErr = "<b><font color = 'red'>ERROR: You can not record a date that is in the future!!</font></b><br>\n";
					$dateFlg = 1;
					$a=$lines;
				}
			}
		}
		
		// asked Molly, she said that these two fields can be optional 12/9/08
		//if($callID == 1){
			//if(!$_POST["call2Prefer"] || !$_POST["preferPhone"] ){
				//$preferFlg = 1;
				//$preferErr = "<b><font color = 'red'>ERROR: You need to enter prefers for call2!!</font></b><br>\n";
			//}
		//}
		
		if(!$fieldFlg && !$dateFlg && !$lineFlg && !$preferFlg){
			// insert records into the contact call table
			$istFlg = insertCalls($inputConArray, $partID, $callID);
			if($istFlg) $istErr = "<font color = 'red'>Error:".$errIstMsg."</font><br>\n";
			//insert script review into the review script table
			if(!$istFlg){
				$istFlg = insertReview($inputSpArray, $partID, $callID);
				// insert call note into the call notes table
				if(!$istFlg && $_POST["callNotes"]){
					$istFlg = insertCallNotes($callNotes, $_POST["callNotes"], $partID);
					if($istFlg) $istErr = "<font color = 'red'>Error:".$errIstMsg."</font><br>\n";
				}elseif($istFlg){
					$istErr = "<font color = 'red'>Error:".$errIstMsg."</font><br>\n";
				}
			}else{
				$istErr = "<font color = 'red'>Error:".$errIstMsg."</font><br>\n";
			}
			
			// update the pt notes
			if(!$istFlg && $_POST["ptNotes"]){
				$updFlg = updatePtNotes($partID, $_POST["ptNotes"]);
				if($updFlg) $updErr = "<font color = 'red'>Error:".$errUpdMsg."</font><br>\n";
			}
			
			// if this is call 1, insert/update call prefer table
			if($callID == 1 && ($_POST["call2Prefer"] || $_POST["preferPhone"])){
				// inert/update the call_prefer table
				if(!$istFlg && !$updFlg){
					$istFlg = insertCall2Prefer($call2PreferArray, $_POST["call2Prefer"], $_POST["preferPhone"], $partID, 2);
					if($istFlg)$istErr = "<font color = 'red'>Error:".$errIstMsg."</font><br>\n";
				}
			}
			
			if(!$istFlg && !$updFlg) $succMsg = "<font color = 'green' size = \"+1\">The following contact info has been successfully saved in the database!</font><br>\n";
		}
	}
}elseif ($_POST["isSubmittedPrev"]==1){
	$url = "contact_calls.php?callID=1&heID=".$heID."&partID=".$partID."&page=view";
	//echo $url2;
	header("Location:".$url);
	exit(); 
}elseif($_POST["isSubmittedEdit"]==1){
	header("Location:edit_part.php?partID=".$partID."&heID=".$heID);
	exit();
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Coaching Call</title>
<link rel="stylesheet" type="text/css" href="includes/main.css" />
<script language="javascript">
	<!--
	// FUNCTION TO GET THE START TIME FOR THE CALL
	function getStartTime(num) {
		var fieldName = "conTimeS" +num;
		var d = new Date();
		var t_hour = d.getHours();     // Returns hours
		var t_min = d.getMinutes();    // Returns minutes
		var t_sec = d.getSeconds();    // Returns seocnds
		var s_time = t_hour + ":" + t_min + ":" + t_sec;
	    document["form2"][fieldName].value= s_time;
	   //alert("The time for "+fieldName + t_hour + ":" + t_min + ":" + t_sec);
	}
	//-->
	
	// FUNCTION TO GET THE END TIME FOR THE CALL
	function getEndTime(num) {
		var fieldName = "conTimeE"+num;
	    var d = new Date();
		var t_hour = d.getHours();     // Returns hours
		var t_min = d.getMinutes();    // Returns minutes
		var t_sec = d.getSeconds();    // Returns seocnds
		var e_time = t_hour + ":" + t_min + ":" + t_sec;
	    document["form2"][fieldName].value= e_time;
	}
	//-->
	
	// FUNCTION: SUBMIT INTEERVIEW INFO
	function submitLog() {
	    document["form2"]["isSubmittedLog"].value=1;
	    document["form2"].submit();
	}
	//-->
	
	// FUNCTION: SUBMIT ADD NEW LINE
	function submitAdd() {
		document["form2"]["isSubmittedAdd"].value=1;
	   document["form2"].submit();
	}
	//-->
	
	// FUNCTION: SUBMIT REMOVE A LINE
	function submitRemove() {
	    document["form2"]["isSubmittedRemove"].value=1;
	    document["form2"].submit();
	   //alert("check the removal");
	}
	//-->
	
	// FUNCTION: SUBMIT PREVIOUS INFO
	function submitPrev() {
	    document["form2"]["isSubmittedPrev"].value=1;
	    document["form2"].submit();
	}
	//-->
	
	// FUNCTION: SUBMIT EDIT PT INFO
	function submitEdit() {
	    document["form2"]["isSubmittedEdit"].value=1;
	    document["form2"].submit();
	}
	//-->
	
	function openpop( newurl){
    	newwin=window.open( newurl,'','width=680,status,scrollbars');
	}

	</script>
</head>

<body>
  <!-- Begin Wrapper -->
   <div id="wrapper1">
   
         <!-- Begin Header -->

         <div id="header1">
		 	<br><br>
		<h2 align = "center"> Contact Call # <?php echo $callID;?> for <?php echo $displayArray[10] . " ". $displayArray[11];?></h2>  
		<br><p align = "right"><form method="POST" action="login.php" >
	     <input type = "submit" value = "log out" size = 100></form></p>  
		 </div>
		 <!-- End Header -->
		 
         <!-- Begin Faux Columns -->
		 <div id="faux1">
		 
		       <!-- Begin Left Column -->
		       <div id="leftcolumn1"> 
		 		<?php
						$menu = getMenu1($heID);
						echo $menu;
					?>
		       </div>

		       <!-- End Left Column -->
		 
		       <!-- Begin Right Column -->
		       <div id="rightcolumn1">
    <?php 
	// REPORT ERRORS
	if($_POST["isSubmittedLog"]==1){
		if ($lineErr) echo $lineErr;
		if ($fieldErr) echo $fieldErr;
		if ($dateErr) echo $dateErr;
		if($preferErr) echo $preferErr;
		if ($istErr) echo $istErr;
		if ($updErr) echo $updErr;
		//report success msg
		if ($succMsg) echo $succMsg;
	}
	?>
	<?php 
		$display = displayPtInfo($displayArray, $dateMailed);
		echo $display;
	?>
	<br>
	<table border = 0 width = 600>
	<tr><td colspan = 5><em><a href="javascript:openpop('showTfr.php?partID=<?php echo $partID; ?>');"></em>Show Baseline Result</td></tr>
	<?php if($displayArray[3] == "Web"){ ?>
	<tr>
	<td><a href="javascript:openpop('showAP.php?partID=<?php echo $partID; ?>');">Show HD Plan</td>
	<td width = 10%><img src="images/blank.gif"></td>
	<td><a href="javascript:openpop('showWeekTrack.php?partID=<?php echo $partID; ?>');">Show Weekly Tracking</td>
	<td width = 10%><img src="images/blank.gif"></td>
	<td><a href="javascript:openpop('showHistTrack.php?partID=<?php echo $partID; ?>');">Show Tracking History</td>
	</tr>
	<?php }?>
	</table>
	<br>
	<form name= form2 method="POST" action="contact_calls.php?callID=<?php echo $callID;?>&heID=<?php echo $heID;?>" >
	<input type="hidden" name="isSubmittedAdd" value=0 ><input type="hidden" name="isSubmittedLog" value=0 >
	<input type="hidden" name="isSubmittedEdit" value=0 >
	<input type="hidden" name="isSubmittedRemove" value=0 >	<input type="hidden" name="partID" value=<?php echo $partID;?> >
    <input type="hidden" name="isSubmittedPrev" value=0 >
	<?php
		$lineNum = 0;
	    // display page header
		$tableHeader = getTableHeader();
		echo $tableHeader;
		$num= 0;
		// call function to print out the contact record from the table
		if ($inputConArray <=0){
			$lineNum = count($callInfoArray);
			reset($callInfoArray);
			while(list($key, $conVal)=each($callInfoArray)){
				$inputConNum[$num] =  "conNum".$num;
				$inputConDate[$num] =  "conDate".$num;
				$inputConDay[$num] = "conDay".$num;
				$inputConPhone[$num] = "conPhone".$num;
				$inputConTimeS[$num] = "conTimeS".$num;
				$inputConTimeE[$num] = "conTimeE".$num;
				$inputConRst[$num] = "conRst".$num;
				$inputConRemove[$num] = "conRemove".$num;
				$conNumAdd = $conVal[2];
				?>
				<tr align = "center">
				<td><?php echo $conVal[0]; ?><input type ="hidden" name = "<?php echo $inputConNum[$num];?>" value = "<?php echo $conVal[0]; ?>">
				</td>
				<td><input type ="text" name = "<?php echo $inputConDate[$num];?>" value = "<?php echo $conVal[1]; ?>" size = 8> </td>
				<td>
					<select name="<?php echo $inputConDay[$num];?>">
					<?php
						for($a = 0; $a<count($weekArray); $a++){
							if($conVal[2] == $weekArray[$a]){
								echo "<option value = \"".$weekArray[$a]."\" selected>".$weekArray[$a]." </option>\n";
							}else{
								echo "<option value = \"".$weekArray[$a]."\">".$weekArray[$a]." </option>\n";
							}
						}
			
					?>
					</select>
				</td>
				<td>
					<select name="<?php echo $inputConPhone[$num];?>">
					<?php
						if($callID == 1){
							for($a = 0; $a<count($phone1Array); $a++){
								if($conVal[3] == $phone1Array[$a]){
									echo "<option value = \"".$phone1Array[$a]."\" selected>".$phone1Array[$a]." </option>\n";
								}else{
									echo "<option value = \"".$phone1Array[$a]."\">".$phone1Array[$a]." </option>\n";
								}
							}
						}else{
							for($a = 0; $a<count($phone2Array); $a++){
								if($conVal[3] == $phone2Array[$a]){
									echo "<option value = \"".$phone2Array[$a]."\" selected>".$phone2Array[$a]." </option>\n";
								}else{
									echo "<option value = \"".$phone2Array[$a]."\">".$phone2Array[$a]." </option>\n";
								}
							}
						}
						
			
					?>
					</select>
				</td>
				<td><?php if(!$conVal[4]){?>
						<input type ="radio" onClick="getStartTime(<?php echo $num;?>);">
					<?php }?>
					<input type = "text" name = "<?php echo $inputConTimeS[$num]; ?>" value = "<?php echo $conVal[4];?>" size = 6>
				</td>
				<td><?php if(!$conVal[5]){?>
					<input type ="radio"  onClick="getEndTime(<?php echo $num;?>);">
					<?php }?>
					<input type = "text" name =  "<?php echo $inputConTimeE[$num]; ?>" value = "<?php echo $conVal[5];?>" size = 6>
					
				</td>
				<td><select name="<?php echo $inputConRst[$num];?>">
				<option value = "0"> </option>
				<?php
					reset($resultArray);
					while(list($key, $rst)=each($resultArray)){
						if($conVal[6] == $key){
							echo "<option value = \"".$key."\" selected>".$rst." </option>\n";
						}else{
							echo "<option value = \"".$key."\">".$rst." </option>\n";
						}
					}
	
				?>
				</select>
				</td>
				<td><input type ="checkbox" name = "<?php echo $inputConRemove[$num];?>" onClick="submitRemove();">
				</td>
				
			<?php
			$num++;
			}
			?>
			<tr>
			<td colspan = 8 align = "left">
			<input type = "submit" name = "addLine" value = "Add New Line" onClick="submitAdd();">
			<input type ="hidden" name = "lineNum" value ="<?php echo $lineNum;?>" size = 5>
			</td>
			</tr>
			<?php 
			// ptint out the script
			displayReview($reviewArray, $scriptArray, 0);
			// if this is call1, then print out the perfer call notes and prefer phone
			if($callID == 1){?>
				<tr>
				<td colspan = 4>Call2 Preferrence<br> Please <strong>briefly</strong> note info re preferred date(s), day(s), time(s)<br>
					<textarea name =  "call2Prefer" cols=20 rows=2 wrap><?php echo $call2PreferArray[2];?></textarea>
				</td>
				<td colspan = 4>Preferred Phone for Call2
					<select name="preferPhone">
					<?php
						for($a = 0; $a<count($phoneArray); $a++){
							if($call2PreferArray[1] == $phoneArray[$a]){
								echo "<option value = \"".$phoneArray[$a]."\" selected>".$phoneArray[$a]." </option>\n";
							}else{
								echo "<option value = \"".$phoneArray[$a]."\">".$phoneArray[$a]." </option>\n";
							}
						}
			
					?>
					</select>
				</td>
				</tr>
				
			<?php }
			// print out the coaching call notes and the general notes?>
			
			<tr>
			<td colspan = 8>Call Notes<br>
			<textarea name = "callNotes" cols=80 rows=6 wrap><?php echo $callNotes;?></textarea></td>
			</tr>
			<tr>
			<td colspan = 8>General Notes<br>
			<textarea name = "ptNotes" cols=80 rows=6 wrap><?php echo $ptInfoArray[17];?></textarea></td>
			</tr>
		<?php 
		}else{
			//get line number
			$lineNum = count($inputConArray);
			//if more there is a date error, then display all the row that have been added
			reset($inputConArray);
			while(list($key, $conVal)=each($inputConArray)){
				$inputConNum[$num] =  "conNum".$num;
				$inputConDate[$num] =  "conDate".$num;
				$inputConDay[$num] = "conDay".$num;
				$inputConPhone[$num] = "conPhone".$num;
				$inputConTimeS[$num] = "conTimeS".$num;
				$inputConTimeE[$num] = "conTimeE".$num;
				$inputConRst[$num] = "conRst".$num;
				$inputConRemove[$num] = "conRemove".$num;
				$conNumAdd = $conVal[2];
				?>
				<tr align = "center">
				<td><?php echo $conVal[0]; ?><input type ="hidden" name = "<?php echo $inputConNum[$num];?>" value = "<?php echo $conVal[0]; ?>">
				</td>
				<td><input type ="text" name = "<?php echo $inputConDate[$num];?>" value = "<?php echo $conVal[1]; ?>" size = 8> </td>
				<td>
					<select name="<?php echo $inputConDay[$num];?>">
					<?php
						for($a = 0; $a<count($weekArray); $a++){
							if($conVal[2] == $weekArray[$a]){
								echo "<option value = \"".$weekArray[$a]."\" selected>".$weekArray[$a]." </option>\n";
							}else{
								echo "<option value = \"".$weekArray[$a]."\">".$weekArray[$a]." </option>\n";
							}
						}
			
					?>
					</select>
				</td>
				<td>
					<select name="<?php echo $inputConPhone[$num];?>">
					<?php
						if($callID == 1){
							for($a = 0; $a<count($phone1Array); $a++){
								if($conVal[3] == $phone1Array[$a]){
									echo "<option value = \"".$phone1Array[$a]."\" selected>".$phone1Array[$a]." </option>\n";
								}else{
									echo "<option value = \"".$phone1Array[$a]."\">".$phone1Array[$a]." </option>\n";
								}
							}
						}else{
							for($a = 0; $a<count($phone2Array); $a++){
								if($conVal[3] == $phone2Array[$a]){
									echo "<option value = \"".$phone2Array[$a]."\" selected>".$phone2Array[$a]." </option>\n";
								}else{
									echo "<option value = \"".$phone2Array[$a]."\">".$phone2Array[$a]." </option>\n";
								}
							}
						}
					?>
					</select>
				</td>
				<td>
					<?php if(!$conVal[4]){?>
						<input type ="radio"  onClick="getStartTime(<?php echo $num;?>);">
					<?php }?>
					<input type = "text" name = "<?php echo $inputConTimeS[$num]; ?>" value = "<?php echo $conVal[4];?>" size = 6>
				</td>
				<td>
					<?php if(!$conVal[5]){?>
					<input type ="radio" onClick="getEndTime(<?php echo $num;?>);">
					<?php }?>
					<input type = "text" name =  "<?php echo $inputConTimeE[$num]; ?>" value = "<?php echo $conVal[5];?>" size = 6>
				</td>
				<td><select name="<?php echo $inputConRst[$num];?>">
				<option value = "0"> </option>
				<?php
					reset($resultArray);
					while(list($key, $rst)=each($resultArray)){
						if($conVal[6] == $key){
							echo "<option value = \"".$key."\" selected>".$rst." </option>\n";
						}else{
							echo "<option value = \"".$key."\">".$rst." </option>\n";
						}
					}
	
				?>
				</select>
				</td>
				<td><input type ="checkbox" name = "<?php echo $inputConRemove[$num];?>" onClick="submitRemove();">
				</td>
				
			<?php
			$num++;
			}
			?>
			<tr>
			<td colspan = 8 align = "left">
			<input type = "submit" name = "addLine" value = "Add New Line" onClick="submitAdd();">
			<input type ="hidden" name = "lineNum" value ="<?php echo $lineNum;?>" size = 5>
			</td>
			</tr>
			<?php 
			// ptint out the script
			displayReview($inputSpArray, $scriptArray, 1);
			// if this is call1, then print out the perfer call notes and prefer phone
			if($callID == 1){?>
				<tr>
				<td colspan = 4>Call2 Preferrence<br> Please <strong>briefly</strong> note info re preferred date(s), day(s), time(s)<br>
					<textarea name="call2Prefer" cols=20 rows=2 wrap><?php echo $_POST["call2Prefer"];?></textarea>
				</td>
				<td colspan = 4>Preferred Phone for Call2
					<select name="preferPhone">
					<?php
						for($a = 0; $a<count($phoneArray); $a++){
							if($_POST["preferPhone"] == $phoneArray[$a]){
								echo "<option value=\"".$phoneArray[$a]."\" selected>".$phoneArray[$a]." </option>\n";
							}else{
								echo "<option value=\"".$phoneArray[$a]."\">".$phoneArray[$a]." </option>\n";
							}
						}
			
					?>
					</select>
				</td>
				</tr>
				
			<?php }
			// print out the coaching call notes and the general notes?>
			
			<tr>
			<td colspan = 8>Call Notes<br>
			<textarea name = "callNotes" cols=80 rows=6 wrap><?php echo $_POST["callNotes"];?></textarea></td>
			</tr>
			<tr>
			<td colspan = 8>General Notes<br>
			<textarea name = "ptNotes" cols=80 rows=6 wrap><?php echo $_POST["ptNotes"];?></textarea></td>
			</tr>
		<?php }?>
		<tr>
			<?php 
			if( $callID == 2){?>
				<td colspan = 3 align = "left">
				<input type = "submit" value=  "View Call1 Record" onClick="submitPrev();"></td>
				<td colspan = 3 align = "right">
				<input type = "submit" value=  "Edit Participant Info" onClick="submitEdit();"></td>
				<td colspan = 2 align = "right">
				<input type = "submit" value=  "Save Record" onClick="submitLog();"></td>
			<?php }else{?>
				<td colspan = 4 align = "right">
				<input type = "submit" value=  "Edit Participant Info" onClick="submitEdit();"></td>
				
				<td colspan = 4 align = "right">
				<input type = "submit" value=  "Save Record" onClick="submitLog();" <?php if($_GET['page']){ ?> disabled <?php }?>></td>
			<?php }?>
		</tr>
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
