<?php
// profile.php - User profile management
require_once '../config/db_connect.php';
require_once '../templates/auth.php';
require_once '../templates/nav.php';

// Get user's profile information
$user_id = $_SESSION['user_id'];
$query = "SELECT username, email, profile_picture, COALESCE(bio, '') as bio 
          FROM users 
          WHERE id = ? 
          LIMIT 1";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

// Handle profile updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim(filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING));
    $bio = trim(filter_input(INPUT_POST, 'bio', FILTER_SANITIZE_STRING));
    $profile_pic = $_FILES['profile_picture'] ?? null;
    
    try {
        // Validate inputs
        if (empty($username)) {
            throw new Exception("Username is required.");
        }
        if (strlen($username) > 50) {
            throw new Exception("Username must be less than 50 characters.");
        }
        if (strlen($bio) > 500) {
            throw new Exception("Bio must be less than 500 characters.");
        }

        // Handle profile picture upload if provided
        if ($profile_pic && $profile_pic['error'] === UPLOAD_ERR_OK) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            $max_size = 5 * 1024 * 1024; // 5MB
            
            if (!in_array($profile_pic['type'], $allowed_types)) {
                throw new Exception("Invalid image type. Only JPG, PNG, and GIF are allowed.");
            }
            if ($profile_pic['size'] > $max_size) {
                throw new Exception("Image size must be less than 5MB.");
            }

            $upload_dir = __DIR__ . '/../uploads/profile_pictures/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $file_extension = strtolower(pathinfo($profile_pic['name'], PATHINFO_EXTENSION));
            $new_filename = uniqid() . '.' . $file_extension;
            $upload_path = $upload_dir . $new_filename;
            
            if (move_uploaded_file($profile_pic['tmp_name'], $upload_path)) {
                $profile_picture = '/uploads/profile_pictures/' . $new_filename;
            } else {
                throw new Exception("Failed to upload profile picture");
            }
        } else {
            $profile_picture = $user['profile_picture'];
        }

        // Update user information
        $update_query = "UPDATE users SET username = ?, bio = ?, profile_picture = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $update_query);
        mysqli_stmt_bind_param($stmt, "sssi", $username, $bio, $profile_picture, $user_id);
        
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['username'] = $username;
            $_SESSION['profile_picture'] = $profile_picture;
            $_SESSION['success'] = "Profile updated successfully!";
            header("Location: profile.php");
            exit;
        } else {
            throw new Exception("Error updating profile: " . mysqli_error($conn));
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Daily-Gist</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #1da1f2;
            --secondary-color: #1583d6;
            --text-color: #2d3748;
            --light-bg: #f7fafc;
        }

        body {
            font-family: 'Segoe UI', system-ui, -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: var(--light-bg);
            color: var(--text-color);
        }

        .profile-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 2rem;
        }

        .profile-header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .profile-picture {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            margin: 0 auto 1.5rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .profile-stats {
            margin-top: 3rem;
            padding: 2rem;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }

        .stat-item {
            text-align: center;
            padding: 1rem;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: var(--primary-color);
        }

        .stat-label {
            color: #64748b;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="profile-container">
        <div class="profile-header">
            <img src="<?php echo htmlspecialchars($user['profile_picture'] ?? '/uploads/profile_pictures/default-avatar.png'); ?>" 
                 alt="Profile Picture" 
                 class="profile-picture">
            <h2><?php echo htmlspecialchars($user['username']); ?></h2>
            <p class="text-muted"><?php echo htmlspecialchars($user['bio']); ?></p>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($_SESSION['success']); ?></div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" action="" enctype="multipart/form-data" class="needs-validation" novalidate>
            <div class="form-group">
                <label for="username" class="form-label">Username</label>
                <input type="text" 
                       class="form-control" 
                       id="username" 
                       name="username" 
                       value="<?php echo htmlspecialchars($user['username']); ?>" 
                       required 
                       maxlength="50">
                <div class="invalid-feedback">
                    Please enter a valid username.
                </div>
            </div>

            <div class="form-group">
                <label for="bio" class="form-label">Bio</label>
                <textarea class="form-control" 
                          id="bio" 
                          name="bio" 
                          rows="4" 
                          maxlength="500"><?php echo htmlspecialchars($user['bio']); ?></textarea>
                <div class="form-text">
                    Max 500 characters
                </div>
            </div>

            <div class="form-group">
                <label for="profile_picture" class="form-label">Profile Picture</label>
                <input type="file" 
                       class="form-control" 
                       id="profile_picture" 
                       name="profile_picture" 
                       accept="image/jpeg,image/png,image/gif">
                <div class="form-text">
                    JPG, PNG, GIF up to 5MB
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Update Profile</button>
        </form>

        <div class="profile-stats">
            <div class="row">
                <div class="col-md-4 stat-item">
                    <div class="stat-number"><?php 
                        $posts_query = "SELECT COUNT(*) FROM posts WHERE user_id = ?";
                        $stmt = mysqli_prepare($conn, $posts_query);
                        mysqli_stmt_bind_param($stmt, "i", $user_id);
                        mysqli_stmt_execute($stmt);
                        $result = mysqli_stmt_get_result($stmt);
                        echo mysqli_fetch_row($result)[0];
                    ?></div>
                    <div class="stat-label">Posts</div>
                </div>
                <div class="col-md-4 stat-item">
                    <div class="stat-number"><?php 
                        $likes_query = "SELECT COUNT(*) FROM likes WHERE user_id = ?";
                        $stmt = mysqli_prepare($conn, $likes_query);
                        mysqli_stmt_bind_param($stmt, "i", $user_id);
                        mysqli_stmt_execute($stmt);
                        $result = mysqli_stmt_get_result($stmt);
                        echo mysqli_fetch_row($result)[0];
                    ?></div>
                    <div class="stat-label">Likes</div>
                </div>
                <div class="col-md-4 stat-item">
                    <div class="stat-number"><?php 
                        $comments_query = "SELECT COUNT(*) FROM comments WHERE user_id = ?";
                        $stmt = mysqli_prepare($conn, $comments_query);
                        mysqli_stmt_bind_param($stmt, "i", $user_id);
                        mysqli_stmt_execute($stmt);
                        $result = mysqli_stmt_get_result($stmt);
                        echo mysqli_fetch_row($result)[0];
                    ?></div>
                    <div class="stat-label">Comments</div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Form validation
        (function () {
            'use strict'
            var forms = document.querySelectorAll('.needs-validation')
            Array.prototype.slice.call(forms)
                .forEach(function (form) {
                    form.addEventListener('submit', function (event) {
                        if (!form.checkValidity()) {
                            event.preventDefault()
                            event.stopPropagation()
                        }
                        form.classList.add('was-validated')
                    }, false)
                })
        })()
    </script>
</body>
</html>
