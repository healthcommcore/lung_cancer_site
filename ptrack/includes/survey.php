<?php
require_once( JPATH_SITE .'/includes/hd2/config.php' );
require_once( JPATH_SITE .'/includes/hd2/constants.php' );
require ( JPATH_SITE .'/includes/hd2/behavior.php' );
require_once( JPATH_SITE .'/includes/hd2/shared.php' );

// For all behaviors, set up Group ID and Question ID as variables to be used for retrieval
//	(easier to modify if survey questions and order changes
// >> could create overall array with arrays of values?


// PA  ??
global $pa_survey;


// studyID
$id_survey = array(
	'gid' => 1,
	'qid' => 30
);

// date survey taken
$date_survey = array(
	'gid' => 1,
	'qid' => 31
);

// PA - special type of implementation
// Moderate
$pam_survey = array(
	'gid' => 3,
	'days_qid' => 8,
	'hours_qid' => array( 'set' => '91', 'value' => '91comment'),
	'min_qid' => array('set' => '92', 'value' => '92comment')
);

// Vigorous
$pav_survey = array(
	'gid' => 3,
	'days_qid' => 33,
	'hours_qid' => array( 'set' => '341', 'value' => '341comment'),
	'min_qid' => array('set' => '342', 'value' => '342comment')
);

// MV
global $mv_survey;

$mv_survey = array(
	'gid' => 9,
	'qid' => 1
);

// FV
global $fv_survey;

$fv_survey = array(
		'gid' => 10,
		'qid' => array('2q1', '2q2','2q3','2q4','2q5','2q6','2q7')
);

// 99 = no answer. Also in case there are null values -> no data

$fv_survey_eval = array(	1=> 0,  2=> 2/28,  3=> 1.5/7, 4=> 3.5/7,  5=> 5.5/7, 6 =>1, 7=> 2, 8=> 3, 9=> 4, 10=> 5);

global $rm_survey;

$rm_survey = array(
		'gid' => 2,
		'qid' => array('3q1', '3q2','3q3','3q4')
);

$rm_survey_eval = array(	1 => 0,  2 => 2/4, 3 => 1, 4 => 3, 5 => 5.5, 6 => 7);


// Smoking
global $sm_survey;

$sm_survey = array(
	'gid' => 4,
	'qid1' => 12,
	'qid2' => 13
);

// *_val : value if spcified, -1 if not enough data
//		-1 IF NO DATA
// *_met 1 (met), 0 (not met), -1 (not enough data)
class survey_usermet {
	var $studyID;
	var $mv_val = 0;	
	var $mv_met = 0;	
	var $pa_val = 0;	
	var $pa_met = 0;	
	var $fv_val = 0;	
	var $fv_met = 0;	
	var $rm_val = 0;	
	var $rm_met = 0;	
	var $sm_val = 0;	
	var $sm_met = 0;	
	var $score = 0;	
}
// Display data - should be in showTFR file, but here so can view data

function displayMRF( $usermrf) {

	// print_r($usermrf);


	// MV
	?>
	<table>
	<tr>
	<td>
	studyID
	</td>
	<td><?php echo $usermrf->studyID ; ?>
	</td>
	</tr>
	
	<tr>
	<td>
	<b>Physical Activity Recommendations</b>
	</td>
	<td>
	<?php
	echo (  ($usermrf->pa_met == 1) ? 'met': ( ($usermrf->pa_met == 0) ? 'not met': 'not enough data'));

	?>	
	</td>
	</tr>
	
	<tr>
	<td>
	<b>Fruits and Vegetables Recommendations</b>
	</td>
	<td>
	<?php
	echo (  ($usermrf->fv_met == 1) ? 'met': ( ($usermrf->fv_met == 0) ? 'not met': 'not enough data'));

	?>	
	</td>
	</tr>
	
	<tr>
	<td>
	<b>Red Meat Recommendations</b>
	</td>
	<td>
	<?php
	echo (  ($usermrf->rm_met == 1) ? 'met': ( ($usermrf->rm_met == 0) ? 'not met': 'not enough data'));

	?>	
	</td>
	</tr>
	
	<tr>
	<td>
	<b>Multivitamins Recommendations</b>
	</td>
	<td>
	<?php
	echo (  ($usermrf->mv_met == 1) ? 'met': ( ($usermrf->mv_met == 0) ? 'not met': 'not enough data'));

	?>	
	</td>
	</tr>
	
	
	<tr>
	<td>
	<b>Smoking Recommendation</b>
	</td>
	<td>
	<?php
	echo (  ($usermrf->sm_met == 1) ? 'met': ( ($usermrf->sm_met == 0) ? 'not met': 'not enough data'));

	?>	
	</td>
	</tr>
	
	<tr>
	<td>
	<b>Score</b>
	</td>
	<td>
	<?php
	echo $usermrf->score . ' out of 5' ;

	?>	
	</td>
	</tr>
	
	</table>
	
	<?php

	
}

// Function to evaluate data for single studyID
//
// MV and SMoking are straightforward question/answer evaluations
// FV and RM consist of a set (array) of questions, and apply calculations on each response
// PA has its own calculations

function evalRow( $row) {
global $id_survey;	
global $sm_survey;	
global $mv_survey;	
global $fv_survey;	
global $rm_survey;
global $fv_survey_eval, $rm_survey_eval;	
global $pam_survey, $pav_survey;

			$user_mrf = new survey_usermet;
			// echo "<h2>participant</h2> id=". $row['id'] ;
			
		// print_r($row);
			// StudyID
			$qArray = $id_survey;
				
			$idColname = SURVEY_ID. "X". $qArray['gid']. "X". $qArray['qid'];
	
		
			// echo 'idColname = '. $idColname;
			$user_mrf->studyID = $row[$idColname] ;
		
			// MV
			//	Simple comparison to recommended levels 6 (code = 8) or 7 (code = 9) days
			$qArray = $mv_survey;
		
			$mvColname = SURVEY_ID. "X". $qArray['gid']. "X". $qArray['qid'];
			
			if (($row[$mvColname] == null) || ($row[$mvColname] == 99)) {
					$user_mrf->mv_val = -1;
					$user_mrf->mv_met = -1;
			
			}
			
			else {
				$user_mrf->mv_val = $row[$mvColname];
				if ($user_mrf->mv_val >= 3) $user_mrf->mv_val = $user_mrf->mv_val-2;
				else $user_mrf->mv_val = 0;
		
				if ($user_mrf->mv_val >= 6) {
					$user_mrf->mv_met = 1;
					// echo '<br><b>MV Recommendation met</b>';
				}
				else {
					$user_mrf->mv_met = 0;
					// echo '<br><b>MV Recommendation NOT met</b>';
				}
			}
				
			// FV
			// Array of questions
			// Handle missing /no data 
			$nodata_flag = false;
			$gid = $fv_survey['gid'];
			$qArray = $fv_survey['qid'];
			foreach ($qArray as $qid) {
		
				$fvColname = SURVEY_ID. "X". $gid. "X". $qid;
			
				if (($row[$fvColname] != null) && ($row[$fvColname] != 99)) {
					
					$user_mrf->fv_val += $fv_survey_eval[$row[$fvColname]];
				}
				else  {
					$nodata_flag = true;
				}		
			}	// End of fv question array
		
		
			// echo "<br><b>FV equivalent = $user_mrf->fv_val</b>";
			if ($user_mrf->fv_val >= 5) {
				$user_mrf->fv_met = 1;	
				// echo '<br><b>FV Recommendation met</b>';
			}
			else {
				if ($nodata_flag) {
					$user_mrf->fv_val = -1;	// no data
					$user_mrf->fv_met = -1;	// no data
				}
				else $user_mrf->fv_met = 0;	
				// echo '<br><b>FV Recommendation NOT met</b>';
			}
			// end FV		
	
	
			// RM
			// Array of questions
			$nodata_flag = false;
			$gid = $rm_survey['gid'];
			$qArray = $rm_survey['qid'];
			foreach ($qArray as $qid) {
				$rmColname = SURVEY_ID. "X". $gid. "X". $qid;		
				if (($row[$rmColname] != null) && ($row[$rmColname] != 99)) {
						
						$user_mrf->rm_val += $rm_survey_eval[$row[$rmColname]];
				}
				else  {
					$nodata_flag = true;
				}
			} // end rm questions array
		
			// echo "<br><b>RM equivalent = $user_mrf->rm_val</b>";
			// echo "<br>nodata = ". $nodata_flag;
			if ($user_mrf->rm_val <= 3) {
				if (!$nodata_flag) $user_mrf->rm_met = 1;
				else {
						$user_mrf->rm_val = -1;	// no data
						$user_mrf->rm_met = -1;	// no data
				}
				//echo '<br><b>RM Recommendation met</b>';
			}
			else {
						$user_mrf->rm_met = 0;
				//echo '<br><b>RM Recommendation NOT met</b>';
			}
			// end RM
				
			// Smoking
			$qArray = $sm_survey;
			// First question
			$smColname = SURVEY_ID. "X". $qArray['gid']. "X". $qArray['qid1'];
			if (($row[$smColname] == null) || ($row[$smColname] == 99)) {
				$user_mrf->sm_val = -1;	// no data
				$user_mrf->sm_met = -1;	// no data
				//echo '<br><b>Smoking Recommendation No data</b>';
			}
			else {
				if ($row[$smColname] == 1) {	// Non smoker
					$user_mrf->sm_met = 1;	
					$user_mrf->sm_val = 0;
					// echo '<br><b>Smoking Recommendation met Q1</b>';
				}
				else {
					// YES, check next question
					$smColname = SURVEY_ID. "X". $qArray['gid']. "X". $qArray['qid2'];
		
					if (($row[$smColname] == null) || ($row[$smColname] == 99)) {
							$user_mrf->sm_val = -1;	// no data
							$user_mrf->sm_met = -1;	// no data
							//echo '<br><b>Smoking Recommendation No data</b>';
					}
					
					else {
						// $user_mrf->sm_val = $row[$smColname];	
		
						if ($row[$smColname] == 1) {	// Non smoker
							$user_mrf->sm_met = 1;	
							$user_mrf->sm_val = 0;
							// echo '<br><b>Smoking Recommendation met Q2</b>';
						}
						else {
							$user_mrf->sm_met = 0;	
							$user_mrf->sm_val = 1;
							// echo '<br><b>Smoking Recommendation NOT met Q2</b>';
						}
					}
				}
			}
			
					
			// end smoking
			
			// PA
			// First retrieve all relevant columns
			// NO DATA? If # days > 0 but no hours or minutes entered?

			$nodata_flag = false;
			
			// MODERATE Activity
			$qArray = $pam_survey;
			$pamTotal = 0;
			$pamDaysColname = SURVEY_ID. "X". $qArray['gid']. "X". $qArray['days_qid'];
			// echo 'Mod PA days colname = '. $pamDaysColname. ' value = '. $row[$pamDaysColname];
			$pamDays = $row[$pamDaysColname];
			// Days field can be:
			//	 = 1 : no days
			//
			// >> could this be ''
			
			if (( $pamDays > 0 ) && ( $pamDays < 9) ) {
				// May be 0 days, in which case there should NOT be any time values entered
			 	$pamDays -= 1;
				// echo '# PAM days = '. $pamDays;
			
				$pamHoursSetColname = SURVEY_ID. "X". $qArray['gid']. "X". $qArray['hours_qid']['set'];
				$pamHoursValColname = SURVEY_ID. "X". $qArray['gid']. "X". $qArray['hours_qid']['value'];
				$pamMinSetColname = SURVEY_ID. "X". $qArray['gid']. "X". $qArray['min_qid']['set'];
				$pamMinValColname = SURVEY_ID. "X". $qArray['gid']. "X". $qArray['min_qid']['value'];
			
				if ($pamDays > 0 ) {
					// echo 'Mod PA hours set colname = '. $pamHoursSetColname. ' value = '. $row[$pamHoursSetColname];
					// May be NULL. Needs to be 'Y' in order to fetch #hours entered
					if ($row[$pamHoursSetColname] == 'Y') {
						// echo 'Mod PA hours val colname = '. $pamHoursValColname. ' value = '. $row[$pamHoursValColname];
						// These values may not be set -> NO data ?
						$pamHours = $row[$pamHoursValColname];
						$pamTotal = $pamHours * 60 * $pamDays;
					
					}
				
					// Do same for minutes
					// echo 'Mod PA min set colname = '. $pamMinSetColname. ' value = '. $row[$pamMinSetColname];
					// May be NULL. Needs to be 'Y' in order to fetch #hours entered
					if ($row[$pamMinSetColname] == 'Y') {
						// echo 'Mod PA min val colname = '. $pamMinValColname. ' value = '. $row[$pamMinValColname];
						$pamMinutes = $row[$pamMinValColname];
						$pamTotal+= $pamMinutes * $pamDays;
					}
					
					// CHeck for no data if neither hours or minutes are set
					if (($row[$pamHoursSetColname] != 'Y')  && ($row[$pamMinSetColname] != 'Y')) {
						$nodata_flag = true;
					}
				}
				else {
					// If 0, then no time should be specified ; else NO DATA
					if (($row[$pamHoursSetColname] == 'Y')  || ($row[$pamMinSetColname] == 'Y')) {
						$nodata_flag = true;
					}
					else $pamTotal = 0;
				}
	
			}
			else {
				// can be 99 or null
				// NO DATA
				$nodata_flag = true;
			}
			// echo '<br>Number of minutes of moderate activity = '. $pamTotal;

			// VIGROUS Activity
			$qArray = $pav_survey;
			$pavTotal = 0;
			$pavDaysColname = SURVEY_ID. "X". $qArray['gid']. "X". $qArray['days_qid'];
			$pavHoursSetColname = SURVEY_ID. "X". $qArray['gid']. "X". $qArray['hours_qid']['set'];
			$pavHoursValColname = SURVEY_ID. "X". $qArray['gid']. "X". $qArray['hours_qid']['value'];
			$pavMinSetColname = SURVEY_ID. "X". $qArray['gid']. "X". $qArray['min_qid']['set'];
			$pavMinValColname = SURVEY_ID. "X". $qArray['gid']. "X". $qArray['min_qid']['value'];
			// echo 'Vig PA days colname = '. $pavDaysColname. ' value = '. $row[$pavDaysColname];
			$pavDays = $row[$pavDaysColname];
			// >> could this be null/not specified? = ''  => NO DATA
			
			if (( $pavDays > 0 ) && ( $pavDays < 9) ) {
				 	$pavDays -= 1;
				// echo '# PAV days = '. $pavDays;
		
				if ($pavDays > 0 ) {
					// echo 'Vig PA hours set colname = '. $pavHoursSetColname. ' value = '. $row[$pavHoursSetColname];
					// May be NULL. Needs to be 'Y' in order to fetch #hours entered
					if ($row[$pavHoursSetColname] == 'Y') {
						// echo 'Vig PA hours val colname = '. $pavHoursValColname. ' value = '. $row[$pavHoursValColname];
						// These values may not be set -> NO data ?
						$pavHours = $row[$pavHoursValColname];
						$pavTotal = $pavHours * 60 * $pavDays;
					
					}
					
					// Do same for minutes
					// echo 'Vig PA min set colname = '. $pavMinSetColname. ' value = '. $row[$pavMinSetColname];
					// May be NULL. Needs to be 'Y' in order to fetch #hours entered
					if ($row[$pavMinSetColname] == 'Y') {
						// echo 'Vig PA min val colname = '. $pavMinValColname. ' value = '. $row[$pavMinValColname];
						$pavMinutes = $row[$pavMinValColname];
						$pavTotal+= $pavMinutes * $pavDays;
					}
					// CHeck for no data if neither hours or minutes are set
					if (($row[$pavHoursSetColname] != 'Y')  && ($row[$pavMinSetColname] != 'Y')) {
						$nodata_flag = true;
					}
				}
				else {
					// If 0, then no time should be specified ; else NO DATA
					if (($row[$pavHoursSetColname] == 'Y')  || ($row[$pavMinSetColname] == 'Y')) {
						$nodata_flag = true;
					}
					else $pavTotal = 0;
				}
			
			}
			else {
				// can be 99 or null
				// NO DATA
				$nodata_flag = true;
			}
			
			// echo '<br>Number of minutes of vigorous activity = '. $pavTotal;
			// echo "<br>nodata = ". $nodata_flag;
			
			// Handle no data case
			
			$user_mrf->pa_val = ($pamTotal ) + ( 2*$pavTotal);

			if ( $user_mrf->pa_val >= 150) {
				$user_mrf->pa_met = 1;
				//echo '<br><b>PA Recommendation  met</b>';
				
			}
			else {
				if ($nodata_flag) {
					//echo '<br><b>PA not enough data</b>';
					$user_mrf->pa_val = -1;
					$user_mrf->pa_met = -1;
					
				}
				else $user_mrf->pa_met = 0;
				//echo '<br><b>PA Recommendation  NOT met</b>';
			}

			
			// tabulate score (no data => 0 )
			$user_mrf->score = 0;
			if ( $user_mrf->pa_met == 1) $user_mrf->score++;
			if ( $user_mrf->mv_met == 1) $user_mrf->score++;
			if ( $user_mrf->sm_met == 1) $user_mrf->score++;
			if ( $user_mrf->rm_met == 1) $user_mrf->score++;
			if ( $user_mrf->fv_met == 1) $user_mrf->score++;

			// print_r($user_mrf);
			return ($user_mrf);
}


// retrieve additional TFR data
//	Select if TFR NOT already exported
//	Select participant has consented
//	>> Update TRF field after export for all retrieved participants
/*
o	Study ID
o	Patient first & last name
o	Address (lines 1 & 2)
o	City
o	State
o	Zip
o	Appt site ?
o	Appt date ?
o	Appt provider first & last name (or just ID?) - depends on BK's database
o	PCP first & last name (or just ID?)


appt provider - first name & last name
randomization (uc, Ix-mats, Ix-mats+cc) - NO uc's
Ix modality (print or web)
Reminder modality (AVR or SMS)
*/

class provider {
	var $fname;
	var $lname;
	var $dept;
	var $arm;
}

class participant {
	var $id;
	var $fname;
	var $lname;
	var $add1;
	var $add2;
	var $city;
	var $state;
	var $zip;
	var $provid;
	var $ixmod;
	var $pwd;
	var $email;
	var $remindrand;
	var $remindmod;
	var $mrf;
	var $date;
	var $mrn;
	var $dob;
}

function cmpid ( $a, $b) {
    if ($a->id == $b->id) {
        return 0;
    }
    return ($a->id < $b->id) ? -1 : 1;
}

	
function getProviderList( $surveyDB ) {

	
	
	// Retrieve provider data first
	
	$sql = "SELECT * from lung_cancer_user.provider pr";

	$result = mysql_query($sql, $surveyDB ) ;
	
	$providerList = array();
	
	if ($result) {
	
		while ($row = mysql_fetch_assoc($result)) {
			$p = new provider;
			// $p->id = $row['provID'];
			$p->fname = $row['provFName'];
			$p->lname = $row['provLName'];
			$p->dept = $row['dept'];
			$p->arm = $row['randArm'];
			
			$providerList[$row['provID']] = $p;
			// print_r($row);
			// print_r($p);
		}
		// print_r($providerList);
		return $providerList;
	}
	else {
		// error db
		printf( 'Database Error, unable to continue:'.  _HCC_TKG_DB_SELECT . ": %s\n",mysql_error( $surveyDB));
		return null;

	}
}

// Identify how many participants can be retrieved for export (but do not export)
function getSurveyPreview() {
global $id_survey;	

	$jConfig = new JConfig;	// Obtain current configuration parameters
	
    $surveyDB=mysql_connect($jConfig->host ,$jConfig->db, $jConfig->password, true);
    mysql_select_db(USERDB,$surveyDB);
	if (!$surveyDB ) {
		echo("Unable to connect to Survey Database:<br>".mysql_error());
		return null;
	}
 	if ( ($providerList = getProviderList( $surveyDB )) == null) return ;
 
  
    mysql_select_db(LIMEDB,$surveyDB);

	// StudyID
		
	$idColname = SURVEY_ID. "X". $id_survey['gid']. "X". $id_survey['qid'];

	$sql = "SELECT r.partID FROM ". USERDB . ".recruitment r left join ". USERDB . ".TFR_info t on t.partID = r.partID ".
		" WHERE r.blResult = 1  AND t.dateMailed IS NULL  "; 
		
	$result = mysql_query($sql, $surveyDB ) ;
	$userlist = array();
	if ($result) {
		while ($row = mysql_fetch_assoc($result)) {
					$userlist[] = $row['partID'];
		}
	}
	

	if (count($userlist) >  0 ) {
			$userlists = implode( ',', $userlist);

	/*
		$sql = "SELECT l.*, p.*,a.*, e.* FROM " .LIMEDB. ".lime_survey_" . SURVEY_ID . " l LEFT JOIN " . 
			USERDB. ".part_info p ON p.partID = l." . $idColname. " JOIN ". USERDB .".appt a ON a.MRN = p.MRN ".
			" JOIN " . USERDB. ".enrollment e on p.partID = e.partID ".
			"   WHERE l.submitDate is NOT NULL AND p.partID IN ("; */
		$sql = "SELECT l.*, p.*,a.*, e.* FROM " .LIMEDB. ".lime_survey_" . SURVEY_ID . " l LEFT JOIN " . 
			USERDB. ".part_info p ON p.partID = l." . $idColname. " JOIN ". USERDB .".appt a ON a.MRN = p.MRN ".
			" JOIN " . USERDB. ".enrollment e on p.partID = e.partID ".
			" JOIN " . LIMEDB. ".lime_tokens_" . SURVEY_ID . " t on  l.".SURVEY_ID. "x1x30 = t.token ".
			"   WHERE l.submitDate is NOT NULL AND t.completed = 'Y' AND p.partID IN ($userlists)"; 

		// echo $sql;
	}

	
	// no further query or processing needed
	else {
		echo 'No participants have taken the survey for TFR generation';
		mysql_close( $surveyDB );
		return;
	}
		
	unset ($userlist);
	
	$result = mysql_query($sql, $surveyDB ) ;
	
	$numrows = 0;
	
	if ($result) {

		if ( mysql_num_rows($result) > 0 ) {
	
			while ($row = mysql_fetch_assoc($result)) {
			
				// Remove uc providers
				if ($providerList[$row['provdID']]->arm != 'uc') $numrows++;
			
				// echo '<br>&nbsp;&nbsp;&nbsp;     '.$row['partID'];
			}
			
			echo '<br />There are <b>' . $numrows . '</b> participants for export<br />';

		}
		else {
			echo 'No participants have taken the survey for TFR generation';
			mysql_close( $surveyDB );
			return;
		}
		
	}
	else {
		// error db
		printf( 'Error, unable to continue:'.  _HCC_TKG_DB_SELECT . ": %s\n",mysql_error( $surveyDB));
		return;

	}
	mysql_close( $surveyDB );
}


function getSurveyData()  { 
global $id_survey, $date_survey;	
global $behaviorSpecs;

	$jConfig = new JConfig;	// Obtain current configuration parameters
	
    $surveyDB=mysql_connect($jConfig->host ,$jConfig->db, $jConfig->password, true);
    mysql_select_db(USERDB,$surveyDB);
	if (!$surveyDB ) {
		echo("Unable to connect to Survey Database:<br>".mysql_error());
		return null;
	}
	
	//	All data for studyIDs (including dups)
	if ( ($providerList = getProviderList( $surveyDB )) == null) return ;
	
    mysql_select_db(LIMEDB,$surveyDB);

	// StudyID
		
	$idColname = SURVEY_ID. "X". $id_survey['gid']. "X". $id_survey['qid'];
	

	// BIG JOIN:
	//	survey, part_info, recruitment, enrollment, appt, 
	
	// Do OWN SORTING to reduce load on SQL query
/*
	$sql = "SELECT l.*, p.*,r.*, a.*, e.* FROM " . USERDB. ".appt a, " .LIMEDB. ".lime_survey_" . SURVEY_ID . " l join ". USERDB .".part_info p on p.partID = l." . $idColname .
		" join ". USERDB . ".recruitment r on p.partID = r.partID join ". USERDB. ".enrollment e on r.partID = e.partID ".
		" left join ". USERDB . ".TFR_info t on t.partID = p.partID ".
		" WHERE r.blResult = 1  AND a.MRN = p.MRN  AND l.submitDate is NOT NULL "; // ORDER BY p.partID
	// echo $sql;
	
	*/
	// Alternative: two separate queries - first retrieves the list of participant IDs from the Admin db that quality
	//		(just two tables)
	//	and second query retrieves all data and makes sure that survey data has been submitted
	$sql = "SELECT r.partID FROM ". USERDB . ".recruitment r left join ". USERDB . ".TFR_info t on t.partID = r.partID ".
		" WHERE r.blResult = 1  AND t.dateMailed IS NULL  "; // ORDER BY p.partID
	// echo $sql;
		
	$result = mysql_query($sql, $surveyDB ) ;
	$userlist = array();
	if ($result) {
		while ($row = mysql_fetch_assoc($result)) {
		
					$userlist[] = $row['partID'];

		}
	}
	

	if (count($userlist) >  0 ) {
		$userlists = implode( ',', $userlist);
		$sql = "SELECT l.*, p.*,a.*, e.* FROM " .LIMEDB. ".lime_survey_" . SURVEY_ID . " l LEFT JOIN " . 
			USERDB. ".part_info p ON p.partID = l." . $idColname. " JOIN ". USERDB .".appt a ON a.MRN = p.MRN ".
			" JOIN " . USERDB. ".enrollment e on p.partID = e.partID ".
			" JOIN " . LIMEDB. ".lime_tokens_" . SURVEY_ID . " t on  l.".SURVEY_ID. "x1x30 = t.token ".
			"   WHERE l.submitDate is NOT NULL AND t.completed = 'Y' AND p.partID IN ($userlists)"; 

	}

	// no further query or processing needed
	else {
		echo 'No participants have taken the survey for TFR generation';
		mysql_close( $surveyDB );
		return;
	}
		
	// echo $sql;

	$result = mysql_query($sql, $surveyDB ) ;
	unset($userlist);
	
	$userlist = array();
	// survey date
	$dateColname = SURVEY_ID. "X". $date_survey['gid']. "X". $date_survey['qid'];
	
	if ($result) {

		if (mysql_num_rows($result) > 0 ) {
	
			while ($row = mysql_fetch_assoc($result)) {

				// Remove uc providers
				if ($providerList[$row['provdID']]->arm != 'uc') {
					// print_r($row);
					$usermrf = evalRow( $row);
					
					// If signaling dups
					// bool in_array  ( mixed $needle  , array $haystack)
					$userrec = new participant;
					$userrec->id = $row['partID'];
					$userrec->date = $row[$dateColname];
					$userrec->dob = $row['dob'];
					$userrec->fname = $row['ptFName'];
					$userrec->lname = $row['ptLName'];
					$userrec->add1 = $row['ptAddress1'];
					$userrec->add2 = $row['ptAddress2'];
					$userrec->city = $row['ptCity'];
					$userrec->state = $row['ptState'];
					$userrec->zip = $row['ptZip'];
					$userrec->provid = $row['provdID'];
					$userrec->ixmod = $row['ixModality'];
					$userrec->pwd = $row['webPwd'];
					$userrec->email = $row['ptEmail'];
					$userrec->remindrand = $row['remindRand'];
					$userrec->remindmod = $row['reminModality'];
					$userrec->mrf = $usermrf;
		
					$userlist[] = $userrec;
				}
						
			}	// end of row
		}
		else {
			echo 'No participants have taken the survey for TFR generation';
			mysql_close( $surveyDB );
			return;
		}
		
	}
	else {
		// error db
		printf( 'Error, unable to continue:'.  _HCC_TKG_DB_SELECT . ": %s\n",mysql_error( $surveyDB));
		return;

	}
	// print_r($userlist);

	usort( $userlist, "cmpid");

	// Column headers
		echo "Study ID";
		echo "\tSurvey date";
		echo "\tFirst Name\t Last Name\t Address 1\t Address 2\t City\t State\t Zip";
		echo "\tDoB";
		echo "\tAppt Provider ID \tAppt Site";
		// echo "\tAppt Provider ID\tAppt Provider First Name\tAppt Provider Last Name";
		// echo "\tPCP  ID\tPCP  First Name\tPCP  Last Name";

		echo "\tIx Modality\t Remind Modality \t PCP Rand (Calls)";
		echo "\tWeb pwd";
		echo "\tWeb email acct";
		echo "\tPA value\t PA met";
		echo "\tFV value\t FV met";
		echo "\tRM value\t RM met";
		echo "\tMV value\t MV met";
		echo "\tSM value\t SM met";
		echo "\tScore";
	
	foreach ($userlist as $userrec) {
				echo "\n". $userrec->id; 
				echo "\t". $userrec->date; 
				echo "\t".$userrec->fname. "\t". $userrec->lname. "\t". $userrec->add1. "\t" .$userrec->add2 . "\t" . $userrec->city. "\t". $userrec->state. "\t" .$userrec->zip; 
				echo "\t". $userrec->dob; 
				echo "\t".$userrec->provid . "\t". $providerList[$userrec->provid]->dept ; 
				echo "\t".$userrec->ixmod. "\t". $userrec->remindmod .  "\t". $providerList[$userrec->provid]->arm; 
				echo "\t". (($userrec->pwd == NULL) ? '': $userrec->pwd); 
				echo "\t". (($userrec->email == NULL) ? '': $userrec->email); 
				// echo "\t". $userrec->pwd; 
	
				echo "\t". number_format($userrec->mrf->pa_val, $behaviorSpecs[1]['weekavgdec']). "\t".  $userrec->mrf->pa_met ; 
	
				echo "\t".number_format($userrec->mrf->fv_val, $behaviorSpecs[2]['weekavgdec']). "\t".  $userrec->mrf->fv_met ; 
	
				echo "\t".number_format($userrec->mrf->rm_val, $behaviorSpecs[3]['weekavgdec']). "\t".  $userrec->mrf->rm_met ; 
				echo "\t".number_format($userrec->mrf->mv_val, $behaviorSpecs[4]['weekavgdec']). "\t".  $userrec->mrf->mv_met ; 
				echo "\t".number_format($userrec->mrf->sm_val, $behaviorSpecs[5]['weekavgdec']). "\t".  $userrec->mrf->sm_met ; 
				echo "\t".$userrec->mrf->score ; 
	}

	// print_r($userlist);
	// check for duplicates
	
	foreach ($userlist as $userrec) {
		$idlist[] = $userrec->id;
	}
	$u_userlist = array_unique( $idlist);
	// print_r($u_userlist);
	
	// Set TFR mailed field to estimated mailing date, with INSERT, since the row will not exist anyway
	// VALUES(1,2,3),(4,5,6),(7,8,9);
	$rightnow = date('Y-m-d', strtotime("+2 day"));

	if (count($u_userlist) >  0 ) {
		$sql = "INSERT into lung_cancer_user.TFR_info ( partID, dateMailed) values ";
		$sqlids = '';

		foreach ($u_userlist as $userid ) {
			if ($sqlids != '' ) $sqlids .= ',';
			$sqlids .= '('. $userid . ',\''. $rightnow . '\')'; 
		}
		$sql .= $sqlids;
		// echo $sql;
		$result = mysql_query($sql, $surveyDB ) ;
		if (! $result) {
			// Error updating TFR mailed field
				// error db
				error_log( "\n". date("Y-m-d H:i:s ") .  'Error, unable to insert TFR mailed information :' . ": %s\n",mysql_error( $surveyDB), 3, '/var/www/logs/hd2/hd2.log');
				printf( 'Error, unable to insert TFR mailed information :' . ": %s\n",mysql_error( $surveyDB));
		}
	}
	else {
		echo 'No participants have taken the survey for TFR generation';
	}
	unset($userlist);
		
	mysql_close( $surveyDB );
}


function getSurveyUserData( $studyID)  { 

global $id_survey;

	$jConfig = new JConfig;	// Obtain current configuration parameters
	// print_r($jConfig);
	
    $surveyDB=mysql_connect($jConfig->host ,$jConfig->db, $jConfig->password, true);
	if (!$surveyDB ) {
		echo("Unable to connect to Survey Database:<br>".mysql_error());
		return null;
	}

    mysql_select_db(LIMEDB,$surveyDB);
	if (!$surveyDB ) {
		echo("Unable to connect to Survey Database:<br>".mysql_error());
		return null;
	}
	
	// StudyID
		
	$idColname = SURVEY_ID. "X". $id_survey['gid']. "X". $id_survey['qid'];
	

	$sql = "SELECT * FROM lime_survey_" . SURVEY_ID .
			" l JOIN " . LIMEDB. ".lime_tokens_" . SURVEY_ID . " t on  l.".SURVEY_ID. "x1x30 = t.token ".
			" where $idColname =$studyID AND submitDate is NOT NULL AND t.completed = 'Y'";

	// echo $sql;
	$result = mysql_query($sql, $surveyDB ) ;
	
	if ($result) {
	
		if (mysql_num_rows($result) > 0 ) {
			while ($row = mysql_fetch_assoc($result) ) {
					echo '<br><br>';
					$usermrf = evalRow( $row);
					// print_r($usermrf);
					
					displayMRF( $usermrf);
			}
		} else {
			echo "<br>There is no survey data for this participant ($studyID), or the survey is incomplete";
		}
	}
	else {
		// error db
		printf( 'Error, unable to continue:'.  _HCC_TKG_DB_SELECT . ": %s\n",mysql_error( $surveyDB));
		return;

	}


	
		
	mysql_close( $surveyDB );


}

// Called by PTS to return:
//	status = true (success) or false(major error, eg. DB pb)
//	in $TFRvalues parameter (which should be declared as an array),  one or more TFR results.
//	each result is a single string
function getSurveyUserDataTFR2( $studyID, &$TFRvalues)  { 

global $id_survey;

	$jConfig = new JConfig;	// Obtain current configuration parameters
	// print_r($jConfig);
	
    $surveyDB=mysql_connect($jConfig->host ,$jConfig->db, $jConfig->password, true);
	if (!$surveyDB ) {
		echo("Unable to connect to Survey Database:<br>".mysql_error());
		return null;
	}

    mysql_select_db(LIMEDB,$surveyDB);
	if (!$surveyDB ) {
		echo("Unable to connect to Survey Database:<br>".mysql_error());
		return null;
	}
	
	// StudyID
		
	$idColname = SURVEY_ID. "X". $id_survey['gid']. "X". $id_survey['qid'];
	

	$sql = "SELECT * FROM lime_survey_" . SURVEY_ID .
			" l JOIN " . LIMEDB. ".lime_tokens_" . SURVEY_ID . " t on  l.".SURVEY_ID. "x1x30 = t.token ".
			" where $idColname =$studyID AND submitDate is NOT NULL AND t.completed = 'Y'";

	// echo $sql;
	$result = mysql_query($sql, $surveyDB ) ;
	
	if ($result) {
	
		if (mysql_num_rows($result) > 0 ) {
			while ($row = mysql_fetch_assoc($result) ) {
					$usermrf = evalRow( $row);
					// print_r($usermrf);
					$TFRvalues[] = "$usermrf->pa_val\t$usermrf->fv_val\t$usermrf->rm_val\t$usermrf->mv_val\t$usermrf->sm_val\t$usermrf->score\t\n";
					// displayMRF( $usermrf);
			}
		} else {
			$TFRvalues[0] = "\t\t\t\t\t\t\n";
			// echo "<br>There is no survey data for this participant ($studyID), or the survey is incomplete";
		}
	}
	else {
		// error db
		printf( 'Error, unable to continue:'.  _HCC_TKG_DB_SELECT . ": %s\n",mysql_error( $surveyDB));
		return false;

	}


	
		
	mysql_close( $surveyDB );
	return true;

}

?>