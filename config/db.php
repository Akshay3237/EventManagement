<?php
// Include the commonprint.php file for printing messages
include('../includes/commonprint.php');

// Database configuration
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "dayra_booking";  // Your database name

// Create connection
$conn = new mysqli($host, $user, $pass);

// Check connection
if ($conn->connect_error) {
    show_error("Connection failed: " . $conn->connect_error);
} else {
    print_message("Connection successful.");
}


// Select the database after creating it
$conn->select_db($dbname);

// Optional: Set character set to UTF-8
$conn->set_charset("utf8");

?>
