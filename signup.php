<?php
session_start();
include 'database.php';

// Optional: Require existing admin to create new accounts (more secure)
// Uncomment these lines if you want only logged-in admins to create new accounts
// include 'check_auth.php';
// requireAuth();

$signup_enabled = true; // Set to false to disable public signups

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validation
    if (empty($username) || empty($password)) {
        $error = "All fields are required!";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters!";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match!";
    } else {
        // Check if username already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error = "Username already exists!";
            $stmt->close();
        } else {
            $stmt->close();

            // Hash password and insert new user
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (username, password, created_at) VALUES (?, ?, NOW())");
            $stmt->bind_param("ss", $username, $hashed_password);

            if ($stmt->execute()) {
                $stmt->close();
                $success = "Account created successfully! You can now login.";
                // Optionally auto-login the user
                // $_SESSION['authenticated'] = true;
                // $_SESSION['username'] = $username;
                // header("Location: index.php");
                // exit();
            } else {
                $error = "Error creating account. Please try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="Create a new admin account for Store Price Ledger">
    <meta name="robots" content="noindex, nofollow">
    <title>Sign Up - Store Price Ledger</title>
    <link rel="icon" type="image/png" href="assets/lovalog-favicon.svg">
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

    .signup-card {
        background: white;
        border-radius: 15px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        padding: 40px;
        max-width: 450px;
        width: 100%;
    }

    .signup-header {
        text-align: center;
        margin-bottom: 30px;
    }

    .signup-header h1 {
        font-size: 24px;
        font-weight: 600;
        color: #212529;
        margin-bottom: 10px;
    }

    .signup-header p {
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

    .password-requirements {
        font-size: 12px;
        color: #6c757d;
        margin-top: 5px;
    }
</style>

<body>
    <div class="signup-card">
        <div class="signup-header">
            <h1>✨ Create Account</h1>
            <p>Sign up to manage products</p>
        </div>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <?= htmlspecialchars($error) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($success)): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?= htmlspecialchars($success) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if ($signup_enabled): ?>
            <form method="POST">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control form-control-lg" id="username"
                        name="username" placeholder="Choose a username"
                        value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>"
                        required autofocus>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control form-control-lg" id="password"
                        name="password" placeholder="Create a password" required>
                    <div class="password-requirements">
                        Must be at least 6 characters
                    </div>
                </div>

                <div class="mb-4">
                    <label for="confirm_password" class="form-label">Confirm Password</label>
                    <input type="password" class="form-control form-control-lg" id="confirm_password"
                        name="confirm_password" placeholder="Confirm your password" required>
                </div>

                <button type="submit" class="btn btn-theme mb-3">Create Account</button>
            </form>

            <div class="text-center">
                <p class="text-muted mb-2">Already have an account?</p>
                <a href="login.php" class="text-decoration-none">Login here</a>
            </div>
        <?php else: ?>
            <div class="alert alert-warning">
                Public signups are currently disabled. Please contact an administrator.
            </div>
            <div class="text-center mt-3">
                <a href="login.php" class="text-decoration-none">Back to Login</a>
            </div>
        <?php endif; ?>

        <div class="text-center mt-3">
            <a href="index.php" class="text-decoration-none text-muted small">View products without access</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>