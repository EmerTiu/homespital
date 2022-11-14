<?php
session_start();
//$localhost = "192.168.254.134"; //Home
$localhost = "192.168.1.11"; //Condo
//$localhost = "192.168.1.102"; //Router
//Connecting to mysql 
$sqlConnect = mysqli_connect("localhost", "root","");
		
if(!$sqlConnect){
die("Error in accessing the database!". mysqli_error());
}

//Connecting to database 
$selectDB = mysqli_select_db($sqlConnect,"homespital");

if(!$selectDB){
die("Failed to connect to the databse" . mysqli_error());
}

$UserId = $_SESSION['userid'];
echo $UserId;
//Retirieving user credentials
$getData = mysqli_query($sqlConnect, "Select * from caregiver_and_guest_users where userid='$UserId';");
if(!$getData){
die("Failed to connect: " . mysqli.error());
}

$SR = mysqli_fetch_array($getData);
//echo $SR['ConnectedID'];
header("Location: http://".$localhost."/homespital/home_patient.php"); 
$_SESSION['userid'] = $SR['ConnectedID'];
exit();
?>
