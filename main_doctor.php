
<?php

session_start();
$localhost = "192.168.254.102"; //Home
//$localhost = "192.168.1.11"; //Condo
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
$doctorid = $_SESSION['doctorid'];
$clientList = array();

$_SESSION["userid"] = $doctorid;

//Retrieve Doctor Information
$query = "select * FROM doctor_users WHERE userid='$doctorid' ;";
$result = mysqli_query($sqlConnect,$query);
$data = mysqli_fetch_array($result);
$name = $data['FirstName'].' '.$data['LastName'];
$contactNumber = $data['PhoneNumber'];
$image = base64_encode($data['Image']);
$clientList = json_decode($data['ClientList'],true);

//Retrieve Clients' Information
$index = 0;
$clientDetails[][] = array(array());
if(!empty($clientList))
{
    foreach($clientList as $client)
    {
        $clientQuery = "select * FROM patient_users WHERE userid='$client' ;";
        $result = mysqli_query($sqlConnect,$clientQuery);
        $clientData = mysqli_fetch_array($result);

        $vitalsQuery = "select * FROM vitals_table WHERE userid='$client' order by DateCreated desc ;";
        $vitals = mysqli_query($sqlConnect,$vitalsQuery);
        $vitalData = mysqli_fetch_array($vitals);

        $clientDetails[$index][0] = $clientData['FirstName'];
        $clientDetails[$index][1] = $clientData['LastName'];
        $clientDetails[$index][2] = $clientData['Bloodtype'];
        $clientDetails[$index][3] = $clientData['ContactNumber'];
        $clientDetails[$index][4] = base64_encode($clientData['Image']);
        (!empty($vitalData)) ? $clientDetails[$index][5] = $vitalData['BodyTemp'] : $clientDetails[$index][5] ="";
        (!empty($vitalData)) ? $clientDetails[$index][6] = $vitalData['OxygenSat'] : $clientDetails[$index][6] ="";
        (!empty($vitalData)) ? $clientDetails[$index][7] = $vitalData['PulseRate'] : $clientDetails[$index][7] ="";
        (!empty($vitalData)) ? $clientDetails[$index][8] = $vitalData['PerfusionIndex'] : $clientDetails[$index][8] ="";
        (!empty($vitalData)) ? $clientDetails[$index][9] = $vitalData['DateCreated'] : $clientDetails[$index][9] ="";
        $index++;
    }
}

function test_input($data) 
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

if(isset($_POST["submit"]) && isset($_POST["EnterClient"]))
{
    $_SESSION["userid"] = $_POST["submit"];
    $_SESSION["name"] = $data['LastName'];
    header("Location: http://".$localhost."/homespital/home_patient.php"); 					
    exit();
}

if(isset($_GET['Logout']))
{
    session_destroy();
    header("Location: http://".$localhost."/homespital/login.php"); 					
    exit();
}

//Adds Users to Client List
if(isset($_POST['Add']) && !empty($_POST['userid']))
{
    $userid = $_POST['userid'];
    //$lastName = $_POST['lastName'];

    $query = "select UserID FROM patient_users WHERE UserID='$userid';";
    $result = mysqli_query($sqlConnect,$query);
    $userID = mysqli_fetch_array($result);

    if(!empty($userID) && empty($clientList)) 
    {
        $clientList = array($userID['UserID']);       
        $newList = json_encode($clientList);
        $updateQuery = "UPDATE doctor_users set ClientList='$newList', DateCreated=current_timestamp WHERE UserID = '$doctorid';";   
        $result = mysqli_query($sqlConnect,$updateQuery); 
	$syncQuery = "UPDATE `Web-Sync` SET LastSync = current_timestamp WHERE Name = 'doctor_users';";
	mysqli_query($sqlConnect,$syncQuery);  
        header("Refresh:0"); 
    }

    if(!empty($userID) && !in_array($userID['UserID'], $clientList))
    {
        array_push($clientList, $userID['UserID']);        
        $newList = json_encode($clientList);
        $updateQuery = "UPDATE doctor_users set ClientList='$newList', DateCreated=current_timestamp WHERE UserID = '$doctorid';";   
        $result = mysqli_query($sqlConnect,$updateQuery);  
	$syncQuery = "UPDATE `Web-Sync` SET LastSync = current_timestamp WHERE Name = 'doctor_users';";
	mysqli_query($sqlConnect,$syncQuery);   
        
        header("Refresh:0"); 
    }
}

//Removes Users from Client List
if(isset($_POST['Remove']) && isset($_POST['submit']))
{    
    array_splice($clientList, array_search( $_POST["submit"], $clientList ), 1);
    $newList = json_encode($clientList);
    $updateQuery = "UPDATE doctor_users SET ClientList= '$newList', DateCreated=current_timestamp WHERE UserID='$doctorid';";
    $result = mysqli_query($sqlConnect,$updateQuery);  
    $syncQuery = "UPDATE `Web-Sync` SET LastSync = current_timestamp WHERE Name = 'doctor_users';";
    mysqli_query($sqlConnect,$syncQuery);      
    header("Refresh:0"); 
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
		
			<div class="col-sm-4" style="text-align:left">Homespital</div>
			<div class="col-sm-4" style="text-align:center">Welcome, 
				<?php echo "Dr. ". $data['LastName']; ?> 
			</div>
            
            <div class="col-sm-4 row" style="text-align:right; ">
				<div class="col-9" style="text-align:right; ">
                    <a href="view_user.php"> <img class="center rounded float-right"  src="assets/person-circle.png"  style="height:50px; width:50px"> </img> </a>
				</div>
				<div class="col-sm-3" style="text-align:right; ">
				    <a href="main_doctor.php?Logout=true" style="color:#FFFFFF; text-decoration: none;">Logout</a>	
			    </div>
					
			</div>
		</div>
	</div>
	  
	<!-- Body Panel -->
	<div class="body row">
        <!-- right panel -->
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" >
            <!-- Doctor Panel -->
            <div class="notify-box" style="background-color:white; border-radius: 10px;"> 
                <div style="background-color:#006b4a; border-radius: 5px; font-size: 24px; color:white">The Doctor</div>
                <div class= "body row">
                    <!-- class="col-xs-12 col-sm-12 col-md-12 col-lg-12" -->
                    <img src="data:image/png;base64,<?php echo $image; ?>" style="height: 300px; width:300px; display: block; margin-left: auto; margin-right: auto;" />
                    <div class="table-responsive table-condensed col-xs-12 col-sm-12 col-md-12 col-lg-12" style="text-align:left;"> 
                        <table class = "table" style="text-align:left;">
                            <tr>
                                <td><th>Name</th></td>
                                <td><?php echo $name; ?></td>
                            </tr>
                            <tr>
                                <td><th>Contact Number</th></td>
                                <td><?php echo $contactNumber; ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="body row"> </div>
		<!-- left panel -->
		<div class="left-body col-xs-12 col-sm-12 col-md-12 col-lg-12" style="border-radius: 10px;"> 
			
			<!-- Title -->
			<div style="background-color:#006b4a; border-radius: 5px; font-size: 24px; color:white"> Clients List </div>
				
			<!--Clients table-->
			<div class = "row-12  d-flex table-wrapper-scroll-y my-custom-schedule col-lg-12" style = "Height: 500px" > 
				<table class="table ">
					<thead>
					  <tr>
						<th> </th>
                        <th>Client Name </th>
						<th>Bloodtype</th>
						<th>Contact Number</th>
						<th>Profile</th>
						<th>Body temperature (Celcius)</th>
						<th>Oxygen Saturation</th>
						<th>Pulse Rate</th>
						<th>Perfusion Index</th>
						<th>Last Updated</th>
					  </tr>
					</thead>
					<tbody>
					    <form name ="form1" action="" method="post">
						<?php for($i=0;$i<$index;$i++)
						{ ?>
						    <tr>
							<td>
							    <input type = "radio" id=<?php echo $clientDetails[$i][0]; ?> name="submit" value= <?php echo $clientList[$i] ?>>
							    <label for = <?php echo $clientDetails[$i][0]; ?>>  </label>
							</td>       
							<td> <?php echo $clientDetails[$i][0]." ". $clientDetails[$i][1];  ?> </td>     
							<td> <?php echo $clientDetails[$i][2];  ?> </td>
							<td> <?php echo $clientDetails[$i][3];  ?> </td>
							<td> <?php echo '<img src="data:image/png;base64,'.$clientDetails[$i][4].'" style="width:100px; height:100px" />'  ?> </td>
							<td> <?php echo $clientDetails[$i][5];  ?> </td>
							<td> <?php echo $clientDetails[$i][6];  ?> </td>
							<td> <?php echo $clientDetails[$i][7];  ?> </td>
							<td> <?php echo $clientDetails[$i][8];  ?> </td>  
							<td> <?php echo $clientDetails[$i][9];  ?> </td>                        
						    </tr>        
						<?php } ?>   
						<input type="submit" value="Submit" name="EnterClient"> 
						<input type="submit" value = "Remove" name="Remove"> 
						 
					    </form>
					</tbody>
				</table>
			</div>			
			<div class="body row"> </div>
            <!-- Title -->
			<div style="background-color:#006b4a; border-radius: 5px; font-size: 24px; color:white"> Add Clients </div>
				
            <!--Add/Remove Clients-->
            <div class = "row-12 my-custom-schedule col-lg-12" style = "Height: 200px" > 
                <form method="post" action=<?php echo $_SERVER["PHP_SELF"];?>>
                    <label for="add">Patient ID <span class="error"></label>
                    <input type="text" class="form-control" id="userid"  name="userid">
                    <input type="submit" value = "Add" name="Add">                    
                </form>
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

