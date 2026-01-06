<?php
include 'database.php';

// Pagination settings
$records_per_page = 10;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$current_page = max(1, $current_page);
$offset = ($current_page - 1) * $records_per_page;

// Search handling
$search = "";
if (isset($_GET['query'])) {
    $search = $conn->real_escape_string($_GET['query']);
}

// Build base query
if ($search != "") {
    $base_sql = "SELECT * FROM products WHERE product_name LIKE '%$search%'";
    $count_sql = "SELECT COUNT(*) as total FROM products WHERE product_name LIKE '%$search%'";
} else {
    $base_sql = "SELECT * FROM products ORDER BY product_name ASC";
    $count_sql = "SELECT COUNT(*) as total FROM products";
}

// Get total records
$total_result = $conn->query($count_sql);
$total_row = $total_result->fetch_assoc();
$total_records = $total_row['total'];
$total_pages = ceil($total_records / $records_per_page);

// Get records for current page
$sql = $base_sql . " LIMIT $offset, $records_per_page";
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

    .pagination .page-link {
        color: #6c757d;
        border: 1px solid #dee2e6;
    }

    .pagination .page-item.active .page-link {
        background-color: #495057;
        border-color: #495057;
        color: white;
    }

    .pagination .page-link:hover {
        background-color: #f8f9fa;
        color: #495057;
    }

    .pagination .page-item.disabled .page-link {
        color: #6c757d;
        pointer-events: none;
        background-color: #fff;
        border-color: #dee2e6;
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

    html,
    body {
        height: 100%;
    }

    body {
        display: flex;
        flex-direction: column;
    }

    main {
        flex: 1 0 auto;
    }

    footer {
        flex-shrink: 0;
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

    <div class="container-fluid px-5" style="margin-top: 50px; margin-bottom: 50px;">
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

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
                <nav aria-label="Product pagination" class="mt-4 mb-3">
                    <ul class="pagination justify-content-center">
                        <!-- Previous Button -->
                        <li class="page-item <?= ($current_page <= 1) ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= $current_page - 1 ?><?= $search ? '&query=' . urlencode($search) : '' ?>" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>

                        <?php
                        // Show page numbers
                        $start_page = max(1, $current_page - 2);
                        $end_page = min($total_pages, $current_page + 2);

                        // First page
                        if ($start_page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=1<?= $search ? '&query=' . urlencode($search) : '' ?>">1</a>
                            </li>
                            <?php if ($start_page > 2): ?>
                                <li class="page-item disabled"><span class="page-link">...</span></li>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                            <li class="page-item <?= ($i == $current_page) ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?><?= $search ? '&query=' . urlencode($search) : '' ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>

                        <?php
                        // Last page
                        if ($end_page < $total_pages): ?>
                            <?php if ($end_page < $total_pages - 1): ?>
                                <li class="page-item disabled"><span class="page-link">...</span></li>
                            <?php endif; ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?= $total_pages ?><?= $search ? '&query=' . urlencode($search) : '' ?>"><?= $total_pages ?></a>
                            </li>
                        <?php endif; ?>

                        <!-- Next Button -->
                        <li class="page-item <?= ($current_page >= $total_pages) ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= $current_page + 1 ?><?= $search ? '&query=' . urlencode($search) : '' ?>" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                    </ul>
                </nav>

                <!-- Showing info -->
                <div class="text-center text-muted mb-3">
                    <small>
                        Showing <?= min($offset + 1, $total_records) ?> to
                        <?= min($offset + $records_per_page, $total_records) ?> of
                        <?= $total_records ?> products
                        <?= $search ? '(filtered from search)' : '' ?>
                    </small>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <?php if (isset($_GET['success'])): ?>
        <script>
            alert('<?php
                    if ($_GET['success'] == 'added') echo 'Product has been added successfully!';
                    elseif ($_GET['success'] == 'updated') echo 'Product has been updated successfully!';
                    elseif ($_GET['success'] == 'deleted') echo 'Product has been deleted successfully!';
                    ?>');
        </script>
    <?php endif; ?>

</body>

</html>