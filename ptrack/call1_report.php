<?php
include("includes/connection.php");
include("includes/he_function.php");
$errMsg="";

header("Content-disposition: attachment;filename=report_for_call1.xls"); 
header("Content-type: application/vnd.ms-excel"); 
header("Pragma: no-cache"); 
header("Expires: 0");

//report header
echo "Report for call 1\n\n";

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
$ptDueArray = array();
$ptOverDueArray = array();
$ptNotDueArray = array();
$dueDate = 14;
$overDue = 24;
$overDueEndDate = 41;
$callID = 1;
//call function to get lis of participants that due for coaching calls
$ptDueArray = getCounsCallDue($heID, $dueDate, $overDue, $dueDate);
// filter the participant in the due list by checking the last contact result
$ptDuConArray = getFinalCallReport($ptDueArray, $callID);

//  get list of pt that is a over due
$ptOverArray = getCounsCallOvDu($heID, $overDue, $overDueEndDate, $dueDate);
// filter the participant in the due list by checking the last contact result
$ptOverConArray = getFinalCallReport($ptOverArray, $callID);

//call function to get lis of participants that not yet due for coaching calls
$ptNotDueArray = getCounsCallDue($heID, 0, $dueDate, $dueDate);
$ptNotDuConArray = getFinalCallReport($ptNotDueArray, $callID);

//call function to get list of participants that call 1 incomplete
$ptIncompArray = getCounsCallOvDu($heID, 0, $overDueEndDate, $dueDate);
$ptIncompConArray = getFinalCallReport($ptIncompArray, $callID);

// print out the cases that call 1 pass 41 days - incomplete
if (count($ptIncompConArray)>0) {
	echo "Call 1 Incomplete, Switch to Call 2\n";
	echo "Study ID\t First Name\t Last Name\t Due Date\t Notes";
	getReportContent($ptIncompConArray, $heID, $callID);
}else{
    echo "No incomplete cases for Call 1\n";
}

echo "\n\n";
// print out the list for over due
if (count($ptOverConArray)>0) {
	echo " Overdue for Coaching Call 1\n";
	echo "Study ID\t First Name\t Last Name\t Due Date\t Notes";
	getReportContent($ptOverConArray, $heID, $callID);
}else{
    echo "No overdue cases for Coaching Call 1!\n";
}

echo "\n\n";
// print out the cases due for counsing calls
if (count($ptDuConArray)>0) {
	echo "List of participants that are due for Coaching Call 1\n";
	echo "Study ID\t First Name\t Last Name\t Due Date\t Notes";
	getReportContent($ptDuConArray, $heID, $callID);
}else{
    echo "No cases due for Coaching Call 1!\n";
}

echo "\n\n";
// print out the cases due for not yet due for this counsing calls
if (count($ptNotDuConArray)>0) {
	echo "Not Yet Due for Coaching Call 1\n";
	echo "Study ID\t First Name\t Last Name\t Due Date\t Notes";
	getReportContent($ptNotDuConArray, $heID, $callID);
	
}else{
    echo "No cases pending that are not yet due for Coaching Call 1!\n";
}

dbClose();
?>
		       
