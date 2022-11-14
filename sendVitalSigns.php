<?php
$serverName = "localhost";

$dbName = "homespital";
$tableName = "vitals_table";
$logsTableName = "vitals_table_logs";
$username = "root";
$password = "";

$apiKeyValue = "y8His941pHq";

$query = $apiKey = $pulseRate = $oxygenSat = $perfIndex = $bodyTemp = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $apiKey = test_input($_POST['api_key']);

    if ($apiKey == $apiKeyValue) {
        // Next step here: Just give the model ID and then the userId will be searched using this ID
        // So this userId will be removed later on
        $userId = 'cc4b3e2a-0507-11ed-970b-7cd30a809727';
        $bodyTemp = test_input($_POST['BodyTemp']);
        $oxygenSat = test_input($_POST['OxygenSat']);
        $pulseRate = test_input($_POST['PulseRate']);
        $perfIndex = test_input($_POST['PerfusionIndex']);
        //$btnStatus = test_input($_POST['BtnStatus']);
        $vitalsID = test_input($_POST['vitals_id']);

        // Creating connection
        $conn = new mysqli($serverName, $username, $password, $dbName);
        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Get the current datetime from the local server
        $local_datetime = mysqli_fetch_row($conn->query("select CURRENT_TIMESTAMP()"))[0];
        // Get the last datetime of the update of vital signs
        // 2 is the column number of UpdatedAt in the vitals_table
        //$lastUpdatedAtResults = $conn->query("select * from vitals_table where userid = 'cc4b3e2a-0507-11ed-970b-7cd30a809727' order by updatedAt DESC LIMIT 1");
        //$lastUpdatedAtRows = mysqli_fetch_row($lastUpdatedAtResults);
        //$lastUpdatedAt = $lastUpdatedAtRows[2];
        // Get the difference of the current datetime and the last datetime (updatedAt)
        //$interval = (array) date_diff(date_create($lastUpdatedAt), date_create($local_datetime));
        //$minutes = $interval['i'];      # MODIFIABLE: Must be in terms of hours in the final test

        $query = "INSERT INTO $tableName (UserId, BodyTemp, OxygenSat, PulseRate, PerfusionIndex) VALUES ('$userId', $bodyTemp, $oxygenSat, $pulseRate, $perfIndex)";

        if ($conn->query($query) == TRUE) {
            $conn->query("INSERT INTO $logsTableName (Logs) VALUES ('New record created successfully.')");
            $sql_update = "UPDATE `Web-Sync` SET LastSync = current_timestamp WHERE Name = 'vitals_table'";
            $conn->query($sql_update);
        } else {
            $conn->query("INSERT INTO $logsTableName (Logs) VALUES ('No new record created.')");
            //let err = $conn->error;
            //$conn->query("INSERT INTO $logsTableName (Logs) VALUES ('$err')");
        }
        $conn->close();
    } else {
        echo "Wrong API Key provided";
    }
} else {
    echo "No data posted with HTTP POST.";
}

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

?>
