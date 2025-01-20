<?php

// Make the connection and check for errors
$conn = new mysqli('localhost', 'root', '', 'alumni_db');

// Check if the connection was successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);  // Provide detailed error message
}

return $conn;  // Return the connection object

?>
