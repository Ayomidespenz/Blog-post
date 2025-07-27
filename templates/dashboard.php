<?php
// dashboard.php - User dashboard
require_once '../templates/auth.php';
require_once '../config/db_connect.php';
require_once '../templates/nav.php';

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Handle like action
if (isset($_POST['like'])) {
    $post_id = filter_input(INPUT_POST, 'post_id', FILTER_VALIDATE_INT);
    if ($post_id) {
        try {
            $stmt = mysqli_prepare($conn, "INSERT IGNORE INTO likes (user_id, post_id) VALUES (?, ?)");
            mysqli_stmt_bind_param($stmt, "ii", $user_id, $post_id);
            mysqli_stmt_execute($stmt);
            $_SESSION['success'] = "Post liked successfully!";
        } catch (Exception $e) {
            $_SESSION['error'] = "Error liking post: " . $e->getMessage();
        }
    } else {
        $_SESSION['error'] = "Invalid post ID";
    }
    header("Location: dashboard.php");
    exit;
}

// Handle repost action
if (isset($_POST['repost'])) {
    $post_id = filter_input(INPUT_POST, 'post_id', FILTER_VALIDATE_INT);
    if ($post_id) {
        try {
            $stmt = mysqli_prepare($conn, "INSERT INTO reposts (user_id, original_post_id) VALUES (?, ?)");
            mysqli_stmt_bind_param($stmt, "ii", $user_id, $post_id);
            mysqli_stmt_execute($stmt);
            $_SESSION['success'] = "Post reposted successfully!";
        } catch (Exception $e) {
            $_SESSION['error'] = "Error reposting: " . $e->getMessage();
        }
    } else {
        $_SESSION['error'] = "Invalid post ID";
    }
    header("Location: dashboard.php");
    exit;
}

// Get user's posts
$query = "SELECT 
    posts.*, 
    users.username, 
    users.profile_picture,
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
JOIN users ON posts.user_id = users.id 
WHERE posts.user_id = ?
ORDER BY posts.created_at DESC";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$posts = mysqli_fetch_all($result, MYSQLI_ASSOC);
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

        .dashboard-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .post-card {
            margin-bottom: 2rem;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            overflow: hidden;
        }

        .post-header {
            padding: 1.5rem;
            border-bottom: 1px solid #e2e8f0;
        }

        .post-content {
            padding: 1.5rem;
        }

        .post-footer {
            padding: 1.5rem;
            border-top: 1px solid #e2e8f0;
            background: #f8fafc;
        }

        .profile-picture {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 0.75rem;
        }

        .post-image {
            width: 100%;
            height: auto;
            object-fit: cover;
        }

        .action-btn {
            margin-right: 1rem;
            color: var(--text-color);
            transition: color 0.2s;
        }

        .action-btn:hover {
            color: var(--primary-color);
        }

        .action-count {
            margin-left: 0.5rem;
            font-size: 0.9rem;
        }

        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
        }

        .empty-state i {
            font-size: 3rem;
            color: var(--secondary-color);
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>My Posts</h2>
            <a href="post_create.php" class="btn btn-primary">Create New Post</a>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($_SESSION['success']); ?></div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($_SESSION['error']); ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <?php if (empty($posts)): ?>
            <div class="empty-state">
                <i class="fas fa-feather-alt"></i>
                <h3>No posts yet</h3>
                <p class="text-muted">You haven't created any posts yet. Share your thoughts with the community!</p>
                <a href="post_create.php" class="btn btn-outline-primary">Create your first post</a>
            </div>
        <?php else: ?>
            <?php foreach ($posts as $post): ?>
                <div class="post-card">
                    <div class="post-header">
                        <div class="d-flex align-items-center">
                            <img src="<?php echo htmlspecialchars($post['profile_picture'] ?? '/uploads/profile_pictures/default-avatar.png'); ?>" 
                                 alt="Profile Picture" 
                                 class="profile-picture">
                            <div>
                                <h5 class="mb-0"><?php echo htmlspecialchars($post['username']); ?></h5>
                                <small class="text-muted"><?php echo date('F j, Y \a\t g:i A', strtotime($post['created_at'])); ?></small>
                            </div>
                        </div>
                    </div>

                    <div class="post-content">
                        <?php if (!empty($post['title'])): ?>
                            <h4><?php echo htmlspecialchars($post['title']); ?></h4>
                        <?php endif; ?>
                        
                        <?php if (!empty($post['content'])): ?>
                            <p><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
                        <?php endif; ?>

                        <?php if (!empty($post['image_url'])): ?>
                            <img src="<?php echo htmlspecialchars($post['image_url']); ?>" 
                                 alt="Post Image" 
                                 class="post-image">
                        <?php endif; ?>
                    </div>

                    <div class="post-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <form method="POST" action="" class="d-inline">
                                    <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                                    <button type="submit" name="like" class="btn btn-link action-btn">
                                        <i class="fas fa-heart"></i>
                                        <span class="action-count"><?php echo $post['likes_count']; ?></span>
                                    </button>
                                </form>

                                <form method="POST" action="" class="d-inline ms-3">
                                    <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                                    <button type="submit" name="repost" class="btn btn-link action-btn">
                                        <i class="fas fa-retweet"></i>
                                        <span class="action-count"><?php echo $post['reposts_count']; ?></span>
                                    </button>
                                </form>
                            </div>

                            <div class="d-flex align-items-center">
                                <a href="comments.php?id=<?php echo $post['id']; ?>" class="btn btn-link action-btn">
                                    <i class="fas fa-comments"></i>
                                    <span class="action-count"><?php echo $post['comments_count']; ?></span>
                                </a>

                                <form method="POST" action="post_delete.php" class="d-inline ms-3" onsubmit="return confirm('Are you sure you want to delete this post?')">
                                    <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                                    <button type="submit" class="btn btn-link text-danger action-btn">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
