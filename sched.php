<html>
<head><title></title></head>
<body>

<?php
	session_start();
	$servername = "localhost";
    $username = "root";
    $password = "";
	$dbname = "homespital";	
	//$localhost = "192.168.1.13";
	//$localhost = "192.168.254.134";
	$localhost = "192.168.1.11"; //Condo
		//$localhost = "192.168.1.102"; //Router
	
	var_dump($_POST['Time']);

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
    }
	
	//$sched_concat = $_SESSION['sched'];
	$userid = $_SESSION['userid'];
	for ($i=1; $i <= 4; $i++) { 
     
      $tempSun = $_POST['Time'][$i-1][0];
	  $tempMon = $_POST['Time'][$i-1][1];
	  $tempTue = $_POST['Time'][$i-1][2];
	  $tempWed = $_POST['Time'][$i-1][3];
	  $tempThu = $_POST['Time'][$i-1][4];
	  $tempFri = $_POST['Time'][$i-1][5];
	  $tempSat = $_POST['Time'][$i-1][6];
	  echo $tempSun;
	  $sun = "Sunday".$i;
	  $mon = "Monday".$i;
	  $tue = "Tuesday".$i;
	  $wed = "Wednesday".$i;
	  $thu = "Thursday".$i;
	  $fri = "Friday".$i;
	  $sat = "Saturday".$i;
      $sql = "UPDATE medicine_intake_schedule SET $sun= '$tempSun' , $mon='$tempMon', $tue='$tempTue', $wed='$tempWed', $thu='$tempThu', $fri='$tempFri', $sat='$tempSat', DateCreated=current_timestamp WHERE userid='$userid';";
      $conn->query($sql);
      
      $sql2 = "UPDATE `Web-Sync` SET LastSync = CURRENT_TIMESTAMP WHERE Name='medicine_intake_schedule';";
      $conn->query($sql2);
    }
	
	header("Location: http://".$localhost."/homespital/home_patient.php"); 
	$conn->close();
	
?>
</body>
</html>
