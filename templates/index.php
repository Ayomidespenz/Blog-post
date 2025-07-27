<?php
// index.php - Public Landing Page
session_start();
include ('../config/db_connect.php');
include ('../templates/nav.php');

// Fetch latest posts with author info and counts
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
ORDER BY posts.created_at DESC 
LIMIT 10";
$posts = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daily -Gist - Your Space for Ideas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #1da1f2;
            --secondary-color: #1985a1;
            --text-color: #2d3748;
            --light-bg: #f7fafc;
        }

        body {
            font-family: 'Segoe UI', system-ui, -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: var(--light-bg);
            color: var(--text-color);
        }

        .hero-section {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 8rem 0;
            text-align: center;
        }

        .hero-section h1 {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
        }

        .hero-section p {
            font-size: 1.25rem;
            margin-bottom: 2rem;
        }

        .post-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            margin-bottom: 2rem;
            transition: transform 0.2s;
        }

        .post-card:hover {
            transform: translateY(-5px);
        }

        .post-header {
            padding: 1.5rem;
            border-bottom: 1px solid #e2e8f0;
        }

        .post-content {
            padding: 1.5rem;
        }

        .post-actions {
            padding: 1rem;
            border-top: 1px solid #e2e8f0;
            background: #f8fafc;
        }

        .action-btn {
            margin-right: 1rem;
        }

        .profile-picture {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 1rem;
        }

        .post-image {
            width: 100%;
            height: 300px;
            object-fit: cover;
            border-radius: 0 0 12px 12px;
        }

        .cta-button {
            background: var(--primary-color);
            color: white;
            padding: 1rem 2rem;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: transform 0.2s;
        }

        .cta-button:hover {
            transform: translateY(-2px);
        }

        .stats {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            margin-bottom: 2rem;
        }

        .stats-item {
            text-align: center;
            padding: 1rem;
        }

        .stats-number {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary-color);
        }

        .footer {
            background: var(--primary-color);
            color: white;
            padding: 3rem 0;
            margin-top: 4rem;
        }

        .feature-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            padding: 1.5rem;
        }

        .about-image-container {
            margin-top: 2rem;
        }

        @media (max-width: 768px) {
            .hero-section h1 {
                font-size: 2.5rem;
            }
            
            .post-card {
                margin-bottom: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <h1>Welcome to Daily-Gist</h1>
            <p>Your platform for sharing ideas, stories, and insights</p>
            <a href="register.php" class="cta-button">Start Writing</a>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="py-5 bg-light">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h2 class="mb-4">About Daily-Gist</h2>
                    <p class="lead mb-4">Welcome to Daily-Gist - your platform for sharing ideas, stories, and insights. We believe everyone has a story to tell and valuable perspectives to share.</p>
                    
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="feature-card p-4 bg-white rounded shadow-sm">
                                <i class="fas fa-users fa-2x text-primary mb-3"></i>
                                <h4>Community</h4>
                                <p>Connect with like-minded individuals and build meaningful relationships through shared interests.</p>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="feature-card p-4 bg-white rounded shadow-sm">
                                <i class="fas fa-pen fa-2x text-primary mb-3"></i>
                                <h4>Express Yourself</h4>
                                <p>Share your thoughts, experiences, and knowledge with a global audience.</p>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="feature-card p-4 bg-white rounded shadow-sm">
                                <i class="fas fa-comments fa-2x text-primary mb-3"></i>
                                <h4>Engage</h4>
                                <p>Participate in meaningful discussions and get feedback on your posts.</p>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="feature-card p-4 bg-white rounded shadow-sm">
                                <i class="fas fa-globe fa-2x text-primary mb-3"></i>
                                <h4>Global Reach</h4>
                                <p>Your content can reach a worldwide audience and make a real impact.</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-6">
                    <div class="about-image-container">
                        <img src="https://images.unsplash.com/photo-1512314889306-cc270ca1418a?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" 
                             alt="People sharing ideas" 
                             class="img-fluid rounded shadow-lg">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="stats text-center">
                        <div class="stats-item">
                            <div class="stats-number"><?php 
                                $result = mysqli_query($conn, "SELECT COUNT(*) as total FROM posts");
                                $row = mysqli_fetch_assoc($result);
                                echo number_format($row['total']);
                            ?></div>
                            <div class="stats-label">Total Posts</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stats text-center">
                        <div class="stats-item">
                            <div class="stats-number"><?php 
                                $result = mysqli_query($conn, "SELECT COUNT(DISTINCT user_id) as total FROM posts");
                                $row = mysqli_fetch_assoc($result);
                                echo number_format($row['total']);
                            ?></div>
                            <div class="stats-label">Active Writers</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stats text-center">
                        <div class="stats-item">
                            <div class="stats-number"><?php 
                                $result = mysqli_query($conn, "SELECT COUNT(*) as total FROM comments");
                                $row = mysqli_fetch_assoc($result);
                                echo number_format($row['total']);
                            ?></div>
                            <div class="stats-label">Discussions</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Latest Posts Section -->
    <section id="posts" class="py-5">
        <div class="container">
            <h2 class="mb-4">Latest Posts</h2>
            
            <div class="row">
                <?php while ($post = mysqli_fetch_assoc($posts)): ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="post-card">
                            <div class="post-header">
                                <div class="d-flex align-items-center">
                                    <img src="<?php echo $post['profile_picture'] ? htmlspecialchars($post['profile_picture']) : '/uploads/default-avatar.png'; ?>" 
                                         alt="<?php echo htmlspecialchars($post['username']); ?>" 
                                         class="profile-picture">
                                    <div>
                                        <h5 class="mb-0"><?php echo htmlspecialchars($post['username']); ?></h5>
                                        <small class="text-muted"><?php echo date('F j, Y', strtotime($post['created_at'])); ?></small>
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
                                <p><?php echo nl2br(htmlspecialchars(substr($post['content'], 0, 150) . '...')); ?></p>
                            </div>

                            <div class="post-actions">
                                <a href="comments.php?post_id=<?php echo $post['id']; ?>" class="action-btn">
                                    <i class="fas fa-comments"></i> <?php echo $post['comments_count']; ?>
                                </a>
                                <a href="#" class="action-btn">
                                    <i class="fas fa-heart"></i> <?php echo $post['likes_count']; ?>
                                </a>
                                <a href="#" class="action-btn">
                                    <i class="fas fa-retweet"></i> <?php echo $post['reposts_count']; ?>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5>Daily-Gist</h5>
                    <p>Your platform for sharing ideas, stories, and insights</p>
                </div>
                <div class="col-md-4">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="#features" class="text-white">Features</a></li>
                        <li><a href="#posts" class="text-white">Latest Posts</a></li>
                        <li><a href="login.php" class="text-white">Login</a></li>
                        <li><a href="register.php" class="text-white">Register</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5>Connect With Us</h5>
                    <div class="social-links">
                        <a href="#" class="text-white me-3"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-white me-3"><i class="fab fa-facebook"></i></a>
                        <a href="#" class="text-white me-3"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-white"><i class="fab fa-linkedin"></i></a>
                    </div>
                </div>
            </div>
            <hr>
            <div class="text-center mt-3">
                <p class="mb-0">&copy; <?php echo date('Y'); ?> Dail-Gist. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
