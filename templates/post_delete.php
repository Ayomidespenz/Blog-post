<!-- post_delete.php -->
<?php
session_start();
include('../config/db_connect.php');
include('../templates/nav.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Get post ID from URL
$post_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Verify post belongs to user
$stmt = mysqli_prepare($conn, "SELECT id FROM posts WHERE id = ? AND user_id = ?");
mysqli_stmt_bind_param($stmt, "ii", $post_id, $_SESSION['user_id']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) === 0) {
    header('Location: dashboard.php');
    exit();
}

// Delete post
$stmt = mysqli_prepare($conn, "DELETE FROM posts WHERE id = ? AND user_id = ?");
mysqli_stmt_bind_param($stmt, "ii", $post_id, $_SESSION['user_id']);
mysqli_stmt_execute($stmt);

// Redirect back to dashboard
header('Location: dashboard.php');
exit();
?>