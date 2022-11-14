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
			// if (empty($_POST["middleInitial"]) ) 
			// {
			// 	echo "error 3";
			// } 
			// if (empty($_POST["birthdate"])) 
			// {
			// 	echo "error 4";
			// } 
			// if (empty($_POST["gender"])) 
			// {
			// 	echo "error 5";
			// } 
			// if (empty($_POST["bloodtype"])) 
			// {
			// 	echo "error 6";
			// } 
			// if ( empty($_FILES['image']['name']) ) 
			// {
			// 	echo "error 7";
			// } 
			// if (empty($_POST["personalNumber"])) 
			// {
			// 	echo "error 8";
			// } 
			// if (empty($_POST["address"])) 
			// {
			// 	echo "error 9";
			// } 
			// if ( empty($_POST["caretakerUser"])) 
			// {
			// 	echo "error 10";
			// } 
			// if (empty($_POST["caretakerEmail"])) 
			// {
			// 	echo "error 11";
			// } 
			// if (empty($_POST["caretakerPassword"])) 
			// {
			// 	echo "error 12";
			// } 
			// if (empty($_POST["guestUser"])) 
			// {
			// 	echo "error 13";
			// } 
			// if (empty($_POST["guestEmail"])) 
			// {
			// 	echo "error 14";
			// } 
			// if (empty($_POST["guestPassword"]) ) 
			// {
			// 	echo "error 15";
			// } 
            if (empty($_POST["firstName"]) || empty($_POST["lastName"]) || empty($_POST["middleInitial"]) || 
            empty($_POST["birthdate"]) || empty($_POST["gender"]) || empty($_POST["bloodtype"])|| 
            empty($_FILES['image']['name']) || empty($_POST["personalNumber"]) || empty($_POST["address"])||
            empty($_POST["caretakerUser"]) || empty($_POST["caretakerEmail"]) || empty($_POST["caretakerPassword"])||
            empty($_POST["guestUser"]) || empty($_POST["guestEmail"]) || empty($_POST["guestPassword"])
            || empty($_POST["countryCode"]) || empty($_POST["gender"]) 
            || empty($_POST["emergencyPerson1"]) || empty($_POST["emergencyNumber1"]) ) 
			{
                echo "error";
				header("Location: http://".$localhost."/thesis/dev-router/register_patient.html"); 					
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
            $query = "insert into users (Username, Email, Rights, Password) values ('$username', '$email', '1', '$password');";
            $result = mysqli_query($sqlConnect,$query);
            if(!$result){
                die("Failed to connect: " . mysqli.error());
            }
	    
	    $sql2 = "UPDATE `Web-Sync` SET LastSync = CURRENT_TIMESTAMP WHERE Name='users';";
	    $result = mysqli_query($sqlConnect,$sql2);
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
            
            
            CreateNewPatient($sqlConnect, $userID);            
            CreateMedicineTable($sqlConnect, $userID);
            CreateNewCaretaker($sqlConnect, $userID);
            CreateNewGuest($sqlConnect, $userID);
        }
        function CreateNewPatient($sqlConnect, $userID)
        {
            $firstname = $_POST['firstName'];
            $lastname = $_POST['lastName'];
            $mi = $_POST['middleInitial'];
            $birthdate = $_POST['birthdate'];
            $gender = $_POST['gender'];
            $bloodtype = $_POST['bloodtype'];
            $contactnumber = $_POST['countryCode'] . $_POST['personalNumber'];
            $address = $_POST['address'];
            $contactNumber1 = $_POST['emergencyNumber1'];
            $contactName1 = $_POST['emergencyPerson1'];
            $contactNumber2 = $_POST['emergencyNumber2'];
            $contactName2 = $_POST['emergencyPerson2'];
            $contactNumber3 = $_POST['emergencyNumber3'];
            $contactName3 = $_POST['emergencyPerson3'];
            $image = addslashes(file_get_contents($_FILES['image']['tmp_name']));
	    $imageName = $_FILES['image']['name'];
	    $imageType = $_FILES['image']['type'];
            
            $query = "insert into patient_users (UserID, FirstName, LastName, MI, Birthdate, Gender, Bloodtype, ContactNumber, Address, ContactNumber1, ContactName1, ContactNumber2, ContactName2, ContactNumber3, ContactName3, Image) values ('$userID', '$firstname', '$lastname', '$mi', '$birthdate', '$gender', '$bloodtype', '$contactnumber', '$address', '$contactNumber1', '$contactName1', '$contactNumber2', '$contactName2', '$contactNumber3', '$contactName3', '$image');";
            $result = mysqli_query($sqlConnect,$query);
            if(!$result){
                die("Failed to connect: " . mysqli.error());
            }
	    
	    $sql2 = "UPDATE `Web-Sync` SET LastSync = CURRENT_TIMESTAMP WHERE Name='patient_users';";
	    $result = mysqli_query($sqlConnect,$sql2);
            if(!$result){
                die("Failed to connect: " . mysqli.error());
            }
        }
        function CreateNewCaretaker($sqlConnect, $userID)
        {
            $username = $_POST['caretakerUser'];
            $email = $_POST['caretakerEmail'];
            $pass = $_POST['caretakerPassword'];
            $query = "insert into users ( Username, Email, Rights, Password) values ('$username', '$email', 3, '$pass');";
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
            $caretakerID = $array['UserID'];

            $query2 = "insert into caretaker_and_guest_users (UserID, ConnectedID) values ('$caretakerID', '$userID');";
            $result = mysqli_query($sqlConnect,$query2);
            if(!$result){
                die("Failed to connect: " . mysqli.error());
            }
	    
	    $sql2 = "UPDATE `Web-Sync` SET LastSync = CURRENT_TIMESTAMP WHERE Name='caretaker_and_guest_users';";
	    $result = mysqli_query($sqlConnect,$sql2);
            if(!$result){
                die("Failed to connect: " . mysqli.error());
            }
        }
        function CreateNewGuest($sqlConnect, $userID)
        {
            $username = $_POST['guestUser'];
            $email = $_POST['guestEmail'];
            $pass = $_POST['guestPassword'];
            $query = "insert into users (Username, Email, Rights, Password) values ('$username', '$email', 4, '$pass');";
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
            $guestID = $array['UserID'];

            $query2 = "insert into caretaker_and_guest_users (UserID, ConnectedID) values ('$guestID', '$userID')";
            $result = mysqli_query($sqlConnect,$query2);
            if(!$result){
                die("Failed to connect: " . mysqli.error());
            }
	    
	    $sql2 = "UPDATE `Web-Sync` SET LastSync = CURRENT_TIMESTAMP WHERE Name='caretaker_and_guest_users';";
	    $result = mysqli_query($sqlConnect,$sql2);
            if(!$result){
                die("Failed to connect: " . mysqli.error());
            }
        }	
        function CreateMedicineTable($sqlConnect, $userID)
        {
            $query = "insert into medicine_intake_status (UserID) values ('$userID');";
            $result = mysqli_query($sqlConnect,$query);
            if(!$result){
                die("Failed to connect: " . mysqli.error());
            }
	    
	    
	    $sql2 = "UPDATE `Web-Sync` SET LastSync = CURRENT_TIMESTAMP WHERE Name='medicine_intake_status';";
	    $result = mysqli_query($sqlConnect,$sql2);
            if(!$result){
                die("Failed to connect: " . mysqli.error());
            }

            $query = "insert into medicine_intake_schedule (UserID) values ('$userID');";
            $result = mysqli_query($sqlConnect,$query);
            if(!$result){
                die("Failed to connect: " . mysqli.error());
            }
	    
	    
	    $sql2 = "UPDATE `Web-Sync` SET LastSync = CURRENT_TIMESTAMP WHERE Name='medicine_intake_schedule';";
	    $result = mysqli_query($sqlConnect,$sql2);
            if(!$result){
                die("Failed to connect: " . mysqli.error());
            }
        }	
?>
