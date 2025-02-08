<?php
// Include the commonprint.php file for printing messages

/**
 * Function to create a database if it does not exist, 
 * or drop the existing one and create a new one.
 *
 * @param mysqli $conn The active database connection.
 * @param string $dbname The name of the database to create.
 */
function createDatabase($conn, $dbname) {
    // Drop the database if it exists (this will delete all data)
    $query = "DROP DATABASE IF EXISTS $dbname";
    if ($conn->query($query) === TRUE) {
        print_message("Existing database '$dbname' deleted successfully.");
    } else {
        show_error("Error deleting existing database: " . $conn->error);
    }

    // Create the new database
    $query = "CREATE DATABASE IF NOT EXISTS $dbname";
    if ($conn->query($query) === TRUE) {
        print_message("Database '$dbname' created successfully (or already exists).");
    } else {
        show_error("Error creating database: " . $conn->error);
    }
}

/**
 * Function to create tables for user, artist, and booking.
 *
 * @param mysqli $conn The active database connection.
 */
function createTables($conn) {
    // SQL to create the 'user' table with FirstName, Surname, LastName, and Profile Picture
    $userTable = "
    CREATE TABLE IF NOT EXISTS user (
        user_id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(255) UNIQUE NOT NULL,
        first_name VARCHAR(255) NOT NULL,
        surname VARCHAR(255) NOT NULL,
        last_name VARCHAR(255) NOT NULL,
        user_email VARCHAR(255) UNIQUE NOT NULL,
        user_phoneno VARCHAR(15) NOT NULL,
        user_password VARCHAR(255) NOT NULL,
        user_dob DATE,
        user_type ENUM('artist', 'customer', 'admin') DEFAULT 'customer',
        profile_pic VARCHAR(255) NULL  -- New column for profile picture
    )";

   

    // SQL to create the 'booking' table
    $bookingTable = "
    CREATE TABLE IF NOT EXISTS booking (
        booking_id INT AUTO_INCREMENT PRIMARY KEY,
        artist_id INT NOT NULL,
        user_id INT NOT NULL,
        date DATE,
        booking_type VARCHAR(255),
        FOREIGN KEY (artist_id) REFERENCES user(user_id),
        FOREIGN KEY (user_id) REFERENCES user(user_id)
    )";

    // Execute the queries for creating the tables
    if ($conn->query($userTable) === TRUE) {
        print_message("Table 'user' created successfully.");
    } else {
        show_error("Error creating table 'user': " . $conn->error);
    }

   

    if ($conn->query($bookingTable) === TRUE) {
        print_message("Table 'booking' created successfully.");
    } else {
        show_error("Error creating table 'booking': " . $conn->error);
    }
}
?>
