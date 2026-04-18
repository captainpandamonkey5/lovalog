<?php
session_start();
include 'database.php';
include 'check_auth.php';

// Flash Message
$success_message = null;
if (isset($_SESSION['success'])) {
    $success_message = $_SESSION['success'];
    unset($_SESSION['success']);
}

$is_authenticated = isAuthenticated();

// ── Search — use prepared statement instead of real_escape_string ─
$search = trim($_GET['query'] ?? '');

// Category Filter
$category_result = $conn->query(
    "SELECT DISTINCT COALESCE(NULLIF(product_category,''), 'Uncategorized') AS product_category
     FROM products
     ORDER BY product_category ASC"
);

// ── Product query (prepared, safe) ──────────────────────────────
if ($search !== '') {
    $like = "%$search%";
    $stmt = $conn->prepare(
        "SELECT * FROM products WHERE product_name LIKE ? ORDER BY product_name ASC"
    );
    $stmt->bind_param('s', $like);
} else {
    $stmt = $conn->prepare("SELECT * FROM products ORDER BY product_name ASC");
}
$stmt->execute();
$result       = $stmt->get_result();
$result_count = $result->num_rows;

// Stats on top of page
$stats = $conn->query("
    SELECT
        COUNT(*)        AS total_products,
        AVG(product_price)  AS avg_price,
        MAX(product_price)  AS highest_price,
        MIN(product_price)  AS lowest_price
    FROM products
")->fetch_assoc();

$category_icons = [
    'Beverages'    => '🥤',
    'Dairy'        => '🥛',
    'Snacks'       => '🍿',
    'Meat'         => '🥩',
    'Vegetables'   => '🥦',
    'Fruits'       => '🍎',
    'Bakery'       => '🍞',
    'Frozen Foods' => '🧊',
    'Condiments'   => '🫙',
    'Others'       => '📋',
    'Uncategorized' => '❓',
];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="LovaLog — Manage your product inventory">
    <meta name="robots" content="noindex, nofollow">
    <link rel="icon" type="image/svg+xml" href="assets/lovalog-favicon.svg">
    <title>LovaLog — Store Price Ledger</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@600;700;800&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="styles.css">

    <style>
        /* ── Design tokens ──────────────────────────────────── */
        :root {
            --green: #16a34a;
            --green-dark: #15803d;
            --green-deep: #166534;
            --green-glow: rgba(22, 163, 74, 0.18);
            --surface: #f7f8f5;
            --surface-2: #eef0eb;
            --border: rgba(0, 0, 0, 0.07);
            --text-primary: #111810;
            --text-muted: #6b7280;
            --font-display: "Syne", sans-serif;
            --font-body: "DM Sans", sans-serif;
            --radius: 12px;
            --radius-lg: 18px;
            --transition: 0.2s ease;
            --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.06), 0 1px 2px rgba(0, 0, 0, 0.04);
            --shadow-md: 0 4px 16px rgba(0, 0, 0, 0.08);
            --shadow-green: 0 4px 20px rgba(22, 163, 74, 0.2);
        }

        /* ── Base ───────────────────────────────────────────── */
        body {
            font-family: var(--font-body);
            background-color: var(--surface);
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        main {
            flex: 1;
        }

        /* ── Hero ───────────────────────────────────────────── */
        .hero {
            padding: 52px 0 40px;
            text-align: center;
            position: relative;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 600px;
            height: 220px;
            background: radial-gradient(ellipse at center, rgba(22, 163, 74, 0.1) 0%, transparent 70%);
            pointer-events: none;
        }

        .hero-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.72rem;
            font-weight: 600;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--green);
            background: rgba(22, 163, 74, 0.09);
            border: 1px solid rgba(22, 163, 74, 0.2);
            border-radius: 999px;
            padding: 4px 12px;
            margin-bottom: 16px;
        }

        .hero h1 {
            font-family: var(--font-display);
            font-size: clamp(2rem, 5vw, 3rem);
            font-weight: 800;
            letter-spacing: -1px;
            line-height: 1.1;
            color: var(--text-primary);
            margin-bottom: 12px;
        }

        .hero h1 span {
            color: var(--green);
            position: relative;
        }

        .hero p {
            color: var(--text-muted);
            font-size: 1rem;
            max-width: 480px;
            margin: 0 auto;
        }

        /* stats on top of page */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 14px;
            margin: 32px 0 28px;
        }

        @media (max-width: 900px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 500px) {
            .stats-grid {
                grid-template-columns: 1fr 1fr;
                gap: 10px;
            }
        }

        .stat-card {
            background: #ffffff;
            color: #16A34A;
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 14px;
            box-shadow: var(--shadow-sm);
            transition: transform var(--transition), box-shadow var(--transition);
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .stat-icon {
            width: 44px;
            height: 44px;
            min-width: 44px;
            background: var(--surface-2);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .stat-card h4 {
            font-family: var(--font-display);
            font-size: 1.25rem;
            font-weight: 700;
            margin: 0 0 2px;
            color: var(--text-primary);
        }

        .stat-card p {
            font-size: 0.75rem;
            color: var(--text-muted);
            margin: 0;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        /* ── Search & filter bar ────────────────────────────── */
        .search-bar-wrap {
            background: #ffffff;
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 6px 6px 6px 18px;
            display: flex;
            align-items: center;
            gap: 10px;
            box-shadow: var(--shadow-sm);
            transition: box-shadow var(--transition), border-color var(--transition);
        }

        .search-bar-wrap:focus-within {
            border-color: var(--green);
            box-shadow: 0 0 0 3px rgba(22, 163, 74, 0.12);
        }

        .search-bar-wrap input {
            flex: 1;
            border: none;
            outline: none;
            font-family: var(--font-body);
            font-size: 0.95rem;
            color: var(--text-primary);
            background: transparent;
        }

        .search-bar-wrap input::placeholder {
            color: #aab0b8;
        }

        .search-count {
            font-size: 0.78rem;
            color: var(--text-muted);
            margin-top: 8px;
            padding-left: 4px;
        }

        .search-count b {
            color: var(--text-primary);
        }

        .category-strip {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin: 16px 0 24px;
        }

        .category-btn {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 6px 14px;
            border-radius: 999px;
            border: 1px solid var(--border);
            background: #ffffff;
            font-family: var(--font-body);
            font-size: 0.8rem;
            font-weight: 500;
            color: var(--text-muted);
            cursor: pointer;
            transition: all var(--transition);
            box-shadow: var(--shadow-sm);
        }

        .category-btn:hover {
            border-color: var(--green);
            color: var(--green);
            background: rgba(22, 163, 74, 0.05);
        }

        .category-btn.active {
            background: var(--green);
            color: #ffffff;
            border-color: var(--green);
            box-shadow: var(--shadow-green);
        }

        .table-wrap {
            background: #ffffff;
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow-sm);
            margin-bottom: 40px;
        }

        .table {
            margin: 0;
            font-family: var(--font-body);
        }

        .table thead th {
            background: var(--surface-2);
            font-size: 0.72rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--text-muted);
            padding: 13px 18px;
            border-bottom: 1px solid var(--border);
            border-top: none;
            white-space: nowrap;
        }

        .table tbody td {
            padding: 14px 18px;
            font-size: 0.9rem;
            border-bottom: 1px solid var(--border);
            vertical-align: middle;
        }

        .table tbody tr:last-child td {
            border-bottom: none;
        }

        .table tbody tr {
            transition: background var(--transition);
        }

        .table tbody tr:hover {
            background: var(--surface);
        }

        /* Row fade-in on load */
        .table tbody tr {
            animation: rowFadeIn 0.35s ease both;
        }

        @keyframes rowFadeIn {
            from {
                opacity: 0;
                transform: translateY(4px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .table tbody tr:nth-child(1) {
            animation-delay: 0.03s;
        }

        .table tbody tr:nth-child(2) {
            animation-delay: 0.06s;
        }

        .table tbody tr:nth-child(3) {
            animation-delay: 0.09s;
        }

        .table tbody tr:nth-child(4) {
            animation-delay: 0.12s;
        }

        .table tbody tr:nth-child(5) {
            animation-delay: 0.15s;
        }

        .product-name {
            font-weight: 600;
            color: var(--text-primary);
            font-size: 0.9rem;
        }

        .price {
            font-family: var(--font-display);
            font-weight: 700;
            color: var(--green-deep);
            font-size: 0.9rem;
        }

        /* Category badge */
        .cat-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            background: var(--surface-2);
            border: 1px solid var(--border);
            border-radius: 999px;
            padding: 3px 10px;
            font-size: 0.75rem;
            font-weight: 500;
            color: var(--text-muted);
            white-space: nowrap;
        }

        /* Qty badge */
        .qty-badge {
            display: inline-block;
            background: var(--surface-2);
            border: 1px solid var(--border);
            border-radius: 6px;
            padding: 3px 10px;
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--text-primary);
            min-width: 36px;
            text-align: center;
        }

        /* Action buttons */
        .btn-edit,
        .btn-delete {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 6px 12px;
            border-radius: var(--radius);
            font-size: 0.78rem;
            font-weight: 600;
            border: none;
            cursor: pointer;
            text-decoration: none;
            transition: all var(--transition);
            white-space: nowrap;
        }

        .btn-edit {
            background: rgba(22, 163, 74, 0.1);
            color: var(--green-deep);
        }

        .btn-edit:hover {
            background: var(--green);
            color: #ffffff;
            box-shadow: var(--shadow-green);
            transform: translateY(-1px);
        }

        .btn-delete {
            background: rgba(239, 68, 68, 0.08);
            color: #dc2626;
        }

        .btn-delete:hover {
            background: #ef4444;
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
            transform: translateY(-1px);
        }

        .actions-cell {
            display: flex;
            gap: 6px;
            justify-content: center;
        }

        .empty-state {
            text-align: center;
            padding: 64px 24px;
            color: var(--text-muted);
        }

        .empty-state .empty-icon {
            font-size: 3rem;
            margin-bottom: 12px;
            opacity: 0.5;
            display: block;
        }

        .empty-state p {
            font-size: 1rem;
            margin-bottom: 20px;
        }

        .toast-notif {
            position: fixed;
            bottom: 28px;
            left: 28px;
            background: #111810;
            color: #ffffff;
            padding: 13px 20px;
            border-radius: var(--radius);
            font-size: 0.875rem;
            font-weight: 500;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.25);
            z-index: 9999;
            opacity: 0;
            transform: translateY(14px);
            transition: all 0.35s ease;
            display: flex;
            align-items: center;
            gap: 8px;
            border-left: 3px solid var(--green);
        }

        .btn-add {
            background: var(--green);
            color: #ffffff;
            border: none;
            border-radius: var(--radius);
            padding: 9px 20px;
            font-family: var(--font-body);
            font-size: 0.875rem;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all var(--transition);
            box-shadow: var(--shadow-green);
        }

        .btn-add:hover {
            background: var(--green-dark);
            color: #ffffff;
            transform: translateY(-1px);
        }

        /* ── Mobile tweaks ──────────────────────────────────── */
        @media (max-width: 640px) {
            .hero {
                padding: 36px 0 28px;
            }

            .table thead th,
            .table tbody td {
                padding: 11px 12px;
                font-size: 0.82rem;
            }
        }
    </style>
</head>

<body>
    <?php include 'header.php'; ?>

    <main class="container-fluid px-3 px-md-5">
        <div class="container">

            <div class="hero">
                <div class="hero-eyebrow">
                    <span>🏪</span> Product Catalog
                </div>
                <h1>Store <span>Price Ledger</span></h1>
                <p>Browse and search through our complete product catalog with up-to-date prices.</p>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fa-solid fa-box-archive"></i>
                    </div>
                    <div>
                        <h4><?= (int)$stats['total_products'] ?></h4>
                        <p>Products</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fa-solid fa-tags"></i>
                    </div>
                    <div>
                        <h4>₱<?= number_format((float)$stats['avg_price'], 2) ?></h4>
                        <p>Avg Price</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fa-solid fa-arrow-trend-up"></i>
                    </div>
                    <div>
                        <h4>₱<?= number_format((float)$stats['highest_price'], 2) ?></h4>
                        <p>Highest</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fa-solid fa-arrow-trend-down"></i>
                    </div>
                    <div>
                        <h4>₱<?= number_format((float)$stats['lowest_price'], 2) ?></h4>
                        <p>Lowest</p>
                    </div>
                </div>
            </div>

            <div class="search-bar-wrap">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#aab0b8" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8" />
                    <path d="m21 21-4.35-4.35" />
                </svg>
                <input type="text" id="liveSearch" placeholder="Search products by name…"
                    value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?>"
                    autocomplete="off" spellcheck="false">
                <?php if ($is_authenticated): ?>
                    <!-- <a href="create.php" class="btn-add">+ Add Product</a> -->
                    <button type="button" class="btn-add" data-bs-toggle="modal" data-bs-target="#addProductModal">
                        <i class="fa-regular fa-square-plus"></i> Add Product
                    </button>
                <?php endif; ?>
            </div>
            <p class="search-count">
                Showing <b id="visibleCount"><?= $result_count ?></b> of <b><?= (int)$stats['total_products'] ?></b> products
            </p>

            <!-- ── Category pills ───────────────────────────────────── -->
            <div class="category-strip">
                <button class="category-btn active" data-category="all">🏪 All</button>
                <?php while ($cat = $category_result->fetch_assoc()):
                    $label = $cat['product_category'];
                    $icon  = $category_icons[$label] ?? '📦';
                ?>
                    <button class="category-btn" data-category="<?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?>">
                        <?= $icon ?> <?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?>
                    </button>
                <?php endwhile; ?>
            </div>

        </div>

        <!-- ── Table (full-width) ────────────────────────────────────── -->
        <div class="container-fluid px-3 px-md-5">
            <div class="table-wrap">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th style="width:60px">#</th>
                                <th>Product Name</th>
                                <th>Category</th>
                                <th style="width:140px">Price</th>
                                <?php if ($is_authenticated): ?>
                                    <th style="width:90px">Qty</th>
                                    <th style="width:180px" class="text-center">Actions</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody id="productTable">
                            <?php if ($result->num_rows > 0): ?>
                                <?php while ($row = $result->fetch_assoc()):
                                    $cat_label = !empty($row['product_category']) ? $row['product_category'] : 'Uncategorized';
                                    $cat_icon  = $category_icons[$cat_label] ?? '📦';
                                ?>
                                    <tr data-category="<?= htmlspecialchars($cat_label, ENT_QUOTES, 'UTF-8') ?>">
                                        <td style="color:var(--text-muted);font-size:0.8rem"><?= (int)$row['id'] ?></td>
                                        <td class="product-name"><?= htmlspecialchars($row['product_name'], ENT_QUOTES, 'UTF-8') ?></td>
                                        <td class="product-category">
                                            <span class="cat-badge">
                                                <?= $cat_icon ?> <?= htmlspecialchars($cat_label, ENT_QUOTES, 'UTF-8') ?>
                                            </span>
                                        </td>
                                        <td class="price">₱<?= number_format((float)$row['product_price'], 2) ?></td>
                                        <?php if ($is_authenticated): ?>
                                            <td><span class="qty-badge"><?= (int)$row['quantity'] ?></span></td>
                                            <td>
                                                <div class="actions-cell">
                                                    <!-- <a href="update.php?id=<?= (int)$row['id'] ?>" class="btn-edit">✏️ Edit</a> -->
                                                    <button type="button" class="btn-edit"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#editProductModal"
                                                        data-id="<?= (int)$row['id'] ?>"
                                                        data-name="<?= htmlspecialchars($row['product_name'], ENT_QUOTES, 'UTF-8') ?>"
                                                        data-category="<?= htmlspecialchars($row['product_category'], ENT_QUOTES, 'UTF-8') ?>"
                                                        data-price="<?= htmlspecialchars($row['product_price'], ENT_QUOTES, 'UTF-8') ?>"
                                                        data-quantity="<?= (int)$row['quantity'] ?>">
                                                        <i class="fa-regular fa-pen-to-square"></i> Edit
                                                    </button>
                                                    <a href="delete.php?id=<?= (int)$row['id'] ?>"
                                                        class="btn-delete"
                                                        onclick="return confirm('Delete «<?= htmlspecialchars(addslashes($row['product_name']), ENT_QUOTES, 'UTF-8') ?>»?')">
                                                        <i class="fa-regular fa-trash-can"></i> Delete
                                                    </a>
                                                </div>
                                            </td>
                                        <?php endif; ?>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="<?= $is_authenticated ? 6 : 4 ?>">
                                        <div class="empty-state">
                                            <span class="empty-icon">📦</span>
                                            <p><?= $search ? "No products match \"" . htmlspecialchars($search, ENT_QUOTES, 'UTF-8') . "\"" : 'No products yet.' ?></p>
                                            <?php if ($is_authenticated): ?>
                                                <a href="create.php" class="btn-add">+ Add First Product</a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </main>
    <?php include 'edit_product_modal.php'; ?>
    <?php include 'add_product_modal.php'; ?>
    <?php include 'footer.php'; ?>

    <?php if ($success_message): ?>
        <?php
        $toast_messages = [
            'added'   => '✅ Product added successfully!',
            'updated' => '✅ Product updated successfully!',
            'deleted' => '✅ Product deleted successfully!',
        ];
        $toast_text = $toast_messages[$success_message] ?? '✅ Done!';
        ?>
        <div id="toast-notif" class="toast-notif"><?= htmlspecialchars($toast_text, ENT_QUOTES, 'UTF-8') ?></div>
        <script>
            (function() {
                const t = document.getElementById('toast-notif');
                setTimeout(() => {
                    t.style.opacity = '1';
                    t.style.transform = 'translateY(0)';
                }, 80);
                setTimeout(() => {
                    t.style.opacity = '0';
                    t.style.transform = 'translateY(14px)';
                }, 3400);
                setTimeout(() => t.remove(), 3900);
            })();
        </script>
    <?php endif; ?>

    <script>
        (function() {
            const searchInput = document.getElementById('liveSearch');
            const rows = document.querySelectorAll('#productTable tr');
            const categoryBtns = document.querySelectorAll('.category-btn');
            const countEl = document.getElementById('visibleCount');
            let activeCategory = 'all';

            function filterRows() {
                const query = searchInput.value.toLowerCase().trim();
                let visible = 0;

                rows.forEach(row => {
                    const nameEl = row.querySelector('.product-name');
                    if (!nameEl) return;

                    const name = nameEl.textContent.toLowerCase();
                    const category = (row.dataset.category || '').trim();

                    const matchSearch = query === '' || name.includes(query);
                    const matchCategory = activeCategory === 'all' || category === activeCategory;
                    const show = matchSearch && matchCategory;

                    row.style.display = show ? '' : 'none';
                    if (show) visible++;
                });

                if (countEl) countEl.textContent = visible;
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
        })();
    </script>

    <script>
        // Pre-fill edit modal with row data when opened
        document.getElementById('editProductModal').addEventListener('show.bs.modal', function(e) {
            const btn = e.relatedTarget;
            document.getElementById('edit_id').value = btn.dataset.id;
            document.getElementById('edit_product_name').value = btn.dataset.name;
            document.getElementById('edit_product_price').value = btn.dataset.price;
            document.getElementById('edit_quantity').value = btn.dataset.quantity;

            const select = document.getElementById('edit_product_category');
            select.value = btn.dataset.category;
        });
    </script>
</body>

</html>