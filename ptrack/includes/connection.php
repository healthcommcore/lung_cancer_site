<?php
// FUNCTION: DATABASE CONNECT
GLOBAL $mysqlID;
function dbConnect() {
	//$mysqlID = mysql_connect("localhost","lung_cancer_site", "hd2please"); // test 
	$mysqlID = mysql_connect("localhost","lung_cancer_site","439cwYY39ndB") or die ('Connection failed.'); // live
	// $mysqlID = mysql_connect("localhost","lung_cancer_site","") or die ('Connection failed.'); // for now
    return $mysqlID;
}
// FUNCTION: SELECT DATABASE
function selectDatabase($dbID) {
    GLOBAL $mysqlID;
    mysql_select_db($dbID,$mysqlID);
	//echo "The db is ".mysql_select_db($dbID,$mysqlID);
}
// FUNCTION: DATABASE CLOSE
function dbClose() {
    GLOBAL $mysqlID;
    mysql_close($mysqlID);
}

// FUNCTION: RUN QUERY
function runQuery($sql) {
    GLOBAL $mysqlID;
    $retVals=array();
	$resultID = "";
	//echo "entering run sql<br>";
	//echo "$sql<br>";
    if ($resultID=mysql_query($sql,$mysqlID)) {
		//echo "good sql<br>";
        $retVals["status"]=1;
        if (substr($sql,0,6)=="SELECT") {
			//echo "select"."<br>";
            $retVals["numRows"]=mysql_num_rows($resultID);
            if (mysql_num_rows($resultID)>0) {
                $row=0;
                while ($recordSpecs=mysql_fetch_array($resultID,MYSQL_ASSOC)) {
                    $retVals["returnedRows"][$row]=$recordSpecs;
                    $row++;
                }
            }
		}
    } else {
	 	//echo "bad sql<br>";
        $retVals["status"]=(0-1);
        $retVals["error"]=mysql_error();
		
		//echo $sql."<br>\n";
    }
	//print_r($retVals);
    return $retVals;
}

//FUNCTION: PRINT OUT THE MENU FOR RA PAGES
function getMenu() {
	 $link1 = "<ul><a href=\"add_part.php\">Add Participant </a></ul><br>";
	 $link2 = "<ul><a href=\"search_part.php\" >Search Participant </a></ul><br>";
	 $link3 = "<ul><a href=\"pcp_review.php\">Participant List for PCP Review</a></ul><br>";
	 $link4 = "<ul><a href=\"recruit_list.php\">Participant List for Recruitment Letter</a></ul><br>";
	 $link5 = "<ul><a href=\"baseline_list.php\" >Participant List for Baseline Appointment</a></ul><br>";
	 $link6 = "<ul><a href=\"raffle.php\">Raffle Drawing</a></ul><br>";
	 $link7 = "<ul><a href=\"exportTFR.php\">Generate TFR #1 Mailing </a></ul><br>";
	 $link8 = "<ul><a href=\"get_qc_report.php\">Quality Control Reports</a></ul><br>";
	 $menu = $link1.$link2.$link3.$link4.$link5.$link6.$link7.$link8;
	 return $menu;
}

//FUNCTION: PRINT OUT THE MENU FOR HE PAGES
function getMenu1($heID) {
	 $link1 = "<ul><a href=\"call1_list.php?heID=".$heID."\">Coaching Call 1 </a></ul><br>";
	 $link2 = "<ul><a href=\"call2_list.php?heID=".$heID."\" >Coaching Call 2</a></ul><br>";
	 $link3 = "<ul><a href=\"search_part.php?heID=".$heID."\">Search Participant</a></ul><br>";
	 $link4 = "<ul><a href=\"call1_report.php?heID=".$heID."\" disabled>Report for Call 1</a></ul><br>";
	 $link5 = "<ul><a href=\"call2_report.php?heID=".$heID."\" disabled>Report for Call 2</a></ul><br>";
	 $menu = $link1.$link2.$link3.$link4.$link5;
	 return $menu;
}

//FUNCTION: DATE VALIDATION
function checkValidDate($inputDate) {
    GLOBAL $dateErrMsg;
	$dateErrMsg = "";
    if((strlen($inputDate)<8)OR(strlen($inputDate)>10)){
            $dateErrMsg = "Please enter the date in 'mm/dd/yyyy' format";
		    return 1;
	}else{
	    //The entered value is checked for proper Date format
        if((substr_count($inputDate,"/"))<>2){
                $dateErrMsg = "Please enter the date in 'mm/dd/yyyy' format";
			    return 1;
		}else{
		    list($month, $day, $year) = split('[/]', $inputDate);
			// check if it is a valide month
			$result=ereg("^[0-9]+$",$month, $reg);
            if (!($result)){
		        $dateErrMsg = "Please enter a valid Month";
				return 1;
			}else{
                if(($month != "00") && (($month <= 0) OR ( $month > 12))){
				    $dateErrMsg = "Please enter a valid Month between 1-12";
					return 1;
				}elseif (($month == "00" && $day != "00") OR ($month == "00" && $year != "0000")){
					$dateErrMsg = "Please enter a valid Month between 1-12";
					return 1;
					
				}
             }
			 
			//check if it is a valide day
		    $result=ereg("^[0-9]+$",$day, $reg);
            if (!($result)){
			    $dateErrMsg = "<b>Please enter a valid Day</b>";
				return 1;
			}else{
                if(($day != "00") && (($day <= 0) OR ( $day > 31))){
				    $dateErrMsg = "Please enter a valid Day between 1-31";
					return 1;
				}elseif(($day == "00" && $month != "00") OR ($day == "00" && $year != "0000")){
				    $dateErrMsg = "Please enter a valid Day between 1-31";
				    return 1;
			    }
				
				if($month ==4 || $month == 6 || $month == 9 || $month == 11){
					if($day>30){
						$dateErrMsg = "There are only 30 days in the month that you entered";
						return 1;
					}
				}
			 }
				
			 // check if it is a valide year
			 $result=ereg("^[0-9]+$",$year, $reg);
             if (!($result)){
			        $dateErrMsg = "Please enter a valid Year";
					return 1;
			 }else{
                 if(($year != "0000") && (($year < 1900) OR ($year>2200))){
				     $dateErrMsg = "Please enter a valid Year between 1900-2200";
					 return 1;
				 }elseif(($year == "0000" && $day != "00") OR ($year == "0000" && $month != "00")){
					  $dateErrMsg = "Please enter a valid Year between 1900-2200";
					  return 1;
				}
				// check Feb days
				if($month ==2){
					// check if it is a leap year
					if($year%400 ==0){
						$maxDays = 29;
					}elseif($year%4 ==0 && $year%100 != 0){
						//leap year have 29 days
						$maxDays = 29;
					}else{
						$maxDays = 28;
					}
					if($day>$maxDays){
						$dateErrMsg = "February only has 28 days";
						return 1;
					}
				}
			}
        } 
    }
	return 0;
}

//FUNCTION: DATE FORMAT TO MM/DD/YYYY
function formateToDate($inputDate) {
	 //formate the month if it is not in mm formate
	list($month, $day, $year) = split('[/]', $inputDate);
	if(strlen($month) == 1){
		$month = "0".$month;
	}elseif (strlen($day) == 1){
		 $day = "0".$day;
	}
	$date = $month."/".$day."/".$year;
	return $date;
}

//FUNCTION: DATE COMPARATION 
function compareDates($date1, $date2) {
	 //formate the month an day if it is not in mm and DD formate
	$convertDate1 = formateToDate($date1);
	$convertDate2 = formateToDate($date2);
	list($month1, $day1, $year1) = split('[/]', $convertDate1);
	list($month2, $day2, $year2) = split('[/]', $convertDate2);
	$formDate1 = mktime(0, 0, 0, $month1, $day1, $year1);
	$formDate2 = mktime(0, 0, 0, $month2, $day2, $year2);
	if ($formDate1 > $formDate2){
		return 1;
	}else{
		return 0;
	}
	
}

//FUNCTION: TIME VALIDATION
function checkValidTime($inputTime) {
    GLOBAL $timeErrMsg;
    if((strlen($inputTime)<7)OR(strlen($inputTime)>8)){
            $timeErrMsg = "<b>Please enter the time in 'hh:mm am/pm' format</b>";
			return 1;
	}else{
	    //The entered value is checked for proper time format
        if(substr_count($inputTime,":")<>1){
			$timeErrMsg = "<b>Please enter the time in 'hh:mm am/pm' format</b>";
	    	return 1;
		}elseif ($inputTime == "00:00"){
			$dateErrMsg = "<b>Please enter a valid time in 'hh:mm am/pm' or 'hh:mm:ss' format</b>";
			return 1;
		}else{
		    list($hour, $string) = split('[:]', $inputTime);
			// check if it is a valide hour
			$result=ereg("^[0-9]+$",$hour, $reg);
            if (!($result)){
		        $timeErrMsg = "<b>Please enter a valid Hour</b>";
				return 1;
			}else{
                if(($hour < 0) OR ($hour > 12)){
				    $timeErrMsg = "<b>Please enter a valid Hour between 1-12</b>";
					return 1;
				}
             }
			 $minute = substr($string, 0, 2);
			//check if it is a valide minute
		    $result=ereg("^[0-9]+$",$minute, $reg);
            if (!($result)){
			    $timeErrMsg = "<b>Please enter a valid Minute</b>";
				return 1;
			}else{
                if(($minute < 0) OR ($minute > 60)){
				    $timeErrMsg = "<b>Please enter a valid Minute between 1-60</b>";
					return 1;
				}
			}
			
			//check the last two letters 
			$indct = substr($string, 3, 2);
			if (strtolower($indct) != "am" && strtolower($indct) != "pm"){
				$timeErrMsg = "<b>Please enter am or pm to indicate it is for morning time or afternoon time!</b>";
				return 1;
			}
        } 
    }
	return 0;
}

//FUNCTION: EMAIL VALIDATION
function checkValidEmail($inputEmail) {
    GLOBAL $emailErrMsg;
	$emailErrMsg="";
   if(strlen($inputEmail)>40 ){
		$emailErrMsg = "<b>The email can only contain 40 charactors</b>";
		return 1;
	}else{
		if(!preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $inputEmail)){
			$emailErrMsg = "<b>Please enter a valid email</b>";
			return 1;
		}
	}
	return 0;
}

//FUNCTION: ZIP CODE VALIDATION
function checkValidZip($inputZip) {
    GLOBAL $zipErrMsg;
	$zipErrMsg="";
    if((strlen($inputZip) != 5) && $inputZip != 0){
        $zipErrMsg = "<b>Please enter a valid zip code with 5 digits</b>";
		return 1;
	}else{
		$result=ereg("^[0-9]+$",$inputZip, $reg);
        if (!($result)){
	        $zipErrMsg = "<b>Please enter a valid zip code with only digits</b>";
			return 1;
		}
	}
	return 0;
}

//FUNCTION: PHONE VALIDATION
function checkValidPhone($inputPhone) {
    GLOBAL $phoneErrMsg;
	$phoneErrMsg ="";
	// first to check if the total number of string is 12
    if (strlen($inputPhone) != 12) {
        $phoneErrMsg = "<b>Please enter a valid phone number with area code in the following format 'xxx-xxx-xxxx'</b>";
		return 1;
	}else{
		// then check there are two "-" in the phone number
		if ((substr_count($inputPhone,"-"))<>2){
			$phoneErrMsg = "<b>Please enter a valid phone number with '-' in the following format 'xxx-xxx-xxxx'</b>";
			return 1;
		}else{
			// last, check if all digits
			list($area, $digit1, $digit2) = split('[-]', $inputPhone);
			$result=ereg("^[0-9]+$",$area, $reg);
	        if (!($result)){
		        $phoneErrMsg = "<b>Please enter a valid area code with only digits</b>";
				return 1;
			}
			
			$result=ereg("^[0-9]+$",$digit1, $reg);
	        if (!($result)){
		        $phoneErrMsg = "<b>Please enter a valid phone with digits</b>";
				return 1;
			}
			
			$result=ereg("^[0-9]+$",$digit2, $reg);
	        if (!($result)){
		        $phoneErrMsg = "<b>Please enter a valid phone number with digits</b>";
				return 1;
			}
		}
	}
	return 0;
}

//FUNCTION: DATE FORMAT
function formatDate($dateParam, $flg){
	if($dateParam){
	    if ($flg){
		    list($month, $day, $year) = split('[/]', $dateParam);
	        $date = $year . "-" . $month . "-" . $day;
		}else{
			if($dateParam != "0000-00-00"){
		        list($year, $month, $day) = split('[-]', $dateParam);
		        $date = $month . "/" . $day . "/" . $year;
			}
		}
	}
	return $date;
}

//FUNCTION: TIME FORMAT
function formatTime($timeParam, $flg){
    if ($flg){
	    list($hour, $string) = split('[:]', $timeParam);
		$min = substr($string, 0, 2);
		$indct = substr($string, 3, 2);
		//echo "The hur is ".$hour ."the idct is " .strtolower($indct)."<br>";
		if (strtolower ($indct) == "pm" && $hour != "12"){
			$time = $hour + 12 . ":" . $min;
		}elseif($hour == "12" && strtolower ($indct) == "am"){
			$time = "00:".$min;
		}else{
			$time = $hour .":".$min;
		}
	}else{
        list($hour, $min, $second) = split('[:]', $timeParam);
		if ($hour > 12){
        	$hour = $hour - 12;
			$time = $hour . ":" .$min . " pm";
		}elseif ($hour == "12"){
			$time = $hour . ":" .$min . " pm";
		}elseif($hour == "00"){
			$time = "12:" .$min . " am";
		}else{
			$time = $hour . ":" .$min . " am";
		}
	}
	return $time;
}

//FUNCTION: CHECK IF MRN IS VALID
function checkMRN($MRN) {	  
	GLOBAL $errMRNMsg;
	$errMRNMsg = "";
	
	if(strlen($MRN)>12){
	    $errMRNMsg = "<b>The MRN should be less then 12 digits</b>";
		return 1;
	}else{
		$result=ereg("^[0-9]+$",$MRN, $reg);
		if (!($result)){
	        $errMRNMsg= "<b>Please enter a valid MRN with all digits</b>";
			return 1;
		}
	}
}

//FUNCTION: CHECK IF MRN ALREADY IN THE DB
function getMRN($MRN) {	  
	GLOBAL $errMRNMsg;
	$errMRNMsg = "";
	
	//select MRN from the part_info table
	// VP changed this to get a count
	$sql1="SELECT count(MRN) as total FROM part_info where MRN = '".$MRN."'";
	$results=runQuery($sql1);
	if ($results["status"]<=0){
		$errMRNMsg =  "<font color=\"#FF0000\">Can not select record from part_info table!! ".mysql_error()." </font><br>\n";
		return 1;
	}else{
		// changed if statement FROM: if($results["numRows"] >0){ TO:
		if ($results["returnedRows"][0]["total"] > 0) {
			$errMRNMsg =  "<font color=\"#FF0000\">This MRN is already in the database.</font>";
			return 1;
		}else{
			return 0;
		}
	}
}

//FUNCTION: CHECK IF EMAIL ALREADY IN THE DB
function getEmail($partID, $ptEmail) {	  
	GLOBAL $errDupMsg;
	$errDupMsg = "";
	if(!$partID){
	//select email from the part_info table
		$sql1="SELECT ptEmail FROM part_info where ptEmail = '".$ptEmail."'";
	}else{
		$sql1="SELECT ptEmail FROM part_info where ptEmail = '".$ptEmail."' AND partID != ".$partID."";
	}
	$results=runQuery($sql1);
	if ($results["status"]<=0){
		$errDupMsg =  "<font color=\"#FF0000\">Can not select email record from part_info table!! ".mysql_error()." </font><br>\n";
		return 1;
	}else{
		if($results["numRows"] >0){
			$errDupMsg =  "<font color=\"#FF0000\">This email already in the database </font>\n";
			return 1;
		}else{
			return 0;
		}
		
	}
}

//FUNCTION: INSERT RECORD TO THE PART_INFO TABLE - USED IN ADD_PART PAGE
function insertPtInfo($MRN, $fName, $lName, $gender, $dob, $ptAddSt1, $ptAddSt2,
                 $ptCity, $ptState, $ptZip, $homePhone, $workPhone, $cellPhone, $otherPhone, 
				 $email, $notes, $date) {
	// Vikki added code to make contact info uc words
	$fName = makeStrUc($fName);
	$lName = makeStrUc($lName);
	$fName = mysql_escape_string($fName);
	$lName = mysql_escape_string($lName);
	$ptAddSt1 = makeStrUc($ptAddSt1);
	$ptAddSt2 = makeStrUc($ptAddSt2);
	$ptCity = makeStrUc($ptCity);
	$notes = mysql_escape_string($notes);
	GLOBAL $errIstMsg;
	$errIstMsg = "";
	$sql1 = "";
	
	$sql = "DELETE FROM part_info WHERE MRN = '".$MRN."' ";
	$results = runQuery($sql);
	//insert into the part_info table
	$sql1="INSERT INTO part_info 
	      	(MRN, ptFName, ptLName, gender, dob, ptAddress1, ptAddress2, ptCity, ptState, ptZip, 
           	ptHPhone, ptWPhone, ptCPhone, ptOPhone, ptEmail, notes, insertDate)
		  VALUES
		 ('".$MRN."', '".$fName."', '".$lName."', '".$gender."',  '".$dob."', 
		   '".$ptAddSt1."', '".$ptAddSt2."', '".$ptCity."', '".$ptState."', '".$ptZip."',
		   '".$homePhone."', '".$workPhone."', '".$cellPhone."', '".$otherPhone."', '".$email."',
		    '".$notes."',  curdate())";
	$results=runQuery($sql1);
	//echo $sql1."<br>";
	if ($results["status"]<=0){
		$errIstMsg =  "<font color=\"#FF0000\">Can not insert record into part_info table!! ".mysql_error()." </font><br>\n";
		return 1;
	}
	return 0;
}

//FUNCTION: GET PARTICIPANT ID
function getPartID($MRN) {	  
	GLOBAL $errIDMsg;
	$errIDMsg = "";
	
	//select partID from the part_info table
	$sql1="SELECT partID FROM part_info where MRN = '".$MRN."'";
	$results=runQuery($sql1);
	if ($results["status"]< 0){
		$errIDMsg =  "<font color=\"#FF0000\">Can not select record from part_info table!! ".mysql_error()." </font><br>\n";
		return 0;
	}else{
		if($results["numRows"] >0){
			$partID = $results["returnedRows"][0]["partID"];
		}
	}
	
	return $partID;
}

//FUNCTION: GET LIST OF ALL PROVIDERS
function getProvider() {	  
	GLOBAL $errPRMsg;
	$errPRMsg = "";
	
	//select provider info from provider table
	$sql="SELECT * FROM provider";
	$results=runQuery($sql);
	if ($results["status"]<=0){
		$errPRMsg =  "<font color=\"#FF0000\">Can not select record from provider table!! ".mysql_error()." </font><br>\n";
		return 1;
	}else{
		if($results["numRows"] >0){
			for ($row=0; $row<$results["numRows"]; $row++) {
				$temp = trim(strtoupper($results["returnedRows"][$row]["provID"]));
				$firstN = trim(strtoupper($results["returnedRows"][$row]["provFName"]));
				$lastN = trim(strtoupper($results["returnedRows"][$row]["provLName"]));
				$provdArray[$temp] = array($firstN, $lastN);
			}
		}
	}
	
	return $provdArray;
}

//FUNCTION: INSERT RECORD TO THE APPT TABLE
function insertApptInfo($MRN, $apptDate, $apptTime, $apptType, $apptWDay, 
				 $provdID, $pcpID, $hvmaNotes) {
					  
	GLOBAL $errIstMsg;
	$errIstMsg = "";
	// VP updated function, check for existing MRN with new function getCountBySQL 
	$sql = "SELECT COUNT(*) as total FROM part_info WHERE MRN = '$str'";
	$result = getCountBySQL($sql);
	if ($result > 0) {
	// record with the MRN already exists 
		$errIstMsg =  "<font color=\"#FF0000\">This MRN already in the appt table.</font><br>\n";
		return 1;
	} else { 
		$sql1 = "DELETE FROM APPT WHERE MRN = '".$MRN."'";
		$results=runQuery($sql1);
		// no such record, continue with insert
		$sql2="INSERT INTO appt
	      	(MRN, apptDate, apptTime, apptType, apptWDay, provdID, pcpID, hvmaNotes, insertDate)
			  VALUES
			 ('".$MRN."', '".$apptDate."', '".$apptTime."', '".$apptType."',  '".$apptWDay."', 
			  '".$provdID."', '".$pcpID."', '".$hvmaNotes."',  curdate())";
		$results=runQuery($sql2);
		// VP changed the condition
		if ($results['status'] == -1){
			$errIstMsg =  "<font color=\"#FF0000\">Can not insert record into appt table!! ".mysql_error()." </font><br>\n";
			return 1;
		}
		return 0;
	}
}

//FUNCTION: INSERT RECORD TO THE ALT_CONTACT TABLE - USED IN EDIT CONTACT PAGE
function insertAltInfo($partID, $conFName, $conLName, $conAddress, $conCity, 
				 $conState, $conZip, $conPhone, $conRelation) {
					  
	GLOBAL $errIstMsg;
	$errIstMsg = "";
	$conFName = makeStrUc($conFName);
	$conLName = makeStrUc($conLName);
	$conAddress = makeStrUc($conAddress);
	$conCity = makeStrUc($conCity);
	//insert into the alt contact table
	$sql="INSERT INTO alt_contact
	      	(partID, conFName, conLName, conAddress, conCity, conState, conZip, conPhone, conRelation)
		  VALUES
		 (".$partID.", '".$conFName."', '".$conLName."', '".$conAddress."',  '".$conCity."', 
		   '".$conState."', '".$conZip."', '".$conPhone."', '".$conRelation."')";
	$results=runQuery($sql);
	if ($results["status"]== -1){
		$errIstMsg =  "<font color=\"#FF0000\">Can not insert record into appt table!! ".mysql_error()." </font><br>\n";
		return 1;
	}
	return 0;
}

//FUNCTION: UPDATE RECORD IN THE ALT_CONTACT TABLE
function updateAltInfo($partID, $conFName, $conLName, $conAddress, $conCity, 
				 $conState, $conZip, $conPhone, $conRelation) {
					  
	GLOBAL $errIstMsg;
	$errIstMsg = "";
	$conFName = makeStrUc($conFName);
	$conLName = makeStrUc($conLName);
	$conAddress = makeStrUc($conAddress);
	$conCity = makeStrUc($conCity);
	
	$sql="UPDATE alt_contact SET 
	      	conFName = '".$conFName."', conLName = '".$conLName."', conAddress = '".$conAddress."', 
			conCity = '".$conCity."', conState = '".$conState."', conZip = '".$conZip."', conPhone = '".$conPhone."', 
		    conRelation = '".$conRelation."'
		  WHERE partID = ".$partID."";
	$results=runQuery($sql);
	if ($results["status"]<=0){
		$errIstMsg =  "<font color=\"#FF0000\">Can not insert record into appt table!! ".mysql_error()." </font><br>\n";
		return 1;
	}
	return 0;
}

//FUNCTION: GET PARTICIPANT LIST FROM PART_INFO TABLE - USED IN SEARCH PARTICIPANT PAGE
function getPtList($partID, $ptLName, $ptFName, $user, $flg){
	// Vikki removed criteria ptStatus != 'I' on all SQL statements - so everyone can search/edit inactive records 
	GLOBAL $rcdErr;
	$ptArray = array();
	
	$ptLName = mysql_real_escape_string($ptLName);
	$ptFName = mysql_real_escape_string($ptFName);
	if($user == "ra" || $user == "admin"){
		if($flg ==2){
			// 4/7/09 after talking to Molly, we tentivelly decide to eliminated pts that has withdrew date
			// during the search to aviod problem with inactive pt show up
			$sql = "SELECT pt.partID, pt.ptFName, pt.ptLName FROM part_info pt LEFT OUTER JOIN enrollment en 
			on pt.partID = en.partID WHERE pt.partID = '$partID' AND (en.dateWithdrew is NULL OR en.dateWithdrew = '0000-00-00')";
			//$sql="SELECT partID, ptFName, ptLName FROM part_info WHERE partID = '$partID'";
		}elseif($flg ==1){
			// 4/7/09 after talking to Molly, we tentivelly decide to eliminated pts that has withdrew date
			// during the search to aviod problem with inactive pt show up
			//$sql="SELECT partID, ptFName, ptLName FROM part_info WHERE ptLName = '$ptLName'";
			$sql = "SELECT pt.partID, pt.ptFName, pt.ptLName FROM part_info pt LEFT OUTER JOIN enrollment en 
			on pt.partID = en.partID WHERE ptLName = '$ptLName' AND (en.dateWithdrew is NULL OR en.dateWithdrew = '0000-00-00')";
		}elseif($flg ==0){
			$sql="SELECT pt.partID, ptFName, ptLName FROM part_info pt LEFT OUTER JOIN enrollment en 
			on pt.partID = en.partID WHERE ptFName = '$ptFName' AND (en.dateWithdrew is NULL OR en.dateWithdrew = '0000-00-00')";
		}else{
			$sql="SELECT pt.partID, ptFName, ptLName FROM part_info pt LEFT OUTER JOIN enrollment en 
			on pt.partID = en.partID WHERE ptFName = '$ptFName' AND ptLName = '$ptLName' AND (en.dateWithdrew is NULL OR en.dateWithdrew = '0000-00-00')";
		}
	}else{	
		if($flg ==2){
			$sql="SELECT part_info.partID, part_info.ptFName, part_info.ptLName FROM part_info, enrollment 
			WHERE part_info.partID = enrollment.partID AND part_info.partID = '".$partID."' AND (dateWithdrew is NULL OR dateWithdrew = '0000-00-00')
			AND heID = ".$user."";
		}elseif($flg ==1){
			$sql="SELECT part_info.partID, part_info.ptFName, part_info.ptLName FROM part_info, enrollment 
			WHERE part_info.partID = enrollment.partID AND ptLName = '".$ptLName."' AND (dateWithdrew is NULL OR dateWithdrew = '0000-00-00')
			AND heID = ".$user."";
		}elseif($flg ==0){
			$sql="SELECT part_info.partID, part_info.ptFName, part_info.ptLName FROM part_info, enrollment 
			WHERE part_info.partID = enrollment.partID AND ptFName = '".$ptFName."' AND (dateWithdrew is NULL OR dateWithdrew = '0000-00-00')
			AND heID = ".$user."";
		}else{
			$sql="SELECT part_info.partID, part_info.ptFName, part_info.ptLName FROM part_info, enrollment 
			WHERE part_info.partID = enrollment.partID AND ptFName = '".$ptFName."' AND ptLName = '".$ptLName."' 
			AND (dateWithdrew is NULL OR dateWithdrew = '0000-00-00') AND heID = ".$user;
		}
	}
	//echo $sql;
	$results=runQuery($sql);
	if ($results["status"]>0) {
		if ($results["numRows"]>0) {
			if($results["numRows"]>1){
				// if find multiple first name
				if($flg == 1){
					//echo "multirows for fname";
					//exit();
					return 2;
				// if find multiple last name
				}elseif($flg == 0){
					//echo "multirows for lname";
					//exit();
					return 4;
					// if multirows for first and last name
				}elseif($flg == 3){
					return 5;
				}
			}else{
				//echo "1 record";
				//exit();
		   		return 0;
			}
		}else{
			//echo "no record";
			//exit();
			if ($rcdErr == ""){$rcdErr = "no record has been returned\n";}
		    return 1;
		}
	}else{
		if ($rcdErr == ""){$rcdErr = "There is a problem running the query: ".mysql_error()."!!!\n";}
		return 3;
	}
}

//FUNCTION: GET PARTICIPANT WITH THE SAME EMAIL PART_INFO TABLE
function getEmailPt($ptEmail, $heID, $detFlg){
	GLOBAL $rcdErr;
	$ptArray = array();
	if($heID){
		$sql="SELECT pt.partID, ptFName, ptLName FROM part_info pt LEFT OUTER JOIN enrollment en 
				on pt.partID = en.partID WHERE pt.ptEmail = '".$ptEmail."' AND (dateWithdrew is NULL OR dateWithdrew = '0000-00-00')
				AND heID = ".$heID."";
	}else{
		$sql="SELECT pt.partID, ptFName, ptLName FROM part_info pt LEFT OUTER JOIN enrollment en 
				on pt.partID = en.partID WHERE pt.ptEmail = '".$ptEmail."' AND (dateWithdrew is NULL OR dateWithdrew = '0000-00-00')";
	}
	$results=runQuery($sql);
	if ($results["status"]>0) {
		if ($results["numRows"]>0) {
			if($detFlg) return $results["returnedRows"][0]["partID"];
			else return 0;
		}else{
			//echo "no record";
			//exit();
			if ($rcdErr == ""){$rcdErr = "no record has been returned\n";}
		    return 1;
		}
	}else{
		if ($rcdErr == ""){$rcdErr = "There is a problem running the query: ".mysql_error()."!!!\n";}
		return 3;
	}
}

//FUNCTION: GET PARTICIPANT WITH THE SAME LAST NAME FROM APRT_INFO TABLE 
function getPtLname($lName, $heID){
	GLOBAL $rcdErr;
	$ptArray = array();
	$lName = mysql_real_escape_string($lName);
	if($heID){
		$sql="SELECT pt.partID, ptFName, ptLName FROM part_info pt LEFT OUTER JOIN enrollment en 
				on pt.partID = en.partID WHERE pt.ptLName = '".$lName."' AND (dateWithdrew is NULL OR dateWithdrew = '0000-00-00')
				AND heID = ".$heID."";
	}else{
		$sql="SELECT pt.partID, ptFName, ptLName FROM part_info pt LEFT OUTER JOIN enrollment en 
				on pt.partID = en.partID WHERE pt.ptLName = '".$lName."' AND (dateWithdrew is NULL OR dateWithdrew = '0000-00-00')";
	}
	$results=runQuery($sql);
    if ($results["status"]>0) {
		if ($results["numRows"]>0) {
			for ($row=0; $row<$results["numRows"]; $row++) {
				$temp = $results["returnedRows"][$row]["partID"];
				$ptArray[$temp] = array($results["returnedRows"][$row]["ptFName"], $results["returnedRows"][$row]["ptLName"]);
			}
		}
	}
	return $ptArray;
}

//FUNCTION: GET PARTICIPANT WITH THE SAME FIRST NAME FROM APRT_INFO TABLE - USED IN PART_LIST PAGE 
function getPtFname($fName, $heID){
	GLOBAL $rcdErr;
	$ptArray = array();
	$fName = mysql_real_escape_string($fName);
	if($heID){
		$sql="SELECT pt.partID, ptFName, ptLName FROM part_info pt LEFT OUTER JOIN enrollment en 
				on pt.partID = en.partID WHERE ptFName = '".$fName."' AND (dateWithdrew is NULL OR dateWithdrew = '0000-00-00')
				AND heID = ".$heID."";
	}else{
		$sql="SELECT pt.partID, ptFName, ptLName FROM part_info pt LEFT OUTER JOIN enrollment en 
				on pt.partID = en.partID WHERE ptFName = '".$fName."' AND (dateWithdrew is NULL OR dateWithdrew = '0000-00-00')";
	}
	$results=runQuery($sql);
    if ($results["status"]>0) {
		if ($results["numRows"]>0) {
			for ($row=0; $row<$results["numRows"]; $row++) {
				$temp = $results["returnedRows"][$row]["partID"];
				$ptArray[$temp] = array($results["returnedRows"][$row]["ptFName"], $results["returnedRows"][$row]["ptLName"]);
			}
		}
	}
			
	return $ptArray;
}

//FUNCTION: GET PARTICIPANT WITH THE SAME FIRST NAME FROM APRT_INFO TABLE - USED IN PART_LIST PAGE 
function getPtBname($fName, $lName, $heID){
	GLOBAL $rcdErr;
	$ptArray = array();
	if(!$heID){
		$sql="SELECT pt.partID, ptFName, ptLName FROM part_info pt LEFT OUTER JOIN enrollment en 
				on pt.partID = en.partID WHERE ptFName = '".$fName."' AND ptLName = '".$lName."'
				AND (dateWithdrew is NULL OR dateWithdrew = '0000-00-00')";
	}else{
		$sql="SELECT pt.partID, ptFName, ptLName FROM part_info pt LEFT OUTER JOIN enrollment en 
				on pt.partID = en.partID WHERE ptFName = '".$fName."' AND ptLName = '".$lName."'
				AND (dateWithdrew is NULL OR dateWithdrew = '0000-00-00') AND heID = ".$heID."";
	}
	$results=runQuery($sql);
    if ($results["status"]>0) {
		if ($results["numRows"]>0) {
			for ($row=0; $row<$results["numRows"]; $row++) {
				$temp = $results["returnedRows"][$row]["partID"];
				$ptArray[$temp] = array($results["returnedRows"][$row]["ptFName"], $results["returnedRows"][$row]["ptLName"]);
			}
		}
	}
	return $ptArray;
	
}

//FUNCTION: GET PARTICIPANT'S DEMO INFO FROM APRT_INFO TABLE - USED IN EDIT PARTICIPANT PAGE
function getPartInfo($partID){
	GLOBAL $rcdErr;
	$ptInfoArray = array();
	$sql="SELECT * FROM part_info WHERE partID = '".$partID."'";
	$results=runQuery($sql);
    if ($results["status"]>0) {
		if ($results["numRows"]>0) {
			$ptInfoArray[0] = $results["returnedRows"][0]["partID"];
			$ptInfoArray[1] = $results["returnedRows"][0]["MRN"];
			$ptInfoArray[2] = $results["returnedRows"][0]["ptFName"];
			$ptInfoArray[3] = str_replace('\\', '', $results["returnedRows"][0]["ptLName"]); // remove escapte string 
			if($results["returnedRows"][0]["gender"] == "F"){
				$ptInfoArray[4] = "Female";
			}else{
				$ptInfoArray[4] = "Male";
			}
			$dob = formatDate($results["returnedRows"][0]["dob"], 0);
			$ptInfoArray[5] = $dob;
			$ptInfoArray[6] = $results["returnedRows"][0]["ptAddress1"];
			$ptInfoArray[7] = $results["returnedRows"][0]["ptAddress2"];
			$ptInfoArray[8] = $results["returnedRows"][0]["ptCity"];
			$ptInfoArray[9] = $results["returnedRows"][0]["ptState"];
			$ptInfoArray[10] = $results["returnedRows"][0]["ptZip"];
			$ptInfoArray[11] = $results["returnedRows"][0]["ptHPhone"];
			$ptInfoArray[12] = $results["returnedRows"][0]["ptWPhone"];
			$ptInfoArray[13] = $results["returnedRows"][0]["ptCPhone"];
			$ptInfoArray[14] = $results["returnedRows"][0]["ptOPhone"];
			$ptInfoArray[15] = $results["returnedRows"][0]["ptPPhone"];
			$ptInfoArray[16] = $results["returnedRows"][0]["ptEmail"];
			$ptInfoArray[17] = str_replace('\\', '', $results["returnedRows"][0]["notes"]); // remove escapte string 
			$ptInfoArray[18] = $results["returnedRows"][0]["ptStatus"];
			$ptInfoArray[19] = $results["returnedRows"][0]["ptRPhone"];
		}else{
			$rcdErr = "no record has been returned for this participant\n";
		}
	}
	//print_r( $ptInfoArray);
	return $ptInfoArray;
}

//FUNCTION: GET IX MODALITY FROM ENROLLMENT TABLE - USED IN EDIT_PART PAGE 
function getIXmodality($partID){
	GLOBAL $rcdErr;
	$rcdErr = "";
	$sql="SELECT ixModality FROM enrollment WHERE partID = '".$partID."'";
	$results=runQuery($sql);
    if ($results["status"]>0) {
		if ($results["numRows"]>0) {
			$ixModality = $results["returnedRows"][0]["ixModality"];
			return $ixModality;
		}else{
			return 0;
		}
	}else{
		$rcdErr = "no record has been returned\n";
		return $rcdErr;
	}
	
}

//FUNCTION: UPDATE RECORD IN THE PART_INFO TABLE - USED IN EDIT_PART PAGE
function updatePtInfo($partID, $MRN, $fName, $lName, $gender, $dob, $ptAddSt1, $ptAddSt2,
                 $ptCity, $ptState, $ptZip, $homePhone, $workPhone, $cellPhone, $otherPhone, 
				 $ptPPhone, $email, $notes) {
	// Vikki added code to make contact info uc words
	$fName = makeStrUc($fName);
	$fName = mysql_escape_string($fName);
	$lName = makeStrUc($lName);
	$lName = mysql_escape_string($lName);
	$ptAddSt1 = makeStrUc($ptAddSt1);
	$ptAddSt2 = makeStrUc($ptAddSt2);
	$ptCity = makeStrUc($ptCity);
	$notes = mysql_escape_string($notes);
	GLOBAL $errIstMsg;
	$errIstMsg = "";
	
	//insert into the part_info table
	$sql="UPDATE part_info SET
	      	MRN = ".$MRN.",  ptFName = '".$fName."', ptLName = '".$lName."', gender = '".$gender."', 
			dob = '".$dob."', ptAddress1 = '".$ptAddSt1."', ptAddress2 = '".$ptAddSt2."', ptCity = '".$ptCity."', 
			ptState = '".$ptState."', ptZip = '".$ptZip."', ptHPhone = '".$homePhone."', ptWPhone = '".$workPhone."', 
			ptCPhone = '".$cellPhone."', ptOPhone = '".$otherPhone."', ptPPhone = '".$ptPPhone."', ptEmail = '".$email."', 
			notes = '".$notes."'
		WHERE partID = ".$partID."";
	$results=runQuery($sql);
	if ($results["status"]<=0){
		$errIstMsg =  "<font color=\"#FF0000\">Can not insert record into part_info table!! ".mysql_error()." </font><br>\n";
		return 1;
	}
	return 0;
}

//FUNCTION: GET PARTICIPANT'S APPT INFO FROM APPT_INFO TABLE - USED IN EDIT APPT PAGE
function getApptInfo($partID){
	$partID = trim($partID);
	GLOBAL $rcdErr;
	$apptInfoArray = array();
	$sql="SELECT appt.MRN, apptDate, apptTime, apptType, apptWDay, provdID, pcpID, hvmaNotes
	        FROM part_info, appt
			WHERE appt.MRN = part_info.MRN AND part_info.partID = '".$partID."' ";
	$results=runQuery($sql);
    if ($results["status"]>0) {
		if ($results["numRows"]>0) {
			$apptInfoArray[0] = trim($results["returnedRows"][0]["MRN"]);
			$apptDate = formatDate($results["returnedRows"][0]["apptDate"], 0);
			$apptInfoArray[1] = trim($apptDate);
			$apptTime = formatTime($results["returnedRows"][0]["apptTime"], 0);
			$apptInfoArray[2] = trim($apptTime);
			$apptInfoArray[3] = trim($results["returnedRows"][0]["apptType"]);
			$apptInfoArray[4] = makeStrUc(trim($results["returnedRows"][0]["apptWDay"])); // convert week days to uc words 
			$apptInfoArray[5] = strtoupper(trim($results["returnedRows"][0]["provdID"])); // make sure provdID is caps 
			$apptInfoArray[6] = strtoupper(trim($results["returnedRows"][0]["pcpID"])); // make sure pcpID is caps 
			$apptInfoArray[7] = trim(str_replace('\\', '', $results["returnedRows"][0]["hvmaNotes"])); // remove escape string
		}else{
			$rcdErr = "no record has been returned for this participant\n";
		}
	}
	
	return $apptInfoArray;
}

//FUNCTION: GET PROVIDERS NAMES - USED IN EDIT_APPT PAGE
function getProvdName($provID) {	  
	GLOBAL $errPRMsg;
	$errPRMsg = "";
	
	//select provider info from provider table
	$sql="SELECT provFName, provLName FROM provider where provID = '".$provID."' ";
	$results=runQuery($sql);
	if ($results["status"]<=0){
		$errPRMsg =  "<font color=\"#FF0000\">Can not select record from provider table!! ".mysql_error()." </font><br>\n";
		return 1;
	}else{
		if($results["numRows"] >0){
			$provdName = $results["returnedRows"][0]["provFName"]." ".$results["returnedRows"][0]["provLName"];
			
		}
	}
	
	return $provdName;
}

//FUNCTION: UPDATE RECORDs IN THE APPT TABLE
function updateApptInfo($MRN, $apptDate, $apptTime, $apptType, $apptWDay, 
				 $provdID, $pcpID, $hvmaNotes) {
					  
	GLOBAL $errIstMsg;
	$errIstMsg = "";
	
	//update the appt table
	$sql="UPDATE appt SET
	      	apptDate = '".$apptDate."', apptTime = '".$apptTime."', apptType = '".$apptType."', 
			 apptWDay = '".$apptWDay."', provdID = '".$provdID."', pcpID = '".$pcpID."', hvmaNotes = '".$hvmaNotes."'
			 WHERE MRN = '".$MRN."'";
	$results=runQuery($sql);
	if ($results["status"]<=0){
		$errIstMsg =  "<font color=\"#FF0000\">Can not update record in the appt table!! ".mysql_error()." </font><br>\n";
		return 1;
	}
	return 0;
}

//FUNCTION: GET PARTICIPANT'S RECRUITMENT INFO FROM RECRUITMENT TABLE - USED IN EDIT RECRUITMENT PAGE
function getRecruitInfo($partID){
	GLOBAL $rcdErr;
	$recruitInfoArray = array();
	$sql="SELECT * FROM recruitment WHERE partID = '".$partID."' ";
	$results=runQuery($sql);
    if ($results["status"]>0) {
		if ($results["numRows"]>0) {
			$recruitInfoArray[0] = $results["returnedRows"][0]["partID"];
			$notifyDate = formatDate($results["returnedRows"][0]["datePCPnotify"], 0);
			$recruitInfoArray[1] = $notifyDate;
			$recruitInfoArray[2] = $results["returnedRows"][0]["pcpOptOut"];
			$lttrSend = formatDate($results["returnedRows"][0]["dateRecrutLttr"], 0);
			$recruitInfoArray[3] = $lttrSend;
			$lttrRecieved = formatDate($results["returnedRows"][0]["dateReceived"], 0);
			$recruitInfoArray[4] = $lttrRecieved;
			$recruitInfoArray[5] = $results["returnedRows"][0]["ptOptOut"];
			if(strtoupper($results["returnedRows"][0]["giveClipbd"]) == 'Y'){
				$recruitInfoArray[6] = "Yes";
			}elseif(strtoupper($results["returnedRows"][0]["giveClipbd"]) == 'N'){
				$recruitInfoArray[6] = "No";
			}
			$recruitInfoArray[7] = $results["returnedRows"][0]["blResult"];
			$recruitInfoArray[8] = $results["returnedRows"][0]["raID1"];
			$recruitInfoArray[9] = $results["returnedRows"][0]["raID2"];
		}else{
			$rcdErr = "no record has been returned for this participant\n";
		}
	}
	
	return $recruitInfoArray;
}

//FUNCTION: GET LIST OF BASELINE RESULT - USED IN EDIT RECRUITMENT PAGE
function getBlResult() {	  
	GLOBAL $errPRMsg;
	$errPRMsg = "";
	
	//select provider info from provider table
	$sql="SELECT * FROM bl_result";
	$results=runQuery($sql);
	if ($results["status"]<=0){
		$errPRMsg =  "<font color=\"#FF0000\">Can not select record from bl_result table!! ".mysql_error()." </font><br>\n";
		return 1;
	}else{
		if($results["numRows"] >0){
			for ($row=0; $row<$results["numRows"]; $row++) {
				$temp = $results["returnedRows"][$row]["blResultID"];
				$resultArray[$temp] = $results["returnedRows"][$row]["blResult"];
			}
		}
	}
	
	return $resultArray;
}

//FUNCTION: GET LIST OF RA NAMES - USED IN EDIT RECRUITMENT PAGE
function getRaNames($title) {	  
	GLOBAL $errPRMsg;
	$errPRMsg = "";
	
	//select provider info from provider table
	$sql="SELECT staffID, staffFName, staffLName FROM staff WHERE title = '".$title."'";
	$results=runQuery($sql);
	if ($results["status"]<=0){
		$errPRMsg =  "<font color=\"#FF0000\">Can not select record from staff table!! ".mysql_error()." </font><br>\n";
		return 1;
	}else{
		if($results["numRows"] >0){
			for ($row=0; $row<$results["numRows"]; $row++) {
				$temp = $results["returnedRows"][$row]["staffID"];
				$resultArray[$temp] = array($results["returnedRows"][$row]["staffFName"], $results["returnedRows"][$row]["staffLName"]);
			}
		}
	}
	 
	return $resultArray;
}

//FUNCTION: INSERT RECORD TO THE RECRUITMENT TABLE - USED IN EDIT RECRUITMENT PAGE
function insertRecuitInfo($partID, $datePCPnotify, $pcpOptOut, $dateRecrutLttr, $dateReceived, $ptOptOut, $giveClipbd, 
                  $blResult, $raID1, $raID2) {
					  
	GLOBAL $errIstRecuMsg;
	$errIstRecuMsg = "";
	
	//insert into the recruitment table
	$sql="INSERT INTO recruitment 
	      	(partID, datePCPnotify, pcpOptOut, dateRecrutLttr, dateReceived, ptOptOut, giveClipbd,  
           	blResult, raID1, raID2)
		  VALUES
		  (".$partID.", '".$datePCPnotify."', '".$pcpOptOut."', '".$dateRecrutLttr."',  '".$dateReceived."', 
		   '".$ptOptOut."', '".$giveClipbd."', '".$blResult."', '".$raID1."', '".$raID2."')";
	$results=runQuery($sql);
	if ($results["status"]== -1){
		$errIstRecuMsg =  "<font color=\"#FF0000\">Can not insert record into recruitment table!! ".mysql_error()." </font><br>\n";
		return 1;
	}
	
	return 0;
}

//FUNCTION: UPDATE RECORD IN THE RECRUITMENT TABLE - USED IN EDIT RECRUITMENT PAGE
function updateRecruitInfo($partID, $datePCPnotify, $pcpOptOut, $dateRecrutLttr, $dateReceived, $ptOptOut, $giveClipbd, 
                  $blResult, $raID1, $raID2) {
					  
	GLOBAL $errIstMsg;
	$errIstMsg = "";
	//insert into the recruitment table
	$sql="UPDATE recruitment SET
	      	datePCPnotify = '".$datePCPnotify."', pcpOptOut = '".$pcpOptOut."', dateRecrutLttr = '".$dateRecrutLttr."', 
			 dateReceived = '".$dateReceived."', ptOptOut = '".$ptOptOut."', giveClipbd = '".$giveClipbd."',  
           	blResult = '".$blResult."', raID1 = '".$raID1."', raID2 ='".$raID2."'
		  WHERE partID = ".$partID."";
	$results=runQuery($sql);
	if ($results["status"]<=0){
		$errIstMsg =  "<font color=\"#FF0000\">Can not update record in the recruitment table!! ".mysql_error()." </font><br>\n";
		return 1;
	}
	
	return 0;
}

//FUNCTION: UPDATE PT STATUS IN PART_INFO TABLE - USED IN EDIT RECRUITMENT PAGE
function updateSatus($partID, $status) {
					  
	GLOBAL $errIstMsg;
	$errIstMsg = "";
	//update part_info table
	$sql="UPDATE part_info SET ptStatus = '".$status."' WHERE partID = ".$partID."";
	$results=runQuery($sql);
	if ($results["status"]<=0){
		$errIstMsg =  "<font color=\"#FF0000\">Can not update pt status in the part_info table!! ".mysql_error()." </font><br>\n";
		return 1;
	}
	
	return 0;
}

//FUNCTION: GET RANDOM ARM - USED IN EDIT RECRUITMENT PAGE
function getRandom($partID) {	  
	GLOBAL $errRandMsg;
	$errRandMsg = "";
	
	//select random arm info from part_info, appt and provider table
	$sql="SELECT randArm FROM part_info, appt, provider
	      WHERE part_info.MRN = appt.MRN AND appt.provdID = provider.provID AND part_info.partID = ".$partID."";
	$results=runQuery($sql);
	
	if ($results["status"]<=0){
		$errRandMsg =  "<font color=\"#FF0000\">Can not select random info from the tables!! ".mysql_error()." </font>\n";
		return 0;
	}else{
		if($results["numRows"] >0){
			$randArm = $results["returnedRows"][0]["randArm"];
		}else{
			$errRandMsg =  "<font color=\"#FF0000\">No ramdomization arm is found </font>\n";
			return 0;
		}
	}
	
	return $randArm;
}

//FUNCTION: GET PARTICIPANT'S ENROLLMENT INFO FROM ENROLLMENT TABLE - USED IN EDIT ENROLLMENT PAGE
function getEnrollment($partID){
	GLOBAL $rcdErr;
	$enrollInfoArray = array();
	// Grab smoking fields out of the userInfo table to add to enrollment array. Although inefficient, this
	// double query will at least keep the code in edit_enrollment much tidier
	$sqlSmoking ="SELECT smoker, numSmoked FROM userInfo WHERE studyID = '".$partID."' ";
	$smokingResult = runQuery($sqlSmoking);
	$sql="SELECT * FROM enrollment WHERE partID = '".$partID."' ";
	$results=runQuery($sql);
    if ($results["status"]>0) {
		if ($results["numRows"]>0) {
			$enrollInfoArray[0] = $results["returnedRows"][0]["partID"];
			$enrollInfoArray[1] = $results["returnedRows"][0]["ixModality"];
			$dateIXchanged = formatDate($results["returnedRows"][0]["dateIXChange"], 0);
			$enrollInfoArray[2] = $dateIXchanged;
			$enrollInfoArray[3] = $results["returnedRows"][0]["ixID"];
			// smoking results from different db table
			$enrollInfoArray[4] = $results["returnedRows"][0]["webPwd"];
			$enrollInfoArray[5] = $results["returnedRows"][0]["remindRand"];
			$enrollInfoArray[6] = $results["returnedRows"][0]["reminModality"];
			$dateRemChanged = formatDate($results["returnedRows"][0]["dateRemChang"], 0);
			$enrollInfoArray[7] = $dateRemChanged;
			$enrollInfoArray[8] = $results["returnedRows"][0]["remindID"];
			$dateRemOpt = formatDate($results["returnedRows"][0]["dateRemOpt"],0);
			$enrollInfoArray[9] = $dateRemOpt;
			$enrollInfoArray[10] = $results["returnedRows"][0]["remindOptID"];
			$startDate = formatDate($results["returnedRows"][0]["startDate"], 0);
			$enrollInfoArray[11] = $startDate;
			$dateProvTFR = formatDate($results["returnedRows"][0]["dateProvdTFR"],0);
			$enrollInfoArray[12] = $dateProvTFR;
			$datePecket = formatDate($results["returnedRows"][0]["datePacket"], 0);
			$enrollInfoArray[13] = $datePecket;
			$enrollInfoArray[14] = $results["returnedRows"][0]["packetID"];
			$datePed= formatDate($results["returnedRows"][0]["datePed"], 0);
			$enrollInfoArray[15] = $datePed;
			$enrollInfoArray[16] = $results["returnedRows"][0]["pedID"];
			$dateWinraff= formatDate($results["returnedRows"][0]["dateWinraff"], 0);
			$enrollInfoArray[17] = $dateWinraff;
			$dateWithdrew= formatDate($results["returnedRows"][0]["dateWithdrew"], 0);
			$enrollInfoArray[18] = $dateWithdrew;
			$enrollInfoArray[19] = $results["returnedRows"][0]["withdID"];
			$enrollInfoArray[20] = $results["returnedRows"][0]["heID"];
			$enrollInfoArray[21] = $smokingResult["returnedRows"][0]["smoker"];
			$enrollInfoArray[22] = $smokingResult["returnedRows"][0]["numSmoked"];			
		}else{
			$rcdErr = "no record has been returned for this participant\n";
		}
	}
	
	return $enrollInfoArray;
}

//FUNCTION: GET PARTICIPANT'S ALT CONTACT INFO FROM ALT_CONTACT TABLE - USED IN EDIT CONTACT PAGE
function getConInfo($partID){
	GLOBAL $rcdErr;
	$conInfoArray = array();
	$sql="SELECT * FROM alt_contact WHERE partID = '".$partID."'";
	$results=runQuery($sql);
    if ($results["status"]>0) {
		if ($results["numRows"]>0) {
			$conInfoArray[0] = $results["returnedRows"][0]["conFName"];
			$conInfoArray[1] = trim(str_replace('\\', '', $results["returnedRows"][0]["conLName"]));
			$conInfoArray[2] = $results["returnedRows"][0]["conAddress"];
			$conInfoArray[3] = $results["returnedRows"][0]["conCity"];
			$conInfoArray[4] = $results["returnedRows"][0]["conState"];
			$conInfoArray[5] = $results["returnedRows"][0]["conZip"];
			$conInfoArray[6] = $results["returnedRows"][0]["conPhone"];
			$conInfoArray[7] = $results["returnedRows"][0]["conRelation"];
		}else{
			$rcdErr = "no record has been returned for this participant\n";
		}
	}
	return $conInfoArray;
}

//FUNCTION: GET LIST OF REASONS FOR IX CHANGE, REMINDER CHANGE, REMINDER OPT-OUT,
//PACKET SENT, PED MAILED AND WITHDREW - USED IN EDIT ENROLLMENT PAGE 1
function getReason($table, $ID, $reason) {	  
	GLOBAL $errRSNMsg;
	$errRSNMsg = "";
	
	$sql="SELECT * FROM ".$table."";
	$results=runQuery($sql);
	if ($results["status"]<=0){
		$errPRMsg =  "<font color=\"#FF0000\">Can not select record from bl_result table!! ".mysql_error()." </font><br>\n";
		return 1;
	}else{
		if($results["numRows"] >0){
			for ($row=0; $row<$results["numRows"]; $row++) {
				$temp = $results["returnedRows"][$row][$ID];
				$reasonArray[$temp] = $results["returnedRows"][$row][$reason];
			}
		}
	}
	return $reasonArray;
}

//FUNCTION: INSERT RECORD TO THE ENROLLMENT TABLE - USED IN EDIT ENROLLMENT PAGE 1
// VIKKI CHANGED THIS FUNCTION - to add StartDate upon first insertion
function insertEnrollInfo($partID, $ixModality, $dateIXChange, $reasonIX, $smoker, $numCigs, $webPwd, $remindRand,
			               $reminModality, $dateRemChang, $reasonRemind, $dateRemOpt, $reasonRemOpt, $startDate) {
					  
	GLOBAL $errIstMsg;
	$errIstMsg = "";
	
	//insert into the enrollment table
	if($dateRemOpt && $dateRemOpt != "0000-00-00"){
		$sql="INSERT INTO enrollment 
		      	(partID, ixModality, dateIXChange, ixID, webPwd, remindRand, 
	             reminModality, dateRemChang, remindID, dateRemOpt, remindOptID, startDate)
			  VALUES
			 (".$partID.", '".$ixModality."', '".$dateIXChange."', '".$reasonIX."',  '".$webPwd."', '".$remindRand."',
			    '".$reminModality."', '".$dateRemChang."', '".$reasonRemind."', '".$dateRemOpt."', '".$reasonRemOpt."', '$startDate')";
	}else{
		$sql="INSERT INTO enrollment 
		      	(partID, ixModality, dateIXChange, ixID, webPwd, remindRand, 
	             reminModality, dateRemChang, remindID, remindOptID, startDate)
			  VALUES
			 (".$partID.", '".$ixModality."', '".$dateIXChange."', '".$reasonIX."',  '".$webPwd."', '".$remindRand."',
			    '".$reminModality."', '".$dateRemChang."', '".$reasonRemind."', '".$reasonRemOpt."', '$startDate')";
	}
	$sql2 = "UPDATE userInfo SET smoker= '". $smoker . "', numSmoked = '" . $numCigs . "' WHERE studyID = ".$partID."";
	$results=runQuery($sql);
	$results2=runQuery($sql2);
	//echo $sql;
	if ($results["status"]== -1 || $results2["status"]== -1){
		$errIstMsg =  "<font color=\"#FF0000\">Can not insert record into enrollment table!! ".mysql_error()." </font><br>\n";
		return 1;
	}
	
	return 0;
}

//FUNCTION: UPDATE RECORD IN THE ENROLLMENT TABLE - USED IN EDIT ENROLLMENT PAGE 1
function updateEnrollInfo($partID, $ixModality, $dateIXChange, $reasonIX, $smoker, $numCigs, $webPwd, $remindRand,
			               $reminModality, $dateRemChang, $reasonRemind, $dateRemOpt, $reasonRemOpt) {
						  
	GLOBAL $errIstMsg;
	$errIstMsg = "";
	if($dateRemOpt && $dateRemOpt != "0000-00-00"){
		//update enrollment table
		$sql="UPDATE enrollment SET
		      	ixModality= '".$ixModality."', dateIXChange = '".$dateIXChange."', ixID = '".$reasonIX."', 
				 webPwd = '".$webPwd."', remindRand = '".$remindRand."', reminModality = '".$reminModality."',  
	           	dateRemChang = '".$dateRemChang."', remindID ='".$reasonRemind."', dateRemOpt ='".$dateRemOpt."', remindOptID = '".$reasonRemOpt."'
			  WHERE partID = ".$partID."";
	}else{
		//update enrollment table
		$sql="UPDATE enrollment SET
		      	ixModality= '".$ixModality."', dateIXChange = '".$dateIXChange."', ixID = '".$reasonIX."', 
				 webPwd = '".$webPwd."', remindRand = '".$remindRand."', reminModality = '".$reminModality."',  
	           	dateRemChang = '".$dateRemChang."', remindID ='".$reasonRemind."', remindOptID = '".$reasonRemOpt."'
			  WHERE partID = ".$partID."";
	}
	$sql2 = "UPDATE userInfo SET smoker= '". $smoker . "', numSmoked = '" . $numCigs . "' WHERE studyID = ".$partID."";
	$results=runQuery($sql);
	$results2=runQuery($sql2);
	//print_r($results2);
	if ($results["status"]<=0 || $results2["status"]<=0){
		$errIstMsg =  "<font color=\"#FF0000\">Can not update record in the enrollment table!! ".mysql_error()." </font><br>\n";
		return 1;
	}
	
	return 0;
}

//FUNCTION: UPDATE RECORD IN THE ENROLLMENT TABLE FOR PART 2 - USED IN EDIT ENROLLMENT PAGE 2
function updateEnrollInfo2($partID, $inputStDate, $tfrDate, $packetDate, $packetID,
			               $pedDate, $pedID, $rafDate, $withdDate, $withdID, $heID) {
						  
	GLOBAL $errIstMsg;
	$errIstMsg = "";
	//update enrollment table
	if($rafDate && $rafDate != "0000-00-00"){
		$sql="UPDATE enrollment SET
		      	startDate= '".$inputStDate."',  dateProvdTFR= '".$tfrDate."', datePacket = '".$packetDate."', 
				packetID = ".$packetID.", datePed = '".$pedDate."', pedID = ".$pedID.", dateWinraff = '".$rafDate."',  
	           	dateWithdrew = '".$withdDate."', withdID =".$withdID.", heID = ".$heID."
			  WHERE partID = ".$partID."";
	}else{
		$sql="UPDATE enrollment SET
		      	startDate= '".$inputStDate."',  dateProvdTFR= '".$tfrDate."', datePacket = '".$packetDate."', 
				packetID = ".$packetID.", datePed = '".$pedDate."', pedID = ".$pedID.", 
	           	dateWithdrew = '".$withdDate."', withdID =".$withdID.", heID = ".$heID."
			  WHERE partID = ".$partID."";
	}
	$results=runQuery($sql);
	if ($results["status"]<=0){
		$errIstMsg =  "<font color=\"#FF0000\">Can not update record in the recruitment table!! ".mysql_error()." </font><br>\n";
		return 1;
	}
	
	return 0;
}

//FUNCTION: UPDATE PREFERRED PHONE FOR REMINDER IN APRT_INFO TABLE - USED IN EDIT ENROLLMENT PAGE 1
function updateRphone($partID, $ptRPhone) {
						 
	GLOBAL $errIstMsg;
	$errIstMsg = "";
	//update part_info table
	$sql="UPDATE part_info SET ptRPhone = '".$ptRPhone."' WHERE partID = ".$partID."";
	$results=runQuery($sql);
	if ($results["status"]<=0){
		$errIstMsg =  "<font color=\"#FF0000\">Can not update record in the part_info table for reminder phone prefer!! ".mysql_error()." </font><br>\n";
		return 1;
	}
	
	return 0;
}

//FUNCTION: GET COACHING CALL PREFER FOR CALL 1 - USED IN EDIT ENROLLMENT PAGE 2
function getCallPrefer($partID, $callID){
	GLOBAL $rcdErr;
	$callPreferArray = array();
	$sql="SELECT * FROM call_prefer WHERE partID = '".$partID."' AND callID = ".$callID."";
	$results=runQuery($sql);
    if ($results["status"]>0) {
		if ($results["numRows"]>0) {
			$callPreferArray[0] = $results["returnedRows"][0]["preferTime"];
			$callPreferArray[1] = $results["returnedRows"][0]["preferPhone"];
			$callPreferArray[2] = str_replace('\\', '', $results["returnedRows"][0]["preferNotes"]);
		}else{
			$rcdErr = "no record has been returned for this participant\n";
		}
	}
	//print_r($callPreferArray);
	return $callPreferArray;
}

//FUNCTION: INSERT CALL 1 PREFER INTO CALL_PREFER TABLE - USED IN EDIT ENROLLMENT PAGE 2
function inserCallPrefer($partID, $callID, $preferTime, $preferPhone) {
						 
	GLOBAL $errIstMsg;
	$errIstMsg = "";
	
	$sql1 = "DELETE FROM call_prefer WHERE partID = ".$partID." AND callID = ".$callID." 
	             AND preferTime = '".$preferTime."' AND preferPhone = '".$preferPhone."'";
	$results=runQuery($sql1);			 
	
	$sql= "INSERT INTO call_prefer
	      	(partID, callID, preferTime, preferPhone, preferNotes)
		  VALUES
		 (".$partID.", ".$callID.", '".$preferTime."', '".$preferPhone."',  '".mysql_escape_string($preferNotes)."')";
	$results=runQuery($sql);
	if ($results["status"]== -1){
		$errIstMsg =  "<font color=\"#FF0000\">Can not add record to the call_prefer table!! ".mysql_error()." </font><br>\n";
		return 1;
	}
	
	return 0;
}

//FUNCTION: UPDATE CALL 1 PREFER FOR CALL 1 IN CALL_PREFER TABLE - USED IN EDIT ENROLLMENT PAGE 2
function updateCallPrefer($partID, $callID, $preferTime, $preferPhone) {
						 
	GLOBAL $errIstMsg;
	$errIstMsg = "";
	//update call_prefer table
	$sql="UPDATE call_prefer SET preferTime = '".$preferTime."', preferPhone = '".$preferPhone."'
	      WHERE partID = ".$partID." AND callID = ".$callID."";
	$results=runQuery($sql);
	if ($results["status"]<=0){
		$errIstMsg =  "<font color=\"#FF0000\">Can not update record in the call_prefer table!! ".mysql_error()." </font><br>\n";
		return 1;
	}
	
	return 0;
}

//FUNCTION: GET PCP REVIEW LIST
function getPCPreview() {	  
	GLOBAL $errRvwMsg;
	$errRvwMsg = "";
	$pcpRevArray = array();
	
	$sql="SELECT part_info.partID, provider.provFName, provider.provLName, appt.apptDate, appt.apptTime, 
		provider.dept, part_info.ptFName, part_info.ptLName, part_info.MRN 
		FROM part_info, provider, appt, recruitment
		WHERE part_info.MRN = appt.MRN AND appt.pcpID = provider.provID AND part_info.partID = recruitment.partID  
		AND provider.review = 'y' and recruitment.datePCPnotify = '0000-00-00' 
		AND appt.apptDate >= adddate(curdate(), 7) ORDER BY provider.provFName, provider.provLName , appt.apptDate,
		appt.apptTime";
	$results=runQuery($sql);
	if ($results["status"]<=0){
		$errRvwMsg =  "<font color=\"#FF0000\">Can not select record from provider table!! ".mysql_error()." </font><br>\n";
		return 1;
	}else{
		if($results["numRows"] >0){
			for ($row=0; $row<$results["numRows"]; $row++) {
				$pcpName = $results["returnedRows"][$row]["provFName"]. " ". $results["returnedRows"][$row]["provLName"];
				$ptName = $results["returnedRows"][$row]["ptFName"] . " " . $results["returnedRows"][$row]["ptLName"];
				$apptTime = formatTime($results["returnedRows"][$row]["apptTime"], 0);
				$apptDate = formatDate($results["returnedRows"][$row]["apptDate"], 0);
				$temp = $results["returnedRows"][$row]["partID"];
				$pcpRevArray[$temp] = array($pcpName , $apptDate, $apptTime,
							$results["returnedRows"][$row]["dept"], $ptName,$results["returnedRows"][$row]["MRN"]);
			}
		}
	}
	
	return $pcpRevArray;
}

//FUNCTION: UPDATE PCP NOTIFY DATE FOR RECRUITMENT TABLE - USED IN PCP REVIEW REPORT PAGE
function updPcpDate($partID) { 
	GLOBAL $errUpdMsg;
	$errUpdMsg = "";
	//update recruitment table
	$sql="UPDATE recruitment SET datePCPnotify = curdate() WHERE partID = ".$partID." ";
	$results=runQuery($sql);
	if ($results["status"]<=0){
		$errUpdMsg =  "<font color=\"#FF0000\">Can not update record in the recruitment table!! ".mysql_error()." </font><br>\n";
		return 1;
	}
	
	return 0;
}

//FUNCTION: GET PT LIST FOR RECRUITMENT LETTER
function getRecruitLttr($dept, $startDate, $endDate, $startTime, $endTime) {	 
	GLOBAL $errRvwMsg;
	$errRvwMsg = "";
	$recruitLtterArray = array();
	$tempArray = array();
	/*
	$sql="SELECT part_info.partID, appt.apptDate, appt.apptTime, part_info.ptFName, part_info.ptLName, 
		  part_info.ptAddress1, part_info.ptAddress2, part_info.ptCity, part_info.ptState, part_info.ptZip,
		  part_info.gender, appt.provdID, appt.pcpID, provider.dept, appt.apptWDay, provider.review, 
		  adddate(recruitment.datePCPnotify, 7) as notifyDate
		  FROM part_info, provider, appt, recruitment
		  WHERE part_info.MRN = appt.MRN AND appt.pcpID = provider.provID AND part_info.partID = recruitment.partID 
		  AND provider.dept = '".$dept."' AND appt.apptDate BETWEEN '".$startDate."' AND '".$endDate."' AND
		  appt.apptTime BETWEEN '".$startTime."' AND '".$endTime."' 
		  AND recruitment.dateRecrutLttr = '0000-00-00'
		  ORDER BY appt.apptDate, appt.apptTime";
	*/
	// new SQL statement by Vikki to fix glitch
	$sql="SELECT p.partID, a.apptDate, a.apptTime, p.ptFName, p.ptLName, 
		  p.ptAddress1, p.ptAddress2, p.ptCity, p.ptState, p.ptZip,
		  p.gender, a.provdID, a.pcpID, v.dept, a.apptWDay, v.review, 
		  adddate(r.datePCPnotify, 7) as notifyDate, r.dateRecrutLttr
		  FROM part_info p JOIN appt a ON p.MRN = a.MRN JOIN provider v ON a.provdID = v.provID LEFT OUTER JOIN recruitment r ON p.partID = r.partID
		  WHERE v.dept = '".$dept."' AND a.apptDate >= '".$startDate."' AND a.apptDate <= '".$endDate."' AND
		  a.apptTime >= '".$startTime."' AND a.apptTime <= '".$endTime."' ORDER BY a.apptDate, a.apptTime";
		  // removed AND r.dateRecrutLttr = '0000-00-00'
	$results=runQuery($sql);
	if ($results["status"]<=0){
		$errRvwMsg =  "<font color=\"#FF0000\">Can not select record from part_info, provider, appt and recruitment tables!! ".mysql_error()." </font><br>\n";
		return 1;
	}else{
		if($results["numRows"] >0){
			for ($row=0; $row<$results["numRows"]; $row++) {
				$ptName = ucwords(strtolower($results["returnedRows"][$row]["ptFName"])) . " " . ucwords(strtolower($results["returnedRows"][$row]["ptLName"]));
				$ptAdd = ucwords(strtolower($results["returnedRows"][$row]["ptAddress1"]));
				if ($results["returnedRows"][$row]["ptAddress2"] != '') {  
					$ptAdd .= ", ".ucwords(strtolower($results["returnedRows"][$row]["ptAddress2"])); 
				} 
				$ptState = convertState2Abbrev($results["returnedRows"][$row]["ptState"]);
				$apptTime = formatTime($results["returnedRows"][$row]["apptTime"], 0);
				$apptDate = formatDate($results["returnedRows"][$row]["apptDate"], 0);
				$notifyDate = formatDate($results["returnedRows"][$row]["notifyDate"], 0);
				$dateRecrutLttr = formatDate($results["returnedRows"][$row]["dateRecrutLttr"], 0);
				$temp = $results["returnedRows"][$row]["partID"];
				$tempArray[$temp] = array($apptDate, $apptTime, $ptName, $ptAdd, ucwords(strtolower($results["returnedRows"][$row]["ptCity"])), $ptState, $results["returnedRows"][$row]["ptZip"], $results["returnedRows"][$row]["gender"],
							$results["returnedRows"][$row]["provdID"], $results["returnedRows"][$row]["pcpID"], 
							$results["returnedRows"][$row]["dept"], $results["returnedRows"][$row]["apptWDay"],
							$results["returnedRows"][$row]["review"], $notifyDate, $dateRecrutLttr);
			}
		}
	}
	$today = date("m/d/Y");
	reset($tempArray);
	while(list($key, $val)=each($tempArray)){
		// get the pcp and current provider names
		$provdName = getProviderNames($val[8]);
		$pcpName = getProviderNames($val[9]);
		// if pcp review is yes, then check if the notify last date is >= current date
		if(strtolower($val[12]) == "y"){
			if($val[13]){
				list($month1, $day1, $year1) = split('[/]', $today);
				list($month2, $day2, $year2) = split('[/]', $val[13]);
				$formDate1 = mktime(0, 0, 0, $month1, $day1, $year1);
				$formDate2 = mktime(0, 0, 0, $month2, $day2, $year2);
				if($formDate1 >= $formDate2) {
					$recruitLtterArray[$key] = array($val[0], $val[1], $val[2], $val[3], $val[4], $val[5], $val[6], $val[7], $provdName, $pcpName, $val[10], $val[11], $val[14]); 
				}
			}
		// if pcp review is no, then put vals in the array
		}else{
			$recruitLtterArray[$key] = array($val[0], $val[1], $val[2], $val[3], $val[4], $val[5], $val[6], $val[7], $provdName, $pcpName, $val[10], $val[11], $val[14]);
			//print_r($recruitLtterArray)."<br>";
		}
	}
	return $recruitLtterArray;
}

//FUNCTION: GET PROVIDER NAMES BY PROVIDER ID
function getProviderNames($provID) {	
	$provID = trim($provID);  
	GLOBAL $errPRMsg;
	$errPRMsg = "";
	
	//select provider info from provider table
	$sql="SELECT * FROM provider WHERE provID = '$provID'";
	$results=runQuery($sql);
	if ($results["status"]<=0){
		$errPRMsg =  "<font color=\"#FF0000\">Can not select record from provider table!! ".mysql_error()." </font><br>\n";
		return 1;
	}else{
		if($results["numRows"] >0){
			$provNames = $results["returnedRows"][0]["provFName"]. " ".$results["returnedRows"][0]["provLName"];
		}
	}
	
	return $provNames;
}

//FUNCTION: UPDATE RECRUITMENT LETTER SEND DATE FOR RECRUITMENT TABLE - USED RECRUITMENT LETTER REPORT PAGE
function updRecriutLtter($partID) { 
	GLOBAL $errUpdMsg;
	$errUpdMsg = "";
	//update recruitment table
	$sql="UPDATE recruitment SET dateRecrutLttr = adddate(curdate(), 1) WHERE partID = ".$partID." ";
	$results=runQuery($sql);
	if ($results["status"]<=0){
		$errUpdMsg =  "<font color=\"#FF0000\">Can not update record in the recruitment table!! ".mysql_error()." </font><br>\n";
		return 1;
	}
	
	return 0;
}

//FUNCTION: INSERT START DATE INTO ENROLLEMNT TABLE FOR CONTROL GROUP - USED IN RECRUITMENT
function insertStartDate($partID, $startDate) {
						 
	GLOBAL $errIstSTMsg;
	$errIstSTMsg = "";
	//insert start date
	$sql= "INSERT INTO enrollment
	      	(partID, startDate)
		  VALUES
		 (".$partID.", '".$startDate."')";
	$results=runQuery($sql);
	//echo $sql;
	if ($results["status"]<=0){
		$errIstSTMsg =  "<font color=\"#FF0000\">Can not insert start date record in the enrollment table!! ".mysql_error()." </font><br>\n";
		return 1;
	}
	
	return 0;
}

//FUNCTION: GET PT LIST FOR ADMIN FORM FOR BASELINE SCHEDULE
function getAdminInfo($dept, $startDate, $endDate, $startTime, $endTime) {	 
	GLOBAL $errRvwMsg;
	$errRvwMsg = "";
	$adminInfoArray = array();
	$tempArray = array();
	
	$sql="SELECT part_info.MRN, part_info.partID, part_info.ptFName, part_info.ptLName, part_info.gender, part_info.dob, part_info.ptAddress1, 
	part_info.ptAddress2, part_info.ptCity, part_info.ptState, part_info.ptZip, part_info.ptHPhone, part_info.ptWPhone, 
	provider.dept, appt.apptDate, appt.apptTime, apptType, appt.hvmaNotes, appt.provdID, appt.pcpID, provider.randArm
	FROM part_info, provider, appt
	WHERE part_info.MRN = appt.MRN AND appt.provdID = provider.provID 
	AND provider.dept = '".$dept."' AND appt.apptDate >= '".$startDate."' AND appt.apptDate <= '".$endDate."' AND
	appt.apptTime >= '".$startTime."' AND appt.apptTime <= '".$endTime."'";
	$results=runQuery($sql);
	if ($results["status"]<=0){
		$errRvwMsg =  "<font color=\"#FF0000\">Can not select record from part_info, appt and provider tables!! ".mysql_error()." </font><br>\n";
		return 1;
	}else{
		if($results["numRows"] > 0){
			for ($row=0; $row<$results["numRows"]; $row++) {
				$mrn = $results["returnedRows"][$row]["MRN"];
				$ptName = $results["returnedRows"][$row]["ptFName"]." ".$results["returnedRows"][$row]["ptLName"];
				$ptAdd = $results["returnedRows"][$row]["ptAddress1"];
				if ($results["returnedRows"][$row]["ptAddress2"] != '') { $ptAdd .= ", ".$results["returnedRows"][$row]["ptAddress2"]; }
				$apptTime = formatTime($results["returnedRows"][$row]["apptTime"], 0);
				$apptDate = formatDate($results["returnedRows"][$row]["apptDate"], 0);
				$dob = formatDate($results["returnedRows"][$row]["dob"], 0);
				$temp = $results["returnedRows"][$row]["partID"];
				$tempArray[$temp] = array($mrn, $ptName, $results["returnedRows"][$row]["gender"], $dob, $ptAdd,
										  $results["returnedRows"][$row]["ptCity"], $results["returnedRows"][$row]["ptState"],
										  $results["returnedRows"][$row]["ptZip"], $results["returnedRows"][$row]["ptHPhone"], 
										  $results["returnedRows"][$row]["ptWPhone"],$results["returnedRows"][$row]["dept"], 
										  $apptDate, $apptTime,  $results["returnedRows"][$row]["apptType"],  
										  str_replace('\\', '', $results["returnedRows"][$row]["hvmaNotes"]), getProviderNames($results["returnedRows"][$row]["provdID"]),  
										  getProviderNames($results["returnedRows"][$row]["pcpID"]), $results["returnedRows"][$row]["randArm"]);
			}
		} 
	}
	reset($tempArray);
	return $tempArray;
}

//FUNCTION: GET PT LIST FOR RA SCHEDULE FOR BASELINE SCHEDULE
function getRaSched($dept, $startDate, $endDate, $startTime, $endTime) {	 
	GLOBAL $errRvwMsg;
	$errRvwMsg = "";
	$tempArray = array();
	// VP changed sql statement to include records that don't yet have a recruitment record 
	/* Qi's old statement 
	$sql="SELECT part_info.MRN, part_info.partID, appt.apptDate, appt.apptTime, part_info.ptFName, part_info.ptLName, part_info.gender, 
	part_info.dob, appt.apptType, appt.provdID, appt.pcpID, provider.review, adddate(recruitment.datePCPnotify, 7) as deadline,
	recruitment.pcpOptOut, recruitment.dateRecrutLttr, recruitment.dateReceived, recruitment.ptOptOut
	FROM part_info, provider, appt, recruitment 
	WHERE part_info.MRN = appt.MRN AND appt.provdID = provider.provID AND part_info.partID = recruitment.partID 
	AND provider.dept = '".$dept."' AND appt.apptDate BETWEEN '".$startDate."' AND '".$endDate."' AND
	appt.apptTime BETWEEN '".$startTime."' AND '".$endTime."' ORDER BY appt.apptDate, appt.apptTime";
	*/
	$sql="SELECT p.MRN, p.partID, a.apptDate, a.apptTime, p.ptFName, p.ptLName, p.gender, 
	p.dob, a.apptType, a.provdID, a.pcpID, v.review, adddate(r.datePCPnotify, 7) as deadline,
	r.pcpOptOut, r.dateRecrutLttr, r.dateReceived, r.ptOptOut
	FROM part_info p JOIN appt a ON p.MRN = a.MRN JOIN provider v ON a.provdID = v.provID LEFT OUTER JOIN recruitment r ON p.partID = r.partID
	WHERE v.dept = '".$dept."' AND (a.apptDate >= '".$startDate."' AND a.apptDate <= '".$endDate."') AND
	a.apptTime >= '".$startTime."' AND a.apptTime <= '".$endTime."' ORDER BY a.apptDate, a.apptTime";
	$results=runQuery($sql);
	if ($results["status"]<=0){
		$errRvwMsg =  "<font color=\"#FF0000\">Can not select record from part_info, appt and provider tables!! ".mysql_error()." </font><br>\n";
		return 1;
	}else{
		if($results["numRows"] > 0){
			for ($row=0; $row<$results["numRows"]; $row++) {
				$mrn = $results["returnedRows"][$row]["MRN"];
				$ptName = $results["returnedRows"][$row]["ptFName"] . " " . $results["returnedRows"][$row]["ptLName"];
				$apptTime = formatTime($results["returnedRows"][$row]["apptTime"], 0);
				$apptDate = formatDate($results["returnedRows"][$row]["apptDate"], 0);
				$dob = formatDate($results["returnedRows"][$row]["dob"], 0);
				$provName =  getProviderNames($results["returnedRows"][$row]["provdID"]);
				$pcpName  =  getProviderNames($results["returnedRows"][$row]["pcpID"]);
				$deadline =  formatDate($results["returnedRows"][$row]["deadline"],0);
				$recruitLttr = formatDate($results["returnedRows"][$row]["dateRecrutLttr"],0);
				$returnDate = formatDate($results["returnedRows"][$row]["dateReceived"],0);
				$temp = $results["returnedRows"][$row]["partID"];
				$tempArray[] = array($mrn, $apptDate, $apptTime, $ptName, $results["returnedRows"][$row]["gender"],
				                          $dob, $results["returnedRows"][$row]["apptType"], $provName, 
										  $pcpName, $results["returnedRows"][$row]["review"],
										  $deadline, $results["returnedRows"][$row]["pcpOptOut"], $recruitLttr, $returnDate,
										  $results["returnedRows"][$row]["ptOptOut"]);
			}
		}
	}
	reset($tempArray);
	return $tempArray;
}

//FUNCTION: GET PT LIST FOR RECEPTIONIST SCHEDULE FOR BASELINE SCHEDULE
function getReceptSched($dept, $startDate, $endDate, $startTime, $endTime) {	 
	GLOBAL $errRvwMsg;
	$errRvwMsg = "";
	$receptSchedArray = array();
	$tempArray = array();
	// Vikki changed the sql statement to include records that don't yet have a recruitment record 
	/* Qi's old statement 
	$sql="SELECT part_info.partID, appt.apptDate, appt.apptTime, part_info.ptFName, part_info.ptLName, 
	appt.apptType, appt.provdID, appt.pcpID, provider.review, recruitment.datePCPnotify
	FROM part_info, provider, appt, recruitment 
	WHERE part_info.MRN = appt.MRN AND appt.provdID = provider.provID AND part_info.partID = recruitment.partID 
	AND provider.dept = '".$dept."' AND appt.apptDate BETWEEN '".$startDate."' AND '".$endDate."' AND
	appt.apptTime BETWEEN '".$startTime."' AND '".$endTime."' ORDER BY appt.apptDate, appt.apptTime";
	*/
	$sql="SELECT p.partID, a.apptDate, a.apptTime, p.ptFName, p.ptLName, 
	a.apptType, a.provdID, a.pcpID, v.review, r.datePCPnotify
	FROM part_info p JOIN appt a ON p.MRN = a.MRN JOIN provider v ON a.provdID = v.provID LEFT OUTER JOIN recruitment r ON p.partID = r.partID 
	WHERE v.dept = '".$dept."' AND a.apptDate >= '".$startDate."' AND a.apptDate <= '".$endDate."' AND
	a.apptTime >= '".$startTime."' AND a.appttime <= '".$endTime."' ORDER BY a.apptDate, a.apptTime";
	$results=runQuery($sql);
	if ($results["status"]<=0){
		$errRvwMsg =  "<font color=\"#FF0000\">Can not select record from part_info, appt and provider tables!! ".mysql_error()." </font><br>\n";
		return 1;
	}else{
		if($results["numRows"] >0){
			for ($row=0; $row<$results["numRows"]; $row++) {
				$ptName = $results["returnedRows"][$row]["ptFName"] . " " . $results["returnedRows"][$row]["ptLName"];
				$apptTime = formatTime($results["returnedRows"][$row]["apptTime"], 0);
				$apptDate = formatDate($results["returnedRows"][$row]["apptDate"], 0);
				$notifyDate = formatDate($results["returnedRows"][$row]["datePCPnotify"],0);
				$recruitLttr = formatDate($results["returnedRows"][$row]["dateRecrutLttr"],0);
				$returnDate = formatDate($results["returnedRows"][$row]["dateReceived"],0);
				$temp = $results["returnedRows"][$row]["partID"];
				$tempArray[$temp] = array($apptDate, $apptTime, $ptName, $results["returnedRows"][$row]["apptType"],
				                          getProviderNames($results["returnedRows"][$row]["provdID"]),
										  getProviderNames($results["returnedRows"][$row]["pcpID"]), 
										  $results["returnedRows"][$row]["review"], $notifyDate);
			}
		}
	}
	reset($tempArray);
	while(list($key, $val)=each($tempArray)){
		$receptSchedArray[$key] = array($val[0], $val[1], $val[2], $val[3], $val[4], $val[5], $val[6], $val[7]);
		//print_r($raSchedArray)."<br>";
	}
	return $receptSchedArray;
}

//FUNCTION: GET COUNT 
// Created by VP to grab counts for reports
function getCountBySQL($sql) {	  
	GLOBAL $errPRMsg;
	$errPRMsg = "";
	
	//select provider info from provider table
	$results=runQuery($sql);
	if ($results["status"]<=0){
		$errPRMsg =  "<font color=\"#FF0000\">Can not select record from staff table!! ".mysql_error()." </font><br>\n";
		return 1;
	}else{
		return $results["returnedRows"][0]["total"];
	}
}

//FUNCTION: GET PARTICIPANT WITH THE SAME LAST NAME FROM APRT_INFO TABLE - USED IN PART_LIST PAGE 
// VP added this function, to allow searching with inner join 
function getPtLnameJoined($lName, $joinOn){
	GLOBAL $rcdErr;
	$ptArray = array();
	if($joinOn == 'appt'){
		$sql="SELECT p.partID, p.ptFName, p.ptLName FROM part_info p INNER JOIN ".$joinOn." j ON p.MRN = j.MRN WHERE p.ptLName = '".$lName."'";
	}else{
		$sql="SELECT p.partID, p.ptFName, p.ptLName FROM part_info p INNER JOIN ".$joinOn." j ON p.partID = j.partID WHERE p.ptLName = '".$lName."'";
	}
	$results=runQuery($sql);
    if ($results["status"]>0) {
		if ($results["numRows"]>0) {
			for ($row=0; $row<$results["numRows"]; $row++) {
				$temp = $results["returnedRows"][$row]["partID"];
				$ptArray[$temp] = array($results["returnedRows"][$row]["ptFName"], $results["returnedRows"][$row]["ptLName"]);
			}
		}
	}
	return $ptArray;
}

//FUNCTION: GET PARTICIPANT WITH THE SAME FIRST NAME FROM APRT_INFO TABLE - USED IN PART_LIST PAGE 
function getPtFnameJoined($fName, $joinOn){
// VP added this function, to allow searching with inner join 
	GLOBAL $rcdErr;
	$ptArray = array();
	if($joinOn == 'appt'){
		$sql="SELECT p.partID, p.ptFName, p.ptLName FROM part_info p INNER JOIN ".$joinOn." j ON p.MRN = j.MRN WHERE p.ptFName = '".$fName."'";
	}else{
		$sql="SELECT p.partID, p.ptFName, p.ptLName FROM part_info p INNER JOIN ".$joinOn." j ON p.partID = j.partID WHERE p.ptFName = '".$fName."'";
	}
	$results=runQuery($sql);
    if ($results["status"]>0) {
		if ($results["numRows"]>0) {
			for ($row=0; $row<$results["numRows"]; $row++) {
				$temp = $results["returnedRows"][$row]["partID"];
				$ptArray[$temp] = array($results["returnedRows"][$row]["ptFName"], $results["returnedRows"][$row]["ptLName"]);
			}
		}
	}
			
	return $ptArray;
}

//FUNCTION: GET PARTICIPANT WITH THE SAME FIRST NAME FROM APRT_INFO TABLE - USED IN PART_LIST PAGE 
function getPtBnameJoined($fName, $lName, $joinOn){
// VP added this function, to allow searching with inner join 
	GLOBAL $rcdErr;
	$ptArray = array();
	if($joinOn == 'appt'){
		$sql="SELECT p.partID, p.ptFName, p.ptLName FROM part_info p INNER JOIN ".$joinOn." j ON p.MRN = j.MRN 
	          WHERE p.ptLName = '".$lName."' AND p.ptFName = '".$fName."'";
	}else{
		$sql="SELECT p.partID, p.ptFName, p.ptLName FROM part_info p INNER JOIN ".$joinOn." j ON p.partID = j.partID 
	          WHERE p.ptLName = '".$lName."' AND p.ptFName = '".$fName."'";
	}
	$results=runQuery($sql);
    if ($results["status"]>0) {
		if ($results["numRows"]>0) {
			for ($row=0; $row<$results["numRows"]; $row++) {
				$temp = $results["returnedRows"][$row]["partID"];
				$ptArray[$temp] = array($results["returnedRows"][$row]["ptFName"], $results["returnedRows"][$row]["ptLName"]);
			}
		}
	}
			
	return $ptArray;
}

// converts mm/dd/yyyy to yyyy-mm-dd for database entry 
function datetoDB($str) {
	list($mo, $day, $yr) = explode('/', $str);
	$date = $yr.'-'.$mo.'-'.$day;
	return $date;
}

// used to convert the state string to the state abbreviation
function convertState2Abbrev($str) {
	if (strlen($str) > 2){
	$str = trim($str);
	$str = ucwords(strtolower($str));
	$states = array(
	'Alabama' => 'al',  
	'Alaska' => 'ak',  
	'Arizona' => 'az',  
	'Arkansas' => 'ar',  
	'California' => 'ca', 
	'Colorado' => 'co',  
	'Connecticut' => 'ct',  
	'Delaware' => 'de', 
	'District Of Columbia' => 'dc', 
	'Florida' => 'fl',  
	'Georgia' => 'ga',  
	'Hawaii' => 'hi',  
	'Idaho' => 'id',  
	'Illinois' => 'il',  
	'Indiana' => 'in',  
	'Iowa' => 'ia',  
	'Kansas' => 'ks',  
	'Kentucky' => 'ky',  
	'Louisiana' => 'la',  
	'Maine' => 'me',  
	'Maryland' => 'md',  
	'Massachusetts' => 'ma',  
	'Michigan' => 'mi',  
	'Minnesota' => 'mn',  
	'Mississippi' => 'ms',  
	'Missouri' => 'mo',  
	'Montana' => 'mt',  
	'Nebraska' => 'ne',   
	'Nevada' => 'nv',  
	'New Hampshire' => 'nh',  
	'New Jersey' => 'nj',  
	'New Mexico' => 'nm',  
	'New York' => 'ny',  
	'North Carolina' => 'nc', 
	'North Dakota' => 'nd',  
	'Ohio' => 'oh',  
	'Oklahoma' => 'ok', 
	'Oregon' => 'or',  
	'Pennsylvania' => 'pa',  
	'Rhode Island' => 'ri',  
	'South Carolina' => 'sc',  
	'South Dakota' => 'sd',  
	'Tennessee' => 'tn',  
	'Texas' => 'tx',  
	'Utah' => 'ut',  
	'Vermont' => 'vt',  
	'Virginia' => 'va', 
	'Washington' => 'wa',   
	'West Virginia' => 'wv',  
	'Wisconsin' => 'wi',  
	'Wyoming' => 'wy' 
	);
	return strtoupper($states[$str]);
	} else {
		return strtoupper($str);
	}
}

function makeStrUc($str) {
	$str = ucwords(strtolower($str));
	return $str;
}
?>
