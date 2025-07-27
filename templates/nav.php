<?php
// Check if user is logged in
$logged_in = isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
?>
<style>
    :root {
        --nav-bg: #1da1f2;
        --nav-bg-dark: #1583d6;
        --nav-text: white;
        --nav-hover: #1583d6;
        --nav-active: #0d73c4;
    }

    .navbar {
        background-color: var(--nav-bg) !important;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        padding: 0.8rem 0;
    }

    .navbar-brand {
        font-weight: 700;
        font-size: 1.5rem;
        color: var(--nav-text) !important;
    }

    .nav-link {
        color: var(--nav-text) !important;
        font-weight: 500;
        padding: 0.5rem 1rem !important;
        transition: all 0.2s ease;
    }

    .nav-link:hover {
        color: var(--nav-hover) !important;
        background-color: rgba(255,255,255,0.1);
        border-radius: 4px;
    }

    .nav-link.active {
        color: var(--nav-active) !important;
        background-color: rgba(255,255,255,0.15);
    }

    .btn-outline-light {
        color: var(--nav-text) !important;
        border-color: rgba(255,255,255,0.5) !important;
        padding: 0.35rem 1.2rem !important;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .btn-outline-light:hover {
        background-color: var(--nav-hover);
        border-color: var(--nav-hover) !important;
        color: black !important;
    }

    .navbar-toggler {
        border: none;
        padding: 0.5rem;
    }

    .navbar-toggler-icon {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba(255, 255, 255, 0.5)' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
    }

    .navbar-toggler:focus {
        box-shadow: none;
        outline: none;
    }

    @media (max-width: 991.98px) {
        .navbar-collapse {
            background-color: var(--nav-bg-dark);
            padding: 1rem;
            border-radius: 8px;
            margin-top: 0.5rem;
        }
    }
</style>

<nav class="navbar navbar-expand-lg navbar-dark fixed-top">
    <div class="container">
        <a class="navbar-brand" href="index.php">Daily-Gist</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Home</a>
                </li>
                <?php if ($logged_in): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="post_create.php">Create Post</a>
                    </li>
                <?php endif; ?>
                <li class="nav-item">
                    <a class="nav-link" href="https://news.google.com" target="_blank" rel="noopener noreferrer">
                        News
                    </a>
                </li>
            </ul>
            <ul class="navbar-nav">
                <?php if ($logged_in): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="profile.php">
                            <?php 
                            $profile_pic = isset($_SESSION['profile_picture']) ? $_SESSION['profile_picture'] : 'default-avatar.png';
                            echo '<img src="' . htmlspecialchars($profile_pic) . '" alt="Profile" class="rounded-circle" style="width: 30px; height: 30px; object-fit: cover; margin-right: 8px;">';
                            echo htmlspecialchars($_SESSION['username']);
                            ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-outline-light" href="logout.php">Logout</a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="btn btn-outline-light" href="login.php">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-outline-light ms-2" href="register.php">Register</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>


<div style="padding-top: 76px;"></div>
