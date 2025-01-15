<?php
require 'db_connection.php'; // Ensure this path is correct and the file exists

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect POST data
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Initialize profile picture path
    $profile_picture_path = '';

    // Handle profile picture upload
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == UPLOAD_ERR_OK) {
        $profile_picture = $_FILES['profile_picture'];
        $allowed_extensions = ['jpg', 'jpeg', 'png'];
        $profile_picture_extension = pathinfo($profile_picture['name'], PATHINFO_EXTENSION);
        $profile_picture_size = $profile_picture['size'];

        if (in_array($profile_picture_extension, $allowed_extensions) && $profile_picture_size <= 5000000) { // Max size 5 MB
            $upload_dir = 'uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true); // Create directory if it does not exist
            }

            $profile_picture_path = $upload_dir . basename($profile_picture['name']);
            if (move_uploaded_file($profile_picture['tmp_name'], $profile_picture_path)) {
                // Success, continue with database insertion
            } else {
                echo 'Failed to upload profile picture. Check directory permissions.';
                exit();
            }
        } else {
            echo 'Invalid file type or file too large.';
            exit();
        }
    } else {
        echo 'No profile picture uploaded or there was an upload error.';
        exit();
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Prepare an SQL statement
    $sql = "INSERT INTO users (username, email, password, profile_picture) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        // Bind parameters and execute the statement
        mysqli_stmt_bind_param($stmt, 'ssss', $username, $email, $hashed_password, $profile_picture_path);
        if (mysqli_stmt_execute($stmt)) {
            // Close the statement
            mysqli_stmt_close($stmt);

            // Close the connection
            mysqli_close($conn);

            // Redirect to the login page
            header('Location: login.php');
            exit(); // Make sure to call exit() after header redirection to stop further script execution
        } else {
            echo "Error: " . mysqli_stmt_error($stmt);
        }
    } else {
        echo "Error: " . mysqli_error($conn);
    }

    // Close the connection in case of error
    mysqli_close($conn);
}
?>

