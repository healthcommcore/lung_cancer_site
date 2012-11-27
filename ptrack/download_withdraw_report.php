<?php 

// LOAD mySQL FUNCTIONS
include("includes/connection.php");
include("includes/he_function.php");
include("includes/report_function.php");
$errMsg="";

// Initialize session and $sid from GET param 
//require '../../includes/initsession.php'; 
//require '../checklogin.php'; 
$armArray = array("uc", "mats", "mats+cc");
$today = date("m/d/Y");
// report by site and appt provider

header("Content-disposition: attachment;filename=withdraw_report.xls"); 
header("Content-type: application/vnd.ms-excel"); 
header("Pragma: no-cache"); 
header("Expires: 0"); 
// print out the headers
echo "\t\t\t\tHd2 withdrawals from study to date \n \t\t\t\tReport produced $today\n\n";
echo "Ramdomization arm\t Total consented\t Total withdrawals\t No longer interested\t Lack of time\t Loss of internet, doesn't want print\t Health\t Pregnancy\t Study protocols\t Death\t Other\t"; 

// CONNECT TO DATABASE
if ($mysqlID=dbConnect()) {
    selectDatabase("lung_cancer_user");
} else {
    $errMsg.="<b>ERROR:</b> ".mysql_error()."<br>\n";
}
// get total consent
$resultArray = getTotalConst($armArray);
if(count($resultArray)>0){
	// first to write the result to an excel file
	reset($resultArray);
	while(list($arm, $valArray)=each($resultArray)){
		echo "\n$arm\t$valArray[0]\t$valArray[1]\t$valArray[2]\t$valArray[3]\t$valArray[4]\t$valArray[5]\t$valArray[6]\t$valArray[7]\t$valArray[8]\t$valArray[9]\t"; 
		if($arm == "Usual care" || $arm == "Material Only" || $arm == "Coaching calls"){
			$totalConst = $totalConst + $valArray[0];
			$totalWithd = $totalWithd + $valArray[1];
			$totalNoIntest = $totalNoIntest + $valArray[2];
			$totalNoTime = $totalNoTime + $valArray[3];
			$totalNoWeb = $totalNoWeb + $valArray[4];
			$totalHealth = $totalHealth + $valArayy[5];
			$totalPreg = $totalPreg + $valArray[6];
			$totalStudy = $totalStudy + $valArray[7];
			$totalDeath = $totalDeath + $valArray[8];
			$totalOther = $totalOther + $valArray[9];
		}
	} 
	echo "\nTOTAL\t$totalConst\t$totalWithd\t$totalNoIntest\t$totalNoTime\t$totalNoWeb\t$totalHealth\t$totalPreg\t$totalStudy\t$totalDeath\t$totalOther\t";
}else{
	echo "\n\n*******No Records In This Report!!******";
}

dbClose();; 

?> 


