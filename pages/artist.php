<?php
session_start();
include('../config/db.php'); // Include the database connection

// Check if the user is logged in
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    // Redirect to login page if not logged in
    header('Location: login.php');
    exit();
}

// Check if 'username' is passed in the query string
if (isset($_GET['username'])) {
    $artist_username = mysqli_real_escape_string($conn, $_GET['username']);

    // Fetch artist details using the username from the query string
    $artist_query = "SELECT * FROM user WHERE username = '$artist_username' AND user_type = 'artist'";
    $artist_result = mysqli_query($conn, $artist_query);

    if (!$artist_result) {
        die("Error executing artist query: " . mysqli_error($conn));
    }

    // Check if the artist exists
    if (mysqli_num_rows($artist_result) > 0) {
        $artist = mysqli_fetch_assoc($artist_result);
    } else {
        echo "Artist not found.";
        exit();
    }
} else {
    echo "No artist selected.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Artist Profile</title>
    <link rel="stylesheet" href="../assets/css/artist.css"> <!-- Link to external CSS -->
</head>
<body>
    <div class="container">
        <div class="artist-card">
            <!-- Display Artist Profile Picture -->
            <img src="uploads/<?php echo $artist['profile_pic'] ? $artist['profile_pic'] : 'default.png'; ?>" class="artist-img" alt="Artist Image">

            <!-- Artist Details -->
            <h2><?php echo $artist['first_name'] . ' ' . $artist['last_name']; ?></h2>
            <p><strong>Username:</strong> <?php echo $artist['username']; ?></p>
            <p><strong>Email:</strong> <?php echo $artist['user_email']; ?></p>
            <p><strong>Phone:</strong> <?php echo $artist['user_phoneno']; ?></p>
            <p><strong>Birth Date:</strong> <?php echo $artist['user_dob']; ?></p>

            <!-- Action Buttons -->
            <div class="action-buttons">
                <button onclick="location.href='book.php?artistusername=<?php echo $artist['username']; ?>'">Book Artist</button>
                <button onclick="location.href='index.php'">Back to Home</button>
            </div>
        </div>
    </div>
</body>
</html>
