<?php
session_start();
include 'database.php';
include 'check_auth.php';

$is_authenticated = isAuthenticated();

// Pagination settings
$records_per_page = 10;
$current_page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($current_page - 1) * $records_per_page;

// Search handling
$search = isset($_GET['query']) ? $conn->real_escape_string($_GET['query']) : "";

// Build queries
$where_clause = !empty($search) ? "WHERE product_name LIKE '%$search%'" : "";
$base_sql = "SELECT * FROM products $where_clause ORDER BY product_name ASC";
$count_sql = "SELECT COUNT(*) as total FROM products $where_clause";

// Get pagination data
$total_result = $conn->query($count_sql);
$total_records = $total_result->fetch_assoc()['total'];
$total_pages = ceil($total_records / $records_per_page);

// Get records
$result = $conn->query("$base_sql LIMIT $offset, $records_per_page");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="Store Price Ledger - Manage your product inventory">
    <meta name="robots" content="noindex, nofollow">
    <title>Store Price Ledger</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        :root {
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-600: #4b5563;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html,
        body {
            height: 100%;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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

        .table-container {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            margin: 30px auto;
            max-width: 1200px;
        }

        .table-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
        }

        .table-header th {
            border: none;
            padding: 18px 15px;
            font-weight: 700;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }

        .table tbody tr {
            transition: all 0.3s ease;
            border-bottom: 1px solid var(--gray-100);
        }

        .table td {
            padding: 16px 15px;
            vertical-align: middle;
            color: var(--gray-600);
        }

        .product-name {
            font-weight: 600;
            color: #1f2937;
        }

        .price {
            font-weight: 700;
            color: var(--primary);
            font-size: 15px;
        }

        .btn-group-actions {
            display: flex;
            gap: 8px;
            justify-content: center;
        }

        .btn-group-actions .btn {
            padding: 8px 14px;
            font-size: 13px;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.2s ease;
            border: none;
        }

        .btn-primary-mod {
            background: var(--primary);
            color: white;
        }

        .btn-primary-mod:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(99, 102, 241, 0.3);
        }

        .btn-danger-mod {
            background: #ef4444;
            color: white;
        }

        .btn-danger-mod:hover {
            background: #dc2626;
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(239, 68, 68, 0.3);
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #9ca3af;
        }

        .empty-state svg {
            margin-bottom: 20px;
            opacity: 0.5;
        }

        .pagination {
            gap: 4px;
        }

        .pagination .page-link {
            color: var(--primary);
            border: 1px solid var(--gray-200);
            border-radius: 6px;
            margin: 0 2px;
        }

        .pagination .page-link:hover {
            background: var(--gray-100);
            border-color: var(--primary);
        }

        .pagination .page-item.active .page-link {
            background: var(--primary);
            border-color: var(--primary);
        }

        @media (max-width: 768px) {
            .table-container {
                margin: 15px;
            }

            .btn-group-actions {
                flex-wrap: wrap;
            }

            .table-header th {
                padding: 12px 8px;
                font-size: 11px;
            }

            .table td {
                padding: 12px 8px;
                font-size: 14px;
            }
        }
    </style>
</head>

<body>
    <?php include 'header.php'; ?>

    <main class="container-fluid px-3 px-md-5">
        <div class="table-container">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-header">
                        <tr>
                            <th style="width: 80px;">ID</th>
                            <th>Product Name</th>
                            <th style="width: 150px;">Price</th>
                            <?php if ($is_authenticated): ?>
                                <th style="width: 120px;">Qty</th>
                                <th style="width: 200px;" class="text-center">Actions</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td>#<?= htmlspecialchars($row['id']) ?></td>
                                    <td class="product-name"><?= htmlspecialchars($row['product_name']) ?></td>
                                    <td class="price">₱<?= number_format($row['product_price'], 2) ?></td>
                                    <?php if ($is_authenticated): ?>
                                        <td><span class="badge bg-light text-dark"><?= htmlspecialchars($row['quantity']) ?></span></td>
                                        <td class="text-center">
                                            <div class="btn-group-actions">
                                                <a href="update.php?id=<?= $row['id'] ?>" class="btn btn-primary-mod btn-sm">✏️ Edit</a>
                                                <a href="delete.php?id=<?= $row['id'] ?>" class="btn btn-danger-mod btn-sm" onclick="return confirm('Delete this product?');">🗑️ Delete</a>
                                            </div>
                                        </td>
                                    <?php endif; ?>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="<?= $is_authenticated ? '5' : '3' ?>" class="empty-state">
                                    <p style="font-size: 18px; margin-bottom: 10px;">📦 No products found</p>
                                    <?php if ($is_authenticated): ?>
                                        <a href="create.php" class="btn btn-primary-mod mt-3">+ Add Product</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($total_pages > 1): ?>
                <nav class="mt-4 mb-3">
                    <ul class="pagination justify-content-center">
                        <li class="page-item <?= $current_page <= 1 ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= $current_page - 1 ?><?= $search ? '&query=' . urlencode($search) : '' ?>">← Prev</a>
                        </li>

                        <?php
                        $start = max(1, $current_page - 2);
                        $end = min($total_pages, $current_page + 2);

                        if ($start > 1): ?>
                            <li class="page-item"><a class="page-link" href="?page=1<?= $search ? '&query=' . urlencode($search) : '' ?>">1</a></li>
                            <?php if ($start > 2): ?>
                                <li class="page-item disabled"><span class="page-link">...</span></li>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php for ($i = $start; $i <= $end; $i++): ?>
                            <li class="page-item <?= $i == $current_page ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?><?= $search ? '&query=' . urlencode($search) : '' ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($end < $total_pages): ?>
                            <?php if ($end < $total_pages - 1): ?>
                                <li class="page-item disabled"><span class="page-link">...</span></li>
                            <?php endif; ?>
                            <li class="page-item"><a class="page-link" href="?page=<?= $total_pages ?><?= $search ? '&query=' . urlencode($search) : '' ?>"><?= $total_pages ?></a></li>
                        <?php endif; ?>

                        <li class="page-item <?= $current_page >= $total_pages ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= $current_page + 1 ?><?= $search ? '&query=' . urlencode($search) : '' ?>">Next →</a>
                        </li>
                    </ul>
                </nav>

                <div class="text-center text-muted mb-3">
                    <small>Showing <?= min($offset + 1, $total_records) ?>–<?= min($offset + $records_per_page, $total_records) ?> of <?= $total_records ?> products</small>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <?php include 'footer.php'; ?>

    <?php if (isset($_GET['success'])): ?>
        <script>
            const messages = {
                'added': 'Product added successfully! ✅',
                'updated': 'Product updated successfully! ✅',
                'deleted': 'Product deleted successfully! ✅'
            };
            alert(messages['<?= $_GET['success'] ?>'] || 'Operation successful!');
        </script>
    <?php endif; ?>
</body>

</html>