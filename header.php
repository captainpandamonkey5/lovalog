<?php
// ─── Session & Auth ────────────────────────────────────────────
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$is_authenticated = isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true;
$search          = htmlspecialchars($_GET['query'] ?? '', ENT_QUOTES, 'UTF-8');
$currentPage     = basename($_SERVER['PHP_SELF']);
?>

<style>
    :root {
        --brand-green: #16A34A;
        --brand-green-dark: #15803D;
        --brand-green-deep: #166534;
        --nav-bg-top: #1c1c1c;
        --nav-bg-bot: #111111;
        --nav-text: #d4d4d4;
        --nav-text-hover: #ffffff;
        --nav-accent: var(--brand-green);
        --nav-height: 64px;
        --radius-md: 8px;
        --transition: 0.22s ease;
    }

    .navbar-custom {
        background: linear-gradient(180deg, var(--nav-bg-top) 0%, var(--nav-bg-bot) 100%);
        box-shadow: 0 1px 0 rgba(255, 255, 255, 0.04),
            0 4px 16px rgba(0, 0, 0, 0.35);
        min-height: var(--nav-height);
        position: sticky;
        top: 0;
        z-index: 1030;
    }

    .navbar-custom::before {
        content: '';
        position: absolute;
        inset: 0;
        background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.04'/%3E%3C/svg%3E");
        background-size: 120px;
        pointer-events: none;
        opacity: 0.5;
    }

    .navbar-brand {
        font-size: 1.45rem;
        font-weight: 800;
        letter-spacing: -0.5px;
        color: #ffffff !important;
        display: flex;
        align-items: center;
        gap: 6px;
        transition: opacity var(--transition);
    }

    .navbar-brand:hover {
        opacity: 0.85;
    }

    .navbar-brand .brand-dot {
        display: inline-block;
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: var(--brand-green);
        box-shadow: 0 0 6px var(--brand-green);
        margin-bottom: 1px;
    }

    .navbar-toggler {
        border-color: rgba(255, 255, 255, 0.2);
        padding: 6px 10px;
    }

    .navbar-toggler-icon {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3E%3Cpath stroke='rgba(255,255,255,0.8)' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3E%3C/svg%3E");
    }

    .navbar-toggler:focus {
        box-shadow: 0 0 0 2px rgba(22, 163, 74, 0.4);
    }

    .nav-button {
        font-size: 0.875rem;
        font-weight: 600;
        border-radius: var(--radius-md);
        background: linear-gradient(135deg, var(--brand-green) 0%, var(--brand-green-dark) 100%);
        padding: 7px 16px;
        transition: all var(--transition);
        color: #ffffff !important;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 7px;
        border: none;
        line-height: 1;
    }

    .nav-button:hover,
    .nav-button:focus {
        background: linear-gradient(135deg, var(--brand-green-dark) 0%, var(--brand-green-deep) 100%);
        transform: translateY(-1px);
        box-shadow: 0 4px 14px rgba(22, 163, 74, 0.35);
        outline: none;
    }

    .nav-button.active {
        background: linear-gradient(135deg, var(--brand-green-deep) 0%, #14532D 100%);
        box-shadow: 0 2px 8px rgba(22, 163, 74, 0.25);
    }

    .navbar-nav .nav-link {
        color: var(--nav-text) !important;
        font-size: 0.875rem;
        font-weight: 500;
        padding: 6px 10px !important;
        border-radius: 6px;
        transition: color var(--transition), background var(--transition);
        position: relative;
    }

    .navbar-nav .nav-link:hover {
        color: var(--nav-text-hover) !important;
        background: rgba(255, 255, 255, 0.06);
    }

    .navbar-nav .nav-link.active-link {
        color: #ffffff !important;
        background: rgba(22, 163, 74, 0.15);
    }

    .navbar-nav .nav-link.active-link::after {
        content: '';
        position: absolute;
        bottom: 2px;
        left: 50%;
        transform: translateX(-50%);
        width: 16px;
        height: 2px;
        border-radius: 2px;
        background: var(--brand-green);
    }

    /* Disabled link */
    .navbar-nav .nav-link.disabled {
        color: rgba(229, 229, 229, 0.35) !important;
        cursor: not-allowed;
        pointer-events: none;
    }

    .nav-divider {
        width: 1px;
        height: 20px;
        background: rgba(255, 255, 255, 0.1);
        margin: 0 6px;
        align-self: center;
    }

    .admin-toggle {
        font-size: 0.875rem;
        font-weight: 600;
        padding: 7px 14px;
        border-radius: var(--radius-md);
        border: 1px solid rgba(255, 255, 255, 0.12);
        background: rgba(255, 255, 255, 0.05);
        color: var(--nav-text) !important;
        transition: all var(--transition);
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .admin-toggle:hover,
    .admin-toggle:focus,
    .admin-toggle.show {
        background: rgba(255, 255, 255, 0.1);
        border-color: rgba(255, 255, 255, 0.2);
        color: #ffffff !important;
        box-shadow: none;
    }

    .admin-toggle::after {
        border-top-color: currentColor;
        opacity: 0.6;
    }

    .dropdown-menu {
        background: #1e1e1e;
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: var(--radius-md);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.4);
        padding: 6px;
        margin-top: 6px !important;
        min-width: 170px;
    }

    .dropdown-menu .dropdown-item {
        color: var(--nav-text);
        font-size: 0.875rem;
        font-weight: 500;
        padding: 8px 12px;
        border-radius: 6px;
        transition: background var(--transition), color var(--transition);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .dropdown-menu .dropdown-item:hover {
        background: rgba(22, 163, 74, 0.15);
        color: #ffffff;
    }

    .dropdown-menu .dropdown-divider {
        border-color: rgba(255, 255, 255, 0.08);
        margin: 4px 0;
    }

    .nav-link-danger {
        color: #f87171 !important;
    }

    .nav-link-danger:hover {
        color: #fca5a5 !important;
        background: rgba(239, 68, 68, 0.1) !important;
    }

    /* ── Mobile adjustments ─────────────────────────────────── */
    @media (max-width: 991px) {
        .navbar-collapse {
            padding: 12px 0;
            border-top: 1px solid rgba(255, 255, 255, 0.07);
            margin-top: 8px;
        }

        .navbar-nav .nav-item {
            width: 100%;
        }

        .nav-divider {
            display: none;
        }

        .nav-button {
            width: 100%;
            justify-content: center;
            margin-bottom: 4px;
        }

        .dropdown-menu {
            position: static !important;
            transform: none !important;
            box-shadow: none;
            border: none;
            background: rgba(255, 255, 255, 0.04);
            margin-top: 2px !important;
        }
    }
</style>

<?php
// Helper: apply active class if current page matches
function navActive(string $page, string $current): string
{
    return basename($page) === $current ? ' active-link' : '';
}
?>

<nav class="navbar navbar-expand-lg navbar-custom" aria-label="Main navigation">
    <div class="container-fluid px-4">

        <a class="navbar-brand" href="index.php">
            LovaLog<span class="brand-dot" aria-hidden="true"></span>
        </a>

        <button class="navbar-toggler" type="button"
            data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false"
            aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center gap-1 py-2 py-lg-0">

                <li class="nav-item">
                    <a class="nav-button<?= $currentPage === 'index.php' ? ' active' : '' ?>"
                        href="index.php"
                        <?= $currentPage === 'index.php' ? 'aria-current="page"' : '' ?>>
                        <i class="fa-solid fa-boxes-stacked"></i> Products
                    </a>
                </li>

                <?php if ($is_authenticated): ?>

                    <li class="nav-item">
                        <a href="#" class="nav-link disabled" tabindex="-1" aria-disabled="true" title="Coming soon">
                            <i class="fa-regular fa-bell"></i> Notifications
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link disabled"
                            href="ideas.php"
                            tabindex="-1"
                            aria-disabled="true"
                            title="Coming soon">
                            <i class="fa-regular fa-lightbulb"></i> Ideas
                        </a>
                    </li>

                    <!-- Divider -->
                    <li aria-hidden="true">
                        <div class="nav-divider"></div>
                    </li>

                    <!-- Admin dropdown -->
                    <li class="nav-item dropdown">
                        <button class="btn admin-toggle dropdown-toggle"
                            id="adminDropdown"
                            data-bs-toggle="dropdown"
                            aria-expanded="false" disabled>
                            <i class="fa-solid fa-user-tie"></i> Admin
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="adminDropdown">
                            <li>
                                <a class="dropdown-item" href="users.php">
                                    <i class="fa-solid fa-users"></i> User Management
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="admin.php">
                                    <i class="fa-solid fa-user-gear"></i> Admin Panel
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- Divider -->
                    <li aria-hidden="true">
                        <div class="nav-divider"></div>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link<?= navActive('change_password.php', $currentPage) ?>"
                            href="change_password.php">
                            Change Password
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link nav-link-danger" href="logout.php">
                            Logout
                        </a>
                    </li>

                <?php else: ?>

                    <li class="nav-item">
                        <a class="nav-link<?= navActive('login.php', $currentPage) ?>"
                            href="login.php"
                            <?= $currentPage === 'login.php' ? 'aria-current="page"' : '' ?>>
                            Login
                        </a>
                    </li>

                <?php endif; ?>

            </ul>
        </div>
    </div>
</nav>