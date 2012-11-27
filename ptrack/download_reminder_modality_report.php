<?php
include("includes/connection.php");
include("includes/report_function.php");
$errMsg="";
// download_remind_modality_report.php
// created by VP
// referred to as: intervention modality report 
// purpose: gives totals and percentages of modality print/web and reminder yes/no of particpants enrolled in conditions 2 or 3 

// CONNECT TO DATABASE
if ($mysqlID=dbConnect()) {
    selectDatabase("lung_cancer_user");

// constants:
$today = date("m/d/Y");
$filetoday = date('Y-m-d');
define('EXCEL_FILENAME', 'reminder_modality_report_'.$filetoday.'.xls');
define('EXCEL_HEADING', 'HD2 Reminder modality report: '.$today);

// total enrolled 
$sql = "SELECT count(*) AS total FROM part_info p, enrollment j, appt a, provider pr WHERE p.partID = j.partID AND 
         p.MRN = a.MRN AND a.provdID = pr.provID AND p.ptStatus = 'A'";
$sql1 = $sql." AND (pr.randArm = 'mats' OR pr.randArm = 'mats+cc')"; // both types of enrollment
$sql2 = $sql." AND j.remindRand = '1'"; // reminder opt in 
$sql3 = $sql." AND j.remindRand != '1'"; // reminder opt out 
$sql4 = $sql2." AND j.reminModality = '1'"; // voice 
$sql5 = $sql2." AND j.reminModality = '2'"; // text 
// get real values 
$total_enrolled = getCountBySQL($sql1);
$total_reminder_yes = getCountBySQL($sql2);
$total_reminder_no = getCountBySQL($sql3);
$total_voice = getCountBySQL($sql4);
$total_text = getCountBySQL($sql5);
// reminder_modality table remModID = 1 for voice and 2 for Text, in enrollment table field is called reminModality 
$sql6 = $sql." AND j.remindOptID > 0";
// reminder opt outs 
$total_reminder_optouts = getCountBySQL($sql6);
$sql7 = $sql." AND j.remindOptID = '1'"; // Irritating 
$sql8 = $sql." AND j.remindOptID = '2'"; // Not Helpful 
$sql9 = $sql." AND j.remindOptID = '3'"; // Technical Reasons 
$sql10 = $sql." AND j.remindOptID = '4'"; // Cost 
$sql11 = $sql." AND j.remindOptID = '5'"; // Other 
$total_irritating = getCountBySQL($sql7); 
$total_nothelpful = getCountBySQL($sql8);
$total_technical = getCountBySQL($sql9);
$total_cost = getCountBySQL($sql10);
$total_other = getCountBySQL($sql11);
// reminder switches 
$sql12 = $sql." AND j.dateRemChang != '0000-00-00'";
$total_reminder_switches = getCountBySQL($sql12);
$sql13 = $sql12." AND j.remindID = '1'"; //
$sql14 = $sql12." AND j.remindID = '2'"; //
$sql15 = $sql12." AND j.remindID = '3'"; //
$sql16 = $sql12." AND j.remindID = '4'"; //
$total_rem_personal = getCountBySQL($sql13); 
$total_rem_technical = getCountBySQL($sql14); 
$total_rem_cost = getCountBySQL($sql15); 
$total_rem_other = getCountBySQL($sql16);

// ARM2, no coaching calls 
// total enrolled 
$sql = "SELECT count(*) AS total FROM part_info p, enrollment j, appt a, provider pr WHERE p.partID = j.partID AND 
         p.MRN = a.MRN AND a.provdID = pr.provID AND p.ptStatus = 'A'";
$sql1 = $sql." AND pr.randArm = 'mats'"; // both types of enrollment
$sql2 = $sql." AND pr.randArm = 'mats' AND j.remindRand = '1'"; // reminder opt in 
$sql3 = $sql." AND pr.randArm = 'mats' AND j.remindRand != '1'"; // reminder opt out 
$sql4 = $sql2." AND j.reminModality = '1'"; // voice 
$sql5 = $sql2." AND j.reminModality = '2'"; // text 
// get real values 
$arm2_enrolled = getCountBySQL($sql1);
$arm2_reminder_yes = getCountBySQL($sql2);
$arm2_reminder_no = getCountBySQL($sql3);
$arm2_voice = getCountBySQL($sql4);
$arm2_text = getCountBySQL($sql5);
// reminder_modality table remModID = 1 for voice and 2 for Text, in enrollment table field is called reminModality 
$sql6 = $sql." AND j.remindOptID > 0";
// reminder opt outs 
$arm2_reminder_optouts = getCountBySQL($sql6);
$sql7 = $sql." AND j.remindOptID = '1'"; // Irritating 
$sql8 = $sql." AND j.remindOptID = '2'"; // Not Helpful 
$sql9 = $sql." AND j.remindOptID = '3'"; // Technical Reasons 
$sql10 = $sql." AND j.remindOptID = '4'"; // Cost 
$sql11 = $sql." AND j.remindOptID = '5'"; // Other 
$arm2_irritating = getCountBySQL($sql7); 
$arm2_nothelpful = getCountBySQL($sql8);
$arm2_technical = getCountBySQL($sql9);
$arm2_cost = getCountBySQL($sql10);
$arm2_other = getCountBySQL($sql11);
// reminder switches 
$sql12 = $sql." AND j.dateRemChang != '0000-00-00'";
$arm2_reminder_switches = getCountBySQL($sql12);
$sql13 = $sql12." AND j.remindID = '1'"; //
$sql14 = $sql12." AND j.remindID = '2'"; //
$sql15 = $sql12." AND j.remindID = '3'"; //
$sql16 = $sql12." AND j.remindID = '4'"; //
$arm2_rem_personal = getCountBySQL($sql13); 
$arm2_rem_technical = getCountBySQL($sql14); 
$arm2_rem_cost = getCountBySQL($sql15); 
$arm2_rem_other = getCountBySQL($sql16);

// AMR3, materials plus coaching calls 
// total enrolled 
$sql = "SELECT count(*) AS total FROM part_info p, enrollment j, appt a, provider pr WHERE p.partID = j.partID AND 
         p.MRN = a.MRN AND a.provdID = pr.provID AND p.ptStatus = 'A'";
$sql1 = $sql." AND pr.randArm = 'mats+cc'"; // both types of enrollment
$sql2 = $sql." AND pr.randArm = 'mats+cc' AND j.remindRand = '1'"; // reminder opt in 
$sql3 = $sql." AND pr.randArm = 'mats+cc' AND j.remindRand != '1'"; // reminder opt out 
$sql4 = $sql2." AND j.reminModality = '1'"; // voice 
$sql5 = $sql2." AND j.reminModality = '2'"; // text 
// get real values 
$arm3_enrolled = getCountBySQL($sql1);
$arm3_reminder_yes = getCountBySQL($sql2);
$arm3_reminder_no = getCountBySQL($sql3);
$arm3_voice = getCountBySQL($sql4);
$arm3_text = getCountBySQL($sql5);
// reminder_modality table remModID = 1 for voice and 2 for Text, in enrollment table field is called reminModality 
$sql6 = $sql." AND j.remindOptID > 0";
// reminder opt outs 
$arm3_reminder_optouts = getCountBySQL($sql6);
$sql7 = $sql." AND j.remindOptID = '1'"; // Irritating 
$sql8 = $sql." AND j.remindOptID = '2'"; // Not Helpful 
$sql9 = $sql." AND j.remindOptID = '3'"; // Technical Reasons 
$sql10 = $sql." AND j.remindOptID = '4'"; // Cost 
$sql11 = $sql." AND j.remindOptID = '5'"; // Other 
$arm3_irritating = getCountBySQL($sql7); 
$arm3_nothelpful = getCountBySQL($sql8);
$arm3_technical = getCountBySQL($sql9);
$arm3_cost = getCountBySQL($sql10);
$arm3_other = getCountBySQL($sql11);
// reminder switches 
$sql12 = $sql." AND j.dateRemChang != '0000-00-00'";
$arm3_reminder_switches = getCountBySQL($sql12);
$sql13 = $sql12." AND j.remindID = '1'"; //
$sql14 = $sql12." AND j.remindID = '2'"; //
$sql15 = $sql12." AND j.remindID = '3'"; //
$sql16 = $sql12." AND j.remindID = '4'"; //
$arm3_rem_personal = getCountBySQL($sql13); 
$arm3_rem_technical = getCountBySQL($sql14); 
$arm3_rem_cost = getCountBySQL($sql15); 
$arm3_rem_other = getCountBySQL($sql16);

// generate report in Excel 
header("Content-disposition: attachment;filename=".EXCEL_FILENAME); 
header("Content-type: application/vnd.ms-excel"); 
header("Pragma: no-cache"); 
header("Expires: 0"); 
// print out the headers
echo "\n\t\t\t\t HD2 Reminder Modality Report \n \t\t\t\tReport produced $today\n\n";
echo "\t\t\t\t\t\t\t Reasons for opt-out \t\t\t\t\t\t Reasons for switch \n";
echo "\t Enrolled \t Reminder = \"no\" \t Reminder = \"yes\" \t \"Voice\" (AVR) \t \"Text\" (SMS) \t # reminder opt-outs \t Do no like \t Not helpful \t Technical reasons \t Cost \t Other \t # reminder modality switch \t Personal preference \t Technical Reasons \t Cost \t Other \n";
echo "TOTAL \t $total_enrolled \t $total_reminder_no \t $total_reminder_yes \t $total_voice \t $total_text \t  $total_reminder_optouts \t $total_irritating \t $total_nothelpful \t $total_technical \t $total_cost \t $total_other \t $total_reminder_switches \t $total_rem_personal \t $total_rem_technical \t $total_rem_cost \t $total_rem_other \n";
echo "ARM 2: Ix - materials only \t $arm2_enrolled \t $arm2_reminder_no \t $arm2_reminder_yes \t $arm2_voice \t $arm2_text \t  $arm2_reminder_optouts \t $arm2_irritating \t $arm2_nothelpful \t $arm2_technical \t $arm2_cost \t $arm2_other \t $arm2_reminder_switches \t $arm2_rem_personal \t $arm2_rem_technical \t $arm2_rem_cost \t $arm2_rem_other \n";
echo "ARM 3: Ix - materials + CC \t $arm3_enrolled \t $arm3_reminder_no \t $arm3_reminder_yes \t $arm3_voice \t $arm3_text \t  $arm3_reminder_optouts \t $arm3_irritating \t $arm3_nothelpful \t $arm3_technical \t $arm3_cost \t $arm3_other \t $arm3_reminder_switches \t $arm3_rem_personal \t $arm3_rem_technical \t $arm3_rem_cost \t $arm3_rem_other \n";
// definitions do not need to be displayed on the report 
/*
echo "DEFINITIONS:\n";
echo "Enrolled: total # participants enrolled \n";
echo "Reminder = no: # enrolled and not randomized to reminder condition\n";
echo "Reminder = yes: # enrolled and randomized to reminder condition \n";
echo "\"Voice\" (AVR) = of those in reminder = yes, how many chose AVR \n";
echo "\"Text\" (SMS) = of those in reminder = yes, how many chose SMS \n";
echo "# reminder opt-outs = of those in reminder = yes, how many opted out? \n";
echo "\t Do not like: programmers note that this is coded as \"irritating\" in DB\n";
echo "\t Not helpful: participant didn't find them helpful\n";
echo "\t Technical reasons: i.e., having difficulty with these types of calls\n";
echo "\t Cost: if SMS cost is an issue \n";
echo "\t Other: We'll track reasons why in Excel file \n";
echo "# reminder modality switch = = of those in reminder = yes, how many switched modalities? \n";
echo "\t Personal preference \n";
echo "\t Technical reasons: i.e., having difficulty with SMS/AVR but willing to try other \n";
echo "\t Cost: if SMS cost is an issue\n";
echo "\t Other: We'll track reasons why in Excel file \n";
*/
} else {
	echo 'No database connection available.';
}
?>
