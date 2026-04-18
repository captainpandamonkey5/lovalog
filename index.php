<?php
session_start();
include 'database.php';
include 'check_auth.php';

$success_message = null;
if (isset($_SESSION['success'])) {
    $success_message = $_SESSION['success'];
    unset($_SESSION['success']);
}

$is_authenticated = isAuthenticated();

// Search handling
$search = isset($_GET['query']) ? $conn->real_escape_string($_GET['query']) : "";

// Add this at the top of index.php with your other queries
$category_result = $conn->query("SELECT DISTINCT COALESCE(product_category, 'Uncategorized') as product_category FROM products ORDER BY product_category ASC");
// Build queries
$where_clause = !empty($search) ? "WHERE product_name LIKE '%$search%'" : "";
$base_sql = "SELECT * FROM products $where_clause ORDER BY product_name ASC";
$count_sql = "SELECT COUNT(*) as total FROM products $where_clause";

$result = $conn->query($base_sql);

$stats = $conn->query("
    SELECT
        COUNT(*) as total_products,
        AVG(product_price) as avg_price,
        MAX(product_price) as highest_price,
        MIN(product_price) as lowest_price
    FROM products
")->fetch_assoc();

$category_icons = [
    'Beverages'   => '🥤',
    'Dairy'       => '🥛',
    'Snacks'      => '🍿',
    'Meat'        => '🥩',
    'Vegetables'  => '🥦',
    'Fruits'      => '🍎',
    'Bakery'      => '🍞',
    'Frozen Foods' => '🧊',
    'Condiments'  => '🫙',
    'Others'      => '📋',
    'Uncategorized' => '❓',
];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="Store Price Ledger - Manage your product inventory">
    <meta name="robots" content="noindex, nofollow">
    <link rel="icon" type="image/png" href="assets/lovalog-favicon.svg">
    <title>Store Price Ledger</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="styles.css">
    <style>
        .table-container {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            margin: 30px auto;
            max-width: 1300px;
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
            color: #181A18;
            border: 1px solid var(--gray-200);
            border-radius: 6px;
            margin: 0 2px;
        }

        .pagination .page-link:hover {
            background: var(--gray-100);
            border-color: #181A18;
        }

        .pagination .page-item.active .page-link {
            background: #16A34A;
            border-color: #F5F5F5;
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

        .svg-bg {
            width: 44px;
            height: 44px;
            min-width: 44px;
            background: #f5f6fa;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .search-section {
            padding: 20px 0;
        }

        .search-form {
            display: flex;
            gap: 8px;
        }

        .search-form input {
            border-radius: 8px;
            border: 1px solid #e6e6e6;
            padding: 10px 16px;
            color: #181A18;
            transition: all 0.3s ease;
        }

        .search-form input:focus {
            border-color: #16A34A;
            box-shadow: 0 0 0 3px rgba(22, 163, 74, 0.05);
            outline: none;
        }

        .btn-search {
            background: linear-gradient(135deg, #16A34A 0%, #15803D 100%);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 10px 24px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-search:hover {
            background: linear-gradient(135deg, #15803D 0%, #166534 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(22, 163, 74, 0.3);
        }

        .category-btn {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 999px;
            border: 1px solid #e0e0e0;
            background: #f5f6fa;
            font-size: 13px;
            font-weight: 500;
            color: #343C54;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .category-btn:hover {
            background: #e8e8e8;
        }

        .category-btn.active {
            background: #16A34A;
            color: white;
            border-color: #16A34A;
        }
    </style>
</head>

<body>
    <?php include 'header.php'; ?>

    <main class="container-fluid px-3 px-md-5">

        <div class="container">

            <div class="text-center mt-5">
                <h1 style="font-weight: bold;">Store Price Ledger</h1>
                <p>Browse and search through our complete product catalog with updated prices.</p>
            </div>

            <div class="row">
                <div class="col-xs-12 col-md-3">
                    <div class="card-stats">
                        <div class="card-stats-inner">
                            <div class="svg-bg">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M20.75 8.518C20.774 8.506 20.799 8.493 20.823 8.48C21.524 8.099 22 7.356 22 6.502V5.502C22 4.259 20.993 3.252 19.75 3.252H4.25C3.007 3.252 2 4.259 2 5.502V6.502C2 7.385 2.509 8.15 3.25 8.518C3.551 8.668 3.891 8.752 4.25 8.752H19.75C20.109 8.752 20.449 8.668 20.75 8.518Z" fill="#343C54" />
                                    <path d="M3.25 9.595C3.565 9.697 3.901 9.752 4.25 9.752H19.75C20.099 9.752 20.435 9.752 20.75 9.595V18.502C20.75 19.745 19.743 20.752 18.5 20.752H5.5C4.257 20.752 3.25 19.745 3.25 18.502V9.595ZM9.25 13.252C9.25 13.666 9.586 14.002 10 14.002H14C14.414 14.002 14.75 13.666 14.75 13.252C14.75 12.838 14.414 12.502 14 12.502H10C9.586 12.502 9.25 12.838 9.25 13.252Z" fill="#343C54" />
                                </svg>
                            </div>
                            <div>
                                <h4><?= $stats['total_products'] ?></h4>
                                <p>Products</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-md-3">
                    <div class="card-stats">
                        <div class="card-stats-inner">
                            <div class="svg-bg">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" transform="rotate(0 0 0)">
                                    <path d="M12.75 2C12.75 1.58579 12.4142 1.25 12 1.25C11.5858 1.25 11.25 1.58579 11.25 2V2.86106C11.1327 2.90253 11.0181 2.95404 10.9073 3.01557L5.6573 5.93223C4.94301 6.32907 4.5 7.08196 4.5 7.89909V19.7509C4.5 20.9935 5.50736 22.0009 6.75 22.0009H17.25C18.4926 22.0009 19.5 20.9935 19.5 19.7509V7.89909C19.5 7.08196 19.057 6.32907 18.3427 5.93223L13.0927 3.01557C12.9819 2.95404 12.8673 2.90253 12.75 2.86106V2ZM12.0001 4.85352C12.4972 4.85352 12.9002 5.25646 12.9002 5.75352C12.9002 6.25057 12.4973 6.65352 12.0002 6.65352C11.5031 6.65352 11.1001 6.25057 11.1001 5.75352C11.1001 5.25646 11.503 4.85352 12.0001 4.85352ZM12.75 9V9.43778C13.7408 9.58842 14.5 10.444 14.5 11.4768C14.5 11.8911 14.1642 12.2268 13.75 12.2268C13.3358 12.2268 13 11.8911 13 11.4768C13 11.1661 12.7481 10.9142 12.4374 10.9142H11.75C11.3358 10.9142 11 11.25 11 11.6642V11.9293C11 12.2419 11.1939 12.5218 11.4866 12.6316L13.0401 13.2141C13.9182 13.5435 14.5 14.383 14.5 15.3209V15.5859C14.5 16.6567 13.752 17.5528 12.75 17.7802V18.25C12.75 18.6642 12.4142 19 12 19C11.5858 19 11.25 18.6642 11.25 18.25V17.8124C10.2592 17.6618 9.5 16.8062 9.5 15.7733C9.5 15.3591 9.83579 15.0233 10.25 15.0233C10.6642 15.0233 11 15.3591 11 15.7733C11 16.0841 11.2519 16.3359 11.5626 16.3359H12.25C12.6642 16.3359 13 16.0002 13 15.5859V15.3209C13 15.0082 12.8061 14.7284 12.5134 14.6186L10.9599 14.036C10.0818 13.7067 9.5 12.8672 9.5 11.9293V11.6642C9.5 10.5934 10.248 9.69736 11.25 9.47V9C11.25 8.58579 11.5858 8.25 12 8.25C12.4142 8.25 12.75 8.58579 12.75 9Z" fill="#343C54" />
                                </svg>
                            </div>
                            <div>
                                <h4>₱<?= number_format($stats['avg_price'], 2) ?></h4>
                                <p>Avg Price</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-md-3">
                    <div class="card-stats">
                        <div class="card-stats-inner">
                            <div class="svg-bg">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" transform="rotate(0 0 0)">
                                    <path d="M4.75 4C4.75 3.58579 4.41421 3.25 4 3.25C3.58579 3.25 3.25 3.58579 3.25 4V18.5C3.25 19.7426 4.25736 20.75 5.5 20.75H20.0005C20.4147 20.75 20.7505 20.4142 20.7505 20C20.7505 19.5858 20.4147 19.25 20.0005 19.25H5.5C5.08579 19.25 4.75 18.9142 4.75 18.5V15.7301L9.19964 11.2802L12.622 14.7026C12.7626 14.8432 12.9534 14.9222 13.1523 14.9222C13.3512 14.9222 13.542 14.8432 13.6826 14.7026L17.9972 10.388L17.9971 12.1165C17.997 12.5308 18.3328 12.8666 18.747 12.8666C19.1612 12.8666 19.497 12.5309 19.4971 12.1166L19.4973 8.58111C19.4973 8.38218 19.4183 8.19141 19.2777 8.05074C19.137 7.91008 18.9462 7.83105 18.7473 7.83105H15.2115C14.7973 7.83105 14.4615 8.16684 14.4615 8.58105C14.4615 8.99527 14.7973 9.33105 15.2115 9.33105H16.9328L13.1523 13.1116L9.72996 9.68923C9.58931 9.54857 9.39854 9.46956 9.19963 9.46956C9.00071 9.46956 8.80994 9.54858 8.66929 9.68924L4.75 13.6087V4Z" fill="#343C54" />
                                </svg>
                            </div>
                            <div>
                                <h4>₱<?= number_format($stats['highest_price'], 2) ?></h4>
                                <p>Highest</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-md-3">
                    <div class="card-stats">
                        <div class="card-stats-inner">
                            <div class="svg-bg">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" transform="rotate(0 0 0)">
                                    <path d="M4.75 4C4.75 3.58579 4.41421 3.25 4 3.25C3.58579 3.25 3.25 3.58579 3.25 4V18.5C3.25 19.7426 4.25736 20.75 5.5 20.75H20.0005C20.4147 20.75 20.7505 20.4142 20.7505 20C20.7505 19.5858 20.4147 19.25 20.0005 19.25H5.5C5.08579 19.25 4.75 18.9142 4.75 18.5V10.3913L8.66929 14.3108C8.80994 14.4514 9.00071 14.5304 9.19963 14.5304C9.39854 14.5304 9.58931 14.4514 9.72996 14.3108L13.1523 10.8884L16.9328 14.6689H15.2115C14.7973 14.6689 14.4615 15.0047 14.4615 15.4189C14.4615 15.8332 14.7973 16.1689 15.2115 16.1689H18.7473C18.9462 16.1689 19.137 16.0899 19.2777 15.9493C19.4183 15.8086 19.4973 15.6178 19.4973 15.4189L19.4971 11.8834C19.497 11.4691 19.1612 11.1334 18.747 11.1334C18.3328 11.1334 17.997 11.4692 17.9971 11.8835L17.9972 13.612L13.6826 9.29743C13.542 9.15678 13.3512 9.07776 13.1523 9.07776C12.9534 9.07776 12.7626 9.15678 12.622 9.29743L9.19964 12.7198L4.75 8.26994V4Z" fill="#343C54" />
                                </svg>
                            </div>
                            <div>
                                <h4>₱<?= number_format($stats['lowest_price'], 2) ?></h4>
                                <p>Lowest</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="search-section" style="padding: 20px 0;">
                <form method="GET" class="search-form" onsubmit="return false;">
                    <input type="text" id="liveSearch" name="query" class="flex-grow-1" placeholder="🔍 Search Products..." value="<?= $search ?>" autocomplete="off">
                    <!-- <button type="submit" class="btn-search">Search</button> -->
                </form>
                <small style="color: #8a8fa8; margin-top: 8px; display: block;">
                    Showing <b><?= $result->num_rows ?></b> of <b><?= $stats['total_products'] ?></b> products
                </small>
            </div>

            <div style="display:flex; flex-wrap:wrap; gap:8px; margin-top:12px;">
                <button class="category-btn active" data-category="all">🏪 All</button>
                <?php while ($cat = $category_result->fetch_assoc()):
                    $label = !empty($cat['product_category']) ? $cat['product_category'] : 'Uncategorized';
                    $icon = $category_icons[$label] ?? '📦';
                ?>
                    <button class="category-btn" data-category="<?= htmlspecialchars($label) ?>">
                        <?= $icon ?> <?= htmlspecialchars($label) ?>
                    </button>
                <?php endwhile; ?>
            </div>
        </div>


        <div class="table-container">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-header">
                        <tr>
                            <th style="width: 80px;">#</th>
                            <th>Product Name</th>
                            <th>Category</th>
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
                                    <td><?= htmlspecialchars($row['id']) ?></td>
                                    <td class="product-name"><?= htmlspecialchars($row['product_name']) ?></td>
                                    <td class="product-category">
                                        <?= htmlspecialchars(!empty($row['product_category']) ? $row['product_category'] : 'Uncategorized') ?>
                                    </td>
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
        </div>
    </main>

    <?php include 'footer.php'; ?>

    <?php if ($success_message): ?>
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
            if ($success_message == 'added') echo '✅ Product has been added successfully!';
            elseif ($success_message == 'updated') echo '✅ Product has been updated successfully!';
            elseif ($success_message == 'deleted') echo '✅ Product has been deleted successfully!';
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

    <script>
        const searchInput = document.getElementById('liveSearch');
        const tableRows = document.querySelectorAll('tbody tr');
        const categoryBtns = document.querySelectorAll('.category-btn');
        let activeCategory = 'all';

        function filterRows() {
            const query = searchInput.value.toLowerCase().trim();
            tableRows.forEach(row => {
                const productName = row.querySelector('.product-name');
                const productCategory = row.querySelector('.product-category');

                if (!productName) return;

                const name = productName.textContent.toLowerCase();
                const category = productCategory ? productCategory.textContent.trim() : '';
                const matchesSearch = query === '' || name.includes(query);
                const matchesCategory = activeCategory === 'all' || category === activeCategory;
                row.style.display = matchesSearch && matchesCategory ? '' : 'none';
            });
        }

        searchInput.addEventListener('input', filterRows);

        categoryBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                categoryBtns.forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                activeCategory = this.dataset.category;
                filterRows();
            });
        });
    </script>
</body>

</html>