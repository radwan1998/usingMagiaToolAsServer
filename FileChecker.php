<?php

class FileChecker
{
    private $uploadedFileName;
    private $fileToUploadSize;
    private $tmpFileName;
    private $maxFileSize;
    private $text;

    public function __construct($file, $text = '', $allowedMaxFileSize = 15)
    {
        $this->uploadedFileName = $file["name"];
        $this->fileToUploadSize = $file["size"];
        $this->tmpFileName = $file["tmp_name"];
        $this->maxFileSize = $allowedMaxFileSize * 1000000; // Convert MB to bytes
        $this->text = $text;
    }

    public function checkFileSize()
    {
        if ($this->fileToUploadSize > $this->maxFileSize) {
            echo "<div class='error'>Sorry, your file is too large.</div>";
            return false;
        }
        return true;
    }

    public function executeCommand()
    {
        // Check file size
        if (!$this->checkFileSize()) {
            return;
        }

        // Initialize cURL session
        $curl = curl_init();

        // Read the file content
        $fileContent = file_get_contents($this->tmpFileName);

        // Set the POST data
        $postData = array(
            'file' => base64_encode($fileContent), // Encode file content as base64
            'text' => $this->text // Pass the 'text' argument
        );

        // Set cURL options
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://127.0.0.1:5000/execute_magika',
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($postData), // Encode POST data as JSON
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json', // Set content type to JSON
            ),
            CURLOPT_RETURNTRANSFER => true,
        ));

        // Execute the cURL request
        $response = curl_exec($curl);

        // Check for errors
        if ($response === false) {
            // Handle error
            $error = curl_error($curl);
            // Log the error
            error_log("cURL error: " . $error);
            // Return or handle the error appropriately
            return null;
        }

        // Close cURL session
        curl_close($curl);

        // Return the response
        return $response;
    }

    public function getFileGroupInfo()
    {
        // Call executeCommand method to get the result
        return $this->executeCommand();
    }
}

?>
