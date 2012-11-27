
<?php 


// AddWebUser( studyID, fname, email, pwd, status)
//
//	This function should be called when a new user is registered for the Web Ix (HD2)
//	It should also be called whenever any of the parameters (first name, email, password or status changes)
//
//	1. Check userInfo to see if account creation or update (studyID already exists)
//	2. Account creation: new user in jos_users and associated tables. If username already exists,
//		internal error.
//		If all goes well, insert new userInfo row for user
//	3. Account update: update all data with arguments passed
//	4. Send welcome email to new user

// define ( "JPATH_SITE", '../../');

// require_once( JPATH_SITE .'/includes/hd2/user.php' );
require_once(  'includes/addwebuser.php' );



// >> MOVE INTO SITE FUNCTIONS/ UTILITY FUNCTIONS
// >> Error handling


 
// >> TO DO : Error handling

$studyID = 00001;
$name = 'Dave';
// $username = 'Testuser';
$email = 'dave_rothfarb@dfcil.harvard.edu';
$password = 'abc123';
$status = 1;	// test

$statusArray = addWebUser( $studyID, $name, $email, $password, $status) ;
echo 'Return from addWebUser<br>';
print_r($statusArray);
?>
