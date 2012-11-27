<?php


// Checks whether this particular user already exists in the userInfo table
// Returns Joomla ID if found
function checkUserInfo ( $userMsqlDB , $studyID) {
	$sql =  "SELECT joomlaID FROM lung_cancer_user.userInfo WHERE studyID=$studyID LIMIT 1";

	$result = mysql_query($sql, $userMsqlDB ) ;
	
	if ($result) {
		$row = mysql_fetch_row($result);
		if ($row != false) {
			// echo 'checkUserInfo: ';
			print_r($row[0]);
			return $row[0];
		}
	}
	return false;

}

// Initialize userInfo data for new studyID
//	by default, startDate is current time
function initUserInfo ( $userMsqlDB , $studyID, $joomlaID) {
		// $old_level = error_reporting(0);	// turn off email error reporting
	   
	$sql =  "INSERT INTO lung_cancer_user.userInfo (studyID, joomlaID) VALUES ( $studyID, $joomlaID)";
	// echo $sql;
	$result = mysql_query($sql, $userMsqlDB ) ;
	
	if ($result) {
			// echo '<br>num affected rows = '. mysql_affected_rows($userMsqlDB);
			if (mysql_affected_rows($userMsqlDB)>0) {

				return array( 0=> 1, 1=> '');
			}
	}
		// error_reporting($old_level);
	$msg = "Unable to initialize userInfo: " . mysql_error($userMsqlDB);
	return array( 0=> 0, 1=> $msg);

}

// 
// Separate function from web site, because it initializes data without Joomla ID
// Returns array 
//	0 => user data, or null if error OR user does not exist
//	1 => error msg if any, else ''
//
function initUserDataFromStudyID($userMsqlDB, $studyID) {
		$sql = "SELECT u.studyID, u.rafflepoints, u.planReason, u.planReasonopt, u.planSupport, u.planBehNo, j.email, j.name, unix_timestamp(j.registerDate) as startDate FROM lung_cancer_user.userInfo u INNER JOIN hd2.jos_users j ON u.joomlaID = j.id WHERE studyID=$studyID";
		// echo $sql;	
		$result = mysql_query($sql, $userMsqlDB ) ;
	
		if (!$result) {
			// Database error
			$msg = sprintf( _HCC_TKG_DB_SELECT . ": %s\n", mysql_error( $userMsqlDB));
			return array( 0=> NULL, 1 => $msg);
		}
		else {
			$row = mysql_fetch_assoc($result);
			if ($row != false) {
				// print_r($row);
				$user->userID=  $row['studyID'];

				$user->startDate = $row['startDate'];
				
				// $user->startDate= $row['registerDate']; 
				// Adjust start date to first minute of the day
				$user->startDate=mktime(0,1,0,date("n",$user->startDate),date("j",$user->startDate),date("Y",$user->startDate));
				$user->points= $row['rafflepoints'];
				$user->planReason= $row['planReason'];
				$user->planReasonopt= $row['planReasonopt'];
				$user->planSupport= $row['planSupport'];
				$user->planBehNo= $row['planBehNo'];
				$user->email =$row['email'];
				$user->fname =$row['name'];
			}
			else {
				// query ok, but no rows - no data for this user
				return array( 0=> NULL, 1 => '');
			}
	
			// echo date("l F j, Y, g:i a", $user->startDate );
			
			// Survey data
			$statusArray= getSmokingData( $user);
			if ($statusArray[0] == null) return array( 0=> NULL, 1 => $statusArray[1]);

			$user = $statusArray[0];

			// This would not be necessary if stored date were correct
			$user->weeksSinceStart = ceil( ($_SERVER['REQUEST_TIME'] - $user->startDate) /_HCC_SECONDS_PER_DAY/7 );
			
			// print_r($user);
			return array( 0=>  $user, 1 => '');
		}

}

?>