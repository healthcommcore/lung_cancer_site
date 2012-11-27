<?php

// Site-wide variable definitions
global $imageDir;
global $relSiteDir, $siteDir;

$imageDir = 'images/hd2/tracking/';
$siteDir= JPATH_SITE. "/images/hd2/tracking/";
$relSiteDir="images/hd2/tracking/";

require_once( JPATH_SITE .'/includes/hd2/shared.php' );

// FUNCTION: DB CONNECTIONS
// Note that error handling automatically performmed and intercepted by Joomla,
//	so no point trying to retrieve or display any error messages.
function dbJConnect( $dbname) {
	// echo $dbname;
	$conf =& JFactory::getConfig();
	
	$host 		= $conf->getValue('config.host');
	$user 		= $conf->getValue('config.user');
	$password 	= $conf->getValue('config.password');
	$database	= $dbname; 
	$prefix 	= '';// $conf->getValue('config.dbprefix');
	$driver 	= $conf->getValue('config.dbtype');
	
	$options	= array ( 'driver' => $driver, 'host' => $host, 'user' => $user, 'password' => $password, 'database' => $database, 'prefix' => $prefix );

	$db =& JDatabase::getInstance( $options );
	
    return $db;

}

// Initialize, if not already set:
//	Global database handles
//	Global $user data structure
//		Called by all modules that require data from user db
//		- mod_email
//		- mod_plan
//		- mod_tracking
//		- mod_raffle
//	Each module should close mysql_() database connection
//		before exit
// ERROR HANDLING
//		Error message and die() so that user can't do anything with web site (?)
//	Or redirect to error page
//	
function initData() {
	require_once( JPATH_SITE .'/includes/hd2/user.php' );
	global $userDB;
	global $userMsqlDB;
	global $user;
	
	// Connect to the database and Initialize global user data
	// Database initialization

	$conf =& JFactory::getConfig();
	$dbname= $conf->getValue('config.db');
	
	// Initialize database IF not already set
	if (! $userDB) {
		$userDB = dbJConnect(USERDB);
		if (!$userDB) {
			// Log error
			error_log( "\n". date("Y-m-d H:i:s ") .  'Error: web site unable to connect to Joomla Database using JDatabase', 3, '/var/www/logs/hd2/hd2.log');
			die( 'The site is temporarily offline (Unable to retrieve the application data)');
		}
	}

	// mysql() db handle
	if (!$userMsqlDB) {
		$statusArray = dbMysqlConnect(USERDB);
		if (! $statusArray[0] ) {
			error_log( "\n". date("Y-m-d H:i:s ") .  'Error: web site unable to connect to Joomla Database using mysql()', 3, '/var/www/logs/hd2/hd2.log');
			die( 'The site is temporarily offline (Unable to retrieve the application data):' . $statusArray[1]);
		
		}
		else $userMsqlDB = $statusArray[0];
	}
		
	// Initialize UserSettings table if necessary
	// fill in user data structure with current information for easy access
	// Look up registration date for this user
	//		userID
	//		list of behaviors (array)
	//		start date
	//
	
	// Possible to call this function twice on same web page due to multiple modules
	if ($user) return $user;
	
	if (! $user) {
		$statusArray = initUserData($userMsqlDB) ;


		if ($statusArray[0] == null) {
			// echo $statusArray[1];
			die('Restricted Access');
		}
		else {
			$user = $statusArray[0];
		}

	}
	// echo '<br>Index:user = ';
	// print_r($user);
	if ($user == NULL) {
		die('Restricted Access');

		// echo 'Missing user data';
	}
	if ($user->weeksSinceStart > 26 && $user->userID != 50488) {
		// DONE PAGE
		echo 	'<meta HTTP-EQUIV="REFRESH" content="0; url=http://www.trackmychanges.org/done.html">';
		die();
	}
	return $user;

}

?>
