<?php 

// LOAD mySQL FUNCTIONS
include("includes/connection.php");
include("includes/he_function.php");
include("includes/report_function.php");
$errMsg="";

// Initialize session and $sid from GET param 
//require '../../includes/initsession.php'; 
//require '../checklogin.php'; 
$cat = $_GET['cat'];
$co = $_GET['co']; 
$departArray = array("WRXIMA", "WRXIMB", "KENIM4", "KENIM6");
$armArray = array("uc", "mats", "mats+cc");
if($cate == "Site") $cat = "site appt.provider";
$cat = strtolower($cat);
$today = date("m/d/Y");
// report by site and appt provider
if($cat == "site"){
	header("Content-disposition: attachment;filename=recruitment_result_site_offerredClipbd=$co.xls"); 
	header("Content-type: application/vnd.ms-excel"); 
	header("Pragma: no-cache"); 
	header("Expires: 0"); 
	// print out the headers
	echo "\t\t\t\tHd2 recruitment results by $cat \n \t\t\t\tClipboard offered = $co \n \t\t\t\tReport produced $today\n\n";
	echo "Site\t Offered Clipboard?=$co\t Consented\t No-show\t Soft refusal\t Hard refusal\t Provider appt. day opt-out\t Ineligible: Not well\t Ineligible: Language\t Ineligible: Other\t Unable to approach\t Not enrolled:other\t"; 
	
	// CONNECT TO DATABASE
	if ($mysqlID=dbConnect()) {
	    selectDatabase("lung_cancer_user");
	} else {
	    $errMsg.="<b>ERROR:</b> ".mysql_error()."<br>\n";
	}
	// get provider list by department
	$providArray = getProvidList($departArray);
	// get the result
	$resultArray = getPtRecritResult($providArray, $co);
	//$result = mysql_query($sql, $mysqlID ) or        die("ERROR: Unable to retrieve part_info results from  the database".mysql_error());
	if(count($resultArray)>0){
		// first to write the result to an excel file
		reset($resultArray);
		while(list($site, $valArray)=each($resultArray)){
			reset($valArray);
			while(list($key, $val)=each($valArray)){
				if($key == 0){
					echo "\n$site\t$val[0]\t$val[1]\t$val[2]\t$val[3]\t$val[4]\t$val[5]\t$val[6]\t$val[7]\t$val[8]\t$val[9]\t$val[10]";
				}else{
					reset($val);
					while(list($name, $num)=each($val)){
						echo "\n$name\t$num[0]\t$num[1]\t$num[2]\t$num[3]\t$num[4]\t$num[5]\t$num[6]\t$num[7]\t$num[8]\t$num[9]\t$num[10]"; 
					}
				}
			}
		} 
		
	}else{
		echo "\n\n*******No Records In This Report!!******";
	}
// report by randomization
}elseif($cat == "randomization"){
	header("Content-disposition: attachment;filename=recruitment_result_randomization_offerredClipbd=$co.xls"); 
	header("Content-type: application/vnd.ms-excel"); 
	header("Pragma: no-cache"); 
	header("Expires: 0"); 
	// print out the headers
	echo "\t\t\t\tHd2 recruitment results by $cat \n \t\t\t\tClipboard offered = $co \n \t\t\t\tReport produced $today\n\n";
	echo "Randomization Arm\t Offered Clipboard?=$co\t Consented\t No-show\t Soft refusal\t Hard refusal\t Provider appt. day opt-out\t Ineligible: Not well\t Ineligible: Language\t Ineligible: Other\t Unable to approach\t Not enrolled:other\t"; 
	
	// CONNECT TO DATABASE
	if ($mysqlID=dbConnect()) {
	    selectDatabase("lung_cancer_user");
	} else {
	    $errMsg.="<b>ERROR:</b> ".mysql_error()."<br>\n";
	}
	// get provider list by random arm
	$providArray = getProvidRand($armArray);
	// last, get the bl result break down
	$resultArray = getPtRecritResult($providArray, $co);
	//$result = mysql_query($sql, $mysqlID ) or        die("ERROR: Unable to retrieve part_info results from  the database".mysql_error());
	if(count($resultArray)>0){
		// first to write the result to an excel file
		reset($resultArray);
		while(list($rand, $valArray)=each($resultArray)){
			reset($valArray);
			while(list($key, $val)=each($valArray)){
				if($key == 0){
					echo "\n$rand\t$val[0]\t$val[1]\t$val[2]\t$val[3]\t$val[4]\t$val[5]\t$val[6]\t$val[7]\t$val[8]\t$val[9]\t$val[10]";
				}else{
					reset($val);
					while(list($name, $num)=each($val)){
						echo "\n$name\t$num[0]\t$num[1]\t$num[2]\t$num[3]\t$num[4]\t$num[5]\t$num[6]\t$num[7]\t$num[8]\t$num[9]\t$num[10]"; 
					}
				}
			}
		} 
		
	}else{
		echo "\n\n*******No Records In This Report!!******";
	}
}else{
	header("Content-disposition: attachment;filename=recruitment_result_RA_offerredClipbd=$co.xls"); 
	header("Content-type: application/vnd.ms-excel"); 
	header("Pragma: no-cache"); 
	header("Expires: 0"); 
	// print out the headers
	echo "\t\t\t\tHd2 recruitment results by $cat \n \t\t\t\tClipboard offered = $co \n \t\t\t\tReport produced $today\n\n";
	echo "Baseline RA\t Offered Clipboard?=$co\t Consented\t No-show\t Soft refusal\t Hard refusal\t Provider appt. day opt-out\t Ineligible: Not well\t Ineligible: Language\t Ineligible: Other\t Unable to approach\t Not enrolled:other\t"; 
	
	// CONNECT TO DATABASE
	if ($mysqlID=dbConnect()) {
	    selectDatabase("lung_cancer_user");
	} else {
	    $errMsg.="<b>ERROR:</b> ".mysql_error()."<br>\n";
	}
	// get list of RA
	$raArray = getBlRAList();
	// get pt list with bl ra 1 and bl ra 2
	$raPtArray = getRaPtList($raArray);
	// last, get the bl result break down
	$resultArray = getRaResult($raPtArray, $co);
	//$result = mysql_query($sql, $mysqlID ) or        die("ERROR: Unable to retrieve part_info results from  the database".mysql_error());
	if(count($resultArray)>0){
		// first to write the result to an excel file
		reset($resultArray);
		while(list($ra1, $valArray)=each($resultArray)){
			reset($valArray);
			while(list($key, $val)=each($valArray)){
				if($key == 0){
					echo "\n$ra1\t$val[0]\t$val[1]\t$val[2]\t$val[3]\t$val[4]\t$val[5]\t$val[6]\t$val[7]\t$val[8]\t$val[9]\t$val[10]";
				}else{
					reset($val);
					while(list($ra2, $num)=each($val)){
						echo "\n$ra2\t$num[0]\t$num[1]\t$num[2]\t$num[3]\t$num[4]\t$num[5]\t$num[6]\t$num[7]\t$num[8]\t$num[9]\t$num[10]"; 
					}
				}
			}
		} 
		
	}else{
		echo "\n\n*******No Records In This Report!!******";
	}
}
dbClose();; 

?> 


