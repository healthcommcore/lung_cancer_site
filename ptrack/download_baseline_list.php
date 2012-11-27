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
$schedule= $_GET['schedule'];

$showDate = str_replace('\\', '-', $startDate);
if($schedule == "admin"){
	header("Content-disposition: attachment;filename=bl_admin_form_dept=$dept-$showDate.xls"); 
}elseif($schedule == "ra"){
	header("Content-disposition: attachment;filename=ra_schedule_dept=$dept-$showDate.xls"); 
}elseif($schedule == "recept"){
	header("Content-disposition: attachment;filename=receptionist_schedule_dept=$dept-$showDate.xls"); 
}
$today = date("m/d/Y");
header("Content-type: application/vnd.ms-excel"); 
header("Pragma: no-cache"); 
header("Expires: 0"); 
//require '../../includes/dbconnect.php'; 
if($schedule == "admin"){
	echo "HD2 BL Admin Form data for $dept from $startDate to $endDate between $startTime and $endTime\n";
	echo "report generated on $today\n\n";
	echo "Study ID\t MRN\t Patient First Name & Last Name\t Gender\t DOB\t Street Address\t City\t State\t Zip\t Home Phone\t Work Phone\t Appointment Department\t Appointment Date\t Appointment Time\t Appointment Type\t Notes from HVMA\t Appointment Provider First & Last Name\t PCP First & Last Name\t Random Arm\t ";
}elseif($schedule == "ra"){
	echo "HD2 BL RA schedule for $dept from $startDate to $endDate between $startTime and $endTime\n";
	echo "report generated on $today\n\n";
	echo "MRN\t Appointment Date\t Appointment Time\t Patient First Name & Last Name\t Gender\t DOB\t Appointment Type\t Appointment Provider First & Last Name\t PCP First & Last Name\t PCP Review\t PCP Opt-out Deadline\t PCP Opt-out\t Date Recruitment Letter Sent\t Date Recruitment Letter Returned\t Patient Opt-out\t Ok to Approach?\t ";
}elseif($schedule == "recept"){
	echo "HD2 BL Receptionist schedule for $dept from $startDate to $endDate between $startTime and $endTime\n";
	echo "report generated on $today\n\n";
	echo "Appointment Date\t Appointment Time\t Patient First Name & Last Name\t Appointment Type\t Appointment Provider First & Last Name\t PCP First & Last Name\t PCP Review\t Date PCP Notified\t Accepted Clipboard\t Refused Clipboard\t Not Well Visit\t Language Barrier\t Provider Appt Day Opt-out\t Unable to Offer Clipboard\t No-show\t ";
}
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
if($schedule == "admin"){
	$adminInfoArray = getAdminInfo($dept, $startDate, $endDate, $startTime, $endTime);
	//$result = mysql_query($sql, $mysqlID ) or        die("ERROR: Unable to retrieve part_info results from  the database".mysql_error());
	if(is_array($adminInfoArray) && count($adminInfoArray)>0){
		// first to write the result to an excel file
		reset($adminInfoArray);
		while(list($key, $val)=each($adminInfoArray)){
			if($val[7]) $val[7] = ". ".$val[7];   
			echo "\n$key\t$val[0]\t$val[1]\t$val[2]\t$val[3]\t$val[4]\t$val[5]\t".convertState2Abbrev($val[6])."\t$val[7]\t$val[8]\t$val[9]\t$val[10]\t$val[11]\t$val[12]\t$val[13]\t$val[14]\t$val[15]\t$val[16]\t$val[17]"; 
		} 
	}else{
		echo "\n\n*******No patients need scheduling for this period time!!******";
	}
}elseif($schedule == "ra"){
	$raSchedArray = getRaSched($dept, $startDate, $endDate, $startTime, $endTime);
	if(is_array($raSchedArray) && count($raSchedArray)>0){
		// first to write the result to an excel file
		reset($raSchedArray);
		while(list($key, $val)=each($raSchedArray)){  
			echo "\n$val[0]\t$val[1]\t$val[2]\t$val[3]\t$val[4]\t$val[5]\t$val[6]\t$val[7]\t$val[8]\t$val[9]\t$val[10]\t$val[11]\t$val[12]\t$val[13]\t$val[14]\t\t"; 
		} 
	}else{
		echo "\n\n*******No patients need scheduling for this period time!!******";
	}
}else{
	$receptSchedArray = getReceptSched($dept, $startDate, $endDate, $startTime, $endTime);
	//$result = mysql_query($sql, $mysqlID ) or        die("ERROR: Unable to retrieve part_info results from  the database".mysql_error());
	if(is_array($receptSchedArray) && count($receptSchedArray)>0){
		// first to write the result to an excel file
		reset($receptSchedArray);
		while(list($key, $val)=each($receptSchedArray)){   
			echo "\n$val[0]\t$val[1]\t$val[2]\t$val[3]\t$val[4]\t$val[5]\t$val[6]\t$val[7]\t$val[8]\t$val[9]\t"; 
		} 
	}else{
		echo "\n\n*******No patients need scheduling for this period time!!******";
	}
}
dbClose();; 
?> 


