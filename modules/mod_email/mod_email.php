<?php
defined( '_JEXEC' ) or die( 'Restricted Access.' );


/**
* @version 1.0 $
* @package Email
*/
 
require( JPATH_SITE .'/includes/hd2/constants.php' );
require( JPATH_SITE .'/modules/mod_email/mod_email.html.php' );
require( JPATH_SITE .'/includes/hd2/siteFunctions.php' );
?>

<script language="javascript" type="text/javascript">

function isBlank(val) {
	if (val == null || val == "") return true;
	for(var i=0;i<val.length;i++) {
		if ((val.charAt(i)!=' ')&&(val.charAt(i)!="\t")&&(val.charAt(i)!="\n")&&(val.charAt(i)!="\r")){return false;}
		}
	return true;
		
}

function processEmail(form) {
	var msg = "Please specify ";
	 	
			
	if (isBlank(form.mm_email.value) || !isEmail(form.mm_email.value)) {
		msg += "a valid email\n";
	} 
	if (isBlank(form.mm_support.value)) {
		msg += "<?php echo _HCC_PP_ERR_SUPPORT; ?>\n";
	} 
		
	if (msg != "Please specify ") {
		alert(msg);
		return false;	
	}
	else	return true;

}
</script>


<?php
// Retrieve page params so we can reconstruct it
global $option;
global $view;
global $itemid;
global $id;
global $task;

global $my, $user, $userDB;

global $BuddyMsgSubject;
global $BuddyMsgBody;

$BuddyMsgSubject = "I'm heading in a Healthy Direction! Want to help?";
$BuddyMsgBody = "
Hello! I've just joined a project through my health care provider that's called <b>Healthy Directions</b>. Healthy Directions is going to help me make healthy changes, and <b>I'm really excited about it.</b> 
<br><br>
A <b>big</b> part of Healthy Directions is asking for help. That's because the support of friends and family can help make health changes easier! I think you'd be a great <b>Healthy Directions Buddy</b>. You don't need to do a lot - just be there for me and encourage me to meet my health goals! 
<br><br>
You can learn more at <a href=\"http://Buddies.TrackMyChanges.org\">Buddies.TrackMyChanges.org</a>, the <b>web site that's for buddies</b> of Healthy Directions participants. You can also get in touch with me soon, and we can talk more about it. 
<br><br>
%s
<br><br>
Thanks!<br><br>
%s
";

// Retrieve URL parameters
// Joomla
$option = trim( JRequest::getVar(  'option'));
$id = trim( JRequest::getVar(  'id'));
$itemid = trim( JRequest::getVar(  'Itemid'));
$view = trim( JRequest::getVar(  'view', null));
$task = trim( JRequest::getVar(  'task', null));
$errmsg = JRequest::getVar( 'errmsg', '');

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

// Function sendemail()
// Arguments:
//	$toemails:
//	$fromemail:
//	$subject:
//	$message:
// Returns mail() status
function sendemail( $toemails, $fromemail, $subject, $message) {
		$message = formatmsg($message);
		$status = mail ($toemails, $subject, $message, 
	  		"From: $fromemail\nContent-Type: text/html; charset=iso-8859-1", "-f".$fromemail);
		return $status;
}


switch ($task) {
	case 'send':
		// place in helper code?
		// BEFORE WE DO ANYTHING, INITIALIZE OR RETRIEVE USER INFO
		//	Can be obained from Joomla DB
		$my = JFactory::getUser();
		if (($user = initData()) == NULL) return;	// ?
	
		$values = array();	// will hold results for values[1..3]
		
		$mm_email = trim(JRequest::getVar( 'mm_email', 0));
		$mm_support = trim(JRequest::getVar( 'mm_support', 0));
		$mm_message = trim(JRequest::getVar( 'mm_message', ''));
		
		if (  ( $mm_email == '') || ( $mm_support == '') ) {
			
	        HTML_email::displayEmail('Please complete all the fields in the form');
			break;
		}
		$emailsArray = explode(',', $mm_email );
		
		if (sizeof( $emailsArray) > 1) {
		        HTML_email::displayEmail('Please provide only one email address');
			break;
		}

		$emailsArray = explode(' ', $mm_email);
		if (sizeof( $emailsArray) > 1) {
		        HTML_email::displayEmail('Please provide only one email address');
			break;
		}
		
		// Verify that there is only one email specified (no spaces, commas)
		$pos = strpos($mm_email, '@');

		if ($pos === false) {
	        HTML_email::displayEmail('Please provide a valid email address');
			break;
		
		}

		$toemail = $mm_email;
		$fromemail = $my->email;	
		
		// Retrieve user information:
		//	First name  (name)
		// 	Email as From email
		$mm_subject = $BuddyMsgSubject; 
		
		$fullmsg = sprintf( $BuddyMsgBody, $mm_message, $my->name);

		$status = sendemail( $toemail, $fromemail , $mm_subject, $fullmsg);
		if ($status) {
			$sendemail_msg = "Your email has been sent.";
		
			// Track this information in the database
			$sql="INSERT INTO userSentemails (studyID, companion ) VALUES ($user->userID, '$mm_support' ) ";
			$userDB->setQuery($sql);	
			$resultID=$userDB->query();
			if (!$resultID) {
				//  error log on hd2 logfile
				
				error_log( "\n". date("Y-m-d H:i:s ") . _HCC_TKG_DB_INSERT . ':'. $userDB->getErrorMsg(), 3, '/var/www/logs/hd2/hd2.log');
			}
		}
		else $sendemail_msg = "There was an error. Your email was not sent.";
	
        HTML_email::sendEmail($sendemail_msg);
		
		break;
	case '':
	default:
        HTML_email::displayEmail('');
}
?>


