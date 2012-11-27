<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
<head>
	<title>Vikki's Temp Page - Misc Code as Needed</title>
<script language="JavaScript" type="text/javascript">
function stepOne() {
	document["form1"].submit();
}

function stepTwo() {
	var cont = confirm('Any changes to Alternate Contact have not been saved. Do you want to continue to the Edit Enrollment form without saving?');
	if (cont == true) { document["form1"].submit(); } else { return false; }
}
</script>
</head>

<body>
<form id="form1" name="form1" method="POST" action="temp.php">
Enter some text: <input type="text" name="var" value="My info."><br>
<input type="submit" value="step one" onclick="stepOne();"> &nbsp;&nbsp; <input type="submit" value="step two" onclick="return stepTwo();">
</form>

<?php 
if (isset($_POST['var']) && $_POST['var'] != '') { echo 'Here is your input: <BR><BR>'.$_POST['var']; } 
?>
</body>
</html>
