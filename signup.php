<?php
session_start();
include 'database.php';

// Redirect if already logged in
if (isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true) {
    header('Location: index.php');
    exit();
}

$signup_enabled = true; // Set to false to disable public signups
$error          = null;
$success        = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $signup_enabled) {
    $username         = trim($_POST['username'] ?? '');
    $password         = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if ($username === '' || $password === '' || $confirm_password === '') {
        $error = 'All fields are required.';
    } elseif (strlen($username) < 3) {
        $error = 'Username must be at least 3 characters.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match.';
    } else {
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $stmt->store_result();
        $exists = $stmt->num_rows > 0;
        $stmt->close();

        if ($exists) {
            $error = 'That username is already taken.';
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt   = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
            $stmt->bind_param('ss', $username, $hashed);

            if ($stmt->execute()) {
                $success = 'Account created successfully! You can now log in.';
            } else {
                $error = 'Error creating account. Please try again.';
            }
            $stmt->close();
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
    <meta name="description" content="LovaLog — Create an account">
    <meta name="robots" content="noindex, nofollow">
    <link rel="icon" type="image/svg+xml" href="assets/lovalog-favicon.svg">
    <title>LovaLog — Sign Up</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --green: #16A34A;
            --green-dark: #15803D;
            --green-deep: #166534;
            --surface: #f7f8f5;
            --surface-2: #eef0eb;
            --border: rgba(0, 0, 0, 0.07);
            --text-primary: #111810;
            --text-muted: #6b7280;
            --font-display: 'Syne', sans-serif;
            --font-body: 'DM Sans', sans-serif;
            --radius: 12px;
            --radius-lg: 18px;
            --transition: 0.2s ease;
            --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.06), 0 1px 2px rgba(0, 0, 0, 0.04);
            --shadow-green: 0 4px 20px rgba(22, 163, 74, 0.2);
        }

        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }

        body {
            font-family: var(--font-body);
            background-color: var(--surface);
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px 16px;
            position: relative;
            overflow-x: hidden;
        }

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
        .signup-card {
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

        /* ── Header ─────────────────────────────────────────── */
        .signup-header {
            text-align: center;
            margin-bottom: 32px;
        }

        .signup-brand {
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

        .signup-header p {
            color: var(--text-muted);
            font-size: 0.875rem;
            margin: 0;
        }

        /* ── Divider ────────────────────────────────────────── */
        .form-divider {
            border: none;
            border-top: 1px solid var(--border);
            margin: 0 0 28px;
        }

        /* ── Labels ─────────────────────────────────────────── */
        .field-label {
            font-size: 0.72rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--text-muted);
            margin-bottom: 6px;
            display: block;
        }

        /* ── Inputs ─────────────────────────────────────────── */
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

        .field-hint {
            font-size: 0.72rem;
            color: var(--text-muted);
            margin-top: 5px;
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

        /* ── Strength bar ───────────────────────────────────── */
        .strength-bar {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 4px;
            margin-top: 8px;
        }

        .strength-bar span {
            height: 3px;
            border-radius: 99px;
            background: var(--surface-2);
            transition: background 0.3s ease;
        }

        .strength-label {
            font-size: 0.72rem;
            margin-top: 5px;
            min-height: 1em;
            color: var(--text-muted);
        }

        /* ── Alerts ─────────────────────────────────────────── */
        .alert-error,
        .alert-success {
            border-radius: var(--radius);
            font-size: 0.875rem;
            font-weight: 500;
            padding: 11px 14px;
            margin-bottom: 22px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .alert-error {
            background: rgba(239, 68, 68, 0.08);
            border: 1px solid rgba(239, 68, 68, 0.2);
            color: #dc2626;
            animation: shake 0.35s ease;
        }

        .alert-success {
            background: rgba(22, 163, 74, 0.08);
            border: 1px solid rgba(22, 163, 74, 0.2);
            color: var(--green-deep);
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

        /* ── Submit ─────────────────────────────────────────── */
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

        .btn-submit:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }

        /* ── Footer links ───────────────────────────────────── */
        .signup-footer {
            margin-top: 28px;
            text-align: center;
            font-size: 0.82rem;
            color: var(--text-muted);
        }

        .signup-footer a {
            color: var(--green-deep);
            font-weight: 600;
            text-decoration: none;
            transition: color var(--transition);
        }

        .signup-footer a:hover {
            color: var(--green);
        }

        .signup-footer .divider-dot {
            margin: 0 8px;
            opacity: 0.4;
        }

        /* ── Disabled state ─────────────────────────────────── */
        .disabled-notice {
            background: rgba(234, 179, 8, 0.08);
            border: 1px solid rgba(234, 179, 8, 0.25);
            border-radius: var(--radius);
            color: #92400e;
            font-size: 0.875rem;
            padding: 12px 16px;
            text-align: center;
            margin-bottom: 20px;
        }

        /* ── Mobile ─────────────────────────────────────────── */
        @media (max-width: 480px) {
            .signup-card {
                padding: 28px 20px;
                border-radius: 16px;
            }

            .signup-brand {
                font-size: 1.6rem;
            }
        }
    </style>
</head>

<body>

    <div class="signup-card">

        <div class="signup-header">
            <div class="signup-brand">
                LovaLog<span class="brand-dot" aria-hidden="true"></span>
            </div>
            <p>Create an account to manage the catalog</p>
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

        <?php if ($success): ?>
            <div class="alert-success">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12" />
                </svg>
                <?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <?php if ($signup_enabled): ?>

            <form method="POST" novalidate>

                <label class="field-label" for="username">Username</label>
                <div class="input-wrap">
                    <input type="text" id="username" name="username"
                        placeholder="Choose a username"
                        value="<?= htmlspecialchars($_POST['username'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                        required autofocus autocomplete="username"
                        minlength="3">
                    <span class="toggle-btn" style="pointer-events:none">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                            <circle cx="12" cy="7" r="4" />
                        </svg>
                    </span>
                </div>
                <p class="field-hint" style="margin-top:-12px; margin-bottom:18px;">At least 3 characters.</p>

                <label class="field-label" for="password">Password</label>
                <div class="input-wrap">
                    <input type="password" id="password" name="password"
                        placeholder="Create a password"
                        required autocomplete="new-password"
                        oninput="checkStrength(this.value)">
                    <button type="button" class="toggle-btn" onclick="togglePw('password', 'eye1')" aria-label="Toggle password">
                        <svg id="eye1" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                            <circle cx="12" cy="12" r="3" />
                        </svg>
                    </button>
                </div>
                <div class="strength-bar">
                    <span id="s1"></span><span id="s2"></span>
                    <span id="s3"></span><span id="s4"></span>
                </div>
                <div class="strength-label" id="strengthLabel"></div>

                <label class="field-label mt-3" for="confirm_password">Confirm Password</label>
                <div class="input-wrap">
                    <input type="password" id="confirm_password" name="confirm_password"
                        placeholder="Repeat your password"
                        required autocomplete="new-password">
                    <button type="button" class="toggle-btn" onclick="togglePw('confirm_password', 'eye2')" aria-label="Toggle confirm password">
                        <svg id="eye2" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                            <circle cx="12" cy="12" r="3" />
                        </svg>
                    </button>
                </div>

                <button type="submit" class="btn-submit">Create Account</button>

            </form>

        <?php else: ?>

            <div class="disabled-notice">
                🔒 Public signups are currently disabled.<br>Please contact an administrator.
            </div>

        <?php endif; ?>

        <div class="signup-footer">
            <a href="login.php">Already have an account? Sign in</a><br>
            <span class="divider-dot">•</span>
            <a href="index.php">Browse without login</a>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const eyeOpen = `<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>`;
        const eyeClosed = `<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/>`;

        function togglePw(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            const hidden = input.type === 'password';
            input.type = hidden ? 'text' : 'password';
            icon.innerHTML = hidden ? eyeClosed : eyeOpen;
        }

        function checkStrength(val) {
            const bars = [1, 2, 3, 4].map(i => document.getElementById('s' + i));
            const label = document.getElementById('strengthLabel');
            const colors = ['#ef4444', '#f97316', '#eab308', '#16A34A'];
            const labels = ['Too short', 'Weak', 'Fair', 'Strong'];

            let score = 0;
            if (val.length >= 6) score++;
            if (val.length >= 10) score++;
            if (/[A-Z]/.test(val) && /[0-9]/.test(val)) score++;
            if (/[^A-Za-z0-9]/.test(val)) score++;

            bars.forEach((b, i) => {
                b.style.background = i < score ? colors[score - 1] : 'var(--surface-2)';
            });

            label.textContent = val.length > 0 ? (labels[score - 1] ?? '') : '';
            label.style.color = score > 0 ? colors[score - 1] : 'var(--text-muted)';
        }
    </script>
</body>

</html>