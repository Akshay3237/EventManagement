<?php
// This file contains functions for printing messages and errors to the browser's console using JavaScript

/**
 * Print a message to the console
 *
 * @param string $message The message to print
 */
function print_message($message) {
    echo "<script>console.log('Message: " . addslashes($message) . "');</script>";
}

/**
 * Print an error message to the console
 *
 * @param string $error The error message to print
 */
function show_error($error) {
    echo "<script>console.error('Error: " . addslashes($error) . "');</script>";
}
?>
