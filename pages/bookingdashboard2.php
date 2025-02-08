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
$artist_id = $_SESSION['user_id'];

// Check if a cancel request was made
if (isset($_GET['cancel_booking_id'])) {
    $cancel_booking_id = mysqli_real_escape_string($conn, $_GET['cancel_booking_id']);

    // Delete the booking
    $delete_query = "DELETE FROM booking WHERE booking_id = $cancel_booking_id AND artist_id = $artist_id";
    $delete_result = mysqli_query($conn, $delete_query);

    if ($delete_result) {
        $success_message = "Booking has been canceled successfully.";
    } else {
        $error_message = "Failed to cancel booking. Please try again.";
    }
}

// Fetch all bookings where the artist's ID matches the logged-in user
$query = "SELECT booking.*, user.first_name AS artist_first_name, user.last_name AS artist_last_name, u.first_name AS client_first_name, u.last_name AS client_last_name 
          FROM booking 
          JOIN user ON booking.artist_id = user.user_id 
          JOIN user AS u ON booking.user_id = u.user_id 
          WHERE booking.artist_id = $artist_id";
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
    <title>Booking Dashboard - Artist</title>
    <link rel="stylesheet" href="../assets/css/bookingdashboard2.css">
</head>
<body>
    <div class="container">
        <h2>Your Artist Booking Dashboard</h2>

        <?php if (isset($success_message)): ?>
            <div class="alert alert-success">
                <?php echo $success_message; ?>
            </div>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
            <div class="alert alert-error">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <?php if (mysqli_num_rows($result) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Client Name</th>
                        <th>Booking Date</th>
                        <th>Booking Type</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?php echo $row['client_first_name'] . ' ' . $row['client_last_name']; ?></td>
                            <td><?php echo $row['date']; ?></td>
                            <td><?php echo $row['booking_type']; ?></td>
                            <td>
                                <a href="bookingdashboard2.php?cancel_booking_id=<?php echo $row['booking_id']; ?>" 
                                   onclick="return confirm('Are you sure you want to cancel this booking?');" class="btn-cancel">
                                    Cancel Booking
                                </a>
                            </td>
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
