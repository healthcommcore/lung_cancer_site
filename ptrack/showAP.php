<title>Process Tracking - User Action Plan</title>
<body>
<?php
define ( "JPATH_SITE", getcwd() . '/../');

require( JPATH_SITE .'/includes/hd2/config.php' );
require( JPATH_SITE .'/includes/hd2/constants.php' );
require( JPATH_SITE .'/includes/hd2/shared.php' );
require( JPATH_SITE .'/includes/hd2/behavior.php' );
require( JPATH_SITE .'/includes/hd2/user.php' );
require( JPATH_SITE .'/includes/hd2/data.php' );
require( JPATH_SITE .'/includes/hd2/plan.php' );



// For all behaviors, set up Group ID and Question ID as variables to be used for retrieval
//	(easier to modify if survey questions and order changes

// Action plan page
// 	Pass studyID as parameter



// $imageDir = JPATH_SITE. 'images/hd2/tracking/';
// $siteDir= JPATH_SITE. "/images/hd2/tracking/";
require_once( 'includes/initweb.php' );

// echo 'Action Plan';

function displayAP( $studyID) {
global $trackedBehaviors, $behaviorSpecs;

	// Connect to the database
	$msg ='';
	
	$statusArray = dbMysqlConnect(USERDB);
	if (! $statusArray[0] ) {
		$msg =  'Unable to connect to user database';
		return array( 0=> 0, 1=> $msg);
	
	}
	else $userMsqlDB = $statusArray[0];
	
	// echo "after connect() <br>";

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
	
	
	echo '<table cellpadding="0"  cellspacing="0" border="0"><tr><td>';
	echo "<h2>$user->fname</h2>";

	if ( $user->planBehNo != 0)  {
		echo '<h3>Action Plan has been created</h3>';
	
	}
	
	else {
		echo '<h3>No Action Plan has been created</h3>';
	}

	echo '</table>';
	
	// Status values returned??
	mysql_close($userMsqlDB);
	
	return array( 0=> $statusArray[0], 1=> $msg);


}

// Retrieve StudyID param
$studyID = trim($_GET['partID']);
if ($studyID != '')  displayAP( $studyID);
else echo 'Please provide a studyID';

?>
</body>
</html>
