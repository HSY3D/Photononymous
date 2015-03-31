<?php
	header("Location: http://cs.mcgill.ca/~hsyed2/organized/");
	$msg = "Photo with id: ".$_POST["id"]." and path: ".$_POST["path"]." has been reported.";

	mail("dblader79@gmail.com", "REPORTED PHOTO", $msg);
?>