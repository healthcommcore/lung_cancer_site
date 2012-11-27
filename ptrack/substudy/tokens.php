<html>
<title>Add survey tokens</title>
<body>
<?php
define ( "JPATH_SITE", getcwd() . '/../../');

require( JPATH_SITE .'/includes/hd2/config.php' );
require( JPATH_SITE .'/includes/hd2/constants.php' );
require( JPATH_SITE .'/includes/hd2/shared.php' );

// REdefine for admin site
// Site-wide variable definitions

require_once( '../includes/initweb.php' );


    $surveyDB=mysql_connect('localhost' ,'root', '439cwYY39ndB', true);

	// Connect to the database
    mysql_select_db('survey_sub' ,$surveyDB);
	if (!$surveyDB ) {
		echo("Unable to connect to Survey Database:<br>".mysql_error());
		return null;
	}
 
  

	// for ($i = 50000; $i < 50001; $i++) {

	for ($i = 50000; $i < 52000; $i++) {
		$sql = "INSERT into lime_tokens_14872 (firstname, lastname, token, language, sent, completed, emailstatus ) values ( 'User', '$i',  '$i', 'en', 'N',  'N', 'OK')"; 
		// echo $sql;
		
		$result = mysql_query($sql, $surveyDB ) ;
		// if (!$resultID) {
			// db inconsistency -
			// echo mysql_error($surveyDB);
			// break;
			// }
	}
	mysql_close( $surveyDB );


?>
</body>
</html>
