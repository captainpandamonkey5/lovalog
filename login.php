<?php
session_start();
include 'database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Get user from database
    $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Verify password
        if (password_verify($password, $user['password'])) {
            $_SESSION['authenticated'] = true;
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $stmt->close();
            header("Location: index.php");
            exit();
        } else {
            $error = "Invalid username or password!";
        }
    } else {
        $error = "Invalid username or password!";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="Store Price Ledger - Secure access to manage products">
    <meta name="robots" content="noindex, nofollow">
    <title>Login - Store Price Ledger</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<style>
    body {
        background-color: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 100vh;
    }

    .login-card {
        background: white;
        border-radius: 15px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        padding: 40px;
        max-width: 400px;
        width: 100%;
    }

    .login-header {
        text-align: center;
        margin-bottom: 30px;
    }

    .login-header h1 {
        font-size: 24px;
        font-weight: 600;
        color: #212529;
        margin-bottom: 10px;
    }

    .login-header p {
        color: #6c757d;
        font-size: 14px;
    }

    .btn-theme {
        font-weight: 600;
        padding: 12px 30px;
        border-radius: 8px;
        background-color: #495057;
        color: white;
        border: none;
        width: 100%;
    }

    .btn-theme:hover {
        background-color: #212529;
    }

    .form-label {
        font-weight: 500;
        color: #495057;
        margin-bottom: 8px;
    }
</style>

<body>
    <div class="login-card">
        <div class="login-header">
            <h1>🔒 Welcome Back</h1>
            <p>Login to manage products</p>
        </div>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control form-control-lg" id="username"
                    name="username" placeholder="Enter your username" required autofocus>
            </div>

            <div class="mb-4">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control form-control-lg" id="password"
                    name="password" placeholder="Enter your password" required>
            </div>

            <button type="submit" class="btn btn-theme mb-3">Login</button>
        </form>

        <div class="text-center">
            <p class="text-muted mb-2">Don't have an account?</p>
            <a href="signup.php" class="text-decoration-none">Sign up here</a>
        </div>

        <div class="text-center mt-3">
            <a href="index.php" class="text-decoration-none text-muted small">View products without access</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>