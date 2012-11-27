<?php
// This file is shared by both the web site (Joomla-based) and administrative and cronjobs.
//	The main issue is the mySQL database connection and access to the database configuration
//

DEFINE( "USERDB", 'lung_cancer_user');

DEFINE( "LIMEDB", 'survey_sub');
DEFINE( "GRAPHFONT", '/Library/Fonts/arial');

// FUNCTION: 
//	 MySQL db connection

// Return array 
//	0 => 0 if error, and 1=> error message
//	0 => db handle, 1=> ''
require_once( JPATH_SITE .'/configuration.php' );
require_once( JPATH_SITE .'/includes/hd2/config.php' );

function dbMysqlConnect( $dbname) {

	$jConfig = new JConfig;	// Obtain current configuration parameters
	// print_r($jConfig);

	// Make sure new separate connection!
	// 
    $db=mysql_connect($jConfig->host ,$jConfig->user, $jConfig->password, true);
	if ($db) 
    	mysql_select_db($dbname ,$db);
	else return array( 0=> 0, 1=> 'mysql_connect error '. mysql_error() );
	return array( 0=> $db, 1=> '');
}


// If unable to connect to database, or missing survey data, set smoking data to default (smoker)
function getSmokingData( $user)  { 
	
	$jConfig = new JConfig;	// Obtain current configuration parameters

	$user->nonSmoker = false; 

    $surveyDB=mysql_connect($jConfig->host ,$jConfig->user, $jConfig->password, true);
	
	if (!$surveyDB ) {
	 	return array( 0=> null, 1=> 'Unable to connect to Survey Database:<br>'. mysql_error( $surveyDB) );
	}
	
    if (! mysql_select_db(LIMEDB,$surveyDB) ) {
	 	return array( 0=> null, 1=> 'Unable to connect to Survey Database:<br>'. mysql_error( $surveyDB) );
	}


    $sql = "SELECT " . SURVEY_ID. "X4X12 ," . SURVEY_ID. "X4X13," . SURVEY_ID. "X4X14 FROM lime_survey_". SURVEY_ID . " WHERE ". SURVEY_ID . "X1X30=$user->userID order by ID DESC LIMIT 1";	
	
	$result = mysql_query($sql, $surveyDB ) ;
	
	if ($result) {
		// $numCig = 0;
		$row = mysql_fetch_row($result);
		{
			// print_r($row);
			// No : non-smoker
			if ($row[0] == 1) $user->nonSmoker = true;
			else {
				if ($row[0] == 2) {
					// If answer = yes, check next question before deciding
			
			
			
					if ($row[1] == 1) $user->nonSmoker = true;
					else {
						$user->nonSmoker = false;
						if ($row[1] == 2) {
							// Yes
							// get # cigarettes smoked per day. If specified, > smoker, and use
							//	the highest value provided if more than one result entry 
							// If not specified, ignore
							if ($row[3] > 0) {
								$user->cigSmoked = $row[3];
								// echo '<br>#cig smoked = '. $row[1];
							}
						}
					}
				}
			}

		}

	}
	
	else return array( 0=> null, 1=> 'Unable to connect to retrieve your survey data from the database:<br>'. mysql_error( $surveyDB) );
	// print_r($user);


	mysql_close( $surveyDB );
	return array( 0=> $user, 1=> '');

}

?>
