<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- My CSS-->
    <link rel="stylesheet" href="style.css">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../node_modules/bootstrap/dist/css/bootstrap.min.css">
</head>
<body>

<?php
    session_start();
    $localhost = "192.168.1.102"; 
    
		
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
		    header("Location: http://".$localhost."/thesis/dev-router/register_patient.html"); 					
		    exit();
		}
	    else
	    {
		header("Location: http://".$localhost."/thesis/dev-router/register_doctor.html"); 					
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
?>

    <div class="card-container">
      <div class="card login-card">
        <a class="card-title login-card" href=<?php echo "http://".$localhost."/thesis/dev-router/login.php"; ?> style="text-decoration: none;" >Homespital</a>
        <div class="card-body">
          <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <div class="form-group">
              <!-- <label for="exampleInputUsername1">Username <span class="error"><?php //echo $userErr;?></span></label> -->
              <label for="exampleInputUsername1">Username </label>
              <input type="username" class="form-control" id="InputUsername1" placeholder="Enter Username" name="user">
            </div>
            <div class="form-group">
              <!-- <label for="exampleInputUsername1">Email <span class="error"><?php //echo $userErr;?></span></label> -->
              <label for="exampleInputUsername1">Email</label>
              <input type="email" class="form-control" id="InputEmail1" placeholder="Enter Email" name="email">
            </div>
            <div class="form-group">
              <!-- <label for="exampleInputPassword1">Password <span class="error"><?php //echo $passErr;?></span></label> -->
              <label for="exampleInputPassword1">Password </label>
              <input type="password" class="form-control" id="InputPassword1" placeholder="Enter Password" name="password">
            </div>
            <div class="form-group">                
                <label for = "rights1"> Patient </label>
                <input type = "radio" id="rights1" name="submit" value= "1">
            </div>
            <div class="form-group">
                <label for = "rights2"> Doctor </label>
                <input type = "radio" id="rights2" name="submit" value= "2">
            </div>
	    <div>
		<span class="error"><?php echo $registerErr;?></span></td></tr>
	    </div>
	    <div>
		<div class=" button-center">
		  <button type="submit" class="btn btn-login" value="Submit">Register</button>
		</div>
            </div>
          </form>
        </div>
      </div>
    </div>
</body>
</html>
