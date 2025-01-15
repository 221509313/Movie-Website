<?php
session_start();
require 'db_connection.php'; // Include your database connection file

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $video = $_FILES['video'];

    // Validate and process the uploaded video
    $allowed_extensions = ['mp4'];
    $video_extension = pathinfo($video['name'], PATHINFO_EXTENSION);
    $video_size = $video['size'];

    if (in_array($video_extension, $allowed_extensions) && $video_size <= 1000000000) { // Max size 1 GB
        $video_path = 'videos/' . basename($video['name']);
        if (move_uploaded_file($video['tmp_name'], $video_path)) {
            // Insert video info into the database using MySQLi
            $stmt = $conn->prepare("INSERT INTO videos (user_id, video_path) VALUES (?, ?)");
            $stmt->bind_param("is", $user_id, $video_path);

            if ($stmt->execute()) {
                // Redirect to the home page with a success message
                header("Location: index.php?success=1");
                exit();
            } else {
                echo 'Failed to upload video: ' . $stmt->error;
            }

            $stmt->close();
        } else {
            echo 'Failed to upload video/ video must be 10 min long or lesser.';
        }
    } else {
        echo 'Invalid file type or file too large.';
    }
}

// Close the connection
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post a Video</title>
</head>
<body>
    <form action="post-video.php" method="POST" enctype="multipart/form-data">
        <label for="video">Select Video:</label>
        <input type="file" name="video" id="video" accept="video/mp4" required>
        <button type="submit">Upload Video</button>
    </form>
</body>
</html>


