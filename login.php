<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $access_code = $_POST['access_code'];

    $correct_code = "99QLZMVZALI";

    if ($access_code === $correct_code) {
        $_SESSION['authenticated'] = true;
        header('Location: index.php');

        exit();
    } else {
        $error = 'Invalid access code. Please try again.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access Code - Store Price Ledger</title>
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
</style>

<body>
    <div class="login-card">
        <div class="login-header">
            <h1>🔒 Access Required</h1>
            <p>Enter access code to manage products</p>
        </div>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <input type="password" class="form-control form-control-lg"
                    name="access_code" placeholder="Enter Access Code" required autofocus>
            </div>
            <button type="submit" class="btn btn-theme">Submit</button>
        </form>

        <div class="text-center mt-3">
            <a href="index.php" class="text-decoration-none text-muted">View products without access</a>
        </div>
    </div>
</body>

</html>