<?php
session_start();
include('../config/db.php'); // Include the database connection

// Check if the user is logged in
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    // Redirect to login page if not logged in
    header('Location: login.php');
    exit();
}

// Check if 'artistusername' is passed in the query string
if (isset($_GET['artistusername'])) {
    $artist_username = mysqli_real_escape_string($conn, $_GET['artistusername']);

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

// Handle booking form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the selected date and booking type
    $selected_date = $_POST['booking_date'];
    $booking_type = $_POST['booking_type'];

    // Get user ID from session
    $user_id = $_SESSION['user_id'];

    // Get today's date to check if the selected date is in the past
    $current_date = date('Y-m-d');

    if ($selected_date < $current_date) {
        $booking_status = 'error'; // Message type for error
        $message = "Your selected date is in the past, please choose a valid date.";
    } else {
        // Check if the artist is already booked for the selected date
        $check_booking_query = "SELECT * FROM booking WHERE artist_id = {$artist['user_id']} AND date = '$selected_date'";
        $check_booking_result = mysqli_query($conn, $check_booking_query);

        if (mysqli_num_rows($check_booking_result) > 0) {
            $booking_status = 'error'; // Message type for error
            $message = "Sorry, the artist is already booked for this date. Please select another date.";
        } else {
            // If the artist is available, insert the booking
            $insert_booking_query = "INSERT INTO booking (artist_id, user_id, date, booking_type) 
                                     VALUES ({$artist['user_id']}, $user_id, '$selected_date', '$booking_type')";

            if (mysqli_query($conn, $insert_booking_query)) {
                $booking_status = 'success'; // Message type for success
                $message = "Booking successfully made! We will confirm your booking shortly.";
            } else {
                $booking_status = 'error'; // Message type for error
                $message = "Error occurred while booking. Please try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Artist - <?php echo $artist['first_name'] . ' ' . $artist['last_name']; ?></title>
    <link rel="stylesheet" href="../assets/css/book.css">
</head>
<body>
    <div class="container">
        <h2>Book Artist - <?php echo $artist['first_name'] . ' ' . $artist['last_name']; ?></h2>

        <?php if (isset($message)): ?>
            <div class="alert-<?php echo $booking_status; ?>" id="alert-box">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <!-- Booking Form -->
        <form action="book.php?artistusername=<?php echo $artist['username']; ?>" method="POST">
            <div class="form-group">
                <label for="booking_date">Select Date:</label>
                <input type="date" name="booking_date" id="booking_date" required>
            </div>

            <div class="form-group">
                <label for="booking_type">Booking Type:</label>
                <input type="text" name="booking_type" id="booking_type" placeholder="e.g., Event, Concert" required>
            </div>

            <button accesskey="s" type="submit" title="use alt+s for book" class="btn-submit">Book Artist</button>
        </form>

        <button accesskey="b" title="use alt+b for back" onclick="location.href='artist.php?username=<?php echo $artist['username']; ?>'">Back to Artist Profile</button>
    </div>

    <script>
        // Display alert messages using JavaScript (if there is any)
        window.onload = function() {
            const alertBox = document.getElementById('alert-box');
            if (alertBox) {
                setTimeout(function() {
                    alertBox.style.display = 'none';
                }, 2000); // Hide the message after 5 seconds
            }
        }
    </script>
</body>
</html>
