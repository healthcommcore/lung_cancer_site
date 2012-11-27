<?php

//FUNCTION: PRINT OUT THE MENU FOR ADMIN PAGES
function getAdMenu() {
	 $link1 = "<ul><a href=\"recruit_report.php?cat=Site\">Recruitment Results by Site and Provider </a></ul><br>";
	 $link2 = "<ul><a href=\"recruit_report.php?cat=Randomization\" >Recruitment Results by Randomization Arm </a></ul><br>";
	 $link3 = "<ul><a href=\"recruit_report.php?cat=RA\">Recruitment Results by Baseline RA </a></ul><br>";
	 $link4 = "<ul><a href=\"withdraw_report.php\">Withdrawals from Study </a></ul><br>";
	 $link5 = "<ul><a href=\"coaching_call_report.php\" >Coaching Call Report </a></ul><br>";
	 $link6 = "<ul><a href=\"ix_modality_report.php\">Ix Modality Report </a></ul><br>";
	 $link7 = "<ul><a href=\"remind_modality_report.php\">Reminder Modality Report </a></ul><br>";
	 $link8 = "<ul><a href=\"reportWeb.php\">Web Statistics Report </a></ul><br>";
	 $link9 = "<ul><a href=\"get_qc_report.php\">Quality Control Reports</a></ul><br>";
	 $link10 = "<ul><a href=\"change_start_date.php\">Change Start Date</a></ul><br>";
	 $link11 = "<ul><a href=\"download_hd2_pts.php\">Download Withdrew participants in HD2</a></ul><br>";
	 $menu = $link1.$link2.$link3.$link4.$link5.$link6.$link7.$link8.$link9.$link10.$link11;

	 return $menu;
}

//FUNCTION: GET PROVIDER LIST BY DEPARTMENT
function getProvidList($deptArray) {	  
	GLOBAL $rcdMsg;
	$rcdNMsg = "";
	$providArray = array();
	for ($a = 0; $a <count($deptArray); $a++){
	//select provider name list from the provider table
		$sql="SELECT provID, CONCAT(\"Dr.\", provFName, \" \", provLName) as name FROM provider where dept = '".$deptArray[$a]."'";
		$results=runQuery($sql);
		if ($results["status"]>0) {
			if ($results["numRows"]>0) {
				for ($row=0; $row<$results["numRows"]; $row++) {
					$providArray[$deptArray[$a]][$row] = array($results["returnedRows"][$row]["provID"], $results["returnedRows"][$row]["name"]);
				}
			}
		}else{
			$rcdNMsg =  "<font color=\"#FF0000\">Can not select record from provider table!! ".mysql_error()." </font><br>\n";
		}
	}
	return $providArray;
}

//FUNCTION: GET PROVIDER LIST BY RANDOMIZATION ARM
function getProvidRand($armArray) {	  
	GLOBAL $rcdMsg;
	$rcdNMsg = "";
	$providArray = array();
	for ($a = 0; $a <count($armArray); $a++){
	//select provider name list from the provider table
		$sql="SELECT provID, CONCAT(\"Dr.\", provFName, \" \", provLName) as name FROM provider where randArm = '".$armArray[$a]."'";
		$results=runQuery($sql);
		if ($results["status"]>0) {
			if ($results["numRows"]>0) {
				for ($row=0; $row<$results["numRows"]; $row++) {
					if($armArray[$a] == "uc"){
						$temp = "ARM 1: Usual Care";
					}elseif($armArray[$a] == "mats"){
						$temp = "ARM 2: Ix-material Only";
					}else{
						$temp = "ARM 3: Ix-material + C.C.";
					}
					$providArray[$temp][$row] = array($results["returnedRows"][$row]["provID"], $results["returnedRows"][$row]["name"]);
				}
			}
		}else{
			$rcdNMsg =  "<font color=\"#FF0000\">Can not select record from provider table!! ".mysql_error()." </font><br>\n";
		}
	}
	return $providArray;
}

//FUNCTION: GET PT LIST BY PROVIDER
function getProvPtList($providArray) {	  
	GLOBAL $rcdMsg;
	$rcdNMsg = "";
	$ptListArray = array();
	reset($providArray);
	while(list($cat, $provid)=each($providArray)){
		reset($provid);
		while(list($key, $val)=each($provid)){
			//select pt name list from the part_info, appt table
			$sql="SELECT part_info.partID FROM part_info, appt WHERE part_info.MRN = appt.MRN AND appt.provdID = '".$val[0]."'";
			$results=runQuery($sql);
			if ($results["status"]>0) {
				if ($results["numRows"]>0) {
					for ($row=0; $row<$results["numRows"]; $row++) {
						$temp = $val[1];
						$ptListArray[$cat][$temp][$row] = $results["returnedRows"][$row]["partID"];
					}
				}
			}else{
				$rcdNMsg =  "<font color=\"#FF0000\">Can not select record from provider table!! ".mysql_error()." </font><br>\n";
			}
		}
	}
	//print_r($ptListArray);
	return $ptListArray;
}

//FUNCTION: GET PT BL RESULT BREAK DOWN
function getPtRecritResult($providArray, $co) {	  
	GLOBAL $rcdMsg;
	$rcdNMsg = "";
	$ptResultArray = array();
	$ptCatResult = array();
	$co = substr($co, 0, 1);
	reset($ptListArray);
	while(list($cat, $provdArray)=each($providArray)){
		$catTotal = 0;
		$conTotal = 0;
		$noShTotal = 0;
		$sftRefTotal = 0;
		$hdRefTotal = 0;
		$prvdTotal = 0;
		$notWTotal = 0;
		$langTotal = 0;
		$otherTotal = 0;
		$unableTotal = 0;
		$notETotal = 0;
		reset($provdAray);
		while(list($key, $val)=each($provdArray)){
			$total = 0;
			$consent= 0;
			$noShow = 0;
			$sftRefs = 0;
			$hdRefs = 0;
			$prvdOpt = 0;
			$notWell = 0;
			$language = 0;
			$other = 0;
			$unable = 0;
			$notEnroll = 0;
			
			//select bl result from recutiment table
			$sql="SELECT r.blResult FROM recruitment r, part_info p, appt a, provider pr WHERE r.partID = p.partID AND p.MRN = a.MRN 
			      AND pr.provID = a.provdID AND a.provdID = '".$val[0]."' AND r. giveClipbd = '".$co."'";
			$results=runQuery($sql);
			if ($results["status"]>0) {
				if ($results["numRows"]>0) {
					for ($row=0; $row<$results["numRows"]; $row++) {
						switch ($results["returnedRows"][$row]["blResult"]){
							case 1;
							$consent++;
							break;
							
							case 2;
							$noShow++;
							break;
							
							case 3;
							$sftRefs++;
							break;
							
							case 4;
							$hdRefs++;
							break;
							
							case 5;
							$prvdOpt++;
							break;
								
							case 6;
							$notWell++;
							break;
							
							case 7;
							$language++;
							break;
						
							case 8;
							$other++;
							break;
							
							case 9;
							$unable++;
							break;
							
							case 10;
							$notEnroll++;
							break;
						}
					}
				}
			}else{
				$rcdNMsg =  "<font color=\"#FF0000\">Can not select record from provider table!! ".mysql_error()." </font><br>\n";
			}
			
			// get the total of each provider's bl result
			$total = $consent + $noShow + $sftRefs + $hdRefs + $prvdOpt + $notWell + $language + $other + $unable + $notEnroll;
			// total of each bl result cross provider
			$conTotal = $conTotal + $consent;
			$noShTotal = $noShTotal + $noShow;
			$sftRefTotal = $sftRefTotal + $sftRefs;
			$hdRefTotal = $hdRefTotal + $hdRefs;
			$prvdTotal = $prvdTotal + $prvdOpt;
			$notWTotal = $notWTotal + $notWell;
			$langTotal = $langTotal + $language;
			$otherTotal = $otherTotal + $other;
			$unableTotal = $unableTotal + $unable;
			$notETotal = $notETotal + $notEnroll;
			// get the total for this site/randomization
			$catTotal = $catTotal + $total;
			//echo "the provider name is ".
			$ptResultArray[$val[1]] = array($total, $consent, $noShow, $sftRefs, $hdRefs, $prvdOpt, $notWell, $language, $other, $unable, $notEnroll);
			//print_r($ptResultArray);
		}
		
		$ptCatResult[$cat][0] = array($catTotal, $conTotal, $noShTotal, $sftRefTotal, $hdRefTotal, $prvdTotal, $notWTotal, $langTotal, $otherTotal, $unableTotal, $notETotal);
		$ptCatResult[$cat][1] = $ptResultArray;
		$ptResultArray = array();
	}
	print_r($ptSiteResult);
	return $ptCatResult;
}

//FUNCTION: GET RA LIST
function getBlRAList() {	  
	GLOBAL $rcdMsg;
	$rcdNMsg = "";
	$raArray = array();
	
	//select ra list from staff table
	$sql="SELECT staffID, CONCAT(staffFName, \" \", staffLName) as staffName FROM staff WHERE title = 'RA' AND status = 'A'";
	$results=runQuery($sql);
	if ($results["status"]>0) {
		if ($results["numRows"]>0) {
			for ($row=0; $row<$results["numRows"]; $row++) {
				$temp = $results["returnedRows"][$row]["staffID"];
				$raArray[$temp] = $results["returnedRows"][$row]["staffName"];
			}
		}
	}else{
		$rcdNMsg =  "<font color=\"#FF0000\">Can not select record from provider table!! ".mysql_error()." </font><br>\n";
	}
	return $raArray;
}

//FUNCTION: GET PT LIST BY BL RAs
function getRaPtList($raArray1) {	  
	GLOBAL $rcdMsg;
	$rcdNMsg = "";
	$raArray2 = $raArray1;
	$raPtArray = array();
	reset($raArray1);
	while(list($ID1, $name1)=each($raArray1)){
		reset($raArray2);
		while(list($ID2, $name2)=each($raArray2)){
			//select pt name list from the part_info, appt table
			$sql="SELECT partID FROM recruitment WHERE raID1 = ".$ID1." AND raID2 = ".$ID2."";
			$results=runQuery($sql);
			if ($results["status"]>0) {
				if ($results["numRows"]>0) {
					for ($row=0; $row<$results["numRows"]; $row++) {
						$temp = "BL_RA #2=".$name2;
						$raPtArray[$name1][$temp][$row] = $results["returnedRows"][$row]["partID"];
					}
				}
			}else{
				$rcdNMsg =  "<font color=\"#FF0000\">Can not select record from provider table!! ".mysql_error()." </font><br>\n";
			}
		}
	}
	//print_r($raPtArray);
	return $raPtArray;
}

//FUNCTION: GET BL RA WITH BL RESULT BREAK DOWN
function getRaResult($raPtArray, $co) {	  
	GLOBAL $rcdMsg;
	$rcdNMsg = "";
	$co = substr($co, 0, 1);
	$resultArray = array();
	reset($raPtArray);
	while(list($ra1, $ra2Array)=each($raPtArray)){ 
		$catTotal = 0;
		$conTotal = 0;
		$noShTotal = 0;
		$sftRefTotal = 0;
		$hdRefTotal = 0;
		$prvdTotal = 0;
		$notWTotal = 0;
		$langTotal = 0;
		$otherTotal = 0;
		$unableTotal = 0;
		$notETotal = 0;
		reset($ra2Array);
		while(list($ra2, $ptArray)=each($ra2Array)){ 
			$total = 0;
			$consent= 0;
			$noShow = 0;
			$sftRefs = 0;
			$hdRefs = 0;
			$prvdOpt = 0;
			$notWell = 0;
			$language = 0;
			$other = 0;
			$unable = 0;
			$notEnroll = 0;
			reset($ptArray);
			while(list($key, $ptID)=each($ptArray)){
				//get bl result from recruitment table
				$sql="SELECT blResult FROM recruitment WHERE partID = ".$ptID." AND giveClipbd = '".$co."'";
				$results=runQuery($sql);
				if ($results["status"]>0) {
					if ($results["numRows"]>0) {
						switch ($results["returnedRows"][0]["blResult"]){
								case 1;
								$consent++;
								break;
								
								case 2;
								$noShow++;
								break;
								
								case 3;
								$sftRefs++;
								break;
								
								case 4;
								$hdRefs++;
								break;
								
								case 5;
								$prvdOpt++;
								break;
								
								case 6;
								$notWell++;
								break;
								
								case 7;
								$language++;
								break;
								
								case 8;
								$other++;
								break;
								
								case 9;
								$unable++;
								break;
								
								case 10;
								$notEnroll++;
								break;
						}
					}
				}else{
					$rcdNMsg =  "<font color=\"#FF0000\">Can not select record from provider table!! ".mysql_error()." </font><br>\n";
				}
			}
			// get the total of each bl ra1's bl result
			$total = $consent + $noShow + $sftRefs + $hdRefs + $prvdOpt + $notWell + $language + $other + $unable + $notEnroll;
			// total of each bl result cross bl ra2
			$conTotal = $conTotal + $consent;
			$noShTotal = $noShTotal + $noShow;
			$sftRefTotal = $sftRefTotal + $sftRefs;
			$hdRefTotal = $hdRefTotal + $hdRefs;
			$prvdTotal = $prvdTotal + $prvdOpt;
			$notWTotal = $notWTotal + $notWell;
			$langTotal = $langTotal + $language;
			$otherTotal = $otherTotal + $other;
			$unableTotal = $unableTotal + $unable;
			$notETotal = $notETotal + $notEnroll;
			// get the total for this bl ra1
			$catTotal = $catTotal + $total;
			$raResultArray[$ra2] = array($total, $consent, $noShow, $sftRefs, $hdRefs, $prvdOpt, $notWell, $language, $other, $unable, $notEnroll);
		}
			$raResult[$ra1][0] = array($catTotal, $conTotal, $noShTotal, $sftRefTotal, $hdRefTotal, $prvdTotal, $notWTotal, $langTotal, $otherTotal, $unableTotal, $notETotal);
			$raResult[$ra1][1] = $raResultArray;
			$raResultArray = array();
	}
	//print_r($raResult);
	return $raResult;
}

//FUNCTION: GET PT LIST BY RANDOMIZATION ARM
function getRandPtList($armArray) {	  
	GLOBAL $rcdMsg;
	$rcdNMsg = "";
	$ptArmArray = array();
	for ($a = 0; $a <count($armArray); $a++){
		//select pt ID from the part_info, appt and provider table based on randomization
		$sql="SELECT part_info.partID FROM part_info, appt, provider where part_info.MRN = appt.MRN AND provider.provID = appt.provdID AND randArm = '".$armArray[$a]."'";
		$results=runQuery($sql);
		if ($results["status"]>0) {
			if ($results["numRows"]>0) {
				for ($row=0; $row<$results["numRows"]; $row++) {
					if($armArray[$a] == "uc"){
						$temp = "Usual care";
					}elseif($armArray[$a] == "mats"){
						$temp = "Material Only";
					}else{
						$temp = "Coaching calls";
					}
					$ptArmArray[$temp][$row] = $results["returnedRows"][$row]["partID"];
				}
			}
		}else{
			$rcdNMsg =  "<font color=\"#FF0000\">Can not select record from provider table!! ".mysql_error()." </font><br>\n";
		}
	}
	return $ptArmArray;
}

//FUNCTION: GET TOTAL CONSENT FOR EACH ARM
function getTotalConst($armArray) {	  
	GLOBAL $rcdMsg;
	$rcdNMsg = "";
	$totalConstArray = array();
	if($heName) list($fname, $lname) = split('[" "]', $heName);
	for($a = 0; $a<count($armArray); $a++){
		//select he from the enrollment table
		$sql="SELECT COUNT(blResult) as consent FROM recruitment, part_info, provider, appt
				WHERE recruitment.partID = part_info.partID AND part_info.MRN = appt.MRN 
				AND appt.provdID = provider.provID AND provider.randArm = '".$armArray[$a]."'
				AND blResult = 1";
		$results=runQuery($sql);
		if ($results["status"]>0) {
			if ($results["numRows"]>0) {
				if($armArray[$a] == "uc"){
					$temp = "Usual care";
				}elseif($armArray[$a] == "mats"){
					$temp = "Material Only";
				}else{
					$temp = "Coaching calls";
				}
				$totalConstArray[$temp] = getTotalWithd($armArray[$a], $results["returnedRows"][0]["consent"]);
				if($armArray[$a] == "mats+cc"){
					$heArray = getHeList();
					reset($heArray);
					while(list($ID, $name)=each($heArray)){
						$totalConWth = getTotalHe($ID);
						$totalConstArray[$name] = getWthdBrkd($armArray[$a], $totalConWth[0], $totalConWth[1], $ID);
					}
				}
			}
		}else{
			$rcdNMsg =  "<font color=\"#FF0000\">Can not get total consent!! ".mysql_error()." </font><br>\n";
		}
	}
	//print_r($totalConstArray);
	return $totalConstArray;
}

//FUNCTION: GET TOTAL WITHDRAW FOR EACH ARM
function getTotalWithd($arm, $totalConst) {	  
	GLOBAL $rcdMsg;
	$rcdNMsg = "";
	$withDBrkArray = array();
	
	//select he from the enrollment table
	$sql="SELECT COUNT(withdID) as withD FROM enrollment, part_info, provider, appt
			WHERE enrollment.partID = part_info.partID AND part_info.MRN = appt.MRN 
			AND appt.provdID = provider.provID AND provider.randArm = '".$arm."'
			AND withdID BETWEEN 1 AND 8";
	$results=runQuery($sql);
	if ($results["status"]>0) {
		if ($results["numRows"]>0) {
			$withDBrkArray = getWthdBrkd($arm, $totalConst, $results["returnedRows"][0]["withD"]);
		}
	}else{
		$rcdNMsg =  "<font color=\"#FF0000\">Can not get total consent!! ".mysql_error()." </font><br>\n";
	}

	//print_r($withDBrkArray);
	return $withDBrkArray;
}

//FUNCTION: GET TOTAL WITHDRAW BREAKDOWN FOR EACH ARM
function getWthdBrkd($arm, $totalConst, $totalWithd, $heID) {	  
	GLOBAL $rcdMsg;
	$rcdNMsg = "";
	$withDArray = array();
	$withDArray[0] = $totalConst;
	$withDArray[1] = $totalWithd;
	for ($b = 1; $b<=8; $b++){
		if($heID == ""){
			//select he from the enrollment table
			$sql="SELECT COUNT(withdID) as withD FROM enrollment, part_info, provider, appt
					WHERE enrollment.partID = part_info.partID AND part_info.MRN = appt.MRN 
					AND appt.provdID = provider.provID AND provider.randArm = '".$arm."'
					AND withdID= ".$b."";
		}else{
			//select he from the enrollment table
			$sql="SELECT COUNT(withdID) as withD FROM enrollment
					WHERE enrollment.heID = ".$heID." AND withdID= ".$b."";
		}
		$results=runQuery($sql);
		//echo $sql."<br>";
		if ($results["status"]>0) {
			if ($results["numRows"]>0) {
				$withDArray[$b+1] = $results["returnedRows"][0]["withD"];
				
			}
		}else{
			$rcdNMsg =  "<font color=\"#FF0000\">Can not get total consent!! ".mysql_error()." </font><br>\n";
		}
	}
	//print_r($withDArray);
	return $withDArray;
}

//FUNCTION: GET HE LIST 
function getHeList() {	  
	GLOBAL $rcdMsg;
	$rcdNMsg = "";
	$heArray = array();
	
	//select provider name list from the provider table
	$sql="SELECT DISTINCT enrollment.heID, CONCAT(staffFName, \" \", staffLName) as staffName FROM enrollment, staff 
	      WHERE staff.staffID = enrollment.heID";
	$results=runQuery($sql);
	if ($results["status"]>0) {
		if ($results["numRows"]>0) {
			for ($row=0; $row<$results["numRows"]; $row++) {
				$heID = $results["returnedRows"][$row]["heID"];
				$heArray[$heID] = $results["returnedRows"][$row]["staffName"];
			}
		}
	}else{
		$rcdNMsg =  "<font color=\"#FF0000\">Can not select record from provider table!! ".mysql_error()." </font><br>\n";
	}
	
	return $heArray;
}

//FUNCTION: GET TOTAL CONSENT AND WITHDRAW FOR EACH HE
function getTotalHe($heID) {	  
	GLOBAL $rcdMsg;
	$rcdNMsg = "";
	$totalConWdArray = array();
	
	//select total consent for each he
	$sql="SELECT COUNT(heID) as consent FROM enrollment WHERE heID = ".$heID."";
	$results=runQuery($sql);
	if ($results["status"]>0) {
		if ($results["numRows"]>0) {
			$totalConst = $results["returnedRows"][0]["consent"];
		}
	}else{
		$rcdNMsg =  "<font color=\"#FF0000\">Can not get total consent!! ".mysql_error()." </font><br>\n";
	}

	// get total withdraw for each he
	$sql="SELECT COUNT(withdID) as withdraw FROM enrollment WHERE heID = ".$heID." AND withdID BETWEEN 1 AND 8";
	$results=runQuery($sql);
	if ($results["status"]>0) {
		if ($results["numRows"]>0) {
			$totalWithd = $results["returnedRows"][0]["withdraw"];
		}
	}else{
		$rcdNMsg =  "<font color=\"#FF0000\">Can not get total consent!! ".mysql_error()." </font><br>\n";
	}
	$totalConWdArray = array($totalConst, $totalWithd);
	return $totalConWdArray;
}

//FUNCTION: GET ALL COACHING CALL
function getAllCC($heID) {	  
	GLOBAL $rcdMsg;
	$rcdNMsg = "";
	$totalCC = 0;
	
	//select provider name list from the provider table
	$sql="SELECT COUNT(enrollment.partID) as total FROM part_info, appt, provider, enrollment 
	       WHERE part_info.MRN = appt.MRN AND appt.provdID = provider.provID AND part_info.partID = enrollment.partID 
		   AND part_info.ptStatus != 'I' AND heID = ".$heID." AND provider.randArm = 'mats+cc'";
	$results=runQuery($sql);
	if ($results["status"]>0) {
		if ($results["numRows"]>0) {
			$totalCC = $results["returnedRows"][0]["total"];
		}
	}else{
		$rcdNMsg =  "<font color=\"#FF0000\">Can not select total coaching call records!! ".mysql_error()." </font><br>\n";
	}
	
	return $totalCC;
}

//FUNCTION: GET COACHLING CALL NOT YET DUE, PDU AND OVERDUE PT LIST FROM PART_INFO, ENROLLMENT TABLE
function getDueCal11($heID, $dueDate1, $dueDate2){
	GLOBAL $rcdErr;
	$ptDueArray = array();
	$due = 0;
	$complete = 0;
	$unreach = 0; 
	$refuseThis =0;
	$refuseAll = 0;
	// if due date 1 = 0, get the not yet due pts
	if($dueDate1 == 0){
		$sql="SELECT part_info.partID FROM part_info, enrollment
		      WHERE part_info.partID = enrollment.partID AND enrollment.heID = ".$heID."
			  AND part_info.ptStatus != 'I' AND curdate() >enrollment. startDate 
			  AND curdate() < adddate(enrollment.startDate, '".$dueDate2."') 
			  ORDER BY enrollment.startDate ASC";
	// if due date2 = 0, then it is overdue
	}elseif($dueDate2 == 0){
		$sql="SELECT part_info.partID FROM part_info, enrollment
		      WHERE part_info.partID = enrollment.partID AND enrollment.heID = ".$heID."
			  AND part_info.ptStatus != 'I' AND curdate() >=  adddate(enrollment.startDate, '".$dueDate1."') 
			  ORDER BY enrollment.startDate ASC";
	}else{
		$sql="SELECT part_info.partID FROM part_info, enrollment 
		      WHERE part_info.partID = enrollment.partID AND enrollment.heID = ".$heID."
			  AND part_info.ptStatus != 'I' AND curdate() >=  adddate(enrollment.startDate, '".$dueDate1."') 
			  AND curdate() < adddate(enrollment.startDate, '".$dueDate2."') 
			  ORDER BY enrollment.startDate ASC";
	}
	$results=runQuery($sql);
	
	if ($results["status"]>0) {
		if ($results["numRows"]>0) {
			for ($row=0; $row<$results["numRows"]; $row++) {
				$conResult = getCallResult($results["returnedRows"][$row]["partID"], 1, 0);
				// echo "<br>".$results["returnedRows"][$row]["partID"]." result is ".$conResult."<br>";
				switch ($conResult){
					
					case 10;
					$complete++;
					break;
					
					case 11;
					$unreach++;
					break;
					
					case 12;
					$refuseThis++;
					break;
					
					case 13;
					$refuseAll++;
					break;
					
					default;
					$due++;
					break;
				}
			}
			$ptDueArray = array($due, $complete, $unreach, $refuseThis, $refuseAll);
		}else{
		    $ptDueArray = array(0, 0, 0, 0, 0);
		}
	}else{
		if ($rcdErr == ""){$rcdErr = "<font color=\"#FF0000\">No record has been returned for coaching call due</font>\n";}
	}
	//print_r($ptDueArray);
	//echo "<br>";
	
	return $ptDueArray;
}

//FUNCTION: GET LAST CONTACT RESULT FROM COUNS_CALLS TABLE
function getCallResult($partID, $contactID, $callFlg){
	GLOBAL $rcdErr;
	if($callFlg == 0){
		$sql="SELECT resultID FROM coach_call WHERE  partID = ".$partID." AND callID = ".$contactID." 
			  ORDER BY callNum DESC LIMIT 1";
	}else{
		$sql="SELECT resultID, callDate FROM coach_call WHERE  partID = ".$partID." AND callID = ".$contactID." 
			  ORDER BY callNum DESC LIMIT 1";
	}
	$results=runQuery($sql);
    if ($results["status"]>0) {
		if ($results["numRows"]>0) {
			if($callFlg == 0) return $results["returnedRows"][0]["resultID"];
			else return array(0=>$results["returnedRows"][0]["resultID"], 1=>$results["returnedRows"][0]["callDate"] );
		}else{
		    if($callFlg == 0) return 1;
			else return array();
		}
	}
}

//FUNCTION: GET CALL 1 RESULT FOR ALL PTS
function getPtCall1Result($heID, $callID){
	GLOBAL $rcdErr;
	$callNumArray = array();
	$ptRstArray = array();
	// first to get the last contact number
	$sql="SELECT coach_call.partID,  enrollment.startDate
		FROM part_info, coach_call, enrollment 
		WHERE  part_info.partID = coach_call.partID AND coach_call.partID = enrollment.partID 
		AND part_info.ptStatus != 'I' AND enrollment.heID = ".$heID." AND callID = ".$callID."
		GROUP BY coach_call.partID";
	$results=runQuery($sql);
    if ($results["status"]>0) {
		if ($results["numRows"]>0) {
			for ($row=0; $row<$results["numRows"]; $row++) {
				$temp = $results["returnedRows"][$row]["partID"];
				$startDate = formatDate($results["returnedRows"][$row]["startDate"], 0);
				$callArray[$temp] = $startDate;
			}
		}else{
		   if ($rcdErr == ""){$rcdErr = "<font color=\"#FF0000\">No record has been returned for last contact result</font>\n";}
		}
	}
	
	// then get the last contact result
	reset ($callArray);
	while(list($key, $val)=each($callArray)){
		$resultArray = getCallResult($key, $callID, 1);
		if($resultArray[0] != 13 && ($resultArray[0] == 10 || $resultArray[0] == 11 || $resultArray[0] == 12)){
			$callDate = formatDate($resultArray[1], 0);
			$ptRstArray[$key] = array($resultArray[0], $callDate, $val);
		}
	}
	//print_r($ptRstArray);
	return $ptRstArray;
}

//FUNCTION: GET BOTH CALL RESULT
function getBothCC($heID, $result){
	GLOBAL $rcdErr;
	
	if($result == 'completed'){
		$resultID = 10;
	}else{
		$resultID1 = 11;
		$resultID2 = 12;
	}
	if($resultID == 10){
		$sql="SELECT COUNT(c.partID) as totalResult FROM coach_call c, enrollment e WHERE  c.partID = e.partID AND
			 e.heID = ".$heID." AND c.resultID = ".$resultID."";
	}else{
		$sql="SELECT COUNT(c.partID) as totalResult FROM coach_call c, enrollment e WHERE  c.partID = e.partID AND
			 e.heID = ".$heID." AND (c.resultID = ".$resultID1." OR c.resultID = ".$resultID2.")";
	}
	$results=runQuery($sql);
    if ($results["status"]>0) {
		if ($results["numRows"]>0) {
			$totalComp = $results["returnedRows"][0]["totalResult"];
		}else{
		    if ($rcdErr == ""){$rcdErr = "<font color=\"#FF0000\">No record has been returned for total records</font>\n";}
		}
	}
	if($resultID == 10){
		$sql1="SELECT COUNT(DISTINCT c.partID) as partResult FROM coach_call c, enrollment e WHERE  c.partID = e.partID AND
			 e.heID = ".$heID." AND c.resultID = ".$resultID."";
	}else{
		$sql1="SELECT COUNT(DISTINCT c.partID) as partResult FROM coach_call c, enrollment e WHERE  c.partID = e.partID AND
			 e.heID = ".$heID." AND (c.resultID = ".$resultID1." OR c.resultID = ".$resultID2.")";
	}
	$results=runQuery($sql1);
    if ($results["status"]>0) {
		if ($results["numRows"]>0) {
			$partComp = $results["returnedRows"][0]["partResult"];
		}else{
		    if ($rcdErr == ""){$rcdErr = "<font color=\"#FF0000\">No record has been returned for non-duplicated record</font>\n";}
		}
	}
	
	$bothComp = $totalComp - $partComp;
	return $bothComp;
}

//FUNCTION: GET CALLS THAT HAVE ONLY CALL #1 COMPLETED
function getOnlyCall1Cmp($heID, $bothComp, $call2Unfinish){
	GLOBAL $rcdErr;
	// get the all call 1 completed
	$sql1="SELECT COUNT(c.partID) as call1Result FROM coach_call c, enrollment e WHERE  c.partID = e.partID AND
		 e.heID = ".$heID." AND c.resultID = 10 AND c.callID = 1";
	$results=runQuery($sql1);
    if ($results["status"]>0) {
		if ($results["numRows"]>0) {
			$call1Comp = $results["returnedRows"][0]["call1Result"];
		}else{
		    if ($rcdErr == ""){$rcdErr = "<font color=\"#FF0000\">No record has been returned for non-duplicated record</font>\n";}
		}
	}
	
	$compCC1 = $call1Comp - $bothComp - $call2Unfinish;
	return $compCC1;
}

//FUNCTION: GET CALLS THAT HAVE ONLY CALL #2 COMPLETED
function getOnlyCall2Cmp($heID, $bothComp){
	GLOBAL $rcdErr;
	// get the all call 1 completed
	$sql1="SELECT COUNT(c.partID) as call1Result FROM coach_call c, enrollment e WHERE  c.partID = e.partID AND
		 e.heID = ".$heID." AND c.resultID = 10 AND c.callID = 2";
	$results=runQuery($sql1);
    if ($results["status"]>0) {
		if ($results["numRows"]>0) {
			$call1Comp = $results["returnedRows"][0]["call1Result"];
		}else{
		    if ($rcdErr == ""){$rcdErr = "<font color=\"#FF0000\">No record has been returned for non-duplicated record</font>\n";}
		}
	}
	
	$compCC2 = $call1Comp - $bothComp;
	return $compCC2;
}

// VP added this function for modality reports
function VP_checkTotals($total, $val1, $val2) {
	if (($val1 + $val2) != $total) {
		return FALSE;
	} else {
		return TRUE;
	}
}

// VP added this function for modality reports
function VP_getPercentage($total, $val1) {
	return sprintf("%01.2f", ($val1/$total)); // percentage 
}

//FUNCTION: CHECK IF PARTID IS VALID
function checkPartID($partID) {	  
	GLOBAL $errIDMsg;
	$errIDMsg = "";
	
	if(strlen($partID)!= 5){
	    $errIDMsg = "<b>The studyID should be 5 digits</b>";
		return 1;
	}else{
		$result=ereg("^[0-9]+$",$partID, $reg);
		if (!($result)){
	        $errIDMsg= "<b>Please enter a valid studyID with all digits</b>";
			return 1;
		}else{
			if(substr($partID, 0, 1) == 0){
				$errIDMsg= "<b>The first digits for the study ID can not be 0</b>";
			return 1;
			}
		}
	}
	
	// check if the partID exist in the db
	$sql="SELECT * FROM enrollment WHERE partID = ".$partID."";
	$results=runQuery($sql);
	if ($results["status"]<=0){
		$errIDMsg =  "<font color=\"#FF0000\">Can not select start date in the enrollment table".mysql_error()."!! ".mysql_error()." </font><br>\n";
		return 1;
	}else{
		if($results["numRows"] ==0){
			$errIDMsg =  "<font color=\"#FF0000\">There is no enrollment info for this study ID </font>\n";
			return 1;
		}
	}
	return 0;
}


//FUNCTION: UPDATE START DATE - USED IN CHNAGE START DATE
function updateStartDate($partID, $startDate) {
					  
	GLOBAL $errIstMsg;
	$errIstMsg = "";
	//update part_info table
	$sql="UPDATE enrollment SET startDate = '".$startDate."' WHERE partID = ".$partID."";
	$results=runQuery($sql);
	if ($results["status"]<=0){
		$errIstMsg =  "<font color=\"#FF0000\">Can not update start date in the enrollment table".mysql_error()."!! ".mysql_error()." </font><br>\n";
		return 1;
	}
	
	return 0;
}

//FUNCTION: GTE LSIT OF PTS THAT CONSETED OR REFUSED FOR HD2 IN KENIM6 - USED IN DOWNLOAD_HD2_PTS
function getHd2Pts() {	  
	GLOBAL $errLstMsg;
	$errLstMsg = "";
	$ptArray = array();
	//get the pt list
	$sql="SELECT p.partID, p.ptFName, p.ptLName, p.dob, p.gender From part_info p LEFT JOIN enrollment e ON p.partID = e.partID 
		WHERE e.dateWithdrew != '0000-00-00' ORDER BY p.partID";
	$results=runQuery($sql);
	 if ($results["status"]>0) {
		if ($results["numRows"]>0) {
			for ($row=0; $row<$results["numRows"]; $row++) {
				$temp = $results["returnedRows"][$row]["partID"];
				$ptName = $results["returnedRows"][$row]["ptFName"]. " ".$results["returnedRows"][$row]["ptLName"];
				$dob = formatDate($results["returnedRows"][$row]["dob"], 0);
				$ptArray[$temp]=array($ptName, $dob, $gender);
			}
		}
	}else{
		$errLstMsg =  "<font color=\"#FF0000\">Can not populate data for HD2 pts list".mysql_error()."!! ".mysql_error()." </font><br>\n";
		return 1;
	}
	return $ptArray;
}
?>
