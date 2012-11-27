<?php 

// LOAD mySQL FUNCTIONS
include("includes/connection.php");
$errMsg="";

// Initialize session and $sid from GET param 
//require '../../includes/initsession.php'; 
//require '../checklogin.php'; 

//$fromDate = $_GET['fromDate']; 
//$toDate = $_GET['toDate']; 
//$tablename='responses_'.$pollno; 
header("Content-disposition: attachment;filename=pcp_review_list.xls"); 
header("Content-type: application/vnd.ms-excel"); 
header("Pragma: no-cache"); 
header("Expires: 0"); 
$today = date("m/d/Y");
//require '../../includes/dbconnect.php'; 
echo "HD2 PCP's to be notified for opt-out $today\n";
echo "PCP First Name & PCP Last Name\t Appointment Date\t Appointment Time\t Appointment Department\t Patient First Name & Patient Last Name\t MRN\t"; 

// CONNECT TO DATABASE
if ($mysqlID=dbConnect()) {
    selectDatabase("lung_cancer_user");
} else {
    $errMsg.="<b>ERROR:</b> ".mysql_error()."<br>\n";
}
$pcpReviewArray = getPCPreview();

//$result = mysql_query($sql, $mysqlID ) or        die("ERROR: Unable to retrieve part_info results from  the database".mysql_error());
if(count($pcpReviewArray)>0){
	// first to write the result to an excel file
	reset($pcpReviewArray);
	while(list($key, $val)=each($pcpReviewArray)){
	        //$newstr= str_replace("\r\n", " ",$fetch[4]);    
		echo "\n$val[0]\t$val[1]\t$val[2]\t$val[3]\t$val[4]\t$val[5]"; 
	} 
	
	// then update the date pcp notify field
	reset($pcpReviewArray);
	while(list($key, $val)=each($pcpReviewArray)){
		 $updFlg = updPcpDate($key);
	} 
}else{
	echo "\n\n*******No patients need PCP review now!!******";
}
dbClose();; 

?> 


