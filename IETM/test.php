<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Step 1: Database connection parameters
$servername = "localhost";
$username = "root";  // Change this to your database username
$password = "";      // Change this to your database password
$dbname = "ietm";    // Change this to your database name

// Step 2: Establish database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check for database connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);  // Connection failure message
} else {
    echo "<p>Database connection established successfully.</p>";  // Success message
}

// Step 3: Specify the path to the XML file
$xmlFilePath = 'C:/xampp/htdocs/test_06 (1).xml';  // Ensure this path is correct

// Check if the file exists
if (!file_exists($xmlFilePath)) {
    die("The file does not exist at the specified path: $xmlFilePath");
}

// Step 4: Load the XML file
$xml = simplexml_load_file($xmlFilePath);

// Check if loading the XML file was successful
if ($xml === false) {
    echo "Error loading XML file: ";
    foreach(libxml_get_errors() as $error) {
        echo "<br>", $error->message;
    }
    exit;
}

// Step 5: Register the XML namespace to access the elements
$xml->registerXPathNamespace('plm', 'http://www.plmxml.org/Schemas/PLMXMLSchema');

// Step 6: Extract the <ProductRevision> elements using XPath
$occurrences = $xml->xpath('//plm:ProductRevision');

// Check if any ProductRevision tags were found in the XML
if (!$occurrences) {
    echo "<p>No ProductRevision tags found in the XML.</p>";
    exit;  // Exit if no occurrences were found
} else {
    echo "<p>Found " . count($occurrences) . " ProductRevision tags.</p>";
}

// Step 7: Prepare the SQL statement for inserting records into the database
$stmt = $conn->prepare("INSERT INTO itemrevision (id, name, revision, label) VALUES (?, ?, ?, ?)");
if (!$stmt) {
    die("Error preparing SQL statement: " . $conn->error);
}

// Step 8: Iterate over each ProductRevision and insert into the database
foreach ($occurrences as $occurrence) {
    // Get the attributes from the XML element
    $occurrenceID = htmlspecialchars((string)$occurrence['id']);
    $occurrenceName = htmlspecialchars((string)$occurrence['name']);
    $occurrenceRev = htmlspecialchars((string)$occurrence['revision']);
    $label = 1;  // Static label value as per the original code

    // Bind the parameters to the prepared statement
    $stmt->bind_param("ssss", $occurrenceID, $occurrenceName, $occurrenceRev, $label);

    // Execute the SQL statement and check for success
    if ($stmt->execute()) {
        echo "<p>New record created successfully for Occurrence ID: $occurrenceID</p>";
    } else {
        echo "<p>Error: " . $stmt->error . "</p>";  // Output detailed error message if execution fails
    }
}

// Step 9: Close the prepared statement and the database connection
$stmt->close();
$conn->close();

?>

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
$occurrences = $xml->xpath('//plm:Occurrence');

// Prepare the SQL statement for inserting data
$stmt = $conn->prepare("INSERT INTO table_occurrence (id, instancedRef, occurrenceRefs) VALUES (?, ?, ?)");

if ($occurrences) {
    // Loop through each <Occurrence> and insert data into the database
    foreach ($occurrences as $occurrence) {
        // Get the 'id' attribute of <Occurrence>
        $occurrenceID = htmlspecialchars((string)$occurrence['id']);
        
        // Get the 'instancedRef' attribute and trim the '#' character
        $instancedRef = htmlspecialchars((string)$occurrence['instancedRef']);
        $instancedRef = ltrim($instancedRef, '#');  // Trim the '#' from the beginning
        
        // Get the 'occurrenceRefs' attribute and separate them with commas if multiple
        $occurrenceRefs = htmlspecialchars((string)$occurrence['occurrenceRefs']);
        
        // If there are multiple occurrenceRefs, separate them by commas
        if (strpos($occurrenceRefs, ' ') !== false) {
            $occurrenceRefsArray = explode(' ', $occurrenceRefs);  // Split by space if multiple refs
            $occurrenceRefs = implode(', ', $occurrenceRefsArray); // Join with commas
        }

        // Bind the parameters and execute the SQL query to insert the data
        $stmt->bind_param("sss", $occurrenceID, $instancedRef, $occurrenceRefs);
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
