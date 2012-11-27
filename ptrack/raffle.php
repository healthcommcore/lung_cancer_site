<?php

// Get the current list of users to save into an array

// BETTER  ERROR HANDLING
echo getcwd() . "\n";

// define ( "JPATH_SITE", '/Library/WebServer/Documents/live_sites/hd2/');
define ( "JPATH_SITE", getcwd() . '/../');
require_once( JPATH_SITE .'/includes/hd2/shared.php' );
require_once( 'includes/connection.php' );

$draw = $_REQUEST['draw'];
?>

<!DOCTYPE html PUBLIC "-//W3C//ulD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/ulD/xhtml1-strict.uld">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Raffle Drawing</title>
<link rel="stylesheet" type="text/css" href="includes/main.css" />
</head>

<body>
<!-- Begin Wrapper -->
   <div id="wrapper">
   
         <!-- Begin Header -->

         <div id="header">
		 <br><br>
		<h2 align = "center">Raffle drawing</h2>  
			<br><p align = "right"><form method="POST" action="login.php" >
	     <input type = "submit" value = "log out" size = 100></form></p>   
		 </div>
		 <!-- End Header -->
		 
         <!-- Begin Faux Columns -->
		 <div id="faux">
		 
		       <!-- Begin Left Column -->
		       <div id="leftcolumn1">
		 			<?php
						$menu = getMenu();
						echo $menu;
					?>
		       </div>

		       <!-- End Left Column -->
			   
			   <!-- Begin Right Column -->
		       <div id="rightcolumn">
               
               
               

<?php
if ($draw != '') {

	$statusArray = dbMysqlConnect(USERDB);
	if (! $statusArray[0] ) {
	?>
 		<table border = "5" align = "left" valign = "top" cellpadding =10 width="650">
		<tr><td>
	<?php
		echo  '<br>Unable to connect to user database: '. $statusArray[1];
	?>
		</td></tr></table>
	<?php
	}
	else $userMsqlDB = $statusArray[0];

		// Exclude users who have already finished and who have already won

		$sql = "SELECT userInfo.studyID, userInfo.rafflepoints FROM userInfo INNER JOIN enrollment ON userInfo.studyID = enrollment.partID WHERE userInfo.activeStatus = 1 AND enrollment.dateWinraff is NULL";	
		$result = mysql_query($sql, $userMsqlDB ) ;
	
		if ($result) {
			// There are results
			while($row = mysql_fetch_assoc($result) ) {
				// print_r($row);
				
				$userList[$row['studyID']] = $row['rafflepoints'];
			}
		}
		
		$allSlots=array();
		foreach ($userList as  $userID => $points) {
			// echo "<br>$userID: " . $points . ' (points)';
			for ($i = 0; $i < ($points); $i++) {
				$allSlots[] = $userID;
			}
		}
        $numEntries=count($allSlots) ;
	?>
 		<table border = "5" align = "left" valign = "top" cellpadding =10 width="650">
		<tr><td>
	<?php
		if ($numEntries > 0) {
			// array starts from 0 to $numEntries-1
			$winningSlot =rand(0,$numEntries-1);
			$winnerID = $allSlots[$winningSlot];
			
			// Retrieve winner first name, last name for display
			$sql = "SELECT ptFName, ptLName FROM part_info  WHERE partID = $winnerID";
			$result = mysql_query($sql, $userMsqlDB ) ;
		
			if ($result) {
			// There are results
				$row = mysql_fetch_assoc($result) ;
				$winFname = $row['ptFName'];
				$winLname = $row['ptLName'];
				// print_r($row);
			}
			
	
			echo '<p><br><br>Total points = ' . $numEntries;
			echo "<br>The web winner is:  <b>$winFname $winLname</b> ($winnerID) </p>";
			
			echo '<p><br><br>Click <a href="edit_enrollment_two.php?partID='. $allSlots[$winningSlot].'" target="_self">here</a> to Register Raffle winner (update the 
			\'Date Won Raffle\' field)</p><br><br>';
			
		}
		else {
			echo '<br>There are no active web participants (who have not already won) for this raffle';
		}
		unset($allSlots);
		unset($userList);
		mysql_close( $userMsqlDB);
	?>
		</td></tr></table>
	<?php
}

else {
	?>
 		<table border = "5" align = "left" valign = "top" cellpadding =10 width="650" height="200">
		<tr><td>
		<form action="raffle.php?draw=true" method="post" >

		
		<input class="right" type="submit" value="Draw a Winner">
		</form>
		</td></tr></table>
	<?php
}
?>

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
