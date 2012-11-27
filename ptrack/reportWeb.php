<?php
define ( "JPATH_SITE", getcwd() . '/../');

require( JPATH_SITE .'/includes/hd2/config.php' );
require( JPATH_SITE .'/includes/hd2/shared.php' );
require( JPATH_SITE .'/includes/hd2/user.php' );
require( JPATH_SITE .'/includes/hd2/data.php' );
require( JPATH_SITE .'/includes/hd2/plan.php' );
require( 'includes/report_function.php' );
require_once( 'includes/initweb.php' );

global $testusers;
$testusers = array(
	50000,
	50001,
	50002,
	50003,
	50192,
	50193
);

function filterTestusers ( $sql) {
global $testusers;

	$sql .= ' (';

		for ( $i=0; $i < count($testusers); $i++) {
			if ($i > 0 ) $sql .= ',';
			
			$sql .= $testusers[$i] ; 
		}
	$sql .= ')';
	return $sql;


}

function displayReport() {
global $trackedBehaviors, $behaviorSpecs;
global $_SERVER;

	// Connect to the database
	$msg ='';
	
	$statusArray = dbMysqlConnect(USERDB);
	if (! $statusArray[0] ) {
		$msg =  'Unable to connect to user database';
		return array( 0=> 0, 1=> $msg);
	
	}
	else $userMsqlDB = $statusArray[0];
	
	// echo "after connect() <br>";
	// Total Ix users
	$sql = "SELECT ixModality, dateIXChange, unix_timestamp(startDate) as startDate, dateWithdrew FROM ". USERDB . ".enrollment where ixModality is not null"; 
	$sql .= " AND partID NOT IN "; 
	$sql = filterTestusers( $sql);
	//echo $sql;
	//echo time();
	$result = mysql_query($sql, $userMsqlDB ) ;
	// $printusers = 0;
	$webusers = 0;
	$switchusers = 0;
	$withdrew = 0;
	$currentusers = 0;
	$ended = 0;
	if ($result) {
		while ($row = mysql_fetch_assoc($result)) {
					//print_r($row);
					if ($row['ixModality'] == 1) {
					
						$webusers++;
						if ($row['dateWithdrew'] != '0000-00-00') $withdrew++;
						else {
						// Compare start date with today's date
							// $weeksSinceStart = ceil( ($_SERVER['REQUEST_TIME'] - $row['startDate']) /60/7 );
							$weeksSinceStart = ceil( (time() - $row['startDate']) / (86400 *7) );
							//echo '<br>'.$row['startDate'];
							//echo ' '. $weeksSinceStart;
							if ($weeksSinceStart > 26) {
								$ended++;
							}
							else $currentusers++;
							
						}
					}
					// if ($row['ixModality'] == 2) $printusers = $row['c'];
					if ($row['dateIXChange'] != '0000-00-00') $switchusers++;
		}
	}
	else {
		// error db
		printf( "Error retrieving Ix enrollment data:  %s\n",mysql_error( $userMsqlDB));
		return;
	}
	
	// User Logins
	// $sql = "SELECT count(studyID) as c, studyID FROM ". USERDB . ".userLogin group by studyID WHERE studyID NOT IN "; 
	$sql = "SELECT count(studyID) as c, studyID FROM ". USERDB . ".userLogin WHERE studyID NOT IN "; 
	$sql = filterTestusers( $sql);
	$sql .= " group by studyID"; 
	// echo $sql;
	$result = mysql_query($sql, $userMsqlDB ) ;
	$userlogins = array();
	if ($result) {
		while ($row = mysql_fetch_assoc($result)) {
					//print_r($row);
					$userlogins[] = $row['c'];
		}
	}
	else {
		// error db
		printf( "Error retrieving user login data:  %s\n",mysql_error( $userMsqlDB));
		return;
	}
	$user2logins = 0;
	$user8logins = 0;
	$totallogins = 0;
	foreach ($userlogins as $userrec) {
		if ($userrec > 1) {
			$user2logins++;
		}
		if ($userrec >= 8) {
			$user8logins++;
		}
		$totallogins+= $userrec;
	}
	
	// Tracking
	// $sql = "SELECT count(studyID) as c FROM ". USERDB . ".userTrack group by studyID"; 
	$sql = "SELECT count(studyID) as c FROM ". USERDB . ".userTrack WHERE studyID NOT IN ";  
	$sql = filterTestusers( $sql);
	$sql .= " group by studyID"; 
	// echo $sql;
		
	$result = mysql_query($sql, $userMsqlDB ) ;
	if ($result) {
		$trackcount = mysql_num_rows($result);
	}
	else {
		// error db
		printf( "Error retrieving user tracking data:  %s\n",mysql_error( $userMsqlDB));
		return;
	}


	// $sql = "SELECT count(studyID) as c, studyID, behaviorID FROM ". USERDB . ".userTrack group by behaviorID, studyID"; 
	$sql = "SELECT count(studyID) as c, studyID, behaviorID FROM ". USERDB . ".userTrack WHERE studyID NOT IN ";  
	$sql = filterTestusers( $sql);
	$sql .= " group by behaviorID, studyID"; 
	// echo $sql;
		
	$result = mysql_query($sql, $userMsqlDB ) ;
	$usertracks = array();
	if ($result) {
		while ($row = mysql_fetch_assoc($result)) {
			if ($row['c'] >= 16)
				$usertracks[$row['behaviorID']] ++;
		}
	}
	else {
		// error db
		printf( "Error retrieving user tracking data:  %s\n",mysql_error( $userMsqlDB));
		return;
	}
	
	// print_r( $usertracks);

	
	// Action Plan
	// $sql = "SELECT planCount, studyID FROM ". USERDB . ".userInfo WHERE planCount > 0   "; 
	$sql = "SELECT planCount, studyID FROM ". USERDB . ".userInfo WHERE planCount > 0  AND studyID NOT IN "; 
	$sql = filterTestusers( $sql);
	// echo $sql;
	$result = mysql_query($sql, $userMsqlDB ) ;
	$userlist = array();
	if ($result) {
		while ($row = mysql_fetch_assoc($result)) {
			$userlist[] = $row['planCount'];
		}
	}
	else {
		// error db
		printf( "Error retrieving action plan data:  %s\n",mysql_error( $userMsqlDB));
		return;
	}
	
	$usercount = 0;
	foreach ($userlist as $userrec) {
		if ($userrec > 1) $usercount++;
	}
	?>
	<table cellpadding="5"  cellspacing="0" style="	
	border-top:solid 1px #000000;
	border-left:solid 1px #000000;padding:0px">
	<tr><td colspan="2" class="web title">
	<h3>Enrollment</h3>
	</td>
	</tr>

	<tr><td class="web">
	Total participants ever enrolled in web
	</td>
	<td class="data web">
	<?php echo $switchusers + $webusers ?>
	</td>
	</tr>

	<tr><td class="web">
	Total participants currently enrolled in web
	</td>
	<td class="data web">
	<?php echo $currentusers ?>
	</td>
	</tr>
	<tr><td class="web">
	Total web participants who have ended study normally
	</td>
	<td class="data web">
	<?php echo $ended ?>
	</td>
	</tr>

	<tr><td class="web">
	Total participants who switched from web to print
	</td>
	<td class="data web">
	<?php echo $switchusers ?>
	</td>
	</tr>

	<tr><td class="web">
	Total web participants who withdrew
	</td>
	<td class="data web">
	<?php echo $withdrew ?>
	</td>
	</tr>

	<tr><td colspan="2" class="web title">
	<h3>Logins</h3>
	</td>
	</tr>

	<tr><td class="web">
	Number of participants who have ever logged in to site
	</td>
	<td class="data web">
	<?php echo count($userlogins) ?>
	</td>
	</tr>
	
	<tr><td class="web">
	Average logins per participant who logged in
	</td>
	<td class="data web">
	<?php echo $totallogins/count($userlogins) ?>
	</td>
	</tr>

	<tr><td class="web">
	Number of participants who have logged in at least 2 times
	</td>
	<td class="data web">
	<?php echo $user2logins; ?>
	</td>
	</tr>

	
	<tr><td class="web">
	Number of participants who have logged in at least 8 times
	</td>
	<td class="data web">
	<?php echo $user8logins; ?>
	</td>
	</tr>

	<tr><td colspan="2" class="web title">
	<h3>Tracking</h3>
	</td>
	</tr>

	<tr><td class="web">
	Number of participants who have tracked at least once
	</td>
	<td class="data web">
	<?php echo $trackcount ?>
	</td>
	</tr>

	<tr><td class="web">
	Number of participants who have tracked PA 16 or more times
	</td>
	<td class="data web">
	<?php echo $usertracks[1]; ?>
	</td>
	</tr>

	<tr><td class="web">
	Number of participants who have tracked FV 16 or more times
	</td>
	<td class="data web">
	<?php echo $usertracks[2]; ?>
	</td>
	</tr>
	<tr><td class="web">
	Number of participants who have tracked RM 16 or more times
	</td>
	<td class="data web">
	<?php echo $usertracks[3]; ?>
	</td>
	</tr>
	<tr><td class="web">
	Number of participants who have tracked MV 16 or more times
	</td>
	<td class="data web">
	<?php echo $usertracks[4]; ?>
	</td>
	</tr>
	<tr><td class="web">
	Number of participants who have tracked SM 16 or more times
	</td>
	<td class="data web">
	<?php echo $usertracks[5]; ?>
	</td>
	</tr>

	<tr><td colspan="2" class="web title">
	<h3>Action Plans</h3>
	</td>
	</tr>

	<tr><td class="web">
	Number of participants who have ever made an action plan
	</td>
	<td class="data web">
	<?php echo count($userlist) ?>
	</td>
	</tr>
	
	<tr><td class="web">
	Number of participants who have ever made an action plan more than once
	</td>
	<td class="data web">
	<?php echo $usercount; ?>
	</td>
	</tr>
	<?php

	// User emails
	$sql = "SELECT distinct studyID FROM ". USERDB . ".userSentemails"; 
	//echo $sql;
		
	$result = mysql_query($sql, $userMsqlDB ) ;
	$userlist = array();
	if ($result) {
		$numemails = mysql_num_rows($result);
	}
	else {
		// error db
		printf( "Error retrieving action user email data:  %s\n",mysql_error( $userMsqlDB));
		return;

	}
	?>

	<tr><td colspan="2" class="web title">
	<h3>Buddy Emails</h3>
	</td>
	</tr>
	
	<tr><td class="web">
	Number of participants who have sent a buddy  email
	</td>
	<td class="data web">
	<?php echo $numemails; ?>
	</td>
	</tr>
	
	</table>

	<?php
	mysql_close($userMsqlDB);
	return array( 0=> $statusArray[0], 1=> $msg);
}


?>
<!DOCTYPE html PUBLIC "-//W3C//ulD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/ulD/xhtml1-strict.uld">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Web Statistics Report</title>
<link rel="stylesheet" type="text/css" href="includes/main.css" />
</head>

<body>
<!-- Begin Wrapper -->
   <div id="wrapper">
   
         <!-- Begin Header -->

         <div id="header">
		 <br><br>
		<h2 align = "center">Web Statistics Report</h2>  
			<br><p align = "right"><form method="POST" action="login.php" >
	     <input type = "submit" value = "log out" size = 100></form></p>   
		 </div>
		 <!-- End Header -->
		 
         <!-- Begin Faux Columns -->
		 <div id="faux">
		 
		       <!-- Begin Left Column -->
		       <div id="leftcolumn1">
		 			<?php
						$menu = getAdMenu();
						echo $menu;
					?>
		       </div>

		       <!-- End Left Column -->
			   
			   <!-- Begin Right Column -->
		       <div id="rightcolumn">

				<?php displayReport(); ?>

<div class="clear"></div>
			   
		       </div>
		       <!-- End Right Column -->
			   
			   <div class="clear"></div>
			   
         </div>	   
         <!-- End Faux Columns --> 
		 
   </div>
   <!-- End Wrapper -->
</body>
</html>
