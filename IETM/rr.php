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

// Step 3: Navigate to the <ProductRevision> tags
$occurrences = $xml->xpath('//plm:ProductRevision');

if ($occurrences) {
    // Output information from all <ProductRevision> tags
    echo "## ProductRevision Information";
    foreach ($occurrences as $occurrence) {
        // Get the 'id' attribute of <Occurrence>
        $occurrenceID = htmlspecialchars((string)$occurrence['id']);
        $occurrenceName = htmlspecialchars((string)$occurrence['name']);
        $occurrenceRev = htmlspecialchars((string)$occurrence['revision']);
        echo "<p>ProductRevision ID: $occurrenceID</p>";
        echo "<p>ProductRevision Name: $occurrenceName</p>";
        echo "<p>ProductRevision Rev: $occurrenceRev</p>";
    }
} else {
    echo "<p>No ProductRevision tags found.</p>";
}

// Step 3: Navigate to the <RequirementRevision> tags
$occurrences1 = $xml->xpath('//plm:RequirementRevision');

if ($occurrences1) {
    // Output information from all <RequirementRevision> tags
    echo "## RequirementRevision Information";
    foreach ($occurrences1 as $occurrence) {
        // Get the 'id' attribute of <Occurrence>
        $occurrenceID = htmlspecialchars((string)$occurrence['id']);
        $occurrenceName = htmlspecialchars((string)$occurrence['name']);
        $occurrenceRev = htmlspecialchars((string)$occurrence['revision']);
        echo "<p>RequirementRevision ID: $occurrenceID</p>";
        echo "<p>RequirementRevision Name: $occurrenceName</p>";
        echo "<p>RequirementRevision Rev: $occurrenceRev</p>";

        // Get the 'dataSetRef' attribute and remove the leading '#'
        $dataSetRef = (string)$occurrence->AssociatedDataSet['dataSetRef'];
        
        // Remove the '#' character from the start of dataSetRef
        $dataSetRef = ltrim($dataSetRef, '#');
        
        echo "<p>dataSetRef: $dataSetRef</p>";
    }
} else {
    echo "<p>No RequirementRevision tags found.</p>";
}
?>
