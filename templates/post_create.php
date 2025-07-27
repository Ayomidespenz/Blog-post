<!-- post_create.php -->
<?php
// session_start();
include ('../config/db_connect.php');
include ('../templates/auth.php');
include ('../templates/nav.php');

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $image = $_FILES['image'] ?? null;
    
    if (empty($title) && empty($content) && !$image) {
        $error = "Please fill in at least one field (title, content, or image).";
    } else {
        try {
            // Handle image upload if provided
            $image_url = null;
            if ($image && $image['error'] === UPLOAD_ERR_OK) {
                $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
                if (in_array($image['type'], $allowed_types)) {
                    $upload_dir = __DIR__ . '/../uploads/posts/';
                    if (!file_exists($upload_dir)) {
                        mkdir($upload_dir, 0777, true);
                    }
                    
                    $file_extension = pathinfo($image['name'], PATHINFO_EXTENSION);
                    $new_filename = uniqid() . '.' . $file_extension;
                    $upload_path = $upload_dir . $new_filename;
                    
                    if (move_uploaded_file($image['tmp_name'], $upload_path)) {
                        $image_url = '/uploads/posts/' . $new_filename;
                    } else {
                        throw new Exception("Failed to upload image");
                    }
                } else {
                    throw new Exception("Invalid image type. Only JPG, PNG, and GIF are allowed.");
                }
            }

            // Insert post
            $stmt = mysqli_prepare($conn, "INSERT INTO posts (user_id, title, content, image_url, created_at) VALUES (?, ?, ?, ?, NOW())");
            mysqli_stmt_bind_param($stmt, "isss", $user_id, $title, $content, $image_url);
            
            if (mysqli_stmt_execute($stmt)) {
                $post_id = mysqli_insert_id($conn);
                header("Location: dashboard.php?success=1");
                exit();
            } else {
                throw new Exception("Error creating post: " . mysqli_error($conn));
            }
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create New Post</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .post-form {
            max-width: 800px;
            margin: 0 auto;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        .image-preview {
            max-width: 100%;
            margin: 1rem 0;
        }
    </style>
</head>
<body class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Create New Post</h2>
        <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
    </div>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <div class="post-form">
        <form method="POST" action="" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">Title (Optional)</label>
                <input type="text" class="form-control" id="title" name="title">
            </div>

            <div class="form-group">
                <label for="content">Content (Optional)</label>
                <textarea class="form-control" id="content" name="content" rows="4"></textarea>
            </div>

            <div class="form-group">
                <label for="image">Image (Optional)</label>
                <input type="file" class="form-control" id="image" name="image" accept="image/jpeg,image/png,image/gif">
                <small class="form-text text-muted">Supported formats: JPG, PNG, GIF</small>
            </div>

            <button type="submit" class="btn btn-primary">Create Post</button>
        </form>
    </div>
</body>
</html>