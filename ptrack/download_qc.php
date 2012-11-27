<?php
// download_qc_report.php
// created by Vikki P
// PURPOSE: quality control reports 
// AVAILABLE TO : Molly, Ruth and the RAs (admin and RA) 
// 2 report types
// web user report: pulls all web users with today as start date, sort by baseline result 
// returns study ids, baseline results 
// baseline report: pulls a list of consented, refused and unable to approach for appt date range 
// returns study ids, baseline results and 3 totals: soft/hard refusal, unable to approach, consented 

// check for all required fields
if ( isset($_POST['mode']) ) { 
	if ($_POST['mode'] == 'webuser') {
		$reqd = array('mode');
	} elseif ($_POST['mode'] == 'blresult') {
		$reqd = array('mode', 'startdate', 'enddate');
	} else {
		$err = urlencode('Invalid request. Please try again.');
		header('location: get_qc_report.php?err='.$err);
		exit;
	}
} else {
	header('location: get_qc_report.php');
}

foreach ($_POST as $var => $val) {
	if (in_array($var, $reqd) && $val == '') {
		$err = urlencode('Missing required values. Please complete the form and then submit.');
		header('location: get_qc_report.php?err='.$err);
		exit;
	}
}

include("includes/connection.php");

//if successfule, CONNECT TO DATABASE
if ($mysqlID=dbConnect()) {
    selectDatabase("lung_cancer_user");
}

// main switch - determines if it's a web report or a baseline result report
$mode = trim($_POST['mode']);

switch ($mode) {
	case 'webuser':
		$reportname = 'HD2 Web Participants with start date = '.date('m-d-Y');
		$headings = "Study ID\tBaseline Result\n";
		$today = date('Y-m-d');
		$displayDate = date('m-d-Y');
		define('MY_FILENAME', "qc-webuser_".$displayDate.'.xls');
		$sql = "SELECT p.partID, r.blResult FROM part_info p 
		INNER JOIN recruitment r ON p.partID = r.partID 
		INNER JOIN enrollment e ON p.partID = e.partID 
		WHERE e.startDate = '$today' AND e.ixModality = '1'"; // just web
	break;
	
	case 'blresult':
	// required fields exist, continue 
		$startDate = formatSearchDate($_POST['startdate']);
		$endDate   = formatSearchDate($_POST['enddate']);
		$reportname = 'HD2 Participants with appt. date between = <date range selected by user> and BL result = consented, refused, or unable to approach';
		$headings = "Study ID\tBaseline Result\tPreferred Phone\tRandomization Arm\tCounselor\n";
		$displayStart = displayDate($startDate);
		$displayEnd = displayDate($endDate);
		define('MY_FILENAME', "qc-blresult_".$displayStart.'-to-'.$displayEnd.'.xls');
		$sql = "SELECT p.partID, r.blResult, p.ptPPhone, v.randArm, s.staffFName, s.staffLName 
		FROM part_info p 
		INNER JOIN recruitment r ON p.partID = r.partID 
		LEFT OUTER JOIN enrollment e ON r.partID = e.partID
		LEFT OUTER JOIN appt a ON p.MRN = a.MRN 
		LEFT OUTER JOIN provider v ON a.provdID = v.provID
		LEFT OUTER JOIN staff s ON e.heID = s.staffID
		WHERE a.apptDate >= '$startDate' AND a.apptDate <= '$endDate' AND r.blResult != '0' AND 
		( (r.blResult >= 1 AND r.blResult <= 5 AND r.blResult != 2) OR (r.blResult = 9) )
		ORDER BY r.blResult";
	break;
	
	default:
		$err = urlencode('Invalid Request. You must use this form to generate the report.');
		header('location: get_qc_report.php?err='.$err);
		exit;
	break;
}

// generate a list 
$result = mysql_query($sql);
$total = mysql_num_rows($result);

$records = $headings;
if ($total > 0) {
	$totConsented = 0;
	$totRefused = 0;
	$totNoApproach = 0;
	while ($r = mysql_fetch_assoc($result)) {
		$records .= $r['partID']."\t".formatBaselineResult($r['blResult']);
		if ($_POST['mode'] == 'blresult') {
			$records .= "\t".$r['ptPPhone']."\t".$r['randArm']."\t".$r['staffFName'].' '.$r['staffLName'];
		}
		$records .= "\n";
		if ($r['blResult'] == '1') { $totConsented = $totConsented + 1; }
		if ($r['blResult'] == '3' || $r['blResult'] == '4') { $totRefused = $totRefused + 1; }
		if ($r['blResult'] == '5' || $r['blResult'] == '9') { $totNoApproach = $totNoApproach + 1; }
	}
} else {
	$records = $headings."\nNo records match your request.";
}

if ($mode == 'blresult') {
	// find and tack on 3 totals 
	$records .= "\nTotals\n";
	$records .= "Consented\t$totConsented\n";
	$records .= "Refusals\t$totRefused\n";
	$records .= "Not approached\t$totNoApproach\n";
}

// generate report in Excel 
header("Content-disposition: attachment;filename=".MY_FILENAME); 
header("Content-type: application/vnd.ms-excel"); 
header("Pragma: no-cache"); 
header("Expires: 0"); 
echo $records;

// ====================================================
function formatSearchDate($str) {
	if ( strpos($str, '-') != false ) {
		$delim = '-';
	} elseif ( strpos($str, '/') != false ) {
		$delim = '/';
	}
		list($mo, $day, $yr) = explode($delim, $str, 3);
		if (strlen($mo) < 2) { $mo = '0'.$mo; }
		if (strlen($day) < 2) { $day = '0'.$day; }
		if (strlen($yr) < 4) { $year = date('Y'); } 
		$ret = $yr.'-'.$mo.'-'.$day; // returns usable date for database search 
	return $ret;
}

function displayDate($str) {
	$delim = '-';
	list($y, $m, $d) = explode($delim, $str, 3);
	$ret = $m.'-'.$d.'-'.$y; // returns usable date for database search 
	return $ret;
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
		return 'Do not approach';
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