<?php
	$photo = $_GET['photo'];
	$voteType = $_GET['voteType'];
	$servername = "mysql.cs.mcgill.ca";
	$username = "dblade";
	$password ="260503611";
	$dbname = "2014fall307dblade";

	$conn = new mysqli($servername, $username, $password, $dbname); //Create connection

	if( mysqli_connect_errno()  )
	{
	  die( 'Could not connect: ' . mysqli_connect_error() );
	}

	if( strcmp( $voteType, "up" ) == 0 )
	{
		$sql = "SELECT Upvotes FROM Uploads WHERE PhotoID=" . $photo;
		$result = mysqli_query( $conn, $sql );

		if( !result )
		{
		  echo 'Could not run query: ' . mysqli_error( $conn );
		  exit;
		}
		else
		{
			$count = mysqli_fetch_array($result, MYSQLI_NUM);
			$count[0] = $count[0] + 1;
		  	$sql2 = "UPDATE Uploads SET Upvotes=" . $count[0] . " WHERE PhotoID=" . $photo;
		  	$result2 = mysqli_query( $conn, $sql2 );
		  	if( !$result2 )
		  	{
		  		echo 'Could not run query: ' . mysqli_error( $conn );
		  		exit;
		  	}
		}
	}

	elseif( strcmp( $voteType, "down" ) == 0 )
	{
		$sql = "SELECT Downvotes FROM Uploads where PhotoID=" . $photo;
		$result = mysqli_query( $conn, $sql );

		if( !$result )
		{
		  echo 'Could not run query: ' . mysqli_error( $conn );
		  exit;
		}
		else
		{
			$count = mysqli_fetch_array($result, MYSQLI_NUM);
			$count[0] = $count[0] + 1;
		  	$sql2 = "UPDATE Uploads SET Downvotes=" . $count[0] . " WHERE PhotoID=" . $photo;
		  	$result2 = mysqli_query( $conn, $sql2 );
		  	if( !$result2 )
		  	{
		  		echo 'Could not run query: ' . mysqli_error( $conn);
		  		exit;
		  	}
		}
	}

	mysqli_close( $conn );
?>
