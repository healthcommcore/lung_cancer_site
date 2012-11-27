<?php 
//  cron job to do some integrity checks on the database
//
//	1. PTS enrollment  = Joomla start date (day, not exact time) for web Ix
//	2. All PTS web Ix participants are in Joomla and userInfo tables
//	3. Users who are past 27 weeks should be web inactive
//  4. All userInfo study IDs should exist in PTS, and are webIx
//	5. userInfo studyIDs and JoomlaIDs are unique (no dups)
//	6. a. All jos_users (registered) are in userInfo table
//	   b. All userInfo are in  jos_users (registered)  table
//  7. survey (how much to do?)
//		Completed token # -> survey with studyID exists
//		survey studyID exists in PTS and BL_result = 1 (Consented)
//		survey studyID unique across survey


 

// define ( "JPATH_SITE", '../../');
define ( "JPATH_SITE", '/var/www/help.trackmychanges.org/');

require_once( JPATH_SITE .'/includes/hd2/user.php' );

require_once( JPATH_SITE .'/includes/hd2/shared.php' );
require_once( JPATH_SITE .'configuration.php' );



global $FromEmail;
$FromEmail = 'trackmychanges@partners.org';
global $WelcomeMsgSubject;
global $WelcomeMsgBody;

$WelcomeMsgSubject = 'Welcome to Healthy Directions!';
$WelcomeMsgBody = "Hello %s,
<br><br>
Welcome to Healthy Directions! This program is for certain Harvard Vanguard patients who want to make healthy changes. The Healthy Directions web site makes it quick and easy to track your health habits, so you can see how you’re doing. It only takes 5 minutes to track! 
<br><br>
You'll even earn raffle points every time you visit. Log on to <a href=\"http://www.TrackMyChanges.org?source=MassEmailSource&studyID=%s\">www.TrackMyChanges.org</a> now to visit Healthy Directions and start earning raffle points. Every month you'll have the chance to win Healthy Directions prizes like movie tickets, magazine subscriptions, and more! 
Here's more of what to expect from the site:

<ul><li>Healthy Directions Plans that can help you reach your goals    
</li>   
<li>helpful hints for making healthy changes
</li>   
<li>delicious recipes 
</li>   
<li>local resources 
</li>   
<li>the Healthy Talk column
</li>   
<li>and more</li></ul>
<p>Your login information for the Healthy Directions web site - <br />
   username: %s <br />
   password: %s</p> 
<p>
Sincerely, 
<br><br>
Healthy Directions
<br><br>
P.S.:
<br>
<ul>
<li>If you have any trouble or questions, please email us at <a href=\"mailto:TrackMyChanges@partners.org\">TrackMyChanges@partners.org</a>.
</li>   
<li>We don't want to become spam! Please add our email address to your system, so your Healthy Directions emails won't be blocked.
</li>   
<li>Please do not reply to this email. It was sent to you by the Healthy Directions computer system.</li></ul> 
";


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

// Note: we are sending one email at a time. Should be ok, since there shouldn't be that many emails
// sent on any given day

function sendemail( $toemails, $fromemail, $subject, $message) {
		$message = formatmsg($message);
		$old_level = error_reporting(0);	// turn off email error reporting
		$status = mail ($toemails, $subject, $message, 
	  		"From: $fromemail\nContent-Type: text/html; charset=iso-8859-1", "-f".$fromemail);

		error_reporting($old_level);
		echo 'status = '. $status;
		return $status;

}
		$tpassword = 'rcarney';
		$fname = 'Robert';
		$studyID = 14710;
		$email = 'triviabob@gmail.com';
		
		echo '<br>NEW CODE Sending to email = '.$email;
		
				$emailbody = sprintf( $WelcomeMsgBody, $fname, $studyID, $email, $tpassword);
				$status = sendemail( $email, $FromEmail, $WelcomeMsgSubject, $emailbody);
?>