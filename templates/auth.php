<!-- auth.php -->
<?php
// auth.php - Authentication middleware
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    // Clear any session data to prevent session fixation
    session_destroy();
    session_start();
    
    // Set a flash message
    $_SESSION['error'] = 'Please log in to access this page.';
    
    // Redirect to login page
    header('Location: login.php');
    exit;
}

// Regenerate session ID to prevent session fixation
if (!isset($_SESSION['regenerated'])) {
    session_regenerate_id(true);
    $_SESSION['regenerated'] = true;
}
?>