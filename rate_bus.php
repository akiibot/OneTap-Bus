<?php
require_once 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['logged_in'])) {
        header("Location: signin.php");
        exit;
    }

    $bus_id = (int) $_POST['bus_id'];
    $user_id = $_SESSION['user_id'];
    $rating = (int) $_POST['rating'];
    $comment = trim($_POST['comment']);

    if ($rating < 1 || $rating > 5) {
        die("Invalid rating");
    }

    $conn = getDbConnection();

    // Check if user already rated this bus
    $check = $conn->prepare("SELECT rating_id FROM bus_rating WHERE bus_id = ? AND user_id = ?");
    $check->bind_param("ii", $bus_id, $user_id);
    $check->execute();

    if ($check->get_result()->num_rows > 0) {
        // Update existing rating
        $stmt = $conn->prepare("UPDATE bus_rating SET rating = ?, comment = ?, updated_at = NOW() WHERE bus_id = ? AND user_id = ?");
        $stmt->bind_param("isii", $rating, $comment, $bus_id, $user_id);
    } else {
        // Insert new rating
        $stmt = $conn->prepare("INSERT INTO bus_rating (bus_id, user_id, rating, comment) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiis", $bus_id, $user_id, $rating, $comment);
    }

    if ($stmt->execute()) {
        header("Location: bus.php?id=" . $bus_id . "&success=1");
    } else {
        echo "Error: " . $conn->error;
    }
}
?>