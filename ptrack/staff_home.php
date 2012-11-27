<?php
include("includes/connection.php");
$errMsg="";

// CONNECT TO DATABASE
if ($mysqlID=dbConnect()) {
    selectDatabase("lung_cancer_user");
} else {
    $errMsg.="<b>ERROR:</b> ".mysql_error()."<br>\n";
}

// get the staff title
if($_POST["title"]){
	$title = $_POST["title"];
}else{
	$title =$_GET['title']; 
}
// check form submission
if ($_POST["isSubmittedLogOut"]==1) {
	header("Location: login.php");
	exit();
}
$heID = $_GET['heID'];

?>
<!DOCTYPE html PUBLIC "-//W3C//ulD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/ulD/xhtml1-strict.uld">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>HD2 Tracking</title>
<link rel="stylesheet" type="text/css" href="includes/main.css" />
</head>

<body>

   
	<?php if($title == "RA"){?>
	<!-- Begin Wrapper -->
   <div id="wrapper">
   
         <!-- Begin Header -->

         <div id="header">
		 <br><br>
		<h2 align = "center"> Home Page for <?php echo $title?></h2>  
		<br><p align = "right"><form method="POST" action="login.php" >
	     <input type = "submit" value = "log out" size = 100></form></p>   
		 </div>
		 <!-- End Header -->
		 
         <!-- Begin Faux Columns -->
		 <div id="faux">
		 
		       <!-- Begin Left Column -->
		       <div id="leftcolumn1">
		 			<?php
						$menu = getMenu();
						echo $menu;
					?>
		       </div>

		       <!-- End Left Column -->
		 
		       <!-- Begin Right Column -->
		       <div id="rightcolumn">
		        
				      <font color="#526D6D"><em><p align = "center"> Please use the links on the left side to navigate to the pages</p></em></font>

		       
			   <div class="clear"></div>
			   
		       </div>
		       <!-- End Right Column -->
			   
			   <div class="clear"></div>
			   
         </div>	   
         <!-- End Faux Columns --> 
		 
   </div>
   <!-- End Wrapper -->

	<?php }elseif($title == "HE"){?>
	<!-- Begin Wrapper -->
   <div id="wrapper">
   
   	<!-- Begin Header -->
    <div id="header">
	<br><br>
	<h2 align = "center"> Home Page for <?php echo $title?></h2> 	   
	<br><p align = "right"><form method="POST" action="login.php" >
	<input type = "submit" value = "log out" size = 100></form></p>
	</div>
	<!-- End Header -->
	<!-- Begin Faux Columns -->
	<div id="faux">
	   <!-- Begin Left Column -->
	   <div id="leftcolumn1">
			<?php
				$menu = getMenu1($heID);
				echo $menu;
			?>
		 <!-- End Left Column -->
		</div>
		<!-- Begin Right Column -->
       <div id="rightcolumn">
  			<p align = "center"> Please use the links on the left side to navigate to the pages</p>
       </div>
	   <!-- End Right Column -->
	   <div class="clear"></div>   
     </div>	   
     <!-- End Faux Columns --> 
	  </div> 
   <!-- End Wrapper -->
	<?php }?>  
  
</body>
</html>
