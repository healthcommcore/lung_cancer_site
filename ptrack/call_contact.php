<?php
// LOAD mySQL FUNCTIONS
include("includes/mySQL_functions.php");
$errMsg="";

// CONNECT TO DATABASE
if ($mysqlID=dbConnect()) {
    selectDatabase("befitcouns");
} else {
    $errMsg.="<b>ERROR:</b> ".mysql_error()."<br>\n";
}

$studyID = $_POST["studyID"];
if(!$studyID) $studyID = $_GET['studyID'];
//echo "The id is ".$_POST["studyID"];

// declare some variables
$timeArray = array("8AM-10AM", "10AM-12PM", "12PM-2PM", "2PM-4PM", "4PM-6PM", "6PM-8PM", "8PM-10PM");
$contentName = array("goals", "barriers", "strategies", "action", "assigned");
$addCount = 0;
$conArray = array();
$ptInfoArray = array();
$methArray = array();
$rstArray = array();
$inputConNum = array();
$inputConDate = array();
$inputConTime = array();
$inputConMeth = array();
$inputConRst = array();
$succMsg = "";

// get the callID
//if($_GET['callID']){
	//$callID = $_GET['callID'];
//}else{
	//$callID = $_POST["callID"];
//}
// call fuction to get pt info
//$ptInfoArray = getPartInfo($partID);	
// get coaching call info
//$callInfoArray = getCallInfo($partID, 0);		
// get aprticipant's phone numbers
//$ptPhoneArray = getPreferPhone($partID);
// get the other content of the counseling calls
//$evalArray = getEvaluation();
//get comments
//$comments = getComments($partID);
// get all the phone number
//$phoneArray = array("", "H-".$ptInfoArray[11], "W-".$ptInfoArray[12], "C-".$ptInfoArray[13], "O-".$ptInfoArray[14]);
//$callPreferArray = getCallPrefer($keyID, $call2ID);
//for ($a = 0; $a<count($phoneArray); $a++){
	//if($callPreferArray[1] == $phoneArray[$a]) $phoneArray[$a] = $phoneArray[$a] . "(prefer)";
//}
// get pt's counseling call contents
//$ptEvalArray = getPtEvaluation($studyID, $_SESSION["contactID"]);
// if form was submitted...
if ($_POST["isSubmittedLog"]==1 || $_POST["isSubmittedAdd"]==1 || $_POST["isSubmittedRemove"] == 1){
	if($_POST["isSubmittedAdd"] ==1){
		$lines= $_POST["lineNum"]+1;
	}else{
		$lines= $_POST["lineNum"];
	}
	$counter = 1;
	//get the input vals from submmited page
	for($a = 0; $a <$lines; $a++ ){
		if($_POST["conRemove".$a] != "on"){
			$_POST["conNotes".$a] = ereg_replace ('[\']', '\'', $_POST["conNotes".$a]);
			if($_POST["conNotes".$a]) trim($_POST["conNotes".$a]);
			$inputConArray[$a]=array($counter, $_POST["conDate".$a], $_POST["conTime".$a],
									$_POST["conPhone".$a], $_POST["conRst".$a], 
									$_POST["conNotes".$a],  $_POST["conRemove".$a]);
			$counter++;
		}
	}
	
	for($a = 0; $a<count($contentName); $a++){
		$_POST["contentNotes".$a] = ereg_replace ('[\']', '\'', $_POST["contentNotes".$a]);
		$inputArray[$a] = array($_POST["contentID".$a], $_POST[$contentName[$a].$a], $_POST["contentNotes".$a]);
		//echo $contentName[$a].$a." is ".$_POST["contentID".$a]."==".$_POST[$contentName[$a].$a]."==".$_POST["contentNotes".$a]."<br>\n";
	}
	
	$today = mktime();
	if($_POST["isSubmittedLog"]==1){
		// check if any contact has been entered
		if($lines == 0){
			$lineErr = "<b><font color = 'red'>ERROR: You need to enter at least one form of contact information!!</font></b><br>\n";
			$lineFlg = 1;
		}
		//check to see if all the dates are correct
		for ($a=0; $a<$lines; $a++){
			if($_POST["conDate".$a]){
				//first to check if the date is in correct formate
				$dateFlg = checkValidDate($_POST["conDate".$a]);
				if ($dateFlg){
				    $dateErr = "<b><font color = 'red'>ERROR: ".$dateErrMsg." for ".$_POST["conDate".$a]."!</font></b><br>\n";
					$a=$lines; 
				// then check to see if it's graeter then today
				}else{
					list($month, $day, $year) = split('[/]', $_POST["conDate".$a]);
					$formDate = mktime(0, 0, 0, $month, $day, $year);
					
					// first check to see if entered date is greater then today
				    if ( $formDate> $today){
					    $dateErr = "<b><font color = 'red'>ERROR: You can not record a date that is in the future!!</font></b><br>\n";
						$dateFlg = 1;
						$a=$lines;
					}
				}
			}else{
				$dateFlg =1;
				$dateErr = "<b><font color = 'red'>ERROR: Please enter the date for contact #".$_POST["conNum".$a]."</font></b><br>\n";
				$a=$lines;
			}
		}
		//check to see if all the times are filled out
		for ($a=0; $a<$lines; $a++){
			if(!$_POST["conTime".$a]){
				$timeFlg =1;
				$timeErr = "<b><font color = 'red'>ERROR: Please enter the time for contact #".$_POST["conNum".$a]."</font></b><br>\n";
				$a = $lines;
			}
		}
		//check to see if all the phone number fields are filled out
		for ($a=0; $a<$lines; $a++){
			if(!$_POST["conPhone".$a]){
				$phoneFlg =1;
				$phoneErr = "<b><font color = 'red'>ERROR: Please enter the phone number for contact #".$_POST["conNum".$a]."</font></b><br>\n";
				$a = $lines;
			}
		}
		
		// check to see if contact result has been selected
		for ($a=0; $a<$lines; $a++){
			if($_POST["conRst".$a] <0){
				$rstFlg =1;
				$rstErr = "<b><font color = 'red'>ERROR: Please enter contact result for contact # ".$_POST["conNum".$a]."</font></b><br>\n";
				$a = $lines;
			}
		}
		//update the conlog table if all fields filled out correctly
		if (!$lineFlg && !$dateFlg && !$timeFlg && !$phoneFlg && !$rstFlg){
			// delete all old record from the conlog table
			$sql1="DELETE FROM couns_calls WHERE studyID = \"".$studyID."\" AND contactID = \"".$_SESSION["contactID"]."\"";
			$results=runQuery($sql1);
			if($results["status"]<=0) {
				if ($istErr == ""){$istErr = "<font color = 'red'>There is a problem running delete query</font><br>\n";}
			}
		
			//insert all the record from the page to the conlog table
			for ($a=0; $a<$lines; $a++){
				$conDate = formatDate($_POST["conDate".$a], 1);
				$sql="INSERT INTO couns_calls (studyID, contactID, conNum, conDate, conTime, PhoneNum, resultID, conNotes)
				      values (".$studyID.", ".$_SESSION["contactID"].", ".$_POST["conNum".$a].", '".$conDate."', '".$_POST["conTime".$a]."',
					  '".$_POST["conPhone".$a]."', '".$_POST["conRst".$a]."', '".$_POST["conNotes".$a]."')";
				$results=runQuery($sql);
				if ($results["status"]<=0) {
					if ($istErr == ""){
						$istErr = "<font color = 'red'>There is a problem insert records into the couns_call table!</font><br>\n";
						$istFlg = 1;
						$a = $lines;
					}
				}
			}
			
			// insert into the counseling call content
			if(!$istFlg){
				// delete all old record from the conlog table
				$sql1="DELETE FROM couns_call_contents WHERE studyID = \"".$studyID."\" AND contactID = \"".$_SESSION["contactID"]."\"";
				$results=runQuery($sql1);
				if($results["status"]<=0) {
					if ($istErr == ""){$istErr = "<font color = 'red'>There is a problem running delete query for couns_call_contents</font><br>\n";}
				}
				for($a = 0; $a<count($contentName); $a++){
					if($_POST[$contentName[$a].$a] == "on"){
						$evaluate = 1;
					}else{
						$evaluate = 0;
					}
					$sql="INSERT INTO couns_call_contents (studyID, contactID, contentID, evaluated, contentNotes)
				      values (".$studyID.", ".$_SESSION["contactID"].", ".$_POST["contentID".$a].", '".$evaluate."',
					  '".trim($_POST["contentNotes".$a])."')";
					$results=runQuery($sql);
					if ($results["status"]<=0) {
						if ($istErr == ""){
							$istErr = "<font color = 'red'>There is a problem insert records into the couns_call_content table!</font><br>\n";
							$istFlg = 1;
							$a = count($contentName);
						}
					}
				}
			}
			
			if(!$istFlg){
				$pt_comments = mysql_escape_string($_POST["comments"]);
				//$_POST["comments"] = ereg_replace ('[\']', '\'', $_POST["comments"]);
				$istFlg = istComments($studyID, trim($pt_comments));
				
				if ($istFlg) {
					if ($istErr == ""){
						$istErr = "<font color = 'red'>ERROR: ".$errIstMsg."</font><br>\n";
					}
				}
			}
			if(!$istFlg){
				$succMsg = "<font color = 'green' size = \"+1\">The following records have been successfully saved in the database!</font><br>\n";
			}
		}
	}
	
}elseif ($_POST["isSubmittedPrev"]==1){
	$prevContID = $_SESSION["contactID"] -1;
	$url1 = "previous_contact.php?studyID=".$studyID;
	$url2 = $url1."&contactID=".$prevContID;
	//echo $url2;
	header("Location:".$url2);
	exit(); 
}else{
	
	//get function to get contact info
	$conArray = getConLog($studyID, $_SESSION["contactID"]);
}


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
<head>
	<title>Participant Recruitment Contact</title>
	<script language="javascript">
	<!--
	
	// FUNCTION: SUBMIT INTEERVIEW INFO
	function submitLog() {
		
	    document["form2"]["isSubmittedLog"].value=1;
	    document["form2"].submit();
	}
	//-->
	
	// FUNCTION: SUBMIT ADD NEW LINE
	function submitAdd() {
		//var m = document["form2"]["lineNum"].value;
		//m++;
		//alert("submit the page");
	    
		//document["form2"]["lineNum"].value = m;
	    document["form2"]["isSubmittedAdd"].value=1;
	    document["form2"].submit();
	}
	//-->
	
	// FUNCTION: SUBMIT ADD NEW LINE
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
	</script>
</head>

<body>

<img src="images/blank.gif" width = 90% height = 5px>
<table border = 0>
<tr>
<td width = 40% align = "right"><form method="POST" action="couns_call.php" >
<input type = "submit" value = "back to previous page" size = 8></form></td>
<td  width =600px><img src="images/blank.gif" height = 5px>
</td>
<td><form method="POST" action="login.php" >
<input type = "submit" value = "log out" size = 8></form></td>
</tr>
</table>
</form>
<?

// REPORT ERRORS
if ($lineErr!="") {
    echo $lineErr;
}
if ($dateErr!="") {
    echo $dateErr;
}
if ($timeErr!="") {
    echo $timeErr;
}
if ($phoneErr!="") {
    echo $phoneErr;
}
if ($rstErr!="") {
    echo $rstErr;
}
if ($istErr!=""){
    echo $istErr;
} 
//report success msg
if ($succMsg!=""){
    echo $succMsg;
} 
?>
<h3 align = "center"> <font color="#526D6D"><em>Contacts for Counseling Call #<?php echo $_SESSION["contactID"];?> for <?php echo $ptInfoArray[1]." ".$ptInfoArray[2];?></em></font></h3>
<h4 align = "left"><font color="#526D6D"><em><?php echo "Email Address: ".$ptInfoArray[19];?></em></font></h4>
	<form name= form2 method="POST" action="couns_call_contact.php" >
	<input type="hidden" name="isSubmittedAdd" value=0 ><input type="hidden" name="isSubmittedLog" value=0 >
	<input type="hidden" name="isSubmittedRemove" value=0 >	<input type="hidden" name="studyID" value=<?echo $studyID;?> >
	<input type="hidden" name="isSubmittedPrev" value=0 >
	<?php
		$lineNum = 0;
	    // display page header
		//$header = getHeader($ptInfoArray);
		//echo $header;
		$tableHeader = getTableHeader();
		echo $tableHeader;
		
		$num= 0;
		
		// call function to print out the contact record from the table
		if ($inputConArray <=0){
			$lineNum = count($conArray);
			reset($conArray);
			while(list($key, $conVal)=each($conArray)){
				$result = getConRst($conVal[4]);
				$inputConNum[$num] =  "conNum".$num;
				$inputConDate[$num] =  "conDate".$num;
				$inputConTime[$num] = "conTime".$num;
				$inputConPhone[$num] = "conPhone".$num;
				$inputConRst[$num] = "conRst".$num;
				$inputConNotes[$num] = "conNotes".$num;
				$inputConRemove[$num] = "conRemove".$num;
				$conNumAdd = $conVal[2];
				?>
				<tr align = "center">
				<td width = 15%><img src="images/blank.gif"></td>
				<td><?php echo $conVal[0]; ?><input type ="hidden" name = "<?echo $inputConNum[$num];?>" value = "<?php echo $conVal[0]; ?>" size = 5>
				</td>
				<td><input type ="text" name = "<?php echo $inputConDate[$num];?>" value = "<?php echo $conVal[1]; ?>" size = 15> </td>
			    <td>
				<?php
					echo "<select name = \"".$inputConTime[$num]."\">\n";
					echo "<option value = \"\">Select one</option>\n";
					for($c =0; $c<count($timeArray); $c++){
						if($conVal[2] == $timeArray[$c]){
							echo "<option value = \"".$timeArray[$c]."\" selected>".$timeArray[$c]." </option>\n";
						}else{
							echo "<option value = \"".$timeArray[$c]."\">".$timeArray[$c]." </option>\n";
						}
					}
					echo "</select>\n";
				?>
				</td>
				<td>
					<select name="<?php echo $inputConPhone[$num];?>">
					<?php
						echo "<option value=\"\" >Select one</option>\n";
						reset($ptPhoneArray);
						while(list($keyID, $phoneVal)=each($ptPhoneArray)){
							if($conVal[3] == $phoneVal[1]){
								echo "<option value = \"".$phoneVal[1]."\" selected>".$phoneVal[0]."--".$phoneVal[1]." </option>\n";
							}else{
								echo "<option value = \"".$phoneVal[1]."\">".$phoneVal[0]."--".$phoneVal[1]." </option>\n";
							}
						}
			
					?>
					</select>
				</td>
				<td><select name="<?php echo $inputConRst[$num];?>">
				<?php
					echo $result;
				?>
				</select>
				</td>
				<td><textarea name="<?php $inputConNotes[$num];?>" cols=18 rows=3 wrap><?php $conVal[5] = ereg_replace ('[\]', '', $conVal[5]); echo trim($conVal[5]);?></textarea>
				</td>
				<td><input type ="checkbox" name = "<?echo $inputConRemove[$num];?>" onClick="submitRemove();">
				</td>
				</tr>
			<?php
			$num++;
			}?>
			<tr>
			<td colspan = 8 align = "left">
			<input type = "submit" name = "addLine" value = "Add New Line" onClick="submitAdd();">
			<input type ="hidden" name = "lineNum" value ="<?php echo $lineNum;?>" size = 5>
			</td>
			</tr>
			<?php // ptint out the evaluate items
			reset($evalArray);
			while(list($key, $val)=each($evalArray)){
				echo "<tr>\n";
				if(count($ptEvalArray)>0){
					reset($ptEvalArray);
					while(list($keyID, $contentVal)=each($ptEvalArray)){
						if($val[0] == $contentVal[0]){
							if($contentVal[1] == 1){
								echo "<td colspan = 4><input type = \"hidden\" name = \"contentID".$keyID."\" value = \"".$val[0]."\">
								     <input type = \"checkbox\" name = \"".$contentName[$keyID].$keyID."\" checked> ".$val[1]." </td><br>\n";
							}else{
								echo "<td colspan = 4><input type = \"hidden\" name = \"contentID".$keyID."\" value = \"".$val[0]."\">
								<input type = \"checkbox\" name = \"".$contentName[$keyID].$keyID."\">".$val[1]." </td><br>\n";
							}
							echo "<td colspan = 4> Notes <input type = \"text\" name = \"contentNotes".$keyID."\" value = \"".$contentVal[2]."\" size = 50></td>";
						}
					}
				}else{
					echo "<td colspan = 4><input type = \"hidden\" name = \"contentID".$key."\" value = \"".$val[0]."\">
						<input type = \"checkbox\" name = \"".$contentName[$key].$key."\"> ".$val[1]." </td><br>\n";
					echo "<td colspan = 4> Notes <input type = \"text\" name = \"contentNotes".$key."\" value = \"\" size = 50></td>";
				}
				echo "</tr>\n";
			}
		}
		// if submitted for another row or save the records
		else{
			//get line number
			$lineNum = count($inputConArray);
			//if more there is a date error, then display all the row that have been added
			reset($inputConArray);
			while(list($key, $conVal)=each($inputConArray)){
				$result = getConRst($conVal[4]);
				$inputConNum[$num] =  "conNum".$num;
				$inputConDate[$num] =  "conDate".$num;
				$inputConTime[$num] = "conTime".$num;
				$inputConPhone[$num] = "conPhone".$num;
				$inputConRst[$num] = "conRst".$num;
				$inputConNotes[$num] = "conNotes".$num;
				$inputConRemove[$num] = "conRemove".$num;
				?>
				<tr align = "center">
				<td width = 15%><img src="images/blank.gif"></td>
				<td>
				<?php	
				echo $conVal[0];?><input type ="hidden" name = "<?php echo $inputConNum[$num];?>" value ="<?echo $conVal[0];?>" size = 5>
				</td>
				<td><input type ="text" name = "<?php echo $inputConDate[$num];?>" value ="<?echo $conVal[1];?>" size = 15></td>
				<td>
					<?php
					echo "<select name = \"".$inputConTime[$num]."\">\n";
					echo "<option value = \"\">Select one</option>\n";
					for($c =0; $c<count($timeArray); $c++){
						if($conVal[2] == $timeArray[$c]){
							echo "<option value = \"".$timeArray[$c]."\" selected>".$timeArray[$c]." </option>\n";
						}else{
							echo "<option value = \"".$timeArray[$c]."\">".$timeArray[$c]." </option>\n";
						}
					}
					echo "</select>\n";
					?>
				</td>
				<td>
					<select name="<?php echo $inputConPhone[$num];?>">
					<?php
						echo "<option value=\"\" >Select one</option>\n";
						reset($ptPhoneArray);
						while(list($keyID, $phoneVal)=each($ptPhoneArray)){
							if($conVal[3] == $phoneVal[1]){
								echo "<option value = \"".$phoneVal[1]."\" selected>".$phoneVal[0]."--".$phoneVal[1]." </option>\n";
							}else{
								echo "<option value = \"".$phoneVal[1]."\">".$phoneVal[0]."--".$phoneVal[1]." </option>\n";
							}
						}
					?>
					</select>
				</td>
				<td><select name="<?php echo $inputConRst[$num];?>">
				<?php
					echo $result;
				?>
				</select>
				</td>
				<td><textarea name = "<?php echo $inputConNotes[$num];?>" cols=18 rows=3 wrap><?php  $conVal[5] = ereg_replace ('[\]', '', $conVal[5]); echo trim($conVal[5]); ?> </textarea></td>
				<td><input type ="checkbox" name = "<?echo $inputConRemove[$num];?>" <?php if($conVal[6] == "on"){?> checked <?php }?> onClick="submitRemove();">
				</td>
				</tr>
				<?php
				$num++;
			}?>
			<tr>
			<td colspan = 8 align = "left">
			<input type = "submit" name = "addLine" value = "Add New Line" onClick="submitAdd();">
			<input type ="hidden" name = "lineNum" value ="<?php echo $lineNum;?>" size = 5>
			</td>
			</tr>
			<?php // ptint out the evaluate items
			reset($evalArray);
			while(list($key, $val)=each($evalArray)){
				echo "<tr>\n";
				reset($inputArray);
				while(list($keyID, $contentVal)=each($inputArray)){
					if($val[0] == $contentVal[0]){
						if($contentVal[1] == "on"){
							echo "<td colspan = 4><input type = \"hidden\" name = \"contentID".$keyID."\" value = \"".$val[0]."\">
							  <input type = \"checkbox\" name = \"".$contentName[$keyID].$keyID."\" checked> ".$val[1]." </td><br>\n";
						}else{
							echo "<td colspan = 4><input type = \"hidden\" name = \"contentID".$keyID."\" value = \"".$val[0]."\">
							<input type = \"checkbox\" name = \"".$contentName[$keyID].$keyID."\">".$val[1]." </td><br>\n";
						}
						$contentVal[2] = ereg_replace ('[\]', '', $contentVal[2]); 
						echo "<td colspan = 4> Notes <input type = \"text\" name = \"contentNotes".$keyID."\" value = \"".trim($contentVal[2])."\" size = 50></td>";
					}
				}
				echo "</tr>\n";
			}
		}?>
		
	<tr><td colspan = 8> comments <textarea name="comments" cols=100 rows=5 wrap><?php if (isset($_POST["comments"]) && $_POST["comments"] != '') {
			$tmpcomments = str_replace('\\', '', $_POST["comments"]);
			echo trim($tmpcomments);
		}?></textarea></td>
	</tr>
    </table>
	<br><br>
	<img src = "images/blank.gif" width = 20% height = 5px>
	<input type = "submit" value = "Previous Records" onClick="submitPrev();" <?php if ($_SESSION["contactID"] ==1){?> disabled <?}?>>
	<img src = "images/blank.gif" width = 30% height = 5px>
	<input type = "submit" value = "Save the Records" onClick="submitLog();">
	</form>
<?php dbClose(); ?>
</body>
</html>
