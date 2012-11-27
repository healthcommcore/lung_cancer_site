<?php
include("includes/connection.php");
include("includes/he_function.php");
$errMsg="";

header("Content-disposition: attachment;filename=report_for_call2.xls"); 
header("Content-type: application/vnd.ms-excel"); 
header("Pragma: no-cache"); 
header("Expires: 0");

//report header
echo "Report for call 2\n\n";

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
// get the he ID
$heID = $_GET['heID'];

// declare some variables 
$ptRstArray = array();
$ptDueArray = array();
$ptOverDueArray = array();
$ptNotDueArray = array();
$compeleteArray = array();
$noContactArray = array();
$ptDuConArray = array();
$ptOverConArray = array();
$ptNotDuConArray = array();
$ptIncompConArray = array();

$dueDate = 14;
$overDue = 24;
$overDueEndDate = 41;
$callID = 1;
$call2ID = 2;
// get the pt list that has contact result (complete, unreachable, refused) for call 1 
$ptRstArray = getCall1Result($heID, $callID);
// get the due date based on the call 1 result
reset($ptRstArray);
while(list($keyID, $val)=each($ptRstArray)){
	$catArray = getCallCat($keyID, $val[2], $val[3], $val[4]);
	// if the flg = 0, then it is a not yet due
	if($catArray[1] == 0){
		// get the coaching call prefer info
		$callNotes= getGenNotes($keyID);
		$ptNotDueArray[$keyID] = array($val[0], $val[1], $catArray[0], $callNotes, $val[2]);
	// if the flg = 1, then it is a due
	}elseif($catArray[1] == 1){
		// get the coaching call prefer info
		$callNotes= getGenNotes($keyID);
		$ptDueArray[$keyID] = array($val[0], $val[1], $catArray[0], $callNotes, $val[2]);
	// otherwise is overdue
	}else{
		// get the coaching call prefer info
		$callNotes= getGenNotes($keyID);
		$ptOverDueArray[$keyID] = array($val[0], $val[1], $catArray[0], $callNotes, $val[2]);
	}
}
//echo print_r($ptNotDueArray);

// filter the participant in the due list by checking the last contact result
reset($ptDueArray);
while(list($keyID, $ptVal)=each($ptDueArray)){
	$resultID = getLastCall2($keyID, $call2ID);
	// if the result for call 1 and call2 are both complete
	// then put in the both completed sub-menu
	if($resultID == 10 ){
		if($ptVal[4] == 10){
			$compeleteArray[$keyID] = $ptVal;
		}else{
			$noContactArray[$keyID] = $ptVal;
		}
	}elseif($resultID == 11 || $resultID == 12){
		$noContactArray[$keyID] = $ptVal;
	}else{
		$ptDuConArray[$keyID] = $ptVal;
	}
}
//  filter the participant in the over due list by checking the last contact result
reset($ptOverDueArray);
while(list($keyID, $ptVal)=each($ptOverDueArray)){
	$resultID = getLastCall2($keyID, $call2ID);
	if($resultID == 10 ){
		if($ptVal[4] == 10){
			$compeleteArray[$keyID] = $ptVal;
		}else{
			$noContactArray[$keyID] = $ptVal;
		}
	}elseif($resultID == 11 || $resultID == 12){
		$noContactArray[$keyID] = $ptVal;
	}else{
		$ptOverConArray[$keyID] = $ptVal;
	}
}
//call function to get list of participants that not yet due for coaching calls
reset($ptNotDueArray);
while(list($keyID, $ptVal)=each($ptNotDueArray)){
	$resultID = getLastCall2($keyID, $call2ID);
	if($resultID == 10 ){
		if($ptVal[4] == 10){
			$compeleteArray[$keyID] = $ptVal;
		}else{
			$noContactArray[$keyID] = $ptVal;
		}
	}elseif($resultID == 11 || $resultID == 12){
		$noContactArray[$keyID] = $ptVal;
	}else{
		$ptNotDuConArray[$keyID] = $ptVal;
	}
}

// print out the list for over due
if (count($ptOverConArray)>0) {
	echo "Overdue for Coaching Call 2\n";
	echo "Study ID\t First Name\t Last Name\t Due Date\t Notes"; 
	getReportContent($ptOverConArray, $heID, $call2ID);
}else{
    echo "No overdue cases for Coaching Call 2!\n";
}

echo "\n\n";
// print out the cases due for counsing calls
if (count($ptDuConArray)>0) {
	echo "List of participants that are due for Coaching Call 2\n";
	echo "Overdue for Coaching Call 2\n";
	echo "Study ID\t First Name\t Last Name\t Due Date\t Notes"; 
	getReportContent($ptDuConArray, $heID, $call2ID);
	
}else{
    echo "No cases due for coaching call 2!\n";
}

echo "\n\n";
// print out the cases due for not yet due for this counsing calls
if (count($ptNotDuConArray)>0) {
	echo "List of participants that areNot Yet Due for Coaching Call 2\n";
	echo "Overdue for Coaching Call 2\n";
	echo "Study ID\t First Name\t Last Name\t Due Date\t Notes"; 
	getReportContent($ptNotDuConArray, $heID, $call2ID);
}else{
    echo "No cases pending that are not yet due for Coaching Call 2!\n";
}

dbClose();
?>
		       