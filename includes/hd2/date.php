<?php
// DATE FUNCTIONS
// Day/Date  handling functions


function getDayofWeek($day) {
global $rightNow;
global $mosConfig_lang;
	
		if ($day != 0) {
			$backDay = strtotime(date("Y-m-d H:i:s", $rightNow) . ' '. $day ." day ago");
				return ( strftime('%A', $backDay));
		}
		else return 'Today';
}

// Determine whether requested back day (!= 0) =  1, 2, 3, precedes user's startDate 

function isBackDate ($newday) {
global  $rightNow, $user;

	if ($newday == 0) return true;
	
	$backDay = strtotime(date("Y-m-d H:i:s", $rightNow) . ' '. $newday ." day ago");
			
	if ($user->startDate < $backDay) return true;

	return false;
}

?>