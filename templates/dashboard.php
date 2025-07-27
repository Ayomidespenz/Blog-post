<?php
// dashboard.php
session_start();
include ('../templates/auth.php');
include ('../config/db_connect.php');
include ('../templates/nav.php');

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Handle like action
if (isset($_POST['like'])) {
    $post_id = (int) $_POST['post_id'];
    $stmt = mysqli_prepare($conn, "INSERT IGNORE INTO likes (user_id, post_id) VALUES (?, ?)");
    mysqli_stmt_bind_param($stmt, "ii", $user_id, $post_id);
    mysqli_stmt_execute($stmt);
    header("Location: dashboard.php");
    exit();
}

// Handle repost action
if (isset($_POST['repost'])) {
    $post_id = (int) $_POST['post_id'];
    $stmt = mysqli_prepare($conn, "INSERT INTO reposts (user_id, original_post_id) VALUES (?, ?)");
    mysqli_stmt_bind_param($stmt, "ii", $user_id, $post_id);
    mysqli_stmt_execute($stmt);
    header("Location: dashboard.php");
    exit();
}

// Fetch user's posts with likes, comments, and reposts counts
$query = "SELECT 
    posts.*, 
    (
        SELECT COUNT(*) FROM likes WHERE post_id = posts.id AND user_id = ?
    ) as has_liked,
    (
        SELECT COUNT(*) FROM likes WHERE post_id = posts.id
    ) as likes_count,
    (
        SELECT COUNT(*) FROM comments WHERE post_id = posts.id
    ) as comments_count,
    (
        SELECT COUNT(*) FROM reposts WHERE original_post_id = posts.id
    ) as reposts_count
FROM posts 
WHERE user_id = ? 
ORDER BY created_at DESC";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "ii", $user_id, $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Get total counts for dashboard stats
$total_posts = mysqli_num_rows($result);

$stmt = mysqli_prepare($conn, "SELECT COUNT(*) as total_likes FROM likes WHERE post_id IN (SELECT id FROM posts WHERE user_id = ?)");
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$total_likes = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))['total_likes'];

$stmt = mysqli_prepare($conn, "SELECT COUNT(*) as total_reposts FROM reposts WHERE original_post_id IN (SELECT id FROM posts WHERE user_id = ?)");
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$total_reposts = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))['total_reposts'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Daily-Gist</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .dashboard-card {
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .post-card {
            margin-bottom: 20px;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .post-header {
            padding: 15px;
            border-bottom: 1px solid #eee;
        }
        .post-content {
            padding: 15px;
        }
        .post-image {
            width: 100%;
            height: 300px;
            object-fit: cover;
        }
        .post-actions {
            padding: 15px;
            border-top: 1px solid #eee;
        }
        .action-btn {
            margin-right: 10px;
        }
        .action-count {
            margin-left: 5px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Welcome, <?php echo htmlspecialchars($username); ?></h2>
            <a href="logout.php" class="btn btn-danger">Logout</a>
        </div>

        <div class="row mb-4">
            <div class="col-md-4">
                <div class="dashboard-card">
                    <h4>Total Posts: <?php echo $total_posts; ?></h4>
                </div>
            </div>
            <div class="col-md-4">
                <div class="dashboard-card">
                    <h4>Total Likes: <?php echo $total_likes; ?></h4>
                </div>
            </div>
            <div class="col-md-4">
                <div class="dashboard-card">
                    <h4>Total Reposts: <?php echo $total_reposts; ?></h4>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <?php if ($total_posts > 0): ?>
                    <?php while ($post = mysqli_fetch_assoc($result)): ?>
                        <div class="post-card">
                            <div class="post-header">
                                <div class="d-flex align-items-center">
                                    <img src="<?php echo $post['profile_picture'] ? htmlspecialchars($post['profile_picture']) : '/uploads/default-avatar.png'; ?>" 
                                         alt="<?php echo htmlspecialchars($post['username']); ?>" 
                                         class="rounded-circle" 
                                         style="width: 40px; height: 40px; object-fit: cover; margin-right: 10px;">
                                    <div>
                                        <h5 class="mb-0"><?php echo htmlspecialchars($post['username']); ?></h5>
                                        <small class="text-muted"><?php echo $post['created_at']; ?></small>
                                    </div>
                                </div>
                            </div>

                            <?php if ($post['image_url']): ?>
                                <img src="<?php echo htmlspecialchars($post['image_url']); ?>" 
                                     alt="Post image" 
                                     class="post-image">
                            <?php endif; ?>

                            <div class="post-content">
                                <h3><?php echo htmlspecialchars($post['title']); ?></h3>
                                <p><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
                            </div>

                            <div class="post-actions">
                                <form method="POST" action="" class="d-inline">
                                    <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                                    <button type="submit" name="like" class="btn btn-outline-primary action-btn">
                                        <i class="fas fa-heart"><?php echo $post['has_liked'] ? ' ❤️' : ''; ?></i>
                                        <span class="action-count"><?php echo $post['likes_count']; ?></span>
                                    </button>
                                </form>

                                <form method="POST" action="" class="d-inline">
                                    <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                                    <button type="submit" name="repost" class="btn btn-outline-success action-btn">
                                        <i class="fas fa-retweet"></i>
                                        <span class="action-count"><?php echo $post['reposts_count']; ?></span>
                                    </button>
                                </form>

                                <a href="comments.php?post_id=<?php echo $post['id']; ?>" class="btn btn-outline-info action-btn">
                                    <i class="fas fa-comments"></i>
                                    <span class="action-count"><?php echo $post['comments_count']; ?></span>
                                </a>

                                <a href="post_edit.php?id=<?php echo $post['id']; ?>" class="btn btn-outline-warning action-btn">
                                    <i class="fas fa-edit"></i>
                                </a>

                                <form method="POST" action="delete_post.php" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this post?')">
                                    <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                                    <button type="submit" class="btn btn-outline-danger action-btn">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="alert alert-info">
                        <p>You haven't created any posts yet. <a href="post_create.php">Create your first post</a>!</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="m-5">
        <a href="post_create.php" class="btn btn-primary">Create New Post</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
