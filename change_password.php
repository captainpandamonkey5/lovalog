<?php
session_start();
include 'check_auth.php';
requireAuth();
include 'database.php';

$user_id         = (int)$_SESSION['user_id'];
$success_message = null;
$error           = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_password = $_POST['current_password'] ?? '';
    $new_password     = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$user) {
        $error = "User not found.";
    } elseif (!password_verify($current_password, $user['password'])) {
        $error = "Current password is incorrect.";
    } elseif (strlen($new_password) < 8) {
        $error = "New password must be at least 8 characters.";
    } elseif ($new_password !== $confirm_password) {
        $error = "New passwords do not match.";
    } else {
        $hashed = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt   = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->bind_param('si', $hashed, $user_id);
        if ($stmt->execute()) {
            $success_message = 'changed';
        } else {
            $error = "Error updating password. Please try again.";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="icon" type="image/svg+xml" href="assets/lovalog-favicon.svg">
    <meta name="robots" content="noindex, nofollow">
    <title>LovaLog — Change Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@600;700;800&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
    <style>
        html,
        body {
            height: 100%;
        }

        .content-wrapper {
            flex: 1;
        }

        .page-hero {
            text-align: center;
            padding: 48px 0 32px;
            position: relative;
        }

        .page-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 500px;
            height: 200px;
            background: radial-gradient(ellipse at center, rgba(22, 163, 74, 0.08) 0%, transparent 70%);
            pointer-events: none;
        }

        .page-eyebrow {
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
            margin-bottom: 14px;
        }

        .page-hero h1 {
            font-family: var(--font-display);
            font-size: clamp(1.6rem, 4vw, 2.2rem);
            font-weight: 800;
            letter-spacing: -0.5px;
            margin-bottom: 8px;
        }

        .page-hero p {
            color: var(--text-muted);
            font-size: 0.95rem;
            margin: 0;
        }

        .form-card {
            background: #ffffff;
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
            padding: 32px;
            margin: 0 auto 48px;
            max-width: 480px;
        }

        @media (min-width: 768px) {
            .form-card {
                padding: 40px;
            }
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
            margin-bottom: 20px;
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
            transition: border-color var(--transition), box-shadow var(--transition);
            outline: none;
        }

        .input-wrap input:focus {
            border-color: var(--green);
            box-shadow: 0 0 0 3px rgba(22, 163, 74, 0.12);
            background: #ffffff;
        }

        .input-wrap input.is-invalid {
            border-color: #ef4444;
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
        }

        .toggle-btn:hover {
            color: var(--text-primary);
        }

        .field-hint {
            font-size: 0.75rem;
            color: var(--text-muted);
            margin-top: 5px;
        }

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
            color: var(--text-muted);
            min-height: 1em;
        }

        .alert-custom {
            border-radius: var(--radius);
            padding: 12px 16px;
            font-size: 0.875rem;
            font-weight: 500;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .alert-error {
            background: rgba(239, 68, 68, 0.08);
            border: 1px solid rgba(239, 68, 68, 0.2);
            color: #dc2626;
        }

        .form-divider {
            border: none;
            border-top: 1px solid var(--border);
            margin: 28px 0;
        }

        .btn-submit {
            width: 100%;
            background: var(--green);
            color: #ffffff;
            border: none;
            border-radius: var(--radius);
            padding: 11px 20px;
            font-family: var(--font-body);
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: all var(--transition);
            box-shadow: var(--shadow-green);
        }

        .btn-submit:hover {
            background: var(--green-dark);
            transform: translateY(-1px);
        }

        .btn-cancel {
            width: 100%;
            background: var(--surface-2);
            color: var(--text-muted);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 11px 20px;
            font-family: var(--font-body);
            font-size: 0.9rem;
            font-weight: 600;
            text-decoration: none;
            text-align: center;
            display: block;
            transition: all var(--transition);
            margin-top: 10px;
        }

        .btn-cancel:hover {
            background: var(--border);
            color: var(--text-primary);
        }
    </style>
</head>

<body>
    <?php include 'header.php'; ?>

    <div class="content-wrapper">
        <div class="container px-3">

            <!-- Hero -->
            <div class="page-hero">
                <div class="page-eyebrow"><i class="fa-solid fa-lock"></i> Account</div>
                <h1>Change Password</h1>
                <p>Keep your account secure with a strong password.</p>
            </div>

            <!-- Card -->
            <div class="form-card">

                <?php if ($error): ?>
                    <div class="alert-custom alert-error">
                        <i class="fa-solid fa-triangle-exclamation"></i> <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                    </div>
                <?php endif; ?>

                <form action="change_password.php" method="POST" id="pwForm">

                    <!-- Current password -->
                    <label class="field-label" for="current_password">Current Password</label>
                    <div class="input-wrap">
                        <input type="password" id="current_password" name="current_password"
                            placeholder="Enter current password" required>
                        <button type="button" class="toggle-btn" onclick="togglePw('current_password')">
                            <?= eyeIcon() ?>
                        </button>
                    </div>

                    <hr class="form-divider">

                    <!-- New password -->
                    <label class="field-label" for="new_password">New Password</label>
                    <div class="input-wrap">
                        <input type="password" id="new_password" name="new_password"
                            placeholder="At least 6 characters" required
                            oninput="checkStrength(this.value)">
                        <button type="button" class="toggle-btn" onclick="togglePw('new_password')">
                            <?= eyeIcon() ?>
                        </button>
                    </div>
                    <div class="strength-bar">
                        <span id="s1"></span><span id="s2"></span>
                        <span id="s3"></span><span id="s4"></span>
                    </div>
                    <div class="strength-label" id="strengthLabel"></div>

                    <!-- Confirm password -->
                    <label class="field-label mt-3" for="confirm_password">Confirm New Password</label>
                    <div class="input-wrap">
                        <input type="password" id="confirm_password" name="confirm_password"
                            placeholder="Repeat new password" required>
                        <button type="button" class="toggle-btn" onclick="togglePw('confirm_password')">
                            <?= eyeIcon() ?>
                        </button>
                    </div>

                    <button type="submit" class="btn-submit">Update Password</button>
                    <a href="index.php" class="btn-cancel">Cancel</a>

                </form>
            </div>

        </div>
    </div>

    <?php include 'footer.php'; ?>

    <?php if ($success_message): ?>
        <div id="toast-notif" class="toast-notif">✅ Password changed successfully!</div>
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
        function togglePw(id) {
            const input = document.getElementById(id);
            input.type = input.type === 'password' ? 'text' : 'password';
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

            label.textContent = val.length > 0 ? labels[score - 1] ?? '' : '';
            label.style.color = score > 0 ? colors[score - 1] : 'var(--text-muted)';
        }
    </script>

</body>

</html>

<?php
function eyeIcon(): string
{
    return '<i class="fa-solid fa-eye"></i>';
}
?>