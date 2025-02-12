<?php
// Include database connection
include('../config/db.php');

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id']; 

// Fetch user profile securely using prepared statement
try {
    $stmt = $conn->prepare("SELECT u.user_id, u.username, u.first_name, u.surname, u.last_name, u.user_email, u.user_phoneno, u.user_dob, u.profile_pic, u.user_type, a.description, a.charge
                            FROM user u 
                            LEFT JOIN artist a ON u.user_id = a.user_id
                            WHERE u.user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    if (!$user) {
        throw new Exception("User not found.");
    }
    
    // Check if the user is an artist and if the artist table entry exists
    if ($user['user_type'] == 'artist' && (empty($user['description']) || empty($user['charge']))) {
        // If no artist entry exists or the description/charge is empty, set default values
        $user['description'] = $user['description'] ?: ''; // Set empty if no description
        $user['charge'] = $user['charge'] ?: '0'; // Set 0 if no charge
    }
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="../assets/css/profile.css">
    <script>
        function toggleEdit() {
            let elements = document.querySelectorAll("#profileForm input, #profileForm select, #profileForm textarea");
            let editButton = document.getElementById("editButton");
            let saveButton = document.getElementById("saveButton");

            elements.forEach(el => el.toggleAttribute("disabled"));

            if (editButton.style.display !== "none") {
                editButton.style.display = "none";
                saveButton.style.display = "inline-block";
            } else {
                editButton.style.display = "inline-block";
                saveButton.style.display = "none";
            }
        }

        function previewImage(event) {
            let output = document.getElementById("profilePicPreview");
            output.src = URL.createObjectURL(event.target.files[0]);
        }
    </script>
</head>
<body>
    <div class="container">
        <nav>
            <a style="background:red; border-radius:20px" href="index.php" accesskey="h" title="go to home page">Home</a>
        </nav>
        <h1>Welcome, <?php echo htmlspecialchars($user['username']); ?></h1>

        <form id="profileForm" action="update_profile.php" method="POST" enctype="multipart/form-data">
            <img id="profilePicPreview" src="uploads/<?php echo $user['profile_pic'] ? $user['profile_pic'] : 'default.png'; ?>" alt="Profile Picture">
            <input type="text" name="existing_profile_pic" value="<?php echo htmlspecialchars($user['profile_pic']); ?>" disabled hidden>
            <div>
                <label for="profile_pic">Change Profile Picture:</label>
                <input type="file" name="profile_pic" id="profile_pic" accept="image/*" onchange="previewImage(event)" disabled>
            </div>

            <div>
                <label for="username">Username:</label>
                <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" disabled>
            </div>

            <div>
                <label for="first_name">First Name:</label>
                <input type="text" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" disabled>
            </div>

            <div>
                <label for="surname">Surname:</label>
                <input type="text" name="surname" value="<?php echo htmlspecialchars($user['surname']); ?>" disabled>
            </div>

            <div>
                <label for="last_name">Last Name:</label>
                <input type="text" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" disabled>
            </div>

            <div>
                <label for="user_email">Email:</label>
                <input type="email" name="user_email" value="<?php echo htmlspecialchars($user['user_email']); ?>" disabled>
            </div>

            <div>
                <label for="user_phoneno">Phone Number:</label>
                <input type="text" name="user_phoneno" value="<?php echo htmlspecialchars($user['user_phoneno']); ?>" disabled>
            </div>

            <div>
                <label for="user_dob">Date of Birth:</label>
                <input type="date" name="user_dob" value="<?php echo htmlspecialchars($user['user_dob']); ?>" disabled>
            </div>

            <div hidden>
                <label for="user_type">User Type:</label>
                <select name="user_type" disabled>
                    <option value="customer" <?php echo $user['user_type'] == 'customer' ? 'selected' : ''; ?>>Customer</option>
                    <option value="artist" <?php echo $user['user_type'] == 'artist' ? 'selected' : ''; ?>>Artist</option>
                </select>
            </div>

            <?php if ($user['user_type'] == 'artist'): ?>
                <!-- Fields only visible to artist -->
                <div>
                    <label for="description">Artist Description:</label>
                    <textarea name="description" rows="4" disabled><?php echo htmlspecialchars($user['description']); ?></textarea>
                </div>

                <div>
                    <label for="charge">Charge:</label>
                    <input type="text" name="charge" value="<?php echo htmlspecialchars($user['charge']); ?>" disabled>
                </div>
            <?php endif; ?>

            <button type="button" id="editButton" class="edit-btn" onclick="toggleEdit()">Edit</button>
            <button type="submit" id="saveButton" class="save-btn">Save</button>
        </form>

        <form action="logout.php" method="POST">
            <button type="submit" class="logout-btn">Logout</button>
        </form>
    </div>
</body>
</html>
