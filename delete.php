<?php
session_start();
include 'check_auth.php';
requireAuth();
include 'database.php';

// check
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = $_GET['id'];


// sql injection prevention
$stmt = $conn->prepare("DELETE FROM  products WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    $stmt->close();
    $conn->close();
    header("Location: index.php");
    exit();
} else {
    echo "Error deleting product: " . $stmt->error;
    $stmt->close();
    $conn->close();
}
