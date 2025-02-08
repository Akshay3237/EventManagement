<?php
session_start();
include('../config/db.php'); // Include the database connection

// Check if the user is logged in
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    // Redirect to login page if not logged in
    header('Location: login.php');
    exit();
}

// Get the currently logged-in user's ID
$user_id = $_SESSION['user_id'];

// Fetch all bookings of the logged-in user and related artist's information
$query = "SELECT booking.*, user.first_name AS artist_first_name, user.last_name AS artist_last_name 
          FROM booking 
          JOIN user ON booking.artist_id = user.user_id 
          WHERE booking.user_id = $user_id";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Error executing query: " . mysqli_error($conn));
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Dashboard</title>
    <link rel="stylesheet" href="../assets/css/bookingdashboard1.css">
</head>
<body>
    <div class="container">
        <h2>Your Booking Dashboard</h2>

        <?php if (mysqli_num_rows($result) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Artist Name</th>
                        <th>Booking Date</th>
                        <th>Booking Type</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?php echo $row['artist_first_name'] . ' ' . $row['artist_last_name']; ?></td>
                            <td><?php echo $row['date']; ?></td>
                            <td><?php echo $row['booking_type']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No bookings found for your account.</p>
        <?php endif; ?>

        <button onclick="location.href='index.php'">Back to Profile</button>
    </div>
</body>
</html>
