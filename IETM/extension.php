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
    foreach(libxml_get_errors() as $error) {
        echo "<br>", $error->message;
    }
    exit;
}


// Step 2: Register the XML namespace to access the elements
$xml->registerXPathNamespace('plm', 'http://www.plmxml.org/Schemas/PLMXMLSchema');


// Step 3: Navigate to the <ExternalFile> tags
$externalFiles = $xml->xpath('//plm:ExternalFile');


if ($externalFiles) {
    // Output information from all <ExternalFile> tags
    echo "<h2>ExternalFile Information</h2>";
   
    // Define your database connection details here
    $host = 'localhost';          // Database host (e.g., 'localhost')
    $dbname = 'ietm';    // Your database name
    $username = 'root';  // Your database username
    $password = '';  // Your database password


    // Create a PDO connection to the database
    try {
        // Database connection
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


        foreach ($externalFiles as $externalFile) {
            // Get the 'id' and 'locationRef' attributes of <ExternalFile>
            $externalID = htmlspecialchars((string)$externalFile['id']);
            $locationRef = htmlspecialchars((string)$externalFile['locationRef']);
           
            // Construct the full path to the HTML file (assuming it’s relative to the root directory)
            $htmlFilePath = 'C:/xampp/htdocs/IETM/IETM/folder/' . $locationRef;


            // Check if the HTML file exists
            if (file_exists($htmlFilePath)) {
                // Read the entire content of the HTML file
                $htmlContent = file_get_contents($htmlFilePath);


                // Check if the content was successfully read
                if ($htmlContent !== false) {
                    // Prepare SQL query to insert the HTML content into the database
                    $sql = "INSERT INTO html_files (id, locationref, file_content) VALUES (:id, :locationref, :file_content)";


                    // Prepare the statement
                    $stmt = $pdo->prepare($sql);


                    // Bind the parameters to the statement
                    $stmt->bindParam(':id', $externalID, PDO::PARAM_STR);
                    $stmt->bindParam(':locationref', $locationRef, PDO::PARAM_STR);
                    $stmt->bindParam(':file_content', $htmlContent, PDO::PARAM_STR);


                    // Execute the query
                    if ($stmt->execute()) {
                        echo "HTML content successfully inserted for ID: $externalID<br>";
                    } else {
                        echo "Failed to insert HTML content for ID: $externalID<br>";
                    }


                } else {
                    echo "Failed to read the HTML file at: $htmlFilePath<br>";
                }
            } else {
                echo "The HTML file at the location $htmlFilePath does not exist.<br>";
            }
        }
    } catch (PDOException $e) {
        // Handle any PDO exceptions
        echo "Database error: " . $e->getMessage();
    }


} else {
    echo "<p>No <ExternalFile> tags found.</p>";
}
?>