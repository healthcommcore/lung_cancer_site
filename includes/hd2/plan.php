<?php

class userPlan {
	var $behaviorID;
	var $strategy1;		
	var $strategy2;		
	var $strategy;	// write-in	
	var $barrier1;		
	var $barrier2;		
}

class Plan {
	var $reason;	// if write-in	
	var $reasonopt;		//  if reason option
	var $support;		//  if support option
	var $beharray;		// array of *selected* behaviors in plan
}

global $planReasons;

$planReasons = array(
	1 => 'I want to feel healthier.',
	2 => 'I want to have more energy.',
	3 => 'It&rsquo;s important to set a good example for my family.'
);



function initPlan() {
global $user;
global $userDB;

	// Reset Plan fields from userInfo table
	$sql="UPDATE userInfo SET planReason = null, planReasonopt =0, planSupport ='', planBehNo=0 WHERE studyID=$user->userID";
	// echo $sql;
	$userDB->setQuery( $sql);	
	$result =$userDB->query();
		
	if (!$result) {
			printf( _HCC_TKG_DB_UPDATE . ": %s\n",$userDB->getErrorMsg());
			return NULL;
	}
	

	$sql="DELETE FROM userPlan WHERE studyID=$user->userID";
	// echo $sql;
	
	// Remove all existing behavior plans (userPlan table entries ) for this user
    $userDB->setQuery($sql);	// Fetch ONE Row
    $result =$userDB->query();
			if (!$result) {
				printf( _HCC_TKG_DB_INSERT . ": %s\n",$userDB->getErrorMsg());
				return NULL;
			}
	
}


// checked that a reason has been specified.
function processPage2Reason() {
global $user;

global $Plan;

	// Priority to write-in if it's set
	if ($Plan->reason == '') {
		// Then an option has to be chosen
		if ( !isset($Plan->reasonopt) || ($Plan->reasonopt == 0) ) {
			return (_HCC_PP_ERR_REASON);
		}
	
	}
	else $Plan->reasonopt = 0;	
	
	//echo "Plan reason =$Plan->reason"; 	
	//echo "Plan reasonopt =$Plan->reasonopt"; 	

}

// checked that a reason has been specified.
function processPage4ChooseBeh() {

global $Plan;

	$Plan->beharray = JRequest::getVar( 'groupID');
	if (sizeof($Plan->beharray) < 1 ) {
			return (_HCC_PP_ERR_HABITS);

	}
	// print_r($Plan->beharray);
}

// checked that a reason has been specified.
function processPage5Support() {
global $strategySpecs, $powerSpecs, $barrierSpecs, $user;
global $userDB, $userMsqlDB;
global $Plan, $my;

	$Plan->support = JRequest::getVar( 'support');
	if (trim($Plan->support) == '' ) {
			return (_HCC_PP_ERR_SUPPORT);

	}
	// print_r($Plan->support);
	// $Plan->beharray = JRequest::getVar( 'groupID');
	// print_r($Plan->beharray);
	
	$behNo = sizeof($Plan->beharray );


	// Write data into userInfo table
	// 	for studyID
	//		reason, reasonopt, support
	$sql="UPDATE userInfo SET planReason ='" . addslashes($Plan->reason) . "', planReasonopt =" .$Plan->reasonopt . ", planSupport ='" .addslashes($Plan->support) . "', planBehNo =" .$behNo . " , planCount=planCount+1 WHERE studyID=$user->userID";
	// echo $sql;
	
	$userDB->setQuery( $sql);	
	$result =$userDB->query();
		
	if (!$result) {
			// Print Message instead of returning error status ?? >> should we really go on?
			return( _HCC_TKG_DB_UPDATE . $userDB->getErrorMsg());
	}

	addPoints( $userMsqlDB, $user->userID, _HCC_RAFFLE_PLAN);



}


// checked that two strategies have been specified.
function processPage8BehStrategy() {
global $Plan;


	// Identify which strategies were selected. 
	//	including write-in
	
	// checkbox next to write-in MAY or MAY NOT be checked 
	$strategies = JRequest::getVar( 'strategyID');
	// print_r($strategies);

	if ($Plan->strategy == '') {
		// If no write-in, two options need to be chosen,
		//	and one cannot be the write-in checkbox
		if (sizeof($strategies) != 2 ) {
			return (_HCC_PP_ERR_2STR);
		}
		if ((sizeof($strategies) == 2 ) && (
			($strategies[0] ==0) || ($strategies[1] ==0)))  {
			return (_HCC_PP_ERR_2STR);
		}
	}
	else {
		// print("write in strategy=");
		// print_r($Plan->strategy);
		
		// If there is write-in, there should either be:
		//	one option chosen (but not the write-in)
		//	or two options, one being the write-in
		if (sizeof($strategies) < 1 )  {
			return (_HCC_PP_ERR_2STR);
		}
		if ((sizeof($strategies) > 1 ) && (
			($strategies[0] !=0) && ($strategies[1] !=0)))  {
			return (_HCC_PP_ERR_2STR);
		}
		if ((sizeof($strategies) == 1 ) && 
			($strategies[0] ==0 ))  {
			return (_HCC_PP_ERR_2STR);
		}
	}
	// One of these may be = 0, in which case the data will
	// be ignored
	$Plan->strategy1 = $strategies[0];
	$Plan->strategy2 = $strategies[1];

}

// checked that two strategies have been specified.
function processPage8BehBarrier() {
global $Plan, $user, $userDB;
	// echo 'processPage8BehBarrier';
	
	if ( (JRequest::getVar( 'processbarrier')) == 1) {
	
	// echo 'processBarrier<br>';
		// Check whether there are any barriers and strategies
		// Identify which barriers were selected. Needs to be = 2
		$barriers = JRequest::getVar( 'barrierID');
				
		// Error handling
		if ((sizeof($barriers) < 1 ) || (sizeof($barriers) > 2 ) ){
			return (_HCC_PP_ERR_2BAR);
		}
		$Plan->barrier1 = intval($barriers[0]);	// Must convert to integer in case it is a string
		
		if (isset( $barriers[1]))
			$Plan->barrier2 = intval($barriers[1]);
		else 
			$Plan->barrier2 =0;
	
		// Write plan
		$behavior = $Plan->beharray[0];
			

		// >> Insert or UPDATE (check if existing entry for userID and behaviorID (in case user reloads page )
		$sql="SELECT id FROM userPlan WHERE (studyID=".$user->userID.") AND 
			(behaviorID=$behavior) LIMIT 1";
		$userDB->setQuery($sql);	
		$resultID=$userDB->query();
		if (($resultID) &&  ($userDB->getNumRows($resultID) >0)) {
			$row =$userDB->loadRow();
			$sql="UPDATE userPlan set studyID = $user->userID, behaviorID = $behavior, strategy = '". addslashes($Plan->strategy) . "', strategy1 = $Plan->strategy1, strategy2 = $Plan->strategy2, barrier1 = $Plan->barrier1, barrier2 =$Plan->barrier2  WHERE id = " . $row[0] ;
					
		}
		else {
			$sql="INSERT INTO userPlan (studyID, behaviorID, strategy, strategy1, strategy2, barrier1, barrier2 ) VALUES ($user->userID, $behavior, '". addslashes($Plan->strategy) . "', $Plan->strategy1, $Plan->strategy2, $Plan->barrier1, $Plan->barrier2 ) ";
		
		}

		
		// echo '<br>'. $sql;		
		$userDB->setQuery($sql);	
		$resultID=$userDB->query();
		if (!$resultID) {
			// What to do on error?
				echo ( _HCC_TKG_DB_INSERT . ':'. $userDB->getErrorMsg());
			
		}
	
		// Move to next behavior in array
		array_shift( $Plan->beharray);
		// print_r($Plan->beharray);
	}

}

// Retrieve all behavior plans for user

function getPlans() {
global $Plan, $user,$userDB ;

	//echo 'getPlans';
	
	$Plan->reason = $user->planReason;
	$Plan->reasonopt = $user->planReasonopt;
	$Plan->support = $user->planSupport;
	// print_r( $Plan); 

	// Determine which have associated plans
    $sql="SELECT * FROM userPlan WHERE (studyID=".$user->userID.") ORDER BY behaviorID";
	$userDB->setQuery($sql);	
	$resultID=$userDB->query();
	if ($resultID) {
	        if ($userDB->getNumRows($resultID) >0) {
					$allrows =$userDB->loadAssocList();
					foreach ($allrows as $row) {
						$userPlans[$row['behaviorID']]  = new userPlan;
						$userPlans[$row['behaviorID']]->behaviorID = $row['behaviorID'];
						$userPlans[$row['behaviorID']]->strategy = $row['strategy'];
						$userPlans[$row['behaviorID']]->strategy1 = $row['strategy1'];
						$userPlans[$row['behaviorID']]->strategy2 = $row['strategy2'];
						$userPlans[$row['behaviorID']]->barrier1 = $row['barrier1'];
						$userPlans[$row['behaviorID']]->barrier2 = $row['barrier2'];
					}
			}	
	}
	// print_r( $userPlans);
	return ($userPlans);
}


?>
