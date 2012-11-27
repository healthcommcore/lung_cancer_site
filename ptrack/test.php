<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Test query</title>
</head>

<body>
<?php 
$db = mysql_connect('localhost','lung_cancer_site','439cwYY39ndB', '_user') or die("Error connecting to database");
/*$query = "SELECT userInfo.joomlaID, part_info.partID, part_info.ptEmail, part_info.ptFName, unix_timestamp(enrollment.startDate) as startDate FROM userInfo INNER JOIN enrollment ON userInfo.studyID = enrollment.partID INNER JOIN part_info  ON enrollment.partID = part_info.partID WHERE userInfo.activeStatus = 1 AND part_info.ptStatus = 'a' AND unix_timestamp(enrollment.startDate) < $wk25ago AND weekday(enrollment.startDate) = $DoW AND part_info.partID != 50488";*/
$query = "SELECT * FROM user_info";
$results = mysql_query($db, $query) or die("Error querying database");
while($row = mysql_fetch_assoc($results)){
	echo $row['joomlaID'] . '\n';
}
?>
	
</body>
</html>
