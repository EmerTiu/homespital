<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- My CSS-->
    <link rel="stylesheet" href="style.css">

    <!-- Bootstrap CSS -->
    <!-- <link rel="stylesheet" href="../node_modules/bootstrap/dist/css/bootstrap.min.css"> -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>
<body>

<?php

    //session_start();
    $localhost = "192.168.1.11"; //Condo
		//$localhost = "192.168.1.102"; //Router
    
		/*
    $registerErr = "";
    if ($_SERVER["REQUEST_METHOD"] == "POST") 
    {
	$entry = false;
	if (empty($_POST["user"]) || empty($_POST["email"]) || empty($_POST["password"]) || !isset($_POST["submit"])) 
	{
	    $registerErr = "* Missing Fields";
	    $entry = true;
	} 
	else 
	{
	    $user = test_input($_POST["user"]);
	    $email = test_input($_POST["password"]);
	    $pass = test_input($_POST["password"]);
	}	
	
	if(!$entry)
	{
	    
	    $_SESSION["register_username"] = $_POST["user"];
	    $_SESSION["register_password"] = $_POST["password"];
	    $_SESSION["register_email"] = $_POST["email"];
	    if($_POST["submit"] == 1)
		{
		    header("Location: http://".$localhost."/homespital/register_patient.html"); 					
		    exit();
		}
	    else
	    {
		header("Location: http://".$localhost."/homespital/register_doctor.html"); 					
		exit();
	    }
	}
    }
    function test_input($data) {
     $data = trim($data);
     $data = stripslashes($data);
     $data = htmlspecialchars($data);
     return $data;
    }
    */
?>

    <div class="card-container">
      <div class="card login-card">
        <a class="card-title login-card" href=<?php echo "http://".$localhost."/homespital/login.php"; ?> style="text-decoration: none;" >Homespital</a>
        <div class="card-body">
            <form method="post" action="register_patient_initial.php">
                <div class=" button-center">
                  <button type="submit" class="btn btn-login" value="Submit">Register as Patient</button>
                </div>
            </form>
            <form method="post" action="register_doctor_initial.php">
                <div class=" button-center">
                  <button type="submit" class="btn btn-login" value="Submit">Register as Doctor</button>
                </div>
            </form>
            <form method="post" action="register_caregiver.html">
                <div class=" button-center">
                  <button type="submit" class="btn btn-login" value="Submit">Register as Caregiver</button>
                </div>
            </form>
            <form method="post" action="register_guest.html">
                <div class=" button-center">
                  <button type="submit" class="btn btn-login" value="Submit">Register as Guest</button>
                </div>
            </form>
            </div>
          </form>
        </div>
      </div>
    </div>
</body>
</html>
