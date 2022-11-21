<?php
        $localhost = "192.168.254.102"; //Home
        //$localhost = "192.168.1.11"; //Condo
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
			// if (empty($_POST["middleInitial"]) ) 
			// {
			// 	echo "error 3";
			// } 
			// if (empty($_POST["birthdate"])) 
			// {
			// 	echo "error 4";
			// } 
            if (empty($_POST["username"]) || empty($_POST["password"]) || empty($_POST["confirm"]) || empty($_POST["patientID"])) 
			{
                echo 
                    '<script>
                        alert("Missing Fields");
                        location="http://'.$localhost.'/homespital/register_guest.html";
                    </script>';
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
                $credentials = array($_POST["username"], $_POST["password"], $_POST["confirm"], $_POST["patientID"]);
                //var_dump($credentials);

                $verify = Verify_Input($credentials, $sqlConnect, $localhost);
                if($verify) CreateNewUser($sqlConnect, $credentials);
                mysqli_close($sqlConnect);
                echo 
                    '<script>
                        alert("Successfully Created Guest Account!");
                        location="http://'.$localhost.'/homespital/login.php";
                    </script>';
            }
        }
        function Verify_Input($credentials, $sqlConnect, $localhost)
        {
            if($credentials[1] != $credentials[2])
            {
                echo 
                    '<script>
                        alert("Passwords does not match ");
                        location="http://'.$localhost.'/homespital/register_guest.html";
                    </script>';
                    return false;
            }
            
            $query = "SELECT * FROM users WHERE Username = '$credentials[0]'";
            $result = mysqli_query($sqlConnect,$query);
                    
            $query2 = "SELECT * FROM caregiver_and_guest_users WHERE ConnectedID = '$credentials[3]' AND Rights='4'";
            $result2 = mysqli_query($sqlConnect,$query2);
            
            if(mysqli_num_rows($result) > 0)
            {   
                echo 
                    '<script>
                        alert("Username already exixts");
                        location="http://'.$localhost.'/homespital/register_guest.html";
                    </script>';
                    return false;
            }

            if(mysqli_num_rows($result2) > 0)
            {
                echo '<script type="text/javascript">
                    alert("Patient already has a Guest Account");
                    location="http://'.$localhost.'/homespital/register_guest.html";
                    </script>';  
                    return false;
            }
            return true;
        }

        function CreateNewUser($sqlConnect, $credentials)
        {
            $username = $credentials[0];
            $pass = $credentials[1];
            $connectedID = $credentials[3];
            $query = "insert into users (Username, Rights, Password) values ('$username', '4', '$pass');";
            $result = mysqli_query($sqlConnect,$query);
            if(!$result){
                die("Failed to connect: " . mysqli.error());
            }

            $query2 = "INSERT INTO caregiver_and_guest_users (UserID, Rights, ConnectedID) SELECT users.UserID, users.Rights, '$connectedID' FROM users WHERE users.Username = '$username'";
            $result = mysqli_query($sqlConnect,$query2);
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
