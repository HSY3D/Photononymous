<?php
$servername = "mysql.cs.mcgill.ca";
$username = "dblade";
$password ="260503611";
$dbname = "2014fall307dblade";
$conn = new mysqli($servername, $username, $password, $dbname);

if($conn->connect_error)
{
  echo "nooo";
}

$sql = "SELECT * FROM Uploads ORDER BY Upvotes DESC";
$uploadTable = mysqli_query($conn, $sql);
if($uploadTable->num_rows > 0){
	$jsons = array();
	$paths = array();
	while($row = $uploadTable->fetch_assoc()){
		if(in_array($row["Path"], $paths)){
			continue;
		}else{
			$paths[] = $row["Path"];
			$jsons[] = $row;
		}
	}
	echo json_encode($jsons);
}else{
	echo "";
}

?>