<?php
// Include database connection
include('../config/db.php');

// Check if the user is logged in (authentication check)
session_start();
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if the user is not authenticated
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id']; // Get the user ID from session
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect the submitted form data
    $username = $_POST['username'];
    $first_name = $_POST['first_name'];
    $surname = $_POST['surname'];
    $last_name = $_POST['last_name'];
    $user_email = $_POST['user_email'];
    $user_phoneno = $_POST['user_phoneno'];
    $user_dob = $_POST['user_dob'];
    $user_type = $_POST['user_type'];

    
    // Profile picture upload logic (optional)
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == 0 && $_FILES['profile_pic']['size']!==0) {
        // Validate and upload the new profile picture

        print_r($_FILES);
        $upload_dir = 'uploads/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true); // Create the folder if not exists
        }

        $profile_pic=$_FILES['profile_pic']['name'];
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];

        if (in_array($_FILES['profile_pic']['type'], $allowed_types)) {
            $file_name = time() . '-' . basename($profile_pic);
            $profile_pic =$file_name;
            $file_path = $upload_dir . $file_name;
            move_uploaded_file($_FILES['profile_pic']['tmp_name'], $file_path);
            $old_file_path= $upload_dir.$_POST['existing_profile_pic'];
            if (file_exists($old_file_path)) {
                if (unlink($old_file_path)) {
                    echo "File deleted successfully!";
                } else {
                    echo "Error deleting file.";
                }
            } 
            else{
                echo "not delte";
            }
        } else {
            echo "Invalid file type. Only JPG, PNG, and GIF files are allowed.";
            $profile_pic=$_POST['existing_profile_pic'];
           
        }
    } else {
        // If no file is uploaded, retain the existing profile picture
        $profile_pic = $_POST['existing_profile_pic'];
    }

    // Update the user profile in the database
    $query = "UPDATE user SET 
                username = ?, 
                first_name = ?, 
                surname = ?, 
                last_name = ?, 
                user_email = ?, 
                user_phoneno = ?, 
                user_dob = ?, 
                user_type = ?, 
                profile_pic = ?
              WHERE user_id = ?";

    // Prepare and bind parameters to the query
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssssssssi", $username, $first_name, $surname, $last_name, $user_email, $user_phoneno, $user_dob, $user_type, $profile_pic, $user_id);

    // Execute the query and check for errors
    if ($stmt->execute()) {
        echo "Profile updated successfully!";
        header("Location: profile.php"); // Redirect to the profile page
        exit();
    } else {
        echo "Error updating profile: " . $stmt->error;
    }
}
?>