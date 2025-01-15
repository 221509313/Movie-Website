<?php
require 'db_connection.php';

if (isset($_GET['video_id'])) {
    $video_id = $_GET['video_id'];

    // Validate video_id
    if (filter_var($video_id, FILTER_VALIDATE_INT) === false) {
        echo 'Invalid video ID';
        exit;
    }

    // Fetch comments for the video
    $sql = "SELECT c.comment, u.username FROM comments c JOIN users u ON c.user_id = u.id WHERE c.video_id = ? ORDER BY c.created_at DESC";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $video_id);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            echo '<p><strong>' . htmlspecialchars($row['username']) . ':</strong> ' . htmlspecialchars($row['comment']) . '</p>';
        }

        $stmt->close();
    }

    $conn->close();
}
?>

