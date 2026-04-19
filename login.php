<?php
session_start();
include 'database.php';

// Redirect if already logged in
if (isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true) {
    header('Location: index.php');
    exit();
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Please fill in all fields.';
    } else {
        $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ?");
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user   = $result->fetch_assoc();
        $stmt->close();

        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['authenticated'] = true;
            $_SESSION['user_id']       = (int)$user['id'];
            $_SESSION['username']      = $user['username'];
            header('Location: index.php');
            exit();
        } else {
            // Generic message — don't reveal whether username or password was wrong
            $error = 'Invalid username or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="LovaLog — Secure login">
    <meta name="robots" content="noindex, nofollow">
    <link rel="icon" type="image/svg+xml" href="assets/lovalog-favicon.svg">
    <title>LovaLog — Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">

    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }

        body {
            align-items: center;
            justify-content: center;
            padding: 24px 16px;
            position: relative;
            overflow-x: hidden;
        }

        /* Subtle radial glow behind card */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 700px;
            height: 400px;
            background: radial-gradient(ellipse at center, rgba(22, 163, 74, 0.08) 0%, transparent 70%);
            pointer-events: none;
        }

        /* ── Card ───────────────────────────────────────────── */
        .login-card {
            background: #ffffff;
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
            padding: 40px;
            width: 100%;
            max-width: 420px;
            position: relative;
            animation: cardIn 0.4s ease both;
        }

        @keyframes cardIn {
            from {
                opacity: 0;
                transform: translateY(16px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-header {
            text-align: center;
            margin-bottom: 32px;
        }

        .login-brand {
            font-family: var(--font-display);
            font-size: 2rem;
            font-weight: 800;
            letter-spacing: -0.5px;
            color: var(--text-primary);
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 4px;
        }

        .brand-dot {
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--green);
            box-shadow: 0 0 8px var(--green);
            margin-bottom: 2px;
        }

        .login-header p {
            color: var(--text-muted);
            font-size: 0.875rem;
            margin: 0;
        }

        .form-divider {
            border: none;
            border-top: 1px solid var(--border);
            margin: 0 0 28px;
        }

        .field-label {
            font-size: 0.72rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--text-muted);
            margin-bottom: 6px;
            display: block;
        }

        .input-wrap {
            position: relative;
            margin-bottom: 18px;
        }

        .input-wrap input {
            width: 100%;
            font-family: var(--font-body);
            font-size: 0.9rem;
            color: var(--text-primary);
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 11px 44px 11px 14px;
            outline: none;
            transition: border-color var(--transition), box-shadow var(--transition), background var(--transition);
        }

        .input-wrap input:focus {
            border-color: var(--green);
            box-shadow: 0 0 0 3px rgba(22, 163, 74, 0.12);
            background: #ffffff;
        }

        .toggle-btn {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            padding: 4px;
            cursor: pointer;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            transition: color var(--transition);
            line-height: 1;
        }

        .toggle-btn:hover {
            color: var(--text-primary);
        }

        /* ── Error alert ────────────────────────────────────── */
        .alert-error {
            background: rgba(239, 68, 68, 0.08);
            border: 1px solid rgba(239, 68, 68, 0.2);
            border-radius: var(--radius);
            color: #dc2626;
            font-size: 0.875rem;
            font-weight: 500;
            padding: 11px 14px;
            margin-bottom: 22px;
            display: flex;
            align-items: center;
            gap: 8px;
            animation: shake 0.35s ease;
        }

        @keyframes shake {

            0%,
            100% {
                transform: translateX(0);
            }

            25% {
                transform: translateX(-6px);
            }

            75% {
                transform: translateX(6px);
            }
        }

        /* ── Submit button ──────────────────────────────────── */
        .btn-submit {
            width: 100%;
            background: var(--green);
            color: #ffffff;
            border: none;
            border-radius: var(--radius);
            padding: 12px 20px;
            font-family: var(--font-body);
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: all var(--transition);
            box-shadow: var(--shadow-green);
            margin-top: 8px;
        }

        .btn-submit:hover {
            background: var(--green-dark);
            transform: translateY(-1px);
            box-shadow: 0 6px 24px rgba(22, 163, 74, 0.28);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        /* ── Footer links ───────────────────────────────────── */
        .login-footer {
            margin-top: 28px;
            text-align: center;
            font-size: 0.82rem;
            color: var(--text-muted);
        }

        .login-footer a {
            color: var(--green-deep);
            font-weight: 600;
            text-decoration: none;
            transition: color var(--transition);
        }

        .login-footer a:hover {
            color: var(--green);
        }

        .login-footer .divider-dot {
            margin: 0 8px;
            opacity: 0.4;
        }

        /* ── Mobile ─────────────────────────────────────────── */
        @media (max-width: 480px) {
            .login-card {
                padding: 28px 20px;
                border-radius: 16px;
            }

            .login-brand {
                font-size: 1.6rem;
            }
        }
    </style>
</head>

<body>

    <div class="login-card">

        <!-- Header -->
        <div class="login-header">
            <div class="login-brand">
                LovaLog<span class="brand-dot" aria-hidden="true"></span>
            </div>
            <p>Sign in to manage your product catalog</p>
        </div>

        <hr class="form-divider">

        <?php if ($error): ?>
            <div class="alert-error">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10" />
                    <line x1="12" y1="8" x2="12" y2="12" />
                    <line x1="12" y1="16" x2="12.01" y2="16" />
                </svg>
                <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <form method="POST" novalidate>

            <label class="field-label" for="username">Username</label>
            <div class="input-wrap">
                <input type="text" id="username" name="username"
                    placeholder="Enter your username"
                    value="<?= htmlspecialchars($_POST['username'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    required autofocus autocomplete="username">
                <span class="toggle-btn" style="pointer-events:none">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                        <circle cx="12" cy="7" r="4" />
                    </svg>
                </span>
            </div>

            <label class="field-label" for="password">Password</label>
            <div class="input-wrap">
                <input type="password" id="password" name="password"
                    placeholder="Enter your password"
                    required autocomplete="current-password">
                <button type="button" class="toggle-btn" onclick="togglePw()" aria-label="Toggle password visibility">
                    <svg id="eyeIcon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                        <circle cx="12" cy="12" r="3" />
                    </svg>
                </button>
            </div>

            <button type="submit" class="btn-submit">Sign In</button>

        </form>

        <div class="login-footer">
            <a href="index.php">Browse catalog without login</a>
            <span class="divider-dot">•</span>
            <a href="signup.php">Create account</a>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function togglePw() {
            const input = document.getElementById('password');
            const icon = document.getElementById('eyeIcon');
            const isHidden = input.type === 'password';
            input.type = isHidden ? 'text' : 'password';
            icon.innerHTML = isHidden ?
                `<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/>` :
                `<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>`;
        }
    </script>
</body>

</html>