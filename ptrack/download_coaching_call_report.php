<?php 

// LOAD mySQL FUNCTIONS
include("includes/connection.php");
include("includes/he_function.php");
include("includes/report_function.php");
$errMsg="";

// Initialize session and $sid from GET param 
//require '../../includes/initsession.php'; 
//require '../checklogin.php'; 

$today = date("m/d/Y");

header("Content-disposition: attachment;filename=coaching_call_report.xls"); 
header("Content-type: application/vnd.ms-excel"); 
header("Pragma: no-cache"); 
header("Expires: 0"); 
// print out the headers
echo "\t\t\t\tHd2 Coaching Call Report \n \t\t\t\tReport produced $today\n\n";
echo " \t ARM 3: Ix-material + cc\t Not yet due - call 1\t Due - call 1\t Overdue - call 1\t Complete - call 1\t Unable to reach - call 1\t Refused - call 1\t Refused all calls\t \t Advanced to call 2\t Not yet due - call 2\t Due - call 2\t Overdue - call 2 \t Completed - call 2\t Unable to reach - call 2 \t Refused - call 2\t FINISHED - Both call completed\t FINISHED - Only call 1 completed\t FINISHED - Only call 2 completed\t FINISHED - Neith call completed"; 

// CONNECT TO DATABASE
if ($mysqlID=dbConnect()) {
    selectDatabase("lung_cancer_user");
} else {
    $errMsg.="<b>ERROR:</b> ".mysql_error()."<br>\n";
}
// declare some variables 
$dueDate = 14;
$overDueDate = 24;

// get HE list from the enrollment able
 $heArray= getHeList();
// initalized some variables
$totalCC = 0;
$totalNotDueC1 =0;
$totalDueC1 = 0;
$totalOvDueC1 = 0;
$totalComC1 = 0;
$totalUnC1 = 0;
$totalRefC1 = 0;
$totalRefAll = 0;
$totalCC2 = 0;
$totalNotDueC2 = 0;
$totalDueC2 = 0;
$totalOvDueC2 = 0;
$totalComC2 = 0;
$totalUnC2 = 0;
$totalRefC2 = 0;
$totalCompCC = 0;
$totalNotCompCC = 0;
$totalCall1Comp = 0;
$totalCall2Comp = 0;
// get not yet due, due, overdure and completed, refused, unable to reach
 reset($heArray);
while(list($heID, $heName)=each($heArray)){
	// echo "<br> The Staff name is ".$heName."<br>";
	//------------------------Call 1 ----------------------------------------//
	$totalC1 = 0;
	$ptDueArray = array();
	$ptOverDueArray = array();
	$ptNotDueArray = array();
	$compCall1 = 0;
	$unCall1 = 0;
	$refCall1 = 0;
	$refAll = 0;
	
	//call function to get total of pts that not yet due for coaching calls
	//) echo "<b>The not yet due array is:</b><br>";
	$ptNotDueArray = getDueCal11($heID, 0, $dueDate);
	// get the total of pts that due for coaching call
	// echo "<b>The due array is:</b><br>";
	$ptDueArray = getDueCal11($heID, $dueDate, $overDueDate);
	// get the total of pts that overdue for coaching call
	// echo "<br><b>The over due array for ".$heID." is</b>:<br>";
	$ptOvDueArray = getDueCal11($heID, $overDueDate, 0);
	// get the total of complete, unreachable and refusals
	$compCall1 = $ptNotDueArray[1] + $ptDueArray[1] + $ptOvDueArray[1];
	//echo "complete call is ".$compCall1."<br>";
	$unCall1 = $ptNotDueArray[2] + $ptDueArray[2] + $ptOvDueArray[2];
	$refCall1 = $ptNotDueArray[3] + $ptDueArray[3] + $ptOvDueArray[3];
	$refAll = $ptNotDueArray[4] + $ptDueArray[4] + $ptOvDueArray[4];
	// get total of CC for call 1
	//$totalC1 = $ptNotDueArray[0] + $ptDueArray[0] + $ptOvDueArray[0] + $compCall1 + $unCall1 + $refCall1 + $refAll;
	$totalC1 = getAllCC($heID);
	//echo "<br>".$totalC1." ".$ptNotDueArray[0]." ".$ptDueArray[0]." ".$ptOvDueArray[0]." ".$compCall1." ".$unCall1." ".$refCall1." ".$refAll."<br>";
	
	// ---------------------------Call 2 ------------------------------------//
	$ptRstArray = array();
	$catArray = array();
	$totalC2 = 0;
	$resultID = 0;
	$compCall2 = 0;
	$unCall2 = 0;
	$refCall2= 0;
	$notDue = 0;
	$due = 0;
	$overDue = 0;
	$call2Unfinish = 0;
	// get the pt list that has contact result (complete, unreachable, refused) for call 1 
	$ptRstArray = getPtCall1Result($heID, 1);
	reset($ptRstArray);
	while(list($keyID, $val)=each($ptRstArray)){
		$resultID = getLastCall2($keyID, 2);
		switch ($resultID){
			case 10;
			$compCall2++;
			break;
			
			case 11;
			$unCall2++;
			break;
			
			case 12;
			$refCall2++;
			break;
			
			default;
			$catArray = getCallCat($keyID, $val[0], $val[1], $val[2]);
			if($catArray[1] == 0){
				$notDue++;
				if($val[0] == 10) $call2Unfinish++;
			}elseif($catArray[1] == 1){
				$due++;
				if($val[0] == 10) $call2Unfinish++;
			}else{
				$overDue++;
				if($val[0] == 10) $call2Unfinish++;
			}
			
			break;
		}
	}
	//echo "<br> call2 completed ".$compCall2." call 2 unreachable ".$unCall2." call 2 refused ".$refCall2;
	//echo "<br> call2 not yet due ".$notDue." call 2 due ".$due." over due ".$overDue;
	$totalC2 = count($ptRstArray);
	// get both call completed
	$compCC = getBothCC($heID, 'completed');
	// get both call not completed
	$notCompCC = getBothCC($heID, ''); 
	// get the all calls that only call 1 completed
	$call1Comp = getOnlyCall1Cmp($heID, $compCC, $call2Unfinish);
	// get all calls only call 2 completed
	$call2Comp = getOnlyCall2Cmp($heID, $compCC);
	
	// get the total of all columns
	$totalCC1 = $totalCC1 + $totalC1;
	$totalNotDueC1 = $totalNotDueC1 + $ptNotDueArray[0];
	$totalDueC1 = $totalDueC1 + $ptDueArray[0];
	$totalOvDueC1 = $totalOvDueC1 + $ptOvDueArray[0];
	$totalComC1 = $totalComC1 + $compCall1;
	$totalUnC1 = $totalUnC1 + $unCall1;
	$totalRefC1 = $totalRefC1 + $refCall1;
	$totalRefAll = $totalRefAll + $refAll;
	
	$totalCC2 = $totalCC2 + $totalC2;
	$totalNotDueC2 = $totalNotDueC2 + $notDue;
	$totalDueC2 = $totalDueC2 + $due;
	$totalOvDueC2 = $totalOvDueC2 + $overDue;
	$totalComC2 = $totalComC2 + $compCall2;
	$totalUnC2 = $totalUnC2 + $unCall2;
	$totalRefC2 = $totalRefC2 + $refCall2;
	
	$totalCompCC = $totalCompCC + $compCC;
	$totalNotCompCC = $totalNotCompCC + $notCompCC;
	$totalCall1Comp = $totalCall1Comp + $call1Comp;
	$totalCall2Comp = $totalCall2Comp + $call2Comp;
	
	$heCoachCallArray[$heName] = Array($totalC1, $ptNotDueArray[0], $ptDueArray[0], $ptOvDueArray[0], $compCall1, $unCall1, $refCall1 , $refAll,
								$totalC2, $notDue, $due, $overDue, $compCall2, $unCall2, $refCall2, $compCC, $call1Comp, $call2Comp, $notCompCC);
	
}
$totalCCArray = array($totalCC1, $totalNotDueC1, $totalDueC1, $totalOvDueC1, $totalComC1, $totalUnC1, $totalRefC1, $totalRefAll, 
				$totalCC2, $totalNotDueC2, $totalDueC2, $totalOvDueC2, $totalComC2, $totalUnC2, $totalRefC2, 
				$totalCompCC, $totalCall1Comp, $totalCall2Comp, $totalNotCompCC);
//print_r($totalCCArray);
//echo"<br>";
//print_r($heCoachCallArray);
echo "\nTotal\t$totalCCArray[0]\t$totalCCArray[1]\t$totalCCArray[2]\t$totalCCArray[3]\t$totalCCArray[4]\t$totalCCArray[5]\t$totalCCArray[6]\t$totalCCArray[7]\t\t$totalCCArray[8]\t$totalCCArray[9]\t$totalCCArray[10]\t$totalCCArray[11]\t$totalCCArray[12]\t$totalCCArray[13]\t$totalCCArray[14]\t$totalCCArray[15]\t$totalCCArray[16]\t$totalCCArray[17]\t$totalCCArray[18]\t";
if(count($heCoachCallArray)>0){
	// first to write the result to an excel file
	reset($heCoachCallArray);
	while(list($he, $valArray)=each($heCoachCallArray)){
		echo "\n$he\t$valArray[0]\t$valArray[1]\t$valArray[2]\t$valArray[3]\t$valArray[4]\t$valArray[5]\t$valArray[6]\t$valArray[7]\t\t$valArray[8]\t$valArray[9]\t$valArray[10]\t$valArray[11]\t$valArray[12]\t$valArray[13]\t$valArray[14]\t$valArray[15]\t$valArray[16]\t$valArray[17]\t$valArray[18]\t"; 
	} 
}else{
	echo "\n\n*******No Records In This Report!!******";
}

dbClose();; 

?> 


