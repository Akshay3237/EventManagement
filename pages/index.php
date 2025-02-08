<?php
session_start();
include('../config/db.php'); // Include the database connection

// Check if the user is logged in
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    // Redirect to login page if not logged in
    header('Location: login.php');
    exit();
}

// Get user information
$username = $_SESSION['username'];
$user_id = $_SESSION['user_id'];

// Check if the logged-in user is an artist
$query = "SELECT * FROM user WHERE user_id = '$user_id'";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Error executing query: " . mysqli_error($conn)); // Add error handling for the user query
}

$user = mysqli_fetch_assoc($result);

// Fetch the list of all artists (those who have user_type = 'artist')
$artist_query = "SELECT * FROM user WHERE user_type = 'artist'";
$artist_result = mysqli_query($conn, $artist_query);

// Add error handling for the artist query
if (!$artist_result) {
    die("Error executing artist query: " . mysqli_error($conn)); // Add error handling for the artist query
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Page</title>
    <link rel="stylesheet" href="../assets/css/home.css"> <!-- Link to external CSS -->
</head>
<body>
    <div class="container">
        <h1>Artists</h1>
        
        <div class="artist-grid">
            <?php while ($artist = mysqli_fetch_assoc($artist_result)) { 
                // Combine first name, last name, and surname to create full name
                $full_name = $artist['first_name'] . ' ' . $artist['last_name'] . ' ' . $artist['surname'];

                // Check if artist has a profile image, else use a default image
                $profile_pic = $artist['profile_pic'] ? 'uploads/' . $artist['profile_pic'] : 'uploads/default.png';
            ?>
                <div class="artist-card" onclick="window.location.href='artist.php?username=<?php echo $artist['username']; ?>'">
                    <img src="<?php echo $profile_pic; ?>" alt="Artist Image">
                    <h3><?php echo $artist['username']; ?></h3>
                </div>
            <?php } ?>
        </div>
    </div>
    
    <div class="nav">
        <!-- Add option to go to the profile page for non-artists -->
        <button accesskey="p" title="Go to Profile" onclick="location.href='profile.php'"><img src="../assets/images/account-icon.png" alt="Account"></button>

      
        <button title="your bookings" accesskey="B" onclick="location.href='bookingdashboard1.php'"><img src="../assets/images/photo3.jpg" alt="B"></button>
     
        <?php if ($user['user_type'] === 'artist') { ?>
        <button title="who books you" onclick="location.href='bookingdashboard2.php'"><img src="../assets/images/photo5.png" alt="$"></button>
    <?php } ?>
    </div>
</body>
</html>
