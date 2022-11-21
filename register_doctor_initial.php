<?php
  session_start();
  $localhost = "192.168.254.102"; //Home
//$localhost = "192.168.1.11"; //Condo
//$localhost = "192.168.1.102"; //Router
  $pass = $user = $confirm = "";
  $registerErr = $passErr = $loginErr = "";
    if ($_SERVER["REQUEST_METHOD"] == "POST") 
    {
      $entry = false;
      if (empty($_POST["user"]) || empty($_POST["password"])|| empty($_POST["confirm"])) 
      {
        $registerErr = "Missing Fields";
			  $entry = true;
      } 
      else 
      {
        $user = test_input($_POST["user"]);
        $pass = test_input($_POST["password"]);
        $confirm = test_input($_POST["confirm"]);
      }
      
      if(!$entry)
      {	
        $sqlConnect = mysqli_connect("localhost","root","");
        if(!$sqlConnect) {
        die();
        }
        //choose the database
        $selectDB = mysqli_select_db($sqlConnect,"homespital");
        if(!$selectDB) {
        die("Database connection failed!" .mysqli_error());
        }

        Verify_Input($user, $pass, $confirm, $sqlConnect, $localhost);
        $_SESSION["user"] = $user;
        $_SESSION["password"] = $pass;
        echo 
        '<script>
            location="http://'.$localhost.'/homespital/register_doctor.html";
        </script>';
      }
    }

    function Verify_Input($user, $pass, $confirm, $sqlConnect, $localhost)
    {
      if($pass != $confirm)
      {
          echo 
              '<script>
                  alert("Passwords does not match ");
                  location="http://'.$localhost.'/homespital/register_doctor_initial.php";
              </script>';
      }

      $query = "SELECT * FROM users WHERE Username = '$user'";
      $result = mysqli_query($sqlConnect,$query);

      if(mysqli_num_rows($result) > 0)
      {   
          echo 
              '<script>
                  alert("Username already exixts");
                  location="http://'.$localhost.'/homespital/register_doctor_initial.php";
              </script>';
      }
    }

    function test_input($data) {
      $data = trim($data);
      $data = stripslashes($data);
      $data = htmlspecialchars($data);
      return $data;
     }

?>

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
    <div class="card-container">
      <div class="card login-card">
      <a class="card-title login-card" href="http://192.168.254.102/homespital/login.php" style="text-decoration: none;" >Homespital</a>
        <div class="card-body">
          <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <div class="form-group">
              <label for="exampleInputUsername1">Username </label>
              <input type="text" class="form-control" id="InputUsername1" placeholder="Enter Username" name="user">
            </div>
            <div class="form-group">
              <label for="exampleInputUsername1">Password</label>
              <input type="password" class="form-control" id="InputPassword" placeholder="Enter Password" name="password">
            </div>
            <div class="form-group">
              <label for="exampleInputPassword1">Confirm Password </label>
              <input type="password" class="form-control" id="InputConfirm" placeholder="Confirm Password" name="confirm">
            </div>
            <div class=" button-center">
                  <button type="submit" class="btn btn-login" value="Submit">Register</button>
                </div>
          </form> 
        </div>
      </div>
    </div><br><br>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>
</html>
