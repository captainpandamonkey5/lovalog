<?php
session_start();
include 'check_auth.php';
requireAuth();
include 'database.php';

$id = (int)($_POST['id'] ?? 0);
if ($id === 0) {
    header('Location: index.php');
    exit();
}

$id = (int)$_GET['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_name     = trim($_POST['product_name'] ?? '');
    $product_category = trim($_POST['product_category'] ?? '');
    $price            = $_POST['product_price'] ?? 0;
    $quantity         = $_POST['quantity'] ?? 0;

    $stmt = $conn->prepare(
        "UPDATE products SET product_name = ?, product_category = ?, product_price = ?, quantity = ? WHERE id = ?"
    );
    $stmt->bind_param('ssdii', $product_name, $product_category, $price, $quantity, $id);

    if ($stmt->execute()) {
        $_SESSION['success'] = 'updated';
    } else {
        $_SESSION['error'] = 'Database error: ' . $stmt->error;
    }
    $stmt->close();
}

header('Location: index.php');
exit();
