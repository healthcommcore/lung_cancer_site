<?php
// LOAD mySQL FUNCTIONS
include("includes/connect.php");
$errMsg="";

// CONNECT TO DATABASE
if ($mysqlID=dbConnect()) {
    selectDatabase("lung_cancer_user");
} else {
    $errMsg.="<b>ERROR:</b> ".mysql_error()."<br>\n";
}

// declare some variables 
$ptDueArray = array();
$ptOverDueArray = array();

// check form submission
if ($_POST["isSubmittedLogOut"]==1) {
	header("Location: login.php");
	exit();
}
// determin the due date
$element = $_SESSION["contactID"]-1;
$dueDate1 = $counsCallWeek[$element]*7;
$dueDate2 = $counsCallWeek[$element]*7 +7;

//call function to get lis of participants that due for  counseling calls
$ptDueArray = getCounsCallDue($_SESSION["staffID"], $dueDate1, $dueDate2, $dueDate1);

// filter the participant in the due list by checking the last contact result
reset($ptDueArray);
while(list($keyID, $ptVal)=each($ptDueArray)){
	$resultFlg = getLastContact($keyID, $_SESSION["contactID"]);
	if($resultFlg){
		$ptDuConArray[$keyID] = $ptVal;
	}
}
//  8 days after call schedule date is a over due
$overDue = 8;
// get list of parcipants that is over due for the counseling call
if( $_SESSION["contactID"] != count($counsCallWeek)){
	$element2 = $element +1;
	$beginDate = $counsCallWeek[$element]*7 + $overDue;
	$endDate = ($counsCallWeek[$element2]*7)-1;
	$ptOverArray = getCounsCallOvDu($_SESSION["staffID"], $beginDate, $endDate, $dueDate1);
}else{
	$beginDate = $counsCallWeek[$element]*7 + $overDue;
	$ptOverArray = getCounsCallOvDu($_SESSION["staffID"], $beginDate, 0, $dueDate1);
}

// filter the participant in the overdue list by checking the last contact result
reset($ptOverArray);
while(list($keyID, $ptVal)=each($ptOverArray)){
	$resultFlg = getLastContact($keyID, $_SESSION["contactID"]);
	if($resultFlg){
		$ptOverConArray[$keyID] = $ptVal;
	}
}

// determin the not yet due list except for the first call
if($_SESSION["contactID"] != 1){
	// the date rang should be between previous due date +1 and current due date
	$element = $_SESSION["contactID"]-2;
	$notDueDate1 = $counsCallWeek[$element]*7+1;
	$notDueDate2 = $dueDate1 -1;
	//call function to get lis of participants that due for  counseling calls
	$ptNotDueArray = getCounsCallDue($_SESSION["staffID"], $notDueDate1, $notDueDate2, $dueDate1);
	
	// filter the participant in the due list by checking the last contact result
	reset($ptNotDueArray);
	while(list($keyID, $ptVal)=each($ptNotDueArray)){
		$resultFlg = getLastContact($keyID, $_SESSION["contactID"]);
		if($resultFlg){
			$ptNotDuConArray[$keyID] = $ptVal;
		}
	}
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
<head>
	<title>Counseling Call List</title>
</head>

<body>
<img src="images/blank.gif" width = 90% height = 5px>
<table border = 0>
<tr>
<td width = 40% align = "right"><form method="POST" action="staff_home.php?title=CHW" >
<input type = "submit" value = "back to home page" size = 8></form></td>
<td  width =600px><img src="images/blank.gif" height = 5px>
</td>
<td><form method="POST" action="login.php" >
<input type = "submit" value = "log out" size = 8></form></td>
</tr>
</table>
</form>
<?
// print out the list for over due
$counter = 0;
if (count($ptOverConArray)>0) {?>
	<h4 align = "center"> <font color="#526D6D"><em>List of Participants that Over Due for Counseling Call #<?php echo $_SESSION["contactID"];?></em></font></h4>
	<img src="images/blank.gif" width = 100% height = 10px>
	<table border = 1 align="center" cellpadding=8 cellspacing = 2 width = 80%>
	<tr>
	</tr>
	<tr  bgcolor = "#95B9C7" align = "center">
	<td width = 16%><strong> Study ID </strong></td>
	<td width = 16%><strong> First Name </strong></td>
	<td width = 16%><strong> Last Name </strong></td>
	<td width = 16%><strong> Due Date </strong></td>
	<td colspan = 2><img src="images/blank.gif"></td>
	</tr>
	<?
	reset ($ptOverConArray);
	while(list($keyID, $ptVal)=each($ptOverConArray)){
		//echo "The site name is " .$sitKey. "<br>";
	    reset($ptVal);
		$ptCount = count($ptVal);
		if ($counter % 2 ==0){
			    $color = "#AFC7C7";
		}else{
			    $color = "#CFECEC";
		}
		echo "<form name = form1 method=\"POST\" action = \"couns_call_contact.php\">\n";
		echo "<tr align = \"center\" bgcolor = ".$color.">\n";
		echo "<td>" .$keyID. "<input type = \"hidden\" name = \"studyID\" value = \"".$keyID."\"></td>\n";
		echo "<td>" .$ptVal[0]. "<input type = \"hidden\" name = \"fName\" value = \"".$ptVal[0]."\"></td>\n";
		echo "<td>" .$ptVal[1]. "<input type = \"hidden\" name = \"lName\" value = \"".$ptVal[1]."\"></td>\n";
		echo "<td>" .$ptVal[2]."</td>\n";
		echo "<td><input type = \"submit\" name = \"action\" value = \"Counseling Calls\"></td>\n";
		echo "</tr></form>\n";
		$counter++;
	}
	echo "</table>\n";
}else{
    echo "<font color=\"#526D6D\" size = \"+1\"><p align = \"center\">no case over due for counseling call #". $_SESSION["contactID"]."!</p></font>";
}

echo "<br><br>\n";
// print out the cases due for counsing calls
$counter = 0;
if (count($ptDuConArray)>0) {?>
	<h4 align = "center"> <font color="#526D6D"><em>List of Participants that Due for Counseling Call # <?php echo $_SESSION["contactID"];?></em></font></h4>
	<img src="images/blank.gif" width = 100% height = 10px>
	<table border = 1 align="center" cellpadding=8 cellspacing = 2 width = 80%>
	<tr>
	</tr>
	<tr  bgcolor = "#95B9C7" align = "center">
	<td width = 16%><strong> Study ID </strong></td>
	<td width = 16%><strong> First Name </strong></td>
	<td width = 16%><strong> Last Name </strong></td>
	<td width = 16%><strong> Due Date </strong></td>
	<td colspan = 2><img src="images/blank.gif"></td>
	</tr>
	<?
	reset ($ptDuConArray);
	while(list($keyID, $ptVal)=each($ptDuConArray)){
		//echo "The site name is " .$sitKey. "<br>";
	    reset($ptVal);
		$ptCount = count($ptVal);
		if ($counter % 2 ==0){
			    $color = "#AFC7C7";
		}else{
			    $color = "#CFECEC";
		}
		echo "<form name = form1 method=\"POST\" action = \"couns_call_contact.php\">\n";
		echo "<tr align = \"center\" bgcolor = ".$color.">\n";
		echo "<td>" .$keyID. "<input type = \"hidden\" name = \"studyID\" value = \"".$keyID."\"></td>\n";
		echo "<td>" .$ptVal[0]. "<input type = \"hidden\" name = \"fName\" value = \"".$ptVal[0]."\"></td>\n";
		echo "<td>" .$ptVal[1]. "<input type = \"hidden\" name = \"lName\" value = \"".$ptVal[1]."\"></td>\n";
		echo "<td>" .$ptVal[2]."</td>\n";
		echo "<td><input type = \"submit\" name = \"action\" value = \"Counseling Calls\"></td>\n";
		echo "</tr></form>\n";
		$counter++;
	}
	echo "</table>\n";
}else{
    echo "<font color=\"#526D6D\" size = \"+1\"><p align = \"center\">no case due for counseling call #". $_SESSION["contactID"]."!</p></font>";
}

echo "<br><br>\n";
// print out the cases due for not yet due for this counsing calls
$counter = 0;
if (count($ptNotDuConArray)>0) {?>
	<h4 align = "center"> <font color="#526D6D"><em>List of Participants that Not Yet Due for Counseling Call # <?php echo $_SESSION["contactID"];?></em></font></h4>
	<img src="images/blank.gif" width = 100% height = 10px>
	<table border = 1 align="center" cellpadding=8 cellspacing = 2 width = 80%>
	<tr>
	</tr>
	<tr  bgcolor = "#95B9C7" align = "center">
	<td width = 16%><strong> Study ID </strong></td>
	<td width = 16%><strong> First Name </strong></td>
	<td width = 16%><strong> Last Name </strong></td>
	<td width = 16%><strong> Due Date </strong></td>
	<td colspan = 2><img src="images/blank.gif"></td>
	</tr>
	<?
	reset ($ptNotDuConArray);
	while(list($keyID, $ptVal)=each($ptNotDuConArray)){
		//echo "The site name is " .$sitKey. "<br>";
	    reset($ptVal);
		$ptCount = count($ptVal);
		if ($counter % 2 ==0){
			    $color = "#AFC7C7";
		}else{
			    $color = "#CFECEC";
		}
		echo "<form name = form1 method=\"POST\" action = \"couns_call_contact.php\">\n";
		echo "<tr align = \"center\" bgcolor = ".$color.">\n";
		echo "<td>" .$keyID. "<input type = \"hidden\" name = \"studyID\" value = \"".$keyID."\"></td>\n";
		echo "<td>" .$ptVal[0]. "<input type = \"hidden\" name = \"fName\" value = \"".$ptVal[0]."\"></td>\n";
		echo "<td>" .$ptVal[1]. "<input type = \"hidden\" name = \"lName\" value = \"".$ptVal[1]."\"></td>\n";
		echo "<td>" .$ptVal[2]."</td>\n";
		echo "<td><input type = \"submit\" name = \"action\" value = \"Counseling Calls\"></td>\n";
		echo "</tr></form>\n";
		$counter++;
	}
	echo "</table>\n";
}elseif(count($ptNotDuConArray)<=0 && $_SESSION["contactID"] != 1){
    echo "<font color=\"#526D6D\" size = \"+1\"><p align = \"center\">no case is not yet due for counseling call #". $_SESSION["contactID"]."!</p></font>";
}
dbClose();
?>
</body>
</html>
