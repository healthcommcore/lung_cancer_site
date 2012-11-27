<?php
// Implementation Notes:
//		important!  
//		start date is start of day (first minute) of registration date, 
//		to avoid situations where week/stage changes in the middle of 
//	the day.
//
//		stepGoal is initialized at 5000. will be
//		updated if the user is promoted or demoted for the Steps goal.
//
//		stepDate keeps track of the day (first hour) on which the
//		new step goal period applies; is initialized with the startDate;
//
//		pointDate saves the last time on which points were clicked by
//		the user, in order to enforce 1 max click per day.

// This class is used to store run-time information so as to avoid
//	a bevy of variables
class user {
	// Retrieved from DB
	var $userID;			// studyID 
	var $startDate;			// From patient DB
	var $email;				// From patient DB or Joomla
	var $fname;				// From Joomla
	var $points;			// From userSettings table
	// var $pointDate;
	// From userPlan table
	var $planReason;
	var $planReasonopt;
	var $planSupport;
	var $planBehNo;
	// From userTrack table,!!  not always set!!
	var $lastTrackDate;		// most recent tracking date, all behaviors (
	// Calculated data (is this necessary)
	var $weeksSinceStart;	// 
	var $nonSmoker;		// From survey. false by default, track smoking
	var $cigSmoked;		// From survey, if > 0, # cigarettes / day
	
}
require_once( JPATH_SITE .'/includes/hd2/config.php' );
require_once( JPATH_SITE .'/includes/hd2/shared.php' );


// Look up user information for the current joomla ID in the
//	userSettings table. Joomla's ID is stored as userID in
//	the table.  If data does not exist for this user (eg. when
//	the user logs in for the first time, a new entry is created
//	with default values:
//	
//		startDate = Joomla user registration date, adjusted
//			to start of day.
//		emil = Joomla email.  This information is saved so
//			that other scripts don't have to perform JOIN
//			with Joomla DB
//		stepDate = startDate	
//		stepGoal = 5000, initial value 
//			for Walking stepped goal promotion/demotion
//
//	Note that two different database handles are used, one
//	for Joomla's DB, one for the BFBW application
//
// Returns array
//	0 => new User structure if successful, else NULL
//	1 => error msg, else ''
//
//	Error conditions:
//		No user  in userInfo table
//		Error db (select)
function initUserData($userMsqlDB) {
	$my = JFactory::getUser();
	if ($my->id == 0) return NULL;

	$user = new user;
	$user->startDate = 0;	// for now	
	
	// For start date, email, use $my values

    $sql = "SELECT * FROM userInfo WHERE joomlaID=$my->id";
	// echo $sql;	
 	$result = mysql_query($sql, $userMsqlDB ) ;

	if (!$result) {
		$msg = sprintf( _HCC_TKG_DB_SELECT . ": %s\n", mysql_error( $userMsqlDB));
		return array( 0=> NULL, 1 => $msg);
	}
	else {
		$row = mysql_fetch_assoc($result);
		if ($row != false) {
			// print_r($row);
			$user->userID=  $row['studyID'];
			
			$user->startDate= strtotime($my->registerDate);
			// Adjust start date to first minute of the day
			$user->startDate=mktime(0,1,0,date("n",$user->startDate),date("j",$user->startDate),date("Y",$user->startDate));
			$user->points= $row['rafflepoints'];
			$user->planReason= $row['planReason'];
			$user->planReasonopt= $row['planReasonopt'];
			$user->planSupport= $row['planSupport'];
			$user->planBehNo= $row['planBehNo'];
			$isNonSmoker = $row['smoker'] == 0 ? true : false;
			$user->nonSmoker = $isNonSmoker;
			$user->cigSmoked = $row['numSmoked'];
			$user->email = $my->email;
			$user->fname = $my->name;

		}
		else {
			$msg = sprintf( _HCC_TKG_DB_SELECT . ": %s\n", mysql_error( $userMsqlDB));
			return array( 0=> NULL, 1 => $msg);
		}
	}

	//echo 'start date: '. date("l F j, Y, g:i a", $user->startDate );


// Don't need this anymore because we are not using Lime survey data to determin smoking status
/*
	// Survey data
	$statusArray= getSmokingData( $user);
	// If error, log error but continue, assume smoker 
	if ($statusArray[0] == null) {
		$user->nonSmoker = false;
		// Log error
		error_log( "\n". date("Y-m-d H:i:s ") .  'GetSmokingData error: '. $statusArray[1], 3, '/var/www/logs/hd2/hd2.log');
	
	}
	
	else $user = $statusArray[0];
	// This would not be necessary if stored date were correct
	$user->weeksSinceStart = ceil( ($_SERVER['REQUEST_TIME'] - $user->startDate) /_HCC_SECONDS_PER_DAY/7 );
	
	// print_r($user);
*/	
	return array( 0=>  $user, 1 => '');
}

// returns previous login date (as date) if any, (from userLogin table)
//	not including current, number of previous login 
//  returns null if no previous login (ie. this is first login) or if
//	 any errors
function getLastLogin($userMsqlDB, $studyID) {
    $sql = "SELECT logintime FROM userLogin WHERE studyID=$studyID  order by logintime desc limit 3";
	
 	$result = mysql_query($sql, $userMsqlDB ) ;

	if (!$result) {
		error_log( "\n". date("Y-m-d H:i:s ") . _HCC_TKG_DB_SELECT . ':'. mysql_error(), 3, '/var/www/logs/hd2/hd2.log');
		return null;
	}
	if ( ($numrows = mysql_num_rows ($result )) > 1) {
		$row = mysql_fetch_row($result);
		// print_r($row);
		if ($row != false) {
			// want next row (which is previous login)
			if ($row = mysql_fetch_row($result))  return ( array('time' => $row[0], 'num' => $numrows -1));
			else return null;
		}
		return null;
	}
	return null;
}

?>