<?php

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: /");
    exit;
}

// Include the FileChecker class
require_once 'FileChecker.php';

// Check if a file has been uploaded
if (!isset($_FILES["fileToUpload"]) || $_FILES["fileToUpload"]["error"] == UPLOAD_ERR_NO_FILE) {
    echo "<div class='error'>No file has been selected for upload.</div>";
    exit;
}

$allowedMaxFileSize = 100; // Maximum allowed file size in MB

// Get the selected option from the form
$selectedOption = $_POST["optionsSelect"];

// Create an instance of FileChecker
$fileChecker = new FileChecker($_FILES["fileToUpload"], $selectedOption, $allowedMaxFileSize);

// Execute the command and capture the output
$response = $fileChecker->executeCommand();

// Display the output
echo "Response from Python server:<br>";
echo $response; // Output the response from the Python server

// Get file group information
$fileGroupInfo = $fileChecker->getFileGroupInfo();

// Display file group information
echo "<br>File Group: " . $fileGroupInfo;

?>
