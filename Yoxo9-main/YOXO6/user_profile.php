<?php
session_start(); // Start the session

require 'db_connection.php'; // Ensure the path is correct

// Get the user ID from the URL (e.g., user_profile.php?id=1)
if (isset($_GET['id'])) {
    $user_id = intval($_GET['id']);

    // Fetch the user's details
    $sql_user = "SELECT username, profile_picture,email FROM users WHERE id = ?";
    $stmt_user = $conn->prepare($sql_user);
    $stmt_user->bind_param("i", $user_id);
    $stmt_user->execute();
    $result_user = $stmt_user->get_result();

    if ($result_user && $user = $result_user->fetch_assoc()) {
        $username = $user['username'];
        $profile_picture = $user['profile_picture'];
        $email = $user['email'];
    } else {
        echo "User not found.";
        exit();
    }

    // Fetch the user's videos
    $sql_videos = "SELECT video_path FROM videos WHERE user_id = ?";
    $stmt_videos = $conn->prepare($sql_videos);
    $stmt_videos->bind_param("i", $user_id);
    $stmt_videos->execute();
    $result_videos = $stmt_videos->get_result();
    $videos = [];

    if ($result_videos) {
        while ($row = $result_videos->fetch_assoc()) {
            $videos[] = $row['video_path'];
        }
    }

    $stmt_user->close();
    $stmt_videos->close();
    $conn->close();
} else {
    echo "No user ID provided.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title><?php echo htmlspecialchars($username); ?>'s Profile</title>
    <style>
        .profile-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .profile-header img {
            border-radius: 50%;
            height: 150px;
            width: 150px;
        }

        .video-container {
            margin-top: 20px;
        }

        .video-container .video-item {
            position: relative;
            overflow: hidden;
            padding-top: 100%; /* 1:1 Aspect Ratio */
        }

        .video-container video {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }

        @media (max-width: 767.98px) {
            .video-container .video-item {
                padding-top: 75%; /* 4:3 Aspect Ratio for smaller screens */
            }
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav id="sidebar" class="col-md-3 col-lg-2 d-md-block bg-primary sidebar" style="padding-bottom: 100%; position:fixed;">
                <div class="sidebar mt-5 ms-4">
                    <h5>Menu</h5>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="index.php">
                               <span class="text-light"><i class="fas fa-home"></i> Home</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                              <span class="text-light"><i class="fas fa-user"></i> Profile</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                               <span class="text-light"><i class="fas fa-video"></i> Videos</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-4">
                <div class="profile-header mt-5">
                    <img src="<?php echo htmlspecialchars($profile_picture); ?>" alt="Profile Picture">
                    <h1><?php echo htmlspecialchars($username); ?></h1>
                    <p>Email: <?php echo htmlspecialchars($user['email']); ?></p>
                </div>
                <div class="video-container">
                    <h2>Videos</h2>
                    <div class="row">
                        <?php if (!empty($videos)) : ?>
                            <?php foreach ($videos as $video): ?>
                                <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-3">
                                    <div class="video-item">
                                        <video controls>
                                            <source src="<?php echo htmlspecialchars($video); ?>" type="video/mp4">
                                            Your browser does not support the video tag.
                                        </video>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <p>No videos available.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz4fnFO9oOZIb+5aH1uFiP73en6pZtU9MBl8pDmtN7sUO1Up8kcLJ6b/wh" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-pzjw8f+ua7Kw1TI3bS1xbpL8E78T+vA8IoF+0xozmDPSQwFzGkL1ry3D+jjG9Q68X" crossorigin="anonymous"></script>
</body>

</html>
