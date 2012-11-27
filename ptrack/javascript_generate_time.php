<?php
// LOAD CONNECTION FUNCTIONS
include("includes/connection.php");
$errIstMsg="";

// CONNECT TO DATABASE
if ($mysqlID=dbConnect()) {
    selectDatabase("lung_cancer_user");
} else {
    $errMsg.="<b>ERROR:</b> ".mysql_error()."<br>\n";
}
if ($_POST["isSubmitted"]==1) {
	//insert into the test_length table
	for ($a = 1; $a <=$_POST["counter"]; $a++){
		$sql="INSERT INTO test_length VALUES
				 (".$_POST["ptID".$a].", '".$_POST["start".$a]."', '".$_POST["end".$a]."')";
		$results=runQuery($sql);
		if ($results["status"]<=0){
			$errIstMsg =  "<font color=\"#FF0000\">Can not insert record into test_length table !! ".mysql_error()."</font><br>\n";
			$a = $_POST["counter"] +1;
		}
	}
}
?> 
 
<?php
if ($_POST["isSubmitted"]==1) {
	if ($errIstMsg!="") {
	    echo $errIstMsg;
	}else{
		echo "<font color = 'green' size = \"+1\">The following alternative contacts has been successfully saved in the database!</font><br>\n";
	}
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
<head>
	<TITLE>JavaScript</TITLE>
<SCRIPT LANGUAGE="JAVASCRIPT" TYPE="TEXT/JAVASCRIPT">
<!--
	function getStartTime(num) {
		var fieldName = "start"+num;
		var d = new Date();
		var t_hour = d.getHours();     // Returns hours
		var t_min = d.getMinutes();    // Returns minutes
		var t_sec = d.getSeconds();    // Returns seocnds
		var s_time = t_hour + ":" + t_min + ":" + t_sec;
	   	document["form1"][fieldName].value= s_time;
	   //alert("The time is " + t_hour + ":" + t_min + ":" + t_sec);
	   // document["form1"].submit();
	}
	
	function getEndTime(num) {
		var fieldName = "end"+num;
	    var d = new Date();
		var t_hour = d.getHours();     // Returns hours
		var t_min = d.getMinutes();    // Returns minutes
		var t_sec = d.getSeconds();    // Returns seocnds
		var e_time = t_hour + ":" + t_min + ":" + t_sec;
	    document["form1"][fieldName].value= e_time;
	}
	//-->
	//-->
//-->
</script>
</head>
<?php $i= 1;?>
<body>
<p>Click the radio button to generate start time and end time</p>
 <form name = form1 method="POST" >
 <input type="hidden" name="isSubmitted" value=1 >

<table border = 0 align = "center" cellpadding = 10>
<tr>
<td><input type = "hidden" name = "ptID<?php echo $i;?>" value = "<?php echo $i;?>"">
	<input type= "radio" name ="getStart"  onClick="getStartTime(<?php echo $i?>);">
			<font color="#526D6D"><em>Start Time</font></em>
<input type = "text" name = "start<?php echo $i;?>"value = ""></td>
<td><input type= "radio" name ="getEnd" value = "" onClick="getEndTime(<?php echo $i?>);">
			<font color="#526D6D"><em>End Time</font></em>
<input type = "text" name = "end<?php echo $i;?>" value = ""></td>
</tr>
<?php $i++;?>
<tr>
<td><input type = "hidden" name = "ptID<?php echo $i;?>" value = "<?php echo $i;?>"">
	<input type= "radio" name ="getStart"  onClick="getStartTime(<?php echo $i?>);">
			<font color="#526D6D"><em>Start Time</font></em>
<input type = "text" name = "start<?php echo $i;?>"value = ""></td>
<td><input type= "radio" name ="getEnd" value = "" onClick="getEndTime(<?php echo $i?>);">
			<font color="#526D6D"><em>End Time</font></em>
<input type = "text" name = "end<?php echo $i;?>" value = ""></td>
<input type = "hidden" name = "counter" value = "<?php echo $i;?>>"">
</tr>
<tr>
<td></td>
<td align = "right"><input type = "submit" value = "submit" size = 10></td>
</tr>
</table>
</form>
</body>
</html>
