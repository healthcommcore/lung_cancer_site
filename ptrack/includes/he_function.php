<?php
//FUNCTION: PRINT OUT THE COACHING CALL HEADERS
function getHeader($callNum) {
	 $header1 = "<tr><td><strong> Study ID </strong></td>";
	 $header2 = "<td><strong> First Name </strong></td>";
	 $header3 = "<td><strong> Last Name </strong></td>";
	 $header4 = "<td><strong> Due Date </strong></td>";
	 $header5 = "<td><strong> Preferred Phone </strong></td>";
	 $header6 = "<td><strong> Preferred Time </strong></td>";
	 $header7 = "<td><img src=\"images/blank.gif\"></td></tr>";
	 $tbHeader = $header1.$header2.$header3.$header4.$header5.$header6.$header7;
	 return $tbHeader;
}

//FUNCTION: GET COACHLING CALL DUE PT LIST FROM PART_INFO TABLE - USED IN CALL1_LIST PAGE
function getCounsCallDue($staffID, $dueDate1, $dueDate2, $dueDays){
	GLOBAL $rcdErr;
	$ptDueArray = array();
	// if due date 2 = 0, get the over due
	if($dueDate1 == 0){
		/* Vikki updated this SQL statement, >= startdate, instead of greater than startdate */
		$sql="SELECT part_info.partID, part_info.ptFName, part_info.ptLName, adddate(enrollment.startDate, '".$dueDays."') as dueDate 
		      FROM part_info, enrollment, userInfo
		      WHERE part_info.partID = enrollment.partID AND userInfo.studyID = part_info.partID 
			  AND enrollment.heID = ".$staffID."
			  AND part_info.ptStatus != 'I' 
			  AND curdate() >= adddate(enrollment.startDate, '".$dueDate2."') 
			  AND TLRmet = -1
			  ORDER BY enrollment.startDate ASC";
	}else{
		$sql="SELECT part_info.partID, part_info.ptFName, part_info.ptLName, adddate(enrollment.startDate, '".$dueDays."') as dueDate 
		      FROM part_info, enrollment, userInfo
		      WHERE part_info.partID = enrollment.partID AND userInfo.studyID = part_info.partID
			  AND enrollment.heID = ".$staffID."
			  AND part_info.ptStatus != 'I' AND curdate() >=  adddate(enrollment. startDate, '".$dueDate1."') 
			  AND curdate() < adddate(enrollment.startDate, '".$dueDate2."') AND
			  TLRmet = -1
			  ORDER BY enrollment.startDate ASC";
	}
	$results=runQuery($sql);
	//echo $sql."<br>\n";
	if ($results["status"]>0) {
		if ($results["numRows"]>0) {
			for ($row=0; $row<$results["numRows"]; $row++) {
				$temp = $results["returnedRows"][$row]["partID"];
				$dueDate = formatDate($results["returnedRows"][$row]["dueDate"], 0);
				$ptDueArray[$temp] = array($results["returnedRows"][$row]["ptFName"], $results["returnedRows"][$row]["ptLName"], 
				                    $dueDate);
			}
		}else{
		    if ($rcdErr == ""){$rcdErr = "<font color=\"#FF0000\">No record has been returned for coaching call due</font>\n";}
		}
	}
	//print_r($ptDueArray);
	return $ptDueArray;
}

//FUNCTION: GET COUNSELING CALL OVER DUE PT LIST FROM PART_INFO TABLE - USED IN CALL1_LIST PAGE
function getCounsCallOvDu($staffID, $beginDate, $endDate, $dueDate){
	GLOBAL $rcdErr;
	$ptOverArray = array();
	// if end date = 0, gte the over due pts
	if($endDate == 0){
		$sql="SELECT part_info.partID, part_info.ptFName, part_info.ptLName, adddate(enrollment.startDate, '".$dueDate."') as dueDate
		      FROM part_info, enrollment, userInfo
		      WHERE part_info.partID = enrollment.partID AND userInfo.studyID = part_info.partID 
			  AND enrollment.heID = ".$staffID."
			  AND part_info.ptStatus != 'I' AND curdate() >=  adddate(enrollment. startDate, '".$beginDate."') 
			   AND TLRmet = -1
			  ORDER BY enrollment.startDate ASC";
	}else{
		$sql="SELECT part_info.partID, part_info.ptFName, part_info.ptLName, adddate(enrollment.startDate, '".$dueDate."') as dueDate 
		      FROM part_info, enrollment
		      WHERE part_info.partID = enrollment.partID AND enrollment.heID = ".$staffID."
			  AND part_info.ptStatus != 'I' AND curdate() >=  adddate(enrollment. startDate, '".$beginDate."') 
			  AND curdate() < adddate(enrollment.startDate, '".$endDate."') 
			  ORDER BY enrollment.startDate ASC";
	}
	$results=runQuery($sql);
	//echo $sql."<br>\n";
    if ($results["status"]>0) {
		if ($results["numRows"]>0) {
			for ($row=0; $row<$results["numRows"]; $row++) {
				$temp = $results["returnedRows"][$row]["partID"];
				$dueDate = formatDate($results["returnedRows"][$row]["dueDate"], 0);
				$ptOverArray[$temp] = array($results["returnedRows"][$row]["ptFName"], $results["returnedRows"][$row]["ptLName"], 
				                    $dueDate);
			}
		}else{
		    if ($rcdErr == ""){$rcdErr = "<font color=\"#FF0000\">No record has been returned for over due list for call 1</font>\n";}
		}
	}
	return $ptOverArray;
}

//FUNCTION: GET LAST CONTACT RESULT FROM COUNS_CALLS TABLE - USED IN CALL1_LIST, CALL2_LIST 
function getLastContact($partID, $contactID){
	GLOBAL $rcdErr;
	
	$sql="SELECT resultID FROM coach_call WHERE  partID = ".$partID." AND callID = ".$contactID." 
		  ORDER BY callNum DESC LIMIT 1";
	$results=runQuery($sql);
    if ($results["status"]>0) {
		if ($results["numRows"]>0) {
			if($results["returnedRows"][0]["resultID"] < 10 || $results["returnedRows"][0]["resultID"] == 11){
				return $partID;
			}else{
				return 0;
			}
		}else{
		    return $partID;
		}
	}
}

//FUNCTION: GET ALL CONTACT ATTEMPTS THAT LESS THEN 5 FROM COUNS_CALLS TABLE - USED IN CALL1_LIST
function getCallAttp($partID, $contactID){
	GLOBAL $rcdErr;
	
	$sql="SELECT count(*) as attp FROM coach_call WHERE  partID = ".$partID." AND callID = ".$contactID." 
		  AND (resultID != 7 AND resultID != 8)";
	$results=runQuery($sql);
	//echo $sql;
    if ($results["status"]>0) {
		if ($results["numRows"]>0) {
			if($results["returnedRows"][0]["attp"] <5){
				return $partID;
			}else{
				return 0;
			}
		}else{
		    return $partID;
		}
	}
}

//FUNCTION: GET FINAL CALL LIST FOR CALL 1 BY FILTERING OUT THE COMPLETED, REFUSAL AND UNREACHABLE CAES
function getFinalCallList($listArray, $callID){
	$finalListArray = array();
	reset($listArray);
	while(list($keyID, $ptVal)=each($listArray)){
		// first check the last conmtact result, filter out complete, resfauls
		$resultFlg = getLastContact($keyID, $callID);
		if($resultFlg){
			// then filter out any pts that had 5 attempts
			$resultFlg1 = getCallAttp($keyID, $callID);
			if($resultFlg1){
				// get the preferred phone and time
				$callPreferArray = getCallPrefer($keyID, $callID);
				$finalListArray[$keyID] = $ptVal;
				array_push($finalListArray[$keyID], $callPreferArray[1]);
				array_push($finalListArray[$keyID], $callPreferArray[0]);
			}
		}
	}
	return $finalListArray;
}

//FUNCTION: GET CALL 2 PARTICIPANT LIST
function getLastAttpt($staffID, $callID, $due, $over){
	GLOBAL $rcdErr;
	$lastAttpArray = array();
	$ptRstArray = array();
	
	// if there is overdue date, then it is for due list
	if($over != 0){
	// first to get the last contact number
		$sql="SELECT coach_call.partID, part_info.ptFName, part_info.ptLName, adddate( max( callDate ) , ".$due." ) AS dueDate, enrollment.startDate, max( callNum ) AS maxNum
			FROM part_info, coach_call, enrollment
			WHERE part_info.partID = coach_call.partID
			AND coach_call.partID = enrollment.partID
			AND enrollment.heID =".$staffID."
			AND callID = ".$callID."
			AND ptStatus != 'I'
			GROUP BY coach_call.partID
			HAVING CURDATE( ) >= adddate( max( callDate ) , ".$due." ) 
			AND CURDATE( ) < adddate( max( callDate ) , ".$over." ) 
			ORDER BY adddate( max( callDate ) , ".$due." ) ";
	}else{
		$sql="SELECT coach_call.partID, part_info.ptFName, part_info.ptLName, adddate( max( callDate ) , ".$due." ) AS dueDate, enrollment.startDate, max( callNum ) AS maxNum
			FROM part_info, coach_call, enrollment
			WHERE part_info.partID = coach_call.partID
			AND coach_call.partID = enrollment.partID
			AND enrollment.heID =".$staffID."
			AND callID = ".$callID."
			AND ptStatus != 'I'
			GROUP BY coach_call.partID 
			HAVING CURDATE() >= adddate( max(callDate), ".$due.") 
			ORDER BY adddate( max( callDate ) , ".$due.")";
	
	}
	$results=runQuery($sql);
	//echo $sql;
    if ($results["status"]>0) {
		if ($results["numRows"]>0) {
			for ($row=0; $row<$results["numRows"]; $row++) {
				$temp = $results["returnedRows"][$row]["partID"];
				$startDate = formatDate($results["returnedRows"][$row]["startDate"], 0);
				$dueDate = formatDate($results["returnedRows"][$row]["dueDate"], 0);
				$lastAttpArray[$temp] = array($results["returnedRows"][$row]["ptFName"], $results["returnedRows"][$row]["ptLName"],
				                                     $dueDate, $startDate, $results["returnedRows"][$row]["maxNum"]);
			}
		}else{
		   if ($rcdErr == ""){$rcdErr = "<font color=\"#FF0000\">No record has been returned for last contact call for call1</font>\n";}
		}
	}
	//print_r($lastAttpArray);
	//then get the pts that only completed the call1 or refused call1
	reset ($lastAttpArray);
	while(list($key, $val)=each( $lastAttpArray)){
		$result = getLastResult($key, $val[4], $callID);
		if($result){
			$ptRstArray[$key] = array($val[0], $val[1], $val[2]);
		// next step to get pts that have 5 call attemps
		}else{
			$attemps = getCallAttp($key, $callID);
			if(!$attemps){ 
				$ptRstArray[$key] = array($val[0], $val[1], $val[2]);
			}
		}
	}
	return $ptRstArray;
}


//FUNCTION: GET CALL 1 RESULT FOR ALL PTS
function getCall1Result($staffID, $callID){
	GLOBAL $rcdErr;
	$callNumArray = array();
	$ptRstArray = array();
	// first to get the last contact number
	$sql="SELECT coach_call.partID, part_info.ptFName, part_info.ptLName, max(coach_call.callNum) as maxNum, 
	    enrollment.startDate
		FROM part_info, coach_call, enrollment 
		WHERE  part_info.partID = coach_call.partID AND coach_call.partID = enrollment.partID 
		AND enrollment.heID = ".$staffID." AND callID = ".$callID." AND ptStatus != 'I'
		GROUP BY coach_call.partID";
	$results=runQuery($sql);
    if ($results["status"]>0) {
		if ($results["numRows"]>0) {
			for ($row=0; $row<$results["numRows"]; $row++) {
				$temp = $results["returnedRows"][$row]["partID"];
				$startDate = formatDate($results["returnedRows"][$row]["startDate"], 0);
				$callNumArray[$temp] = array($results["returnedRows"][$row]["ptFName"], $results["returnedRows"][$row]["ptLName"],
				                                     $results["returnedRows"][$row]["maxNum"], $startDate);
			}
		}else{
		   if ($rcdErr == ""){$rcdErr = "<font color=\"#FF0000\">No record has been returned for last contact result</font>\n";}
		}
	}
	// then get the last contact result
	reset ($callNumArray);
	while(list($key, $val)=each($callNumArray)){
		$resultArray = getLastResult($key, $val[2], $callID);
		if($resultArray[0] != 13 && ($resultArray[0] == 10 || $resultArray[0] == 11 || $resultArray[0] == 12)){
			$ptRstArray[$key] = array($val[0], $val[1], $resultArray[0], $resultArray[1], $val[3]);
		}
	}
	//print_r($ptRstArray);
	return $ptRstArray;
}

//FUNCTION: GET CALL 1 LAST REULT BASED ON THE LAST CALL NUM
function getLastResult($partID, $callNum, $callID){
	GLOBAL $rcdErr;
	$callInfoArray = array();
	// first to get the last contact number
	$sql="SELECT resultID FROM coach_call
		WHERE partID = ".$partID." AND callID = ".$callID." AND callNum = ".$callNum."";
	$results=runQuery($sql);
    if ($results["status"]>0) {
		if ($results["numRows"]>0) {
			if($results["returnedRows"][0]["resultID"] == 10 || $results["returnedRows"][0]["resultID"] ==12){
			   return $results["returnedRows"][0]["resultID"];}
		}else{
		   if ($rcdErr == ""){$rcdErr = "<font color=\"#FF0000\">No record has been returned for last contact result</font>\n";}
		}
	}
	
	return 0;
}


//FUNCTION: GET CALL 2 CATEGORIES
function getCallCat($partID, $resultID, $callDate, $startDate){
	GLOBAL $rcdErr;
	$callNumArray = array();
	$dateDue1 = 28;
	$dateDue2 = 42;
	$today = date("m/d/Y");
	$notYetDueArray = array();
	$dueArray = array();
	$overDueArray = array();
	// calculate the due date for call 2
	if($resultID == 10 || $resultID == 12){
		// for result = complete or refusal, due date is startdate + 28
		list($month, $date, $year) = split('[/]', $callDate);
		$call2DueDate = date("m/d/Y", mktime(0, 0, 0, $month, $date+$dateDue1, $year));
		//echo "duse date is ".$call2DueDate."<br>";
		// get over due date
		list($month1, $date1, $year1) = split('[/]', $call2DueDate);
		$call2OverDue = date("m/d/Y", mktime(0, 0, 0, $month1, $date1+$dateDue1, $year1));
		$notDueFlg = compareDates($today, $call2DueDate);
		$overDueFlg = compareDates($today, $call2OverDue);
		if($overDueFlg){
			$overDueArray = array($call2DueDate, 2);
			return $overDueArray;
		// if today <= over due date, then it's either over due, or due
		}else{
			if($today == $call2OverDue){
				$overDueArray = array($call2DueDate, 2);
				return $overDueArray;
			// if today < over due, then it's due or not yet due
			}else{
				// if today > due date, the not yet due flg = 1, then it is due
				if($notDueFlg){
					$dueArray = array($call2DueDate, 1);
					return $dueArray;
				}else{
					if($today == $call2DueDate){
						$dueArray = array($call2DueDate, 1);
						return $dueArray;
					}
					// if today <= due date, the due flg = 0, it's either due or voer due
					else{
						$notYetDueArray = array($call2DueDate, 0);
						return $notYetDueArray;
					}
				}
			}
		}
	// for unreachable, the due date is startDate + 42
	}elseif($resultID == 11){
		list($month, $date, $year) = split('[/]', $startDate);
		$call2DueDate = date("m/d/Y", mktime(0, 0, 0, $month, $date+$dateDue2, $year));
		//echo "duse date is ".$call2DueDate."<br>";
		// get over due date
		list($month1, $date1, $year1) = split('[/]', $call2DueDate);
		$call2OverDue = date("m/d/Y", mktime(0, 0, 0, $month1, $date1+$dateDue1, $year1));
		$notDueFlg = compareDates($today, $call2DueDate);
		$overDueFlg = compareDates($today, $call2OverDue);
		//if today > over dueDate, then over due flg = 1 and it is over due
		if($overDueFlg){
			$overDueArray = array($call2DueDate, 2);
			return $overDueArray;
		// if today <= over due date, then it's either over due, or due
		}else{
			if($today == $call2OverDue){
				$overDueArray = array($call2DueDate, 2);
				return $overDueArray;
			// if today < over due, then it's due or not yet due
			}else{
				// if today > due date, the not yet due flg = 1, then it is due
				if($notDueFlg){
					$dueArray = array($call2DueDate, 1);
					return $dueArray;
				}else{
					if($today == $call2DueDate){
						$dueArray = array($call2DueDate, 1);
						return $dueArray;
					}
					// if today <= due date, the due flg = 0, it's either due or voer due
					else{
						$notYetDueArray = array($call2DueDate, 0);
						return $notYetDueArray;
					}
				}
			}
		}
	}
}

//FUNCTION: GET LAST CONTACT RESULT FOR CALL 2 FROM COUNS_CALLS TABLE - USED IN CALL1_LIST, CALL2_LIST 
function getLastCall2($partID, $contactID){
	GLOBAL $rcdErr;
	
	$sql="SELECT resultID FROM coach_call WHERE  partID = ".$partID." AND callID = ".$contactID." 
		  ORDER BY callNum DESC LIMIT 1";
	$results=runQuery($sql);
    if ($results["status"]>0) {
		if ($results["numRows"]>0) {
			$resultID = $results["returnedRows"][0]["resultID"];
		}else{
			$resultID = 0;
		}
	}
	
	return $resultID;
}

//FUNCTION: GET PARTICIPANT'S CONTACT CALL INFO FROM COACH_CALL TABLE - USED IN CONTACT CALLPAGE
function getCallInfo($partID, $callID){
	GLOBAL $rcdErr;
	$callInfoArray = array();
	$sql="SELECT * FROM coach_call WHERE partID = ".$partID." AND callID = ".$callID."";
	$results=runQuery($sql);
    if ($results["status"]>0) {
		if ($results["numRows"]>0) {
			for ($row=0; $row<$results["numRows"]; $row++) {
				$callDate = formatDate($results["returnedRows"][$row]["callDate"], 0);
				//$startTime = formatTime($results["returnedRows"][$row]["startTime"], 0);
				//$endTime = formatTime($results["returnedRows"][$row]["endTime"], 0);
				$callInfoArray[$row] = array($results["returnedRows"][$row]["callNum"], $callDate,
					$results["returnedRows"][$row]["callWDay"], $results["returnedRows"][$row]["phoneNum"],
					$results["returnedRows"][$row]["startTime"], $results["returnedRows"][$row]["endTime"], $results["returnedRows"][$row]["resultID"]);
			}
		}else{
			$rcdErr = "no caoching call record has been returned for this participant\n";
		}
	}
	return $callInfoArray;
}

//FUNCTION: PRINT OUT THE COACHING CALL TABLE CONTENT
function getTableContent($ptConArray, $heID, $callID) {
	reset ($ptConArray);
	while(list($keyID, $ptVal)=each($ptConArray)){
		//echo "The site name is " .$sitKey. "<br>";
	    reset($ptVal);
		$ptCount = count($ptVal);
		echo "<form name = form1 method=\"POST\" action = \"contact_calls.php?callID=".$callID."&heID=".$heID."\">\n";
		echo "<tr align = \"center\" bgcolor = ".$color.">\n";
		echo "<td>" .$keyID. "<input type = \"hidden\" name = \"partID\" value = \"".$keyID."\"></td>\n";
		echo "<td>" .$ptVal[0]. "<input type = \"hidden\" name = \"fName\" value = \"".$ptVal[0]."\"></td>\n";
		echo "<td>" .$ptVal[1]. "<input type = \"hidden\" name = \"lName\" value = \"".$ptVal[1]."\"></td>\n";
		echo "<td>" .$ptVal[2]."</td>\n";
		echo "<td>" .$ptVal[3]."</td>\n";
		echo "<td>" .$ptVal[4]."</td>\n";
		echo "<td><input type = \"submit\" name = \"action\" value = \"Coaching Calls\"></td>\n";
		echo "</tr></form>\n";
	}
}

//FUNCTION: GET ALL DISPLAY INFO FOR CONTACT CALL - USED IN CONTACT CALL PAGE
function getDisplayInfo($partID){
	GLOBAL $rcdErr;
	$displayArray = array();
	$sql="SELECT part_info.gender, part_info.dob, part_info.ptCity, enrollment.ixModality, enrollment.remindRand, 
			enrollment.reminModality, appt.apptDate, appt.provdID, appt.pcpID, recruitment.raID1, part_info.ptFName,
			part_info.ptLName
			FROM part_info, appt, recruitment, enrollment
			WHERE part_info.MRN = appt.MRN AND part_info.partID = recruitment.partID 
			AND part_info.partID = enrollment.partID AND part_info.partID = ".$partID."";
	$results=runQuery($sql);
    if ($results["status"]>0) {
		if ($results["numRows"]>0) {
			if($results["returnedRows"][0]["gender"] == "F"){
				$gender = "Female";
			}else{
				$gender = "Male";
			}
			$displayArray[0] = $gender;
			$dob = formatDate($results["returnedRows"][0]["dob"], 0);
			$displayArray[1] = $dob;
			$displayArray[2] = $results["returnedRows"][0]["ptCity"];
			if($results["returnedRows"][0]["ixModality"] == 1){
				$ixModality = "Web";
			}else{
				$ixModality = "Print";
			}
			$displayArray[3] = $ixModality;
			if($results["returnedRows"][0]["remindRand"] == 1){
				$remindRand = "Yes";
			}else{
				$remindRand = "No";
			}
			$displayArray[4] = $remindRand;
			if($results["returnedRows"][0]["reminModality"] == 1){
				$reminModality = "Voice";
			}else{
				$reminModality = "Text";
			}
			$displayArray[5] = $reminModality;
			$apptDate = formatDate($results["returnedRows"][0]["apptDate"], 0);
			$displayArray[6] = $apptDate;
			$providName = getProvdName($results["returnedRows"][0]["provdID"]);
			$displayArray[7] = $providName;
			$pcpName = getProvdName($results["returnedRows"][0]["pcpID"]);
			$displayArray[8] = $pcpName;
			$raName = getBlRa($results["returnedRows"][0]["raID1"]);
			$displayArray[9] = $raName;
			$displayArray[10] = $results["returnedRows"][0]["ptFName"];
			$displayArray[11] = $results["returnedRows"][0]["ptLName"];
		}else{
			$rcdErr = "no crecord has been returned for this participant\n";
		}
	}
	return $displayArray;
}

//FUNCTION: GET LIST OF RA NAMES - USED IN EDIT RECRUITMENT PAGE
function getBlRa($staffID) {	  
	GLOBAL $errBLRMsg;
	$errBLRsg = "";
	
	//select staff first and last names from staff table
	$sql="SELECT staffFName, staffLName FROM staff WHERE staffID = ".$staffID."";
	$results=runQuery($sql);
	if ($results["status"]<=0){
		$errBLRMsg =  "<font color=\"#FF0000\">Can not select record from staff table!! ".mysql_error()." </font><br>\n";
		return 1;
	}else{
		if($results["numRows"] >0){
			for ($row=0; $row<$results["numRows"]; $row++) {
				$temp = $results["returnedRows"][$row]["staffID"];
				$raName = $results["returnedRows"][$row]["staffFName"]. " ". $results["returnedRows"][$row]["staffLName"];
			}
		}
	}
	 
	return $raName;
}

//FUNCTION: GET TFR SENT DATE - USED IN CONTACT CALL PAGE
function getTFRDate($partID) {	  
	GLOBAL $errTFRMsg;
	$errTFRMsg = "";
	
	//select staff first and last names from staff table
	$sql="SELECT * FROM TFR_info WHERE partID = ".$partID."";
	$results=runQuery($sql);
	if ($results["status"]<=0){
		$errTFRMsg =  "<font color=\"#FF0000\">Can not select record from TFR_info table!! ".mysql_error()." </font><br>\n";
		return 1;
	}else{
		if($results["numRows"] >0){
			$dateMailed = $results["returnedRows"][0]["dateMailed"];
		}
	}
	 
	return $dateMailed;
}

//FUNCTION: PRINT OUT THE PT INFO
function displayPtInfo($displayArray, $dateMailed) {
	$line1 = "<p><font color=\"#526D6D\"><em><strong>Gender:</strong> ".$displayArray[0]. " <strong>DOB:</strong> ".$displayArray[1]. " <strong>City</strong>: ".$displayArray[2]."</em></font></p>";
	$line2 = "<p><font color=\"#526D6D\"><em><strong>IX Modality:</strong> ".$displayArray[3]. " <strong>Reminder Randomization:</strong> ".$displayArray[4]. " <strong>Reminder Modality</strong>: ".$displayArray[5]."</em></font></p>";
	$line3 = "<p><font color=\"#526D6D\"><em><strong>Appt Date:</strong> ".$displayArray[6]. " <strong>Provider Name:</strong> ".$displayArray[7]. " <strong>PCP Name:</strong> ".$displayArray[8]."</em></font></p>";
	$line4 = "<p><font color=\"#526D6D\"><em><strong>Baseline RA 1:</strong>" .$displayArray[9]. " <strong>Date TFR Sent:</strong> ".$dateMailed ."</em></font></p>";
	$allLines = $line1.$line2.$line3.$line4;
	 return $allLines;
}

//FUNCTION: PRINT OUT THE COACHING CALL TABLE HEADERS
function getTableHeader() {
	$table = "<table border =1 align = \"left\" cellpadding =40>";
	//$header1 = "<tr><td><img src=\"images/blank.gif\"></td>";
	$header2 = "<tr><td><strong> Call Number </strong></td>";
	$header3 = "<td><strong> Call Date </strong></td>";
	$header4 = "<td><strong> Call Day </strong></td>";
	$header5 = "<td><strong> Phone Number </strong></td>";
	$header6 = "<td><strong>Start Time </strong></td>";
	$header7 = "<td><strong> End Time </strong></td>";
	$header8 = "<td><strong>Contact Result</td></strong>";
	$header9 = "<td><strong>Remove</td></strong></tr>";
	$tbHeader = $table.$header2.$header3.$header4.$header5.$header6.$header7.$header8.$header9;
	return $tbHeader;
}

//FUNCTION: GET CONTACT RESULT FROM CALL RESULT TABLE
function getContactResult($callID){
	GLOBAL $rcdErr;
	$callResult = array();
	if($callID == 1){
		$sql="SELECT * FROM call_result";
	}else{
		$sql="SELECT * FROM call_result WHERE resultID BETWEEN 1 AND 12";
	}
	$results=runQuery($sql);
    if ($results["status"]>0) {
		if ($results["numRows"]>0) {
			for ($row=0; $row<$results["numRows"]; $row++) {
				$temp = $results["returnedRows"][$row]["resultID"];
				$callResult[$temp] =  $results["returnedRows"][$row]["result"];
			}
		}else{
			$rcdErr = "no record has been returned for this participant\n";
		}
	}
	return $callResult;
}

//FUNCTION: GET CALL SCRIPT BASED ON CALL #
function getScript($callID){
	GLOBAL $rcdErr;
	$tableName = "call".$callID."_script";
	$scriptArray = array();
	$sql="SELECT * FROM $tableName ORDER BY scriptID ASC";
	$results=runQuery($sql);
    if ($results["status"]>0) {
		if ($results["numRows"]>0) {
			for ($row=0; $row<$results["numRows"]; $row++) {
				$temp = $results["returnedRows"][$row]["scriptID"];
				$scriptArray[$temp] =  $results["returnedRows"][$row]["script"];
			}
		}else{
			$rcdErr = "no record has been returned for the script\n";
		}
	}
	//print_r($scriptArray);
	return $scriptArray;
}

//FUNCTION: GET PT REVIEW VALUE
function getReview($partID, $callID){
	GLOBAL $rcdErr;
	
	$reviewArray = array();
	$sql="SELECT * FROM review_script WHERE partID = ".$partID." AND callID = ".$callID." ORDER BY scriptID ASC";
	$results=runQuery($sql);
    if ($results["status"]>0) {
		if ($results["numRows"]>0) {
			for ($row=0; $row<$results["numRows"]; $row++) {
				$temp = $results["returnedRows"][$row]["scriptID"];
				$reviewArray[$temp] =  $results["returnedRows"][$row]["reviewed"];
			}
		}else{
			$rcdErr = "no record has been returned for the script\n";
		}
	}
	//print_r($scriptArray);
	return $reviewArray;
}

//FUNCTION: DISPLAY THE REVIEW POINTS
function displayReview($reviewArray, $scriptArray, $flg=0) {
	for($x = 1; $x<=count($scriptArray); $x++){
		if(!$flg){
			$checked = ($reviewArray[$x] == 1)? "checked":"";
		}else{
			$checked = ($reviewArray[$x] == "on")? "checked":"";
		}
		
		$colspan = ($x == count($scriptArray))? "8":"4";
		if($x%2 != 0){
			$tr1 = "<tr>\n";
			$td1 = "<td colspan = ".$colspan."><input type = \"checkbox\" name = \"".substr($scriptArray[$x], 0, 5)."\" ".$checked.">".$scriptArray[$x]."</td>\n";
			echo $tr1.$td1;
		}else{
			$td2 = "<td colspan = 4 ><input type = \"checkbox\" name = \"".substr($scriptArray[$x], 0, 5)."\" ".$checked.">".$scriptArray[$x]."</td>\n";
			$tr2 = "</tr>\n";
			echo $td2.$tr2;
		}
		//$row = $row. $tr1.$td1.$td2.$tr2;
	}
}

//FUNCTION: GET CALL NOTES
function getCallNotes($partID){
	GLOBAL $rcdErr;
	
	$sql="SELECT * FROM call_notes WHERE partID = ".$partID."";
	$results=runQuery($sql);
    if ($results["status"]>0) {
		if ($results["numRows"]>0) {
			$callNotes =  $results["returnedRows"][0]["callNotes"];
		}else{
			$rcdErr = "no record has been returned for the script\n";
		}
	}
	return $callNotes;
}

//FUNCTION: INSERT RECORD TO THE COACH CALL TABLE - USED IN CONATCT CALL PAGE
function insertCalls($inputConArray, $partID, $callID) {
					  
	GLOBAL $errIstMsg;
	$errIstMsg = "";
	// delete old record
	$sql = "DELETE FROM coach_call WHERE partID = ".$partID." AND callID = ".$callID."";
	$results=runQuery($sql);
	
	// insert all records
	reset($inputConArray);
	while(list($key, $val)=each($inputConArray)){
		$conDate = formatDate($val[1], 1);
		$timeLength = getTimeLength($val[5], $val[4]);
		$sql1 = "INSERT INTO coach_call
		      	(partID, callID, callNum, callDate, callWDay, phoneNum, startTime, endTime, callLength, resultID)
			  VALUES
			 (".$partID.", ".$callID.", ".$val[0].", '".$conDate."',  '".$val[2]."', '".$val[3]."',
			    '".$val[4]."', '".$val[5]."', '".$timeLength."', '".$val[6]."')";
		$results=runQuery($sql1);
		if ($results["status"]== -1){
			$errIstMsg =  "<font color=\"#FF0000\">Can not insert record into coach_call table!! ".mysql_error()." </font><br>\n";
			return 1;
		}
	}
	return 0;
}

//FUNCTION:GET TIME DIFF BETWEEN START TIME AND END TIME - USED IN CONATCT CALL PAGE
function getTimeLength($endTime, $startTime) {
					  
	GLOBAL $errIstMsg;
	$errIstMsg = "";
	// delete old record
	$sql = "SELECT timediff('".$endTime."', '".$startTime."') as length";
	$results=runQuery($sql);
	$timeLength = $results["returnedRows"][0]["length"];
	
	return $timeLength;
}

//FUNCTION: INSERT RECORD TO THE REVIW SCRIPT TABLE - USED IN CONATCT CALL PAGE
function insertReview($inputSpArray, $partID, $callID) {
					  
	GLOBAL $errIstMsg;
	$errIstMsg = "";
	// delete old record
	$sql = "DELETE FROM review_script WHERE partID = ".$partID." AND callID = ".$callID."";
	$results=runQuery($sql);
	
	// insert all records
	for($a = 1; $a<=count($inputSpArray); $a++){
		if($inputSpArray[$a] == "on"){
			$review = 1;
		}else{
			$review = 0;
		}
		$sql1 = "INSERT INTO review_script
		      	(partID, callID, scriptID, reviewed)
			  VALUES
			 (".$partID.", ".$callID.", ".$a.", ".$review.")";
		$results=runQuery($sql1);
		if ($results["status"]== -1){
			$errIstMsg =  "<font color=\"#FF0000\">Can not insert record into review_script table!! ".mysql_error()." </font><br>\n";
			return 1;
		}
	}
	return 0;
}

//FUNCTION: INSERT RECORD TO THE CALL NOTES TABLE - USED IN CONATCT CALL PAGE
function insertCallNotes($oldNotes, $newNotes, $partID) {
	GLOBAL $errIstMsg;
	$errIstMsg = "";
	if($oldNotes)$oldNotesStr = mysql_real_escape_string($oldNotes);
	if($newNotes)$newNotesStr = mysql_real_escape_string($newNotes);
	// if there is a old notes, tehn update records
	if($oldNotes){
		$sql = "UPDATE call_notes SET callNotes = '".$newNotesStr."' WHERE partID = ".$partID."";
	}else{
		// else,insert records
		$sql = "INSERT INTO call_notes
		      	(partID, callNotes)
			  VALUES
			 (".$partID.", '".$newNotesStr."')";
	}
	$results=runQuery($sql);
	if ($results["status"]== -1){
		$errIstMsg =  "<font color=\"#FF0000\">Can not insert record into review_script table!! ".mysql_error()." </font><br>\n";
		return 1;
	}
	return 0;
}

//FUNCTION: UPDATE PT NOTES IN THE PART_INFO TABLE - USED IN CONTACT CALL PAGE
function updatePtNotes($partID, $ptNotes) {
	GLOBAL $errUpdMsg;
	$errUpdMsg = "";
	$ptNotesStr = mysql_real_escape_string($ptNotes);
	// update pt notes
	$sql = "UPDATE part_info SET notes = '".$ptNotesStr."' WHERE partID = ".$partID."";
	$results=runQuery($sql);
	if ($results["status"]<=0){
		$errUpdMsg =  "<font color=\"#FF0000\">Can not update pt notes in the part_info table!! ".mysql_error()." </font><br>\n";
		return 1;
	}
	return 0;
}

//FUNCTION: INSERT CALL 2 PREFER INTO CALL_PREFER TABLE - USED IN CONTACT CALLS
function insertCall2Prefer($prefer2Array, $preferNotes, $preferPhone, $partID, $callID) {
						 
	GLOBAL $errIstMsg;
	$errIstMsg = "";
	if(count($prefer2Array)<=0){
		//$sql1 = "delete from call_prefer where partID = ".$partID." and callID = ".$callID."";
		//$results=runQuery($sql1);
		$sql= "INSERT INTO call_prefer
		      	(partID, callID, preferTime, preferPhone, preferNotes)
			  VALUES
			 (".$partID.", ".$callID.", '', '".$preferPhone."',  '".$preferNotes."')";
	}else{
		$sql="UPDATE call_prefer SET  preferPhone = '".$preferPhone."', preferNotes = '".$preferNotes."'
	      WHERE partID = ".$partID." AND callID = ".$callID."";
	}
	$results=runQuery($sql);
	$sql;
	if ($results["status"]== -1){
		if(count($prefer2Array)<=0){
			$save = "insert";
		}else{
			$save = "update";
		}
		$errIstMsg =  "<font color=\"#FF0000\">Can not ".$save." record in the call_prefer table!! ".mysql_error()." </font><br>\n";
		return 1;
	}
	
	return 0;
}

//FUNCTION: GET FINAL CALL REPORT FOR CALL 1 BY FILTERING OUT THE COMPLETED, REFUSAL AND UNREACHABLE CAES
function getFinalCallReport($listArray, $callID){
	$finalListArray = array();
	reset($listArray);
	while(list($keyID, $ptVal)=each($listArray)){
		$resultFlg = getLastContact($keyID, $callID);
		if($resultFlg){
			// get the preferred phone and time
			$callNotes= getGenNotes($keyID);
			$finalListArray[$keyID] = $ptVal;
			array_push($finalListArray[$keyID], $callNotes);
		}
	}
	return $finalListArray;
}

//FUNCTION: PRINT OUT THE COACHING CALL REPORT CONTENT
function getReportContent($ptConArray, $heID, $callID) {
	reset ($ptConArray);
	while(list($keyID, $ptVal)=each($ptConArray)){
		//echo "The site name is " .$sitKey. "<br>";
	    reset($ptVal);
		$ptCount = count($ptVal);
		echo "\n$keyID\t$ptVal[0]\t$ptVal[1]\t$ptVal[2]\t$ptVal[3]\t";
	}
}

//FUNCTION: GET GENERAL NOTES
function getGenNotes($partID){
	GLOBAL $rcdErr;
	
	$sql="SELECT notes FROM part_info WHERE partID = ".$partID."";
	$results=runQuery($sql);
    if ($results["status"]>0) {
		if ($results["numRows"]>0) {
			$generalNotes =  $results["returnedRows"][0]["notes"];
		}else{
			$rcdErr = "no record has been returned for the general notes\n";
		}
	}
	return $generalNotes;
}
?>