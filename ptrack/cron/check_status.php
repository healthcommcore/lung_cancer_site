<?php 
//  cron job to check the pt staus 
//
//	1. All pts who have "I" status should be in the following senerio: pt or pcp out, not consent, withdraw
//	2. All pts who have "A" status should not have any above
//	
// LOAD mySQL FUNCTIONS
include("/var/www/lung_cancer_site/ptrack/includes/connection.php");
// CONNECT TO DATABASE
if ($mysqlID=dbConnect()) {
    selectDatabase("lung_cancer_user");
} else {
    echo "<b>ERROR:</b> ".mysql_error()."</b><br>\n";
}
//--> declare vars 
$inactive_records = array();
$change_to_active = array();
$active_records = array();
$change_to_inactive = array();
$upd_act_errs = array();
$upd_act_succ = array();
// declare path
$resultLogsPath = '/var/www/logs/hd2/'; 

// check the pts that has status = 'I'
echo "checking pt with inactive status<br>";
$sql="SELECT pt.partID, pt.ptStatus, re.pcpOptOut, re.ptOptOut, re.blResult, en.withdID 
from part_info pt LEFT OUTER JOIN recruitment re on pt.partID = re.partID LEFT OUTER JOIN enrollment en
on pt.partID = en.partID WHERE pt.ptStatus = 'I'";
$result = mysql_query($sql);
while ($row = mysql_fetch_assoc($result)) {
	$inactive_records[$row['partID']] = $row;
	// first to check the recruitment info
	if($row['pcpOptOut'] != 'Opt-out' && $row['ptOptOut'] != 'Opt-out' && $row['blResult'] <= 1){
		// then check the enrollment info
		if($row['withdID'] == 0 || $row['withdID'] == ''){
			//echo $row['partID'].">>".$row['pcpOptOut'].">".$row['ptOptOut'].">".$row['blResult'].">".$row['withdID']."<br>";
			$change_to_active[] = $row['partID'];
			//echo "the pt is consented and did not withdrew, then the record will add to the change array ".$row['partID']."<br>";
		}
	}
}

// update the record to active
if(count($change_to_active) >0){
	echo "the records need to be changed to active are: <br>";
	foreach ($change_to_active as $rec){
		echo $rec."<br>";
		$updFlg1 = updateSatus($rec, 'A');
		 if($updFlg1){
			$upd_act_errs[] = $rec;
		}else{
			$upd_act_succ[] = $rec;
		}
	}
}else{
	echo "no records need to be changed to active. <br>";
}


// check the pts that has status = 'A'
/*echo "checking pt with active status<br>";
$sql="SELECT pt.partID, pt.ptStatus, re.pcpOptOut, re.ptOptOut, re.blResult, en.withdID 
from part_info pt LEFT OUTER JOIN recruitment re on pt.partID = re.partID LEFT OUTER JOIN enrollment en
on pt.partID = en.partID WHERE pt.ptStatus = 'A'";
$result = mysql_query($sql);
while ($row = mysql_fetch_assoc($result)) {
	$active_records[$row['partID']] = $row;
	// first to check the recruitment info
	if($row['pcpOptOut'] == 'Opt-out' || $row['ptOptOut'] == 'Opt-out' || $row['blResult'] > 1){
		echo $row['partID'].">>".$row['pcpOptOut'].">".$row['ptOptOut'].">".$row['blResult'].">".$row['withdID']."<br>";
		echo "this pt did not consent to baseline <br>";
		$change_to_inactive[] = $row['partID'];
		//$change_to_inactive[$row['partID']] = $row['blResult'];
		echo "this record will add to the change array ".$row['partID']."<br>";
	}else{
		// then check the enrollment info
		if($row['withdID'] >0){
			echo $row['partID'].">>".$row['pcpOptOut'].">".$row['ptOptOut'].">".$row['blResult'].">".$row['withdID']."<br>";
			$change_to_inactive[] = $row['partID'];
			//$change_to_inactive[$row['partID']] = $row['blResult'];
			echo "the pt withdrew, then the record will add to the change array ".$row['partID']."<br>";
		}
	}
}

// update the record to inactive
echo "the records need to be changed to inactive are: <br>";
foreach ($change_to_inactive as $rec){
	echo $rec."<br>";
	//$updFlg2 = updStatus($rec, 'I');
	// if($updFlg2){
		//$upd_inact_errs[] = $rec;
	//}
}
*/
if (count($change_to_active) > 0) {
	$total_act = count($change_to_active);
	$total_inact = count($change_to_inactive);
	//--> log what happened to file in append mode 
	$upd_msg = '';
	if (!$updFlg1) {
		$upd_msg .= "\n".date('m-d-Y').": Successfully update the status for inactive pts to active total ".$total_act." pts.<br>";
	//} elseif(!$updFlg2) {
		//$upd_msg .= date('m-d-Y').": Successfully update the status for active pts to inactive total ".$total_inact." pts.<br>";
	}else{
		if($updFlg1){
			$total_act_failed = count($upd_act_errs);
			$upd_msg .= "\nUpdate inactive pts to active status have have total ".$total_act. " pts. 
			                 There are ".$total_act_failed." pts failed for update";
			
		}//elseif($updFlg2){
			//$total_inact_failed = count($upd_inact_errs);
			//$upd_msg .= "Update active pts to inactive status have have total ".$total_inact. " pts. 
			                // There are ".$total_inact_failed." pts failed for update";
			//$total_inact_failed = count($upd_inact_errs);
			//foreach ($upd_act_errs as $err) {
				//$upd_msg .= $err;
			//}
		//}
		
	}
}else{
	$upd_msg .= "\n".date('m-d-Y').": No records need to be changed";
}

// ifno file, create a file to write
if (!file_exists($resultLogsPath.'status_log.txt')) { 
	$fp = fopen($resultLogsPath.'status_log.txt', 'w'); // append  records 
	fwrite($fp, $upd_msg);
	fclose($fp);
	chmod($resultLogsPath.'status_log.txt', 0664); 
}else{
	$fp = fopen($resultLogsPath.'status_log.txt', 'a'); // append  records 
	fwrite($fp, $upd_msg);
	fclose($fp);
}
//--> log all activity - // sending email to programmer and project manager
if (count($change_to_active) > 0) {
	
	$sendto1 = 'Qi_Wang@dfci.harvard.edu'; // to programmer
	$sendto2 = 'Molly_Coeling@DFCI.HARVARD.EDU'; // send to Molly too
	$statuslog = $resultLogsPath.'upd_status_result.txt'; // file that we'll write to for all changed records 
	$msg = date('m-d-Y')." The following records have been updated status to active:\n"; // start error message
	foreach ($upd_act_succ as $succ) {
		$msg .= $succ."\n";
	}
	$msg .= "\n\n";
	if(count($upd_act_errs) > 0){
		$msg .= "The following records have failed during upodate:\n";
		foreach ($upd_act_errs as $err) {
			$msg .= $err."\n";
		}
	}
	
	$msg .= "\n\n";
	if (!file_exists($statuslog)) { 
		$fp = fopen($statuslog, 'w'); // write to the file 
		fwrite($fp, $msg);
		fclose($fp);
		chmod($statuslog, 0664); 
	}else{
		$fp = fopen($statuslog, 'a'); // append bad records 
		fwrite($fp, $msg);
		fclose($fp);
	}
	$status1 = mail($sendto1, 'Status checking result', $msg, "From: $sendto1" );
	$status2 = mail($sendto2, 'Status checking result', $msg, "From: $sendto1" );
	if (!$status1) error_log("\n".  'Error sending email to '. $sendto1 , 3, $resultLogsPath.'check_status.err');
	if (!$status2) error_log("\n".  'Error sending email to '. $sendto2 , 3, $resultLogsPath.'check_status.err');
	echo "Status checking result mailed to: $sendto1 and $sendto2<BR>\n";
	echo '<PRE>'.$msg.'</PRE>';
}

dbClose();


?>

