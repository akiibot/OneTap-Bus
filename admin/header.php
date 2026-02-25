<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Security Check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

require_once '../db.php';
$conn = getDbConnection();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bus Admin Panel</title>

    <!-- Admin Specific Styles to make it look premium -->
    <style>
        :root {
            --admin-primary: #1e293b;
            --admin-accent: #4f6ef7;
            --admin-bg: #f3f4f6;
            --admin-text: #334155;
            --sidebar-width: 260px;
        }

        body {
            margin: 0;
            padding: 0;
            background: var(--admin-bg);
            font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            color: var(--admin-text);
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Styles */
        .sidebar {
            width: var(--sidebar-width);
            background: var(--admin-primary);
            color: #fff;
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            display: flex;
            flex-direction: column;
            box-shadow: 4px 0 24px rgba(0, 0, 0, 0.05);
            z-index: 100;
        }

        .brand {
            padding: 24px;
            font-size: 20px;
            font-weight: 800;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            letter-spacing: -0.5px;
        }

        .brand span {
            color: var(--admin-accent);
        }

        .nav-links {
            padding: 20px 10px;
            flex: 1;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 14px 16px;
            color: #94a3b8;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.2s ease;
            margin-bottom: 4px;
            font-weight: 500;
        }

        .nav-link:hover,
        .nav-link.active {
            background: rgba(255, 255, 255, 0.05);
            color: #fff;
        }

        .nav-link.active {
            background: var(--admin-accent);
            box-shadow: 0 4px 12px rgba(79, 110, 247, 0.4);
        }

        .nav-link i {
            margin-right: 12px;
            font-size: 18px;
        }

        /* Main Content Area */
        .main-content {
            margin-left: var(--sidebar-width);
            flex: 1;
            padding: 32px;
        }

        .page-header {
            margin-bottom: 32px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .page-title {
            font-size: 28px;
            font-weight: 700;
            color: #1e293b;
            margin: 0;
        }

        /* Card Styles */
        .card {
            background: #fff;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        /* Button Styles */
        .btn {
            padding: 10px 20px;
            border-radius: 8px;
            border: none;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.1s;
        }

        .btn-primary {
            background: var(--admin-accent);
            color: white;
            box-shadow: 0 4px 10px rgba(79, 110, 247, 0.3);
        }

        .btn-danger {
            background: #ef4444;
            color: white;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            text-align: left;
            padding: 14px 18px;
            border-bottom: 1px solid #e2e8f0;
        }

        th {
            background: #f8fafc;
            color: #64748b;
            font-weight: 600;
            font-size: 13px;
            text-transform: uppercase;
        }

        tr:hover {
            background: #f8fafc;
        }

        /* Utility */
        .badge {
            padding: 4px 10px;
            border-radius: 99px;
            font-size: 12px;
            font-weight: 700;
        }
    </style>
</head>

<body>

    <div class="sidebar">
        <div class="brand">Bus<span>Admin</span></div>

        <div class="nav-links">
            <a href="dashboard.php"
                class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : '' ?>">
                📊 Dashboard
            </a>
            <a href="buses.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'buses.php' ? 'active' : '' ?>">
                🚌 Buses
            </a>
            <a href="routes.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'routes.php' ? 'active' : '' ?>">
                🛣️ Routes & Stops
            </a>
            <a href="../index.php" class="nav-link">
                🏠 Go to Website
            </a>
        </div>
    </div>

    <div class="main-content">