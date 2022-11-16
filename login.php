<!doctype html>
<?php
// Start the session
session_start();
?>
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
    <title>Login Homespital</title>
  </head>

  <body>
  
	<?php
	    $localhost = "192.168.1.11"; //Condo
		//$localhost = "192.168.1.102"; //Router
	    $showRegister = 1;

	    //open the connection
	    $sqlConnect = mysqli_connect("localhost","root",'');
	    if(!$sqlConnect) {
	     die();
	    }
	    //choose the database
	    $selectDB = mysqli_select_db($sqlConnect,"homespital");
	    if(!$selectDB) {
	     die("Database connection failed!" .mysqli_error());
	    }	
		    

	    $userErr = $passErr = $loginErr = "";
	    if ($_SERVER["REQUEST_METHOD"] == "POST") 
	    {
		$entry = false;
		if (empty($_POST["user"])) 
		{
			$userErr = "Username is required";
			$entry = true;
		} 
		else 
		{
			$user = test_input($_POST["user"]);
		}
		
		if (empty($_POST["password"])) {
		$passErr = "Password is required";
		$entry = true;
		} 
		else 
		{
			$pass = test_input($_POST["password"]);
		}
		
		if(!$entry)
		{			      
		  $check = false;
		  $query = "select UserId, rights FROM users WHERE username='$user' AND password='$pass' ;";
		  $result = mysqli_query($sqlConnect,$query);
		  $data = mysqli_fetch_array($result);

		  if(!$result || mysqli_num_rows($result)==0) {
		      $loginErr = "* Incorrect username/password";
		      //echo "no table";
		  }
		  else if($data['rights'] == 1)
		  {
		      $_SESSION["userid"] = $data[0];
		      $_SESSION["rights"] = $data['rights'];
		      header("Location: http://".$localhost."/homespital/home_patient.php"); 					
		      exit();
		  }
		  else if($data['rights'] == 2)
		  {
		      header("Location: http://".$localhost."/homespital/main_doctor.php");
		      $_SESSION["userid"] = $data[0];
		      $_SESSION["rights"] = $data['rights'];
		      $_SESSION["doctorid"] = $data[0];
		      exit();
		      echo "Doctor  Rights";
		  }
		  else if($data['rights'] == 3 || $data['rights']==4)
		  {
		      $_SESSION["userid"] = $data[0];
		      $_SESSION["rights"] = $data['rights'];
		      header("Location: http://".$localhost."/homespital/get_client.php");
		      exit();
		  }
		  else{
			$_SESSION["userid"] = $data[0];
			$_SESSION["rights"] = $data['rights'];
			header("Location: http://".$localhost."/homespital/admin_dashboard.php?session_id=".session_id());
			exit();;
				var_dump($_SESSION);
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
        <h1 class="card-title login-card">Homespital</h1>
        <div class="card-body">
          <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <div class="form-group">
              <label for="exampleInputUsername1">Username <span class="error"><?php echo $userErr;?></span></label>
              <input type="username" class="form-control" id="InputUsername1" placeholder="Enter Username" name="user">
            </div>
            <div class="form-group">
              <label for="exampleInputPassword1">Password <span class="error"><?php echo $passErr;?></span></label>
              <input type="password" class="form-control" id="InputPassword1" placeholder="Enter Password" name="password">
            </div>
			<div>
				<span class="error"> <?php echo $loginErr;?></span></td></tr>
			</div>
			<div>
				<div class=" button-center">
				  <button type="submit" class="btn btn-login" value="Submit">Login</button>
				</div>

            </div>
          </form>
	    <?php 
	      if($showRegister==1)
	      {
		  echo '<form action="http://'.$localhost.'/homespital/register.php";>';
		  echo	'<div class=" button-center">';
		  echo		'<button type="submit" class="btn btn-login" value="Submit">Register</button>';
		  echo	'</div>';
		  echo '</form>';
	      }?>	
        </div>
      </div>
    </div>
  </body>
</html>

<?php
mysqli_close($sqlConnect);
?>
