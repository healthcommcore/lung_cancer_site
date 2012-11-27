<html>
<title>Process Tracking - User Tracking</title>
<body>
<?php
define ( "JPATH_SITE", getcwd() . '/../../');

require( JPATH_SITE .'/includes/hd2/config.php' );
require( JPATH_SITE .'/includes/hd2/constants.php' );
require( JPATH_SITE .'/includes/hd2/shared.php' );
require( JPATH_SITE .'/includes/hd2/behavior.php' );
require( JPATH_SITE .'/includes/hd2/user.php' );
// require( JPATH_SITE .'/includes/hd2/data.php' );
require( 'data.php' );


global $user;
// REdefine for admin site
// Site-wide variable definitions
global $imageDir;
global $relSiteDir, $siteDir;

// $imageDir = JPATH_SITE. 'images/hd2/tracking/';
// $siteDir= JPATH_SITE. "/images/hd2/tracking/";
$relSiteDir= "../../images/hd2/tracking/";
// require_once( JPATH_SITE .'/includes/hd2/chart.php' );
require_once( 'chart100.php' );
require_once( '../includes/initweb.php' );

global $trackedBehaviors;
	// array of trackgoal objects (defined trackgoal.php)
	//	contains current tracked behaviors, keyed by behaviorID
$trackedBehaviors = array();
$trackedBehaviors[] = 1;
$trackedBehaviors[] = 2;
$trackedBehaviors[] = 3;
$trackedBehaviors[] = 4;
	


function displayWeekly( $studyID) {
global $trackedBehaviors, $behaviorSpecs;

	// Connect to the database
	$msg ='';
	
	$statusArray = dbMysqlConnect('lung_cancer_user');
	if (! $statusArray[0] ) {
		$msg =  'Unable to connect to user database';
		return array( 0=> 0, 1=> $msg);
	
	}
	else $userMsqlDB = $statusArray[0];
	
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
	//	If none, assume smoking
	if (!$user->nonSmoker) $trackedBehaviors[] = 5;

	$currentData = array();	
	for ($i = 1; $i <= count($behaviorSpecs); $i++) {
		$currentData[$i] = new trackData;
	}

	echo '<table cellpadding="0"  cellspacing="0" border="0"><tr><td>';
	echo "<h2>$user->fname</h2>";
	
	$statusArray = getWeekData ($userMsqlDB, $studyID, $currentData, $_SERVER['REQUEST_TIME']);
	// print_r($currentData);

	if ($statusArray[0] == null ) {
		echo 'We are not able to retrieve your weekly tracking data due to a database error: ' . $statusArray[1];
		return;
	}
		
	else {	
		$currentData = $statusArray[0];
		print_r($currentData);

			foreach ($trackedBehaviors as $behaviorID) {
				
			
			
				echo '<tr><td><h4>';
				echo ucwords($behaviorSpecs[$behaviorID]['sname']); 
					echo '</h4>';
		
				
				drawChart($behaviorID, $currentData[$behaviorID]->weekArray, true, 	true, $user, $_SERVER['REQUEST_TIME'], sprintf("%d_%d", $studyID, $behaviorID));
					echo '</td></tr>';
		}
	}	
	echo '</table>';
	
	// Status values returned??
	mysql_close($userMsqlDB);
	
	return array( 0=> $statusArray[0], 1=> $msg);


}


// Retrieve StudyID param
$studyID = trim($_GET['partID']);
if ($studyID != '')  displayWeekly( $studyID);
else echo 'Please provide a studyID';



?>
</body>
</html>
