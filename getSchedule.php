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
  $response = ""; //format MSD is column MSB of word is 1st row LSB of word is last row

  switch($day){
    case 'Saturday':
      $response = '0';
      break;

    case 'Friday':
      $response = '1';
      break;

    case 'Thursday':
      $response = '2';
      break;

    case 'Wednesday':
      $response = '3';
      break;

    case 'Tuesday':
      $response = '4';
      break;

    case 'Monday':
      $response = '5';
      break;

    case 'Sunday':
      $response = '6';
      break;
  }

  $sql_sched = "SELECT SCHEDULE.* FROM patient_users AS PATIENT LEFT JOIN medicine_intake_schedule AS SCHEDULE ON PATIENT.UserID = SCHEDULE.UserID WHERE PATIENT.box_ID = '$boxID';";
  $result_sched = $conn->query($sql_sched);
  //row_sched = mysqli_fetch_array($result_sched);
  $row_sched = $result_sched->fetch_assoc();

  $sql_check = "SELECT SCHEDULE.* FROM patient_users AS PATIENT LEFT JOIN medicine_intake_status AS SCHEDULE ON PATIENT.UserID = SCHEDULE.UserID WHERE PATIENT.box_ID = '$boxID';";
  $result_check = $conn->query($sql_check);
  $row_check = $result_check->fetch_assoc();
  
  

  
  for ($i=0; $i < 4; $i++) 
  {    
    if(strtotime($timenow)==strtotime($row_sched[$day.($i+1)]) && $row_check[$day.($i+1)]==0) 
    {
      $response = $response."1";
    }
    else
    {
      $response = $response."0";
    }
  }

  echo $response; //answer
  //echo "61111"; //testing
  
?>
