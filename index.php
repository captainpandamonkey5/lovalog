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
    .table-container {
        background: white;
        border-radius: 15px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .table {
        margin-bottom: 0;
    }

    .table-header {
        background-color: #495057;
        color: white;
    }

    .table-header th {
        border: none;
        padding: 15px;
        font-weight: 600;
        font-size: 14px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .table tbody tr {
        transition: background-color 0.2s ease;
    }

    .table tbody tr:hover {
        background-color: #f8f9fa;
    }

    .table td {
        padding: 15px;
        vertical-align: middle;
        border-color: #e9ecef;
    }

    .product-name {
        font-weight: 500;
        color: #212529;
    }

    .btn-group-actions {
        display: flex;
        gap: 8px;
        justify-content: center;
        flex-wrap: wrap;
    }

    .btn-group-actions .btn {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 6px 12px;
        font-size: 13px;
        font-weight: 500;
        border-radius: 6px;
        transition: all 0.2s ease;
    }

    .btn-group-actions .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }

    .btn-group-actions .btn svg {
        flex-shrink: 0;
    }

    .empty-state {
        padding: 20px;
    }

    @media (max-width: 768px) {
        .btn-text {
            display: none;
        }

        .btn-group-actions .btn {
            padding: 6px 10px;
        }

        .table-header th {
            font-size: 12px;
            padding: 12px 8px;
        }

        .table td {
            padding: 12px 8px;
            font-size: 14px;
        }
    }

    @media (max-width: 576px) {
        .btn-group-actions {
            flex-direction: column;
        }

        .btn-group-actions .btn {
            width: 100%;
            justify-content: center;
        }

        .btn-text {
            display: inline;
        }
    }
</style>

<body>

    <?php include 'header.php'; ?>

    <div class="container mt-2">
        <form method="GET" class="input-group">
            <input type="text" name="query" class="form-control" style="height: 50px; margin-top: 10px;" value="<?= htmlspecialchars($search) ?>" placeholder="Search Products" aria-label="Search Products">
            <button type="submit" class="btn btn-theme">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="25" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                    <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0" />
                </svg>
            </button>
        </form>
    </div>

    <div class="container-fluid px-3" style="margin-top: 50px; margin-bottom: 50px;">
        <div class="table-container">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-header">
                        <tr>
                            <th scope="col" style="width: 80px;">ID</th>
                            <th scope="col">Product Name</th>
                            <th scope="col" style="width: 150px;">Price</th>
                            <th scope="col" style="width: 120px;">Quantity</th>
                            <th scope="col" style="width: 200px;" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['id']) ?></td>
                                    <td class="product-name"><?= htmlspecialchars($row['product_name']) ?></td>
                                    <td>₱<?= number_format($row['product_price'], 2) ?></td>
                                    <td><?= htmlspecialchars($row['quantity']) ?></td>
                                    <td class="text-center">
                                        <div class="btn-group-actions">
                                            <a href="update.php?id=<?= $row['id'] ?>" class="btn btn-primary btn-sm">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-pencil" viewBox="0 0 16 16">
                                                    <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325" />
                                                </svg>
                                                <span class="btn-text">Edit</span>
                                            </a>
                                            <a href="delete.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm"
                                                onclick="return confirm('Are you sure you want to delete this product?');">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                                    <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z" />
                                                    <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z" />
                                                </svg>
                                                <span class="btn-text">Delete</span>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center empty-state">
                                    <div class="py-5">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor" class="bi bi-inbox text-muted mb-3" viewBox="0 0 16 16">
                                            <path d="M4.98 4a.5.5 0 0 0-.39.188L1.54 8H6a.5.5 0 0 1 .5.5 1.5 1.5 0 1 0 3 0A.5.5 0 0 1 10 8h4.46l-3.05-3.812A.5.5 0 0 0 11.02 4zm-1.17-.437A1.5 1.5 0 0 1 4.98 3h6.04a1.5 1.5 0 0 1 1.17.563l3.7 4.625a.5.5 0 0 1 .106.374l-.39 3.124A1.5 1.5 0 0 1 14.117 13H1.883a1.5 1.5 0 0 1-1.489-1.314l-.39-3.124a.5.5 0 0 1 .106-.374z" />
                                        </svg>
                                        <p class="text-muted mb-3">No products found</p>
                                        <a href="create.php" class="btn btn-theme">Add Your First Product</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>
</body>

</html>