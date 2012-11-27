<?php
error_reporting(E_ALL);
/**
* @version 1.0 $
* @package  Plan for HD2
* @copyright (C) 2008 Therese Lung - DFCI/ CCBR/ HCC
*/


// Plan Module 
// General implementation:
//	Static tables hold power plan information indexed by behavior,
// 		-list of reasons
//		-list of strategies
//		-list of barriers
//		-list of tips 
//
//	Dynamic information is stored in the userPlan table in the database
//		Unlike BFBW, user sees a single Plan for all selected behaviors.
//		But like BFBW, internally we store a separate row of data for each behavior
//			and write it as it's completed
//		Each plan is uniquely identified by studyID  and behaviorID
//		Each plan's data consists of:
//			reason (write-in text) or option
//			strategy 1, strategy 2, as indices into the strategy array for the
//				behavior
//			barrier 1, barrier 2, as indices into the barrier array for the behavior
//
//	In addition, the userInfo table stores additional Plan information such as:
//		- plan reason (including write-in)
//		- support/buddy selected
//		- # behaviors selected - this is for internal use, allows code to determine whether
//		user has completed action plan. If not, start over next time
//		- # plans made throughout study - for stats analysis
//
//	Linear Process for each plan:
//		For a given behaviorID,
//		- list the strategies -> user selects 2
//		-process the strategy selection, save into hidden fields, list the barriers
//			-> user selects 2
//		-process the barriers, retrieve all user selections (including hidden field data
//			that has been passed from screen to screen) and save into database
//			(note: only one entry per userID/behaviorID combination
//		-display the summary for the power plan
//			-> user can print, or create another power plan
//
//
//	To handle the stages in the linear process within the same Joomla module,
//	the 'op' parameter is set in the URL to define the step in the process being
//	handled.
?>


<?php
 
/** ensure this file is being included by a parent file */
defined( '_JEXEC' ) or die( 'Restricted Access.' );


// $behaviorSpecs definition
global $behaviorSpecs;
global $powerSpecs;
require ( JPATH_SITE .'/includes/hd2/constants.php' );
require ( JPATH_SITE .'/includes/hd2/siteFunctions.php' );
require ( JPATH_SITE .'/includes/hd2/behavior.php' );
require ( JPATH_SITE .'/includes/hd2/points.php' );

// Action Plan functions
require ( JPATH_SITE .'/includes/hd2/plan.php' );
require ( JPATH_SITE .'/modules/mod_plan/mod_plan.html.php' );

// Retrieve page params so we can reconstruct it
global $option;
global $view;
global $item;
global $itemid;
global $id;	

global $rightNow;
global $userDB;
global $userMsqlDB;
global $user;

global $Plan;
$Plan = new Plan;
 
if (($user = initData()) == NULL) return;

// print_r($user);


// Joomla
$option = trim( JRequest::getVar( 'option'));
$id = trim( JRequest::getVar( 'id'));
$itemid = trim( JRequest::getVar( 'Itemid'));
$op = trim( JRequest::getVar( 'op'));


$view = trim( JRequest::getVar( 'view'));

// Current AP parameters, if specified
$Plan->reason = htmlentities( strip_tags(stripslashes(trim( JRequest::getVar( 'reason')))));
$Plan->reasonopt = htmlentities( stripslashes(trim( JRequest::getVar( 'reasonopt'))));
// Set reason option to zero if none chosen by default 
if ($Plan->reasonopt == '') $Plan->reasonopt = 0;	
	$Plan->beharray = JRequest::getVar( 'groupID');

$Plan->support = htmlentities( stripslashes(trim( JRequest::getVar( 'support'))));

$Plan->strategy = htmlentities( strip_tags(stripslashes(trim( JRequest::getVar( 'strategy')))));
$Plan->strategy1 = trim( JRequest::getVar( 'strategy1'));
$Plan->strategy2 = trim( JRequest::getVar( 'strategy2'));
if ($Plan->strategy2 == '') $Plan->strategy2 = 0;

$Plan->barrier1 = trim( JRequest::getVar( 'barrier1'));
$Plan->barrier2 = trim( JRequest::getVar( 'barrier2'));
$statusMsg = '';

// Main Plan module switch
//	op undefined: entry page (also default)
switch ($op) {
	case "step1":
		//  Plan Reasons
		initPlan();
		HTML_plan::displayPage2Reason('&op=step2');
		break;
	case "step2";
		// Process reasons, then display 
		// Plan Goal / Recommendations
		$statusMsg = processPage2Reason();
		if ($statusMsg != '') {
			HTML_plan::displayStatusMsg( $statusMsg );
			HTML_plan::displayPage2Reason('&op=step2');
		}
		else HTML_plan::displayPage3Goals('&op=step3');
		
		break;
	
	case "step3";
		// Plan Behavior choice
		HTML_plan::displayPage4ChooseBeh('&op=step4');
		
		break;
		
	case "step4";
		// Process behavior choices, then display
		// Plan Support
		$statusMsg = processPage4ChooseBeh();
		if ($statusMsg != '') {
			HTML_plan::displayStatusMsg( $statusMsg );
			HTML_plan::displayPage4ChooseBeh('&op=step4');
		}
		else HTML_plan::displayPage5Support('&op=step5');
		
		break;
	case "step5";
		// Process support choices, then display
		// Plan Skills
		$statusMsg = processPage5Support();
		if ($statusMsg != '') {
			HTML_plan::displayStatusMsg( $statusMsg );
			HTML_plan::displayPage5Support('&op=step5');
		}
		else HTML_plan::displayPage6Skills('&op=step6');
		
		break;
		
	case "step6";
		// Display Making Changes screen
		HTML_plan::displayPage7BehSummary('&op=step7a');
		
		break;

	case "step7a";
		// Check whether we came here from a step7b - ie. is there Plan form data submitted? 
		//	If so, write the Plan for the first behavior on the list
		//	Then:
		$statusMsg = processPage8BehBarrier();
		
		// Strategies for next behavior
		HTML_plan::displayPage8BehStrategy('&op=step7b');
		
		break;

	case "step7b";
		// Process strategy choices then display barriers
		// Barriers for next behavior
		$statusMsg = processPage8BehStrategy();
		if ($statusMsg != '') {
			HTML_plan::displayStatusMsg( $statusMsg );
			HTML_plan::displayPage8BehStrategy('&op=step7b');
		}
		else HTML_plan::displayPage8BehBarrier();
		
		break;
		
	case "view";
		$statusMsg = processPage8BehBarrier();
		if ($statusMsg != '') {
			HTML_plan::displayStatusMsg( $statusMsg );
			HTML_plan::displayPage8BehBarrier('&op=step7b');
		}
		
		else {
			$userPlans= getPlans();
			HTML_plan::displayPlan( $userPlans, false);
		}
		
		
		break;

	case "":
	
		if ( $user->planBehNo != 0)  {
			// Retrieve behavior plans
			$userPlans= getPlans();
			if (sizeof ($userPlans) == $user->planBehNo) {
				HTML_plan::displayPlan( $userPlans, true);
				break;
			}
		}
		HTML_plan::displayPage1Intro('&op=step1');
		break;
	default:
}

mysql_close( $userMsqlDB);
$userMsqlDB = null;

?>

