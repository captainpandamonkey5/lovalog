<?php
// Session and authentication check
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$is_authenticated = isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true;
$search = htmlspecialchars($_GET['query'] ?? '');
?>

<style>
    .nav-button {
        font-size: 0.95rem;
        font-weight: 600;
        border-radius: 8px;
        background: linear-gradient(135deg, #16A34A 0%, #15803D 100%);
        padding: 8px 16px;
        transition: all 0.3s ease;
        color: white;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border: none;
    }

    .nav-button:hover {
        background: linear-gradient(135deg, #15803D 0%, #166534 100%);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(22, 163, 74, 0.3);
    }

    .navbar-custom {
        background: linear-gradient(180deg, #1a1a1a 0%, #0f0f0f 100%);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
    }

    .navbar-brand {
        font-size: 1.5rem;
        font-weight: 700;
        letter-spacing: -0.5px;
        color: white;
    }

    .nav-link {
        color: #e5e5e5 !important;
        font-weight: 500;
        transition: color 0.3s ease;
        margin-left: 12px;
    }

    .nav-link:hover {
        color: #16A34A !important;
    }
</style>

<nav class="navbar navbar-expand-lg navbar-custom">
    <div class="container-fluid px-4 py-3">
        <a class="navbar-brand" href="index.php">Store Price Ledger</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item">
                    <a class="nav-button" href="index.php">
                        📦
                        Products
                    </a>
                </li>
                <?php if ($is_authenticated): ?>
                    <li class="nav-item"><a class="nav-link" href="create.php">Add Product</a></li>
                    <li class="nav-item"><a class="nav-link" href="change_password.php">Change Password</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>