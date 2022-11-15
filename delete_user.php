<?php
    $localhost = "192.168.1.9";
    //echo $_POST['Username'];

    $sqlConnect = mysqli_connect("localhost","root","");
    if(!$sqlConnect) {
    die();
    }
    //choose the database
    $selectDB = mysqli_select_db($sqlConnect,"homespital");
    if(!$selectDB) {
    die("Database connection failed!" .mysqli_error());
    }

    //Update to delete table
    $query = "INSERT INTO `to_delete`(`id`, `UserID`, `Username`) VALUES ('','".$_POST['UserID']."','".$_POST['Username']."')";
    $result = mysqli_query($sqlConnect,$query);

    //Delete user account
    $query = "DELETE FROM `users` WHERE Username = '".$_POST['Username']."'";
    $result = mysqli_query($sqlConnect,$query);
    header("Location: http://".$localhost."/homespital/admin_dashboard.php"); 					
    exit();
?>
