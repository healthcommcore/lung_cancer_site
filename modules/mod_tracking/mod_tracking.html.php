<?php
/**
* @version 1.0 $
* @package HD2 Tracking
* @copyright (C) 2008 HCC
*/

/** ensure this file is being included by a parent file */
defined( '_JEXEC' ) or die( 'Restricted Access.' );

// HTML generation functions

class HTML_tracking {
// Module definition
	// Content area header
	function displayTrackingHeader($header, $linktxt, $linkurl) {
		global $behaviorSpecs;
		?>
		<table style="width: 100%; border-collapse: collapse" summary="" border="0" cellpadding="3" cellspacing="0">
		<tbody>
		<tr valign="top">
		<td width="70%">
		<h2><?php echo $header?></h2></td>
		<td align="right" width="30%">
		<p><a href="<?php echo $linkurl?>" title="" target="_blank"><font face="Arial" size="2"><?php echo $linktxt?></font></a><br />
		</p></td></tr></tbody></table>
		<?php
	}
	
	function displayErrorMsg($errmsg) {
		echo '<span class="alert">'. $errmsg . '</span>';
	}


	function showHelpLink($behaviorID) {
		global $view, $id, $itemid, $option;
		global $behaviorSpecs;
		if ($behaviorSpecs[$behaviorID]['helplinktxt'] != null ) {
			echo '<p><a href="'. "index.php?option=$option&view=$view&id=" .
			$behaviorSpecs[$behaviorID]['helpid'] . "&Itemid=" .
			$behaviorSpecs[$behaviorID]['helpitem'] . '" target="_blank" class="helplink">'. $behaviorSpecs[$behaviorID]['helplinktxt']  . '</a></p>';
		}
		if ( isset($behaviorSpecs[$behaviorID]['helplinktxt2']) ) {
			echo '<p><a href="'. "index.php?option=$option&view=$view&id=" .
			$behaviorSpecs[$behaviorID]['helpid2'] . "&Itemid=" .
			$behaviorSpecs[$behaviorID]['helpitem2'] . '" target="_blank" class="helplink">'. $behaviorSpecs[$behaviorID]['helplinktxt2']  . '</a></p>';
		}

	}
	function displayForm($behaviorID, $showHelp, $isset) {
		global $view, $id, $itemid, $option;
		global $behaviorSpecs, $currentData, $userDB, $rightNow, $user;
		global $trackedBehaviors;
		
		// Javascript check function depends on input type
		/*
		if ( $behaviorSpecs[$behaviorID]['entrytype'] == _HCC_TRACKING_IN_NUMERIC) {
			$jscript = 'processNumeric(this)';
		} else {
			if ( sizeof ($behaviorSpecs[$behaviorID]['options']) < 4) {
				$jscript = 'processRadio(this)';
			} else {
				$jscript = 'processList(this)';
			}
		}
		*/
		// if ($isset) echo '<p>Data already entered for today</p>';
		
		// Form entry area
		?>
		<div align="right">
		<?php
		echo '<label for="myform">' .$behaviorSpecs[$behaviorID]['formquest'] . '</label>';
		if ($isset) echo '<input type="hidden" name="dataset'.$behaviorID.'" id="dataset'.$behaviorID.'" value="1">';
		else echo '<input type="hidden" name="dataset'.$behaviorID.'" id="dataset'.$behaviorID.'" value="0">';
	
		// Entry form varies depending on behavior type
		if ( $behaviorSpecs[$behaviorID]['entrytype'] == _HCC_TRACKING_IN_NUMERIC) {
			echo '<input type="text" name="response'. $behaviorID .'" id="response'. $behaviorID .'" value="'. $behaviorSpecs[$behaviorID]['step2item'].'" onblur="if(this.value==\'\') this.value=\''.$behaviorSpecs[$behaviorID]['step2item'].'\';" 
			onfocus="if(this.value==\''.$behaviorSpecs[$behaviorID]['step2item'].'\') this.value=\'\';" size="10" maxlength="'. strlen($behaviorSpecs[$behaviorID]['step2item']) .
			'">';
		}
		else {
			$listopt = $behaviorSpecs[$behaviorID]['options'];
			if ( sizeof ($listopt) < 4) {
				// Kludge to reverse MV Y/N
				$rlistopt = array_reverse( $listopt );
				foreach ( $rlistopt as $key => $val) {
					echo '<input type="radio" name="response'. $behaviorID .'" id="response'. $behaviorID .'" value="'. $val. '">' . $key;
				}
			}
			else {
				?>
				<select name="response<?php echo $behaviorID ?>">
				<option value="" SELECTED>-Select-</option>
				<?php foreach($listopt as $key => $val) { ?>
				<option value='<?php echo $val?>'><?php echo $key?></option>
				<?php 
				} 
			} ?>
		</select>
		<?php
		}
			if ( $showHelp) HTML_tracking::showHelpLink($behaviorID);
	
		?>
		</div>
		<?php
	}

// CURRENTLY NOT USED
// Past days displayed only if there are past days- that is, if tracking day > 1
// Display link to all possible days except for current tracking date
//  - compare with start date for each back day to see whether to display link to back days or not
// Link to default tracking page, with op= and day=# parameters		
function displayBackLinks() {
global $option;
global $view;
global $itemid, $id;
		
		// Before displaying Div, Are there ANY back dates available for this behavior?
		if (! isBackDate (1) )  return;
		$itemid=55;
		$id=45;
		
		echo '<div><ul>';
		
		if ( isBackDate (1) ) {
			echo '<li><a href="'. "index.php?option=com_content&view=article&id=$id&Itemid=$itemid&behaviorID=$behaviorID&day=1" .			
				'">'. _HCC_TRACKING_YESTERDAY . '</a></li>';
		} 
		if ( isBackDate (2) ) {
			echo '<li><a href="'. "index.php?option=com_content&view=article&id=$id&Itemid=$itemid&behaviorID=$behaviorID&day=2" .			
				'">'. _HCC_TRACKING_2DAYS . '</a></li>'; 
		}
		if ( isBackDate (3) ) {
			echo '<li><a href="'. "index.php?option=com_content&view=article&id=$id&Itemid=$itemid&behaviorID=$behaviorID&day=3" .			
				'">'. _HCC_TRACKING_3DAYS . '</a></li>';
		} 
		echo '</ul></div>';
}


// Functions to handle different tracking switch cases
//
function displayStep1Entry($day, $statusMsg) {
	global $view, $id, $itemid, $option;
	global $behaviorSpecs, $currentData, $userDB, $rightNow, $user;
	global $trackedBehaviors;

	if ($statusMsg != '') {
			echo '<span class="alert">'. $statusMsg . '</span>';
	}
		
	// loop through tracked behaviors
    HTML_tracking::displayTrackingHeader('Track my Health Habits for ' . getDayofWeek($day), 'Get help with tracking',
			'index.php?option=com_content&amp;view=article&id=22&Itemid=92');

    echo '<form  class="track-textinput" action="'. JRoute::_("index.php?option=$option&view=$view&id=$id&Itemid=$itemid&op=step2&day=$day")  . '" method="post" name="enterSteps" onSubmit="return processTrackingEntry(this)">';

		foreach ($trackedBehaviors as $behaviorID) {
?>		
		
			<div class="important-grey"><span class="important-title">Track <?php echo $behaviorSpecs[$behaviorID]['sname'] ;?></span>
			<table style="width: 100%; border-collapse: collapse" border="0" cellpadding="3" cellspacing="0">
			<tbody>
			<tr valign="top">
			<td valign="top" width="110">
			<p><a href="/index.php?option=com_content&view=article&id=<?php echo $behaviorSpecs[$behaviorID]['contentid'] ;?>&Itemid=<?php echo $behaviorSpecs[$behaviorID]['contentitem'] ;?>"><img style="margin-bottom: 0pt; margin-right: 15px; border:none;" alt="" title="" src="images/stories/behavior_icons/<?php echo $behaviorSpecs[$behaviorID]['image'] ; echo (isset($currentData[$behaviorID]->weekArray[7-$day]) ? '_de.gif': '_sm.gif'); ?>" align="left" /></a></p></td>
			<td>
<?php				if ($behaviorID == 1) { ?>
				<div class="indent2"><h3>How many steps did you walk?</h3></div>
<?php		
			}
			// Pass flag on whether there is already data for the day being tracked - day 0 => array index 7, day 1 => array index 6...
		
			// Use day of week time for  which data is being entered
		 	HTML_tracking::displayForm($behaviorID, ($behaviorID == 1) ? false: true, isset($currentData[$behaviorID]->weekArray[7-$day]));
			if ($behaviorID == 1) {
				// try here for now
				// special PA cases (behaviorID=1)
				?>
				<div class="indent2"><h3>Did you do any physical activity without your pedometer?</h3></div>
				<div align="right">

				<label for="myform">How many <?php echo _HCC_MINUTES?> of moderate activity <b>without</b> your pedometer?</label><input name="response1A" value="<?php echo _HCC_MINUTES?>"  onblur="if(this.value=='') this.value='<?php echo _HCC_MINUTES?>';" 
			onfocus="if(this.value=='<?php echo _HCC_MINUTES?>') this.value='';" size="10" maxlength="<?php echo strlen( _HCC_MINUTES) ?>" type="text">    
			</div>
				<div align="right">
				<label for="myform">How many <?php echo _HCC_MINUTES?> of vigorous activity <b>without</b> your pedometer?</label><input name="response1B" value="<?php echo _HCC_MINUTES?>"  onblur="if(this.value=='') this.value='<?php echo _HCC_MINUTES?>';" 
			onfocus="if(this.value=='<?php echo _HCC_MINUTES?>') this.value='';" size="10" maxlength="<?php echo strlen( _HCC_MINUTES) ?>" type="text">    
			</div>
				<?php HTML_tracking::showHelpLink($behaviorID); ?>
			
				<?php
			}
			?>	</td></tr></tbody></table></div><?php
		}
		?>
		<div id="formbutton" style="margin-left:10px">
		<input class="hd_button" type="submit" value="Submit">
		</div>
	</form>
<?php
}

function displayMRFscore( $MRFscore, $showtrack) {
	?>
<div class="myscore"><span class="myscore-title">My Score</span>


	<?php
	if ($MRFscore >= 0 ) { ?>
	
<table style="width: 100%; border-collapse: collapse;" border="0" cellpadding="3" cellspacing="0">
<tbody>
<tr valign="top">
<td width="160">
<p>
		<!-- display stars -->
		<?php
		$stars = floor($MRFscore);
		for ($i =0; $i < $stars; $i++)
			echo '<img src="images/hd2/tracking/fullstar.gif" height="34" width="32" style="margin: 5px 0px;">';
		if  (( (($MRFscore - $stars) *10 ) / 2.5) >= 3 ) {
				// echo "half star";
			echo '<img src="images/hd2/tracking/halfstar.gif" height="34" width="32" style="margin: 5px 0px;">';
			$stars++;
		}
		else if  (( (($MRFscore - $stars) *10 ) / 2.5) >= 1 ) {
			echo '<img src="images/hd2/tracking/halfstar.gif" height="34" width="32" style="margin: 5px 0px;">';
			$stars++;
		}
		
		
		// Remaining blank stars
		for ($i =0; $i < (5-$stars); $i++)
			echo '<img src="images/hd2/tracking/blankstar.gif" height="34" width="32" style="margin: 5px 0px;">';
		
		?>
</p>
		<p><strong>My Score</strong> is <span class="highlight-bold"><?php echo $MRFscore;  ?></span> out of 5 possible points.</p>
<p><a title="" href="index.php?option=com_content&amp;view=article&amp;id=19&amp;Itemid=37" class="helplink">What does this mean?</a></p></td>
<td>
		<p><?php echo _HCC_TKG_MYSCORE ?></p>
<?php if ($showtrack) { ?>
<p><a class="hd_button" title="Track Now" href="index.php?option=com_content&amp;view=article&amp;id=42&amp;Itemid=51">Track now</a> </p>
<?php } ?>
</td></tr></tbody></table>
	
		<?php } else { ?>
		<p><?php echo _HCC_TKG_MYSCORE ?><br>
	
		<?php echo _HCC_TKG_NOMRF ?>
<?php if ($showtrack) { ?>
<p><a class="hd_button" title="Track Now" href="index.php?option=com_content&amp;view=article&amp;id=42&amp;Itemid=51">Track now</a> </p>
<?php } ?>
		
		<p><a href="index.php?option=com_content&amp;view=article&amp;id=19&amp;Itemid=37" title="" class="helplink">
		What does this mean?</a></p>
		<?php } 
?>
</div>
<?php
}

function displayStep2Process($behaviorID, $day, $statusMsg, $responseArray, $MRFscore) {
global $userDB, $user, $rightNow, $behaviorSpecs;
global $currentData, $response;
global $option, $view, $itemid, $id;
global $trackedBehaviors;


		// revert to data entry if error message
		if (($statusMsg != '') ) {
			echo '<span class="alert">'. $statusMsg . '</span>';
			// If unable to process data because it was not submitted, redisplay
			// entry form
			HTML_tracking::displayForm($behaviorID, $day);
		}
		// Display current set of weekly data
		else {
		        HTML_tracking::displayTrackingHeader('How You Did ' . getDayofWeek($day), 'Get help with these charts', 'index.php?option=com_content&view=article&id=77&Itemid=94');
			
			// Loop through behaviors
			foreach ($trackedBehaviors as $behaviorID) {
				
	?>		
			
				<div class="important-grey"><span class="important-title">Track <?php echo $behaviorSpecs[$behaviorID]['sname'] ;?></span>
				<table style="width: 100%; border-collapse: collapse" border="0" cellpadding="3" cellspacing="0">
				<tbody>
				<tr valign="top">
				<td valign="top" width="110">
				<p><a href="/index.php?option=com_content&view=article&id=<?php echo $behaviorSpecs[$behaviorID]['contentid'] ;?>&Itemid=<?php echo $behaviorSpecs[$behaviorID]['contentitem'] ;?>"><img style="margin-bottom: 0pt; margin-right: 15px; border:none" alt="" title="" src="images/stories/behavior_icons/<?php echo $behaviorSpecs[$behaviorID]['image'] ;?>_sm.gif" align="left" /></a></p></td>
				<td>
		<?php		
				drawChart($behaviorID, $currentData[$behaviorID]->weekArray, true, 	true, $user, $day);
				?>

				<!-- CSS to be done -->
				</td><td width = "200" valign="top" align="left">
				<?php
				if (isset($responseArray[ $behaviorID]) ) {
					echo '<h4>You entered</h4>';
				
					$response = $responseArray[ $behaviorID];
					
					// Daily feedback msg
					if ($behaviorSpecs[$behaviorID]['options'] != null) {
						// options list
							$tmparray = array_flip($behaviorSpecs[$behaviorID]['options']);
							echo '<p><b>'. $tmparray[$response] . '</b> '. $behaviorSpecs[$behaviorID]['step2item'].'</p>';
					} else {
						echo '<p><b>'. number_format($response)  . '</b> '. $behaviorSpecs[$behaviorID]['step2item'].'</p>';
					} 
					$msg = evaluateFeedback($behaviorID, $response, false);
					echo '<p>'. $msg .'</p>';
				}
				else 
					echo '<p>', _HCC_TKG_INPUT_NO_BEHDATA, '</p>';
				
				?>
				</td></tr></tbody></table></div>
			<?php
			}
		?>

		<?php HTMl_tracking::displayMRFscore($MRFscore, false); ?>
		
		<?php
		}
}


//	Display Weekly chart (per behavior)
function displayWeekly($MRFscore) {
global $option, $view, $itemid, $id;
global $userDB, $user, $rightNow, $behaviorSpecs;
global $currentData;
global $trackedBehaviors;
		
		HTML_tracking::displayTrackingHeader('My Weekly Tracking', 'Get help with these charts', 'index.php?option=com_content&view=article&id=77&Itemid=94');
		HTMl_tracking::displayMRFscore($MRFscore, false); 

		foreach ($trackedBehaviors as $behaviorID) {
?>
			
				<div class="important-grey"><span class="important-title">This Week's <?php echo $behaviorSpecs[$behaviorID]['sname'] ;?></span>
				<table style="width: 100%; border-collapse: collapse" border="0" cellpadding="3" cellspacing="0">
				<tbody>
				<tr valign="top">
				<td valign="top" width="110">
				<p><a href="/index.php?option=com_content&view=article&id=<?php echo $behaviorSpecs[$behaviorID]['contentid'] ;?>&Itemid=<?php echo $behaviorSpecs[$behaviorID]['contentitem'] ;?>"><img style="margin-bottom: 0pt; margin-right: 15px;border:none" alt="" title="" src="images/stories/behavior_icons/<?php echo $behaviorSpecs[$behaviorID]['image'] ;?>_sm.gif" align="left" /></a></p></td>
				<td>

				<?php 
				drawChart($behaviorID, $currentData[$behaviorID]->weekArray, true, 	true, $user, 0);
				?>
 				</td></tr></tbody></table></div>
		<?php 
		}
}

//	Display History
function displayHistory($weekno, $MRFscores) {
global $option, $view, $itemid, $id;
global $userDB, $user, $rightNow, $behaviorSpecs;
global $currentData;
global $trackedBehaviors;
		HTML_tracking::displayTrackingHeader('My Tracking History', 'Get help with these charts', 'index.php?option=com_content&view=article&id=77&Itemid=94');
		foreach ($trackedBehaviors as $behaviorID) {
?>

				<div class="important-grey"><span class="important-title">My <?php echo $behaviorSpecs[$behaviorID]['sname'] ;?> Changes</span>
				<table style="width: 100%; border-collapse: collapse" border="0" cellpadding="3" cellspacing="0">
				<tbody>
				<tr valign="top">
				<td valign="top" width="110">
				<p><a href="/index.php?option=com_content&view=article&id=<?php echo $behaviorSpecs[$behaviorID]['contentid'] ;?>&Itemid=<?php echo $behaviorSpecs[$behaviorID]['contentitem'] ;?>"><img style="margin-bottom: 0pt; margin-right: 15px; border:none" alt="" title="" src="images/stories/behavior_icons/<?php echo $behaviorSpecs[$behaviorID]['image'] ;?>_sm.gif" align="left" /></a></p></td>
				<td>
	
			<?php 
			/*
	echo '<p>' . ceil( ($_SERVER['REQUEST_TIME'] - $user->startDate) /_HCC_SECONDS_PER_DAY/7) . '<p>';	
	echo '<p>' . $user->weeksSinceStart . '<p>';	
			 */
			drawChart($behaviorID, $currentData[$behaviorID]->allArray, false, $weekno, $user, $rightNow);
			?>
 				</td></tr></tbody></table></div>
			<?php
		}
		?>
        </td></tr>
		<tr>
		<td>
				<div class="important-grey"><span class="important-title"><?php echo 'My Score Changes' ;?></span>
				<table style="width: 100%; border-collapse: collapse" border="0" cellpadding="3" cellspacing="0">
				<tbody>
				<tr valign="top">
				<td valign="top" width="110">
				<p><a href="/index.php?option=com_content&view=article&id=<?php echo $behaviorSpecs[6]['contentid'] ;?>&Itemid=<?php echo $behaviorSpecs[6]['contentitem'] ;?>"><img style="margin-bottom: 0pt; margin-right: 15px; border:none" alt="" title="" src="images/stories/behavior_icons/<?php echo $behaviorSpecs[6]['image'] ;?>_sm.gif" align="left" /></a></p></td>
				<td>
		<?php 
			// Draw MRF history chart
			drawChart(6, $MRFscores, false, $weekno, $user, $rightNow);
			
		?>
 		</td></tr></tbody></table></div>
		
		</td>
		</tr>
		</table>
		<?php
}

function displayMessage( $remindTitle, $remindBody) {
global $user;
?>

        	<p><font size="5"><b><?php echo 'Welcome, '. $user->fname; ?></b></font></p>
		<?php 
/*			if (!$remindTitle) {
		
	
		$remindTitle = _HCC_FIRSTLOGIN_1;
		$remindBody = _HCC_FIRSTLOGIN_2;
		}
	*/
		if ($remindTitle) {
		?> 
		<div class="important-red"><span class="important-title-red">
		<?php
			
		echo $remindTitle;
			echo '</span>';
			echo $remindBody;
		?>
		</div>
		<?php
		} 

}

// Display tracking summary.
//	This appears on  the homepage 
function displaySummary( $MRFscore) {
global $option, $view, $itemid, $id;
global $my;
global $userDB, $user, $rightNow, $behaviorSpecs;
global $currentData, $trackedBehaviors;
global $imageDir;
		HTMl_tracking::displayMRFscore($MRFscore, true); 
		
        //  Loop through tracked behaviors 
		// MAIM LOOP THROUGH TRACKED BEHAVIORS
		//	Note that behavior stored by their key as defined in $behaviorSpecs,
		//	so the order is preset.
		//
		// - If enough data (4/7 days) for past 7 days and 7 days before that: Give weekly avg. f/b AND trend f/b
		//  - If enough data for past 7 days but NOT for 7 days before that: Give weekly avg. f/b ONLY
		// - If not enough data for past 7 days: Give "not enough data" message (needs to be written) 
		foreach ($trackedBehaviors as $behaviorID ) {
	?>
	
			 <div class="important-grey"><span class="important-title"><?php echo ucwords($behaviorSpecs[$behaviorID]['sname']); ?></span>
			<table style="width: 100%; border-collapse: collapse;" border="0" cellpadding="3" cellspacing="0">
			<tbody>
			<tr valign="top">
			<td valign="top" width="110">
			<p><a href="/index.php?option=com_content&view=article&id=<?php echo $behaviorSpecs[$behaviorID]['contentid'] ;?>&Itemid=<?php echo $behaviorSpecs[$behaviorID]['contentitem'] ;?>"><img style="margin-bottom: 15px; margin-right: 15px;border:none" alt="" title="" src="images/stories/behavior_icons/<?php echo $behaviorSpecs[$behaviorID]['image'] ;?>_lrg.gif" width="100"></a></p></td>
			<td>
			<p><?php echo $behaviorSpecs[$behaviorID]['homeinfo'] ?></p>
			
	
			
			</p>
			<?php 
			// !! Display feedback information only if there is sufficient data - ie. 
			//	WeekAvg != -1 (not enough data)
			// If there is weekly average data
			//	Look up weekly feedback tables for corresponding feedback message
				echo '<h3>Your Progress</h3>';
			echo '<ul>';
			if ($currentData[$behaviorID]->weekAvg >= 0 ) {
				$numStr = ' <span class="avg">'. number_format($currentData[$behaviorID]->weekAvg, $behaviorSpecs[$behaviorID]['weekavgdec']) . '</span>';
				$msg = evaluateFeedback( $behaviorID, $currentData[$behaviorID]->weekAvg, true);	
				printf ( $msg, $numStr) ;
			}
			echo '</ul>';
			echo '<ul>';
			// If there is sufficient data to determine trend levels
			//	Display feedback
			if ( ($currentData[$behaviorID]->weekAvg >= 0  ) && ($currentData[$behaviorID]->prevWeekAvg >= 0  ) ) {
				// echo "<br>trend level=";
				// print_r($currentData[$behaviorID]->trendLevel);
				$numStr = ' <span class="avg">'. number_format($currentData[$behaviorID]->prevWeekAvg, $behaviorSpecs[$behaviorID]['weekavgdec']) . '</span>';
				$msg = $behaviorSpecs[$behaviorID]['tfeedback'][$currentData[$behaviorID]->trendLevel];
				printf ( $msg, $numStr) ;

			}
		
			if ( ($currentData[$behaviorID]->weekAvg < 0  ) || ($currentData[$behaviorID]->prevWeekAvg < 0  ))	{
				// Only display insufficient tracking message for users who have 
				//	just begun tracking this behavior (use goalstart date, not just
				//	overall start date)
				//
				if (($rightNow - $user->startDate)	 > (14 * _HCC_SECONDS_PER_DAY) ) {
					if ($currentData[$behaviorID]->weekAvg < 0  ) {
						echo '<li>', _HCC_TKG_NOWEEKAVG, '</li>';
				
					}
					else {	// if have weekly data, but no previous week avg
						echo '<li>', _HCC_TKG_NOTRENDAVG, '</li>';
					}
				
				} else 
						echo '<li>', _HCC_TKG_NOWEEKAVG, '</li>';
			}
			echo '</ul>';

            ?> 
			<p><a class="hd_button" href="index.php?option=com_content&amp;view=article&amp;id=42&amp;Itemid=51" title="Track Now">Track now</a> </p>
			
			</td></tr></tbody></table></div>
        
        <?php 
		}	// end loop behavior
}



}


?>


