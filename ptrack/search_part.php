<?php
include("includes/connection.php");
$errMsg="";

// CONNECT TO DATABASE
if ($mysqlID=dbConnect()) {
    selectDatabase("lung_cancer_user");
} else {
    $errMsg.="<b>ERROR:</b> ".mysql_error()."<br></b>\n";
}

// check form submission
if ($_POST["isSubmittedLogOut"]==1) {
	header("Location: login.php");
	exit();
}
if(!$_POST["heID"]){
	$heID = $_GET['heID'];
}
// CHECK FORM SUBMISSION
if($_POST["isSubmittedSearch"]==1){
	$lname = str_replace('\\', '', $_POST["ptLName"]);
	$fname = str_replace('\\', '', $_POST["ptFName"]);
	$_POST["ptEmail"] = trim($_POST["ptEmail"]);
	//echo "the email is  ".$_POST["ptEmail"];
	// if searched by RA
	if(!$heID){
		if($_POST["partID"]){
			$ptFlg = getPtList($_POST["partID"], "","", "ra", 2);
		}elseif($lname && !$fname){
			$ptFlg = getPtList("", strtoupper($lname), "", "ra", 1);
		}elseif($fname && !$lname){
			$ptFlg = getPtList("", "", strtoupper($fname), "ra", 0);
		}elseif($fname && $lname){
			$ptFlg = getPtList("", strtoupper($lname), strtoupper($fname), "ra", 3);
		}elseif($_POST["ptEmail"]){
			// first passing val is email, second is hdID and third is flg to indicate it is for checking
			$ptFlg = getEmailPt($_POST["ptEmail"], "", 0);
		}elseif(!$_POST["partID"] && !$lname && !$fname && !$_POST["ptEmail"]){
			$searchErr = "<b><font color = 'red'>ERROR: you did not enter the Study ID or last name or first name!!</font></b>";
			$searchFlg = 1;
		}
	}else{
		if($_POST["partID"]){
			$ptFlg = getPtList($_POST["partID"], "","", $heID, 2);
		}elseif($lname && !$fname){
			$ptFlg = getPtList("", strtoupper($lname), "", $heID, 1);
		}elseif($fname && !$lname){
			$ptFlg = getPtList("", "", strtoupper($fname), $heID, 0);
		}elseif($fname && $lname){
			$ptFlg = getPtList("", strtoupper($lname), strtoupper($fname), "ra", 3);
		}elseif($_POST["ptEmail"]){
			// first passing val is email, second is hdID and third is flg to indicate it is for checking
			$ptFlg = getEmailPt($_POST["ptEmail"], $heID, 0);
		}elseif(!$_POST["partID"] && !$lname && !$fname && !$_POST["ptEmail"]){
			$searchErr = "<b><font color = 'red'>ERROR: you did not enter the Study ID or last name or first name!!</font></b>";
			$searchFlg = 1;
		}
	}
	//echo "The pt flg is ".$ptFlg;
	//exit();
	if(!$searchFlg){
		// if no record exist, display error message
		if($ptFlg ==1){
			$searchErr = "<b><font color = 'red'>ERROR: based on the information you provided, ".$rcdErr."
			<br><br>Please check the Study ID, last name, or first name that you have entered and try again!
			<br><br>If all information entered correctly, then this participant withdrew from the study!!</b><br>";
		}elseif($ptFlg ==2){
			header("Location: part_list.php?ptLName=".$_POST["ptLName"]."&action=".$_POST["action1"]."&heID=".$heID."");
			exit();
		}elseif($ptFlg ==3){
			$errRcdMsg = "<b><font color = 'red'>ERROR: ".$rcdErr."</b>";
		}elseif($ptFlg ==4){
			header("Location: part_list.php?ptFName=".$_POST["ptFName"]."&action=".$_POST["action1"]."&heID=".$heID."");
			exit();
		}elseif($ptFlg ==5){
			header("Location: part_list.php?ptLName=".$_POST["ptLName"]."&ptFName=".$_POST["ptFName"]."&action=".$_POST["action1"]."&heID=".$heID."");
			//exit();
		}else{
			if($_POST["partID"]){
				$partID = $_POST["partID"];
			}elseif($lname && !$fname){
				// get participant ID based on last name
				$partArray = getPtLname($lname, $heID);
				reset ($partArray);
				list($partID, $ptVal)=each($partArray);
			}elseif($fname && !$lname){
				$partArray = getPtFname($fname, $heID);
				reset ($partArray);
				list($partID, $ptVal)=each($partArray);
			}elseif($fname && $lname){
				$partArray = getPtBname($fname, $lname, $heID);
				reset ($partArray);
				list($partID, $ptVal)=each($partArray);
			}elseif($_POST["ptEmail"]){
				$partID = getEmailPt($_POST["ptEmail"], $heID, 1);
				//echo "The part ID is ".$partID."<br>";
			}
			
			if($_POST["action1"] == "participants"){
				header("Location: edit_part.php?partID=".$partID."&heID=".$heID."");
				exit();
			}elseif($_POST["action1"] == "appointment"){
				header("Location: edit_appt.php?partID=".$partID);
				exit();
			}elseif($_POST["action1"] == "recruitment"){
				header("Location: edit_recruitment.php?partID=".$partID);
				exit();
			}elseif($_POST["action1"] == "contact"){
				header("Location: edit_contact.php?partID=".$partID."&heID=".$heID."");
				exit();
			}else{
				header("Location: edit_enrollment_one.php?partID=".$partID."&heID=".$heID."");
				exit();
			}
		}
	}
}

?>
<!DOCTYPE html PUBLIC "-//W3C//ulD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/ulD/xhtml1-strict.uld">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>HD2 Tracking</title>
<link rel="stylesheet" type="text/css" href="includes/main.css" />

</head>

<body>
	<!-- Begin Wrapper -->
   <div id="wrapper">
   
         <!-- Begin Header -->

         <div id="header">
		 <br><br>
		<h2 align = "center"> Search Participants</h2>  
		<br><p align = "right"><form method="POST" action="login.php" >
	     <input type = "submit" value = "log out" size = 100></form></p>   
		 </div>
		 <!-- End Header -->
		 
         <!-- Begin Faux Columns -->
		 <div id="faux">
		 
		       <!-- Begin Left Column -->
		       <div id="leftcolumn1">
				 <?php
				 	if($heID){
						$menu = getMenu1($heID);
					}else{
						$menu = getMenu();
					}
					echo $menu;
				?>
		       </div>

		       <!-- End Left Column -->
		 
		       <!-- Begin Right Column -->
		       <div id="rightcolumn">
		        <form name = form2 method="POST" action="search_part.php?heID=<?php echo $heID; ?>" >
				<input type="hidden" name="isSubmittedSearch" value= 1>
				<?php if ($errMsg!="") echo $errMsg;
				if ($errRcdMsg!="") echo $errRcdMsg;
				if ($searchErr!="") echo $searchErr;
				?>
			    <table border = 0 align = "center" cellpadding = 10>
	<tr>
		<td colspan = 2 align = "left" valign = "top">
    	    <font color="#526D6D"><em>Search for a participant by Study ID, Last Name and/or First Name </em></font></td>
	</tr>
	<tr valign = "bottom">
		<td align = "right"><font color="#526D6D"><em>&nbsp;&nbsp;Study ID</em></font></td>
		<td><input type = "text" name = "partID" value = "<?php if($_POST["isSubmittedSearch"]==1) echo trim($_POST["partID"]);?>"></td>
	</tr>
	<tr>
		<td></td>
		<td align = "left"> <font color="#526D6D"><em>OR </em></font></td>
	</tr>
	<tr>
		<td align = "right"><font color="#526D6D"><em>&nbsp;&nbsp;Last Name</em></font></td>
		<td><input type = "text" name = "ptLName" value = "<?php if($_POST["isSubmittedSearch"]==1) echo str_replace('\\', '', trim($_POST['ptLName']));?>">
		</td>
	</tr>
	<tr>
		<td></td>
		<td align = "left"> <font color="#526D6D"><em>AND/OR </em></font></td>
	</tr>
	<tr>
		<td align = "right"><font color="#526D6D"><em>&nbsp;&nbsp;First Name</em></font></td>
		<td><input type = "text" name = "ptFName" value = "<?php if($_POST["isSubmittedSearch"]==1) echo str_replace('\\', '', trim($_POST["ptFName"]));?>">
		</td>
	</tr>
	<tr>
		<td></td>
		<td align = "left"> <font color="#526D6D"><em>OR </em></font></td>
	</tr>
	<tr>
		<td align = "right"><font color="#526D6D"><em>&nbsp;&nbsp;Email</em></font></td>
		<td><input type = "text" name = "ptEmail" value = "<?php if($_POST["isSubmittedSearch"]==1) echo str_replace('\\', '', trim($_POST["ptEmail"]));?>">
		</td>
	</tr>
	<tr>
		<td></td>
		<td>
			<input type= "radio" name ="action1" value = "participants" checked>
			<font color="#526D6D"><em>Edit Participant Info
		</td>
	</tr>
	<?php if(!$heID){?>
	<tr>
		<td></td>
		<td>
		<input type= "radio" name ="action1" value = "appointment">
			<font color="#526D6D"><em>Edit Appointment
		</td>
	</tr>
	<tr>
		<td></td>
		<td>
		<input type= "radio" name ="action1" value = "recruitment">
			<font color="#526D6D"><em>Edit Recruitment</font></em>
		</td>
	</tr>
	<?php }?>
	<tr>
		<td></td>
		<td>
		<input type= "radio" name ="action1" value = "contact">
			<font color="#526D6D"><em>Edit Alternative Contact</font></em>
		</td>
	</tr>
	
	<tr>
		<td></td>
		<td>
		<input type= "radio" name ="action1" value = "enrollment">
			<font color="#526D6D"><em>Edit Enrollment</font></em>
		</td>
	</tr>
	<tr>
		<td colspan = 2 align = "right"><input type = "submit" value = "Search"></td>
	</tr>
  </table>
	</form>
		       
			   <div class="clear"></div>
			   
		       </div>
		       <!-- End Right Column -->
			   
			   <div class="clear"></div>
			   
         </div>	   
         <!-- End Faux Columns --> 
		 
   </div>
   <!-- End Wrapper -->
  
</body>
</html>
