<!-- post_edit.php -->
<?php
session_start();
include('../config/db_connect.php');
include('nav.php');

// Verify user exists
$user_query = mysqli_prepare($conn, "SELECT id FROM users WHERE id = ?");
mysqli_stmt_bind_param($user_query, "i", $_SESSION['user_id']);
mysqli_stmt_execute($user_query);
$user_result = mysqli_stmt_get_result($user_query);
$user = mysqli_fetch_assoc($user_result);

if (!$user) {
    header("Location: dashboard.php?error=2");
    exit();
}

// Get post ID from URL
$post_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$user_id = $_SESSION['user_id'];

// Fetch post with proper error handling
try {
    $stmt = mysqli_prepare($conn, "SELECT * FROM posts WHERE id = ? AND user_id = ?");
    if (!$stmt) {
        throw new Exception("Error preparing statement: " . mysqli_error($conn));
    }
    
    mysqli_stmt_bind_param($stmt, "ii", $post_id, $user_id);
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Error executing query: " . mysqli_error($conn));
    }
    
    $result = mysqli_stmt_get_result($stmt);
    $post = mysqli_fetch_assoc($result);
    
    if (!$post) {
        throw new Exception("Post not found or access denied");
    }
} catch (Exception $e) {
    $error = $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    
    if (!empty($title) && !empty($content)) {
        try {
            $stmt = mysqli_prepare($conn, "UPDATE posts SET title = ?, content = ? WHERE id = ? AND user_id = ?");
            if (!$stmt) {
                throw new Exception("Error preparing update statement: " . mysqli_error($conn));
            }
            
            mysqli_stmt_bind_param($stmt, "ssii", $title, $content, $post_id, $user_id);
            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception("Error updating post: " . mysqli_error($conn));
            }
            
            header("Location: dashboard.php?success=1");
            exit();
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    } else {
        $error = "Please fill in both title and content.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Post</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .post-form {
            max-width: 800px;
            margin: 0 auto;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        .alert {
            margin-bottom: 1rem;
        }
    </style>
</head>
<body class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Edit Post</h2>
        <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
    </div>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
        <div class="alert alert-success">Post updated successfully!</div>
    <?php elseif (isset($_GET['error']) && $_GET['error'] == 1): ?>
        <div class="alert alert-danger">Post ID is required.</div>
    <?php elseif (isset($_GET['error']) && $_GET['error'] == 2): ?>
        <div class="alert alert-danger">User not found.</div>
    <?php endif; ?>

    <div class="post-form">
        <form method="POST" action="">
            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($post['title']); ?>" required>
            </div>

            <div class="form-group">
                <label for="content">Content</label>
                <textarea class="form-control" id="content" name="content" rows="10" required><?php echo htmlspecialchars($post['content']); ?></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Update Post</button>
        </form>
    </div>
</body>
</html>