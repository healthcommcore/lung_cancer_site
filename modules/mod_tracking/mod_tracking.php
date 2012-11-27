<?php
/**
* @version 1.0 $
* @package Tracking for HD2
* @copyright (C) 2008 HCC
*/

// Tracking (self-monitoring)
//
// Data
//

// Important note on time handling
// The code
//		1.Daylight Saving Time means that relative date calculations 
//		cannot be based on adding the expected, fixed # seconds
//		per sec.
//		2.PHP 5 lags 23 seconds every year to leap year issues
//	To overcome this problem, the function strtotime() is used
//	to calculate timestamps corresponding to future and past
//	dates.
//
//	note that PHP5 is supposed to have errors in the strtotime
//	calculations, but couldn't find any obvious problems, and it's
//	not possible at this stage to return to PHP4
//	
//	it *is* OK to use #seconds to calculate amount of time
//	in days, etc... between two timestamps.	
//
?>

<?php
/** ensure this file is being included by a parent file */


defined( '_JEXEC' ) or die( 'Restricted Access.' );

require_once( JPATH_SITE .'/includes/hd2/date.php' );
global $behaviorSpecs;

require_once ( JPATH_SITE .'/includes/hd2/constants.php' );
require( JPATH_SITE .'/includes/hd2/behavior.php' );
require_once( JPATH_SITE .'/includes/hd2/siteFunctions.php' );
require_once( JPATH_SITE .'/includes/hd2/user.php' );
require( JPATH_SITE .'/includes/hd2/data.php' );
require( JPATH_SITE .'/includes/hd2/points.php' );
require ( JPATH_SITE .'/includes/hd2/chart.php' );
require ( JPATH_SITE .'/modules/mod_tracking/mod_tracking.html.php' );

global $my;		

global $rightNow;
global $userDB;
global $userMsqlDB;
global $user;
global $currentData;
global $option;
global $view;
global $itemid;
global $id;
global $trackedBehaviors;


$rightNow = $_SERVER['REQUEST_TIME'];

// array of trackgoal objects (defined trackgoal.php)
//	contains current tracked behaviors, keyed by behaviorID
$trackedBehaviors = array();

// Overall data for all currently tracked behaviors
// array of trackData objects, keyed by behaviorID
// 	for each tracked behavior, retrieve database info and set  as needed (only
//	one set may be needed for any given page at a time)
//		- today's data, if any (and set flag)
//		- this week's data (array)
//		- previous weeks' data (array)

$currentData = array();	
for ($i = 1; $i <= count($behaviorSpecs); $i++) {
	$currentData[$i] = new trackData;
}

// Retrieve Joomla URL parameters so we can reconstruct it
$option = trim( JRequest::getVar(  'option'));
$id = trim( JRequest::getVar(  'id'));
$itemid = trim( JRequest::getVar(  'Itemid'));
$view = trim( JRequest::getVar(  'view', null));

// Retrieve Tracking module parameters
$op = trim( JRequest::getVar(  'op'));
// Tracking day  for the behavior being tracked (if any) = today, back1, back2, back3
// $day = trim( JRequest::getVar( 'day'));	
$day = $params->get('day', '');

?>

<script language="javascript" type="text/javascript">


function checkRadio( form) {
	for (var i=0; i < form.response4.length; i++){
		if (form.response4[i].checked) return true;
	}
	return false;
}

function processTrackingEntry (form) {
	var errormsg = '';
	// is smoking on the form?
	smoking = document.getElementById("response5");
		
	// First check for non-numeric values (exclude prompt text)
	if (!(isBlank(form.response1.value) ) && (form.response1.value != '<?php echo $behaviorSpecs[1]['step2item'] ?>') && (form.response1.value != parseInt(form.response1.value)))
		errormsg += "\n<?php echo _HCC_TKG_INPUT_ERR_NUMBER_JS. ' '. $behaviorSpecs[1]['step2item']?>" ;	
	if (!(isBlank(form.response1A.value) ) && (form.response1A.value != '<?php echo _HCC_MINUTES ?>') && (form.response1A.value != parseInt(form.response1A.value)))
		errormsg += "\n<?php echo _HCC_TKG_INPUT_ERR_NUMBER_JS. ' '. _HCC_MINUTES . ' of moderate activity'?>" ;	
	if (!(isBlank(form.response1B.value) ) && (form.response1B.value != '<?php echo _HCC_MINUTES ?>') && (form.response1B.value != parseInt(form.response1B.value)))
		errormsg += "\n<?php echo _HCC_TKG_INPUT_ERR_NUMBER_JS. ' '. _HCC_MINUTES . ' of vigorous activity'?>" ;	
	if ((smoking != null) &&
		(!(isBlank(form.response5.value) ) && (form.response5.value != '<?php echo $behaviorSpecs[5]['step2item'] ?>') && (form.response5.value != parseInt(form.response5.value)))
		)
		errormsg += "\n<?php echo _HCC_TKG_INPUT_ERR_NUMBER_JS. ' '. $behaviorSpecs[5]['step2item']?>" ;	
	// First check for range errors
	if	(form.response1.value > 20000 ) errormsg += "\n" + "<?php echo _HCC_TKG_INPUT_ERR_RANGE. ' '. $behaviorSpecs[1]['maxVal'] . ' '. $behaviorSpecs[1]['step2item']?>" ;
	if	(form.response1A.value > 200 ) errormsg += "\n" + "<?php echo _HCC_TKG_INPUT_ERR_RANGE. ' 200 minutes of moderate activity' ?>";
	if	(form.response1B.value > 100 ) errormsg += "\n" +"<?php echo _HCC_TKG_INPUT_ERR_RANGE. ' 100 minutes of vigorous activity' ?>";
	if	((smoking != null) && (form.response5.value > 30 )) errormsg +="\n" + "<?php echo _HCC_TKG_INPUT_ERR_RANGE. ' '. $behaviorSpecs[5]['maxVal'] . ' '. $behaviorSpecs[5]['step2item']?>" ;
	
	if (errormsg != '') {
		alert (errormsg);
		return false;
	}
	
	
	// Then check if not all fields entered
	// Does not matter whether or not any data has been previously entered
	
	if ( 
		(isBlank(form.response1.value) || (form.response1.value == '<?php echo $behaviorSpecs[1]['step2item'] ?>')) &&
		(isBlank(form.response1A.value) || (form.response1A.value == '<?php echo _HCC_MINUTES ?>')) &&
		(isBlank(form.response1B.value) || (form.response1B.value == '<?php echo _HCC_MINUTES ?>')) &&
		(form.response2.selectedIndex == 0 ) &&
		(form.response3.selectedIndex == 0 ) && 
		( 
			((smoking != null)  && 
			(isBlank(form.response5.value) || (form.response5.value == '<?php echo $behaviorSpecs[5]['step2item'] ?>')) ) 
			|| (smoking == null)
		) &&
		! checkRadio( form)
		)
		{
			alert('<?php echo _HCC_TKG_INPUT_NO_DATA ?>');
			return false;
		}

	// If we get here, some data has been entered, so now
	// check if no data entered for any field
	// Only one of the PA is required
	// discount those that already have data
	if ( 
		((isBlank(form.response1.value) || (form.response1.value == '<?php echo $behaviorSpecs[1]['step2item'] ?>') ) 
			&& (form.dataset1.value == 0) &&
			(isBlank(form.response1A.value) || (form.response1A.value == '<?php echo _HCC_MINUTES ?>')) &&
			(isBlank(form.response1B.value) || (form.response1B.value == '<?php echo _HCC_MINUTES ?>')) 
		)  ||
		((smoking != null)  && (form.dataset5.value == 0) && (isBlank(form.response5.value) || (form.response5.value == '<?php echo $behaviorSpecs[5]['step2item'] ?>')) ) ||
		( (form.response2.selectedIndex == 0 ) && (form.dataset2.value == 0)) ||
		( (form.response3.selectedIndex == 0 ) && (form.dataset3.value == 0)) || 
		( ! checkRadio( form) && (form.dataset4.value == 0))
		)
		{
			var answer = confirm ('You did not enter information for all of your health habits. Are you sure you want to submit your information?')
			if (answer) return true;
			else
			return false;
		}
	
	return true;
}

// Tracking form validation
// These functions remain in this PHP file so they have access to
// PHP error message definitions.


</script>

<?php
// $weekflag = true for weekly feedback
// returns msg
function evaluateFeedback ( $behaviorID, $response, $weekflag	) {
	global $behaviorSpecs;
	
	if ( $weekflag) $beharray = $behaviorSpecs[$behaviorID]['wfeedback'];
	else 		$beharray = $behaviorSpecs[$behaviorID]['dfeedback'];

	if ($behaviorSpecs[$behaviorID]['goalcompare'] == '>') {		
		// Start with the last dfeedback array item and work backwards
		$fbarray = array_reverse( $beharray, true);
		foreach ($fbarray as $level => $threshold) {
			if ($response >= $threshold[0]) {
				if (! $weekflag) return $threshold[1][rand(0,2)];
				return $threshold[1];	
			}
		}
		// if we get here, we are at the last level
		$keys = array_keys($beharray);
		// return $beharray[$keys[0]][1];
		if (! $weekflag) return $beharray[$keys[0]][1][rand(0,2)];
		return $beharray[$keys[0]][1];	
	}
	else  if ($behaviorSpecs[$behaviorID]['goalcompare'] == '<') {		
		foreach ($beharray as $level => $threshold) {
			if ($response <= $threshold[0]) {
				if (! $weekflag) return $threshold[1][rand(0,2)];
				return $threshold[1];	
			}
		}
		// if we get here, we are at the last level
		$keys = array_keys($beharray);
		if (! $weekflag) return $beharray[$keys[count($keys) -1]][1][rand(0,2)];
		return $beharray[$keys[count($keys) -1]][1];	
	}
}


function validateDataEntry( $behaviorID, $response) {
global $behaviorSpecs;
	
	
	// Case: Numeric data
	if ($behaviorSpecs[$behaviorID]['entrytype'] == _HCC_TRACKING_IN_NUMERIC) {		// 
		// Check for error cases
		/* Not an error to submit empty values */
		// if ($response =="") {
		if (($response =="") || ($response ==$behaviorSpecs[$behaviorID]['step2item'])){
			return (array( '', 0, ''));
		}
		if (!is_numeric($response)) {
				return ( array( $response, -1, _HCC_TKG_INPUT_ERR_NUMBER . ' '. $behaviorSpecs[$behaviorID]['step2item']));
		}
		// CHECK value against minVal 
		//	Maxval isn't checked because it is used for graphing purposes
		//	Instead we just compare with hard-coded figure for
		//	the only behavior that applies, Steps
		if (($response < $behaviorSpecs[$behaviorID]['minVal']) || 
				($response > $behaviorSpecs[$behaviorID]['maxVal'])) {
				return (  array( $response, -1, _HCC_TKG_INPUT_ERR_RANGE . ' <span class="highlight-bold">'. $behaviorSpecs[$behaviorID]['maxVal'] . '</span> '. $behaviorSpecs[$behaviorID]['step2item']));
		}
	}
	else {
		// For drop-down list input
		if ($response =='') {
				return( array( $response, 0, _HCC_TKG_INPUT_ERR_SELECT));
		}
	}
	// ALL OK, no error message
	return (array ( $response, 1,''));
}

function validatePAEntry( $response,$max, $txt, $prompt) {
	if (($response =="") || ($response ==$prompt)){
		return (array( "", 0, ''));
	}
	if (($response < 0) || 	($response > $max )) {
		// below or above min/max
		return (  array($response, -1, _HCC_TKG_INPUT_ERR_RANGE . ' <span class="highlight-bold">'. $max . '</span> minutes of '. $txt));
		}
	// ALL OK, no error message
	return (array ($response, 1,''));

}

function displayStep1Entry($day) {
global $userMsqlDB, $user, $rightNow;
global $currentData;
global $trackedBehaviors;
		$statusArray = getWeekData ($userMsqlDB, $user->userID, $currentData, $rightNow);

		if ($statusArray[0] == null ) {
			HTML_tracking::displayErrorMsg( 'We are not able to retrieve your tracking data due to a database error: ' . $statusArray[1]);
			return;
		}
		else {
			HTML_tracking::displayStep1Entry($day, '');
		}
}

// Validate user tracking input
//	if all OK, display chart and F/B
//	if errors, return error msg
function displayStep2Process($day) {
global $userDB, $userMsqlDB, $user, $rightNow;
global $currentData, $response;
global $trackedBehaviors;

		$responseArray = array();
		// Process input
		$statusMsg = "";
		foreach ($trackedBehaviors as $behaviorID) {
			$responsename = 'response' . $behaviorID;
			$response = strip_tags(trim( JRequest::getVar( $responsename)));
			// echo "Response for beh  $behaviorID = $response<br>";
			
		

			$retArray= validateDataEntry( $behaviorID, $response);
			
			switch ($retArray[1] ) {
				case 0:	// No data, move on
					// echo "No data for beh  $behaviorID<br>";	
					unset( $responseArray[ $behaviorID] );
					break;
				case 1:	// Good data, set in response array so can look up later for saving into DB and displaying charts
					$responseArray[ $behaviorID] = $retArray[0];
					break;
				case -1:
				default:
					unset( $responseArray[ $behaviorID] );
					// Bad data
					// Concatenate error messages
					// $statusMsg .= $retArray[1] . $response. 'for behavior'. $behaviorID .'<br>';
					$statusMsg .= $retArray[2] . '<br>';
			}
			
			// Process other PA fields whether or not there are error on any specific field - to catch all user input errors
			//	at the same time
			if (  ($behaviorID == 1) ) {
				// Special PA cases. Not great way to do this, but easiest/fastest
				// Handle additional fields
			
				$responsename = 'response1A';
				$response = strip_tags(trim( JRequest::getVar( $responsename)));
				// echo "Response for beh  $responsename = $response<br>";
				$retArray= validatePAEntry( $response, 200, 'moderate activity', _HCC_MINUTES);
				if ( $retArray[1] > 0 ) {
					// Add to steps total
					$responseArray[ $behaviorID] += $retArray[0] * 100;
		
				}
				if ( $retArray[1] < 0 ) {
					unset( $responseArray[ $behaviorID] );
					// Bad data
					// Concatenate error messages
					$statusMsg .= $retArray[2] . '<br>';
				
				}
			}

			if (  ($behaviorID == 1) ) {
				// Special PA case
				// Handle additional fields
			
				$responsename = 'response1B';
				$response = strip_tags(trim( JRequest::getVar( $responsename)));
				// echo "Response for beh  $responsename = $response<br>";
				$retArray= validatePAEntry( $response,100, 'vigorous activity', _HCC_MINUTES);
				if ( $retArray[1] > 0 ) {
					// Add to steps total
					$responseArray[ $behaviorID] += $retArray[0] * 200;
		
				}
				if ( $retArray[1] < 0 ) {
					unset( $responseArray[ $behaviorID] );
					// Bad data
					// Concatenate error messages
					$statusMsg .= $retArray[2] . $response. 'for behavior'. $behaviorID .'<br>';
				
				}
			}

		}
		
		if ($statusMsg != '') {
			return $statusMsg;
		}
		// If no data submitted for any fields, return with error message
		$datacount =0;
		foreach ($trackedBehaviors as $behaviorID) {
			if ( isset( $responseArray[ $behaviorID] ) ) $datacount++;
		}
		
		if ($datacount == 0) {
			return _HCC_TKG_INPUT_NO_DATA;
		}
		// Define time for  which data is being entered
		$thisFormTime=$rightNow - ( $day * _HCC_SECONDS_PER_DAY);
		
		// Save data points into database, one per behavior
		foreach ($trackedBehaviors as $behaviorID) {
			// If response data available
			if ( isset($responseArray[ $behaviorID])) {
				// echo "Saving data ". $responseArray[ $behaviorID]. "for beh  $behaviorID<br>";
				$statusMsg = saveTrackData ($userMsqlDB, $user->userID, $behaviorID, $responseArray[ $behaviorID], $thisFormTime);
				if ($statusMsg != null ) {
					$statusMsg = "Your data was not saved : $statusMsg";
					return $statusMsg;
				}
			}
		}
		
		if (($statusMsg == '') ) {	
		
			// Same week info
			$statusArray = getWeekData ($userMsqlDB, $user->userID, $currentData, $rightNow);
			
			// No data save errors, but display error message
			if ($statusArray[0] == null ) {
				HTML_tracking::displayErrorMsg( 'Your data was saved but we are not able to display your weekly tracking data due to a database error: ' . $statusArray[1]);
				return '';
			}
			else {
				$currentData = $statusArray[0];
			}
		// print_r($currentData);
		}
		
		// Calculate MRF scores -  per behavior
		foreach ($trackedBehaviors as $behaviorID) {
			$currentData[$behaviorID]->calcWeekMRF($behaviorID);
			// echo "MRF score for behavior  $behaviorID = ". $currentData[$behaviorID]->weekMRF;
		}
		$MRFscore = calcMRFscore ($user, $currentData, $trackedBehaviors);
				
		HTML_tracking::displayStep2Process($behaviorID, $day, $statusMsg, $responseArray, $MRFscore);
		return '';
}
//	Display weekly chart
function displayWeekly() {
global $userMsqlDB, $user, $rightNow;
global $currentData;
global $trackedBehaviors;

		$statusArray = getWeekData ($userMsqlDB, $user->userID, $currentData, $rightNow);

		if ($statusArray[0] == null ) {
			HTML_tracking::displayErrorMsg( 'We are not able to retrieve your weekly tracking data due to a database error: ' . $statusArray[1]);
			return;
		}
		
		else {
		 	$currentData = $statusArray[0];
			$MRFscore = calcMRFscore ($user, $currentData, $trackedBehaviors);
			HTML_tracking::displayWeekly($MRFscore);
			// print_r($currentData);
			// echo "MRFscore = $MRFscore";
		}
}

//	Display 6 month chart
function displayHistory() {
global $userMsqlDB, $user, $rightNow;
global $currentData;
global $trackedBehaviors;
		foreach ($trackedBehaviors as $behaviorID) {

		
			$statusArray = getAllData ($userMsqlDB, $user, $behaviorID, $currentData, $rightNow);
			// if ($statusArray[0] == null ) {
			if ( ($statusArray[0] == null) && ($statusArray[1] != '') ) {
				HTML_tracking::displayErrorMsg( 'We are not able to retrieve your tracking data due to a database error: ' . $statusArray[1]);
				return;
			}
			else $currentData = $statusArray[0];
			$currentData[$behaviorID]->calcAllMRF( $behaviorID);
			// print_r($currentData);
			// print_r($user->weeksSinceStart);
		}
		$MRFscores = calcMRFhistory ($user, $currentData, $trackedBehaviors);
		// print_r($MRFscores);
		HTML_tracking::displayHistory($user->weeksSinceStart, $MRFscores);
		unset($MRFscores);
}

// Display tracking summary.
//	This appears on  the homepage 
//
//  Includes reminder messages for tracking and action planning
//			Needed: Start date ( $user->startDate) 
//				Ap Done ( $user->planBehNo != 0)  
//				Last login  
//				Last tracking  
//				Today's date 
//
//			Calculate:
//				#days since last tracking
//				last login versus last tracking
//				#days/weeks since startDate( $user->weeksSinceStart) 
function displaySummary() {
global $userMsqlDB, $user, $rightNow, $behaviorSpecs;
global $currentData, $trackedBehaviors;

		
		// Retrieve previous login date from userLogin (not including today's login)
		// $user->lastLogin= strtotime($my->lastVisitDate);
		
		// Messages: count # logins in userLogin table
		//	if this is first login (# = 1) -> login msg
		//	if no *previous* login since start + 8 -> AP msg 1 AND we are past that date
		//	if no *previous* login since start + 13 wks -> AP msg 2 AND we are past that date
		//	if today > last track + 8 -> tracking msg
		
		// Only one msg at a time - so login msg overrides. If both AP and tracking, use combined msg
		
		$firstLoginMsg = false;
		$remindAPmsg1 = false;
		$remindAPmsg2 = false;
		$remindTRmsg = false;


		
		$remindTitle = '';
		$remindBody = ''; 
		
		$loginArray = getLastLogin($userMsqlDB, $user->userID);
		$previousLoginTime = strtotime($loginArray['time']);
		$previousLoginNum = $loginArray['num'];

		// echo '<br>previousLoginTime = ' . $loginArray['time'] ;
		// echo '<br>previousLoginNum = ' . $previousLoginNum ;
		// First Login message overrides all, so display this
		if ($previousLoginTime == null) {
			// first login msg
			//echo 'first login';
			$remindTitle = _HCC_FIRSTLOGIN_1;
			$remindBody = _HCC_FIRSTLOGIN_2; 
			HTMl_tracking::displayMessage( $remindTitle, $remindBody);
		}
		
		else {

			// RETRIEVE DATA FOR all BEHAVIORs TRACKED
			// alaoang with most recent tracking date (for any behavior)
			$statusArray = get2WeekData ($userMsqlDB, $user->userID, $currentData, $rightNow);
			//	Sets $user->lastTrackDate (not best place to place it, but easiest right now)
			if ($statusArray['msg'] != null) {
				// Error
				// Unable to retrieve any data, can't go any further
				HTML_tracking::displayErrorMsg( 'We are not able to retrieve and display your tracking data due to a database error: ' . $statusArray['msg']);
				return;
			}
			$user->lastTrackDate = $statusArray['last'];


			$startPlus8days = strtotime( date("Y-m-d H:i:s", $user->startDate) . " +8 days");
			// echo '<br>start + 8 date =' . date("Y-m-d H:i:s", $startPlus8days);
	
			$startPlus13wks = strtotime( date("Y-m-d H:i:s", $user->startDate) . " +13 weeks");
			// echo '<br>start + 13 weeks =' . date("Y-m-d H:i:s", $startPlus13wks);
	
			// >> Need to know whether previous login was also first login - if yes,
			//	display AP or tracking message
			//	Else do not display
			if ( $previousLoginNum == 1 ) {
				// 1 previous login, so First Login msg has been seen. Display AP msg if
				//	timeframe is correct
				if ($rightNow > $startPlus8days) {
					// AP reminder msg 1
					// echo 'first AP';
					$remindTitle = _HCC_APREMINDER1_1;
					$remindBody = _HCC_APREMINDER1_2; 
				}
				if ($rightNow > $startPlus13wks) {
					// AP reminder msg 2
					//echo 'second AP';
					$remindTitle = _HCC_APREMINDER2_1;
					$remindBody = _HCC_APREMINDER2_2; 
				}
			}
			
			else {
				// more than 1 previous login
				//	Need to determinw whether ot not the AP msg has already been displayed
				if ( ($previousLoginTime <= $startPlus8days) &&  ($rightNow > $startPlus8days) )
				{
					// AP reminder msg 1
					//echo 'first AP';
					$remindTitle = _HCC_APREMINDER1_1;
					$remindBody = _HCC_APREMINDER1_2; 
				}
				if ( ($previousLoginTime <= $startPlus13wks) &&  ($rightNow > $startPlus13wks) )
				{
					// AP reminder msg 2
					//echo 'second AP';
					$remindTitle = _HCC_APREMINDER2_1;
					$remindBody = _HCC_APREMINDER2_2; 
				}
			
			}
					
			
			if ( $user->lastTrackDate != 0 ) {
			
				// echo '<br>last track =' . date("Y-m-d H:i:s", $user->lastTrackDate) ;
			} else {
				// consider last trackdate = start date
				$user->lastTrackDate = $user->startDate;
				
				// echo '<br>No tracking so far..., today ='. date("Y-m-d H:i:s", $rightNow) ;
			}
			$lastTrackPlus8days = strtotime( date("Y-m-d H:i:s", $user->lastTrackDate) . " +8 days");
	
			if ( $rightNow > $lastTrackPlus8days ) {
				// echo '<br>track msg' ;
				// Combo message if we also have an AP message		
				if ($remindTitle == '' ) {
					$remindTitle = _HCC_TRACKREMINDER_1;
					$remindBody = _HCC_TRACKREMINDER_2; 
				}
				
				else  {
					$remindTitle = _HCC_COMBOREMINDER_1;
					$remindBody = _HCC_COMBOREMINDER_2; 
				}
			}
			
			foreach ($trackedBehaviors as $behaviorID) {
				$currentData[$behaviorID]->calcWeekAvg(($behaviorSpecs[$behaviorID]['weekavgtype'] ), $behaviorID);
				$currentData[$behaviorID]->calcTrendLevel($behaviorID);
				// echo "<br>beh $behaviorID, week data=";
				
				// print_r($currentData[$behaviorID]->weekArray);
				// print_r($currentData[$behaviorID]->prevWeekArray);
				// echo "<br>beh $behaviorID, trend level=";
				// print_r($currentData[$behaviorID]->prevWeekAvg);
				// print_r($currentData[$behaviorID]->trendLevel);
				// echo '<br>Trend fb msg = ' .$behaviorSpecs[$behaviorID]['tfeedback'][$currentData[$behaviorID]->trendLevel];
			}
			$MRFscore = calcMRFscore ($user, $currentData, $trackedBehaviors);
			HTMl_tracking::displayMessage( $remindTitle, $remindBody);
			HTMl_tracking::displaySummary($MRFscore, $remindTitle,$remindBody);
			
			
		}


}


// BEFORE WE DO ANYTHING, INITIALIZE OR RETRIEVE USER INFO
if (($user = initData()) == NULL) return;
// print_r($user);

// set array of tracked behaviors ( depending on whether or not user is tracking smoking)
// For now, assume all until have access to survey DB
$trackedBehaviors[] = 1;
$trackedBehaviors[] = 2;
$trackedBehaviors[] = 3;
// Remove multivitamin from tracking: Dave Rothfarb, 8-13-12
// $trackedBehaviors[] = 4;

// For smoking, retrieve survey information
//	If none, assume smoking?

if (!$user->nonSmoker) $trackedBehaviors[] = 5;
if ( $user->cigSmoked != 0 ) 
{
	// Change trend goal baseline value for cigarettes
	$behaviorSpecs[5]['trendgoal'] =$user->cigSmoked; 
}
// echo 'Cigarettes baseline = '.$behaviorSpecs[5]['trendgoal']; 	
// check module parameters first

$type = $params->get('type', 'default');
switch ($type) {
	case 'weekly':
	case 'history':
	// case 'back':
	case 'summary':
		$op = $type;
		break;
	default:
		break;
	
}



// Main Tracking module switch
// NOTE: Different module types 
switch ($op) {

	case "step2":
		// Make sure that day is valid date
		if ( isBackDate ($day) ) {
			$statusMsg = displayStep2Process($day);
			if ( $statusMsg != '') {
				HTML_tracking::displayStep1Entry($day, $statusMsg);
			}
		}
		else {
			HTML_tracking::displayErrorMsg('You cannot track for '. getDayofWeek($day). ' because it is before your start date');
		
		}
        break;
	case "history":
		displayHistory();
		break;
	case "weekly":
		displayWeekly();
		break;

	case 'summary':
		displaySummary();
		break;
	default:
		// Make sure that day is valid date
		if ( isBackDate ($day) ) {
			displayStep1Entry($day, '');
		
		}
		else {
        	HTML_tracking::displayTrackingHeader('Track my Health Habits for ' . getDayofWeek($day), 'Get help with Tracking',
			'index.php?option=com_content&amp;view=article&amp;id=22');
			HTML_tracking::displayErrorMsg('You cannot track on this date because it is before your start date');
		
		}
}	

unset( $currentData);
mysql_close( $userMsqlDB);
$userMsqlDB = null;
?>
