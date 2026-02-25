<?php
require_once 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$conn = getDbConnection();
$userId = $_SESSION['user_id'];

$stmt = $conn->prepare("
    SELECT name, email, address, date_of_birth, gender, avatar_url
    FROM users
    WHERE id = ?
");
$stmt->bind_param("i", $userId);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

$firstName = explode(' ', trim($user['name']))[0];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Profile</title>

<style>

* {
    box-sizing: border-box;
    font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
}

body {
    margin: 0;
    background: #f6f8fc;
}

.profile-hero {
    background: radial-gradient(circle at top left, #1f2937, #020617);
    padding: 70px 0;
}

.hero-inner {
    max-width: 1100px;
    margin: auto;
    display: flex;
    align-items: center;
    gap: 40px;
    padding: 0 30px;
}

.hero-avatar img {
    width: 180px;
    height: 220px;
    object-fit: cover;
    border-radius: 14px;
    border: 4px solid #ffffff;
}

.hero-text h1 {
    font-size: 72px;
    margin: 0;
    color: #ffffff;
    font-weight: 800;
    letter-spacing: 2px;
}

.hero-text p {
    margin-top: 10px;
    color: #cbd5f5;
    font-size: 16px;
}


.profile-content {
    padding: 50px 30px;
}

.profile-card {
    max-width: 900px;
    margin: auto;
    background: #ffffff;
    padding: 30px;
    border-radius: 14px;
    box-shadow: 0 10px 30px rgba(0,0,0,.06);
}

.profile-card h2 {
    margin-top: 0;
}

.profile-card input,
.profile-card select {
    margin-bottom: 12px;
}

.profile-card h3 {
    margin-bottom: 18px;
}


label {
    display: block;
    font-weight: 600;
    margin-top: 15px;
    margin-bottom: 6px;
}

input,
select {
    width: 100%;
    padding: 10px 12px;
    border-radius: 8px;
    border: 1px solid #d1d5db;
    font-size: 14px;
}

input:focus,
select:focus {
    outline: none;
    border-color: #4f6ef7;
}

hr {
    margin: 25px 0;
    border: none;
    border-top: 1px solid #e5e7eb;
}

button {
    background: #4f6ef7;
    color: #ffffff;
    border: none;
    padding: 12px 18px;
    border-radius: 8px;
    cursor: pointer;
    font-size: 15px;
    margin-top: 20px;
}

button:hover {
    background: #3f5ee0;
}
</style>
</head>

<body>

<?php include 'header.php'; ?>
<?php if (isset($_SESSION['error'])): ?>
<script>
    alert("<?= htmlspecialchars($_SESSION['error']) ?>");
</script>
<?php unset($_SESSION['error']); endif; ?>


<div class="profile-hero">
    <div class="hero-inner">

        <div class="hero-avatar">
            <img
                src="<?= !empty($user['avatar_url']) ? htmlspecialchars($user['avatar_url']) : 'assets/default-avatar.png' ?>"
                alt="Avatar"
            >
        </div>

        <div class="hero-text">
            <h1><?= htmlspecialchars($firstName) ?></h1>
            <p><?= htmlspecialchars($user['email']) ?></p>
        </div>

    </div>
</div>


<div class="profile-content">
    <div class="profile-card">

        <h2>Profile Information</h2>

        <form method="POST" action="update_profile.php">

            <label>Avatar URL</label>
            <input
                type="url"
                name="avatar_url"
                value="<?= htmlspecialchars($user['avatar_url'] ?? '') ?>"
                placeholder="https://example.com/avatar.jpg"
            >

            <label>Full Name</label>
            <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>

            <label>Email</label>
            <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>

            <label>Address</label>
            <input type="text" name="address" value="<?= htmlspecialchars($user['address'] ?? '') ?>">

            <label>Date of Birth</label>
            <input type="date" name="date_of_birth" value="<?= htmlspecialchars($user['date_of_birth'] ?? '') ?>">

            <label>Gender</label>
            <select name="gender">
                <option value="">Select</option>
                <option value="male" <?= ($user['gender'] ?? '') === 'male' ? 'selected' : '' ?>>Male</option>
                <option value="female" <?= ($user['gender'] ?? '') === 'female' ? 'selected' : '' ?>>Female</option>
                <option value="other" <?= ($user['gender'] ?? '') === 'other' ? 'selected' : '' ?>>Other</option>
            </select>

            <hr>

            <h3>Change Password</h3>

            <input type="password" name="current_password" placeholder="Current password">
            <input type="password" name="new_password" placeholder="New password">
            <input type="password" name="confirm_password" placeholder="Confirm new password">

            <button type="submit">Save Changes</button>
        </form>

    </div>
</div>

</body>
</html>
