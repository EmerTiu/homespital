
<?php

session_start();
//$localhost = "192.168.254.134"; //Home
$localhost = "192.168.1.11"; //Condo
//$localhost = "192.168.1.102"; //Router
//open the connection
$sqlConnect = mysqli_connect("localhost","root","");
if(!$sqlConnect) {
 die();
}
//choose the database
$selectDB = mysqli_select_db($sqlConnect,"homespital");
if(!$selectDB) {
 die("Database connection failed!" .mysqli_error());
}

//echo PHPinfo();
//var_dump($_SESSION);
$UserId = $_SESSION['userid'];
$Rights = $_SESSION['rights'];

$query = $count = "";
switch($Rights)
{
    case 1:
        $query = "SELECT * FROM patient_users WHERE UserID='$UserId' ;";
        break;
    case 2:
        $query = "SELECT * FROM doctor_users WHERE UserID='$UserId' ;";
        break;
    default:
        $query = "SELECT * FROM caregiver_and_guest_users WHERE ConnectedID = '$UserId' AND Rights = '$Rights' ;";
}

//Retrieve Information
$result = mysqli_query($sqlConnect,$query);
$data = mysqli_fetch_array($result);
$keys = array_keys($data);
$accountType = "";
if($Rights==1)
{
    $accountType = "Patient";
    $count =  (count($data)-8)/2;
}
else if($Rights==2)
{
    $accountType = "Doctor";
    $count =  (count($data)-10)/2;
}
else if($Rights==3)
{
    $accountType = "Caregiver";
    $count =  (count($data)-4)/2;
}
else if($Rights==4)
{
    $accountType = "Guest";
    $count =  (count($data)-4)/2;
}
?>

<!--      ------------------------------------------------------------------------------ -->
<!doctype html>

<html lang="en">
  <head>
  
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=yes">
    <meta http-equiv="Content_Type" content="text/html; charset=iso-8859-1">
    <!-- My CSS-->
    <link rel="stylesheet" href="style.css">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../node_modules/bootstrap/dist/css/bootstrap.min.css">

    <title>Home Patient Homespital</title>
	
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
  </head>

  <body>
	<!-- Top Panel -->
	<div class="card home-tab-card">
		<div class="menu row" >
		
			<div class="col-sm-6" style="text-align:left">Homespital</div>
            <div class="col-sm-6" style="text-align:right; ">
				<?php
                    if($Rights==2) echo '<a href="main_doctor.php" style="color:#FFFFFF; text-decoration: none;">Back</a>';
                    else echo '<a href="home_patient.php" style="color:#FFFFFF; text-decoration: none;">Back</a>';
                ?>
			</div>
		</div>
	</div>

    <div class="notify-box" style="background-color:white; border-radius: 10px;"> 
        <div style="background-color:#006b4a; border-radius: 5px; font-size: 24px; color:white">User Information</div>
        <div class= "body row">
            <!-- class="col-xs-12 col-sm-12 col-md-12 col-lg-12" -->
            <?php
                if($Rights == 1 || $Rights == 2) echo '<img src="data:image/png;base64, '. base64_encode($data["Image"]).' " style="height: 300px; width:300px; display: block; margin-left: auto; margin-right: auto;" />';
            ?>            
            <div class="table-responsive table-condensed col-xs-12 col-sm-12 col-md-12 col-lg-12" style="text-align:left;"> 
                <table class = "table" style="text-align:left;">
                            <tr>
                                <td>Account Type </td>
                                <td><?php echo $accountType; ?></td>
                            </tr>
                    <?php
                        for($i=0;$i<$count;$i++)
                        {?>
                            <tr>
                                <td><?php echo $keys[(2*$i)+1]; ?> </td>
                                <td><?php echo $data[$i]; ?></td>
                            </tr><?php
                        }
                    ?>
                </table>
            </div>
        </div>
    </div>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
	
    <script src="main.js"></script>
	<script src="thesis_script.php"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
	<script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <!-- <link rel="stylesheet" href="../node_modules/bootstrap/dist/css/bootstrap.min.css"> -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
  </body>
</html>

<?php

mysqli_close($sqlConnect);
?>

