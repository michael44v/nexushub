<?php
//session_start();

$servername = "localhost";
$username = "htsuktdr_root";
$password = "victor47009A";
$dev = "htsuktdr_dev";

// Create connection
$conn = new mysqli($servername, $username, $password, $dev);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " .$conn->connect_error);
}
else {
    //echo "Connected successfully";

}
function filter($input) {
	$input = trim($input);
	$input = stripslashes($input);
	$input = htmlspecialchars($input);
	return $input;
  }
//echo "Connected successfully";

?>