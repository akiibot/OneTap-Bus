<?php

declare(strict_types=1);
session_start();

require_once 'db.php';
$error = '';
$success = '';
$name = '';
$email = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'user';

    // Simple verification to prevent invalid roles
    if (!in_array($role, ['user', 'admin'])) {
        $role = 'user';
    }
    if ($name === '' || $email === '' || $password === '') {
        $error = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email format.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } elseif ($role === 'admin') {
        // Verify Authentication Key for Admins
        $auth_key = $_POST['auth_key'] ?? '';
        $admin_secret = 'admin123'; // Hardcoded secret key for now

        if ($auth_key !== $admin_secret) {
            $error = 'Invalid Admin Authentication Key.';
        }
    }

    if ($error === '') {

        try {
            $conn = getDbConnection();
            $check = $conn->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
            $check->bind_param("s", $email);
            $check->execute();
            $check->store_result();

            if ($check->num_rows > 0) {
                $error = 'Email already registered.';
            } else {

                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare(
                    "INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, ?)"
                );
                $stmt->bind_param("ssss", $name, $email, $password_hash, $role);
                $stmt->execute();

                $success = 'Account created successfully. You can now sign in.';
                $name = $email = '';
            }

        } catch (PDOException $e) {
            $error = 'Something went wrong. Please try again.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Sign Up</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="auth.css">

    <style>
        .success {
            color: #1f7a1f;
            text-align: center;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    <?php include 'header.php'; ?>
    <div class="auth-container">
        <div class="visual-area" id="visual">
            <div class="visual-base"></div>
            <div class="curve curve-back"></div>
            <div class="curve curve-front"></div>
            <img src="bus-image.png" alt="Foreground" class="visual-foreground">
        </div>
        <div class="login-panel">
            <div class="login-box">

                <h2><?= $t['signup_title'] ?></h2>

                <?php if ($error): ?>
                    <div class="error"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="success"><?php echo htmlspecialchars($success); ?></div>
                    <p class="signup-text">
                        <a href="signin.php"><?= $t['go_signin'] ?></a>
                    </p>
                <?php else: ?>

                    <form method="POST" novalidate>

                        <label><?= $t['name'] ?></label>
                        <input type="text" name="name" value="<?php echo htmlspecialchars($name); ?>" required>

                        <label><?= $t['email'] ?></label>
                        <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>

                        <label><?= $t['password'] ?></label>
                        <input type="password" name="password" required>

                        <label><?= $t['account_type'] ?></label>
                        <select name="role"
                            style="width: 100%; padding: 12px; margin-bottom: 20px; border: 1px solid #ddd; border-radius: 8px;">
                            <option value="user"><?= $t['user_passenger'] ?></option>
                            <option value="admin"><?= $t['admin_manager'] ?></option>
                        </select>

                        <!-- Admin Auth Key Field (Hidden by default) -->
                        <div id="authKeyField" style="display:none;">
                            <label><?= $t['admin_key'] ?></label>
                            <input type="password" name="auth_key" placeholder="<?= $t['enter_key'] ?>"
                                style="border-color: var(--accent);">
                        </div>

                        <button type="submit"><?= $t['create_account'] ?></button>
                    </form>

                    <p class="signup-text">
                        <?= $t['have_account'] ?>
                        <a href="signin.php"><?= $t['go_signin'] ?></a>
                    </p>

                <?php endif; ?>

            </div>
        </div>

    </div>

    <script src="parallax.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const roleSelect = document.querySelector('select[name="role"]');
            const authField = document.getElementById('authKeyField');

            function toggleAuthField() {
                if (roleSelect.value === 'admin') {
                    authField.style.display = 'block';
                } else {
                    authField.style.display = 'none';
                }
            }

            // Run on change and on load
            roleSelect.addEventListener('change', toggleAuthField);
            toggleAuthField();
        });
    </script>
</body>

</html>