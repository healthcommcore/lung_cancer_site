<?php 

// LOAD mySQL FUNCTIONS
include("includes/connection.php");
$errMsg="";

// Initialize session and $sid from GET param 
//require '../../includes/initsession.php'; 
//require '../checklogin.php'; 
$dept = $_GET['dept'];
$startDate = $_GET['startDate']; 
$endDate = $_GET['endDate']; 
$startTime = $_GET['startTime']; 
$endTime = $_GET['endTime']; 

$showDate = str_replace('\\', '-', $startDate);
//$tablename='responses_'.$pollno; 
header("Content-disposition: attachment;filename=recruitment_letter_list_$showDate.xls"); 
header("Content-type: application/vnd.ms-excel"); 
header("Pragma: no-cache"); 
header("Expires: 0"); 
$today = date("m/d/Y");
//require '../../includes/dbconnect.php'; 
echo "HD2 Recruitment Letter to be mailed for $dept from $startDate to $endDate between $startTime and $endTime\n\n";
echo "Appointment Date\t Appointment Time\t Patient First Name & Last Name\t Street Address\t City\t State\t Zip\t Gender\t Appointment Provider First & Last Name\t PCP First & Last Name\t Appointment Department\t Appointment day\t Date Recritment Letter Sent"; 
$startDate = formatDate($startDate, 1);
$endDate = formatDate($endDate, 1);
$startTime = formatTime($startTime, 1);
$endTime = formatTime($endTime, 1);
// CONNECT TO DATABASE
if ($mysqlID=dbConnect()) {
    selectDatabase("lung_cancer_user");
} else {
    $errMsg.="<b>ERROR:</b> ".mysql_error()."<br>\n";
}
$recruitLttrArray = getRecruitLttr($dept, $startDate, $endDate, $startTime, $endTime);

//$result = mysql_query($sql, $mysqlID ) or        die("ERROR: Unable to retrieve part_info results from  the database".mysql_error());
if(is_array($recruitLttrArray) && count($recruitLttrArray)>0){
	// first to write the result to an excel file
	reset($recruitLttrArray);
	while(list($key, $val)=each($recruitLttrArray)){
	        //$newstr= str_replace("\r\n", " ",$fetch[4]);    
		echo "\n$val[0]\t$val[1]\t$val[2]\t$val[3]\t$val[4]\t$val[5]\t. $val[6]\t$val[7]\t$val[8]\t$val[9]\t$val[10]\t$val[11]\t$val[12]"; 
	} 
	
	// then update the date recruitment letter send field
	reset($recruitLttrArray);
	while(list($key, $val)=each($recruitLttrArray)){
		 $updFlg = updRecriutLtter($key);
	} 
}else{
	echo "\n\n*******No patients need recruitment letters now!!******";
}
dbClose();; 

?> 


