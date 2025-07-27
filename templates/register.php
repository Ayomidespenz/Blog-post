<?php
session_start();
include('../config/db_connect.php');
include('../templates/nav.php');

$username = $email = $password = '';
$profile_picture = null;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $profile_picture = $_FILES['profile_picture'] ?? null;

    if (empty($username) || empty($email) || empty($password)) {
        $errors[] = "All fields are required.";
    } elseif (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters long.";
    } else {
        try {
            // Check if username or email already exists
            $stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE username = ? OR email = ?");
            mysqli_stmt_bind_param($stmt, "ss", $username, $email);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($result) > 0) {
                $errors[] = "Username or email already exists.";
            } else {
                // Handle profile picture upload if provided
                $profile_picture_url = null;
                if ($profile_picture && $profile_picture['error'] === UPLOAD_ERR_OK) {
                    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
                    if (in_array($profile_picture['type'], $allowed_types)) {
                        $upload_dir = __DIR__ . '/../uploads/profile_pictures/';
                        if (!file_exists($upload_dir)) {
                            mkdir($upload_dir, 0777, true);
                        }
                        
                        $file_extension = pathinfo($profile_picture['name'], PATHINFO_EXTENSION);
                        $new_filename = uniqid() . '.' . $file_extension;
                        $upload_path = $upload_dir . $new_filename;
                        
                        if (move_uploaded_file($profile_picture['tmp_name'], $upload_path)) {
                            $profile_picture_url = '/uploads/profile_pictures/' . $new_filename;
                        } else {
                            throw new Exception("Failed to upload profile picture");
                        }
                    } else {
                        throw new Exception("Invalid image type. Only JPG, PNG, and GIF are allowed.");
                    }
                }

                // Hash the password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                // Insert new user
                $stmt = mysqli_prepare($conn, "INSERT INTO users (username, email, password_hash, profile_picture, created_at) VALUES (?, ?, ?, ?, NOW())");
                mysqli_stmt_bind_param($stmt, "ssss", $username, $email, $hashed_password, $profile_picture_url);

                if (mysqli_stmt_execute($stmt)) {
                    header('Location: login.php?success=1');
                    exit();
                } else {
                    throw new Exception("Database error: " . mysqli_error($conn));
                }
            }
        } catch (Exception $e) {
            $errors[] = "Registration failed: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Daily-Gist</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .register-form {
            max-width: 500px;
            margin: 0 auto;
            padding: 2rem;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .profile-picture-preview {
            max-width: 200px;
            margin: 1.5rem 0;
            border-radius: 8px;
        }
        .btn-primary {
            width: 100%;
            padding: 0.75rem;
            font-weight: 600;
        }
        .text-center {
            margin-top: 1.5rem;
        }
        .text-muted {
            font-size: 0.9rem;
        }
        .custom-file-label {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 0.5rem 1rem;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="register-form">
            <?php if (!empty($errors)): ?>
                <?php foreach ($errors as $error): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endforeach; ?>
            <?php endif; ?>

            <div class="text-center mb-4">
                <h2 class="mb-2">Create Account</h2>
                <p class="text-muted">Join Daily-Gist and start sharing your ideas</p>
            </div>

            <form method="POST" action="" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                    <small class="form-text text-muted">Minimum 6 characters</small>
                </div>

                <div class="form-group">
                    <label for="profile_picture">Profile Picture (Optional)</label>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" id="profile_picture" name="profile_picture" accept="image/jpeg,image/png,image/gif">
                        <label class="custom-file-label" for="profile_picture">Choose file</label>
                    </div>
                    <small class="form-text text-muted">Supported formats: JPG, PNG, GIF</small>
                </div>

                <button type="submit" class="btn btn-primary">Create Account</button>

                <div class="text-center">
                    <p class="text-muted">Already have an account? <a href="login.php">Sign in</a></p>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Update file input label when a file is selected
        document.querySelector('.custom-file-input').addEventListener('change', function(e) {
            const fileName = e.target.files[0]?.name || 'Choose file';
            e.target.nextElementSibling.innerHTML = fileName;
        });
    </script>
</body>
</html>
