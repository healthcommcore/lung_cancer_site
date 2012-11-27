<html>
<head>
<title>Process Tracking - User Tracking</title>
</head>
<body>
<?php
define ( "JPATH_SITE", getcwd() . '/../');

require( JPATH_SITE .'/includes/hd2/config.php' );
require( JPATH_SITE .'/includes/hd2/constants.php' );
require( JPATH_SITE .'/includes/hd2/shared.php' );
require( JPATH_SITE .'/includes/hd2/behavior.php' );
require( JPATH_SITE .'/includes/hd2/user.php' );
require( JPATH_SITE .'/includes/hd2/data.php' );


global $user;
// REdefine for admin site
// Site-wide variable definitions
global $imageDir;
global $relSiteDir, $siteDir;

$imageDir = 'images/hd2/tracking/';
$siteDir= JPATH_SITE. "/images/hd2/tracking/";
$relSiteDir= "../images/hd2/tracking/";
require_once( JPATH_SITE .'/includes/hd2/chart.php' );
require_once( 'includes/initweb.php' );

global $trackedBehaviors;
// array of trackgoal objects (defined trackgoal.php)
//	contains current tracked behaviors, keyed by behaviorID
$trackedBehaviors = array();
$trackedBehaviors[] = 1;
$trackedBehaviors[] = 2;
$trackedBehaviors[] = 3;
$trackedBehaviors[] = 4;
	

// function drawChart($behaviorID, $userData, $weekFlag, $data, $user, $rightNow) {
function drawChartN($behaviorID, $userData, $weekFlag, $data, $user, $day, $N) {
global $relSiteDir,$imageDir, $siteDir, $inputFile;
global $behaviorSpecs;


	if ($weekFlag == true) {
		$inputFile= $siteDir. $behaviorSpecs[$behaviorID]['weekchart'];
		$outName = "weekIMG";
	}
	else {
		$inputFile= $siteDir. $behaviorSpecs[$behaviorID]['longchart'];
		$outName = "historyIMG";
	}
		
	// Behavior goal value
	$goal = $behaviorSpecs[$behaviorID]['dailygoal'];
	
	$outputFile= JPATH_SITE. '/'. $imageDir.$outName.$behaviorID.$N.$user->userID.".gif";
	$relOutputFile=$relSiteDir.$outName.$behaviorID.$N.$user->userID.".gif";
	
	
	
	$today = $_SERVER['REQUEST_TIME'];
	$mvgFile = "/tmp/". $outName.$behaviorID."_".$today.$N.$user->userID.".mvg";
    $fp=fopen($mvgFile,"w");
	
	/*
	echo '<br>day :';
	print_r($day  );
	echo '<br>input :';
	print_r($inputFile  );
	echo '<br>rel output :';
	print_r($relOutputFile  );
	echo '<br>output :';
	print_r($outputFile  );
	echo '<br>mvgFile :';
	print_r($mvgFile  );
	*/

	$numSegmentsY= $behaviorSpecs[$behaviorID]['labels'];
	// X segments do not vary - 7 or 26
	$numSegmentsX= ( $weekFlag == true) ? 7: 26; 

	// INITIALIZE VARIABLES
    $xAxisTitleCOLOR="#404040";
    $xAxisLabelCOLOR="#fb7d02";
	$chartBarCOLOR="#FF7601"; 
	$chartLineCOLOR="#34a6de";
	$circleStrokeCOLOR="#5F347C";
	$goalStrokeCOLOR="#FF7601"; 
	$goalFillCOLOR= "#003C63"; 
	
	$polyCOLOR = "#FFC38D";
	$lineWidth = 3;
	$circleWidth = 2;
	
	$fillCOLOR="#5F347C";
	$emptyCOLOR = "#FFFAF0";
	
	$yAxisTitleSIZE=10;	// For No Data bar
	
	if ($weekFlag == true) 
		$currentX = 	strftime('%u', $today)  ;
	else
		$currentX = $data;
	// echo '<br>current X :';
	// print_r($currentX  );
	// print_r($userData);

	// For weekly chart, display array data up today:  Data should contain data indexed from 1..7 where 7 is today 
	// (rolling 7 days). Else for 16-week chart, $data (current week) -1, because we are not displaying data for the current week
	// until it is completed
	
	
	//  show all 7 days for weekly chart
	// $loopLength = ( $weekFlag == true)? ((  $data == 1 ) ? $currentX: $currentX -1)  : ($data -1);
	$loopLength = ( $weekFlag == true)? 7  : ($data -1);

	// Current  data in $userData
	$origX = ($weekFlag == true )? 45 : 45;	// 52;
	$origY = ($weekFlag == true )? 35 : 31;	
	
	$endX = ($weekFlag == true )? 230 : 565;	//414;
	$endY = ($weekFlag == true )? 206 : 204;	
	
	$polyLine = '';
	$polyPoints = 0;
	
	$segmentWidth= ($endX - $origX )/($numSegmentsX);	
	// segmentHeight needs to take into account additional space on top = one segment
	$segmentHeight= ($endY - $origY )/($numSegmentsY + 1);	
	$barWidth = ($weekFlag == true )? 10 : 8;	

	$ratio = $behaviorSpecs[$behaviorID]['maxChartVal']/ ($numSegmentsY - 1);
	
	// Maximum representable value = $ratio * ($numSegmentsY + 1)
	//	to make sure we don't go over chart edge
	$maxChartVal = $ratio * ($numSegmentsY);

    fputs($fp,"font '". GRAPHFONT . "'\n");

	// Generate Date labels dynamically
	if ($weekFlag == true) {
		$labelCOLOR = "#FFFFFF";
		$days = array( 1=> 'M', 2=> 'T',3 =>'W',4 =>'T',5 =>'F',6 =>'S', 7=>'S');
	
		// First day of week is $currentX +1, unless $currentX = 7, => day = 1
	
		// Set up weekly array  in similar fashion
		$week = array();
		$c = 1;
		for ($i = $currentX +1 ; ($i <= count($days)); $i++, $c++) {
			$week[$c] = $days[$i];
		}
		
		if ($i > 1) {
			// We didn't start on Monday, now return to start of week
			for ($i = 1; ($i <= $currentX); $i++, $c++) {
				$week[$c] = $days[$i];
			}
		}
		// print_r($week);
		
		$i = 1;
		foreach ( $week as $weekday) {
			$x1 = $origX + (($i * $segmentWidth) - ($segmentWidth/2) -($barWidth/2) );
					
			// adjust X - affected by font size
			$cX = $x1 + ($barWidth/2) - 4;
				
			newElement($fp, array("eDef"=>array("TEXT","text ". ($cX) .",". ($endY +18)  . " '". $week[$i] ."'",13), "stroke"=>array(1,"round","round",$labelCOLOR, 1), "fill"=>array($labelCOLOR,1)));
			$i++;
		}
	}
	
	//echo "day = $day";
	//echo "CurrentX = $currentX";
	
	// Establish points for the Polyline first because everything else
	//	is drawn aboe the line.	
	for ($i=1; $i<= $loopLength; $i++) {	
		if ((isset($userData[$i])) && (ereg("[0-9]",$userData[$i]))) {
			// Redefine $userData value if above maxVal to maximum representable value
			if ($userData[$i]> $maxChartVal) {
				$userData[$i] = $maxChartVal ;
			}
				$barHeight = ($segmentHeight * $userData[$i]) / $ratio ; 
			if ($userData[$i]>= 0) {
					$y1 = max ($origY, $endY - $barHeight - $segmentHeight );
				}
				else $y1 = $endY;
				
				$x1 = $origX + (($i * $segmentWidth) - ($segmentWidth/2) -($barWidth/2));
				
				// Add point to polygon
				if ($polyLine != '' ) $polyLine .= ' ';
				$polyLine .= ($x1 +($barWidth/2)) .  "," . $y1;
				$polyPoints ++;
		}
	}
	// Draw the connecting lines
	if ( ($polyLine != '' )  && ($polyPoints > 1 ))  {
       	fputs($fp,"fill 'none'\n");
        fputs($fp,"stroke '" . $polyCOLOR . "'\n");
    	fputs($fp,"stroke-width ".$lineWidth."\n");
		fputs($fp, "polyline ". $polyLine . "\n");
	}
	
	// Now draw the bars and circles
	// It's necessary to draw the bars and circles AFTER the connecting line
	
	for ($i=1; $i<= $loopLength; $i++) {	
		if ((isset($userData[$i])) && (ereg("[0-9]",$userData[$i]))) {
	
				// Draw the bar except for data < 0
				$barHeight = ($segmentHeight * $userData[$i]) / $ratio ; 
				if ($userData[$i] >= 0) {
					$y1 = max ($origY, $endY - $barHeight - $segmentHeight );
					$circleFillCOLOR = $fillCOLOR;	// empty center
				}
				else {
					$y1 = $endY;	// for drawing circle
					$circleFillCOLOR = $emptyCOLOR;	// empty center
				}

				$y2 = $endY;
				$x1 = $origX + (($i * $segmentWidth) - ($segmentWidth/2) -($barWidth/2));
				$x2 = $x1 + $barWidth;

				if ($userData[$i] >= 0) {
					
					newElement($fp, array("eDef"=>array("POLY","rectangle ".$x1.",".$y1." ".$x2.",".$y2), "stroke"=>array(1,"square","miter",$chartBarCOLOR,0), "fill"=>array($chartBarCOLOR,1)));
				}
				
				// Add circle
				//	Check if we have met goal
				if  (
					(( $behaviorSpecs[$behaviorID]['goalcompare'] == '=') &&
					( $userData[$i] == $goal)) ||
				
				 	(( $behaviorSpecs[$behaviorID]['goalcompare'] == '>') &&
					( $userData[$i] >= $goal)) ||

				 	(( $behaviorSpecs[$behaviorID]['goalcompare'] == '<') &&
					( $userData[$i] <= $goal)) 
					)
					{
						$cX = $x1 + ($barWidth/2);
						$cY = $y1;
						
						$rX = $cX + ($barWidth/2) + 1;
						$rY = $cY;
						 
						newElement($fp,array("eDef"=>array("POLY","circle ".$cX.",".$cY." ".$rX.",".$rY), "stroke"=>array(1,"square","miter",$goalStrokeCOLOR,1.2), "fill"=>array($goalFillCOLOR,1)));
				}
				
				else {
					$cX = $x1 + ($barWidth/2);
					$cY = $y1;
					
					$rX = $cX + ($barWidth/2) - 1;
					$rY = $cY;
					 
					newElement($fp,array("eDef"=>array("POLY","circle ".$cX.",".$cY." ".$rX.",".$rY), "stroke"=>array(1,"square","miter",$chartBarCOLOR,2.1), "fill"=>array($chartBarCOLOR,1)));
				}
			}
			else if ($weekFlag == false) {
				/*
				// for 26-wk chart, display white vertical bar
				//	when insufficient data
				$noBarWidth = 10; //20;
				$noBarColor = "#cccccc";
				$noBarFill = "#FFFFFF";
				$y1 = $origY+1;
				$y2 = $endY;
				$x1 = $origX + (($i * $segmentWidth) - ($segmentWidth/2) -($noBarWidth/2));
				$x2 = $x1 + $noBarWidth;
					newElement($fp, array("eDef"=>array("POLY","rectangle ".$x1.",".$y1." ".$x2.",".$y2), "stroke"=>array(1,"square","miter",$noBarColor,1), "fill"=>array($noBarFill,1)));

				$titleLength=strlen(_HCC_FORM_NODATA_CHART)*($yAxisTitleSIZE  * .6);
				$marginX = (($endY - $origY) - $titleLength ) /2;
				
				mvgRotate($fp, -90);

				newElement($fp, array("eDef"=>array("TEXT","text ". -($titleLength + $origX + $marginX) .",". ($x1 + $segmentWidth/2 ). " '". _HCC_FORM_NODATA_CHART ."'",$yAxisTitleSIZE), "stroke"=>array(1,"round","round",$noBarColor,0), "fill"=>array($noBarColor,1)));
				mvgRotate($fp, 90);
				*/
				
			}
	}

    // =============================================================
    // X-AXIS RED CIRCLE for current Day of Week or current Week
    // =============================================================
	
	// For chart:

	if ($weekFlag == true) {
		$i = 7- $day;
	}
	else {
		$i = $currentX;
	}
	// echo '<br>axis i :';
	// print_r($i  );
	if (isset($userData[$i])) 
		$barHeight = $segmentHeight * ($userData[$i] / $ratio) ; 
	else $barHeight =0;
	
	$x1 = $origX + (($i * $segmentWidth) - ($segmentWidth/2) -($barWidth/2));
				
	// Add circle
	$cX = $x1 + ($barWidth/2);
	$cY = $endY + 14;
				
	$rX = $cX + ($barWidth/2) - 1.5;
	$rY = $endY + 22;
	
	/*

    fputs($fp,"fill 'none'\n");
    fputs($fp,"stroke '" . $goalStrokeCOLOR . "'\n");
    fputs($fp,"stroke-width ".$circleWidth."\n");
    fputs($fp,"stroke-antialias 1\n");
    fputs($fp,"circle ".$cX.",".$cY." ".$rX.",".$rY. "\n");
		*/
	
	//	arrow below x labels
	//	3 point polygon
	
	$cY += 20;
	$p1X = $cX - ($barWidth/2);
	$p1Y = $cY;
	
	$p2X = $cX + ($barWidth/2);
	$p2Y = $cY;
	
	$p3X = $cX; 
	$p3Y = $cY - 2 * ($barWidth/2); 
	
	$p4X = $cX - ($barWidth/2);
	$p4Y = $cY;
	
	$polyLine = "$p1X, $p1Y $p2X, $p2Y $p3X, $p3Y $p4X, $p4Y";
	// Draw the connecting lines
	fputs($fp,"fill ".$goalStrokeCOLOR."\n");
    fputs($fp,"stroke '" . $goalStrokeCOLOR . "'\n");
    fputs($fp,"stroke-width ".$lineWidth."\n");
	fputs($fp, "polyline ". $polyLine . "\n");

	fclose( $fp);
	
	$results=system("convert  -draw @".$mvgFile ." ".$inputFile." ".$outputFile);
	echo "<img src=\"$relOutputFile?".time()."\" border=0>";
}



// THIS IS A PRIVATE SPECIAL VERSION OF THE getAllData function - last-minute
// request, so simplest, if not best, way to implement it.

// Sets input parameter array with weekly average for a given behavior since
//	start of study
// set in $currentData
//	Returns status array
//	0 => currentData array, null if error
//	1 => error message if KO, else ''
function getAllDataN ($userMsqlDB, $user, $behaviorID, $currentData, $rightNow, $N) {
	
    // Adjust to start of day
	// $timeSTART=mktime(0,0,1,date("n",$stageStart),date("j",$stageStart),date("Y",$stageStart));
	$timeSTART=mktime(0,0,1,date("n",$user->startDate),date("j",$user->startDate),date("Y",$user->startDate));
	
	
	// Query database to select between start date and today
    $sql="SELECT userVal, timestamp FROM userTrack WHERE (studyID=".$user->userID.") AND (behaviorID=".$behaviorID.") order by timestamp ASC";
	// echo $sql;
	
 	$result = mysql_query($sql, $userMsqlDB ) ;
	if ($result) {
					// assign row info into $currentData
					$weekno = 1;		// start with first week
					$weekcount = 0;		// number data items for current week
					$weektotal = 0;		// running total
					$weekEND = strtotime(date("Y-m-d H:i:s", $timeSTART) . " +1 week");

				while ($row = mysql_fetch_assoc($result))  {
					// print_r($row);

						if ( $row['timestamp'] <= $weekEND ) {
							$weektotal += $row['userVal'];
							$weekcount++;
						}
						
						else {
							// calculate average for week, if meets minimal data requirements.
							if ($weekcount >= $N ) {
								$currentData[$behaviorID]->allArray[$weekno] = $weektotal/ $weekcount;
							}
							// Else NO data set for  $currentData[$behaviorID]->allArray[$weekno]
							//	But set var $allMRFArray to -1 
							// else  $currentData[$behaviorID]->weekMRF
							
							// determine which week timestamp corresponds to and
							//	update week # and weekEND
							$delta = intval (($row['timestamp'] - $weekEND ) / (7 *	_HCC_SECONDS_PER_DAY)) + 1;
							// reset totals to new value
							$weekno+= $delta;
							$weekEND = strtotime(date("Y-m-d H:i:s", $timeSTART) . " +". $weekno . " week");
							$weektotal = $row['userVal'];
							$weekcount = 1;
							
						}
					} // end rows
					
					// If there is data for final week, set it - although we are not currently displaying it
					if ($weekcount >= $N ) {
 						$currentData[$behaviorID]->allArray[$weekno] = $weektotal/ $weekcount;
					}
    }
	else {
		// Error query
		$msg = mysql_error( $userMsqlDB);
		return array( 0=> null , 1=> $msg);
	}
	
	// print_r($currentData);
	// return null;
	return array( 0=> $currentData , 1=> '' );

}


function displayHistoryN( $studyID) {
global $trackedBehaviors, $behaviorSpecs;

	// Connect to the database
	$msg ='';
	
	$statusArray = dbMysqlConnect(USERDB);
	if (! $statusArray[0] ) {
		$msg =  'Unable to connect to user database';
		return array( 0=> 0, 1=> $msg);
	
	}
	else $userMsqlDB = $statusArray[0];
	
	
	$allUsers = array();
	$sql = "SELECT userInfo.studyID FROM userInfo";
	// echo $sql;
	$result = mysql_query($sql, $userMsqlDB ) ;
	if ($result) {
			if  (mysql_num_rows($result) > 0) {

				while( $row = mysql_fetch_assoc($result)) {
					$allUsers[] = $row['studyID'];
				}
			}
			else {
				echo ( "No web users to report");
				return;
			}
	}
	// print_r($allUsers);

	// Loop through user list
	foreach ($allUsers as $studyID) {
		$tempcount++;
			echo "<h2>$studyID</h2>";
	
		$statusArray = initUserDataFromStudyID($userMsqlDB, $studyID);
		if ($statusArray[0] == null)  {
			if ($statusArray[1] == '') {
				echo '<b>There is no web data for this user.  The user is probably not in the web Ix.</b>';
			} else {
				echo '<b>There was a technical error - please seek technical help</b>: '.$statusArray[1] ;		
			}
			return;
		}	
	
		$user = $statusArray[0];
		
		// For smoking, retrieve survey information
		//	If none, assume smoking?
		if (!$user->nonSmoker) $trackedBehaviors[4] = 5;
		
	
		$currentData = array();	
		for ($i = 1; $i <= count($behaviorSpecs); $i++) {
			$currentData[$i] = new trackData;
		}
	
		echo '<table cellpadding="0"  cellspacing="0" border="0"><tr><td>';
		
		// print_r($currentData);
		// print_r($trackedBehaviors);
	
		for ($i = 3; $i < 8; $i++)  {
			echo "<h2>$studyID, $i+ tracked per week</h2>";
	
			foreach ($trackedBehaviors as $behaviorID) {
					echo '<tr><td><h4>';
					echo ucwords($behaviorSpecs[$behaviorID]['sname']); 
					echo '</h4>';
						
					$statusArray = getAllDataN ($userMsqlDB, $user, $behaviorID, $currentData, $_SERVER['REQUEST_TIME'], $i);
					if ( ($statusArray[0] == null) && ($statusArray[1] != '') ) {
						echo 'We are not able to retrieve your tracking data due to a database error: ' . $statusArray[1];
						return;
					}
					else $currentData = $statusArray[0];
				
					
					// $currentData[$behaviorID]->calcAllMRF( $behaviorID);
					// print_r($currentData);
					// print_r($user->weeksSinceStart);
						
					drawChartN($behaviorID, $currentData[$behaviorID]->allArray, false, $user->weeksSinceStart, $user, $_SERVER['REQUEST_TIME'], $i);
					echo '</td></tr>';
				}	
			// $MRFscores = calcMRFhistory ($user, $currentData, $trackedBehaviors);
			echo '<tr><td><h4>';
		
			echo '</table>';
			unset($currentData);
		
		}
		// if ($tempcount >= 3) break;
	}
	
	// Status values returned??
	mysql_close($userMsqlDB);
	
	return array( 0=> $statusArray[0], 1=> $msg);


}

		displayHistoryN ( $studyID);


?>
</body>
</html>
