<?php
        session_start();
        //$localhost = "192.168.254.102"; //Home
        //$localhost = "192.168.1.13"; //Condo
        $localhost = "192.168.1.11"; //Condo
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

        $UserId = $_SESSION['userid'];
        $Rights = $_SESSION['rights'];
        
        //var_dump($_SESSION);
        $query = "SELECT * FROM patient_users WHERE UserID='$UserId' ;";
        $result = mysqli_query($sqlConnect,$query);
        $data = mysqli_fetch_array($result);

        $emailQuery = "SELECT Email FROM users WHERE UserID='$UserId' ;";
        $result = mysqli_query($sqlConnect,$emailQuery);
        $email = mysqli_fetch_array($result);
        //var_dump($data);

        $contactNumberInput = '<input type="text" class="form-control" id="personalNumber" name="personalNumber" value = "'.htmlspecialchars($data["ContactNumber"]).'" >';
        $emailInput = '<input type="text" class="form-control" id="email" name="email" value = "'.htmlspecialchars($email["Email"]).'" >';
        $addressInput = '<input type="text" class="form-control" id="address" name="address" value = "'.htmlspecialchars($data["Address"]).'" >';
        $contactNameInput1 = '<input type="text" class="form-control" id="contactPerson1" name="contactPerson1" value = "'.htmlspecialchars($data["ContactName1"]).'" >';
        $contactNumberInput1 = '<input type="text" class="form-control" id="contactNumber1" name="contactNumber1" value = "'.htmlspecialchars($data["ContactNumber1"]).'" >';
        $contactNameInput2 = '<input type="text" class="form-control" id="contactPerson2" name="contactPerson2" value = "'.htmlspecialchars($data["ContactName2"]).'" >';
        $contactNumberInput2 = '<input type="text" class="form-control" id="contactNumber2" name="contactNumber2" value = "'.htmlspecialchars($data["ContactNumber2"]).'" >';
        $contactNameInput3 = '<input type="text" class="form-control" id="contactPerson3" name="contactPerson3" value = "'.htmlspecialchars($data["ContactName3"]).'" >'; 
        $contactNumberInput3 = '<input type="text" class="form-control" id="contactNumber3" name="contactNumber3" value = "'.htmlspecialchars($data["ContactNumber3"]).'" >';        

		if ($_SERVER["REQUEST_METHOD"] == "POST") 
		{
            UpdateUser($sqlConnect, $UserId);
            mysqli_close($sqlConnect); 
            echo 
            '<script>
                alert("Successfully Updated Patient Account!");
                location="http://'.$localhost.'/homespital/view_user.php";
            </script>';  
        }

        function UpdateUser($sqlConnect, $userID)
        {
            $image = $imageName = $imageType = $query = "";
            $imageArray = array();
            
            $contactnumber = $_POST['countryCode'] . $_POST['personalNumber'];
            $email = $_POST['email'];
            $address = $_POST['address'];
            $contactNumber1 = $_POST['contactNumber1'];
            $contactName1 = $_POST['contactPerson1'];
            $contactNumber2 = $_POST['contactNumber2'];
            $contactName2 = $_POST['contactPerson2'];
            $contactNumber3 = $_POST['contactNumber3'];
            $contactName3 = $_POST['contactPerson3'];

            
            if($_FILES["image"]['size']==0)
            {                
                $query = "UPDATE patient_users SET ContactNumber = '$contactnumber', Address = '$address', ContactNumber1 = '$contactNumber1', ContactName1 = '$contactName1', ContactNumber2 = '$contactNumber2', ContactName2 = '$contactName2', ContactNumber3 = '$contactNumber3', ContactName3 = '$contactName3' WHERE UserID = '$UserId'";
            }
            else
            {
                $image = addslashes(file_get_contents($_FILES['image']['tmp_name']));
                $imageName = $_FILES['image']['name'];
                $imageType = $_FILES['image']['type'];
                $query = "UPDATE patient_users SET ContactNumber = '$contactnumber', Address = '$address', ContactNumber1 = '$contactNumber1', ContactName1 = '$contactName1', ContactNumber2 = '$contactNumber2', ContactName2 = '$contactName2', ContactNumber3 = '$contactNumber3', ContactName3 = '$contactName3', ImageName = '$imageName', ImageType = '$imageType', Image = '$image' WHERE UserID = '$UserId'";
            }

            $result = mysqli_query($sqlConnect,$query);
            if(!$result){
                die("Failed to connect: " . mysqli.error());
            }

            $emailQuery = "UPDATE users SET Email = '$email' WHERE UserID = '$UserId'";
            $result = mysqli_query($sqlConnect,$emailQuery);
            if(!$result){
                die("Failed to connect: " . mysqli.error());
            }

            $sql1 = "UPDATE `Web-Sync` SET LastSync = CURRENT_TIMESTAMP WHERE Name='users';";
	        $result = mysqli_query($sqlConnect,$sql1);
            if(!$result){
                die("Failed to connect: " . mysqli.error());
            }
	    
	        $sql2 = "UPDATE `Web-Sync` SET LastSync = CURRENT_TIMESTAMP WHERE Name='patient_users';";
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
                <div class="form-group">
                    <label for="firstname">First Name</label>
                    <div id="firstname" name="firstName"> <?php echo $data["FirstName"] ?></div>
                </div>
                <div class="form-group">
                    <label for="lastName">Last Name</label>
                    <div id="lastName" name="lastName"> <?php echo $data["LastName"] ?></div>
                </div>
                <div class="form-group">
                    <label for="middleInitial">MI</label>
                    <div id="middleInitial"name="middleInitial"> <?php echo $data["MI"] ?></div>
                </div>
                <div class="form-group">
                    <label for="birthdate">Birthdate</label>
                    <div id="birthdate" name="birthdate"> <?php echo $data["Birthdate"] ?></div>
                </div>
                <div class="form-group">
                    <label for="gender">Gender</label>
                    <div id="gender" name="gender"> <?php echo $data["Gender"] ?></div>
                </div>
                <div class="form-group">
                    <label for="bloodtype">Bloodtype</label>
                    <div id="bloodtype" name="bloodtype"> <?php echo $data["Bloodtype"] ?></div>
                </div>
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" accept-charset="utf-8" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="image">Profile Image: </label>
                        <input type="file" id="image" name="image" accept="image/png, image/gif, image/jpeg" >
                    </div>
                    <div class="form-group">
                        <label for="personalNumber">Contact Number </label>
                        <?php echo $contactNumberInput; ?>
                    </div>
                    <div class="form-group">
                        <label for="personalNumber">Email Address </label>
                        <?php echo $emailInput; ?>
                    </div>
                    <div class="form-group">
                        <label for="address">Home Address</label>
                        <?php echo $addressInput; ?>
                    </div>
                    <div class="form-group">
                        <label for="contactPerson1">Emergency Contact Name 1 </label>
                        <?php echo $contactNameInput1; ?>
                    </div> 
                    <div class="form-group">
                        <label for="contactNumber1">Emergency Contact Number 1 </label>
                        <?php echo $contactNumberInput1; ?>
                    </div>
                    <div class="form-group">
                        <label for="contactPerson2">Emergency Contact Name 2 </label>
                        <?php echo $contactNameInput2; ?>
                    </div>
                    <div class="form-group">
                        <label for="contactNumber2">Emergency Contact Number 2 </label>
                        <?php echo $contactNumberInput2; ?>
                    </div>
                    <div class="form-group">
                        <label for="contactPerson3">Emergency Contact Name 3 </label>
                        <?php echo $contactNameInput3; ?>
                    </div>
                    <div class="form-group">
                        <label for="contactNumber3">Emergency Contact Number 3 </label>
                        <?php echo $contactNumberInput3; ?>
                    </div>
                    <div>
                    </div>
                    <div>
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
