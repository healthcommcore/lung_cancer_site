<?php
// retrieve data for past 7 days for all behaviors.  Since it is possible
//	to change behaviors during course of the week, there may be data
//	for previous behaviors; it will be up to the caller to
//	look up only tracked behavior(s)
//	
//	For each behavior, tracking info is returned in 
// 	 	$currentData[behaviorID] trackData class
//	Depending on the data being retrieved, only parts of the trackData
//		structure will be fillled in.
//
//		getWeekData
//			retrieve weekly data for all tracked behaviors
//			(used for summary display)
//		getAllData
//			retrieve historical data (since start date)
//			for a specific behavior (or all?)
//
//	Weekly data: getBehaviorWeekData(), getWeekData()
//		trackData.weekArray 
//		$currentData[behaviorID]->weekArray [day of week = 1 -> 7]
//			where Monday =1
//			if data exists for any given day and behavior
//		
// calcWeekAvg:
//		Insufficient data points: average  = -1
//
//	historical data: trackData.allArray 
//		$currentData[behaviorID]->allArray[$weekno] = average for
//		the week. If insufficient data exists, there is *no* data for that weekno
//

// Global definitions required
// $behaviorSpecs
function calcWeekAvgNum ( $weekArray) {
	if (sizeof($weekArray) < _HCC_TRACKING_FEEDBACK_MIN) return -1;
	return round (( array_sum( $weekArray) / sizeof($weekArray)), 1);
	
}
function calcWeekAvgTotal ( $weekArray) {
	if (sizeof($weekArray) < _HCC_TRACKING_FEEDBACK_MIN) return -1;
	return  array_sum( $weekArray) ;
	
}
function calcWeekNumVal ( $weekArray, $val) {
	if (sizeof($weekArray) < _HCC_TRACKING_FEEDBACK_MIN) return -1;
	// Number of times $val appears in $weekArray
	$count = 0;
	foreach ($weekArray as $day) {	
		if ($day == $val ) $count ++;
	}
	return $count;
}

// Data for a particular behavior
class trackData {
	var $weekArray;		// variable size, up to 7, depending on data set
	var $prevWeekArray;		// variable size, up to 7, depending on data set
	var $weekAvg;		// -1 if not enough data
	var $prevWeekAvg;		// -1 if not enough data
	var	$allArray;		// weekly averages, all weeks
	var $weekMRF;		// per behavior for current week. -1 if not enough data
	var $allMRFArray;	// per behavior, for all weeks
	var $trendLevel;	// calculated by calcTrendLevel. Levels 1 - 5. 0, undefined, -1 means no data
	//	FOR WEEKLY FEEDBACK average calculations only
	// Note special cases - would like to change this in future
	//	versions:
	//		Insufficient data points: average  = -1 
	function calcWeekAvg ( $feedbacktype, $behaviorID) {
		// Check that there are at least 4 days of data for the week
		
		
		//if (sizeof($this->weekArray) < _HCC_TRACKING_FEEDBACK_MIN) 
			// $this->weekAvg = -1;
			
			
		// else
		
			// call function depending on type of data 
			switch ( $feedbacktype ) {
				case _HCC_TRACKING_AVG_YES:
					$this->weekAvg = calcWeekNumVal( $this->weekArray, 1);
					$this->prevWeekAvg = calcWeekNumVal( $this->prevWeekArray, 1);
					break;
				case _HCC_TRACKING_AVG_NO:
					$this->weekAvg = calcWeekNumVal( $this->weekArray, 0);
					$this->prevWeekAvg = calcWeekNumVal( $this->prevWeekArray, 0);
					break;
				case _HCC_TRACKING_AVG_DAILY:
					$this->weekAvg = calcWeekAvgNum( $this->weekArray);
					$this->prevWeekAvg = calcWeekAvgNum( $this->prevWeekArray);
					break;
				case _HCC_TRACKING_AVG_TOTAL:
					$this->weekAvg = calcWeekAvgTotal( $this->weekArray);
					$this->prevWeekAvg = calcWeekAvgTotal( $this->prevWeekArray);
					break;
				default:
					$this->weekAvg = calcWeekAvgNum( $this->weekArray);
					$this->prevWeekAvg = calcWeekAvgNum( $this->prevWeekArray);
			}
		// echo "calcWeekAvg for behaviorID $behaviorID = ". $this->weekAvg;
	}


	// If we have enough data points for the current week, calculate MRF score for each behavior
	
	function calcAllMRF ( $behaviorID) {
		global $behaviorSpecs;
		// Check that there are at least 4 days of data for the week
		
		// echo '<br>calcAllMRF allArray:';
		// print_r($this->allArray);
		// echo "<br>Calculating MRF score for behavior $behaviorID";
		if ( !isset($this->allArray)) return;
		
		foreach( $this->allArray as $weekno => $weekavg ) {
			// If no data, no MRF score (-1)
			// if ( $weekavg < 0 ) $this->allMRFArray[$weekno] = $weekavg;
			// else 
			{
				// echo "<br>Calculating MRF score for weekno $weekno, behavior $behaviorID";
				$total =  $weekavg * 7;
				$wg = $behaviorSpecs[$behaviorID]['weeklygoal'];
				$min = $behaviorSpecs[$behaviorID]['weeklyMRFmin'];
				// All <= 0  => 0
				//	All >= 1 => 1
				switch ( $behaviorSpecs[$behaviorID]['goalcompare'] ) {
					case '>':
						$this->allMRFArray[$weekno] = ($total - $min)/ ($wg - $min);
						break;
					case '<':
						$this->allMRFArray[$weekno] = 1 - (($total - $wg)/($min - $wg));
						break;
					default:
						// ERROR
				}
				$this->allMRFArray[$weekno] = ($this->allMRFArray[$weekno] > 1) ? 1: round($this->allMRFArray[$weekno],1 );
				$this->allMRFArray[$weekno] = ($this->allMRFArray[$weekno] < 0) ? 0: round($this->allMRFArray[$weekno],1 );
		
	
	
			}

		}
		// echo '<br>calcAllMRF allMRFArray:';
		// print_r($this->allMRFArray); 
	}
	
	function calcWeekMRF ( $behaviorID) {
		global $behaviorSpecs;
		// Check that there are at least 4 days of data for the week
		
		
		
		if (count($this->weekArray) < _HCC_TRACKING_FEEDBACK_MIN) 
			$this->weekMRF = -1;
		else {
			// get daily average based on available data points
			$total =  (array_sum($this->weekArray) / count($this->weekArray)) * 7;
			$wg = $behaviorSpecs[$behaviorID]['weeklygoal'];
			$min = $behaviorSpecs[$behaviorID]['weeklyMRFmin'];
			

			// All <= 0  => 0
			//	All >= 1 => 1
			switch ( $behaviorSpecs[$behaviorID]['goalcompare'] ) {
				case '>':
					$this->weekMRF = ($total - $min)/ ($wg - $min);
					break;
				case '<':
					$this->weekMRF = 1 - (($total - $wg)/($min - $wg));
					break;
				default:
					// ERROR
			}
			$this->weekMRF = ($this->weekMRF > 1) ? 1: round($this->weekMRF,1 );
			$this->weekMRF = ($this->weekMRF < 0) ? 0: round($this->weekMRF,1 );
		}
	}
	
	function calcTrendLevel($behaviorID) {
	global $behaviorSpecs;
		// ONLY if there is sufficient data for an average
		// -1 if not available
		// echo "<br>beh $behaviorID weekav = $this->weekAvg, prevweekavg  = $this->prevWeekAvg";
		if ( ($this->weekAvg < 0) ||  ($this->prevWeekAvg < 0) ) $this->trendLevel = 0;
		
		else {
		
			$tg = $behaviorSpecs[$behaviorID]['trendgoal'];
			$diff = $this->weekAvg - $this->prevWeekAvg;
			// echo "<br>beh $behaviorID trend diff =". $diff;
			$per = ($diff/$tg) * 100;
			// $tenper = $tg/10;
			// $twentyper = $tg/5;
			// echo "<br>beh $behaviorID trend per %  = ". $per . " of goal ". $tg;
			if ($per <= -20) $this->trendLevel = 1;
			else {
				if ($per <= - 10) $this->trendLevel = 2;
				else { 
					if ($per >= 20) $this->trendLevel = 5;
					else {
						if ($per >= 10) $this->trendLevel = 4;
	
						else $this->trendLevel = 3;
					}
				}
			}
		}
	}


}


// Retrieves data for the week up until now
//	All behavior data is retrieved for the current user and time range
//
//	returns null if all ok, data in $currentData parameter
//	returns msg if error
function getWeekData ($userMsqlDB, $userID, $currentData, $rightNow) {
        $daySTART=mktime(0,0,1,date("n",$rightNow),date("j",$rightNow),date("Y",$rightNow));
        $dayEND=$rightNow;
		// $weekSTART = strtotime(date("Y-m-d H:i:s", $rightNow) . " -6 days");
		$weekSTART = strtotime(date("Y-m-d H:i:s", $daySTART) . " -6 days");
	
		// echo '<br>getweekdata weekstart = '. date("Y-m-d H:i:s", $weekSTART);	
		
        $weekEND=$dayEND;						// week's end = dayEND
		$todayDoW = strftime("%u", $rightNow);


        $sql="SELECT behaviorID, userVal, timestamp FROM userTrack WHERE (studyID=".$userID.") AND (timestamp>".$weekSTART.") AND (timestamp<=".$dayEND.") ORDER BY timestamp";
		// echo $sql;
		
	 	$result = mysql_query($sql, $userMsqlDB ) ;
        if ($result) {
			while ($row = mysql_fetch_assoc($result))  {
							
							// assign row info into $currentData
								// add data point to weekArray
								// indexed by day of week
								$DoW = strftime("%u", $row['timestamp']);
								
								// Change indexing to use past 7 days, with current day = 7
								$arrayIndex = $DoW + ( 7 - $todayDoW);
								if ($arrayIndex > 7 ) $arrayIndex = $arrayIndex % 7;
							
								$currentData[$row['behaviorID']]->weekArray[$arrayIndex] = $row['userVal'];
			}
		}
		else {
			// Error query
			$msg = _HCC_TKG_DB_SELECT . mysql_error( $userMsqlDB);
			return array( 0=> null , 1=> $msg);
		}
		/*
		foreach ($trackedBehaviors as $behaviorID => $tBehavior) {
			$currentData[$behaviorID]->calcWeekAvg(($behaviorSpecs[$behaviorID]['feedbacktype'] ), $behaviorID);
		}
		*/
		// echo 'getWeekData';
		// print_r($currentData[1]->weekArray);
		return array( 0=> $currentData, 1=> '');
}

// Retrieves data for the week up until now
//	All behavior data is retrieved for the current user and time range
//
function get2WeekData ($userMsqlDB, $userID, $currentData, $rightNow) {
        $daySTART=mktime(0,0,1,date("n",$rightNow),date("j",$rightNow),date("Y",$rightNow));
        $dayEND=$rightNow;
		// $TwoWeekSTART = strtotime(date("Y-m-d H:i:s", $rightNow) . " -2 week");
		// $OneWeekSTART = strtotime(date("Y-m-d H:i:s", $rightNow) . " -1 week");
		$TwoWeekSTART = strtotime(date("Y-m-d H:i:s", $daySTART) . " -13 days");
		$OneWeekSTART = strtotime(date("Y-m-d H:i:s", $daySTART) . " -6 days");

		// echo '<br>getweekdata OneWeekSTART = '. date("Y-m-d H:i:s", $OneWeekSTART);	
		// echo '<br>getweekdata TwoWeekSTART = '. date("Y-m-d H:i:s", $TwoWeekSTART);	
		
        $weekEND=$dayEND;						// week's end = dayEND
		$todayDoW = strftime("%u", $rightNow);


        $sql="SELECT behaviorID, userVal, timestamp FROM userTrack WHERE (studyID=".$userID.") AND (timestamp>".$TwoWeekSTART.
			") AND (timestamp<=".$dayEND.") order by timestamp ASC";
		// echo $sql;
		
	 	$result = mysql_query($sql, $userMsqlDB ) ;
        if ($result) {
			while ($row = mysql_fetch_assoc($result))  {
				$lastTimestamp = $row['timestamp'];
				
				$DoW = strftime("%u", $row['timestamp']);
				// echo "<br>Day of Week = $DoW";
								
				// Change indexing to use past 7 days, with current day = 7
				$arrayIndex = $DoW + ( 7 - $todayDoW);
				if ($arrayIndex > 7 ) $arrayIndex = $arrayIndex % 7;
				// echo "<br>Array index = $arrayIndex";
							
				// Figure out which week the data falls in
				if ($row['timestamp'] > $OneWeekSTART) {
					$currentData[$row['behaviorID']]->weekArray[$arrayIndex] = $row['userVal'];
			
				}
				else {
					$currentData[$row['behaviorID']]->prevWeekArray[$arrayIndex] = $row['userVal'];
			
				}
								
			}
		}
		else {
			$msg = _HCC_TKG_DB_SELECT . mysql_error( $userMsqlDB);
			return array( 'msg' => $msg, 'last' => 0);	
	
		}
		
		return array( 'msg' => null, 'last' => $lastTimestamp);	
}

// THIS HAS BEEN CHANGED
// Sets input parameter array with weekly average for a given behavior since
//	start of study
// set in $currentData
//	Returns status array
//	0 => currentData array, null if error
//	1 => error message if KO, else ''
function getAllData ($userMsqlDB, $user, $behaviorID, $currentData, $rightNow) {
	
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
							if ($weekcount >= _HCC_TRACKING_FEEDBACK_MIN ) {
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
					if ($weekcount >= _HCC_TRACKING_FEEDBACK_MIN ) {
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


// Saves data for the specified user, behavior ID and time
// Returns null if successful
//	else returns error message
function saveTrackData ($userMsqlDB, $userID, $behaviorID, $data, $time) {
		$daySTART=mktime(0,0,1,date("n",$time),date("j",$time),date("Y",$time));
        $dayEND=mktime(23,59,59,date("n",$time),date("j",$time),date("Y",$time));
        $sql="SELECT * FROM userTrack WHERE (studyID=".$userID.") AND (behaviorID=".$behaviorID.") AND (timestamp>".$daySTART.") AND (timestamp<".$dayEND.")";
		
 		$result = mysql_query($sql, $userMsqlDB ) ;
		
        if ($result) {
            if (mysql_num_rows($result)>0) {
                $sql="UPDATE userTrack SET userVal=".$data.", timestamp=".$time." WHERE (studyID=".$userID.") AND (behaviorID=".$behaviorID.") AND (timestamp>".$daySTART.") AND (timestamp<".$dayEND.")";
 				$result = mysql_query($sql, $userMsqlDB ) ;
 				if (!$result) 	return (_HCC_TKG_DB_UPDATE . mysql_error($userMsqlDB));
           } else {
                $sql="INSERT INTO userTrack (studyID,behaviorID,userVal,timestamp) VALUES (".$userID.",".$behaviorID.",".$data.",". $time.")";
				// echo $sql;
 				$result = mysql_query($sql, $userMsqlDB ) ;
				if (!$result) {
							return (_HCC_TKG_DB_INSERT . mysql_error($userMsqlDB));
				}
				addPoints( $userMsqlDB, $userID, _HCC_RAFFLE_TRACK);

            }
        }
		else {
				return (_HCC_TKG_DB_SELECT . mysql_error($userMsqlDB));
		}
		return null;
}

// Compile  scores IF none has -1 (no data) evaluation
function calcMRFscore ($user, $currentData, $trackedBehaviors) {
		$MRFscore = 0;
		foreach ($trackedBehaviors as $behaviorID) {
			$currentData[$behaviorID]->calcWeekMRF($behaviorID);
			//echo "MRF score for behavior  $behaviorID = ". $currentData[$behaviorID]->weekMRF;
			
			
				if ( ($currentData[$behaviorID]->weekMRF >= 0) && ($MRFscore >= 0)) {
					$MRFscore+=$currentData[$behaviorID]->weekMRF;
				}
				else 
					$MRFscore= -1;

		}
		// Add full score for non-smokers
	
		if( ($MRFscore >= 0) &&  ( $user->nonSmoker) ) $MRFscore += 1;

		return $MRFscore;
}

function calcMRFhistory ($user, $currentData, $trackedBehaviors) {
		$MRFscores = array();		// indexed by week number
		
		if (!$currentData) return $MRFscores;
		
		for ($weekno = 0; $weekno < $user->weeksSinceStart ; $weekno ++) {
			foreach ($trackedBehaviors as $behaviorID) {
				if ( isset($currentData[$behaviorID]->allMRFArray[$weekno]) && ($MRFscores[$weekno] >= 0)) {
						$MRFscores[$weekno]+=$currentData[$behaviorID]->allMRFArray[$weekno];
				}
				// else do not set any value
				// else $MRFscores[$weekno]= -1;
			}
			// Add full score for non-smokers
			// if( ($MRFscores[$weekno] >= 0) &&  ($user->nonSmoker) ) $MRFscores[$weekno] += 1;
			if( isset($MRFscores[$weekno]) &&  ($user->nonSmoker) ) $MRFscores[$weekno] += 1;

		}
		
		
		// print_r( $MRFscores);
		return $MRFscores;
}

?>