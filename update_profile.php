<?php
require_once 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$conn = getDbConnection();
$userId = $_SESSION['user_id'];
$name = trim($_POST['name']);
$email = trim($_POST['email']);
$address = $_POST['address'] ?? null;
$dob = $_POST['date_of_birth'] ?? null;
$gender = $_POST['gender'] ?? null;
$avatarUrl = $_POST['avatar_url'] ?? null;

$currentPw = $_POST['current_password'] ?? '';
$newPw = $_POST['new_password'] ?? '';
$confirmPw = $_POST['confirm_password'] ?? '';
$stmt = $conn->prepare("
    UPDATE users 
    SET name = ?, email = ?, address = ?, date_of_birth = ?, gender = ?, avatar_url = ?
    WHERE id = ?
");
$stmt->bind_param(
    "ssssssi",
    $name,
    $email,
    $address,
    $dob,
    $gender,
    $avatarUrl,
    $userId
);
$stmt->execute();
$stmt->close();
if (!empty($newPw)) {

    if ($newPw !== $confirmPw) {
        $_SESSION['error'] = "New passwords do not match.";
        header("Location: dashboard.php");
        exit;
    }
    $stmt = $conn->prepare("SELECT password_hash FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->bind_result($hashedPw);
    $stmt->fetch();
    $stmt->close();
    if (!password_verify($currentPw, $hashedPw)) {
        $_SESSION['error'] = "Current password is incorrect.";
        header("Location: dashboard.php");
        exit;
    }
    $newHash = password_hash($newPw, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
    $stmt->bind_param("si", $newHash, $userId);
    $stmt->execute();
    $stmt->close();
}

header("Location: dashboard.php?success=1");
exit;
