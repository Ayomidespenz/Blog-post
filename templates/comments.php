
<!-- comments.php -->
<?php
include ('../templates/auth.php');
include ('../config/db_connect.php');

if (!isset($_GET['post_id'])) {
    header("Location: dashboard.php");
    exit();
}

$post_id = (int) $_GET['post_id'];
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Handle new comment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $comment = trim($_POST['comment']);
    if (!empty($comment)) {
        try {
            $stmt = mysqli_prepare($conn, "INSERT INTO comments (user_id, post_id, content, created_at) VALUES (?, ?, ?, NOW())");
            mysqli_stmt_bind_param($stmt, "iis", $user_id, $post_id, $comment);
            if (mysqli_stmt_execute($stmt)) {
                echo '<div class="alert alert-success">Comment added successfully!</div>';
            } else {
                throw new Exception("Error adding comment: " . mysqli_error($conn));
            }
        } catch (Exception $e) {
            echo '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
        }
    }
}

// Fetch post
$post_result = mysqli_query($conn, "SELECT p.*, u.username FROM posts p JOIN users u ON p.user_id = u.id WHERE p.id = $post_id");
$post = mysqli_fetch_assoc($post_result);

if (!$post) {
    die("Post not found.");
}

// Fetch comments
$comments_query = "SELECT c.*, u.username FROM comments c 
                  JOIN users u ON c.user_id = u.id 
                  WHERE c.post_id = ? 
                  ORDER BY c.created_at DESC";
$stmt = mysqli_prepare($conn, $comments_query);
mysqli_stmt_bind_param($stmt, "i", $post_id);
mysqli_stmt_execute($stmt);
$comments_result = mysqli_stmt_get_result($stmt);
$comments = mysqli_fetch_all($comments_result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Comments</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
    <a href="dashboard.php" class="btn btn-secondary mb-3">Back to Dashboard</a>
    <h2><?php echo htmlspecialchars($post['title']); ?></h2>
    <p class="text-muted">by <?php echo htmlspecialchars($post['username']); ?> on <?php echo $post['created_at']; ?></p>
    <p><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>

    <hr>
    <h4>Comments</h4>
    <form method="POST" action="">
        <div class="mb-3">
            <textarea name="comment" class="form-control" placeholder="Add a comment..." required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>

    <div class="mt-4">
        <?php foreach ($comments as $comment): ?>
            <div class="border rounded p-2 mb-2">
                <strong><?php echo htmlspecialchars($comment['username']); ?></strong>
                <span class="text-muted small">on <?php echo $comment['created_at']; ?></span>
                <p><?php echo nl2br(htmlspecialchars($comment['content'])); ?></p>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>