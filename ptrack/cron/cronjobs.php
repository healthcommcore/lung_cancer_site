<?php 
// Daily cron job to accomplish several automated tasks
//
//	1. deactivate all web users who have ended their Ix (26 wks past startDate) 
//		set block = 1 in jos_users (for studyID -> JoomlaID)
//	2. identify all AVR/SMS users who need to receive their weekly message
//		generate text file for automated transfer
//	3. send email reminders to active web users according to protocol
//


 

// define ( "JPATH_SITE", '../../');
define ( "JPATH_SITE", '/var/www/html/lung_cancer_site');

require_once( JPATH_SITE .'/includes/hd2/user.php' );

require( JPATH_SITE .'/includes/hd2/behavior.php' );
require_once( JPATH_SITE .'/includes/hd2/config.php' );
require_once( JPATH_SITE .'/includes/hd2/shared.php' );
require_once( JPATH_SITE .'/includes/hd2/data.php' );
require_once( JPATH_SITE .'configuration.php' );

// Static definitions
//	RA email address for web->print
$RAemail = 'onehundredhd2reports@partners.org';


$RAemailSubject = 'HD2 Automated reminder: participants to roll over from web to print';
$RAemailBody = 'Here are the study IDs of the participants who need to be rolled over from web to print';

$RAemailSubjectTLR = 'HD2 Automated reminder: participants who track less than 3 days on week 1';
$RAemailBodyTLR = 'Here are the study IDs of the participants who tracked less than 3 days on week 1';
$RAemailSmokerTLR = '
				Number of cigarettes smoked = _______ cigarettes
				';

$fromEmail = 'dave_rothfarb@dfci.harvard.edu';


$CRfilepath = "/var/www/html/logs/hd2/";

// Participant Email reminders 
global $emailReminders;

global $userMsqlDB, $user, $rightNow, $behaviorSpecs;
global $currentData, $trackedBehaviors;

$emailRemindersTLR = array(
1 => array('Healthy Directions & Tracking!',
	"Hello %s,

We're looking forward to helping you meet your health goals!  And research shows that tracking, or recording your progress, really helps - it's a great way to see how things are going.  If you're keeping track of one goal, research also shows it's not much harder to keep track of others.  

Tracking is so important that we want to help you make it a daily habit.  We'll help you get started by tracking for you–just let us know how your day went.

				Number of steps taken = _____ steps
				
				Number of servings of fruits and vegetables eaten = ____ servings 
				
				Number of servings of red meat eaten = _______ servings
				%s
				Took a multivitamin today (yes/no) = 

Remember that you can even keep up with your healthy habits on your own by tracking your progress directly on the Healthy Directions web site at any time:

Help.TrackMyChanges.org
		Your user name is: %s
		Your password is: %s

The web site also has lots of healthy tips and ideas - a great place to start is the \"Healthy Directions & Me\" section - check it out!  If you'd like to contact us, please call 617-582-7295.

Good luck and thanks for your participation,

The Healthy Directions team

This electronic message and attached files may contain information that is confidential.  This information is solely for the use of the individual(s) and entity(s) named as recipients.  If you are not the intended recipient, you are hereby notified that any disclosure, copying, distribution, or other use of the contents of this electronic message is strictly prohibited.  If you have received this electronic message in error, please notify the sender immediately to arrange for return of the document."),
2 => array('Healthy Directions Tracking!',
	"Hello %s,

We hope that tracking is going well for you!  It's great to track, since studies show that people who keep up with their healthy habits find it easier to make long-term healthy changes!!  Let us know how your day went.

				Number of steps taken = _____ steps
				
				Number of servings of fruits and vegetables eaten = ____ servings 
				
				Number of servings of red meat eaten = _______ servings
				%s
				Took a multivitamin today (yes/no) = 

Remember that you can even keep up with your healthy habits on your own by tracking your progress directly on the Healthy Directions web site at any time:

Help.TrackMyChanges.org
		Your user name is: %s
		Your password is: %s

The web site is also updated regularly with stories about real people's experiences as they're trying to make healthy changes – check it out!   If you'd like to contact us, please call 617-582-7295.

Keep on tracking!  

The Healthy Directions team

This electronic message and attached files may contain information that is confidential.  This information is solely for the use of the individual(s) and entity(s) named as recipients.  If you are not the intended recipient, you are hereby notified that any disclosure, copying, distribution, or other use of the contents of this electronic message is strictly prohibited.  If you have received this electronic message in error, please notify the sender immediately to arrange for return of the document."),
3 => array('Healthy Directions Tracking!',
	"Hello %s,

Hope you've had success today with tracking your healthy habits!  Let us know how your day went.

				Number of steps taken = _____ steps
				
				Number of servings of fruits and vegetables eaten = ____ servings 
				
				Number of servings of red meat eaten = _______ servings
				%s
				Took a multivitamin today (yes/no) = 

Remember that you can even keep up with your healthy habits on your own by tracking your progress directly on the Healthy Directions web site at any time:

Help.TrackMyChanges.org
		Your user name is: %s
		Your password is: %s

The web site also has lots of great recipes to help you meet your goals – check them out!  There also is a weekly blog from one of our health coaches, and lists of local resources. 

If you'd like to contact us, please call 617-582-7295.

Keep on tracking!  

The Healthy Directions team

This electronic message and attached files may contain information that is confidential.  This information is solely for the use of the individual(s) and entity(s) named as recipients.  If you are not the intended recipient, you are hereby notified that any disclosure, copying, distribution, or other use of the contents of this electronic message is strictly prohibited.  If you have received this electronic message in error, please notify the sender immediately to arrange for return of the document."),
4 => array('Healthy Directions Tracking!',
	"Hello %s,

It takes time to make changes, so don't give up if you're not meeting your goals yet.  Just remember to stick with it and that tracking will help you get where you want to be!  Let us know how your day went.

				Number of steps taken = _____ steps
				
				Number of servings of fruits and vegetables eaten = ____ servings 
				
				Number of servings of red meat eaten = _______ servings
				%s
				Took a multivitamin today (yes/no) = 

The web site can help you come up with a plan – check out the \"My Plan\" section!: 

Help.TrackMyChanges.org
		Your user name is: %s
		Your password is: %s

Remember that you can even keep up with your healthy habits on your own by tracking your progress directly on the Healthy Directions web site at any time.  If you'd like to contact us, please call 617-582-7295.

Keep on tracking!  

The Healthy Directions team

This electronic message and attached files may contain information that is confidential.  This information is solely for the use of the individual(s) and entity(s) named as recipients.  If you are not the intended recipient, you are hereby notified that any disclosure, copying, distribution, or other use of the contents of this electronic message is strictly prohibited.  If you have received this electronic message in error, please notify the sender immediately to arrange for return of the document."),
5 => array('Healthy Directions Tracking!',
	"Hello %s,

Hope you had a great day!  Let us know how your day went. 

				Number of steps taken = _____ steps
				
				Number of servings of fruits and vegetables eaten = ____ servings 
				
				Number of servings of red meat eaten = _______ servings
				%s
				Took a multivitamin today (yes/no) = 

If you missed a day of tracking, that's okay.  Just try to get back to it as soon as you can.  You can even keep up with your healthy habits on your own by tracking your progress directly on the Healthy Directions web site at any time: 

Help.TrackMyChanges.org
		Your user name is: %s
		Your password is: %s

Need more ideas for reaching your health goals?  Check out The Healthy Tips section of the web site – it's a great resource.  If you'd like to contact us, please call 617-582-7295.

Keep on tracking!  

The Healthy Directions team

This electronic message and attached files may contain information that is confidential.  This information is solely for the use of the individual(s) and entity(s) named as recipients.  If you are not the intended recipient, you are hereby notified that any disclosure, copying, distribution, or other use of the contents of this electronic message is strictly prohibited.  If you have received this electronic message in error, please notify the sender immediately to arrange for return of the document."),
6 => array('Healthy Directions Tracking!',
	"Hello %s,

Did you meet your health goals for today?  Let us know how your day went.

				Number of steps taken = _____ steps
				
				Number of servings of fruits and vegetables eaten = ____ servings 
				
				Number of servings of red meat eaten = _______ servings
				%s
				Took a multivitamin today (yes/no) = 

Remember that you can even keep up with your healthy habits on your own by tracking your progress directly on the Healthy Directions web site at any time:

Help.TrackMyChanges.org
		Your user name is: %s
		Your password is: %s

Changing health habits can be hard—but friends and family can help if you get a buddy on board!  The Healthy Tips section has lots of ideas about how buddies can support you as you make healthy changes – check it out!    If you'd like to contact us, please call 617-582-7295.

Keep on tracking!  

The Healthy Directions team

This electronic message and attached files may contain information that is confidential.  This information is solely for the use of the individual(s) and entity(s) named as recipients.  If you are not the intended recipient, you are hereby notified that any disclosure, copying, distribution, or other use of the contents of this electronic message is strictly prohibited.  If you have received this electronic message in error, please notify the sender immediately to arrange for return of the document."),
7 => array('Healthy Directions Tracking!',
	"Hello %s,

Hope you had a great day working toward your health goals!  Let us know how your day went.

				Number of steps taken = _____ steps
				
				Number of servings of fruits and vegetables eaten = ____ servings 
				
				Number of servings of red meat eaten = _______ servings
				%s
				Took a multivitamin today (yes/no) = 

Don't get discouraged if you have a setback.  Making changes is really difficult!  The Healthy Directions web site has lots of ideas to help you get back on track, - check them out! 

Remember that you can even keep up with your healthy habits on your own by tracking your progress directly on the Healthy Directions web site at any time.  If you'd like to contact us, please call 617-582-7295.

Help.TrackMyChanges.org
		Your user name is: %s
		Your password is: %s

Keep on tracking!  

The Healthy Directions team

This electronic message and attached files may contain information that is confidential.  This information is solely for the use of the individual(s) and entity(s) named as recipients.  If you are not the intended recipient, you are hereby notified that any disclosure, copying, distribution, or other use of the contents of this electronic message is strictly prohibited.  If you have received this electronic message in error, please notify the sender immediately to arrange for return of the document."),
8 => array('Healthy Directions Tracking!',
	"Hello %s,

Hope things went well today with working toward your health goals!  Let us know how your day went.

				Number of steps taken = _____ steps
				
				Number of servings of fruits and vegetables eaten = ____ servings 
				
				Number of servings of red meat eaten = _______ servings
				%s
				Took a multivitamin today (yes/no) = 

Remember that you can even keep up with your healthy habits on your own by tracking your progress directly on the Healthy Directions web site at any time:

Help.TrackMyChanges.org
		Your user name is: %s
		Your password is: %s

After tracking directly on the Healthy Directions web site, you can see reports and get feedback on your progress – check it out!

Keep on tracking!  

The Healthy Directions team

This electronic message and attached files may contain information that is confidential.  This information is solely for the use of the individual(s) and entity(s) named as recipients.  If you are not the intended recipient, you are hereby notified that any disclosure, copying, distribution, or other use of the contents of this electronic message is strictly prohibited.  If you have received this electronic message in error, please notify the sender immediately to arrange for return of the document."),
9 => array('Healthy Directions Tracking!',
	"Hello %s,

We hope that tracking is going well for you!  Let us know how your day went.

				Number of steps taken = _____ steps
				
				Number of servings of fruits and vegetables eaten = ____ servings 
				
				Number of servings of red meat eaten = _______ servings
				%s
				Took a multivitamin today (yes/no) = 

Remember that you can even keep up with your healthy habits on your own by tracking your progress directly on the Healthy Directions web site at any time:

Help.TrackMyChanges.org
		Your user name is: %s
		Your password is: %s

The web site has lots of quick and easy recipes - check them out!  If you'd like to contact us, please call 617-582-7295.

Keep on tracking!  

The Healthy Directions team

This electronic message and attached files may contain information that is confidential.  This information is solely for the use of the individual(s) and entity(s) named as recipients.  If you are not the intended recipient, you are hereby notified that any disclosure, copying, distribution, or other use of the contents of this electronic message is strictly prohibited.  If you have received this electronic message in error, please notify the sender immediately to arrange for return of the document."),
10 => array('Healthy Directions Tracking!',
	"Hello %s,

It can be hard to make changes, so don't give up if you're not meeting your goals yet.  Just keep working toward your goal.  Tracking will help you get where you want to be!  Let us know how your day went.

				Number of steps taken = _____ steps
				
				Number of servings of fruits and vegetables eaten = ____ servings 
				
				Number of servings of red meat eaten = _______ servings
				%s
				Took a multivitamin today (yes/no) = 

Remember that you can even keep up with your healthy habits on your own by tracking your progress directly on the Healthy Directions web site at any time:

Help.TrackMyChanges.org
		Your user name is: %s
		Your password is: %s

The web site can also help you come up with ways to have your family and friends help you make healthy changes.   You can also make a plan to help you meet your goals in the \"My Plan\" section – check it out!  If you'd like to contact us, please call 617-582-7295.

Keep on tracking!  

The Healthy Directions team

This electronic message and attached files may contain information that is confidential.  This information is solely for the use of the individual(s) and entity(s) named as recipients.  If you are not the intended recipient, you are hereby notified that any disclosure, copying, distribution, or other use of the contents of this electronic message is strictly prohibited.  If you have received this electronic message in error, please notify the sender immediately to arrange for return of the document."),
11 => array('Healthy Directions Tracking!',
	"Hello %s,

Did you meet your health goals for today?  Let us know how your day went.

				Number of steps taken = _____ steps
				
				Number of servings of fruits and vegetables eaten = ____ servings 
				
				Number of servings of red meat eaten = _______ servings
				%s
				Took a multivitamin today (yes/no) = 

It can be easier to make changes with help from friends and family.  Ask a buddy to help!  The Healthy Tips section has lots of ideas about how buddies can support you as you make healthy changes – check it out! 

Help.TrackMyChanges.org
		Your user name is: %s
		Your password is: %s

Remember that you can even keep up with your healthy habits on your own by tracking your progress directly on the Healthy Directions web site at any time.  If you'd like to contact us, please call 617-582-7295.

Keep on tracking!

The Healthy Directions team

This electronic message and attached files may contain information that is confidential.  This information is solely for the use of the individual(s) and entity(s) named as recipients.  If you are not the intended recipient, you are hereby notified that any disclosure, copying, distribution, or other use of the contents of this electronic message is strictly prohibited.  If you have received this electronic message in error, please notify the sender immediately to arrange for return of the document."
),

12 => array('Healthy Directions Tracking!',
	"Hello %s,

Hope you had a great day!  Let us know how your day went. 

				Number of steps taken = _____ steps
				
				Number of servings of fruits and vegetables eaten = ____ servings 
				
				Number of servings of red meat eaten = _______ servings
				%s
				Took a multivitamin today (yes/no) = 

If you missed a day of tracking, that's okay.  Just try to get back to it as soon as you can.  Remember that you can even keep up with your healthy habits on your own by tracking your progress directly on the Healthy Directions web site at any time: 

Help.TrackMyChanges.org
		Your user name is: %s
		Your password is: %s

Need more ideas for reaching your health goals?  The Healthy Tips section on the web site is a great resource – check it out!  If you'd like to contact us, please call 617-582-7295.

Keep on tracking!  

The Healthy Directions team

This electronic message and attached files may contain information that is confidential.  This information is solely for the use of the individual(s) and entity(s) named as recipients.  If you are not the intended recipient, you are hereby notified that any disclosure, copying, distribution, or other use of the contents of this electronic message is strictly prohibited.  If you have received this electronic message in error, please notify the sender immediately to arrange for return of the document."),
13 => array('Healthy Directions Tracking!',
	"Hello %s,

How did you do today working toward your health goals?  Let us know how your day went.

				Number of steps taken = _____ steps
				
				Number of servings of fruits and vegetables eaten = ____ servings 
				
				Number of servings of red meat eaten = _______ servings
				%s
				Took a multivitamin today (yes/no) = 

This is the second to last email to help you with tracking.  But, remember that you can always keep up with your healthy habits on your own by tracking your progress directly on the Healthy Directions web site at any time: 

Help.TrackMyChanges.org
		Your user name is: %s
		Your password is: %s

Many people find it helpful to track every day—then they can get a clear picture of their progress and also see what areas they might like to work more on in the future.

We hope that you'll continue tracking on the Healthy Directions web site to help you meet your health goals – check it out!  If you'd like to contact us, please call 617-582-7295.

Keep on tracking!  

The Healthy Directions team

This electronic message and attached files may contain information that is confidential.  This information is solely for the use of the individual(s) and entity(s) named as recipients.  If you are not the intended recipient, you are hereby notified that any disclosure, copying, distribution, or other use of the contents of this electronic message is strictly prohibited.  If you have received this electronic message in error, please notify the sender immediately to arrange for return of the document."),
'14' => array('Healthy Directions Tracking!',
	"Hello %s,

Hope you had a great day working on your health goals.  Let us know how your day went.  

				Number of steps taken = _____ steps
				
				Number of servings of fruits and vegetables eaten = ____ servings 
				
				Number of servings of red meat eaten = _______ servings
				%s
				Took a multivitamin today (yes/no) = 

This is the last email we will send for you to tell us directly about your day.  But, remember that you can always keep up with your healthy habits on your own by tracking your progress directly on the Healthy Directions web site at any time: 

Help.TrackMyChanges.org
		Your user name is: %s
		Your password is: %s

We hope these emails helped you get started with tracking, and that you will continue tracking everyday on the Healthy Directions web site.  Check it out!  If you'd like to contact us, please call 617-582-7295.

We'll send you your results for your second week of tracking soon. 

Keep on tracking!

The Healthy Directions team

This electronic message and attached files may contain information that is confidential.  This information is solely for the use of the individual(s) and entity(s) named as recipients.  If you are not the intended recipient, you are hereby notified that any disclosure, copying, distribution, or other use of the contents of this electronic message is strictly prohibited.  If you have received this electronic message in error, please notify the sender immediately to arrange for return of the document."),
);



$emailReminders = array(
'B' => array('Check out Healthy Directions!',
	"Hello %s,

Healthy Directions invites you to check out everything we have to offer on the Healthy Directions website!  

Help.TrackMyChanges.org
		Your user name is: %s
		Your password is: %s

You'll find new things to see, like an updated Healthy Talk column, featured recipes, and more. 

We're also eager to have you track your health habits.  It only takes 5 minutes or less each day, and it helps you see how you're doing.  Plus, you earn raffle points every time you log on and track! Each month you'll have the chance to win great prizes, like magazine subscriptions, movie tickets, and more.

If you're having technical trouble, we are more than happy to help you. We want you to be able to see and do everything!  Please email Help.TrackMyChanges@partners.org.  Someone will be in touch and will help you.  If you'd like to contact us, please call 617-582-7295.

Sincerely, 

Healthy Directions 

P.S.: We don't want to become spam! Please add our email address to your system, so your Healthy Directions emails won't be blocked.

This electronic message and attached files may contain information that is confidential.  This information is solely for the use of the individual(s) and entity(s) named as recipients.  If you are not the intended recipient, you are hereby notified that any disclosure, copying, distribution, or other use of the contents of this electronic message is strictly prohibited.  If you have received this electronic message in error, please notify the sender immediately to arrange for return of the document.  "
),
'W25' => array('We\'re almost at the end of Healthy Directions!', 
	"Hello %s,

In just one more week, you will be finished with the Healthy Directions web site. At this time, your access to the web site will end. 

Be sure to visit the Healthy Directions site over the next 7 days and take advantage of everything it has to offer! You'll get a chance to see and do all your Healthy Directions favorites. You can still track your health goals, read this week's Healthy Talk column, and more.

Help.TrackMyChanges.org
		Your user name is: %s
		Your password is: %s

We're so happy that you've taken part in the project, and we wish you continued success.  If you've made changes and seen how they pay off, keep up the good work.  To keep up with your healthy changes, visit smallstep.gov.  Remember, healthy changes can last a lifetime!   If you'd like to contact us, please call 617-582-7295.

Sincerely, 

Healthy Directions

P.S.: We don't want to become spam! Please add our email address to your system, so your Healthy Directions emails won't be blocked.

This electronic message and attached files may contain information that is confidential.  This information is solely for the use of the individual(s) and entity(s) named as recipients.  If you are not the intended recipient, you are hereby notified that any disclosure, copying, distribution, or other use of the contents of this electronic message is strictly prohibited.  If you have received this electronic message in error, please notify the sender immediately to arrange for return of the document.  "
),
'W26' => array('Goodbye from Healthy Directions!',
	"Hello %s,

Thank you for being part of Healthy Directions! We hope that the site helped you set health goals – and reach them. We also hope that you had some fun!

Even though you won't be able to visit the Healthy Directions site any more, your healthy changes can last a lifetime. Stay on track and think about what helps you keep up with your new health habits. 

You can even make your own log to keep track of what you're doing each day. Keeping up with health habits can be hard – if you slip up, don't beat yourself up. Try to get back on track as soon as you can, and you will be successful! For tips, visit smallstep.gov.  It's got great ideas for helping you keep up your healthy changes.

Finally, don't forget that friends and family can keep offering their support, even after Healthy Directions is over. This can help you keep those healthy habits for a lifetime. If you'd like to contact us, please call 617-582-7295.

Thanks again, and best of luck!

Sincerely,

Healthy Directions
This electronic message and attached files may contain information that is confidential.  This information is solely for the use of the individual(s) and entity(s) named as recipients.  If you are not the intended recipient, you are hereby notified that any disclosure, copying, distribution, or other use of the contents of this electronic message is strictly prohibited.  If you have received this electronic message in error, please notify the sender immediately to arrange for return of the document.  "
),
);


// Note: we are sending one email at a time. Should be ok, since there shouldn't be that many emails
// sent on any given day

function sendemail( $email, $subject, $messagetext) {
global $fromEmail;


	$status = mail( $email, $subject, $messagetext, "From: $fromEmail", "-f".$fromEmail );
	
	// if (!$status) error_log("\n".  'Error sending email to '. $email , 3, $CRfilepath.'cronjobs.err');

}

function sendReminder( $email, $msgkey, $fname, $studyID, $username, $password) {
global $emailReminders;

	$messagetext = sprintf($emailReminders[$msgkey][1], $fname, $username, $password);
	info_log( "\tEmail type " .$msgkey . ' sent to '. $email . ' studyID =' . $studyID); 
	sendemail( $email, $emailReminders[$msgkey][0], $messagetext);
}

function sendReminderTLR( $email, $msgkey, $fname, $studyID, $nonsmoker, $username, $password) {
global $emailRemindersTLR, $RAemailSmokerTLR;

	if ( $nonsmoker == true) 
		$messagetext = sprintf($emailRemindersTLR[$msgkey][1], $fname, '', $username, $password);
	else

		$messagetext = sprintf($emailRemindersTLR[$msgkey][1], $fname, $RAemailSmokerTLR, $username, $password);
	info_log( "\tEmail type " .$msgkey . ' sent to '. $email . ' studyID =' . $studyID); 
	// echo( "\tEmail type " .$msgkey . ' sent to '. $email . ' studyID =' . $studyID); 


	
	sendemail( $email, $emailRemindersTLR[$msgkey][0], $messagetext);
}

function notifyErrorAdmin ( $msg ) {
	sendemail( 'dave_rothfarb@dfci.harvard.edu', 'Mary Cooley Lung Cancer Project cronjob technical error', $msg);
}

global $logfile;

function info_log( $text ) {
global $logfile;

	if (!$logfile) return;
	echo "\n". date("Y-m-d H:i:s ") .$text;
	if (!fwrite( $logfile, "\n". $text) ) notifyErrorAdmin('Unable to write to log file cronjobs.log');
}

function getWks( $timeDiff) {

	return ceil ( ($timeDiff) / (7 * 86400) );

}

function getSmokerStatus( $sm1, $sm2, $sm3 ) {
	// $sm3 not used
	// echo "sm1,2,3 =  $sm1, $sm2, $sm3";
	$nonsmoker = false;
			if ($sm1 == 1) $nonsmoker = true;
			else {
				if ($sm2 == 2) {
					// If answer = yes, check next question before deciding
			
			
			
					if ($sm2 == 1) $nonsmoker = true;
					else {
						$nonsmoker = false;
					}
				}
			}
	return $nonsmoker;
}

// For now, assume all until have access to survey DB
$trackedBehaviors[] = 1;
$trackedBehaviors[] = 2;
$trackedBehaviors[] = 3;
$trackedBehaviors[] = 4;
$trackedBehaviors[] = 5;


	// Log all errors to error log file
	// Enter a timestamp into log file
	// error_log( "\n". 'testing system log file');
	// chmod($CRfilepath.'cronjobs.log', 664); 
	$logfile = fopen($CRfilepath.'cronjobs.log', "a");
	if (! $logfile) {
		echo 'Error fopen log file';
		info_log( 'Error fopen log file');
		notifyErrorAdmin( 'Error fopen log file');
		// Try to continue anyway
	}
	info_log( "\n". date('Y-m-d'));

	$jConfig = new JConfig;	// Obtain current configuration parameters

	$statusArray = dbMysqlConnect(USERDB);
	if (! $statusArray[0] ) {
		echo  "\nUnable to connect to user database: ". $statusArray[1];
		info_log(  'Unable to connect to user database: '. $statusArray[1]);
		notifyErrorAdmin(  'Unable to connect to user database: '. $statusArray[1]);
		die();
	}
	else $userMsqlDB = $statusArray[0];
		
	// Day of Week for this run, since we're only interested in exact weekly 'anniversaries'
	$DoW = strftime("%u", $_SERVER['REQUEST_TIME']);
	// Substract 1 to match SQL weekday() function return values
	$DoW--;
	$today = $_SERVER['REQUEST_TIME'];
	// Adjust today date to beginning of day 
    $today=mktime(0,0,1,date("n",$today),date("j",$today),date("Y",$today));
	
	
	// 1. DEACTIVATE END OF IX USERS
	// Select
	//		criteria: active web users (both part_info and userInfo, to rule out those already deactivated and include only web. Enrollment data will only exist for
	//			enrolled participants
	//			start date before (today-26 wks)
	//		email: for those who will receive email reminders
	//		data: start date
	//	Action for all matching IDs:
	//		set jos_users.block =1
	//		set userInfo.activeStatus = 0
	
	//
	// Calculate today's date - 26 weeks
	$wk26ago = strtotime(date("Y-m-d H:i:s", $_SERVER['REQUEST_TIME']) . " -26 week");
	$wk25ago = strtotime(date("Y-m-d H:i:s", $_SERVER['REQUEST_TIME']) . " -25 week");
	
	// Adjust wk26ago date to beginning of day 
    $wk26ago=mktime(0,0,1,date("n",$wk26ago),date("j",$wk26ago),date("Y",$wk26ago));
	// Adjust wk26ago date to beginning of day 
    $wk25ago=mktime(0,0,1,date("n",$wk25ago),date("j",$wk25ago),date("Y",$wk25ago));

    $todaydate =date("Y-m-d H:i:s", $today);
	$wk26agodate =date("Y-m-d H:i:s", $wk26ago);	

	$sql = "SELECT userInfo.joomlaID, part_info.partID, part_info.ptEmail, part_info.ptFName, unix_timestamp(enrollment.startDate) as startDate FROM userInfo INNER JOIN enrollment ON userInfo.studyID = enrollment.partID INNER JOIN part_info  ON enrollment.partID = part_info.partID WHERE userInfo.activeStatus = 1 AND part_info.ptStatus = 'a' AND unix_timestamp(enrollment.startDate) < $wk25ago AND weekday(enrollment.startDate) = $DoW AND part_info.partID != 50488";
	// echo $sql;	
	info_log ( 'Web Deactivation::');
	$result = mysql_query($sql, $userMsqlDB ) ;
	
	if ($result) {
			if  (mysql_num_rows($result) > 0) {

				while( $row = mysql_fetch_assoc($result)) {
						$joomlaID = $row['joomlaID'];
						$studyID = $row['partID'];
						$email = $row['ptEmail'];
						$fname = $row['ptFName'];
						// print_r($row);
						$startDate = $row['startDate'];	
						// DO NOT Adjust start date date here - want to send msg as soon as hit week to beginning of day 
						
						if ($startDate < $wk26ago) {
							// Let error messages accumulate - no time to be perfect		
							// Make sure we are connected to user db
							$db = mysql_select_db(USERDB, $userMsqlDB);
							if (!$db) error_log( mysql_error($userMsqlDB));
							
							$sql =  "UPDATE  userInfo SET activeStatus=0 WHERE joomlaID = $joomlaID";
							$result2 = mysql_query($sql, $userMsqlDB ) ;
							if (!$result2) error_log( mysql_error($userMsqlDB));
							$db = mysql_select_db($jConfig->db, $userMsqlDB);
							if (!$db) error_log( mysql_error($userMsqlDB));
							
							$sql =  "UPDATE  jos_users SET block = 1 WHERE id = $joomlaID";
							$result2 = mysql_query($sql, $userMsqlDB ) ;
							if (!$result2)  error_log( mysql_error($userMsqlDB));
							
							// Go back to user db
							$db = mysql_select_db(USERDB, $userMsqlDB);
							if (!$db) error_log( mysql_error($userMsqlDB));

							// send goodbye email
							sendReminder( $email, 'W26', $fname, $studyID) ;
						}
						
						else {
							sendReminder( $email, 'W25', $fname, $studyID) ;
						}
				}
			}
			else {
				info_log ( "\tNo web users to deactivate");
			}
	}
	else {
			echo mysql_error($userMsqlDB);
			info_log(  mysql_error($userMsqlDB));
			notifyErrorAdmin(  mysql_error($userMsqlDB));
	}
    $todaydate =date("Y-m-d H:i:s", $today);

	// 2. Determine whether any users have met or not met the TLR for the substudy
	//		A. identify all eligible users
	//		Include: Q: all users whose TLRmet field == 0, or only those whose 'anniversary' (DOW) is the same as today?
	//			ideally only DOW, but since it's always possible that systems went down, etc.., best to identify all
	//			AND  users whose start date is today's date - 7 days
	//		Exclude:
	//
	//		B. For each user retrieved, one at a time (we can afford to do this because of the low # in the study)
	//		Retrieve all tracking data for the first week after start date
	//		Determine whether TLR met or not, and set TLRmet field accordingly
	
	// A.

	$wkago = strtotime(date("Y-m-d H:i:s", $_SERVER['REQUEST_TIME']) . " -1 week");

	$userList = array();	// array of arrays
	$userMetList = array();	// array of studyIDs met TLR
	$userNotMetList = array();	// array of studyIDs did not meet TLR
	
	// Adjust to beginning of day 
    $wkago=mktime(0,0,1,date("n",$wkago),date("j",$wkago),date("Y",$wkago));
	$sql = "SELECT userInfo.joomlaID, part_info.partID, part_info.ptEmail, part_info.ptFName, unix_timestamp(enrollment.startDate) as startDate FROM userInfo INNER JOIN enrollment ON userInfo.studyID = enrollment.partID INNER JOIN part_info  ON enrollment.partID = part_info.partID WHERE userInfo.activeStatus = 1 AND part_info.ptStatus = 'a' AND userInfo.TLRmet = 0 AND unix_timestamp(enrollment.startDate) < $wkago ORDER BY enrollment.startDate, part_info.partID"; // AND weekday(enrollment.startDate) = $DoW";
	// echo $sql;
	info_log ( 'TLR met search::');
	$result = mysql_query($sql, $userMsqlDB ) ;
	if ($result) {
			if  (mysql_num_rows($result) > 0) {

				while( $row = mysql_fetch_assoc($result)) {
						$joomlaID = $row['joomlaID'];
						$studyID = $row['partID'];
						// $email = $row['ptEmail'];
						// $fname = $row['ptFName'];
						$startDate = $row['startDate'];	
						$userList[] =  array($studyID, $startDate);
						// print_r($row);
				}
			}
			else {
				info_log ( "\tNo web users to consider for TLR");
			}
	}
	else {
			echo mysql_error($userMsqlDB);
			info_log(  mysql_error($userMsqlDB));
			notifyErrorAdmin(  mysql_error($userMsqlDB));
	}

	// B.
	// 
	// Add studyID to RA email list
	
	foreach ($userList as $user) { 
		// data for up to 7 days after start date - due to algorithm used, 'rightnow ' includes all hours of 7th day, but not into 8th day
		// $rightNow = $_SERVER['REQUEST_TIME'];
		$startPlus6days = strtotime( date("Y-m-d H:i:s", $user[1]) . " +6 days") + (23 * 60 * 60) + (59 * 60) + (59)  ;
		// echo '<br>start + 6 date =' . date("Y-m-d H:i:s", $startPlus6days);
		$currentData = array();	
		for ($i = 1; $i <= count($behaviorSpecs); $i++) {
			$currentData[$i] = new trackData;
		}

		// Retrieve data up to previous day ie. start until end of 7th day
		$statusArray = getWeekData ($userMsqlDB, $user[0], $currentData, $startPlus6days);
		if ($statusArray[0] == null ) {
		
			info_log(   'We are not able to retrieve your tracking data due to a database error: ' . $statusArray[1]);
		}
		else {
			// echo '<br><br>studyID: '. $user[0]. '<br>';
			// print_r($currentData);
		}
		

		// For each behavior, see if any has been tracked for at least 3 days. If so, TLR met for the week
		//	no need to check other behaviors
		foreach ($trackedBehaviors as $behaviorID) {
			// echo '<br>#days tracked for behavior '. $behaviorID. ' = '. count($currentData[$behaviorID]->weekArray);
			if ( count($currentData[$behaviorID]->weekArray) >= 3) {
						$userMetList[] =  $user[0];
						break;
			}
		}		
		// break;
	}
	

	// Copy all users from 2 dim array to single array
	foreach ($userList as $user) { 
		$userNotMetList[] = $user[0];
	}
	// Remove those who have met TLR
				
	$RANotMetList=	array_diff( $userNotMetList, $userMetList );
	
	
	if ( count( $userMetList) ) {
		// echo '<br><br>Met LISt: <br>';
		// print_r( $userMetList );
		info_log ( "\tTLR met:");

		// Update TLRmet field for all studyIDs which have met TLR
		foreach ($userMetList as $userid ) {
			info_log ( "\t\t". $userid);
			if ($sqlids != '' ) $sqlids .= ',';
			$sqlids .=  $userid ; 
		}
		
		$sql =  "UPDATE  lung_cancer_user.userInfo SET TLRmet=1 WHERE studyID IN ( $sqlids )";
		// echo $sql;
		$result = mysql_query($sql, $userMsqlDB ) ;
					
		if ($result) {	
						// Pb with checking mysql_affected_rows() so assume all ok
						// Don't check # rows because if no change, may be 0
						//if (mysql_affected_rows($userMsqlDB)>0) {
						//}
					}
					else {
						// error_reporting($old_level);
						$msg = "Database error updating user information userInfo: " . mysql_error($userMsqlDB);
						info_log( $msg);
		}
	}
	
	$sqlids = '';
	
	if ( count( $RANotMetList) ) {
		info_log ( "\tTLR not met:");
		// echo '<br><br>Not Met LISt: <br>';
		// print_r( $RANotMetList );
		// Update TLRmet field for all studyIDs which have met TLR
		foreach ($RANotMetList as $userid ) {
			if ($sqlids != '' ) $sqlids .= ',';
			$sqlids .=  $userid ; 
		}
		
		$sql =  "UPDATE  lung_cancer_user.userInfo SET TLRmet=-1 WHERE studyID IN ( $sqlids )";
		// echo $sql;
		$result = mysql_query($sql, $userMsqlDB ) ;
					
		if ($result) {	
				// Pb with checking mysql_affected_rows() so assume all ok
				// Don't check # rows because if no change, may be 0
				//if (mysql_affected_rows($userMsqlDB)>0) {
				//}
				}
				else {
					// error_reporting($old_level);
					$msg = "Database error updating user information userInfo: " . mysql_error($userMsqlDB);
					info_log( $msg);
		}
		
		foreach( $RANotMetList as $userid )  {
				info_log ( "\t\t". $userid);

				$RAemailBodyTLR .=  '
						' . $userid;
		}
		sendemail( $RAemail, $RAemailSubjectTLR, $RAemailBodyTLR) ;
	}

	// 3. TLR email reminders
	//		Include all users with TLRmet field = -1 and are active
	//		SELECT studyID, first name, email, #days since start date
	//		Only if start date > today - 21 days
	
	$threewksago = strtotime(date("Y-m-d H:i:s", $_SERVER['REQUEST_TIME']) . " -21 days");	// cronjob run after midnight
    $threewksago=mktime(0,0,1,date("n",$threewksago),date("j",$threewksago),date("Y",$threewksago));
    $threewksagodate =date("Y-m-d H:i:s", $threewksago);

	// echo '\ntoday date = '. date("Y-m-d H:i:s", $today) ;
	// echo '\n21 days ago date = '. $threewksagodate ;
	
	// LEFT JOIN with SURVEY DB?
	$sql="SELECT i.studyID, j.email, j.name,datediff( '$todaydate', j.RegisterDate) as daysstart, s.". SURVEY_ID. "X4X12 as sm1,s." . SURVEY_ID. "X4X13 as sm2, s." . SURVEY_ID. "X4X14 as sm3" . " ,e.webPwd " .
		" FROM lung_cancer_site.jos_users j 
		INNER JOIN lung_cancer_user.userInfo i ON j.id = i.joomlaID 
		INNER JOIN lung_cancer_user.enrollment e ON e.partID = i.studyID
		LEFT OUTER JOIN ". LIMEDB. ".lime_survey_". SURVEY_ID . " s ON s.". SURVEY_ID . "X1X30= i.studyID
		WHERE j.block =0 AND (j.RegisterDate >  '$threewksagodate')   
		AND  i.TLRmet = -1 ORDER BY j.RegisterDate DESC	";
	
	// $sql = "SELECT " . SURVEY_ID. "X4X12 ," . SURVEY_ID. "X4X13," . SURVEY_ID. "X4X14 FROM lime_survey_". SURVEY_ID . " WHERE ". SURVEY_ID . "X1X30=$user->userID order by ID DESC LIMIT 1";	

	
	// echo $sql;
	
	
	info_log ( 'Substudy Email Ix Reminders::');
	
	$result = mysql_query($sql, $userMsqlDB ) ;
	$emailcount =0;
	if ($result) {
			if  (mysql_num_rows($result) > 0) {
				while( $row = mysql_fetch_assoc($result)) {
						//print_r($row);
						$email = $row['email'];
						$fname = $row['name'];
						$studyID = $row['studyID'];
						$daysstart = $row['daysstart'];
						// echo "<br><br>studyid = $studyID\n";
 						$nonsmoker = getSmokerStatus( $row['sm1'], $row['sm2'], $row['sm3'] );
						// echo "<br>nonsmoker  = " . $nonsmoker;
						$password = $row['webPwd'];

						// Send message corresponding to # days since start date - 7
						//	A. calculate user's IxWeek and number of weeks since last login
						// $IxWeek = floor ( ($today - $startDate) / (7 * 86400) );
						$IxDay = $daysstart-7 +1;	// +1 to take into account that this job will run just after midnight, 
												// the morning of the day the email is sent
	
						// echo "<br>IxDay  = " . $IxDay;
						// echo "<br>daysSinceStart  = " . $daysstart;
						// info_log ( "\t\tEmail Ix Day ". $IxDay. "\t" . $email);
						
						// Log which emails sent to which users
						if ($IxDay <= 14)  {
							$emailcount++;
							
							sendReminderTLR( $email, $IxDay, $fname, $studyID, $nonsmoker, $email, $password) ;
						}
				}
				
				if ( $emailcount == 0 ) {
					info_log ( "\tNo active  users to remind by emails Ix");
				}
			
	
			}
			else {
					info_log ( "\tNo active  users to remind by emails Ix");
			}
		
	}
	else {
			info_log(  mysql_error($userMsqlDB));
			notifyErrorAdmin(  mysql_error($userMsqlDB));

	}
	

	// 4. Email reminders
	//		Last login date in jos_users, OR in userLogin table 
	//		SELECT studyID, max(logintime) FROM `userLogin` group by studyID
	
	// joomla data not 100% reliable if non-study users
	//	are created, so must join with lung_cancer_user.userInfo
	//
	// All needed info happens to be in jos_users table, so let's use it and avoid joins with user data?
	//	BUT: 
	// Select
	//	Criteria: active  web users, logins within 28 days (ignore all previous) 
	//	data: studyID, first name (name) , email, start date (registerDate), last login
	
	//
	//	Calculate for each user
	//		IxWeek
	//		number of days since last login
	
	// !! Note that last visit times are recorded as UTC times in Joomla, so must subtract timezone param value to obtain actual
	// 	local time
	$monthago = strtotime(date("Y-m-d H:i:s", $_SERVER['REQUEST_TIME']) . " -28 days");
    $monthago=mktime(0,0,1,date("n",$monthago),date("j",$monthago),date("Y",$monthago));
    $monthagodate =date("Y-m-d H:i:s", $monthago);

	// echo '\ntoday date = '. date("Y-m-d H:i:s", $today) ;
	// echo '\n28 days ago date = '. $monthagodate ;
	$RAemailList = array();

	// for extra caution, filter out users over 26 wks, in case cronjob didn't run and deactivate them
	// Do an union to use userLogin table for last login date 
	/*
	$sql = "(SELECT i.studyID, datediff( '$todaydate', j.RegisterDate) as dayslogin ,  j.email, j.name,datediff( '$todaydate', j.RegisterDate) as daysstart, false as lastlogin  FROM lung_cancer_site.jos_users j INNER JOIN lung_cancer_user.userInfo i ON j.id = 
	i.joomlaID WHERE (j.block =0 AND (j.RegisterDate >  '$wk26agodate') ) AND (weekday(j.RegisterDate) = $DoW) AND (j.lastVisitDate = '0000-00-00')
	) 
	UNION 
	
	(SELECT i.studyID, datediff( '$todaydate', convert_tz(j.lastvisitDate,'+00:00','$jConfig->offset:00')) as dayslogin ,  j.email, j.name,datediff( '$todaydate', j.RegisterDate) as daysstart, true as lastlogin  FROM lung_cancer_site.jos_users j INNER JOIN lung_cancer_user.userInfo i ON j.id = 
	i.joomlaID WHERE (j.block =0 AND (j.RegisterDate >  '$wk26agodate') ) AND (j.lastVisitDate != '0000-00-00') AND 
	(weekday(convert_tz(j.lastvisitDate,'+00:00','$jConfig->offset:00')) = $DoW)   AND ( convert_tz(j.lastvisitDate,'+00:00','$jConfig->offset:00') >  '$monthagodate' )
	) 	
	
  	order by studyID"; 
	*/
	
	
	
	$sql = "(SELECT i.studyID, e.webPwd, datediff( '2010-02-09 00:00:01', j.RegisterDate) as dayslogin ,  j.email, j.name,datediff( '2010-02-09 00:00:01', j.RegisterDate) as daysstart, false as lastlogin  FROM lung_cancer_site.jos_users j INNER JOIN lung_cancer_user.userInfo i ON j.id = 
	i.joomlaID INNER JOIN lung_cancer_user.enrollment e ON i.studyID = e.partID WHERE (j.block =0 AND (j.RegisterDate >  '2009-08-11 00:00:01') ) AND (weekday(j.RegisterDate) = 1) AND (j.lastVisitDate = '0000-00-00')
	) 
	UNION 
	
	(SELECT i.studyID, e.webPwd, datediff( '2010-02-09 00:00:01', convert_tz(j.lastvisitDate,'+00:00','-5:00')) as dayslogin ,  j.email, j.name,datediff( '2010-02-09 00:00:01', j.RegisterDate) as daysstart, true as lastlogin  FROM lung_cancer_site.jos_users j INNER JOIN lung_cancer_user.userInfo i ON j.id = 
	i.joomlaID INNER JOIN lung_cancer_user.enrollment e ON i.studyID = e.partID WHERE (j.block =0 AND (j.RegisterDate >  '2009-08-11 00:00:01') ) AND (j.lastVisitDate != '0000-00-00') AND 
	(weekday(convert_tz(j.lastvisitDate,'+00:00','-5:00')) = 1)   AND ( convert_tz(j.lastvisitDate,'+00:00','-5:00') >  '2010-01-12 00:00:01' )
	) 	
	
  	order by studyID";
	
	// echo $sql;
	
	
	
	info_log ( 'Email Reminders::');
	
	$result = mysql_query($sql, $userMsqlDB ) ;
	$emailcount =0;
	if ($result) {
			if  (mysql_num_rows($result) > 0) {
				while( $row = mysql_fetch_assoc($result)) {
						//print_r($row);
						$email = $row['email'];
						$fname = $row['name'];
						$studyID = $row['studyID'];
						$daysSinceLogin = $row['dayslogin'];
						$daysstart = $row['daysstart'];
						$lastlogin = $row['lastlogin'];
						$password = $row['webPwd'];
						

					// Identify those who are getting weekly messages
					//	A. calculate user's IxWeek and number of weeks since last login
					// $IxWeek = floor ( ($today - $startDate) / (7 * 86400) );
							$IxWeek = floor ( ($daysstart) / (7 ) );
					

					// determine message to be sent based on number of days since last login
					//	and IxWeek .  We match on exact number of days to avoid sending more than
					//	one email reminder per week
					if ($daysSinceLogin == 	28 ) { // Email to ppt and RA
							// echo "\nstudyid = $studyID\n";
							// echo "\nIxWeek  = " . $IxWeek;
							// echo "\ndaysSinceLogin  = " . $daysSinceLogin;
							// Add studyID to RA email list only if still time
							$emailcount++;
							sendReminder( $email, 'B', $fname, $studyID, $email, $password) ;
							if ($IxWeek < 14) $RAemailList[] =  $studyID;
			
					}
				
				
				}
				
				if ($emailcount > 0 ) {
					info_log ( "\tWeb Users to roll over:");
					// echo count( $RAemailList) ;
					foreach( $RAemailList as $email )  {
						info_log ( "\t\t". $email);

						$RAemailBody .=  '
						' . $email;
					}
					sendemail( $RAemail, $RAemailSubject, $RAemailBody) ;
				}
				else {
					info_log ( "\tNo active  users to remind by emails");
				}
			
	
			}
			else {
					info_log ( "\tNo active  users to remind by emails");
			}
		
	}
	else {
			info_log(  mysql_error($userMsqlDB));
			notifyErrorAdmin(  mysql_error($userMsqlDB));

	}
	
	// Close db handles
	fclose($logfile);
	mysql_close($userMsqlDB);
?>
