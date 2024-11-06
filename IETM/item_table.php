<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Step 1: Specify the path to the XML file
$xmlFilePath = 'C:/xampp/htdocs/test_06 (1).xml'; // Ensure this path is correct

// Database credentials
$servername = "localhost";
$username = "root";  // Change to your database username
$password = "";      // Change to your database password
$dbname = "ietm"; // Change to your database name

// Step 2: Create a connection to the database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the file exists
if (!file_exists($xmlFilePath)) {
    die("The file does not exist at the specified path: $xmlFilePath");
}

// Load the XML file
$xml = simplexml_load_file($xmlFilePath);

// Check if loading was successful
if ($xml === false) {
    // Display error information
    echo "Error loading XML file: ";
    foreach(libxml_get_errors() as $error) {
        echo "<br>", $error->message;
    }
    exit;
}

// Step 3: Register the XML namespace to access the elements
$xml->registerXPathNamespace('plm', 'http://www.plmxml.org/Schemas/PLMXMLSchema');

// Step 4: Navigate to the <Occurrence> tags
$value = $xml->xpath('//plm:Requirement');

// Prepare the SQL statement for inserting data
$stmt = $conn->prepare("INSERT INTO item (id, name, catalogueId) VALUES (?, ?, ?)");

if ($value) {
    // Loop through each <Occurrence> and insert data into the database
    foreach ($value as $occurrence) {
        // Get the 'id' and 'name'attribute of <Occurrence>
        $occurrenceID = htmlspecialchars((string)$occurrence['id']);
        $occurrenceName = htmlspecialchars((string)$occurrence['name']);
        
        // Get the 'catalogueId' attribute 
        $catalogueId = htmlspecialchars((string)$occurrence['catalogueId']);
        $catalogueId = ltrim($catalogueId, '#');  // Trim the '#' from the beginning
        
        
        // Bind the parameters and execute the SQL query to insert the data
        $stmt->bind_param("sss", $occurrenceID, $occurrenceName, $catalogueId);
        $stmt->execute();
    }
    
    echo "Data successfully inserted into the database.";
} else {
    // If no occurrences are found, show a message
    echo "No Occurrence tags found in the XML file.";
}

// Close the prepared statement and the database connection
$stmt->close();
$conn->close();
?>
