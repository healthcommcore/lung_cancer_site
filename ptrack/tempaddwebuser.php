<?php
DEFINE( "USERDB", 'lung_cancer_user');

DEFINE( "LIMEDB", 'survey_sub');
require_once( 'includes/initweb.php' );

function mosMakePassword($length=8) {
	$salt 		= "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
	$makepass	= '';
	mt_srand(10000000*(double)microtime());
	for ($i = 0; $i < $length; $i++)
		$makepass .= $salt[mt_rand(0,61)];
	return $makepass;
}


function registerUser( $db, $fname, $username, $email, $password, $startdate) {

	// Other user attributes 
	$userID = 0;
	
	$usertype = 'Registered';
	$gid = 18;		// $acl->get_group_id('Registered','ARO');
	$registerDate = date("Y-m-d H:i:s", $startdate);
	
	echo '<br> New registerDate = ' . $registerDate;
	
	$params = 'editor=\nlanguage=\helpsite=\ntimezone=-5';

	// Remove blanks, HTML tags, slashes
	$fname = trim( strip_tags( stripslashes ( $fname)));
	$username = trim( strip_tags( stripslashes ( $username)));
	$email = trim( strip_tags( stripslashes ( $email)));
	$password = trim( strip_tags( stripslashes ( $password)));

	
	// We won't bother to check email validity
	

	$pwd 				= $password;	// saved text password

	$salt				= mosMakePassword(16);
	$crypt				= md5($password.$salt);
	$password		= $crypt.':'.$salt;		// saved encrypted password

	$sql = "INSERT INTO jos_users (name, username, email, password, usertype, gid, registerDate, params) VALUES
	('". $fname . "','" . $username ."','". $email . "','" . $password . "','" . $usertype . "',". $gid .
	",'" . $registerDate . "','" . $params .
	"')";
	
	// echo $sql;
	$resultID=mysql_query($sql,$db);
	if ($resultID) {
			// Fetch resulting userID

		$userID = mysql_insert_id($db);
		// echo '<br> New joomla = ' . $userID;
	
		// Insert row in jos_core_acl_aro
		$sql = "INSERT INTO jos_core_acl_aro (name, value, section_value) VALUES
		('". $fname . "', $userID ,'". 'users' . "')";
		// echo $sql;
		$resultID=mysql_query($sql,$db);
		if (!$resultID) {
			// db inconsistency - what to do?
			$msg = 'Database Internal consistency error inserting core_acl_ro ' . mysql_error($db);
			return array( 0=> 0, 1=> $msg);
		}
		else {
			$aroID = mysql_insert_id($db);
		
			// Insert row in jos_core_acl_aro
			$sql = "INSERT INTO jos_core_acl_groups_aro_map (group_id, aro_id) VALUES
			($gid , $aroID )";
			// echo $sql;
			$resultID=mysql_query($sql,$db);
			if (!$resultID) {
				$msg = 'Database Internal consistency error inserting core_acl_aro_map: ' . mysql_error($db);
				return array( 0=> 0, 1=> $msg);
			}
	
		}
		

		// echo "<br>Insert successful";
	}
	else return array( 0=> 0, 1=> "Insert failed" . mysql_error($db));

	return array( 0=> $userID, 1=> '');
}

function dbMysqlConnect( $dbname) {

	// print_r($jConfig);

	// Make sure new separate connection!
	// 
    $db=mysql_connect('localhost' ,'lung_cancer_site', '439cwYY39ndB', true);
	if ($db) 
    	mysql_select_db($dbname ,$db);
	else return array( 0=> 0, 1=> 'mysql_connect error '. mysql_error() );
	return array( 0=> $db, 1=> '');
}


function TempAddWebUser( $studyID, $fname, $email, $password, $startdate) {
	// No data checks, assume all is good
	// No email sent
	$statusArray = dbMysqlConnect('hd2_');
	
	if (! $statusArray[0] ) {
		$msg =  'Unable to connect to web site database';
		return array( 0=> 0, 1=> $msg);
	
	}
	else $joomlaMsqlDB = $statusArray[0];
	
	
	$statusArray = dbMysqlConnect('lung_cancer_user');
	if (! $statusArray[0] ) {
		$msg =  'Unable to connect to user database';
		return array( 0=> 0, 1=> $msg);
	
	}
	else $userMsqlDB = $statusArray[0];
	
	
		$statusArray = registerUser($joomlaMsqlDB,  $fname, $email, $email, $password, $startdate);

		if ($statusArray[0] > 0 ) {
			// echo "New user ID = $statusArray[0]";
			
			$joomlaID = $statusArray[0];
			
			$statusArray = initUserInfo ( $userMsqlDB , $studyID, $statusArray[0]); // 
			if ($statusArray[0] != true ) {
				// Error initializing userInfo
				// echo 'debug: error initUserInfo return<br>';
				$msg = 'Database error initializing user information : '.$statusArray[1] ;
				$statusArray[0] = 0;
				return array( 0=> 0, 1=> $msg);
			}
		}
		else {
			// Error registering new user
			$msg = 'Web site user registration error (please contact HCC) : '.$statusArray[1] ;
			$statusArray[0] = 0;
				return array( 0=> 0, 1=> $msg);
		}
		return array( 0=> 1, 1=> '');
	// Status values returned??
	mysql_close($joomlaMsqlDB);
	mysql_close($userMsqlDB);
}


?>

<h2>Test tempaddwebuser</h2>

<?php
/*

 $statusArray = TempAddWebUser( 99999, 'TempAdd', 'TempAdd@test.test', 'test', time());
		if ($statusArray[0] <= 0 ) {
		echo "<br>Error: $msg";
	}
	*/
?>
