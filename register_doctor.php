<?php
        session_start();
        //$localhost = "192.168.254.102"; //Home
        //$localhost = "192.168.1.13"; //Condo
        $localhost = "192.168.1.11"; //Condo
		//$localhost = "192.168.1.102"; //Router
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
            if (empty($_POST["firstName"]) || empty($_POST["lastName"])) 
			{
                echo "error";
				header("Location: http://".$localhost."/homespital/register_doctor.html"); 					
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
                        alert("Successfully Created Doctor Account!");
                        location="http://'.$localhost.'/homespital/login.php";
                    </script>';               
            }
        }
        function CreateNewUser($sqlConnect)
        {
            $username = $_SESSION["user"];
            $email = $_POST["email"];
            $password = $_SESSION["password"];
            $query = "insert into users (Username, Email, Rights, Password) values ('$username', '$email', '2', '$password');";
            $result = mysqli_query($sqlConnect,$query);
            if(!$result){
                die("Failed to connect: " . mysqli.error());
            }
	    
            $sql2 = "UPDATE `Web-Sync` SET LastSync = CURRENT_TIMESTAMP WHERE Name='users';";
            $result = mysqli_query($sqlConnect,$sql2);
            if(!$result){
                die("Failed to connect: " . mysqli.error());
            }  

            CreateNewDoctor($sqlConnect, $username); 
        }
        function CreateNewDoctor($sqlConnect, $username)
        {
            $firstname = $_POST['firstName'];
            $lastname = $_POST['lastName'];
            $contactnumber = $_POST['countryCode'] . $_POST['personalNumber'];

            $query = "";
            if($_FILES["image"]['size']==0)
            {                
                $query = "INSERT INTO doctor_users (UserID, FirstName, LastName, PhoneNumber, ImageName, ImageType, Image) SELECT users.UserID, '$firstname', '$lastname', '$contactnumber', assets.ImageName, assets.ImageName, assets.Image FROM users, assets WHERE users.Username = '$username' AND assets.ImageName = 'blank.png'";
            }
            else
            {
                $image = addslashes(file_get_contents($_FILES['image']['tmp_name']));
                $imageName = $_FILES['image']['name'];
                $imageType = $_FILES['image']['type'];
                $query = "INSERT INTO doctor_users (UserID, FirstName, LastName, PhoneNumber, ImageName, ImageType, Image) SELECT users.UserID, '$firstname', '$lastname', '$contactnumber', '$imageName', '$imageType', '$image' FROM users WHERE users.Username = '$username'";
            }
            echo $query;
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
