<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">

<html>
<head>
	<title>Testing my javascript</title>
	<script language="JavaScript" type="text/javascript">
	<!-- 
		function checkFirst() {
		var cont = confirm('You have not clicked on the save button. Are you sure you want to continue?');
		if (cont == true) { 
			return true;
		} else {
			return false;
		}
	}
	//-->
	</script>
</head>

<body>

<form action="?" method="POST">
Here is a value: <input type="text" name="myvalue" value="12345">
<input type="submit" value="Run my script"> 
</form>

<form action="test-js2.php" method="POST">
<input type="submit" value="Go to another screen" onclick="return checkFirst();">
</form>

<? if (isset($_POST['myvalue'])) { ?>
<p>Yup. The script ran. Here is your value: <?php echo $_POST['myvalue']?></p>
<? } ?>
</body>
</html>
