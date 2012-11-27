<?php

// FUNCTION: set STROKE settings
function mvgStroke($fp, $sSpecs) {
    if ($sSpecs[4]>0) {
        fputs($fp,"stroke-antialias ".$sSpecs[0]."\n");
        fputs($fp,"stroke-linecap ".$sSpecs[1]."\n");
        fputs($fp,"stroke-linejoin ".$sSpecs[2]."\n");
        fputs($fp,"stroke '".$sSpecs[3]."'\n");
        fputs($fp,"stroke-width ".$sSpecs[4]."\n");
    } else {
        fputs($fp,"stroke 'none'\n");
    }
}
// FUNCTION: set FILL settings
function mvgFill($fp, $fSpecs) {
    fputs($fp,"fill ".$fSpecs[0]."\n");
    fputs($fp,"fill-opacity ".$fSpecs[1]."\n");
}
// FUNCTION: draw POLYGON
function mvgPoly($fp, $pSTR) {
    fputs($fp,$pSTR."\n");
}
// FUNCTION: render TEXT
function mvgText($fp, $tSTR,$tSize) {
    fputs($fp,"font-size ".$tSize."\n");
    fputs($fp,$tSTR."\n");
}
// FUNCTION: ROTATE graphic elements
function mvgRotate($fp, $a) {
    fputs($fp,"rotate ".$a."\n");
}

// FUNCTION: define NEW ELEMENT
function newElement($fp, $eSpecs) {
    mvgStroke($fp, $eSpecs["stroke"]);
    mvgFill($fp, $eSpecs["fill"]);
    if ($eSpecs["eDef"][0]=="POLY") mvgPoly($fp, $eSpecs["eDef"][1]);
    if ($eSpecs["eDef"][0]=="TEXT") mvgText($fp, $eSpecs["eDef"][1],$eSpecs["eDef"][2]);
}

global $relSiteDir, $siteDir;
global $inputFile;

//>> CHANGES for substudy

define ( "JPATH_SITE", getcwd() . '/../../');

$imageDir = 'images/hd2/tracking/';
$siteDir= JPATH_SITE. "/images/hd2/tracking/";
$relSiteDir="../../images/hd2/tracking/";

// <<

// $weekFlag = true for weekly. Else 26-week chart
// $data = n/a for weekly chart
// $data = current week in 26-week chart
//
// Note behavior-specific parameters ($behaviorID used as index into $behaviorSpecs)
//	are also used for special case MRF History (to avoid having to write lots of
//	special code:
//		Filenames
//		goal/goalcompare: 
//		numSegmentsY
	
// function drawChart($behaviorID, $userData, $weekFlag, $data, $user, $rightNow) {
function drawChart($behaviorID, $userData, $weekFlag, $data, $user, $day, $outputfilename) {
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
	
	// $outputFile= JPATH_SITE. '/'. $imageDir.$outName.$behaviorID.".gif";
	$outputFile= JPATH_SITE. '/'. $imageDir.$outputfilename.".gif";
	// $relOutputFile=$relSiteDir.$outName.$behaviorID.".gif";
	$relOutputFile=$relSiteDir.$outputfilename.".gif";
	
	echo $outputFile;
	
	/*
	print_r($inputFile  );
	echo '<br>rel output';
	print_r($relOutputFile  );
	echo '<br>output';
	print_r($outputFile  );
	*/
	
	
	$today = $_SERVER['REQUEST_TIME'];
	$mvgFile = "/tmp/". $outName.$behaviorID."_".$today.".mvg";
    $fp=fopen($mvgFile,"w");

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


?>