<?php
	$servername = "mysql.cs.mcgill.ca";
	$username = "dblade";
	$password ="260503611";
	$dbname = "2014fall307dblade";
	
	$conn = new mysqli($servername, $username, $password, $dbname); 
	if( !$conn )
	{
	  die( 'Could not connect: ' . mysqli_error( $conn ) );
	}

	$sql = "SELECT * FROM Uploads";
	$result = mysqli_query( $conn, $sql );

	if( !$result )
	{
	  echo 'Could not run query: ' . mysqli_error();
	  exit;
	}
	else
	{
	  $resArr = array();
	  while( $row = mysqli_fetch_assoc($result) ) 
	  {
	  	$resArr[] = $row; 
	  }
	  $jsonData = json_encode($resArr);
	  echo $jsonData;
	}

	mysqli_close( $conn );
?>
