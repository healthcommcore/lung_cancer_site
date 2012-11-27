<?php
include("includes/connection.php");
include("includes/report_function.php");
$errMsg="";
// download_ix_modality_report.php
// created by VP
// referred to as: intervention modality report 
// purpose: gives totals and percentages of modality print/web and reminder yes/no of particpants enrolled in conditions 2 or 3 

// CONNECT TO DATABASE
if ($mysqlID=dbConnect()) {
    selectDatabase("lung_cancer_user");
	
// constants:
$today = date("m/d/Y");
$filetoday = date('Y-m-d');
define('EXCEL_FILENAME', 'ix_modality_report_'.$filetoday.'.xls');
define('EXCEL_HEADING', 'HD2 Ix modality report: '.$today);

// declare vars based on query:
$total_enrolled = ''; // total participants enrolled in conditions 2 or 3 to date
$total_web = ''; // total number of ix_modality = web
$total_print = ''; // total number of ix_modality = print
$percent_web = ''; // percent of ix_modality = web
$percent_print = ''; // percent of ix_modality = print
$total_reminder_yes = ''; // total number of reminder = yes 
$total_reminder_no = ''; // total number of reminder = no 
$percent_reminder_yes = ''; // percentage of reminder = yes 
$percent_reminder_no = ''; // percentage of reminder = no 

// per ix_modality table ixModID = 1 for web and 2 for print, in enrollment table field is called ixModality 
// per reminder_modality table remModID = 1 for voice and 2 for Text, in enrollment table field is called reminModality 

// SQL statements 
$sql = "SELECT count(*) AS total FROM part_info p INNER JOIN enrollment j ON p.partID = j.partID WHERE p.ptStatus = 'A'";
$sql1 = $sql." AND (j.ixModality = '1' OR j.ixModality = '2')"; // both types of enrollment
$sql2 = $sql." AND j.ixModality='1'"; // web only 
$sql3 = $sql." AND j.ixModality='2'"; // print only 
$sql4 = $sql1." AND j.remindRand='1' AND j.dateRemOpt IS NULL"; // reminder opt in 
$sql5 = $sql1." AND (j.remindRand='2' OR j.dateRemOpt IS NOT NULL)"; // reminder no
$sql6 = $sql3." AND dateIXChange != '0000-00-00'"; // web to print 
$sql7 = $sql6." AND ixID = 1"; // web inactivity 
$sql8 = $sql6." AND ixID = 2"; // internet access reduced 
$sql9 = $sql6." AND ixID = 3"; // personal preference 
$sql10 = $sql6." AND ixID = 4"; // other 
// get real values 
$total_enrolled = getCountBySQL($sql1);
$total_web = getCountBySQL($sql2);
$total_print = getCountBySQL($sql3);
$total_reminder_yes = getCountBySQL($sql4);
$total_reminder_no = getCountBySQL($sql5);
$total_web_to_print = getCountBySQL($sql6);
$total_web_inactivity = getCountBySQL($sql7);
$total_internet_reduced = getCountBySQL($sql8);
$total_personal_preference = getCountBySQL($sql9);
$total_other = getCountBySQL($sql10);

// SQL statements 
$sql = "SELECT count(*) AS total FROM part_info p INNER JOIN enrollment j ON p.partID = j.partID WHERE p.ptStatus = 'A' AND j.heID IS NULL"; // no coaching calls 
$sql1 = $sql." AND (j.ixModality = '1' OR j.ixModality = '2')"; // both types of enrollment
$sql2 = $sql." AND j.ixModality='1'"; // web only 
$sql3 = $sql." AND j.ixModality='2'"; // print only 
$sql4 = $sql1." AND j.remindRand='1' AND j.dateRemOpt IS NULL"; // reminder opt in 
$sql5 = $sql1." AND (j.remindRand='2' OR j.dateRemOpt IS NOT NULL)"; // reminder no
$sql6 = $sql3." AND dateIXChange != '0000-00-00'"; // web to print 
$sql7 = $sql6." AND ixID = 1"; // web inactivity 
$sql8 = $sql6." AND ixID = 2"; // internet access reduced 
$sql9 = $sql6." AND ixID = 3"; // personal preference 
$sql10 = $sql6." AND ixID = 4"; // other 
// get real values for arm2 
$arm2_enrolled = getCountBySQL($sql1);
$arm2_web = getCountBySQL($sql2);
$arm2_print = getCountBySQL($sql3);
$arm2_reminder_yes = getCountBySQL($sql4);
$arm2_reminder_no = getCountBySQL($sql5);
$arm2_web_to_print = getCountBySQL($sql6);
$arm2_web_inactivity = getCountBySQL($sql7);
$arm2_internet_reduced = getCountBySQL($sql8);
$arm2_personal_preference = getCountBySQL($sql9);
$arm2_other = getCountBySQL($sql10);

// SQL statements 
$sql = "SELECT count(*) AS total FROM part_info p INNER JOIN enrollment j ON p.partID = j.partID WHERE p.ptStatus = 'A' AND j.heID > 0";
$sql1 = $sql." AND (j.ixModality = '1' OR j.ixModality = '2')"; // both types of enrollment
$sql2 = $sql." AND j.ixModality='1'"; // web only 
$sql3 = $sql." AND j.ixModality='2'"; // print only 
$sql4 = $sql1." AND j.remindRand='1' AND j.dateRemOpt IS NULL"; // reminder opt in 
$sql5 = $sql1." AND (j.remindRand='2' OR j.dateRemOpt IS NOT NULL)"; // reminder no
$sql6 = $sql3." AND dateIXChange != '0000-00-00'"; // web to print 
$sql7 = $sql6." AND ixID = 1"; // web inactivity 
$sql8 = $sql6." AND ixID = 2"; // internet access reduced 
$sql9 = $sql6." AND ixID = 3"; // personal preference 
$sql10 = $sql6." AND ixID = 4"; // other 
// get real values for arm3
$arm3_enrolled = getCountBySQL($sql1);
$arm3_web = getCountBySQL($sql2);
$arm3_print = getCountBySQL($sql3);
$arm3_reminder_yes = getCountBySQL($sql4);
$arm3_reminder_no = getCountBySQL($sql5);
$arm3_web_to_print = getCountBySQL($sql6);
$arm3_web_inactivity = getCountBySQL($sql7);
$arm3_internet_reduced = getCountBySQL($sql8);
$arm3_personal_preference = getCountBySQL($sql9);
$arm3_other = getCountBySQL($sql10);

// generate report in Excel 
header("Content-disposition: attachment;filename=".EXCEL_FILENAME); 
header("Content-type: application/vnd.ms-excel"); 
header("Pragma: no-cache"); 
header("Expires: 0"); 
// print out the headers
echo "\n\t\t\t\t HD2 Ix Modality Report \n \t\t\t\tReport produced $today\n\n";
echo "\n\t\t\t\t\t Reasons web to print\n";
echo "\t Enrolled \t Print \t Web site \t Web to Print: TOTAL \t Protocol - web inactivity \t Internet access reduced \t Personal Preference \t Other \n";
echo "TOTAL \t $total_enrolled \t $total_print \t $total_web \t $total_web_to_print \t $total_web_inactivity \t $total_internet_reduced \t $total_personal_preference \t $total_other \n";
echo "ARM 2: Ix - materials only \t $arm2_enrolled \t $arm2_print \t $arm2_web \t $arm2_web_to_print \t $arm2_web_inactivity \t $arm2_internet_reduced \t $arm2_personal_preference \t $arm2_other \n";
echo "ARM 3: Ix - materials + CC \t $arm3_enrolled \t $arm3_print \t $arm3_web \t $arm3_web_to_print \t $arm3_web_inactivity \t $arm3_internet_reduced \t $arm3_personal_preference \t $arm3_other \n";
echo "\n\n";
// definitions do not need to be displayed on the report 
/*
echo "DEFINITIONS \n";
echo "Enrolled = total # participants ever enrolled \n";
echo "Print = # of enrolled who chose print \n";
echo "Web site = # of enrolled who chose web site \n";
echo "Web to print: TOTAL = # who switched (or were switched by us) to print \n";
echo "Reasons web to print (note that participants cannot switch from print to web) \n";
echo "\t Protocol - web inactivity: Didn't log on for 28 days w/in 1st 3 months of lx (this will be majority of Web to print: TOTAL)\n";
echo "\t Internet access reduced: unable to use web site due to Internet constraints \n"; 
echo "\t Personal preference: they just want the print materials instead \n";
echo "\t Other: We'll track reasons why in Excel file\n";
*/
} else {
	echo 'No database connection available.';
}
?>