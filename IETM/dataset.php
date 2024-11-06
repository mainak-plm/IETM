<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Step 1: Specify the path to the XML file
$xmlFilePath = 'C:/xampp/htdocs/test_06 (1).xml'; // Ensure this path is correct

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
    foreach (libxml_get_errors() as $error) {
        echo "<br>", $error->message;
    }
    exit;
}

// Step 2: Register the XML namespace to access the elements
$xml->registerXPathNamespace('plm', 'http://www.plmxml.org/Schemas/PLMXMLSchema');

// Step 3: Connect to MySQL Database using mysqli
$servername = "localhost"; // Your MySQL server
$username = "root";        // Your MySQL username
$password = "";            // Your MySQL password
$dbname = "ietm";          // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Step 4: Navigate to the <DataSet> tags
$DataSet = $xml->xpath('//plm:DataSet');

if ($DataSet) {
    // Prepare an SQL statement for inserting data into the 'externalfile' table
    $stmt = $conn->prepare("INSERT INTO dataset_externalfile (id, name, memberRefs) VALUES (?, ?, ?)");

    if ($stmt === false) {
        die("Error preparing statement: " . $conn->error);
    }

    // Loop through each <DataSet> and process the data
    foreach ($DataSet as $dataset) {
        // Get the 'id', 'name', 'type' attributes of <DataSet>
        $datasetID = htmlspecialchars((string)$dataset['id']);
        $datasetName = htmlspecialchars((string)$dataset['name']);
        $type = htmlspecialchars((string)$dataset['type']);

        // Skip processing if the type is "PDF"
        if ($type != "PDF") {
            $memberRefs = htmlspecialchars((string)$dataset['memberRefs']);
            $memberRefs = ltrim($memberRefs, '#');  // Trim the '#' from the beginning

            // Bind the parameters to the prepared statement
            $stmt->bind_param("sss", $datasetID, $datasetName, $memberRefs);

            // Execute the prepared statement
            if (!$stmt->execute()) {
                echo "Error executing statement: " . $stmt->error . "<br>";
            } else {
                echo "<p>Inserted dataset: $datasetName (ID: $datasetID)</p>";
            }
        }
    }

    // Close the prepared statement
    $stmt->close();
} else {
    // If no <DataSet> tags are found, show a message
    echo "<p>No DataSet tags found in the XML file.</p>";
}

// Close the database connection
$conn->close();
?>
