<?php
include 'database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // handle form submission
    $product_name = $_POST['product_names'];
    $price = $_POST['product_price'];
    $quantity = $_POST['quantity'];

    $conn->query("INSERT INTO products (product_name, product_price, quantity)
    VALUES ('$product_name', '$price', '$quantity')");
    header("Location: index.php");
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Store Price Ledger - Update Products</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Optional: Bootstrap JS (for modals, dropdowns, etc.) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>
    <div class="container">

    </div>
</body>

</html>