<?php 


// AddWebUser( studyID, fname, email, pwd, status)
//
//	This function should be called when a new user is registered for the Web Ix (HD2)
//	It should also be called whenever any of the parameters (first name, email, password or status changes)
//
//	The logic has been changed to creating/updating Joomla jos_users and userInfo tables separately, in order
//	to maximize success, in case we run into unexpected (and undiagnosed) problems
//	1. Check userInfo to see if account creation or update (studyID already exists)
//	2. Joomla Account creation: new user in jos_users and associated tables. If username already exists,
//		internal error.
//		If all goes well, insert new userInfo row for user
//	3. Account update: update all data with arguments passed
//	4. UserInfo entry creation or update (status -> inactive)
//	5. If create new user AND new userInfo entry, send welcome email to new user
//	
//	All error conditions (partial or total) do NOT generate welcome emails.
//
//  Returns an array
//	Array[0] = status: 1 if success, 0 if error, 2 if warning.  Both 0 and 2 messages should be displayed so
//		that RA can notify appropriate staff member
//	Array[1] = error message if any. Else ''
//
//	Error conditions: these are all internal consistency or database errors and MUST be manually corrected
//		Orgin of error should also be located if database problems
//		- username(which uses email) or email already exists
//		- invalid password (unlikely, since PT code already verifies this)
//		- database errors - connect, insert, update various tables
//	Warning condition:
//		- unable to send welcome email message

define ( "JPATH_INC", '../');

define ( "JPATH_SITE", getcwd() . '/../');

require_once( JPATH_INC .'/includes/hd2/user.php' );
require_once( JPATH_INC .'/includes/hd2/shared.php' );
require_once( JPATH_SITE .'configuration.php' );
require_once( 'initweb.php' );


// Email constant definitions
global $FromEmail;
$FromEmail = 'helptrackmychanges@partners.org';
global $WelcomeMsgSubject;
global $WelcomeMsgBody;

$WelcomeMsgSubject = 'Welcome to Healthy Directions!';
$WelcomeMsgBody = "Hello %s,
<br><br>
Welcome to Healthy Directions! This program is for certain Harvard Vanguard patients who want to make healthy changes. The Healthy Directions web site makes it quick and easy to track your health habits, so you can see how you’re doing. It only takes 5 minutes to track! 
<br><br>
You'll even earn raffle points every time you visit. Log on to <a href=\"http://help.TrackMyChanges.org?source=MassEmailSource&studyID=%s\">help.TrackMyChanges.org</a> now to visit Healthy Directions and start earning raffle points. Every month you'll have the chance to win Healthy Directions prizes like movie tickets, magazine subscriptions, and more! 
Here's more of what to expect from the site:

<ul><li>Healthy Directions Plans that can help you reach your goals    
</li>   
<li>helpful hints for making healthy changes
</li>   
<li>delicious recipes 
</li>   
<li>local resources 
</li>   
<li>the Healthy Talk column
</li>   
<li>and more</li></ul>
<p>Your login information for the Healthy Directions web site - <br />
   username: %s <br />
   password: %s</p> 
<p>
Sincerely, 
<br><br>
Healthy Directions
<br><br>
P.S.:
<br>
<ul>
<li>If you have any trouble or questions, please email us at <a href=\"mailto:HelpTrackMyChanges@partners.org\">HelpTrackMyChanges@partners.org</a>.
</li>   
<li>We don't want to become spam! Please add our email address to your system, so your Healthy Directions emails won't be blocked.
</li>   
<li>Please do not reply to this email. It was sent to you by the Healthy Directions computer system.</li></ul> 
";

/** FROM JOOMLA
* Random password generator
* @return password
*/
function mosMakePassword($length=8) {
	$salt 		= "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
	$makepass	= '';
	mt_srand(10000000*(double)microtime());
	for ($i = 0; $i < $length; $i++)
		$makepass .= $salt[mt_rand(0,61)];
	return $makepass;
}


// Function formatmsg($message)
// Argument: $message
//
// Reformat messages so that max # chars per line (\n separator) in email is about 70 chars
// Use blank as word separator (for simplicity) - replace with \n as necessary
// Returns: new formatted string
function formatmsg($message) {
		$words = explode(' ', $message); // each entry in array $words is a word from the string
		$retmsg = "";
		$linechars = 0;			// start with first char
		foreach($words as $word)
		{
			// Keep blanks, newlines as in original
			// Check to see if there are any newlines in the text, starting from the END
			if ( ($pos = strrpos($word, "\n")) != false) {
				$linechars = strlen($word-$pos);	// new start
				$retmsg .= ' '.$word;
			}
			else if (($linechars + strlen($word)) > 70 ) {
				// line too long, insert newline
				$retmsg .= "\n".$word;	
				$linechars = 0;
			}
			else {
				$linechars += (strlen($word) +1);	// add space
				if ($retmsg != '') $retmsg .= ' '.$word;
				else $retmsg = $word;
			}
		}
		return $retmsg; // return the new string
}

// returns true if successful,
//	false otherwise

function sendemail( $toemails, $fromemail, $subject, $message) {
// Temp
		// $toemails = 'therese_lung@dfci.harvard.edu';
		
		
		$message = formatmsg($message);
		$old_level = error_reporting(0);	// turn off email error reporting
		$status = mail ($toemails, $subject, $message, 
	  		"From: $fromemail\nContent-Type: text/html; charset=iso-8859-1", "-f".$fromemail);

		error_reporting($old_level);
		return $status;
}

function notifyErrorAdmin ( $msg ) {
	sendemail( 'therese_lung@dfci.harvard.edu','therese_lung@dfci.harvard.edu',  'AddWebUser technical error', $msg);
}

 
// Note that Joomla and database is case-insensitive with regard to username, email, etc...
function checkValidJoomla( $username, $password) {
	// Joomla 1.5 has no minimum length requirements on username and password
	// Check username and password character set
	if ( (! preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $username)) 
		|| (ereg('[^A-Za-z0-9]', $password)) ) {
			return false;
	}
	return true;
}

// Convention:
//	password = 
//	username = email
//	name = first name (used for welcome message)
//	NEW Joomla user creation
//	returns status in status array:
//		[0 => ] > 0 if ok, new Joomla ID
//		[0 => 0] 	if errror
//		[1 => error msg	 if error
function registerUser( $db, $fname, $username, $email, $password) {
	$jConfig = new JConfig;	// Obtain current configuration parameters

	// Other user attributes 
	$usertype = 'Registered';
	$gid = 18;		// $acl->get_group_id('Registered','ARO');
	$registerDate = date("Y-m-d H:i:s");
	
	$params = 'editor=\nlanguage=\helpsite=\ntimezone=-5';





	$sql = "INSERT INTO ". $jConfig->db .".jos_users (name, username, email, password, usertype, gid, registerDate, params) VALUES
	('". $fname . "','" . $username ."','". $email . "','" . $password . "','" . $usertype . "',". $gid .
	",'" . $registerDate . "','" . $params .
	"')";
	
	// echo $sql;
	$resultID=mysql_query($sql,$db);
	if ($resultID) {
			// Fetch resulting userID

		$userID = mysql_insert_id($db);
	
		// Insert row in jos_core_acl_aro
		$sql = "INSERT INTO ". $jConfig->db .".jos_core_acl_aro (name, value, section_value) VALUES
		('". $fname . "', $userID ,'". 'users' . "')";
		$resultID=mysql_query($sql,$db);
		if (!$resultID) {
			// db inconsistency -
			$msg = 'Database Internal consistency error inserting core_acl_ro: ' . mysql_error($db);
			error_log( "\n". date("Y-m-d H:i:s ") . $msg, 3, '/var/www/logs/hd2/hd2.log');
			return array( 0=> 0, 1=> $msg);
		}
		else {
			$aroID = mysql_insert_id($db);
			// Insert row in jos_core_acl_aro
			$sql = "INSERT INTO ". $jConfig->db .".jos_core_acl_groups_aro_map (group_id, aro_id) VALUES
			($gid , $aroID )";
			// echo $sql;
			$resultID=mysql_query($sql,$db);
			if (!$resultID) {
				$msg = 'Database Internal consistency error inserting core_acl_aro_map: ' . mysql_error($db);
				error_log( "\n". date("Y-m-d H:i:s ") . $msg, 3, '/var/www/logs/hd2/hd2.log');
				return array( 0=> 0, 1=> $msg);
			}
	
		}
	}
	else return array( 0=> 0, 1=> "Insert failed" . mysql_error($db));

	return array( 0=> $userID, 1=> '');
}

function updateUser( $userMsqlDB, $joomlaID, $fname, $username, $email, $password, $status) {
	$jConfig = new JConfig;	// Obtain current configuration parameters

	// only deactivate user possible in updates, because user can be deactivated in joomla after 26 wks, but still
	//	considered active by PT database
	
	if (!$status) {
		// echo 'UpdateUser BLOCK';
		$block = ', block = 1 ';
	}
	
	$sql = "UPDATE  ". $jConfig->db .".jos_users SET name = '". $fname . "', username = '" . $username ."', email = '". $email . "', 
		password = '" . $password . "'". $block. " WHERE id = $joomlaID";
	
	// echo $sql;
	$resultID=mysql_query($sql,$userMsqlDB);
	if (!$resultID) {
			$msg = "Database error updating user information: " . mysql_error($userMsqlDB);
			error_log( "\n". date("Y-m-d H:i:s ") . $msg, 3, '/var/www/logs/hd2/hd2.log');
			return array( 0=> 0, 1=> $msg);
	}
	
	return array( 0=> 1, 1=> '');

}


function addWebUser( $studyID, $fname, $email, $password, $status, $createflag) {
global $WelcomeMsgSubject;
global $WelcomeMsgBody;
global $FromEmail;
	$jConfig = new JConfig;	// Obtain current configuration parameters


	// echo "Call addwebuser with studyID= $studyID, first name = $fname, email = $email, pwd= $password, status=$status, createflag=$createflag<br>";
	$msg ='';
	
	// Remove blanks, HTML tags, slashes
	$fname = trim( strip_tags( stripslashes ( $fname)));
	// $username = trim( strip_tags( stripslashes ( $username)));
	$email = trim( strip_tags( stripslashes ( $email)));
	$username = $email;
	$password = trim( strip_tags( stripslashes ( $password)));

	// Check that all input values have been specified if status = 1
	if ($status == 1) {
		if ( ($email == '') || ($fname == '') || ($password == '')) {
				$msg = "Error: The email, first name and/or password is not specified";
				return array( 0=> 0, 1=> $msg);
		
		}
		
		if (! checkValidJoomla( $username, $password) ) {
			$msg =  "The username or password contains characters other than letters and numbers";
			return array( 0=> 0, 1=> $msg);
		}
		// We won't bother to check email validity

	}
	
	// Connect to the database
	$statusArray = dbMysqlConnect(USERDB);
	if (! $statusArray[0] ) {
		$msg =  'Unable to connect to user database';
		return array( 0=> 0, 1=> $msg);
	
	}
	else $userMsqlDB = $statusArray[0];
	

	if ( $createflag ) {
		// Not in userInfo - now make sure that it's not in Joomla table - in case this username or email has already been
		//	created
		//	(username = email for HD2)
	
		$sql = "SELECT id FROM ". $jConfig->db .".jos_users WHERE username = '". $username. "' OR email = '". $email. "'";
		// echo $sql;
		
		$resultID=mysql_query($sql,$userMsqlDB);
		if (!$resultID) {
			// db error
			$msg = 'Database select error: ' . mysql_error($userMsqlDB);
			error_log( "\n". date("Y-m-d H:i:s ") . $msg, 3, '/var/www/logs/hd2/hd2.log');
			mysql_close($userMsqlDB);
			return array( 0=> 0, 1=> $msg);
		}
		
	
		if (mysql_num_rows($resultID)>0) {
				// If it exists, unexpected but we'll continue in update mode (instead of worrying about errors)
				$row = mysql_fetch_row($resultID);
				// echo 'Joomla user already exists: ';
				// print_r($row[0]);
				$joomlaID = $row[0];
				// log this but continue
				// Notify admin
				$msg = 'User registration warning: the username or email already exists for ' . $email . ' - change to update mode';
				notifyErrorAdmin($msg);

				error_log( "\n". date("Y-m-d H:i:s ") .  $msg , 3, '/var/www/logs/hd2/hd2.log');
				
				// Go into update mode to recover from error 
				$createflag = false;
				/*
				$msg = 'User registration warning: please SAVE the record again';
				mysql_close($userMsqlDB);
				return array( 0=> 0, 1=> $msg);
				*/
		}
	}
	
	
	// Encrypt password using same algorithm as Joomla
	$tpassword = $password;	// saved text password for email
	
	$pwd 				= $password;	// saved text password

	$salt				= mosMakePassword(16);
	$crypt				= md5($password.$salt);
	$password		= $crypt.':'.$salt;		// saved encrypted password
	

	
	
	if (! $createflag) {
		// already exists, update
		// Retrieve current Joomla ID
		
		$joomlaID = checkUserInfo( $userMsqlDB, $studyID);
		if ($joomlaID == 0 ) {
				$msg = "Database Internal consistency error, this user does not exist in userInfo for studyID $studyID";
				error_log( "\n". date("Y-m-d H:i:s ") . $msg, 3, '/var/www/logs/hd2/hd2.log');
				mysql_close($userMsqlDB);
				return array( 0=> 0, 1=> $msg);
		}
		
		// echo 'Update Joomla user ' . $joomlaID;
		$statusArray = updateUser($userMsqlDB,  $joomlaID, $fname, $email, $email, $password, $status );
		if (! $statusArray[0] ) {
			$msg =  'Error updating Joomla user information: '.$statusArray[1] ;
			error_log( "\n". date("Y-m-d H:i:s ") . $msg, 3, '/var/www/logs/hd2/hd2.log');
			mysql_close($userMsqlDB);
			return array( 0=> 0, 1=> $msg);
		
		}	
	}
	
	
	else {
		// echo 'Create Joomla user';
		// doesn't exist, create
		// email = username

		$statusArray = registerUser($userMsqlDB,  $fname, $email, $email, $password);
		
	
		if ($statusArray[0] > 0 ) {
			// echo "New user ID = $statusArray[0]";
			$joomlaID = $statusArray[0];
		}
		
		else {
			// Error registering new user
			$msg = 'Web site user registration error (please contact HCC) : '.$statusArray[1] ;
			error_log( "\n". date("Y-m-d H:i:s ") . $msg, 3, '/var/www/logs/hd2/hd2.log');
			mysql_close($userMsqlDB);
			return array( 0=> 0, 1=> $msg);
		}
	}
	
	// This is not really necessary, except for integrity checking
	// Now check userInfo table - either Joomla ID or studyID match.  If either matches but not the other, we have a problem
	$sql =  "SELECT joomlaID, studyID FROM hd2_user_sub.userInfo WHERE studyID=$studyID OR joomlaID = $joomlaID";
	// echo $sql;

	$result = mysql_query($sql, $userMsqlDB ) ;
	
	
	if ($result) {
		/*
		// Make sure only 1 row found
		if (mysql_num_rows($result)> 1) {
			// Internal consistency error
				$msg = "Database Internal consistency error, too many userInfo entries for joomlaID $joomlaID or studyID $studyID";
				error_log( "\n". date("Y-m-d H:i:s ") . $msg, 3, '/var/www/logs/hd2/hd2.log');
				mysql_close($userMsqlDB);
				return array( 0=> 0, 1=> $msg);
		}
		*/
		$row = mysql_fetch_row($result);
		if ($row != false) {
			// Make sure existing entry matches both joomlaID and studyID
			// Update only for status == false
			if (($row[0] != $joomlaID) || ($row[1] != $studyID) ) {
				// echo 'checkUserInfo: ';
				// print_r($row[0]);
				// Internal consistency error
				$msg = "Database Internal consistency error, unexpected userInfo entry for joomlaID $joomlaID or studyID $studyID";
				notifyErrorAdmin($msg);


				error_log( "\n". date("Y-m-d H:i:s ") . $msg, 3, '/var/www/logs/hd2/hd2.log');
				$msg = 'User registration warning: please SAVE the record again';
				return array( 0=> 0, 1=> $msg);
			}
			
		}
	}
	else {
		$msg = 'Database select error: ' . mysql_error($userMsqlDB);
		error_log( "\n". date("Y-m-d H:i:s ") . $msg, 3, '/var/www/logs/hd2/hd2.log');
		$statusArray[0] = 0;
		return array( 0=> 0, 1=> $msg);
	}
	
	// Create or update UserInfo
	if ($createflag) {

			// echo 'create new entry pair';
			// Create new userInfo entry for this studyID - Joomla ID pair
			$statusArray = initUserInfo ( $userMsqlDB , $studyID, $joomlaID); // 
			if ($statusArray[0] > 0 )  {
				// echo 'Sending email...<br>';	
				// Send email 
				$emailbody = sprintf( $WelcomeMsgBody, $fname, $studyID, $email, $tpassword);
				$status = sendemail( $email, $FromEmail, $WelcomeMsgSubject, $emailbody);

				if ( $status != 1) {
					$msg = 'Error: Unable to send welcome email to: ' . $email;
					error_log( "\n". date("Y-m-d H:i:s ") . $msg, 3, '/var/www/logs/hd2/hd2.log');
					$statusArray[0] = 2;
				}
				else {
					$statusArray[0] = 1;
				}
			}
			else if ($statusArray[0] == 0 ) {
				// Error initializing userInfo
				$msg = 'Database error initializing user information : '.$statusArray[1] ;
				error_log( "\n". date("Y-m-d H:i:s ") . $msg, 3, '/var/www/logs/hd2/hd2.log');
				$statusArray[0] = 0;
			}
	}
	// update userInfo data ONLY if changing to inactive ($status = 0)
	else {
		if ($status == 0 ) {
				// echo 'update existing entry pair<br>';
		
				$sql =  "UPDATE  hd2_user_sub.userInfo SET activeStatus=$status WHERE studyID = $studyID AND joomlaID = $joomlaID";
				// echo $sql;
				
				$result = mysql_query($sql, $userMsqlDB ) ;
				
				if ($result) {	
					// Pb with checking mysql_affected_rows() so assume all ok
					// Don't check # rows because if no change, may be 0
					//if (mysql_affected_rows($userMsqlDB)>0) {
						mysql_close($userMsqlDB);
						return array( 0=> 1, 1=> '');
					//}
				}
				else {
					// error_reporting($old_level);
					$msg = "Database error updating user information userInfo: " . mysql_error($userMsqlDB);
					error_log( "\n". date("Y-m-d H:i:s ") . $msg, 3, '/var/www/logs/hd2/hd2.log');
					mysql_close($userMsqlDB);
					return array( 0=> 0, 1=> $msg);
				}
		}
	}
	
	mysql_close($userMsqlDB);
	
	
	return array( 0=> $statusArray[0], 1=> $msg);


}


?>