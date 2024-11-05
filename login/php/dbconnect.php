<?php
$servername = "localhost";
$database = "ietm";
$username = "root";
$password = "";
 
// Create connection
 
$conn = mysqli_connect($servername, $username, $password, $database);
 
// Check connection
 
if (!$conn) {
 
    die("Connection failed with MySQL Database " . mysqli_connect_error());
 
}
echo "Connected successfully with MySQL Database";
mysqli_close($conn);
?>