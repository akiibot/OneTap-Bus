<?php

declare(strict_types=1);
session_start();
require_once 'db.php';
if (!isset($_SESSION['initiated'])) {
    session_regenerate_id(true);
    $_SESSION['initiated'] = true;
}

$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email format.';
    } else {

        $conn = getDbConnection();

        $stmt = $conn->prepare(
            "SELECT id, name, email, password_hash, role
             FROM users
             WHERE email = ?
             LIMIT 1"
        );

        if (!$stmt) {
            $error = 'Database error.';
        } else {

            $stmt->bind_param('s', $email);
            $stmt->execute();

            $result = $stmt->get_result();
            $user = $result->fetch_assoc();

            if (
                $user !== null &&
                isset($user['password_hash']) &&
                is_string($user['password_hash']) &&
                $user['password_hash'] !== '' &&
                password_verify($password, $user['password_hash'])
            ) {

                session_regenerate_id(true);

                $_SESSION['user_id'] = (int) $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['logged_in'] = true;

                header('Location: index.php');
                exit;

            } else {
                $error = 'Invalid email or password.';
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
    <title>Sign In</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="auth.css">
    <style>

    </style>
</head>

<body>
    <?php include 'header.php'; ?>
    <div class="auth-container">

        <div class="visual-area" id="visual">
            ->
            <div class="visual-base"></div>
            <div class="curve curve-back"></div>
            <div class="curve curve-front"></div>
            <img src="bus-image.png" alt="Foreground" class="visual-foreground">

        </div>
        <div class="login-panel">
            <div class="login-box">
                <h2><?= $t['login_title'] ?></h2>
                <?php if ($error !== ''): ?>
                    <div class="error"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <form method="POST" novalidate>
                    <label><?= $t['email'] ?></label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>

                    <label><?= $t['password'] ?></label>
                    <input type="password" name="password" required>

                    <button type="submit"><?= $t['signin'] ?></button>
                </form>

                <p class="signup-text">
                    <?= $t['no_account'] ?>
                    <a href="sign-up.php"><?= $t['go_signup'] ?></a>
                </p>
            </div>
        </div>

    </div>

    <script src="parallax.js"></script>
</body>

</html>