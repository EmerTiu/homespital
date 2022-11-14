<?php
 
  $servername = "localhost";
  $username = "root";
  $password = "password";
  $dbname = "homespital";
  $boxID = $_POST['box_id'];
  //$boxID = "A00001";

  // Create connection
  $conn = new mysqli($servername, $username, $password, $dbname);
  // Check connection
  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }

  date_default_timezone_set("Asia/Manila");
  $day = date("l"); 
  $timenow=date("H:i");
  $time = strtotime($timenow);
  $time = $time - 60;
  $check = date("H:i", $time);
  $response = ""; //format MSD is column MSB of word is 1st row LSB of word is last row

  $sql_sched = "SELECT SCHEDULE.* FROM patient_users AS PATIENT LEFT JOIN medicine_intake_schedule AS SCHEDULE ON PATIENT.UserID = SCHEDULE.UserID WHERE PATIENT.box_ID = '$boxID';";
  $result_sched = $conn->query($sql_sched);
  $row_sched = $result_sched->fetch_assoc();

  $sql_check = "SELECT SCHEDULE.* FROM patient_users AS PATIENT LEFT JOIN medicine_intake_status AS SCHEDULE ON PATIENT.UserID = SCHEDULE.UserID WHERE PATIENT.box_ID = '$boxID';";
  $result_check = $conn->query($sql_check);
  $row_check = $result_check->fetch_assoc();
  
  for ($i=0; $i < 4; $i++) 
  {  
      
    if(strtotime($check)==strtotime($row_sched[$day.($i+1)]) && $row_check[$day.($i+1)]==0) 
    {
      $currDay = $day.($i+1);
      $sql_upload = "UPDATE medicine_intake_status AS SCHEDULE LEFT JOIN patient_users AS PATIENT ON PATIENT.UserID = SCHEDULE.UserID SET SCHEDULE.$currDay = '-1' , SCHEDULE.DateCreated=current_timestamp WHERE PATIENT.box_ID = '$boxID'";
      $conn->query($sql_upload);
      $sql_update = "UPDATE `Web-Sync` SET LastSync = current_timestamp WHERE Name = 'medicine_intake_status'";
      $conn->query($sql_update);
    }
  }
  
  $conn->close();

?>
