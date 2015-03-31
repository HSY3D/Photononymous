<?php
header("location: http://cs.mcgill.ca/~hsyed2/organized/");
//indicate file name and path
$target_dir = "images/";
$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
$uploadOk = 1;
$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
//mySQL credentials
$servername = "mysql.cs.mcgill.ca";
$username = "dblade";
$password ="260503611";
$dbname = "2014fall307dblade";
$conn = new mysqli($servername, $username, $password, $dbname);
$lat = $_POST["lat"];
$lng = $_POST["lng"];

if($lat AND $lng)
{
    $sql = 'INSERT INTO Uploads (Path, Upvotes, Downvotes, Time, Latitude, Longitude)
        VALUES("'.$target_file.'","0","0","'.date(DATE_ISO8601).'",'.$lat.",".$lng.")";
}

else
{
    $sql = 'INSERT INTO Uploads (Path, Upvotes, Downvotes, Time)
        VALUES("'.$target_file.'","0","0","'.date(DATE_ISO8601).'")';
}
        
if($conn->connect_error)
{
  die("Connection failed: " . mysqli_connect_error());
}

$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);

// Check if image file is a actual image or fake image

if(isset($_POST["submit"])) {
    $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
    if($check !== false) {
        //TODO insert PATH, set default votes, time
         echo "File is an image - " . $check["mime"] . ".<br>";
        $uploadOk = 1;
    }
    else {
        echo "File is not an image.";
        $uploadOk = 0;
    }
}
// Check if file already exists

if (file_exists($target_file)) {
    echo "Sorry, file already exists.";
    $uploadOk = 0;
}
// Check file size
if ($_FILES["fileToUpload"]["size"] > 500000) {
    echo "Sorry, your file is too large.";
    $uploadOk = 0;
}
// Allow certain file formats
if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
&& $imageFileType != "gif" ) {
    echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
    $uploadOk = 0;
}
// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
    echo "Sorry, your file was not uploaded.";
// if everything is ok, try to upload file
} else {
	
    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
        echo "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";

        if($conn->query($sql) === TRUE)
        {
            echo "File successfully added to DB.";
            $uploadOk = 1;
        }
    } else {
        echo "Sorry, there was an error uploading your file.";
    }
}
?>