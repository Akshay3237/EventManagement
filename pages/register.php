<?php
session_start();
// Include the database connection
include('../config/db.php');

// Check if the user is already logged in
if (isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true) {
    // Redirect to index.php if already logged in
    header('Location: index.php');
    exit();
}
// Initialize error message
$user_error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the form data
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
    $surname = mysqli_real_escape_string($conn, $_POST['surname']);
    $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $userphoneno = mysqli_real_escape_string($conn, $_POST['userphoneno']);
    $useremail = mysqli_real_escape_string($conn, $_POST['useremail']);
    $usertype = mysqli_real_escape_string($conn, $_POST['usertype']);
    $dob = mysqli_real_escape_string($conn, $_POST['dob']);
    $userpassword = mysqli_real_escape_string($conn, $_POST['userpassword']);

    // Hash the password
    $hashed_password = password_hash($userpassword, PASSWORD_DEFAULT);

    // Try to insert the data into the database
    try {
        // Insert query to add user
        $query = "INSERT INTO user (first_name, surname, last_name, username, user_email, user_phoneno, user_password, user_dob, user_type) 
                  VALUES ('$first_name', '$surname', '$last_name', '$username', '$useremail', '$userphoneno', '$hashed_password', '$dob', '$usertype')";

        // Execute query
        if (mysqli_query($conn, $query)) {
            // Redirect to login page after successful registration
            header('Location: login.php');
            exit();
        } else {
            // Throw exception if query fails
            throw new Exception("Error executing query: " . mysqli_error($conn));
        }
    } catch (Exception $e) {
        // Catch any errors and set the error message
        $user_error_message = "An error occurred while processing your registration: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="../assets/css/register.css">
</head>
<body>
    <div class="register-container">
        <h2>User Registration</h2>
        <form id="registerForm" action="register.php" method="POST">
            <div class="form-group">
                <label for="first_name">First Name</label>
                <input type="text" id="first_name" name="first_name" required>
            </div>
            <div class="form-group">
                <label for="surname">Surname</label>
                <input type="text" id="surname" name="surname" required>
            </div>
            <div class="form-group">
                <label for="last_name">Last Name</label>
                <input type="text" id="last_name" name="last_name" required>
            </div>
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="userphoneno">Phone Number</label>
                <input type="text" id="userphoneno" name="userphoneno" required>
            </div>
            <div class="form-group">
                <label for="useremail">Email</label>
                <input type="email" id="useremail" name="useremail" required>
            </div>
            <div class="form-group">
                <label for="usertype">User Type</label>
                <select id="usertype" name="usertype">
                    <option value="artist">Artist</option>
                    <option value="customer">Customer</option>
                </select>
            </div>
            <div class="form-group">
                <label for="dob">Date of Birth</label>
                <input type="date" id="dob" name="dob" required>
            </div>
            <div class="form-group">
                <label for="userpassword">Password</label>
                <input type="password" id="userpassword" name="userpassword" required>
            </div>
            <div class="form-group">
                <button type="submit">Register</button>
            </div>
        </form>

        <!-- Display error message if there's any -->
        <?php
        if (isset($user_error_message) && $user_error_message != '') {
            echo "<p style='color: red;'>$user_error_message</p>";
        }
        ?>

        <div>
            <p>Already have an account? <a href="login.php">Login here</a></p>
        </div>
    </div>
</body>
</html>
