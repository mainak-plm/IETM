<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ietm"; // Replace with your actual database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// The ID of the user you're fetching
$user_id = 'id52'; // Example ID

// SQL query to fetch the 'name', 'email', and 'age' attributes of the user with id = 1
$sql = "SELECT name, dataSetRef FROM itemrevision WHERE id = ?";

// Prepare the SQL statement
$stmt = $conn->prepare($sql);

// Bind parameters (the type 'i' means an integer for user_id)
$stmt->bind_param("s", $user_id);

// Execute the query
$stmt->execute();

// Bind result variables
$stmt->bind_result($name, $val);

// Fetch the result
if ($stmt->fetch()) {
    // Output the values of the 'name', 'email', and 'age' columns
    echo "Name: " . htmlspecialchars($name) . "<br>";
    echo "dataSetRef: " . htmlspecialchars($val) . "<br>";
    
} else {
    echo "No user found with ID " . $user_id;
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>
