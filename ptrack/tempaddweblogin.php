<?php
DEFINE( "USERDB", 'lung_cancer_user');



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




?>

<h2>Test tempaddlogins</h2>

<?php
	$statusArray = dbMysqlConnect('lung_cancer_site');
	
	if (! $statusArray[0] ) {
		$msg =  'Unable to connect to web site database';
		echo "<br>Error: $msg";
	
	}
	else $joomlaMsqlDB = $statusArray[0];
	
	
	$statusArray = dbMysqlConnect('lung_cancer_user');
	if (! $statusArray[0] ) {
		$msg =  'Unable to connect to user database';
		echo "<br>Error: $msg";
	
	}
	else $userMsqlDB = $statusArray[0];
	/*

	echo '<h1>Userlogin is</h1>';

	// Retrieve all web users currently in userLogin table
	$sql = "SELECT distinct u.studyID FROM userLogin u";
	
	$result = mysql_query($sql, $userMsqlDB ) ;
	
	$loglist = array();
	if ($result) {
			if  (mysql_num_rows($result) > 0) {

				while( $row = mysql_fetch_assoc($result)) {
						// print_r($row);
						$loglist[] = $row['studyID'];
				}
			}
	}

	else {
			echo "<br>Error:" . mysql_error($userMsqlDB);
	}
	print_r($loglist);
	
	*/


	// Retrieve all web users from enrollment table
	// Remove userlogin ids from overall web list
	echo '<h1>New Web logins</h1>';
	/*
	$sql = "SELECT e.partID, unix_timestamp( e.startDate) as startDate, u.joomlaID  FROM enrollment e, userInfo u  
		WHERE u.studyID = e.partID 
		AND e.ixModality = 1 AND e.partID NOT IN( ";

		for ( $i=0; $i < count($loglist); $i++) {
			if ($i > 0 ) $sql .= ',';
			
			$sql .= $loglist[$i] ; 
		}
		$sql .= ')';
	*/
	$startID = 999999;
	$endID = 999999;

	$sql = "SELECT e.partID, unix_timestamp( e.startDate) as startDate, u.joomlaID  FROM enrollment e, userInfo u  
		WHERE u.studyID = e.partID 
		AND e.ixModality = 1 AND e.partID > $startID AND e.partID < $endID ";

	echo $sql;
	$today = time();
		
	$result = mysql_query($sql, $userMsqlDB ) ;
	
	$idlist = array();
	if ($result) {
			if  (mysql_num_rows($result) > 0) {

				while( $row = mysql_fetch_assoc($result)) {
						print_r($row);
						// !! signal bad start dates
						if ($row['startDate'] == null) {
							echo "<br>Error: no start date for " .$row['partID'];
						
						}
						
						else if 
						 ($row['startDate'] >  $today) {
							echo "<br>Error: start date after today " .$row['partID'];
						
						}
						else {
							$idlist[] = array( 'id'=> $row['partID'], 'start' => $row['startDate'], 'jid' => $row['joomlaID']);
						}
						
				}
			}
	}

	else {
			echo "<br>Error:" . mysql_error($userMsqlDB);
	}
	print_r($idlist);
	
	$secsperday = 86400;
	// Randomly assign a last login date to each new web login, between
	// start date and today
	
	for ( $i=0; $i < count($idlist); $i++) {
		$idlist[$i]['lastlogin'] = rand($idlist[$i]['start'], $today);
		echo "<br>Start  date:  ". $idlist[$i]['start']. " , " .date( "Y-m-d H:i:s", $idlist[$i]['start']);
		echo "<br>Random last login date:  ". $idlist[$i]['lastlogin']. " , " .date( "Y-m-d H:i:s", $idlist[$i]['lastlogin']);
		
		// Set last login date into Joomla users table
		// time is offset by 5 (current timezone diff)
		
		$sql = "UPDATE  jos_users SET lastvisitDate = '" . date( "Y-m-d H:i:s", ($idlist[$i]['lastlogin'] + 5 * $secsperday)) .
			"' WHERE id =" . $idlist[$i]['jid'];
		
		// echo $sql;
		$result=mysql_query($sql,$joomlaMsqlDB);
		if (!$result) {
			echo "<br>Error:" . mysql_error($joomlaMsqlDB);
		}

	}
	

	// for each user, generate random number of logins between their start data and their last login
	// date
	
	
	
	for ( $i=0; $i < count($idlist); $i++) {
		$numdays = $idlist[$i]['lastlogin'] - $idlist[$i]['start']; 
		// login per random # of days out of every 3, then make sure last login
		$logindate = $idlist[$i]['start'];
		echo "<br><br>number of days between start and login date:  ". $numdays/ $secsperday;
		
		while ($logindate < $idlist[$i]['lastlogin'] ) {
			$delta = rand (1,3);
			$logindate = ($delta * $secsperday) + $logindate;
			if ($logindate < $idlist[$i]['lastlogin'] ) {
				// create row in userLogin table
				echo "<br>Random login date:  ". $logindate. " , " .date( "Y-m-d H:i:s", $logindate);
						$sql="INSERT into  userLogin  (studyID, logintime) VALUES (". $idlist[$i]['id']. ",'". 
						date( "Y-m-d H:i:s", $logindate) . "')";
						// echo $sql;
						$result = mysql_query($sql, $userMsqlDB ) ;
			}
			else break;
		
		}
		// create final reow for this user
		$sql="INSERT into  userLogin  (studyID, logintime) VALUES (". $idlist[$i]['id']. 
							",'" . date( "Y-m-d H:i:s",$idlist[$i]['lastlogin']). "')";
		// echo $sql;
		$result = mysql_query($sql, $userMsqlDB ) ;
						
		if (!$result) {
			echo "<br>Error:" . mysql_error($userMsqlDB);
		}
	}	

	// Status values returned??
	mysql_close($joomlaMsqlDB);
	mysql_close($userMsqlDB);
?>
