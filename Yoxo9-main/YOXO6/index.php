<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Define if the user is logged in
$is_logged_in = isset($_SESSION['user_id']);

// Include database connection
require 'db_connection.php'; // Ensure the path is correct

// Fetch users and profile pictures
$sql_profile = "SELECT profile_picture FROM users";
$result_profile = $conn->query($sql_profile);
$profile_picture = [];

if ($result_profile) {
    while ($row = $result_profile->fetch_assoc()) {
        if (isset($row["profile_picture"])) {
            $profile_picture[] = $row["profile_picture"];
        }
    }
    $result_profile->free();
} else {
    echo "Error: " . $conn->error;
}

// Fetch videos
$sql_videos = "SELECT v.*, u.username FROM videos v JOIN users u ON v.user_id = u.id"; // Adjust based on your schema
$result_videos = $conn->query($sql_videos);
$videos = [];

if ($result_videos) {
    while ($row = $result_videos->fetch_assoc()) {
        $videos[] = $row;
    }
    $result_videos->free();
} else {
    echo "Error: " . $conn->error;
}

// Initialize current user picture
$current_user_picture = '';

if (isset($_SESSION['user_id']) && $is_logged_in) { // Check if user is logged in
    $user_id = $_SESSION['user_id']; // Get user_id from session

    // Prepare and execute SQL query to fetch profile picture
    $sql_current_user = "SELECT profile_picture FROM users WHERE id = ?";
    if ($stmt = $conn->prepare($sql_current_user)) {
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result_current_user = $stmt->get_result();

        if ($result_current_user && $row = $result_current_user->fetch_assoc()) {
            $current_user_picture = $row["profile_picture"];
        } else {
            // Handle case where user profile picture is not found
            $current_user_picture = 'default_profile_picture.png'; // Placeholder image
        }

        $stmt->close();
    } else {
        // Handle SQL preparation error
        error_log("SQL preparation failed: " . $conn->error);
    }
} else {
    // Handle case where user is not logged in or user_id is not set
    $current_user_picture = 'default_profile_picture.png'; // Placeholder image
}

//-----------------------------------------------------------------------------views




?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Yoxo6</title>
    <link rel="icon" type="image/x-icon" href="images/webprofile.jpg">
    <link rel="stylesheet" href="styles.css">

</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Left Column: Menu Icon and Sidebar -->
            <div class="col-md-2 bg-primary d-none d-md-block">
                <div class="sidebar p-3" style="position: fixed;">
                    <img src="images/webprofile.jpg" alt="Logo" style="height: 50px; width: 50px; border-radius: 50%;">
                    <span class="h4 text-light">Yoxo6</span>
                    <ul class="mt-3 list-unstyled">
                        <li><a class="text-light" href="#">Home</a></li>
                        <li><a class="text-light" href="#">Followers</a></li>
                        <li><a class="text-light" href="#">Something else here</a></li>
                        <li><a class="text-light" href="#">Separated link</a></li>
                    </ul>
                    <div class="mt-3">
                        <?php if ($is_logged_in) : ?>
                            <form action="logout.php" method="POST">
                                <button type="submit" class="btn btn-danger bg-dark w-100">Logout</button>
                            </form>
                            <div class="settings-link mt-2">
                                <a class="text-light" href="#">Settings</a>
                            </div>
                        <?php else : ?>
                            <a href="register.php" class="btn btn-light w-100">Sign Up</a>
                            <a href="login.php" class="btn btn-light mt-2 w-100">Sign In</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Offcanvas for Small Screens -->
            <div class="d-md-none">
                <button class="btn btn-primary mt-3 ms-3" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasSidebar" aria-controls="offcanvasSidebar">
                    <i class="fas fa-bars"></i>
                </button>

                <div class="offcanvas offcanvas-start bg-primary" tabindex="-1" id="offcanvasSidebar" aria-labelledby="offcanvasSidebarLabel">
                    <div class="offcanvas-header">
                        <h5 class="offcanvas-title text-light" id="offcanvasSidebarLabel">Yoxo6</h5>
                        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                    </div>
                    <div class="offcanvas-body d-flex flex-column justify-content-between">
                        <div>
                            <div class="text-center">
                                <img src="images/webprofile.jpg" alt="Logo" class="rounded-circle mb-3" style="height: 80px; width: 80px; border: 2px solid #fff;">
                            </div>
                            <ul class="list-unstyled">
                                <li class="mb-2"><a class="text-light" href="#">Home</a></li>
                                <li class="mb-2"><a class="text-light" href="#">Followers</a></li>
                                <li class="mb-2"><a class="text-light" href="#">Something else here</a></li>
                                <li><a class="text-light" href="#">Separated link</a></li>
                            </ul>
                            <div class="mt-4">
                                <?php if ($is_logged_in) : ?>
                                    <form action="logout.php" method="POST">
                                        <button type="submit" class="btn btn-danger bg-dark w-100 mt-3">Logout</button><br>
                                        <div>
                                            <a href="post-video.php" class="btn btn-danger bg-dark w-100">Post Video</a>
                                        </div>
                                    </form>
                                <?php else : ?>
                                    <a href="register.php" class="btn btn-light w-100">Sign Up</a>
                                    <a href="login.php" class="btn btn-light mt-2 w-100">Sign In</a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="settings-link mt-4">
                            <a class="text-light" href="#"><i class="fas fa-cog"></i> Settings</a>
                        </div>
                    </div>
                </div>
            </div>



            <!-- Middle Column: Video, Search Bar, Go Live Button -->
            <div class="col-md-8">
                <div class="container mt-3">
                    <div class="search-bar mb-3 d-flex align-items-center">
                        <input type="text" class="form-control search-input me-2" placeholder="Search...">
                        <a href="live.php" class="btn btn-secondary go-live-btn"><i class="fas fa-video"></i> Go Live</a>
                    </div>

                    <?php if (!empty($videos)) : ?>
                        <?php foreach ($videos as $video): ?>
                            <div class="video-container mb-4" style="position: relative;">
                                <video id="videoPlayer" width="100%" height="400" controls>
                                    <source src="<?php echo htmlspecialchars($video['video_path']); ?>" type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <p>
                                        Posted by:
                                        <a href="user_profile.php?id=<?php echo urlencode($video['user_id']); ?>" style="text-decoration: none; color: black; font-weight: bolder;">
                                            <?php echo htmlspecialchars($video['username']); ?>
                                        </a>
                                        <button class="follow-btn" data-user-id="<?php echo $video['user_id']; ?>" style="background: none; border: none; padding: 0;">
                                            <i class="fas fa-plus icon-small" style="color: #007bff; cursor: pointer;"></i>
                                        </button>
                                    </p>

                                    <div>
                                        <span style="margin-right: 10px;">
                                            <i class="fas fa-eye" style="color: gray; font-size: 18px;"></i>
                                           
                                        </span>
                                        <span style="margin-right: 10px; cursor: pointer;" onclick="toggleCommentsModal(<?php echo $video['id']; ?>)">
                                            <i class="fa-regular fa-comment-dots" style="color: gray; font-size: 18px;"></i>
                                        </span>
                                        <span style="margin-right: 10px;">
                                            <i class="fas fa-heart" style="color: gray; font-size: 18px;"></i>
                                        </span>
                                        <a href="#" class="btn btn-link">
                                            <i class="fas fa-share-alt" style="color: gray; font-size: 18px;"></i>
                                        </a>
                                    </div>
                                </div>



                                <!-- The pop-up comment box for each video -->
                                <div id="commentsModal<?php echo $video['id']; ?>" class="comments-modal">
                                    <div class="comments-modal-content">
                                        <span class="close" onclick="toggleCommentsModal(<?php echo $video['id']; ?>)">&times;</span>
                                        <h3>Add a Comment</h3>
                                        <form id="commentForm<?php echo $video['id']; ?>" method="post">
                                            <input type="hidden" name="video_id" value="<?php echo $video['id']; ?>">
                                            <textarea name="comment" placeholder="Write your comment here..." required></textarea>
                                            <button type="submit">Submit</button>
                                        </form>
                                        <div class="comments-section" id="commentsSection<?php echo $video['id']; ?>">
                                            <?php
                                            // Fetch and display comments for the specific video
                                            $query = "SELECT comments.comment, users.username FROM comments 
                                            JOIN users ON comments.user_id = users.id 
                                            WHERE video_id = ? ORDER BY comments.created_at DESC";
                                            $stmt = $conn->prepare($query);
                                            $stmt->bind_param("i", $video['id']);  // Bind the video ID to the query
                                            $stmt->execute();
                                            $result = $stmt->get_result();

                                            while ($row = $result->fetch_assoc()) {
                                                echo "<div class='comment-box'>";
                                                echo "<p><strong>" . htmlspecialchars($row['username']) . ":</strong> " . htmlspecialchars($row['comment']) . "</p>";
                                                echo "</div>";
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>


                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No videos available.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Right Column: User Profile, Post Button, and Card -->
            <div class="col-md-2 col-12">
                <?php if ($is_logged_in): ?>
                    <div class="profile-section p-3">
                        <a href="user_profile.php?id=<?php echo htmlspecialchars($user_id); ?>">
                            <img src="<?php echo htmlspecialchars($current_user_picture); ?>" alt="Profile Picture" class="current-user-profile" style="height: 50px; width: 50px; border-radius: 50%;">
                        </a>
                        <a href="post-video.php" class="btn btn-dark post-video-button mt-2 mb-5">Post Video</a>
                    </div>
                    <!-- Card Section -->
                    <div class="card mt-5 me-2" style="position: fixed;">
                        <div class="card-header">
                            Promote Program (PP)
                        </div>
                        <div class="card-body">
                            <p class="card-text text-dark"><b>Join the Yoxo6 Creator Program to promote your videos and unlock exciting monetization opportunities. Be part of something big!</b></p>
                            <a href="#" class="btn btn-dark">Join</a>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Optionally, you could add content here for users who are not logged in -->
                    <!-- Card Section -->
                    <div class="card mt-5 me-2" style="position: fixed;">
                        <div class="card-header">
                            Promote Program (PP)
                        </div>
                        <div class="card-body">
                            <p class="card-text text-dark">Join the Yoxo6 Creator Program to promote your videos and unlock exciting monetization opportunities. Be part of something big!</p>
                            <a href="#" class="btn btn-primary">Promote</a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

        </div>
    </div>




    <script>
        let peerConnection = new RTCPeerConnection(); // Create a peer connection
        let dataChannel = peerConnection.createDataChannel("comments"); // WebRTC DataChannel

        // Add event listener for WebRTC DataChannel messages
        dataChannel.onmessage = (event) => {
            const {
                video_id,
                comment,
                username
            } = JSON.parse(event.data);
            // Append the new comment to the comment section
            appendComment(video_id, username, comment);
        };

        function toggleCommentsModal(videoId) {
            const modal = document.getElementById('commentsModal' + videoId);
            if (modal.style.display === 'block') {
                modal.style.display = 'none';
            } else {
                modal.style.display = 'block';
                loadComments(videoId); // Load existing comments
            }
        }

        function appendComment(video_id, username, comment) {
            const commentsSection = document.getElementById('commentsSection' + video_id);
            const commentBox = document.createElement('div');
            commentBox.classList.add('comment-box');
            commentBox.innerHTML = `<p><strong>${username}:</strong> ${comment}</p>`;
            commentsSection.appendChild(commentBox);
        }

        // Function to submit comments via AJAX
        document.querySelectorAll('form[id^="commentForm"]').forEach((form) => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const video_id = this.querySelector('input[name="video_id"]').value;
                const comment = this.querySelector('textarea[name="comment"]').value;

                $.ajax({
                    url: 'add_comment.php',
                    type: 'POST',
                    data: {
                        video_id: video_id,
                        comment: comment
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            // Keep the modal open and clear the comment field
                            form.reset();
                            // Immediately append the new comment to the section
                            appendComment(video_id, response.username, comment);

                            // Send the new comment through WebRTC DataChannel (for other users)
                            const data = {
                                video_id: video_id,
                                comment: comment,
                                username: response.username
                            };
                            dataChannel.send(JSON.stringify(data)); // Broadcast the comment to all users
                        } else {
                            console.log('Error: ' + response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log('AJAX error: ' + error);
                    }
                });
            });
        });

        // Function to append the comment directly to the comment section
        function appendComment(video_id, username, comment) {
            const commentsSection = document.getElementById('commentsSection' + video_id);
            const commentBox = document.createElement('div');
            commentBox.classList.add('comment-box');
            commentBox.innerHTML = `<p><strong>${username}:</strong> ${comment}</p>`;
            commentsSection.appendChild(commentBox);
        }


        // Function to load comments
        function loadComments(video_id) {
            $.ajax({
                url: 'load_comments.php',
                type: 'GET',
                data: {
                    video_id: video_id
                },
                success: function(data) {
                    $('#commentsSection' + video_id).html(data); // Inject comments dynamically
                },
                error: function(xhr, status, error) {
                    console.log('Error loading comments: ' + error);
                }
            });
        }
        //----------------------views
     
    </script>



    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
</body>

</html>
