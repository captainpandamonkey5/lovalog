<?php
$current_year = date('Y');
$author_url   = 'https://captainpandamonkey5.github.io/pandamonkeycard/';
$author_name  = 'CaptainPandaMonkey';
?>

<style>
    .footer-custom {
        background: linear-gradient(180deg, #111111 0%, #1c1c1c 100%);
        border-top: 1px solid rgba(255, 255, 255, 0.06);
        margin-top: auto;
        position: relative;
        overflow: hidden;
    }

    .footer-custom::before {
        content: '';
        position: absolute;
        inset: 0;
        background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.04'/%3E%3C/svg%3E");
        background-size: 120px;
        pointer-events: none;
        opacity: 0.5;
    }

    .footer-content {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 2rem;
        align-items: center;
        position: relative;
        /* above ::before grain */
    }

    .footer-brand {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 1.35rem;
        font-weight: 800;
        letter-spacing: -0.5px;
        color: #ffffff;
        margin-bottom: 5px;
    }

    .footer-brand .brand-dot {
        display: inline-block;
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: var(--brand-green, #16A34A);
        box-shadow: 0 0 6px var(--brand-green, #16A34A);
        margin-bottom: 1px;
    }

    .footer-section p {
        color: #8a8a8a;
        font-size: 0.8rem;
        margin: 0;
        letter-spacing: 0.02em;
        text-transform: uppercase;
    }

    .footer-credit {
        text-align: right;
        color: #6b6b6b;
        font-size: 0.82rem;
    }

    .footer-credit a {
        color: #d4d4d4;
        text-decoration: none;
        font-weight: 600;
        transition: color 0.22s ease;
        border-bottom: 1px solid rgba(22, 163, 74, 0.4);
        padding-bottom: 1px;
    }

    .footer-credit a:hover {
        color: #ffffff;
        border-color: #16A34A;
    }

    .footer-credit .heart {
        color: #f87171;
        display: inline-block;
        transition: transform 0.22s ease;
    }

    .footer-credit:hover .heart {
        transform: scale(1.25);
    }

    /* ── Mobile ─────────────────────────────────────────────── */
    @media (max-width: 768px) {
        .footer-content {
            grid-template-columns: 1fr;
            text-align: center;
        }

        .footer-brand {
            justify-content: center;
        }

        .footer-credit {
            text-align: center;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid rgba(255, 255, 255, 0.06);
        }
    }
</style>

<footer class="footer-custom py-4" aria-label="Site footer">
    <div class="container-fluid px-4">
        <div class="footer-content">

            <div class="footer-section">
                <div class="footer-brand">
                    LovaLog<span class="brand-dot" aria-hidden="true"></span>
                </div>
                <p>Manage your products efficiently</p>
            </div>

            <div class="footer-credit">
                <p class="mb-0">
                    &copy; <?= $current_year ?>
                    <a href="<?= htmlspecialchars($author_url, ENT_QUOTES, 'UTF-8') ?>"
                        target="_blank"
                        rel="noopener noreferrer">
                        <?= htmlspecialchars($author_name, ENT_QUOTES, 'UTF-8') ?>
                    </a>
                    <span class="heart" aria-hidden="true">❤️</span>
                    All rights reserved.
                </p>
            </div>

        </div>
    </div>
</footer>