<?php
session_start();
include 'check_auth.php';
requireAuth();
include 'database.php';

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = $_GET['id'];

// Fetch product data
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();
$stmt->close();

// If product not found, redirect
if (!$product) {
    header("Location: index.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_name = $_POST['product_name'];
    $product_category = $_POST['product_category'];
    $price = $_POST['product_price'];
    $quantity = $_POST['quantity'];

    $stmt = $conn->prepare("UPDATE products SET product_name = ?, product_category = ?, product_price = ?, quantity = ? WHERE id = ?");
    $stmt->bind_param("ssddi", $product_name, $product_category, $price, $quantity, $id);

    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        $_SESSION['success'] = 'updated';
        header("Location: index.php");
        exit();
    } else {
        $error = "Error updating product: " . $stmt->error;
    }
}
?>

<style>
    .select-wrapper {
        position: relative;
    }

    .select-wrapper::after {
        content: '▾';
        position: absolute;
        right: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #6c757d;
        font-size: 16px;
        pointer-events: none;
    }

    .select-wrapper select {
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
    }
</style>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="Store Price Ledger - Manage your product inventory, prices, and quantities efficiently">
    <meta name="keywords" content="store, price ledger, inventory, products, price management">
    <meta name="author" content="CaptainPandaMonkey">
    <link rel="icon" type="image/png" href="assets/lovalog-favicon.svg">
    <meta name="robots" content="noindex, nofollow">
    <title>Store Price Ledger - Update Products</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>

<style>
    html,
    body {
        height: 100%;
    }

    body {
        display: flex;
        flex-direction: column;
        background-color: #f8f9fa;
    }

    .content-wrapper {
        flex: 1 0 auto;
    }

    footer {
        flex-shrink: 0;
    }

    .form-card {
        background: white;
        border-radius: 15px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        padding: 30px;
        margin: 20px auto;
        max-width: 600px;
    }

    @media (min-width: 768px) {
        .form-card {
            padding: 40px;
            margin: 50px auto;
        }
    }

    .form-header {
        margin-bottom: 30px;
    }

    .form-header h1 {
        font-size: 24px;
        font-weight: 600;
        color: #212529;
        margin-bottom: 10px;
    }

    @media (min-width: 768px) {
        .form-header h1 {
            font-size: 28px;
        }
    }

    .form-header p {
        color: #6c757d;
        font-size: 14px;
        margin: 0;
    }

    @media (min-width: 768px) {
        .form-header p {
            font-size: 16px;
        }
    }

    .form-floating>label {
        color: #6c757d;
    }

    .form-control:focus {
        border-color: #495057;
        box-shadow: 0 0 0 0.2rem rgba(73, 80, 87, 0.25);
    }

    .btn-theme {
        font-weight: 600;
        padding: 12px 30px;
        border-radius: 8px;
        background-color: #495057;
        color: white;
        border: none;
        transition: all 0.3s ease;
        width: 100%;
    }

    .btn-theme:hover {
        background-color: #212529;
        color: white;
    }

    .btn-cancel {
        font-weight: 600;
        padding: 12px 30px;
        border-radius: 8px;
        background-color: #e9ecef;
        color: #495057;
        border: none;
        transition: all 0.3s ease;
        width: 100%;
        text-decoration: none;
        display: inline-block;
        text-align: center;
    }

    .btn-cancel:hover {
        background-color: #dee2e6;
        color: #212529;
    }

    .button-group {
        display: flex;
        flex-direction: column;
        gap: 15px;
        margin-top: 30px;
    }

    @media (min-width: 576px) {
        .button-group {
            flex-direction: row;
        }

        .btn-theme {
            width: auto;
            flex-grow: 1;
        }

        .btn-cancel {
            width: auto;
        }
    }

    .alert {
        border-radius: 8px;
    }
</style>

<body>
    <?php include 'header.php'; ?>

    <div class="content-wrapper">
        <div class="container-fluid px-3">
            <div class="form-card">
                <div class="form-header">
                    <h1>Update Product</h1>
                    <p>Edit product details below</p>
                </div>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <form action="update.php?id=<?= $id ?>" method="POST">
                    <div class="form-floating mb-4">
                        <input type="text" class="form-control" id="product_name" name="product_name"
                            placeholder="Product Name" value="<?= htmlspecialchars($product['product_name']) ?>" required>
                        <label for="product_name">Product Name</label>
                    </div>

                    <div class="select-wrapper">
                        <div class="form-floating mb-4">
                            <select class="form-control" id="product_category" name="product_category" required>
                                <option value="" disabled selected>Select A Category</option>
                                <?php
                                $categories = ['Beverages', 'Snacks', 'Dairy', 'Meat', 'Vegetables', 'Fruits', 'Bakery', 'Frozen Foods', 'Condiments', 'Others'];
                                foreach ($categories as $cat): ?>
                                    <option value="<?= $cat ?>" <?= $product['product_category'] == $cat ? 'selected' : '' ?>>
                                        <?= $cat ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label for="product_category">Product Category</label>
                        </div>
                    </div>

                    <div class="form-floating mb-4">
                        <input type="number" class="form-control" id="product_price" name="product_price"
                            placeholder="Product Price" step="0.01" min="0"
                            value="<?= htmlspecialchars($product['product_price']) ?>" required>
                        <label for="product_price">Product Price</label>
                    </div>

                    <div class="form-floating mb-4">
                        <input type="number" class="form-control" id="quantity" name="quantity"
                            placeholder="Quantity" min="0"
                            value="<?= htmlspecialchars($product['quantity']) ?>" required>
                        <label for="quantity">Quantity</label>
                    </div>

                    <div class="button-group">
                        <button type="submit" class="btn btn-theme">Update Product</button>
                        <a href="index.php" class="btn-cancel">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>
    <?php if (isset($_SESSION['success'])): ?>
        <div id="toast-notif" style="
        position: fixed;
        bottom: 30px;
        left: 30px;
        background: #212529;
        color: white;
        padding: 14px 20px;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 500;
        box-shadow: 0 4px 20px rgba(0,0,0,0.2);
        z-index: 9999;
        opacity: 0;
        transform: translateY(20px);
        transition: all 0.4s ease;
    ">
            <?php
            if ($_SESSION['success'] == 'added') echo '✅ Product has been added successfully!';
            elseif ($_SESSION['success'] == 'updated') echo '✅ Product has been updated successfully!';
            elseif ($_SESSION['success'] == 'deleted') echo '✅ Product has been deleted successfully!';
            ?>
        </div>

        <script>
            const toast = document.getElementById('toast-notif');
            setTimeout(() => {
                toast.style.opacity = '1';
                toast.style.transform = 'translateY(0)';
            }, 100);
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateY(20px)';
            }, 3500);
            setTimeout(() => toast.remove(), 4000);
        </script>
    <?php endif; ?>
</body>

</html>