<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Step 1: Specify the path to the XML file
$xmlFilePath = 'C:/xampp/htdocs/new 8.xml'; // Ensure this path is correct

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

// Step 3: Navigate to the <Occurrence> tags
$occurrences = $xml->xpath('//plm:Occurrence');

if ($occurrences) {
    // Output information from all <Occurrence> tags
    echo "## Occurrence Information";
    foreach ($occurrences as $occurrence) {
        // Get the 'id' attribute of <Occurrence>
        $occurrenceID = htmlspecialchars((string)$occurrence['id']);
        echo "<p>Occurrence ID: $occurrenceID</p>";

        // Check if the 'occurrenceRefs' attribute exists and print each ref
        if (isset($occurrence['occurrenceRefs'])) {
            $occurrenceRefs = explode(" ", (string)$occurrence['occurrenceRefs']);
            echo "<p>OccurrenceRefs:</p><ul>";
            foreach ($occurrenceRefs as $ref) {
                echo "<li>" . htmlspecialchars($ref) . "</li>";
            }
            echo "</ul>";
        } else {
            echo "<p>No OccurrenceRefs found.</p>";
        }
    }
} else {
    echo "<p>No Occurrence tags found.</p>";
}
?>