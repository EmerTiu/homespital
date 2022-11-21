<?php
        session_start();
        $localhost = "192.168.254.102"; //Home
//$localhost = "192.168.1.11"; //Condo
//$localhost = "192.168.1.102"; //Router
		if ($_SERVER["REQUEST_METHOD"] == "POST") 
		{
			$entry = false;
            if (empty($_POST["firstName"]) || empty($_POST["lastName"]) || empty($_POST["middleInitial"]) || 
            empty($_POST["birthdate"]) || empty($_POST["gender"]) || empty($_POST["bloodtype"])) 
			{
                echo "error";
				header("Location: http://".$localhost."/homespital/register_patient.html"); 					
                exit();
			} 
            else
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

                CreateNewUser($sqlConnect);
                session_destroy();
                mysqli_close($sqlConnect);
                echo 
                    '<script>
                        alert("Successfully Created Patient Account!");
                        location="http://'.$localhost.'/homespital/login.php";
                    </script>';
            }
        }
        function CreateNewUser($sqlConnect)
        {
            $username = $_SESSION["user"];
            $email = $_POST["email"];
            $password = $_SESSION["password"];
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
        }
        function CreateNewPatient($sqlConnect, $userID)
        {
            $image = $imageName = $imageType = $query = "";
            $imageArray = array();
            
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

            $box_id = "A00002";
            $vitals_id = "B00002";
            
            if($_FILES["image"]['size']==0)
            {                
                $query = "INSERT INTO patient_users (UserID, box_id, vitalsDevice_id, FirstName, LastName, MI, Birthdate, Gender, Bloodtype, ContactNumber, Address, ContactNumber1, ContactName1, ContactNumber2, ContactName2, ContactNumber3, ContactName3, ImageName, ImageType, Image) SELECT '$userID', '$box_id', '$vitals_id', '$firstname', '$lastname', '$mi', '$birthdate', '$gender', '$bloodtype', '$contactnumber', '$address', '$contactNumber1', '$contactName1', '$contactNumber2', '$contactName2', '$contactNumber3', '$contactName3', assets.ImageName, assets.ImageName, assets.Image FROM assets WHERE assets.ImageName = 'blank.png'";
            }
            else
            {
                $image = addslashes(file_get_contents($_FILES['image']['tmp_name']));
                $imageName = $_FILES['image']['name'];
                $imageType = $_FILES['image']['type'];
                $query = "INSERT INTO patient_users (UserID, box_id, vitalsDevice_id, FirstName, LastName, MI, Birthdate, Gender, Bloodtype, ContactNumber, Address, ContactNumber1, ContactName1, ContactNumber2, ContactName2, ContactNumber3, ContactName3, ImageName, ImageType, Image) VALUES ('$userID', '$box_id', '$vitals_id', '$firstname', '$lastname', '$mi', '$birthdate', '$gender', '$bloodtype', '$contactnumber', '$address', '$contactNumber1', '$contactName1', '$contactNumber2', '$contactName2', '$contactNumber3', '$contactName3', '$imageName', '$imageType', '$image')";
            }

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
