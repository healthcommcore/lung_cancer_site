<?php
include("includes/connection.php");
$errMsg="";
//clear the session
$_SESSION = array();
// CONNECT TO DATABASE
if ($mysqlID=dbConnect()) {
    selectDatabase("lung_cancer_user");
} else {
    $errMsg.="<b>ERROR:</b> ".mysql_error()."<br>\n";
}
// if form was submitted...
if (isset($_POST["isSubmitted"]) && $_POST["isSubmitted"]==1) {
    if (($_POST["loginName"]!="") && ($_POST["password"]!="")) {
        // SELECT staffID and last name from staff table
        $sql="SELECT staffID, staffLName, title FROM staff WHERE (staffLName='".$_POST["loginName"]."') AND (password= '".$_POST["password"]."') AND status = 'A'";
        $results=runQuery($sql);
        if ($results["status"]>0) {
            if ($results["numRows"]>0) {
				if($results["returnedRows"][0]["title"] == "Adm"){
					header("Location: admin_home.php");
			        exit(); 
				}elseif($results["returnedRows"][0]["title"] == "Sup"){
					// VP changed this from header("Location: download_fu_list.php?"); 
					header("Location: get_fu_report.php?");
			        exit(); 
				}else{
				    // get staffID
					$heID=$results["returnedRows"][0]["staffID"];
					// set cookie for heID
					setcookie  ( 'heID', $heID, time()+60*60*24); // cookie lasts one day 
					$title = $results["returnedRows"][0]["title"];
					if($title == "RA"){
						$url = "staff_home.php?title=".$title;
					}else{
						$url = "staff_home.php?title=".$title."&heID=".$heID;
					}
					header("Location:".$url);
			        exit();        
			    }
            } else {
                $errMsg.="<b><font color = 'red'>We couldn't find that username and password in the database. Please check it and try again.</font></b><br>\n";
            }
        } else {
            $errMsg.="<b><font color = 'red'>There was a problem accessing the database. Please try again.</font></b><br>\n";
        	
		}

    } else {
        $errMsg.="<b><font color = 'red'>Oops, username OR password wasn't filled out.</font></b><br>\n";
    }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
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
		       <h2 align = "center">HD2 Tracking	</h2>	 
		 </div>
		 <!-- End Header -->
		 
         <!-- Begin Faux Columns -->
		 <div id="faux">
		 
		       <!-- Begin Left Column -->
		       <div id="leftcolumn"> 
		 
		       </div>

		       <!-- End Left Column -->
		 
		       <!-- Begin Right Column -->
		       <div id="rightcolumn">
		       		<?php if ($errMsg!="") echo $errMsg; ?>
	<form method="POST" action="login.php">
    <input type="hidden" name="isSubmitted" value=1 >
    <br><br>
	<h5 align = "center">(Please make sure you use the Firefox broswer)</h3>
	<br><br>
    <p align = "center">User Name:
    	<select name="loginName">
    	<?php
    	// SELECT lname name from table staff and display in a popup window
    	$sql="SELECT DISTINCT staffLName FROM staff WHERE status = 'A'";
    	$results=runQuery($sql);
    	if ($results["status"]>0) {
        	if ($results["numRows"]>0) {
            	for ($row=0; $row<$results["numRows"]; $row++) {
                	echo "<option value='".$results["returnedRows"][$row]["staffLName"]."'>".$results["returnedRows"][$row]["staffLName"]."</option>\n";
            	}
        	}
    	}
    	?>
    	</select></p>
		<br><br>
		<p align = "center">Password: <input type="password" name="password" value="" size = 13></p>
		<br><br><br>
	    <p align = "right"><input type = "submit" value = "login" size = 100></p>
	
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
