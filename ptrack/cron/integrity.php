<?php 
//  cron job to do some integrity checks on the database
//
//	1. PTS enrollment  = Joomla start date (day, not exact time) for web Ix
//	2. All PTS web Ix participants are in Joomla and userInfo tables
//	3. Users who are past 27 weeks should be web inactive
//  4. All userInfo study IDs should exist in PTS, and are webIx
//	5. userInfo studyIDs and JoomlaIDs are unique (no dups)
//	6. a. All jos_users (registered) are in userInfo table
//	   b. All userInfo are in  jos_users (registered)  table
//  7. survey (how much to do?)
//		Completed token # -> survey with studyID exists
//		survey studyID exists in PTS 
//		survey studyID unique across survey
//		completed survey tokens exist as survey studyIDs

define ( "JPATH_SITE", '/var/www/new/html/lung_cancer_site');

require_once( JPATH_SITE .'/includes/hd2/user.php' );

require_once( JPATH_SITE .'/includes/hd2/shared.php' );
require_once( JPATH_SITE .'configuration.php' );


$CRfilepath = "/var/www/new/html/logs/hd2/";

define ( "SURVEY_ID", '18742');

function sendemail( $email, $subject, $messagetext) {
 	$fromEmail = 'dave_rothfarb@dfci.harvard.edu@dfci.harvard.edu';
	$status = mail( $email, $subject, $messagetext, "From: $fromEmail" );
	if (!$status) error_log("\n".  'Error sending email to '. $email , 3, $CRfilepath.'cronjobs.err');

}

function sendReminder( $email, $msgkey, $fname, $studyID) {
global $emailReminders;

	$messagetext = sprintf($emailReminders[$msgkey][1], $fname);
	info_log( "\tEmail type " .$msgkey . ' sent to '. $email . ' studyID =' . $studyID); 
	sendemail( $email, $emailReminders[$msgkey][0], $messagetext);
}

function notifyErrorAdmin ( $msg ) {
	sendemail( 'dave_rothfarb@dfci.harvard.edu', 'Healthy Directions II substudy cronjob technical error', $msg);
}

global $logfile;

function info_log( $text ) {
global $logfile;

	if (!$logfile) return;
	echo "\n". date("Y-m-d H:i:s ") .$text;
	if (!fwrite( $logfile, "\n". $text) ) notifyErrorAdmin('Unable to write to log file cronjobs.log');
}

global $errflag;
/*

	// Log all errors to error log file
	// Enter a timestamp into log file
	// error_log( "\n". 'testing system log file');
	chmod($CRfilepath.'cronjobs.log', 664); 
	$logfile = fopen($CRfilepath.'cronjobs.log', "a");
	if (! $logfile) {
		echo 'Error fopen log file';
		info_log( 'Error fopen log file');
		notifyErrorAdmin( 'Error fopen log file');
		// Try to continue anyway
	}
	*/
	// temp out: 
	// info_log( "\n". date('Y-m-d'));
	echo ( "\n". date('Y-m-d'). "\n");

	$jConfig = new JConfig;	// Obtain current configuration parameters

	$statusArray = dbMysqlConnect(USERDB);
	if (! $statusArray[0] ) {
		echo  "\nUnable to connect to user database: ". $statusArray[1];
		info_log(  'Unable to connect to user database: '. $statusArray[1]);
		notifyErrorAdmin(  'Unable to connect to user database: '. $statusArray[1]);
		die();
	}
	else $userMsqlDB = $statusArray[0];
		
	// Day of Week for this run, since we're only interested in exact weekly 'anniversaries'
	$DoW = strftime("%u", $_SERVER['REQUEST_TIME']);
	// Substract 1 to match SQL weekday() function return values
	$DoW--;
	$today = $_SERVER['REQUEST_TIME'];
	// Adjust today date to beginning of day 
    $today=mktime(0,0,1,date("n",$today),date("j",$today),date("Y",$today));
	
	echo 'HD2 Database Integrity Check';
	//	1. PTS enrollment  = Joomla start date (day, not exact time) for web Ix
	$sql = "SELECT e.startDate, date(e.startDate), e.partID, date(j.registerDate), j.registerDate FROM enrollment e , userInfo u, lung_cancer_site.jos_users j WHERE e.partID = u.studyID AND u.joomlaID = j.id AND e.`ixModality` = 1 AND date(e.startDate) != date(j.registerDate)";
	// echo $sql;	
	// info_log ( 'Web Deactivation::');
	$result = mysql_query($sql, $userMsqlDB ) ;
	
	if ($result) {
			if  (mysql_num_rows($result) > 0) {
				echo "\nError PTS web Ix and jos_users start dates don\'t match";
				$errflag = true;
				while( $row = mysql_fetch_assoc($result)) {
						$studyID = $row['partID'];
						echo "\nstudyID: $studyID";
						// print_r($row);
				}
			}
			else echo "\nPTS web Ix and jos_users start dates match";
	}
	else echo "\nDB Error: ".mysql_error( $userMsqlDB) ;
	
	//	2. All PTS web Ix participants are in Joomla and userInfo tables
	//	a. All PTS web Ix participants are in userInfo table
	$sql = "SELECT e.partID FROM enrollment e WHERE NOT EXISTS (SELECT * from userInfo u WHERE e.partID = u.studyID) AND e.`ixModality` = 1";
	// echo $sql;	
	// info_log ( 'Web Deactivation::');
	$result = mysql_query($sql, $userMsqlDB ) ;
	
	if ($result) {
			if  (mysql_num_rows($result) > 0) {
				echo "\nError PTS web Ix does not exist in userInfo table";
				$errflag = true;
				while( $row = mysql_fetch_assoc($result)) {
						$studyID = $row['partID'];
						echo "<br>studyID: $studyID";
						// print_r($row);
				}
			}
			else echo "\nPTS web Ix users are all in userInfo";
	}
	else echo "\nDB Error: ".mysql_error( $userMsqlDB) ;

	
	//	b. All PTS web Ix participants are in Joomla table
	$sql = "SELECT e.partID FROM enrollment e WHERE NOT EXISTS (SELECT * from userInfo u, lung_cancer_site.jos_users j WHERE e.partID = u.studyID AND u.joomlaID = j.id) AND e.`ixModality` = 1";
	// echo $sql;	
	// info_log ( 'Web Deactivation::');
	$result = mysql_query($sql, $userMsqlDB ) ;
	
	if ($result) {
			if  (mysql_num_rows($result) > 0) {
				echo "\nError PTS web Ix does not exist in jos_users";
				$errflag = true;
				while( $row = mysql_fetch_assoc($result)) {
						$studyID = $row['partID'];
						echo "<br>studyID: $studyID";
						// print_r($row);
				}
			}
			else echo "\njos_users JoomlaIDs are all in jos_users";
	}
	else echo "\nDB Error: ".mysql_error( $userMsqlDB) ;

//	3. Users who are past 27 weeks should be web inactive in userInfo and Joomla
//	a. Users who are past 27 weeks should be web inactive in userInfo and Joomla
	// Calculate today's date - 26 weeks
	$wk27ago = strtotime(date("Y-m-d H:i:s", $_SERVER['REQUEST_TIME']) . " -27 week");
	

    $todaydate =date("Y-m-d H:i:s", $today);
	$wk27agodate =date("Y-m-d H:i:s", $wk27ago);	

	// $sql = "SELECT u.studyID, e.startDate, u.activeStatus FROM enrollment e JOIN userInfo
 // u ON e.partID = u.studyID WHERE u.activeStatus = 1 AND (e.startDate) < '$wk27agodate' ";
	$sql = "SELECT u.studyID, e.startDate, u.activeStatus, j.block FROM enrollment e JOIN userInfo
 u ON e.partID = u.studyID JOIN lung_cancer_site.jos_users j ON u.joomlaID = j.id WHERE ((u.activeStatus = 1) OR (j.block = 0)) AND (e.startDate) < '$wk27agodate' ";
	// echo $sql;
	// info_log ( 'Web Deactivation::');
	$result = mysql_query($sql, $userMsqlDB ) ;
	
	if ($result) {
			if  (mysql_num_rows($result) > 0) {
				echo "\nError PTS web Ix studyID ended but is still active in userInfo or Joomla";
				$errflag = true;
				while( $row = mysql_fetch_assoc($result)) {
						$studyID = $row['studyID'];
						echo "<br>studyID: $studyID";
						// print_r($row);
				}
			}
			else echo "\nAll PTS web Ix studyID ended are inactive in userInfo and Joomla";
	}
	else echo "\nDB Error: ".mysql_error( $userMsqlDB);

//	b. Users who are PTS inactive and web Ix should be web inactive in userInfo and Joomla
$sql = "SELECT u.studyID, e.startDate, u.activeStatus, j.block, p.ptStatus FROM part_info p JOIN enrollment e ON p.partID = e.partID 
 JOIN userInfo
 u ON e.partID = u.studyID JOIN lung_cancer_site.jos_users j ON u.joomlaID = j.id WHERE ((u.activeStatus = 1) OR (j.block = 0)) AND p.ptStatus = 'i'";
 
	// echo $sql;
	// info_log ( 'Web Deactivation::');
	$result = mysql_query($sql, $userMsqlDB ) ;
	
	if ($result) {
			if  (mysql_num_rows($result) > 0) {
				echo "\nError PTS web Ix studyID inactive but is still active in userInfo or Joomla";
				$errflag = true;
				while( $row = mysql_fetch_assoc($result)) {
						$studyID = $row['studyID'];
						echo "<br>studyID: $studyID";
						// print_r($row);
				}
			}
			else echo "\nAll Inactive PTS web Ix studyIDs are inactive in userInfo and Joomla";
	}
	else echo "\nDB Error: ".mysql_error( $userMsqlDB);


//  4. All userInfo study IDs should exist in PTS, and are webIx


	$sql = "SELECT u.studyID FROM userInfo u  WHERE NOT EXISTS (SELECT * from enrollment e WHERE e.partID = u.studyID AND e.`ixModality` = 1) AND u.activestatus = 1";
	// echo $sql;	
	// info_log ( 'Web Deactivation::');
	$result = mysql_query($sql, $userMsqlDB ) ;
	
	if ($result) {
			if  (mysql_num_rows($result) > 0) {
				echo "\nError userInfo studyID does not exist in PTS/is not web Ix";
				$errflag = true;
				while( $row = mysql_fetch_assoc($result)) {
						$studyID = $row['studyID'];
						echo "<br>studyID: $studyID";
						// print_r($row);
				}
			}
			else echo "\nuserInfo studyIDs exist in PTS and is web Ix";
	}
	else echo "\nDB Error: ".mysql_error( $userMsqlDB) ;

	//	5. userInfo studyIDs and JoomlaIDs are unique (no dups)
	$sql = "Select * FROM (SELECT count(studyID) as count, studyID FROM `userInfo` group by studyID) t where count > 1";
	// echo $sql;	
	// info_log ( 'Web Deactivation::');
	$result = mysql_query($sql, $userMsqlDB ) ;
	
	if ($result) {
			if  (mysql_num_rows($result) > 0) {
				echo "\nError userInfo studyID duplicates";
				$errflag = true;
				while( $row = mysql_fetch_assoc($result)) {
						$count = $row['count'];
						$studyID = $row['studyID'];
						echo "\nstudy ID: $studyID, count = $count";
						print_r($row);
				}
			}
			else echo "\nuserInfo studyIDs are unique (no dups)";
	}
	else echo "\nDB Error: ".mysql_error( $userMsqlDB) ;

	$sql = "Select * FROM (SELECT count(joomlaID) as count, joomlaID FROM `userInfo` group by joomlaID) t where count > 1";
	// echo $sql;	
	// info_log ( 'Web Deactivation::');
	$result = mysql_query($sql, $userMsqlDB ) ;
	
	if ($result) {
			if  (mysql_num_rows($result) > 0) {
				echo "\nError userInfo joomlaID duplicates";
				$errflag = true;
				while( $row = mysql_fetch_assoc($result)) {
						$count = $row['count'];
						$joomlaID = $row['joomlaID'];
						echo "\njoomla ID: $joomlaID, count = $count";
						// print_r($row);
				}
			}
			else echo "\nuserInfo JoomlaIDs are unique (no dups)";
	}
	else echo "\nDB Error: ".mysql_error( $userMsqlDB);
	
//	6. a. All jos_users (registered) are in userInfo table
	$sql = "SELECT * FROM lung_cancer_site.jos_users j WHERE NOT EXISTS (SELECT * from lung_cancer_user.userInfo u  WHERE j.id = u.joomlaID  ) and j.gid = 18";
	// echo $sql;	
	// info_log ( 'Web Deactivation::');
	$result = mysql_query($sql, $userMsqlDB ) ;
	
	if ($result) {
			if  (mysql_num_rows($result) > 0) {
				echo "\nError joomlaID userInfo verification";
				$errflag = true;
				while( $row = mysql_fetch_assoc($result)) {
						// $studyID = $row['studyID'];
						$joomlaID = $row['id'];
						echo "\njoomla ID = $joomlaID has no studyID in userInfo";
						// print_r($row);
				}
			}
			else echo "\njos_users JoomlaIDs are all in userInfo";
	}
	else echo "\nDB Error: ".mysql_error( $userMsqlDB) ;


//	   b. All userInfo are in  jos_users (registered)  table
	$sql = "SELECT * FROM `userInfo` u WHERE NOT EXISTS (SELECT * from lung_cancer_site.jos_users j WHERE j.id = u.joomlaID)";
	// echo $sql;	
	// info_log ( 'Web Deactivation::');
	$result = mysql_query($sql, $userMsqlDB ) ;
	
	if ($result) {
			if  (mysql_num_rows($result) > 0) {
				echo "\nError userInfo joomlaID verification";
				$errflag = true;
				while( $row = mysql_fetch_assoc($result)) {
						$studyID = $row['studyID'];
						$joomlaID = $row['joomlaID'];
						echo "\njoomla ID: $joomlaID, studyID = $studyID";
						print_r($row);
				}
			}
			else echo "\nuserInfo JoomlaIDs are all in jos_users";
	}
	else echo "\nDB Error: ".mysql_error( $userMsqlDB) ;

	mysql_select_db(LIMEDB);
	// >> check status
//  7. survey (how much to do?)
//		a.Completed token # -> survey with studyID exists.
//		survey has token 
	$colname = SURVEY_ID.'x1x30';

	$sql = "SELECT s.14872x1x30  FROM lime_survey_" . SURVEY_ID . " s 
WHERE NOT EXISTS (SELECT * FROM lime_tokens_" . SURVEY_ID . " t WHERE
 s." . SURVEY_ID . "x1x30 = t.token )";

	// echo $sql;	
	// info_log ( 'Web Deactivation::');
	$result = mysql_query($sql, $userMsqlDB ) ;
	
	if ($result) {
			if  (mysql_num_rows($result) > 0) {
				echo "\nError survey studyID does not exists in survey token table";
				$errflag = true;
				while( $row = mysql_fetch_assoc($result)) {
						
						$studyID = $row[$colname];
						echo "\nsurvey study ID: $studyID";
						// print_r($row);
				}
			}
			else echo "\nsurvey studyIDs exist in survey token table";
	}
	else echo "\nDB Error: ".mysql_error( $userMsqlDB);

//		b. survey studyID exists in PTS 
	$sql = "SELECT  s." . SURVEY_ID . "x1x30  FROM " .LIMEDB. ".lime_survey_" . SURVEY_ID . " s WHERE NOT EXISTS (SELECT * FROM lung_cancer_user.part_info p, lung_cancer_user.recruitment r WHERE
 s." . SURVEY_ID . "x1x30 = p.partID AND p.partID = r.partID  )";

	// echo $sql;	
	// info_log ( 'Web Deactivation::');
	$result = mysql_query($sql, $userMsqlDB ) ;
	
	if ($result) {
			if  (mysql_num_rows($result) > 0) {
				echo "\nError survey studyID does not exists in PTS or is not recruited";
				$errflag = true;
				while( $row = mysql_fetch_assoc($result)) {
						
						$studyID = $row[$colname];
						echo "\nsurvey study ID: $studyID";
						// print_r($row);
				}
			}
			else echo "\nsurvey studyIDs exist in PTS";
	}
	else echo "\n>DB Error: ".mysql_error( $userMsqlDB);

//		c.survey studyID unique across survey
	$sql = "Select * FROM (SELECT count(*) as count,  " . SURVEY_ID . "x1x30  FROM " .LIMEDB. ".lime_survey_" . SURVEY_ID . " group by " . SURVEY_ID . "x1x30 )t where count > 1";

	// echo $sql;	
	// info_log ( 'Web Deactivation::');
	$result = mysql_query($sql, $userMsqlDB ) ;
	
	if ($result) {
			if  (mysql_num_rows($result) > 0) {
				echo "\nError survey results studyID duplicates";
				$errflag = true;
				while( $row = mysql_fetch_assoc($result)) {
						$count = $row['count'];
						$studyID = $row[$colname];
						echo "\nstudy ID: $studyID, count = $count";
						// print_r($row);
				}
			}
			else echo "\nsurvey studyID unique (no dups)";
	}
	else echo "\nDB Error: ".mysql_error( $userMsqlDB) ;
	

//		d. completed survey tokens exist as survey studyIDs
	$sql = "SELECT t.token, t.completed  FROM lime_tokens_" . SURVEY_ID . " t 
WHERE NOT EXISTS (SELECT * FROM lime_survey_" . SURVEY_ID . " s  WHERE
 s." . SURVEY_ID . "x1x30 = t.token ) AND t.completed = 'Y'";
	// echo $sql;	
	// info_log ( 'Web Deactivation::');
	$result = mysql_query($sql, $userMsqlDB ) ;
	
	if ($result) {
			if  (mysql_num_rows($result) > 0) {
				echo "\nError completed survey tokens do not exist as survey studyIDs";
				$errflag = true;
				while( $row = mysql_fetch_assoc($result)) {
						$count = $row['count'];
						$studyID = $row[$colname];
						echo "\nstudy ID: $studyID, count = $count";
						// print_r($row);
				}
			}
			else echo "\ncompleted survey tokens exist as survey studyIDs";
	}
	else echo "\nDB Error: ".mysql_error( $userMsqlDB) ;


	// Close db handles
	// fclose($logfile);


	if ( $errflag )  notifyErrorAdmin ( 'DB Integrity check has found errors. See cronjob output' );

	mysql_close($userMsqlDB);
?>
