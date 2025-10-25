<?php
include 'database.php';
$result = $conn->query("SELECT * FROM products");

$search = "";
if (isset($_GET['query'])) {
    $search = $conn->real_escape_string($_GET['query']);
}

if ($search != "") {
    $sql = "SELECT * FROM products WHERE product_name LIKE '%$search%'";
} else {
    $sql = "SELECT * FROM products";
}

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Store Price Ledger</title>
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
        <h1 class="text-center">Products</h1>
        <a href="/create.php" class="btn btn-theme">Add Product</a>
    </div>

    <div class="container mt-5">
        <form method="GET" class="input-group">
            <input type="text" name="query" class="form-control" value="<?= htmlspecialchars($search) ?>" placeholder="Search Products" aria-label="Search Products">
            <button type="submit" class="btn btn-theme">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                    <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0" />
                </svg>
            </button>
        </form>
    </div>

    <div class="container" style="margin-top: 50px;">
        <table class="table table-bordered table-hover">
            <tr>
                <th>ID</th>
                <th>Product Name</th>
                <th>Product Price</th>
                <th>Quantity</th>
                <th>Actions</th>
            </tr>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td style="width: 550px;"><?= $row['product_name'] ?></td>
                            <td><?= number_format($row['product_price']) ?></td>
                            <td><?= $row['quantity'] ?></td>
                            <td>
                                <a href="/update.php?id=<?= $row['id'] ?>" class="btn btn-theme btn-sm">Update</a>
                                <a href="/delete.php?id=<?= $row['id'] ?>" class="btn btn-theme btn-sm">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center">No Products Found 😔</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>

</html>