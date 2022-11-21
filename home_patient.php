<!doctype html>
<?php
// Start the session
session_start();
?>
<html lang="en">
  <head>
  
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=yes">

    <!-- My CSS-->
    <link rel="stylesheet" href="style.css">

    <!-- Bootstrap CSS -->
    <!-- <link rel="stylesheet" href="../node_modules/bootstrap/dist/css/bootstrap.min.css"> -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <title>Home Patient Homespital</title>
	
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
	
	<!-- PHP script -->
	<?php
		$localhost = "192.168.254.102"; //Home
		//$localhost = "192.168.1.11"; //Condo
		//$localhost = "192.168.1.102"; //Router
		//Connecting to mysql 
		$sqlConnect = mysqli_connect("localhost", "root","");
		
		if(!$sqlConnect){
		die("Error in accessing the database!". mysqli_error());
		}
		
		//Connecting to database 
		$selectDB = mysqli_select_db($sqlConnect,"homespital");
		
		if(!$selectDB){
		die("Failed to connect to the databse" . mysqli_error());
		}
		
		$UserId = $_SESSION['userid'];
		$Rights = $_SESSION['rights'];
		if($Rights==2)$doctorName = $_SESSION['name'];

		//End session
		function endSession(){
			unset($_SESSION['userid']);
			session_destroy();
		}
				
		//Retirieving user credentials
		$getData = mysqli_query($sqlConnect, "Select * from patient_users where userid='$UserId';");
		if(!$getData){
		die("Failed to connect: " . mysqli.error());
		}
		$SR = mysqli_fetch_array($getData);
		$firstName = $SR['FirstName'];
		$lastName = $SR['LastName'];
		$gender = $SR['Gender'];
		$mi = $SR['MI'];
		$birthdate = $SR['Birthdate'];
		$age = (date_diff(date_create($birthdate), date_create(date("Y-m-d")))) -> format('%y');
		$bloodtype = $SR['Bloodtype'];
		$address = $SR['Address'];
		$contactNumber1 = $SR['ContactNumber1'];
		$contactName1 = $SR['ContactName1'];
		$contactNumber2 = $SR['ContactNumber2'];
		$contactName2 = $SR['ContactName2'];
		$contactNumber3 = $SR['ContactNumber3'];
		$contactName3 = $SR['ContactName3'];
		$image = base64_encode($SR['Image']);
		
		//Retrieving Medicine Box Schedule Database
		$get_sched = mysqli_query($sqlConnect,"select * from medicine_intake_schedule where userid = '$UserId';"); 
		$get_check = mysqli_query($sqlConnect,"select * from medicine_intake_status where userid = '$UserId';"); 

		if(!$get_sched || !$get_check){
		die("Failed to connect: " . mysqli.error());
		}
		
		$scheduleTimeTable = mysqli_fetch_array($get_sched);		
		$scheduleStatusTable = mysqli_fetch_array($get_check);

		$scheduleTime = array();
		$scheduleStatus = array();
		$day_index = 1;
		for($i = 0; $i<7; $i++)
		{
			$scheduleTime[$i] = array();
			$scheduleStatus[$i] = array();

			for($j = 0; $j<4; $j++)
			{
				$scheduleTime[$i][$j] = $scheduleTimeTable[$day_index];	
				$scheduleStatus[$i][$j] = $scheduleStatusTable[$day_index++];					
			}
		}
		$day_index=0;
		
		
		//Retrieving Vitals Database
		$get_vitals = mysqli_query($sqlConnect,"select * from vitals_table where userid = '$UserId' order by DateCreated desc");
		if(!$get_vitals){
		die("Failed to connect: " . mysqli.error());
		}
		
		$temp = array();
		$oxygen = array();
		$pulseRate = array();
		$perfusionIndex = array();
		$dateTime = array();
		
		$vitalsCount=0;
		while($SR = mysqli_fetch_array($get_vitals)){
			$temp[] = $SR['BodyTemp'];
			$oxygen[] = $SR['OxygenSat'];
			$pulseRate[] = $SR['PulseRate'];
			$perfusionIndex[] = $SR['PerfusionIndex'];
			$dateTime[] = $SR['DateCreated'];
			$vitalsCount++;
		}
		
		//Initializing vitals array for canvasJS	
		$tempGraph = array();
		$oxygenGraph = array();
		$pulseRateGraph = array();
		$perfusionIndexGraph = array();
		($vitalsCount > 10) ? $graphIndex = 9 : $graphIndex = $vitalsCount-1;
		$index = 0; 		
		for($i = $graphIndex; $i>=0; $i--)
		{	
			$tempGraph[$index] = array("y" => $temp[$i], "label" => $dateTime[$i]);
			$oxygenGraph[$index] = array("y" => $oxygen[$i], "label" => $dateTime[$i]);
			$pulseRateGraph[$index] = array("y" => $pulseRate[$i], "label" => $dateTime[$i]);
			$perfusionIndexGraph[$index++] = array("y" => $perfusionIndex[$i], "label" => $dateTime[$i]);
		}
		
		if(isset($_GET['doctorLogout']))
		{
			unset($_SESSION['userid']);
			header("Location: http://".$localhost."/homespital/main_doctor.php");
			exit();
		}

		if(isset($_GET['userLogout']))
		{
			session_destroy();
			header("Location: http://".$localhost."/homespital/login.php");
			exit();
		}
		//echo $image . "\n 90 \n";
		//echo base64_encode($SR['Image']);
		//echo base64_decode($image);
	?>
	
	<!--CanvasJS Vitals Chart Generation -->
	
	<script>		
		let maxLength = 32;
		let message = -1;

		window.onload = function () {
		
		var chart1 = new CanvasJS.Chart("chartContainer", {
			
			theme: "light2",
			title:{
				text: "Body Temperature",
				fontSize: 20,
			},
			axisX: {
				title: "Time",
			},
			axisY: {
				title: "Temperature",
				stripLines: [
				{
						value: 34.0,
						label: "Low Body Temperature"
				},
				{
						value: 38.5,
						label: "Fever"
				},
				]
			},
			
			data: [{        
				type: "line",
				indexLabelFontSize: 12,
				axisY: {
					title: "Temperature (in celcius)"
				},
				axisX: {
					title: "Time"
				},
				dataPoints: <?php echo json_encode($tempGraph, JSON_NUMERIC_CHECK); ?>
			}]
		});
		//console.log(<?php echo json_encode($tempGraph, JSON_NUMERIC_CHECK); ?>);
		
		
		
		var chart2 = new CanvasJS.Chart("chartContainer2", {
			
			theme: "light2",
			title:{
				text: "Oxygen Saturation",
				fontSize: 20,
			},
			axisX: {
				title: "Time",
			},
			axisY: {
				title: "Saturation",
				stripLines: [
				{
						value: 95,
						label: "Low Oxygen Saturation"
				},
				]
			},
			data: [{        
				type: "line",
				indexLabelFontSize: 12,
				dataPoints: <?php echo json_encode($oxygenGraph, JSON_NUMERIC_CHECK); ?>
			}]
		});
		
		var chart3 = new CanvasJS.Chart("chartContainer3", {
			
			theme: "light2",
			title:{
				text: "Pulse Rate ",
				fontSize: 20,
			},
			axisX: {
				title: "Time",
			},
			axisY: {
				title: "Pulse Rate",
				stripLines: [
				{
						value: 60,
						label: "Slow Pulse Rate"
				},
				{
						value: 105,
						label: "Fast Pulse Rate"
				},
				]
			},
			data: [{        
				type: "line",
				indexLabelFontSize: 12,
				dataPoints: <?php echo json_encode($pulseRateGraph, JSON_NUMERIC_CHECK); ?>
			}]
		});

		var chart4 = new CanvasJS.Chart("chartContainer4", {
			
			theme: "light2",
			title:{
				text: "Perfusion Index ",
				fontSize: 20,
			},
			axisX: {
				title: "Time",
			},
			axisY: {
				title: "Index",
			},
			data: [{        
				type: "line",
				indexLabelFontSize: 12,
				dataPoints: <?php echo json_encode($perfusionIndexGraph, JSON_NUMERIC_CHECK); ?>
			}]
		});

		chart1.render();
		chart2.render();
		chart3.render();
		chart4.render();

		}
		
		function assistanceMessage()
		{ 
			
			message = window.prompt("Enter Message (Max " + maxLength + " characters in length):");
			console.log(message.length);
			if (message == 1 || (message != null && message.length > maxLength)) {
				assistanceMessage();
			}
			
			if(message != null)
			{
				$.ajaxSetup({timeout:1000});
				assistanceButton(message);
			}
			
			window.onload = GenerateCanvas();
		}

		function assistanceButton(message)
        {
            val = '1';
            MessageVar = message;
            NodeMCU = "http://" + "192.168.1.120" + ":80/";
            $.get( NodeMCU, {state: val , lcd: MessageVar})    ;
            {Connection: close};
        }

		function emergencyButton()
        {
			$.ajaxSetup({timeout:1000});
            val = '2';
            MessageVar = "";
            NodeMCU = "http://" + "192.168.1.120" + ":80/";
            $.get( NodeMCU, {state: val , lcd: MessageVar})    ;
            {Connection: close};
        }
	</script>
  </head>

  <body>
	<!-- Top Panel -->
	<div class="card home-tab-card">
		<div class="menu row" >
		
			<div class="col-sm-4" style="text-align:left">Homespital</div>
			<div class="col-sm-4" style="text-align:center">Hello, 
				<?php 
					switch($Rights)
					{
						case 1: 
							echo ($gender=='M') ? "Mr. ". $lastName : "Ms. ". $lastName;
							break;
						case 2: 
							echo "Dr. ". $doctorName;
							break;
						case 3:
							echo "Caregiver";
							break;
						default:
							echo "Guest";
					}
				?> 
			</div>
			<div class="col-sm-4 row" style="text-align:right; ">
				<div class="col-9" style="text-align:right; ">
					<?php
						//header("Location: http://".$localhost."/homespital/main_doctor.php");  href="http://"'.$localhost.'"/homespital/view_user.php"
						if($Rights != 2) echo '<a href="view_user.php"> <img class="center rounded float-right"  src="assets/person-circle.png"  style="height:50px; width:50px"> </img> </a>'
					?>
					
				</div>
				<div class="col-3" style="text-align:right; ">
					<?php 
						if($Rights==2)
						{
							echo '<a href="home_patient.php?doctorLogout=true" style="color:#FFFFFF; text-decoration: none;" >Back</a>';
						}
						else
						{
							echo '<a href="home_patient.php?userLogout=true" style="color:#FFFFFF; text-decoration: none;" >Logout</a>';	
						}
					?>	
				</div>
					
			</div>
		</div>
	</div>
	  
	<!-- Body Panel -->
	<div class="body row">
		<!-- left panel -->
		<div class="left-body col-xs-12 col-sm-12 col-md-12 col-lg-6" style="border-radius: 10px;"> 
			
			<!-- Title -->
			<div style="background-color:#006b4a; border-radius: 5px; font-size: 24px; color:white"> Patient's Vitals </div>

			<!-- Vitals graph -->	
			<div class = "table-wrapper-scroll-y my-custom-scrollbar col-lg-12" > 
				<table class="table">
					<tr><div class="vitalsgraph" id="chartContainer" style="height: 100%; width: 100%"></div></tr>
					<tr><div class="vitalsgraph" id="chartContainer2" style="height: 100%; width: 100%"></div></tr>
					<tr><div class="vitalsgraph" id="chartContainer3" style="height: 100%; width: 100%"></div></tr>
					<tr><div class="vitalsgraph" id="chartContainer4" style="height: 100%; width: 100%"></div></tr>
				</table>
			</div>		
			
							
			<!--Vitals table-->
			<div class = "table-responsive table-condensed d-flex justify-content-center table-wrapper-scroll-y my-custom-scrollbar col-lg-12" > 
				<table class="table ">
					<thead>
					  <tr>
						<th>Body Temperature (C)</th>
						<th>Oxygen Saturation</th>
						<th>Pulse Rate</th>
						<th>Perfusion Index</th>
						<th>Date Created</th>
					  </tr>
					</thead>
					<tbody>
						<?php
							for($i=0;$i<$vitalsCount;$i++)
							{?>
								<tr>
									<td><?php echo $temp[$i]; ?> </td>
									<td><?php echo $oxygen[$i]; ?> </td>
									<td><?php echo $pulseRate[$i]; ?> </td>
									<td><?php echo $perfusionIndex[$i]; ?> </td>
									<td><?php echo $dateTime[$i]; ?> </td>
								</tr><?php
							}
						?>
					</tbody>
				</table>
			</div>
			
			<div class="body row"> </div>
		</div>
		
		<!-- right panel -->
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-6" >
			<div style="background-color:#006b4a; border-radius: 5px; font-size: 24px; color:white"> Medicine Box Schedule </div>
			
			<!-- Schedule Panel -->
			<div style="background-color:#ffffff; height:500px">  
				<form method="post" action="sched.php">			
					<!-- <div class = "row-12  d-flex justify-content-center table-wrapper-scroll-y my-custom-schedule col-lg-12" style="height: 400px;" >  -->
					<div class = "row-12  d-flex table-wrapper-scroll-y my-custom-schedule col-lg-12" style="height: 400px;" > 
						<table class="col-12 table ">
							<thead>
							<tr>
								<th>Sun</th>
								<th>Mon</th>
								<th>Tues</th>
								<th>Wed</th>
								<th>Thurs</th>
								<th>Fri</th>
								<th>Sat</th>
							</tr>
							</thead>
							<tbody>	
								<?php
								for($i=0;$i<4;$i++)
								{ ?>
								<tr>
									<?php 
										for($j=0;$j<7;$j++)
										{
											//Schedule Table With Update Enabled
											if($Rights == 3)
											{
												?><td><input <?php echo ($scheduleStatus[$j][$i]==-1) ? "style = 'background-color:#fc6262'" : ""  ?> 
												<?php echo ($scheduleStatus[$j][$i]==1) ? "style = 'background-color:#62fca2'" : ""  ?> 
												type="time" name="Time[<?php echo $i; ?>][<?php echo $j; ?>]"  value="<?php echo $scheduleTime[$j][$i]; ?>"></td><?php
											}

											//Schedule Table Without Update Enabled
											else
											{
												?><td><div <?php echo ($scheduleStatus[$j][$i]==-1) ? "style = 'background-color:#fc6262'" : ""  ?> 
												<?php echo ($scheduleStatus[$j][$i]==1) ? "style = 'background-color:#62fca2'" : ""  ?> >
												<?php echo $scheduleTime[$j][$i]; ?> </div></td> <?php 
											}
										} 
									?>		
								</tr><?php
								} ?>
							</tbody>
						</table>			
					</div>  
					<?php
						if($Rights==3) 
						{
							echo '<div class="button-center">';
							echo  '<button type="submit" class="btn btn-login" value="Submit" style="">Update</button>';
							echo '</div>' ;
						}						
					?>
				</form>
			</div>
			<div class="body row"> </div>
			<!-- Notice Buttons Panel -->
			<div class="notify-box" style="background-color:white; border-radius: 10px;"> 
				<div style="background-color:#006b4a; border-radius: 5px; font-size: 24px; color:white"><?php echo ($Rights==1) ? "Notify" : "Patient's Information"; ?></div>
				<?php
					if($Rights==1)
					{ ?>
						<div class="body row"> </div>
						<div class= "notice d-flex justify-content-center" style= "text-align = center">				
							<img class="center"  src="assets/assist.png" onclick = assistanceMessage() > </img>    
							&nbsp;&nbsp;&nbsp;&nbsp;
							<img class="center" src="assets/emergency.png" onclick = emergencyButton()> </img>
						</div>
						<div class="body row"> </div>
					<?php }
					else
					{ ?>
						<div class= "body row">
							<!-- class="col-xs-12 col-sm-12 col-md-12 col-lg-12" -->
							<img src="data:image/png;base64,<?php echo $image; ?>" style="height: 300px; width:300px; display: block; margin-left: auto; margin-right: auto;" />
							<div class="table-responsive table-condensed col-xs-12 col-sm-12 col-md-12 col-lg-12" style="text-align:left;"> 
								<table class = "table" style="text-align:left;">
									<thead>
										<tr>
											<th>Last Name</th>
											<th>First Name</th>
											<th>M.I.</th>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td><?php echo $lastName; ?></td>
											<td><?php echo $firstName; ?></td>
											<td><?php echo $mi; ?></td>
										</tr>
									</tbody>
									<thead>
										<tr>
											<th>Age</th>
											<th>Birthdate</th>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td><?php echo $age; ?></td>
											<td><?php echo $birthdate; ?></td>
										</tr>
									</tbody>
									<thead>
										<tr>
											<th>Gender</th>
											<th>Blood Type</th>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td><?php echo $gender; ?></td>
											<td><?php echo $bloodtype; ?></td>
										</tr>
									</tbody>
									<thead>
										<tr>
											<th>Address</th>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td><?php echo $address; ?></td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
						<table class = "table table-condensed" style="text-align:left;">
							<thead>
								<tr>
									<th>Emergency Contacts</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td><?php echo $contactName1; ?> - <?php echo $contactNumber1; ?></td>
								</tr>
								<tr>
									<td><?php echo $contactName2; ?> - <?php echo $contactNumber2; ?></td>
								</tr>
								<tr>
									<td><?php echo $contactName3; ?> - <?php echo $contactNumber3; ?></td>
								</tr>
							</tbody>
						</table>
					<?php }
				?>
				
			</div>
		</div>
	</div>
	<script>
		
	</script>
    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
	<!--<script src="../scripts/jquery.min.js"></script> -->	
	<!-- <script src="../scripts/canvasjs.min.js"> </script> -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
	<script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
  </body>
</html>

<?php

mysqli_close($sqlConnect);
?>
