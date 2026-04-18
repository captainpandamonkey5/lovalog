<?php
session_start();
include 'check_auth.php';
requireAuth();
include 'database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_name     = trim($_POST['product_name'] ?? '');
    $product_category = trim($_POST['product_category'] ?? '');
    $price            = $_POST['product_price'] ?? 0;
    $quantity         = $_POST['quantity'] ?? 0;

    $stmt = $conn->prepare(
        "INSERT INTO products (product_name, product_category, product_price, quantity)
         VALUES (?, ?, ?, ?)"
    );
    $stmt->bind_param('ssdi', $product_name, $product_category, $price, $quantity);

    if ($stmt->execute()) {
        $_SESSION['success'] = 'added';
    } else {
        $_SESSION['error'] = 'Database error: ' . $stmt->error;
    }
    $stmt->close();
}

header('Location: index.php');
exit();
