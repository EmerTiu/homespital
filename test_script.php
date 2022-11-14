<html>
    <head>
    <meta http-equiv="Content_Type" content="text/html; charset=iso-8859-1">
    </head>

    <body>
        <form name ="form1" action="" method="post" enctype="multipart/form-data">
            <table>
                <tr>
                <td> Select File</td>
                <td><input type = "file" name = "f1"></td>
                </tr>

                <tr>
                    <td><input type = "submit" name="submit1" value="upload"></td>
                    <td><input type = "submit" name="submit2" value="display"></td>
                    <td><input type = "submit" name="submit3" value="clients"></td>
                    <td><input type = "submit" name="submit4" value="display clients"></td>
                </tr>
            </table>
        </form>

        <?php
        $sqlConnect = mysqli_connect('localhost','root','');
		if(!$sqlConnect) {
		 die();
		}

		$selectDB = mysqli_select_db($sqlConnect,'homespital');
		if(!$selectDB) {
		 die("Database connection failed!" .mysqli_error());
		}

        //inserting doctor image 
        if(isset($_POST["submit1"]))
        {
            $image = addslashes(file_get_contents($_FILES['f1']['tmp_name']));
            //$query = "update doctor_users set image = '$image' where userid = 'f1ef91b4-0585-11ed-970b-7cd30a809727'";
            //mysqli_query($sqlConnect, $query);
            echo $image;
        }

        //displaying doctor image
        if(isset($_POST["submit2"]))
        {
            $query = "select * from doctor_users where userid = 'f1ef91b4-0585-11ed-970b-7cd30a809727'";
            $res = mysqli_query($sqlConnect, $query);
            echo "<table>";
            echo "<tr>";
            while($row=mysqli_fetch_array($res))
            {
                echo "<td>";
                echo '<img src="data:image/png;base64,'.base64_encode($row['Image']).'" />';
                echo "</td>";
            }
            echo "</tr>";
            echo "</table>";
        }
        
        //inserting doctor clientlist 
        if(isset($_POST["submit3"]))
        {        
            $clientList = array('cc4b3e2a-0507-11ed-970b-7cd30a809727', '18407193-0586-11ed-970b-7cd30a809727');
            $queryList = json_encode($clientList);
            echo $queryList;
            $query = "update doctor_users set clientlist = '$queryList' where userid = 'f1ef91b4-0585-11ed-970b-7cd30a809727';";
            mysqli_query($sqlConnect, $query);
        }

        //displaying doctor clientlist 
        if(isset($_POST["submit4"]))
        {
            $query = "select * from doctor_users where userid = 'f1ef91b4-0585-11ed-970b-7cd30a809727'";
            $res = mysqli_query($sqlConnect, $query);
            echo "<tr>";
            if(!$res) {echo "error";}
            while($row=mysqli_fetch_array($res))
            {
                $newList = json_decode($row['ClientList'], true);
                echo "<td>";
                echo $newList[0];
                echo "</td>";
            }
        }
        ?>
    </body>
</html>
