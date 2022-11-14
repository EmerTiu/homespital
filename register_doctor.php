<?php
        session_start();
        //$localhost = "192.168.254.102"; //Home
        //$localhost = "192.168.1.13"; //Condo
	$localhost = "192.168.1.102"; //Router
		if ($_SERVER["REQUEST_METHOD"] == "POST") 
		{
			$entry = false;
			// if (empty($_POST["firstName"]) ) 
			// {
			// 	echo "error 1";
			// } 
            // if ( empty($_POST["lastName"])  ) 
			// {
			// 	echo "error 2";
			// } 
            // if (empty($_FILES['image']['name']) ) 
			// {
			// 	echo "error 3";
			// } 
            // if (empty($_POST["personalNumber"])) 
			// {
			// 	echo "error 4";
			// } 
            if (empty($_POST["firstName"]) || empty($_POST["lastName"]) || empty($_FILES['image']['tmp_name']) || empty($_POST["personalNumber"]) || empty($_POST["countryCode"]) ) 
			{
                echo "error";
				header("Location: http://localhost/thesis/dev-router/register_doctor.html"); 					
                exit();
			} 
            else
            {
                $sqlConnect = mysqli_connect("localhost","root","password");
                if(!$sqlConnect) {
                die();
                }
                //choose the database
                $selectDB = mysqli_select_db($sqlConnect,"homespital");
                if(!$selectDB) {
                die("Database connection failed!" .mysqli_error());
                }

                CreateNewUser($sqlConnect);
                session_destroy();
                mysqli_close($sqlConnect);
                header("Location: http://".$localhost."/thesis/dev-router/login.php"); 					
                exit();
               
            }
        }
        function CreateNewUser($sqlConnect)
        {
            $username = $_SESSION["register_username"];
            $email = $_SESSION["register_email"];
            $password = $_SESSION["register_password"];
            $query = "insert into users (Username, Email, Rights, Password) values ('$username', '$email', '2', '$password');";
            $result = mysqli_query($sqlConnect,$query);
            if(!$result){
                die("Failed to connect: " . mysqli.error());
            }

            $query = "select UserID from users where Username = '$username' AND Email = '$email';";
            $result = mysqli_query($sqlConnect,$query);
            if(!$result){
                die("Failed to connect: " . mysqli.error());
            }
            $array = mysqli_fetch_array($result);	
            $userID = $array['UserID'];
            
            
            CreateNewDoctor($sqlConnect, $userID); 
        }
        function CreateNewDoctor($sqlConnect, $userID)
        {
            $firstname = $_POST['firstName'];
            $lastname = $_POST['lastName'];
            $contactnumber = $_POST['countryCode'] . $_POST['personalNumber'];
            $image = addslashes(file_get_contents($_FILES['image']['tmp_name']));
	    $imageName = $_FILES['image']['name'];
	    $imageType = $_FILES['image']['type'];

            $query = "insert into doctor_users (UserID, FirstName, LastName, PhoneNumber, ImageName, ImageType, Image) values ('$userID', '$firstname', '$lastname', '$contactnumber', '$imageName', '$imageType', '$image');";
            $result = mysqli_query($sqlConnect,$query);
            if(!$result){
                die("Failed to connect: " . mysqli.error());
            }
	    
	    $sql2 = "UPDATE `Web-Sync` SET LastSync = CURRENT_TIMESTAMP WHERE Name='doctor_users';";
	    $result = mysqli_query($sqlConnect,$sql2);
            if(!$result){
                die("Failed to connect: " . mysqli.error());
            }
        }
?>
