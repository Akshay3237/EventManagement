<?php
session_start();
include('../config/db.php'); // Include the database connection

// Check if the user is already logged in
if (isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true) {
    // Redirect to index.php if already logged in
    header('Location: index.php');
    exit();
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the form data
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Query to find the user by username
    $query = "SELECT * FROM user WHERE username = '$username'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        // User found, now verify the password
        $user = mysqli_fetch_assoc($result);
        if (password_verify($password, $user['user_password'])) {
            // Password correct, create session variables
            $_SESSION['authenticated'] = true;
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_id'] = $user['user_id'];

            // Redirect to the index page on successful login
            header('Location: index.php');
            exit();
        } else {
            $error_message = "Invalid password Or UserName!";
        }
    } else {
        $error_message = "Invalid password Or UserName!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="../assets/css/login.css">
</head>
<body>

<div class="login-container">
    <h2>Login</h2>
    <form method="POST" action="login.php">
        <input type="text" name="username" id="username" placeholder="Username" required>
        <input type="password" name="password" id="password" placeholder="Password" required>
        <button type="submit">Login</button>
        <button class="signup-button" onclick="redirectToSignup()">Sign Up</button>
    </form>

    <?php
    if (isset($error_message)) {
        echo "<p style='color: red;'>$error_message</p>";
    }
    ?>

    <p>Don't have an account? <a href="register.php">Register here</a></p>
</div>

<script>
    function redirectToSignup() {
        window.location.href = "register.php"; // Redirect to signup page
    }
</script>

</body>
</html>
