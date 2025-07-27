<?php
// Database configuration
$host = 'localhost';
$username = 'cohort';
$password = '1234';
$database = 'blog_platform_db';
$port = 3306;

try {
    // connect to the database
    $conn = mysqli_connect($host, $username, $password, $database, $port);

    // check connection
    if (!$conn) {
        throw new Exception('Connection error: ' . mysqli_connect_error());
    }

    // Set charset to utf8mb4 for proper character encoding
    mysqli_set_charset($conn, "utf8mb4");

    // Log successful connection
    error_log("Database connection established successfully");
} catch (mysqli_sql_exception $e) {
    // Handle MySQL specific errors
    error_log("MySQL Error: " . $e->getMessage());
    die('<div style="color: red; padding: 10px; border: 1px solid red; border-radius: 5px; margin: 10px; background-color: #ffebee;">
            <strong>✕ Error!</strong> Database connection failed. Please try again later.
          </div>');
} catch (Exception $e) {
    // Handle general exceptions
    error_log("Error: " . $e->getMessage());
    die('<div style="color: red; padding: 10px; border: 1px solid red; border-radius: 5px; margin: 10px; background-color: #ffebee;">
            <strong>✕ Error!</strong> An unexpected error occurred. Please try again later.
          </div>');
}
?>
