<!-- login.php -->
<?php
session_start();
include('../config/db_connect.php');
include('../templates/nav.php');

$email = $password = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        $errors[] = "Both fields are required.";
    } else {
        try {
            $stmt = mysqli_prepare($conn, "SELECT id, username, password_hash, profile_picture FROM users WHERE email = ? LIMIT 1");
            mysqli_stmt_bind_param($stmt, "s", $email);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $user = mysqli_fetch_assoc($result);

            if ($user && password_verify($password, $user['password_hash'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['profile_picture'] = $user['profile_picture'];
                header('Location: dashboard.php');
                exit();
            } else {
                $errors[] = "Invalid credentials.";
            }
        } catch (Exception $e) {
            $errors[] = "Login failed: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Daily -Gist</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .login-form {
            max-width: 400px;
            margin: 0 auto;
            padding: 2rem;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .profile-picture {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            margin: 0 auto 1.5rem;
            object-fit: cover;
        }
        .btn-primary {
            width: 100%;
            padding: 0.75rem;
            font-weight: 600;
        }
        .text-center {
            margin-top: 1.5rem;
        }
        .text-muted {
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="login-form">
            <?php if (!empty($errors)): ?>
                <?php foreach ($errors as $error): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endforeach; ?>
            <?php endif; ?>

            <div class="text-center mb-4">
                <h2 class="mb-2">Welcome Back</h2>
                <p class="text-muted">Sign in to continue to Daily-Gist</p>
            </div>

            <form method="POST" action="">
                <?php if (isset($_SESSION['profile_picture'])): ?>
                    <img src="<?php echo htmlspecialchars($_SESSION['profile_picture'] ?? '/uploads/default-avatar.png'); ?>" 
                         alt="Profile Picture" 
                         class="profile-picture">
                <?php endif; ?>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>

                <button type="submit" class="btn btn-primary">Sign In</button>

                <div class="text-center">
                    <p class="text-muted">Don't have an account? <a href="register.php">Sign up</a></p>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
