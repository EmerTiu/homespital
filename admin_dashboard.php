
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


//Retrieve List of Users
$query = "SELECT * FROM `users` WHERE Rights!='0'";
$result = mysqli_query($sqlConnect,$query);
$data = mysqli_fetch_assoc($result);
while( $row = mysqli_fetch_array($result)){
    $userData[] = $row; // Inside while loop
}
// echo '<pre>';
// var_dump($userData);
// echo '<pre>';


function test_input($data) 
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}


if(isset($_GET['Logout']))
{
    session_destroy();
    header("Location: http://".$localhost."/homespital/login.php"); 					
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") 
{

    switch($_POST['editUserRights']){
        case 1:
            $type = "patient";
            break;
        case 2:
            $type = "doctor";
            break;
        case 3:
            $type = "caregiver";
            break;
        default:
            $type = "guest";
    }
    $_SESSION["userid"] = $_POST['editUserID'];
    $_SESSION["rights"] = $_POST['editUserRights'];
    $_SESSION["adminPermission"] = 1;
    mysqli_close($sqlConnect); 
    echo 
    '<script>
        location="http://'.$localhost.'/homespital/edit_'.$type.'.php";
    </script>';  
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
			<div class="col-sm-4" style="text-align:center">Admin Dashboard</div>
            <div class="col-sm-4" style="text-align:right; ">
				<a href="admin_dashboard.php?Logout=true" style="color:#FFFFFF; text-decoration: none;">Logout</a>	
			</div>
		</div>
	</div>
	  
	<!-- Body Panel -->
	<div class="body row">
        <div class="body row"> </div>
		<!-- left panel -->
		<div class="left-body col-xs-12 col-sm-12 col-md-12 col-lg-12" style="border-radius: 10px;"> 
			
			<!-- Title -->
			<div style="background-color:#006b4a; border-radius: 5px; font-size: 24px; color:white"> Accounts </div>
				
			<!--Accounts table-->
			<div class = "row-12  d-flex table-wrapper-scroll-y my-custom-schedule col-lg-12" style = "Height: 700px" > 
				<table class="table ">
					<thead>
					  <tr>
                        <th>#</th>
                        <th>Date Created</th>
						<th>Username</th>
						<th>Email</th>
                        <th>Type</th>
                        <th></th>
                        <th>
                            <?php
                                echo '<form action="http://'.$localhost.'/homespital/register.php";>';
                                echo	'<div class=" button-center">';
                                echo		'<button type="submit" class="btn" value="Submit">+</button>';
                                echo	'</div>';
                                echo '</form>';
                            ?>                           
                        </th>
					  </tr>
					</thead>
					<tbody>
                        <?php for($i=0;$i<count($userData);$i++)
                            { ?>
                                <tr>     
                                    <td> <?php echo $i+1?></td>
                                    <td> <?php echo $userData[$i]['DateCreated'];  ?> </td>     
                                    <td> <?php echo $userData[$i]['Username'];  ?> </td>  
                                    <td> <?php echo $userData[$i]['Email'];  ?> </td> 
                                    <td> 
                                        <?php switch($userData[$i]['Rights'])
                                        {
                                            case 1:
                                                echo "Patient";
                                                break;
                                            case 2:
                                                echo "Doctor";
                                                break;
                                            case 3:
                                                echo "Caregiver";
                                                break;
                                            default:
                                                echo "Guest";
                                        }
                                              
                                        ?> 
                                    </td>  
                                    <td>
                                        <?php 
                                            echo '<form method="post" action="'.htmlspecialchars($_SERVER["PHP_SELF"]).'"';
                                            echo	'<div class=" button-center">';
                                            echo    '<input type="hidden" name="admin" value="true" />';
                                            echo    '<input type="hidden" name="editUserRights" value="'.$userData[$i]['Rights'].'" />';
                                            echo    '<input type="hidden" name="editUserID" value="'.$userData[$i]['UserID'].'" />';
                                            echo		'<button type="submit" class="btn btn-primary" value="Submit">Edit</button>';
                                            echo	'</div>';
                                            echo '</form>';
                                        ?> 
                                    </td>  
                                    <td>
                                        <?php 
                                            echo '<form method="post" action="http://'.$localhost.'/homespital/delete_user.php" onsubmit="return confirm(\'Are you sure you want to delete '.$userData[$i]['Username'].'.\')";>';
                                            echo	'<div class=" button-center">';
                                            echo    '<input type="hidden" name="Username" value="'.$userData[$i]['Username'].'" />';
                                            echo    '<input type="hidden" name="UserID" value="'.$userData[$i]['UserID'].'" />';
                                            echo		'<button type="submit" class="btn btn-danger" value="Submit">Delete</button>';
                                            echo	'</div>';
                                            echo '</form>';
                                        ?> 
                                    </td>  
                                </tr>  						 
					    <?php }?>
					</tbody>
				</table>
			</div>			
			<div class="body row"> </div>
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

