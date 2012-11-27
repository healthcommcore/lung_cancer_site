<?php
$limit = (60*60*10);
set_time_limit($limit);
$start = time();
$today = date('Y-m-d');
//--> Connection Info 
$checkfile = '/var/www/hvma/CadenceAppt.txt'; // data import file gets placed in folder via FTP
//$checkfile = '/var/www/hvma/test1.txt'; test for bug fixing
//$checkfile = $_SERVER["DOCUMENT_ROOT"].'/CadenceAppt.txt'; // test file 
if (!file_exists($checkfile)) { echo 'No data to import. '; exit; }
$new_records = file($checkfile); 

// add names to ignore to text file, as first;last
$ignorefile = '/var/www/lung_cancer_site/ptrack/cron/ignore-names.txt';
//$ignorefile = 'ignore-names.txt'; // test file

$ignoreNames = array();
if (file_exists($ignorefile)) { 
	$tmpIgnoreNames = file($ignorefile); // list of names to skip when importing, first;last
}
foreach ($tmpIgnoreNames as $ti) {
	$ignoreNames[] = trim($ti);
}

// Logs 
$importLogPath = '/var/www/hvma/import_complete/'; // live path 
//$importLogPath = $_SERVER["DOCUMENT_ROOT"].'/hvma/import_complete/'; // test path 
$resultLogsPath = '/var/www/logs/hd2/'; // LIVE 
//$resultLogsPath = $_SERVER["DOCUMENT_ROOT"].'/logs/hd2/'; // test path 

$dbh = mysql_connect('localhost', "lung_cancer_site", "439cwYY39ndB") or die ('Connection failed.'); // live and test 
mysql_select_db('lung_cancer_user') or die ('No such resource.');

//--> declare vars for loops
$bad_records = array();
$good_records = array();
$existing_records = array();
$existing_appts = array();
$existing_recruits = array();
// get all providers in the database 
$sql = "SELECT * FROM provider WHERE 1";
$result = mysql_query($sql);
$providers = array();
while ($row = mysql_fetch_assoc($result)) {
	$providers[] = $row;
}

//--> loop to sort good records from import vs. bad records (wrong number of vars)
foreach ($new_records as $rec) {
	//$rec = trim($rec);
	$list = explode("\t", $rec);
	if (count($list) != 20) { 
		$bad_records[] = trim($rec)."\twrong number of fields"; 
	} else {
	// has the correct number of elements, continue - check appt provider 
		// see if this record is part of the list of names to ignore.
		$ignoreCheck = $list[3].';'.$list[4]; // firstName;lastName return 
		$checkIgnore = skipBadNames($ignoreCheck, $ignoreNames);
		switch ( $checkIgnore ) {
		case 'include':
			// echo $ignoreCheck.' was not found in the array.<BR>';
			if (validProvider($list[4], $list[3]) !== FALSE) {
				// attach appt prov id to record 
				$provid = getProvID($list[4], $list[3], $providers); 
				$rec = str_replace("\n", '', $rec);
				$rec .= "\t".$provid; // array position 20
				//echo "<br>the new recored with providerID is ".$rec."<br>";
				$rec .= "\t".getProvReview($provid); // array position 21
				$good_records[] = $rec;
			} else {
				// not a valid appt provider for this study
				// check to see if we have them in our providers table. if not, add to bad list
				$existingProv = getProvID($list[4], $list[3], $providers);
				if ($existingProv == FALSE) {
					$bad_records[] = trim($rec)."\tunrecognized appt provider";
				}
			}
		break;
		
		case 'skip':
			// echo $ignoreCheck." was ignored during import.<BR>\n";
		break;
		}// end of ignore names switch 
	}
}// eo foreach newrecords

//--> loop thru records in the database and place in array 
$sql = 'SELECT * FROM part_info WHERE 1'; 
$result = mysql_query($sql);
while ($row = mysql_fetch_assoc($result)) {
	$existing_records[$row['MRN']] = $row;
}

//--> loop thru appts in the database and place in array 
$sql = 'SELECT * FROM appt WHERE 1'; 
$result = mysql_query($sql);
while ($row = mysql_fetch_assoc($result)) {
	$existing_appts[$row['MRN']] = $row;
}

//--> loop thru recruitments in the database and place in array 
$sql = 'SELECT * FROM recruitment WHERE 1'; 
$result = mysql_query($sql);
while ($row = mysql_fetch_assoc($result)) {
	$existing_recruits[$row['partID']] = $row;
}

//=======LOOP THRU NEW RECORDS=======
foreach ($good_records as $grec) {
	$grec = str_replace("\n", '', $grec);
	// echo $grec."<BR>\n";
	$new = explode("\t", $grec);
	// $new contains:
	// 0=MRN 1=ptFName 2=ptLName 3=provFName 4=provLName 5=apptTime 6=appType 7=apptWDay 8=hvmaNotes
	// 9=dob 10=apptDate 11=ptAddress1 12=ptAddress2 13=ptHPhone 14=ptWPhone 15=ptState 16=ptZip
	// 17=ptCity 18=gender 19=provID 20=apptProvID 21=apptProvReview
	$mrn = $new[0];
	//--> see if there is a matching MRN in exiting records 
	$match = array_key_exists($mrn, $existing_records);
	
	// there's a match in part_info
	if ($match == TRUE) { 
		$my_part_info = $existing_records[$mrn]; // this grabs the corresponding assoc array 
		// FIND MATCHING APPT AND RECRUITMENT RECORDS, IF ANY 
		$match2 = array_key_exists($mrn, $existing_appts); // checks for matching appt record 
		$match3 = array_key_exists($my_part_info['partID'], $existing_recruits); // checks for matching recruitment record 
		if ($match2 == TRUE) { $my_appt = $existing_appts[$mrn]; } else { $my_appt = FALSE; }
		if ($match3 == TRUE) { $my_recruit = $existing_recruits[$my_part_info['partID']];}else { $my_recruit = FALSE; 	}
		//--> compare new to existing, update array as needed 
		if ($match2 == TRUE) {
			// there's a matching appt record, continue 
			$replace = overwriteRec($new, $my_appt, $my_recruit);// chk new record against matching appt and recruitment record
			if ($replace == TRUE) {
			//echo 'replace = true > ';
				// check provider.review stored in $new[21] 
				$chkReview = strtoupper($new[21]);
				switch ($chkReview) {
					case 'Y':
						// echo 'review = y > ';
						// if provider.review == yes, check recruitment.datePCPnotify 
						if (is_array($my_recruit) && $my_recruit['datePCPnotify'] == '') { $chkPCPnotify = TRUE; } else { $chkPCPnotify = FALSE; }
						if (!is_array($my_recruit)) { $chkPCPnotify = TRUE; }
						// echo 'review = y, check recruitment.datePCPnotify > ';
						switch ($chkPCPnotify) {
							case TRUE:
							// echo 'pcp notify = true > ';
							//== date pcp notified = null, clear recruitment and update part and appt
							if ($match3 == TRUE) { 	// echo 'unset recruitment > '; 
								unset($existing_recruits[$my_part_info['partID']]); 
							}
							//==  update part_info ==
							// echo 'update part info > ';
							$existing_records[$mrn]['ptFName']    = $new[1]; 
							$existing_records[$mrn]['ptLName']    = makeStrUc($new[2]); 
							$existing_records[$mrn]['dob']        = formatDate($new[9]); 
							$existing_records[$mrn]['gender']     = $new[18];
							$existing_records[$mrn]['ptAddress1'] = $new[11];
							$existing_records[$mrn]['ptAddress2'] = $new[12];
							$existing_records[$mrn]['ptCity']     = $new[17];
							$existing_records[$mrn]['ptState']    = convertState2Abbrev($new[15]);
							$existing_records[$mrn]['ptZip']      = formatZip($new[16]);
							$existing_records[$mrn]['ptHPhone']   = $new[13];
							$existing_records[$mrn]['ptWPhone']   = $new[14];
							$existing_records[$mrn]['insertDate'] = $today;
							//== add/update appt ==
							// APPT REQUIRES MRN, `apptDate`, `apptTime`, `apptType`, `apptWDay`, 
							// `provdID`, `pcpID`, `hvmaNotes`, `insertDate
							// echo 'add/update appt > ';
							$existing_appts[$mrn] = array('MRN' => $mrn, 'apptDate' => formatDate($new[10]), 'appTime' => formatApptTime($new[5]), 'appType' => $new[6], 'apptWDay' => $new[7], 'provdID' => $new[20], 'pcpID' => $new[19], 'hvmaNotes' => $new['8'], 'insertDate' => $today);
							break;
							
							case FALSE:
							// echo 'pcp notify is false > ';
							// (datePCPnotify == blank) clear recruitment fields and use imported fields
							if ($match3 == TRUE) { // echo 'unset recruitment > '; 
								unset($existing_recruits[$my_part_info['partID']]); 
							}
							//==  update part_info ==
							// echo ' update part info > '; 
							$existing_records[$mrn]['ptFName']    = $new[1]; 
							$existing_records[$mrn]['ptLName']    = makeStrUc($new[2]); 
							$existing_records[$mrn]['dob']        = formatDate($new[9]); 
							$existing_records[$mrn]['gender']     = $new[18];
							$existing_records[$mrn]['ptAddress1'] = $new[11];
							$existing_records[$mrn]['ptAddress2'] = $new[12];
							$existing_records[$mrn]['ptCity']     = $new[17];
							$existing_records[$mrn]['ptState']    = convertState2Abbrev($new[15]);
							$existing_records[$mrn]['ptZip']      = $new[16];
							$existing_records[$mrn]['ptHPhone']   = $new[13];
							$existing_records[$mrn]['ptWPhone']   = $new[14];
							$existing_records[$mrn]['insertDate'] = $today;
							//== add/update appt ==
							// echo 'add/update appt > '; 
							$existing_appts[$mrn] = array('MRN' => $mrn, 'apptDate' => formatDate($new[10]), 'appTime' => formatApptTime($new[5]), 'appType' => $new[6], 'apptWDay' => $new[7], 'provdID' => $new[20], 'pcpID' => $new[19], 'hvmaNotes' => $new['8'], 'insertDate' => $today);
							break;
						}// eo notify switch 
					break;
					
					case 'N':
						//echo 'review = no > ';
						// if provider.review == no, clear recruitment fields and use imported fields 
						if ($match3 == TRUE) { echo 'unset recruitment > '; 
							unset($existing_recruits[$my_part_info['partID']]); 
						}
							//==  update part_info ==
							// echo 'update part info > '; 
							$existing_records[$mrn]['ptFName']    = $new[1]; 
							$existing_records[$mrn]['ptLName']    = makeStrUc($new[2]); 
							$existing_records[$mrn]['dob']        = formatDate($new[9]); 
							$existing_records[$mrn]['gender']     = $new[18];
							$existing_records[$mrn]['ptAddress1'] = $new[11];
							$existing_records[$mrn]['ptAddress2'] = $new[12];
							$existing_records[$mrn]['ptCity']     = $new[17];
							$existing_records[$mrn]['ptState']    = convertState2Abbrev($new[15]);
							$existing_records[$mrn]['ptZip']      = $new[16];
							$existing_records[$mrn]['ptHPhone']   = $new[13];
							$existing_records[$mrn]['ptWPhone']   = $new[14];
							$existing_records[$mrn]['insertDate'] = $today;
							//== add/update appt ==
							 echo 'add/update appt > ';
							$existing_appts[$mrn] = array('MRN' => $mrn, 'apptDate' => formatDate($new[10]), 'appTime' => formatApptTime($new[5]), 'appType' => $new[6], 'apptWDay' => $new[7], 'provdID' => $new[20], 'pcpID' => $new[19], 'hvmaNotes' => $new['8'], 'insertDate' => $today);
					break;
				}// eo review switch
				$existing_records[$mrn]['ptStatus'] = 'A'; // 
				// eo replace = true, update existing records 
			}else{
				//echo 'replace = false > '
			}
		} else {
			// echo 'no matching appt record > ';
			// if there's no appt for existing record, there should not be a recruitment record, but if there is one, remove it 
			if ($match3 == TRUE) {  //echo 'unset recruitment > '; 
				unset($existing_recruits[$my_part_info['partID']]); 
			}
			//==  update part_info ==
			// echo 'update part info > ';
			$existing_records[$mrn]['ptFName']    = $new[1]; 
			$existing_records[$mrn]['ptLName']    = makeStrUc($new[2]); 
			$existing_records[$mrn]['dob']        = formatDate($new[9]); 
			$existing_records[$mrn]['gender']     = $new[18];
			$existing_records[$mrn]['ptAddress1'] = $new[11];
			$existing_records[$mrn]['ptAddress2'] = $new[12];
			$existing_records[$mrn]['ptCity']     = $new[17];
			$existing_records[$mrn]['ptState']    = convertState2Abbrev($new[15]);
			$existing_records[$mrn]['ptZip']      = $new[16];
			$existing_records[$mrn]['ptHPhone']   = $new[13];
			$existing_records[$mrn]['ptWPhone']   = $new[14];
			$existing_records[$mrn]['insertDate'] = $today;
			//== add/update appt ==
			// echo 'add/update appt > ';
			$existing_appts[$mrn] = array('MRN' => $mrn, 'apptDate' => formatDate($new[10]), 'appTime' => formatApptTime($new[5]), 'appType' => $new[6], 'apptWDay' => $new[7], 'provdID' => $new[20], 'pcpID' => $new[19], 'hvmaNotes' => $new['8'], 'insertDate' => $today);
		}
	} else {
	// there is no match, add to existing records, appts 
	// echo 'no match > ';
		// part_info req's: partID MRN ptStatus('A') ptFName ptLName dob gender ptAddress1 ptAddress2 ptCity ptState ptZip ptHPhone ptWPhone ptCPhone ptOPhone ptPPhone ptRPhone ptEmail  notes insertDate
		// echo 'add part info > ';
		$existing_records[$mrn] = array(NULL, $new[0], 'A', $new[1], makeStrUc($new[2]), formatDate($new[9]), $new[18], $new[11], $new[12], $new[17], convertState2Abbrev($new[15]), $new[16], $new[13], $new[14], NULL, NULL, NULL, NULL, NULL, NULL, $today);
		// appt req's MRN, `apptDate`, `apptTime`, `apptType`, `apptWDay`, `provdID`, `pcpID`, `hvmaNotes`, `insertDate
		// echo 'add appt info > ';
		$existing_appts[$mrn] = array($new[0], formatDate($new[10]), formatApptTime($new[5]), $new[6], $new[7], $new[20], $new[19], $new['8'], $today);
		// recruitment - no record to enter
	}
}// eo looping new records 

//--> create temp tables
//----> Create Temp Part_Info Table
$sql1 = 'DROP TABLE IF NOT EXITS `tmppartinfo`;';
$sql2 = "CREATE TABLE IF NOT EXISTS `tmppartinfo` (
  `partID` mediumint(5) unsigned NOT NULL auto_increment,
  `MRN` varchar(12) default NULL,
  `ptStatus` char(1) default 'A',
  `ptFName` varchar(30) default NULL,
  `ptLName` varchar(30) default NULL,
  `dob` date default NULL,
  `gender` char(1) default NULL,
  `ptAddress1` varchar(50) default NULL,
  `ptAddress2` varchar(50) default NULL,
  `ptCity` varchar(30) default NULL,
  `ptState` varchar(20) default NULL,
  `ptZip` varchar(5) default NULL,
  `ptHPhone` varchar(20) default NULL,
  `ptWPhone` varchar(20) default NULL,
  `ptCPhone` varchar(20) default NULL,
  `ptOPhone` varchar(20) default NULL,
  `ptPPhone` varchar(20) default NULL,
  `ptRPhone` varchar(20) default NULL,
  `ptEmail` varchar(40) default NULL,
  `notes` text,
  `insertDate` date default NULL,
  PRIMARY KEY  (`partID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=50000;"; // added auto increment for first import 10000
$result = mysql_query($sql1);
$result = mysql_query($sql2);
//----> Temp Appt Table
$sql3 = 'DROP TABLE IF EXISTS `tmpappt`;';
$sql4 = "CREATE TABLE IF NOT EXISTS `tmpappt` (
  `MRN` varchar(12) NOT NULL,
  `apptDate` date default '0000-00-00',
  `apptTime` time default '00:00:00',
  `apptType` varchar(10) default NULL,
  `apptWDay` varchar(15) default NULL,
  `provdID` varchar(20) default NULL,
  `pcpID` varchar(20) default NULL,
  `hvmaNotes` text,
  `insertDate` date default NULL,
  PRIMARY KEY  (`MRN`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;";
$result = mysql_query($sql3);
$result = mysql_query($sql4);
//----> Temp Recruitment Table
$sql5 = 'DROP TABLE IF EXISTS `tmprecruitment`;';
$sql6 = "CREATE TABLE IF NOT EXISTS `tmprecruitment` (
  `partID` mediumint(5) unsigned NOT NULL,
  `datePCPnotify` date default '0000-00-00',
  `pcpOptOut` char(8) NOT NULL,
  `dateRecrutLttr` date default '0000-00-00',
  `dateReceived` date default '0000-00-00',
  `ptOptOut` varchar(8) NOT NULL,
  `giveClipbd` char(1) default NULL,
  `blResult` smallint(2) default NULL,
  `raID1` smallint(5) default NULL,
  `raID2` smallint(5) default NULL,
  PRIMARY KEY  (`partID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;";
$result = mysql_query($sql5);
$result = mysql_query($sql6);

// echo '<BR><BR>';
// LOOP UPDATED ARRAYS, INSERT VALUES INTO TABLES
foreach ($existing_records as $mrn => $er) {
	$er = cleanArray($er);
	$sql = 'INSERT INTO tmppartinfo (partID, MRN, ptStatus, ptFName, ptLName, dob, gender, ptAddress1, ptAddress2, ptCity, ptState,  ptZip, ptHPhone, ptWPhone, ptCPhone, ptOPhone, ptPPhone, ptRPhone, ptEmail, notes, insertDate) values ("';
	$sql .= implode('", "', $er);
	$sql .='");';
	$ins = mysql_query($sql) or die ('Part info: '.mysql_error());
}// eo updated part_info records 
foreach ($existing_appts as $mrn => $ap) {
	$ap = cleanArray($ap);
	$sql = 'INSERT INTO tmpappt (MRN, apptDate, apptTime, apptType, apptWDay, provdID, pcpID, hvmaNotes, insertDate) VALUES ("';
	$sql .= implode('", "', $ap);
	$sql .= '");';
	$ins = mysql_query($sql) or die ('Appt: '.mysql_error());
}//eo updated appt records 
foreach ($existing_recruits as $partID => $rc) {
	$rc = cleanArray($rc);
	$sql = 'INSERT INTO tmprecruitment (partID, datePCPnotify, pcpOptOut, dateRecrutLttr, dateReceived, ptOptOut, giveClipbd, blResult, raID1, raID2) VALUES ("';
	$sql .= implode('", "', $rc);
	$sql .= '");';
	$ins = mysql_query($sql) or die ('Recruitment: '.mysql_error());
}//eo updated recruitment records

// get all participant ids that don't have a matching recruitment record
$newParts = mysql_query("SELECT tmppartinfo.partID FROM tmppartinfo LEFT JOIN tmprecruitment ON tmppartinfo.partID = tmprecruitment.partID
WHERE tmprecruitment.partID IS NULL;") or die ('Select Null Recruits: '.mysql_error());
$totalRecruits = count($existing_recruits) + mysql_num_rows($newParts);
while ($row = mysql_fetch_assoc($newParts)) {
	$sql = "INSERT INTO tmprecruitment (partID) VALUES ('".$row['partID']."')";
	$ins = mysql_query($sql) or die ('New Recruitment: '.mysql_error());
}

//--> do data check on new tables 
$chkTmpPartInfo = 'SELECT COUNT(*) AS total FROM tmppartinfo';
$chkTmpAppt = 'SELECT COUNT(*) AS total FROM tmpappt';
$chkTmpRecuit = 'SELECT COUNT(*) AS total FROM tmprecruitment';
$tmp1 = mysql_query($chkTmpPartInfo) or die ('tmp part_info failed: '.mysql_error());
$chk1 = mysql_fetch_assoc($tmp1);
$tmp2 = mysql_query($chkTmpAppt) or die ('tmp appt failed: '.mysql_error());
$chk2 = mysql_fetch_assoc($tmp2);
$tmp3 = mysql_query($chkTmpRecuit) or die ('tmp recruitment failed: '.mysql_error());
$chk3 = mysql_fetch_assoc($tmp3);
$tbl_errs = array();
//--> if data passes, replace old tables w/ new ones 
if ($chk1['total'] != count($existing_records)) { $tbl_errs[] = 'Part Info Table Error. '; }
if ($chk2['total'] != count($existing_appts)) { $tbl_errs[] = 'Appt Table Error. '; }
if ($chk3['total'] != $totalRecruits) { $tbl_errs[] = 'Recruitment Table Error. '; }
if (count($tbl_errs) < 1) {
	// drop original tables 
	$drop1 = mysql_query('DROP TABLE IF EXISTS part_info');
	$drop2 = mysql_query('DROP TABLE IF EXISTS appt');
	$drop3 = mysql_query('DROP TABLE IF EXISTS recruitment');
	// rename temp tables 
	$changed = array();
	$rename1 = 'RENAME TABLE `tmppartinfo` TO `part_info` ';
	$result = mysql_query($rename1) or die (mysql_error());
	if ($result) { $changed[] = 'part_info'; }
	$rename2 = 'RENAME TABLE `tmpappt` TO `appt` ';
	$result = mysql_query($rename2) or die (mysql_error());
	if ($result) { $changed[] = 'appt'; }
	$rename3 = 'RENAME TABLE `tmprecruitment` TO `recruitment` ';
	$result = mysql_query($rename3) or die (mysql_error());
	if ($result) { $changed[] = 'recruitment'; }
}

//--> log what happened to file in append mode 
$upd_msg = '';
	if (count($tbl_errs) < 1) {
		$upd_msg .= date('m-d-Y').": Successfully created temp tables. ";
		if (count($changed) == 3) {
			$upd_msg .= "All tables replaced successfully. ";
		} else {
			foreach ($changed as $c) {
				$upd_msg .= $c." was replaced. ";
			}
		}
	} else {
		$upd_msg .= "Temp tables failed: ";
		foreach ($tbl_errs as $err) {
			$upd_msg .= $err;
		}
	}
	$upd_msg .= "Total records in each table: ";
	$upd_msg .= 'part_info ('.$chk1['total'].'), appt ('.$chk2['total'].'), recruitment('.$chk3['total'].")\n";
	$fp = fopen($resultLogsPath.'update_log.txt', 'a'); // append bad records 
	fwrite($fp, $upd_msg);
	fclose($fp);

//--> if there are bad records - add to log - email msg to person responsible for maintenance - requires manual check/entry...
if (count($bad_records) > 0) {
	$sendto1 = 'Qi_Wang@dfci.harvard.edu'; // when Qi returns
	$badlog = $resultLogsPath.'bad_records.txt'; // file that we'll write to for bad records 
	$bad_msg = date('m-d-Y')." Data Import Results\n"; // start error message
	foreach ($bad_records as $rec) {
		$bad_msg .= $rec."\n";
	}
	$bad_msg .= "\n\n";
	$fp = fopen($badlog, 'a'); // append bad records 
	fwrite($fp, $bad_msg);
	fclose($fp);
	$em_msg = date('d-m-Y').': Check the HD2 sub-study log on the server ('.$badlog.'). '.count($bad_records).' records were excluded from the data import.';
	mail($sendto1, 'Data Import Errors', $em_msg);
	echo "Data Import Errors mailed to: $sendto1 <BR>\n";
	echo '<PRE>'.$em_msg.'</PRE>';
}

mysql_close($dbh);

// add code to rename import file 
$filedate = date('m-d-Y_his'); // grab date and time to append to filename
$startname = strrpos($checkfile, '/');
$endname = strpos($checkfile, '.txt');
$namelen = $endname - $startname;
$justname = substr($checkfile, $startname, $namelen);
$filerenamed = $importLogPath.$justname.'_'.$filedate.'.txt';
rename($checkfile, $filerenamed); // rename import file 

$end = time();

echo "<BR><BR>\n\n";
$seconds = $end - $start;
$total = ($end - $start) / 60;
echo "Time in seconds: $seconds<BR>";
echo "Time in minutes: $total<BR>";

//============= FUNCS ================
function checkName($str) {
	// names accept letters, numbers, dashes, single apostophes and white space
	return ( ! preg_match("/^([-a-z0-9\-\'\s])+$/i", $str)) ? FALSE : TRUE;
}

function formatZip($str) {
	// return first 5 numbers only 
	$str = trim($str);
	if (strpos($str, '-') != false) {
		$end = strpos($str, '-');
		$end = $end;
		$ret = substr($str, 0, $end);
	} else {
		$ret = $str;
	}
	while (strlen($ret) < 5) {
		$ret = '0'.$ret;
	}
	return $ret;
}

function formatApptTime($str) {
	list($hr, $min) = explode(':', $str);
	$pm = array(1, 2, 3, 4, 5, 6); // these values are pm 
	if ( in_array($hr, $pm) ) {
		$hr = $hr + 12;
	} 
	$str = $hr.':'.$min;
	if (strpos($str, ':') != 2) {
		$str = '0'.$str;
	}
	return $str.':00';
}

function formatDate($str) {
	$str = trim($str);
	// comes in as 12/31/1977
	list($mo, $day, $yr) = explode('/', $str);
	// leaves as yyyy-mm-dd
	return $yr.'-'.$mo.'-'.$day;
}

function matchByMrn($str, $ourdb) {
	if (in_array($str, $ourdb)) { return true; } else { return false; }
}

// check to see if the appt provider is in our list
function validProvider($l, $f) {
$l = trim($l);
$f = trim($f);
if (strlen($f) > 2) { 
	$f = substr($f, 0, 2); // grab first two letters
}
/* 
NOTE: all providers must be in our providers table. This providers array contains only those  appointment providers (not pcp's) we're including in the study. Array: last => first  to a max of 11 chars.
*/
$providers = array(
	'BAUMWOLL' => 'RO', 
	'COHEN' => 'CA',
	'COWAN' => 'LA',
	'GARLAND' => 'JA',
	'KASUBA' => 'DA',
	'ROSETO' => 'JA',
	'SRIDHAR' => 'SH',
	'SVENSON' => 'JO'
);
	$f = strtoupper($f);
	$l = strtoupper($l);
	$ret = FALSE; // default value 
	if ($f != '') {
		if (array_key_exists($l, $providers) && strpos($providers[$l], $f) !== FALSE) {
			$ret = TRUE;
		}
	} else {
		if (array_key_exists($l, $providers)) {
			$ret = TRUE;
		}
	}
	return $ret;
}// eo function 

// gets provider ID from database for appt provider -  pcp id is sent in data import 
function getProvID($l, $f, $providers) {
	$f = trim(strtoupper($f));
	if (strpos($f, ' ') !== FALSE) {
		$end = strpos($f, ' ');
		$f = substr($f, 0, $end); //  strips off title 
	}
	$l = trim(strtoupper($l));
	$ret = FALSE;
	foreach ($providers as $p) {
		$upperfname = trim(strtoupper($p['provFName']));
		$upperlname = trim(strtoupper($p['provLName']));
		if ($f != '') {
			
			if ($upperlname == $l && strpos($upperfname, $f) !== FALSE) {
				$ret = $p['provID'];
				break;
			}
		} else {
			if ($upperlname == $l) {
				$ret = $p['provID'];
				break;
			}
		}
	}
	return $ret;
}// eo function 

function getProvReview($id) {
	GLOBAL $dbh;
	$sql = "SELECT review FROM provider WHERE provID = '$id'";
	$result = mysql_query($sql, $dbh) or die ('query failed: '.mysql_error());
	$row = mysql_fetch_assoc($result);
	return $row['review'];
}// eo function 

function getProvNotify($id) {
	GLOBAL $dbh;
	$sql = "SELECT datePCPnotify FROM recruitment WHERE partID = $id";
	$result = mysql_query($sql, $dbh) or die ('query failed: '.mysql_error());
	if (mysql_num_rows($result) > 0) {
		$row = mysql_fetch_assoc($result);
		return $row['datePCPnotify'];
	} else {
		return '';
	}
}// eo function 

function overwriteRec($new, $appt, $recruit) {
	$ret = TRUE; // by default, we want to overwrite 
	// $new = new record
	// $appt = old appt record
	// $recruit = old recruitment record, MAY BE FALSE 
	//-> 1 - if appt date, time and provider are the same, ignore new record
	$newApptDate = formatDate($new[10]);
	$newApptTime = formatApptTime($new[5]);
	if (formatDate($new[10]) == $appt['apptDate'] && formatApptTime($new[5]) == $appt['apptTime'] && $new[20] == $appt['provdID']) {
		return FALSE;
	}
	if ($recruit != FALSE) {
		//-> 2 - check baseline result 
		//---> if recruit.blResult = (1, 5, 7, 8, 4) ignore new record
		$myb = array(1, 5, 7, 8, 4);
		if (in_array($recruit['blResult'], $myb)) {
			return FALSE;
		}
		//-> 3 - check patient opt-out
		//---> if not blank, ignore new record
		if ($recruit['pcpOptOut'] != '') {
			return FALSE;
		}
		//-> 4 - check pcp opt out
		//---> if pcp opt out not blank, ignore
		if ($recruit['ptOptOut'] != '') {
			return FALSE;
		}
	}
	return $ret;
}// eo function

function cleanArray($ar) {
	$ret = array();
	foreach ($ar as $x) {
		$tmp = str_replace('\\', '\\\\', $x);
		$tmp = str_replace('"', '\"', $tmp);
		$ret[] = $tmp;
	}
	return $ret;
}

// temporary function to grab uc providers only
// check to see if the appt provider is in our list
function ucProvider($l, $f) {
$l = trim($l);
$f = trim($f);
if (strlen($f) > 2) { 
	$f = substr($f, 0, 2); // grab first two letters
}
/* 
NOTE: all providers must be in our providers table. This providers array contains only uc appt providers we're including in the study. 
// Array: last => first = first 2 chars 
*/
$providers = array(
	'DONNELLY' => 'BA',
	'FOXWORTHY' => 'DO',
	'MINKINA' => 'NA',
	'PENNOYER' => 'HI',
	'ROSETO' => 'JA',
	'SIWIEC' => 'SH',
	'SULLIVAN' => 'AN',
	'TAPP' => 'SH',
	'THARAYIL' => 'MA',
	'TREMBLAY' => 'LA',
	'ZUCKERMAN' => 'CA'
);
	$f = trim(strtoupper($f));
	$l = trim(strtoupper($l));
	$ret = FALSE; // default value 
	if ($f != '') {
		if (array_key_exists($l, $providers) && strpos($providers[$l], $f) !== FALSE) {
			$ret = TRUE;
		}
	} else {
		if (array_key_exists($l, $providers)) {
			$ret = TRUE;
		}
	}
	return $ret;
}// eo function 

function skipBadNames($str, $arr) {
	if ( in_array($str, $arr) ) {
		return 'skip';
	} else {
		return 'include';
	}
}

// used to convert the state string to the state abbreviation
function convertState2Abbrev($str) {
	$str = trim($str);
	if (strlen($str) > 2) {
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
		'Washington D.C.' => 'dc',  
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
	$result=ereg("-",$str, $reg);
    if (!($result)){
		$str = ucwords(strtolower($str));
	}else{
		list($firstPart, $lastPart) =  split('[-]', $str);
		$firstPart = ucwords(strtolower($firstPart));
		$lastPart = ucwords(strtolower($lastPart));
		$str = $firstPart."-".$lastPart;
		//echo "<br>the formated str is ".$str;
	}
	return $str;
}
?>
