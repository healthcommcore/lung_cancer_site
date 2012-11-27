<?php
// download_fu_report.php
// created by VP
// PURPOSE: generates a list of participants within a date range specified by the user 
// everyone who was ever consented 
// Upon completion of creating file for download, update  
// before inclusion. 
include("includes/connection.php");
include("includes/report_function.php");
define ( "JPATH_SITE", getcwd() . '/../');
require("includes/survey.php");
// check for valid start date and end date
$errors = array();
if ( !isset($_POST['startdate']) || $_POST['startdate'] == '') { $errors[] = 'msg1=true'; }
if ( !isset($_POST['enddate']) || $_POST['enddate'] == '' ) { $errors[] = 'msg2=true'; }
if ( isset($_POST['startdate']) && $_POST['startdate'] != '' && checkValidDate($_POST['startdate']) == 1 ) { $errors[] = 'msg3=true'; }
if ( isset($_POST['enddate']) && $_POST['enddate'] != '' && checkValidDate($_POST['enddate']) == 1 ) { $errors[] = 'msg4=true'; }
if ( count($errors) > 0) {
	$emsg = implode('&', $errors);
	$emsg .= '&startdate='.$_POST['startdate'].'&enddate='.$_POST['enddate'];
	header('location: get_fu_report.php?'.$emsg);
	exit;
}

// data entered is valid, proceed

// CONNECT TO DATABASE
if ($mysqlID=dbConnect()) {
    selectDatabase("lung_cancer_user");
	
// constants:
$firstdate = str_replace('/', '-', $_REQUEST['startdate']);
$lastdate = str_replace('/', '-', $_REQUEST['enddate']);

$dbdate1 = datetoDB($_REQUEST['startdate']);
$dbdate2 = datetoDB($_REQUEST['enddate']);
define('MY_FILENAME', 'follow_up_list_'.$firstdate.'_to_'.$lastdate.'.xls');

// query db, grab partID for baseline result = consented 
$sql = "SELECT r.partID FROM recruitment r INNER JOIN enrollment e ON r.partID = e.partID WHERE r.blResult = '1' AND e.startDate >= '$dbdate1' AND e.startdate <= '$dbdate2' AND e.dateUMASS = '0000-00-00'";

// generate a list 
$result = mysql_query($sql, $mysqlID);
$total = mysql_num_rows($result);

	if ($total > 0) {
	
		// get all providers and save in array for later use
		$providers = array();
		$getprov = "SELECT provID as id, provFName as first_name, provLName as last_name, randArm as arm FROM provider";
		$providerresult = mysql_query($getprov, $mysqlID);
		while ($row = mysql_fetch_assoc($providerresult)) {
			$providers[$row['id']] = array('first_name' => $row['first_name'], 'last_name' => $row['last_name'], 'arm' => $row['arm']);
		}
	
		$records = "Study ID \t First Name \t Last Name \t DOB \t Gender \t Address 1 \t Address 2 \t City \t State \t Zip \t Home Phone \t Work Phone \t Cell Phone \t Other Phone \t Preferred Phone \t Alt First Name \t Alt Last Name \t Alt Address \t Alt City \t Alt State \t Alt Zip \t Alt Phone \t Start Date \t Withdrawal Date \t Due Date \t Study Arm \t Ix Modality \t Date Ix modality Switched \t Reminder Randomization \t Reminder Modality \t Date reminder switched \t Date reminder opt-out \t Appt provider first name \t Appt provider last name \t Date TFR #1 mailed \t Complete_BL_survey_data \t Appt provider ID \t Notes \t Email \t PA_value \t FV_value \t RM_value \t MV_value \t SM_value \t Score \t Calculate Fields for TFR #2 \n";
		
		// process all part ids 
		$today = date('Y-m-d'); // date outside loop - just one date 
		while ( $row = mysql_fetch_row($result) ) {
			// make these arrays blank for inside the loop 
			$part = array();
			$alt = array();
			$prov = array();
			$TFRvalues = array();
			// grab the entire record and create a CSV file 
			$getme = 'SELECT p.partID as studyID, p.ptFName as first_name, p.ptLName as last_name, p.dob as dob, p.gender as gender, p.ptAddress1 as address1, p.ptAddress2 as address2, p.ptCity as city, p.ptState as state, p.ptZip as zip, 
			p.ptHPhone as phone_home, p.ptWPhone as phone_work, p.ptOPhone as phone_other, p.ptCPhone as phone_cell, p.ptPPhone as phone_preferred, e.startDate as start_date, e.dateWithdrew as withdrawal_date, 
			e.ixModality as ix_modality, e.dateIXChange as ix_date_changed, e.remindRand as reminder_randomization, e.reminModality as reminder_modality, e.dateRemChang as reminder_date_changed, e.dateRemOpt as reminder_optout_date, 
			t.dateMailed as date_tfr, r.blResult as baseline_result, v.provdID as provider_id, p.notes as notes, p.email as email
			FROM part_info p INNER JOIN enrollment e ON p.partID = e.partID INNER JOIN recruitment r ON e.partID = r.partID INNER JOIN appt v on p.mrn = v.mrn LEFT JOIN TFR_info t on p.partID = t.partID WHERE p.partID = '.$row[0];
			
			$res1 = mysql_query($getme, $mysqlID);
			$part = mysql_fetch_array($res1);
			$duedate = get6Mos($part['start_date']);
			// make separte query to grab alternate contact data 
			$getalt = 'SELECT * FROM alt_contact WHERE partID = '.$row[0];
			$res2 = mysql_query($getalt, $mysqlID);
			if (mysql_num_rows($res2) > 0) {
				$alt = mysql_fetch_array($res2);
			}
		
			//Qi add this part in 8/5/2009 to call Therese's function in order to get TFR info
			$status = getSurveyUserDataTFR2($row[0], $TFRvalues);
	
			// enter todays date for dateUMASS field in enrollment table 
			$putdate = "UPDATE enrollment SET dateUMASS = '$today' WHERE partID = ".$row[0];
			//mysql_query($putdate, $mysqlID);
			
			// check the TFR return status
			if($status){
				// if only one TFR
				foreach ($TFRvalues as $key=>$val){
					$part['last_name'] = str_replace('\\', '', $part['last_name']);
					// add row to $records for display in Excel
					$records .= $part['studyID']."\t".ucwords(strtolower($part['first_name']))."\t".ucwords(strtolower(stripslashes($part['last_name'])))."\t".displayDate($part['dob'])."\t".$part['gender']."\t".ucwords(strtolower($part['address1']))."\t".ucwords(strtolower($part['address2']))."\t".ucwords(strtolower($part['city']))."\t".convertState2Abbrev($part['state'])."\t. ".$part['zip']."\t".$part['phone_home']."\t".$part['phone_work']."\t".$part['phone_cell']."\t".$part['phone_other']."\t".$part['phone_preferred']."\t";
					
					// check for alternate contact 
					if (count($alt) > 0) {
						$records .= $alt['conFName']."\t".$alt['conLName']."\t".$alt['conAddress']."\t".$alt['conCity']."\t".$alt['conState']."\t. ".$alt['conZip']."\t ".$alt['conPhone']."\t";
					} else {
						// there is no altnerate contact - pad fields 
						$records .= "\t\t\t\t\t\t\t";
					}
					
					$records .= displayDate($part['start_date'])."\t".displayDate($part['withdrawal_date'])."\t".$duedate."\t".$providers[$part['provider_id']]['arm']."\t".$part['ix_modality']."\t".displayDate($part['date_ix_changed'])."\t".formatYesNo($part['reminder_randomization'])."\t".formatReminderModality($part['reminder_modality'])."\t".displayDate($part['date_reminder_changed'])."\t".displayDate($part['reminder_optout_date'])."\t".$providers[$part['provider_id']]['first_name']."\t".$providers[$part['provider_id']]['last_name']."\t".displayDate($part["date_tfr"])."\t".formatBaselineResult($part['baseline_result'])."\t".$part['provider_id']."\t".modifyNotes($part['notes'])."\t";
					// add TFR value
					$records = $records.$val;
				}
			}else{
				echo "There is a db error with the survey.";
			}
		
		} // while row 
		
		// generate report in Excel 
		header("Content-disposition: attachment;filename=".MY_FILENAME); 
		header("Content-type: application/vnd.ms-excel"); 
		header("Pragma: no-cache"); 
		header("Expires: 0"); 
		echo $records;

	} else {
		echo 'Sorry, no records match your request dates.';
	}

} else {
	echo 'No database connection available.';
}

// ====================================================
function displayDate($str) {
	if ($str == '' || $str == '0000-00-00') { $ret = ''; } else {
		list($yr, $mo, $day) = explode('-', $str);
		$ret = $mo.'/'.$day.'/'.$yr;
	}
	return $ret;
}

function formatYesNo($str) {
	if ($str == '1') { return 'Yes'; } else { return 'No'; }
}

function get6Mos($str) {
	$yr = "";
	$mo = "";
	$day = "";
	// str comes in as yyyy-mm-dd
	list ($yr, $mo, $day) = explode('-', trim($str));
	$time = mktime(1, 0, 0, $mo, $day, $yr);
	$sixMos = $time + (60 * 60 * 24 * 180);
	$newDate = date('m/d/Y', $sixMos);
	return $newDate;
}

function formatIxModality($str) {
	switch ($str) {
		case 1:
		return 'Web';
		break;
		
		case 2:
		return 'Print';
		break;
		
		default:
		return '';
	}
}

function formatReminderModality($str) {
	if($str == 0){ 
		$str = '';
	}
	return $str;
}

function modifyNotes($notes) {
	$notes = str_replace("\n", '', $notes);
	$notes = str_replace("\t", '', $notes);
	$notes = str_replace("\r", '', $notes);
	return stripslashes($notes);
}

function formatBaselineResult($str) {
	switch ($str) {
		case 1:
		return 'Consented';
		break;
		
		case 2:
		return 'No-show';
		break;
		
		case 3:
		return 'Soft refusal';
		break;
		
		case 4:
		return 'Hard refusal';
		break;
		
		case 5:
		return 'Provider opt-out day-of-appt';
		break;
		
		case 6:
		return 'Ineligible: Not well';
		break;
		
		case 7:
		return 'Ineligible: Language';
		break;
		
		case 8:
		return 'Ineligible: Other';
		break;
		
		case 9:
		return 'Unable to approach';
		break;
		
		case 10:
		return 'Not enroll - other';
		break;
		
		default:
		return '';
	}
}
?>