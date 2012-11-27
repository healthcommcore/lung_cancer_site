<?php
define ( "JPATH_SITE", getcwd() . '/../');
header("Content-disposition: filename=reportHistoryTracking.xls");
header("Content-type: application/vnd.ms-excel");
header("Expires: 0");

require( JPATH_SITE .'/includes/hd2/config.php' );
require( JPATH_SITE .'/includes/hd2/constants.php' );
require( JPATH_SITE .'/includes/hd2/shared.php' );
require( JPATH_SITE .'/includes/hd2/behavior.php' );
require( JPATH_SITE .'/includes/hd2/user.php' );
require( JPATH_SITE .'/includes/hd2/data.php' );


global $user;
// REdefine for admin site
// Site-wide variable definitions
global $imageDir;
global $relSiteDir, $siteDir;

$imageDir = 'images/hd2/tracking/';
$siteDir= JPATH_SITE. "/images/hd2/tracking/";
$relSiteDir= "../images/hd2/tracking/";
require_once( 'includes/initweb.php' );

global $trackedBehaviors;
// array of trackgoal objects (defined trackgoal.php)
//	contains current tracked behaviors, keyed by behaviorID
$trackedBehaviors = array();
$trackedBehaviors[] = 1;
$trackedBehaviors[] = 2;
$trackedBehaviors[] = 3;
$trackedBehaviors[] = 4;
$trackedBehaviors[] = 5;	// Set to smoking even if we're not
	

// THIS IS A PRIVATE SPECIAL VERSION OF THE getAllData function - last-minute
// request, so simplest, if not best, way to implement it.

// Sets input parameter array with # times tracked each week for a given behavior since
//	start of study
// set in $currentData
//	Returns status array
//	0 => currentData array, null if error
//	1 => error message if KO, else ''
function getAllDataTrack ($userMsqlDB, $user, $behaviorID, $currentData, $rightNow) {
	
    // Adjust to start of day
	// $timeSTART=mktime(0,0,1,date("n",$stageStart),date("j",$stageStart),date("Y",$stageStart));
	$timeSTART=mktime(0,0,1,date("n",$user->startDate),date("j",$user->startDate),date("Y",$user->startDate));
	
	
	// Query database to select between start date and today
    $sql="SELECT userVal, timestamp FROM userTrack WHERE (studyID=".$user->userID.") AND (behaviorID=".$behaviorID.") order by timestamp ASC";
	// echo $sql;
	
 	$result = mysql_query($sql, $userMsqlDB ) ;
	if ($result) {
					// assign row info into $currentData
					$weekno = 1;		// start with first week
					$weekcount = 0;		// number data items for current week
					$weektotal = 0;		// running total
					$weekEND = strtotime(date("Y-m-d H:i:s", $timeSTART) . " +1 week");

				while ($row = mysql_fetch_assoc($result))  {
					// print_r($row);

						if ( $row['timestamp'] <= $weekEND ) {
							$weektotal += $row['userVal'];
							$weekcount++;
						}
						
						else {
							// set count for week, 
								$currentData[$behaviorID]->allArray[$weekno] = $weekcount;
							
							// determine which week timestamp corresponds to and
							//	update week # and weekEND
							$delta = intval (($row['timestamp'] - $weekEND ) / (7 *	_HCC_SECONDS_PER_DAY)) + 1;
							// reset totals to new value
							$weekno+= $delta;
							$weekEND = strtotime(date("Y-m-d H:i:s", $timeSTART) . " +". $weekno . " week");
							$weektotal = $row['userVal'];
							$weekcount = 1;
							
						}
					} // end rows
					
					// If there is data for final week, set it - although we are not currently displaying it
					$currentData[$behaviorID]->allArray[$weekno] = $weekcount;
    }
	else {
		// Error query
		$msg = mysql_error( $userMsqlDB);
		return array( 0=> null , 1=> $msg);
	}
	
	// print_r($currentData);
	// return null;
	return array( 0=> $currentData , 1=> '' );

}


function displayReport( $studyID) {
global $trackedBehaviors, $behaviorSpecs;

	// Connect to the database
	$msg ='';
	
	$statusArray = dbMysqlConnect(USERDB);
	if (! $statusArray[0] ) {
		$msg =  'Unable to connect to user database';
		return array( 0=> 0, 1=> $msg);
	
	}
	else $userMsqlDB = $statusArray[0];
	
	$allUsers = array();
	$sql = "SELECT userInfo.studyID FROM userInfo";
	// echo $sql;
	$result = mysql_query($sql, $userMsqlDB ) ;
	if ($result) {
			if  (mysql_num_rows($result) > 0) {

				while( $row = mysql_fetch_assoc($result)) {
					$allUsers[] = $row['studyID'];
				}
			}
			else {
				echo ( "No web users to report");
				return;
			}
	}
	// print_r($allUsers);
		// Column headers
		echo "Study ID";
		echo "\tWeek #";
		echo "\tPA\t RM\t FV\t MV\t SM";
		echo "\tSmoker";
	
	foreach ($allUsers as $studyID) {
		$tempcount++;
		// echo "<h2>$studyID</h2>";

			$statusArray = initUserDataFromStudyID($userMsqlDB, $studyID);
			if ($statusArray[0] == null)  {
				if ($statusArray[1] == '') {
					echo '<b>There is no web data for this user.  The user is probably not in the web Ix.</b>';
				} else {
					echo '<b>There was a technical error - please seek technical help</b>: '.$statusArray[1] ;		
				}
				return;
			}	
		
			$user = $statusArray[0];
			
			// For smoking, retrieve survey information
			//	If none, assume smoking?
			// if (!$user->nonSmoker) $trackedBehaviors[] = 5;
			
		
			$currentData = array();	
			for ($i = 1; $i <= count($behaviorSpecs); $i++) {
				$currentData[$i] = new trackData;
			}
		
			// echo '<table cellpadding="0"  cellspacing="0" border="0"><tr><td>';
			
			// print_r($currentData);
			// print_r($trackedBehaviors);
		
				// echo "<h2>$studyID</h2>";
	
				foreach ($trackedBehaviors as $behaviorID) {
						//echo '<tr><td><h4>';
						//echo ucwords($behaviorSpecs[$behaviorID]['sname']); 
						//echo '</h4>';
							
						$statusArray = getAllDataTrack ($userMsqlDB, $user, $behaviorID, $currentData, $_SERVER['REQUEST_TIME']);
						if ( ($statusArray[0] == null) && ($statusArray[1] != '') ) {
							echo 'We are not able to retrieve your tracking data due to a database error: ' . $statusArray[1];
							return;
						}
						else $currentData = $statusArray[0];
					
						
						// print_r( $currentData[$behaviorID]->allArray);
						// print_r($user->weeksSinceStart);
							
						// drawChartN($behaviorID, $currentData[$behaviorID]->allArray, false, $user->weeksSinceStart, $user, $_SERVER['REQUEST_TIME'], $i);
						//echo '</td></tr>';
					}	
				//echo '<tr><td><h4>';
			
				// echo '</table>';
				
				// Display data - loop through weeks, then each behavior
				// for ($w = 1; $w <= 26; $w++) {
				for ($w = 1; ($w <= $user->weeksSinceStart) && ($w <= 26) ; $w++) {
					// echo 'Week # '. $w. '<br>';
					echo "\n". $studyID; 
					echo "\t". $w; 
					foreach ($trackedBehaviors as $behaviorID) {
						if (isset($currentData[$behaviorID]->allArray[$w])) {
							// 3 +
							echo "\t".$currentData[$behaviorID]->allArray[$w];
						
						}
						else echo "\t".'0';
					}
					if ($user->nonSmoker) echo "\t".'N';
					else echo "\t".'Y';
				} 
				unset($currentData);
				
		// if ($tempcount >= 3) break;
	}
	
	// Status values returned??
	mysql_close($userMsqlDB);
	
	return array( 0=> $statusArray[0], 1=> $msg);


}

displayReport ( $studyID);


?>
