<?php 

// LOAD mySQL FUNCTIONS
include("includes/connection.php");
include("includes/report_function.php");
$errMsg="";

header("Content-disposition: attachment;filename=pcp_review_list.xls"); 
header("Content-type: application/vnd.ms-excel"); 
header("Pragma: no-cache"); 
header("Expires: 0"); 
$today = date("m/d/Y");
//require '../../includes/dbconnect.php'; 
echo "HD2 participants that withdrew from the study $today\n";
echo "Study ID\t Patient First Name & Patient Last Name\t DOB\t Gender\t\n"; 

// CONNECT TO DATABASE
if ($mysqlID=dbConnect()) {
    selectDatabase("lung_cancer_user");
} else {
    $errMsg.="<b>ERROR:</b> ".mysql_error()."<br>\n";
}
$ptArray = getHd2Pts();

//$result = mysql_query($sql, $mysqlID ) or        die("ERROR: Unable to retrieve part_info results from  the database".mysql_error());
if(count($ptArray)>0){
	// first to write the result to an excel file
	reset($ptArray);
	while(list($key, $val)=each($ptArray)){
	        //$newstr= str_replace("\r\n", " ",$fetch[4]);    
		echo "\n$key\t$val[0]\t$val[1]\t$val[2]"; 
	} 
	
}else{
	echo "\n\n*******No patients need PCP review now!!******";
}
dbClose();; 

?> 


