<?php
        session_start();
        //$localhost = "192.168.254.102"; //Home
        //$localhost = "192.168.1.13"; //Condo
        $localhost = "192.168.1.9"; //Condo
		//$localhost = "192.168.1.102"; //Router


        $sqlConnect = mysqli_connect("localhost","root","");
        if(!$sqlConnect) {
        die();
        }
        //choose the database
        $selectDB = mysqli_select_db($sqlConnect,"homespital");
        if(!$selectDB) {
        die("Database connection failed!" .mysqli_error());
        }
        if(isset($_POST['admin']))
        {
            $redirect = "/homespital/admin_dashboard.php";
            $UserId = $_POST['editUserID'];
            $Rights = $_POST['editUserRights'];
            $query = "SELECT * FROM caregiver_and_guest_users WHERE UserID = '$UserId' AND Rights = '$Rights' ;";
        }
        else{            
            $redirect = "/homespital/view_user.php";
            $UserId = $_SESSION['userid'];
            $Rights = $_SESSION['rights'];
            $query = "SELECT * FROM caregiver_and_guest_users WHERE ConnectedID = '$UserId' AND Rights = '$Rights' ;";
        }
        echo $UserId."\n";
        echo $Rights;
        
        //var_dump($_SESSION);

        echo $redirect;
        //echo $query;
        $result = mysqli_query($sqlConnect,$query);
        $data = mysqli_fetch_array($result);
        //var_dump($data);

        $caregiverID = $data["UserID"];

        $emailQuery = "SELECT Email FROM users WHERE UserID='$caregiverID' ;";
        $result = mysqli_query($sqlConnect,$emailQuery);
        $email = mysqli_fetch_array($result);
        //var_dump($data);

        $contactNumberInput = '<input type="text" class="form-control" id="personalNumber" name="personalNumber" value = "'.htmlspecialchars($data["ContactNumber"]).'" >';
        $emailInput = '<input type="text" class="form-control" id="email" name="email" value = "'.htmlspecialchars($email["Email"]).'" >';      

		if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['admin'])) 
		{
            UpdateUser($sqlConnect, $UserId, $caregiverID);
            mysqli_close($sqlConnect); 
            echo 
            '<script>
                alert("Successfully Updated Caregiver Account!");
                location="http://'.$localhost.$redirect.'";
            </script>';  
        }

        function UpdateUser($sqlConnect, $userID, $caregiverID)
        {
            $image = $imageName = $imageType = $query = "";
            $imageArray = array();
            
            $contactnumber = $_POST['countryCode'] . $_POST['personalNumber'];
            $email = $_POST['email'];
           
            $query = "UPDATE caregiver_and_guest_users SET ContactNumber = '$contactnumber' WHERE UserID = '$caregiverID'";

            $result = mysqli_query($sqlConnect,$query);
            if(!$result){
                die("Failed to connect: " . mysqli.error());
            }

            $emailQuery = "UPDATE users SET Email = '$email' WHERE UserID = '$caregiverID'";
            $result = mysqli_query($sqlConnect,$emailQuery);
            if(!$result){
                die("Failed to connect: " . mysqli.error());
            }

            $sql1 = "UPDATE `Web-Sync` SET LastSync = CURRENT_TIMESTAMP WHERE Name='users';";
	        $result = mysqli_query($sqlConnect,$sql1);
            if(!$result){
                die("Failed to connect: " . mysqli.error());
            }
	    
	        $sql2 = "UPDATE `Web-Sync` SET LastSync = CURRENT_TIMESTAMP WHERE Name='caregiver_and_guest_users';";
	        $result = mysqli_query($sqlConnect,$sql2);
            if(!$result){
                die("Failed to connect: " . mysqli.error());
            }
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
      <a class="card-title login-card" style="text-decoration: none;" >Homespital</a>
        <div class="card-body">
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" accept-charset="utf-8" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="personalNumber">Contact Number </label>
                    <?php echo $contactNumberInput; ?>
                </div>
                <div class=" button-center">
                    <button type="submit" class="btn btn-login" value="Submit">Login</button>
                </div>
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
