<?php
include 'check_auth.php';
requireAuth();
include 'database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_code = $_POST['new_code'];
    $confirm_code = $_POST['confirm_code'];

    if ($new_code === $confirm_code) {
        $hashed_code = password_hash($new_code, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = 'access_code'");
        $stmt->bind_param("s", $hashed_code);

        if ($stmt->execute()) {
            $success = "Access code updated successfully!";
        } else {
            $error = "Error updating access code.";
        }
        $stmt->close();
    } else {
        $error = "Codes do not match!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="Store Price Ledger - Manage your product inventory, prices, and quantities efficiently">
    <meta name="keywords" content="store, price ledger, inventory, products, price management">
    <meta name="author" content="CaptainPandaMonkey">
    <meta name="robots" content="noindex, nofollow">
    <title>Change Access Code - Store Price Ledger</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php include 'header.php'; ?>

    <div class="container mt-5">
        <div class="card mx-auto" style="max-width: 500px;">
            <div class="card-body">
                <h3 class="card-title mb-4">Change Access Code</h3>

                <?php if (isset($success)): ?>
                    <div class="alert alert-success"><?= $success ?></div>
                <?php endif; ?>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">New Access Code</label>
                        <input type="password" class="form-control" name="new_code" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Confirm Access Code</label>
                        <input type="password" class="form-control" name="confirm_code" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Update Code</button>
                    <a href="index.php" class="btn btn-secondary w-100 mt-2">Cancel</a>
                </form>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>
</body>

</html>