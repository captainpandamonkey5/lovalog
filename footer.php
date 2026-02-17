<?php
$current_year = date('Y');
$author_url = 'https://captainpandamonkey5.github.io/pandamonkeycard/';
$author_name = 'CaptainPandaMonkey';
?>

<style>
    .footer-custom {
        background: linear-gradient(180deg, #0f0f0f 0%, #1a1a1a 100%);
        border-top: 1px solid #2a2a2a;
        margin-top: auto;
    }

    .footer-content {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 2rem;
        align-items: center;
    }

    .footer-section h5 {
        font-size: 1.25rem;
        font-weight: 700;
        color: white;
        margin-bottom: 8px;
        letter-spacing: -0.5px;
    }

    .footer-section p {
        color: #b3b3b3;
        font-size: 0.95rem;
        margin: 0;
    }

    .footer-credit {
        text-align: right;
        color: #b3b3b3;
        font-size: 0.95rem;
    }

    .footer-credit a {
        color: white;
        text-decoration: none;
        font-weight: 600;
        transition: color 0.3s ease;
    }

    .footer-credit a:hover {
        color: #b3b3b3;
    }

    @media (max-width: 768px) {
        .footer-content {
            grid-template-columns: 1fr;
            text-align: center;
        }

        .footer-credit {
            text-align: center;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid #2a2a2a;
        }
    }
</style>

<footer class="footer-custom py-4">
    <div class="container-fluid px-4">
        <div class="footer-content">
            <div class="footer-section">
                <h5>Store Price Ledger</h5>
                <p>Manage your products efficiently</p>
            </div>
            <div class="footer-credit">
                <p class="mb-0">&copy; <?= $current_year ?> <a href="<?= htmlspecialchars($author_url) ?>"><?= htmlspecialchars($author_name) ?></a> ❤️ All rights reserved.</p>
            </div>
        </div>
    </div>
</footer>