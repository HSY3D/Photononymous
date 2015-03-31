<?php
	$servername = "mysql.cs.mcgill.ca";
	$username = "dblade";
	$password ="260503611";
	$dbname = "2014fall307dblade";
	$conn = new mysqli($servername, $username, $password, $dbname);

	if($conn->connect_error)
	{
  		die("Connection failed: " . mysqli_connect_error());
	}

	$id = $_POST["id"];
	$sql = "UPDATE Uploads SET Downvotes = Downvotes + 1 WHERE PhotoID='".$id."'";
	$uploadTable = mysqli_query($conn, $sql);

	?>