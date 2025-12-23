<?php
include 'database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // handle form submission
    $product_name = $_POST['product_name'];  // Fixed: removed the 's'
    $price = $_POST['product_price'];
    $quantity = $_POST['quantity'];

    // Use prepared statements to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO products (product_name, product_price, quantity) VALUES (?, ?, ?)");
    $stmt->bind_param("sdi", $product_name, $price, $quantity);

    if ($stmt->execute()) {
        $stmt->close();
        header("Location: index.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
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

<style>
    .btn-theme {
        font-weight: bold;
        margin-top: 10px;
        border-radius: 10px;
        background-color: gray;
        color: white;
        border: none !important;
    }

    .btn-theme:hover {
        font-weight: bold;
        margin-top: 10px;
        border-radius: 10px;
        background-color: #000000ff;
        color: white;
        border: none !important;
    }
</style>

<body>
    <div class="container">
        <h1>Welcome to the Database</h1>
        <h1 class="text-center">Add New Product</h1>
    </div>

    <div class="container mt-5">
        <form action="create.php" method="POST">

            <div class="form-floating mb-3">
                <input type="text" class="form-control" id="product_name" name="product_name"
                    placeholder="Product Name" required>
                <label for="product_name">Product Name</label>
            </div>

            <div class="form-floating mb-3">
                <input type="number" class="form-control" id="product_price" name="product_price"
                    placeholder="Product Price" step="0.01" min="0" required>
                <label for="product_price">Product Price</label>
            </div>

            <div class="form-floating mb-3">
                <input type="number" class="form-control" id="quantity" name="quantity"
                    placeholder="Quantity" min="0" value="0" required>
                <label for="quantity">Quantity</label>
            </div>

            <button type="submit" class="btn btn-theme">Add Product</button>
            <button type="button" class="btn btn-theme" onclick="document.querySelector('form').reset();">Clear</button>
        </form>
    </div>
</body>

</html>