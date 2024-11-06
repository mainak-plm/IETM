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

// Fetch 'name' and 'dataSetRef' from 'itemrevision' table for a specific id
$id = 'id13';  // Example id, replace with dynamic value if necessary

// Construct SQL query (directly inserting the variable)
$sql = "SELECT name, dataSetRef FROM itemrevision WHERE id = '$id'";

// Execute the query
$result = $conn->query($sql);

// Create arrays to store the data for the left side and right side
$names = [];
$fileContents = [];

while ($row = $result->fetch_assoc()) {
    $name = htmlspecialchars($row['name']); // Sanitize output for display
    $dataSetRef = htmlspecialchars($row['dataSetRef']); // Sanitize output for display

    // Store the name and its corresponding dataSetRef in arrays
    $names[] = ['name' => $name, 'dataSetRef' => $dataSetRef];

    // Use prepared statements for the second query (fetch 'memberRefs')
    $sql2 = "SELECT memberRefs FROM dataset_externalfile WHERE id = '$dataSetRef'";
    $result2 = $conn->query($sql2);

    if ($result2 && $result2->num_rows > 0) {
        $row2 = $result2->fetch_assoc();
        $memberRefs = htmlspecialchars($row2['memberRefs']); // Sanitize output for display

        // Use prepared statements for the third query (fetch 'locationref' and 'file_content')
        $sql3 = "SELECT locationref, file_content FROM html_files WHERE id = '$memberRefs'";
        $result3 = $conn->query($sql3);

        if ($result3 && $result3->num_rows > 0) {
            $row3 = $result3->fetch_assoc();
            $locationRef = htmlspecialchars($row3['locationref']); // Sanitize output for display
            $fileContent = $row3['file_content']; // Don't sanitize file_content since it might contain HTML

            // Store the fileContent for later display
            $fileContents[] = ['fileContent' => $fileContent];
        }
    }
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interactive Content</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .container {
            display: flex;
        }
        .left-side {
            width: 30%;
            padding: 20px;
            border-right: 1px solid #ddd;
            background-color: #f7f7f7;
        }
        .right-side {
            width: 70%;
            padding: 20px;
        }
        .name-item {
            cursor: pointer;
            padding: 10px;
            margin: 5px 0;
            background-color: #e0e0e0;
            border-radius: 5px;
        }
        .name-item:hover {
            background-color: #ccc;
        }
        .file-content {
            display: none;
        }
    </style>
</head>
<body>

<div class="container">
    <!-- Left Side -->
    <div class="left-side">
        <h2>Items</h2>
        <?php foreach ($names as $index => $item): ?>
            <div class="name-item" data-index="<?= $index ?>">
                <?= $item['name'] ?>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Right Side -->
    <div class="right-side">
        <h2>File Content</h2>
        <?php foreach ($fileContents as $index => $content): ?>
            <div class="file-content" id="file-content-<?= $index ?>">
                <h3>File Content <?= $index + 1 ?></h3>
                <div><?= $content['fileContent'] ?></div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- JavaScript for interactivity -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        // Hide all file contents initially
        $(".file-content").hide();

        // When a name item is clicked
        $(".name-item").click(function() {
            var index = $(this).data("index");  // Get the index of the clicked name item

            // Hide all other file contents
            $(".file-content").hide();

            // Show the corresponding file content
            $("#file-content-" + index).show();
        });
    });
</script>

</body>
</html>
