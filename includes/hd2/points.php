<?php

// Error message if unable to add points, but site can go on
//	Note that the error message may appear in the wrong place
//	
function addPoints( $userMsqlDB, $userID, $add) {
	// Add points
	$sql="UPDATE userInfo SET rafflepoints=rafflepoints+" . $add . " WHERE studyID=$userID";
 	$result = mysql_query($sql, $userMsqlDB ) ;
	if (!$result) {
		printf( _HCC_TKG_DB_UPDATE . ": %s\n",mysql_error());
	}
}

?>