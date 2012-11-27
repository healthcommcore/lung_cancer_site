<title>Process Tracking - User Tailored Feedback Report</title>
<body>
<?php
define ( "JPATH_SITE", getcwd() . '/../');

require_once( JPATH_SITE .'/includes/hd2/config.php' );
require_once( JPATH_SITE .'/includes/hd2/constants.php' );
require_once( JPATH_SITE .'/includes/hd2/shared.php' );
require_once( JPATH_SITE .'/includes/hd2/user.php' );
require_once( 'includes/initweb.php' );
require_once( 'includes/survey.php' );



// For all behaviors, set up Group ID and Question ID as variables to be used for retrieval
//	(easier to modify if survey questions and order changes

// Action plan page
// 	Pass studyID as parameter



function displayTFR( $studyID) {
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

 	//$statusArray = initUserDataFromStudyID($userMsqlDB, $studyID);
	// Must retrieve username information from the part_info table instead of Joomla
	//	to include Print Ix users 
		$sql = "SELECT ptFName FROM lung_cancer_user.part_info WHERE partID=$studyID";
		// echo $sql;	
		$result = mysql_query($sql, $userMsqlDB ) ;
	
		if (!$result) {
			// Database error
			echo '<b>There was a technical error - please seek technical help</b>: '. mysql_error( $userMsqlDB);		
			return;
		}
		else {
			$row = mysql_fetch_assoc($result);
			if ($row != false) {
				// print_r($row);
				$username=  $row['ptFName'];
			}
			else {
				echo '<b>There is no data for this user.  The user is probably not in the Ix.</b>';
			}
		}
	

	// print_r($user);
	
	echo '<table cellpadding="0"  cellspacing="0" border="0"><tr><td>';
	echo "<h2>$username</h2>";
	getSurveyUserData($studyID);	
	echo '</table>';
	
	// Status values returned??
	mysql_close($userMsqlDB);
	
	return array( 0=> $statusArray[0], 1=> $msg);


}

// Retrieve StudyID param
$studyID = trim($_GET['partID']);
if ($studyID != '')  displayTFR( $studyID);
else echo 'Please provide a studyID';

?>
</body>
</html>
