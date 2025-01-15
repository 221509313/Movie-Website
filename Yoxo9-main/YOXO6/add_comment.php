<?php
session_start();
require 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['video_id'], $_POST['comment']) && !empty($_POST['comment'])) {
        $video_id = $_POST['video_id'];
        $comment = $_POST['comment'];
        $user_id = $_SESSION['user_id'];

        if (filter_var($video_id, FILTER_VALIDATE_INT) === false || filter_var($user_id, FILTER_VALIDATE_INT) === false) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Invalid video or user ID']);
            exit;
        }

        $sql = "INSERT INTO comments (video_id, user_id, comment) VALUES (?, ?, ?)";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("iis", $video_id, $user_id, $comment);
            if ($stmt->execute()) {
                // Fetch username for the response
                $usernameQuery = "SELECT username FROM users WHERE id = ?";
                $userStmt = $conn->prepare($usernameQuery);
                $userStmt->bind_param("i", $user_id);
                $userStmt->execute();
                $userResult = $userStmt->get_result();
                $user = $userResult->fetch_assoc();
                
                echo json_encode(['status' => 'success', 'username' => $user['username']]);
            } else {
                http_response_code(500);
                echo json_encode(['status' => 'error', 'message' => $stmt->error]);
            }
            

            $stmt->close();
        } else {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $conn->error]);
        }
    } else {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Missing parameters']);
    }

    $conn->close();
} else {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>



