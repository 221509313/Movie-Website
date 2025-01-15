<?php
// profile.php

// Include database connection
include 'db_connection.php';

// Get the user ID from the query parameter
$user_id = $_GET['id'] ?? null;

if ($user_id) {
    // Fetch user details from the database
    $query = "SELECT * FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
    } else {
        $user = null;
    }
} else {
    $user = null;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/css/bootstrap.min.css">
    <style>
        .profile-img {
            border-radius: 50%;
            width: 150px;
            height: 150px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <?php if ($user) : ?>
            <div class="text-center">
                <img src="<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Profile Picture" class="profile-img">
                <h2 class="mt-3"><?php echo htmlspecialchars($user['username']); ?></h2>
                <p>Email: <?php echo htmlspecialchars($user['email']); ?></p>
                <!-- Add more user details here -->
            </div>
        <?php else : ?>
            <p class="text-center">User not found.</p>
        <?php endif; ?>
    </div>
</body>
</html>
