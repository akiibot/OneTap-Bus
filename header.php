<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Enforce Login Logic
$current_page = basename($_SERVER['PHP_SELF']);
$public_pages = ['signin.php', 'sign-up.php'];

if (!isset($_SESSION['logged_in']) && !in_array($current_page, $public_pages)) {
    header("Location: signin.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>One Tap Bus</title>

    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <?php require_once 'lang.php'; ?>
    <style>
        /* Bangla Font Support */
        body:lang(bn) {
            font-family: 'Hind Siliguri', 'Inter', sans-serif;
        }
    </style>

    <!-- Leaflet Maps -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <!-- Global Styles -->
    <link rel="stylesheet" href="style.css">

    <style>
        /* Dropdown Logic */
        .dropdown {
            position: relative;
        }

        .dropdown-menu {
            position: absolute;
            top: 100%;
            right: 0;
            min-width: 200px;
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: var(--shadow-lg);
            opacity: 0;
            visibility: hidden;
            transform: translateY(10px);
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            padding: 8px;
            z-index: 2000;
            margin-top: 8px;
        }

        .dropdown:hover .dropdown-menu {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .dropdown-menu a {
            display: block;
            padding: 10px 14px;
            border-radius: 8px;
            color: var(--text-main);
            font-weight: 500;
            transition: all 0.15s;
        }

        .dropdown-menu a:hover {
            background: var(--bg-body);
            color: var(--primary);
        }
    </style>

    <script>
        // Apply theme immediately to body to prevent flash
        (function () {
            const theme = localStorage.getItem('theme');
            if (theme === 'dark') {
                document.documentElement.classList.add('dark-mode');
            }
        })();

        function toggleTheme() {
            const html = document.documentElement;
            html.classList.toggle('dark-mode');

            const isDark = html.classList.contains('dark-mode');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');

            updateIcon(isDark);
        }

        function updateIcon(isDark) {
            const iconSun = document.getElementById('icon-sun');
            const iconMoon = document.getElementById('icon-moon');
            if (iconSun && iconMoon) {
                if (isDark) {
                    iconMoon.style.display = 'none';
                    iconSun.style.display = 'block';
                } else {
                    iconMoon.style.display = 'block';
                    iconSun.style.display = 'none';
                }
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            const isDark = document.documentElement.classList.contains('dark-mode');
            updateIcon(isDark);
        });
    </script>
</head>

<body>

    <header class="site-header">
        <div class="container header-inner">
            <!-- Logo -->
            <a href="index.php" class="site-logo">
                <img src="assets/images/icon.png" alt="Logo" class="site-logo-img">
                <span>OneTap Bus</span>
            </a>

            <!-- Navigation -->
            <nav class="nav-links">
                <a href="index.php"><?= $t['home'] ?></a>
                <a href="about.php"><?= $t['about'] ?></a>

                <!-- Language Toggle -->
                <?php if ($_SESSION['lang'] == 'en'): ?>
                    <a href="?lang=bn"
                        style="font-family: 'Hind Siliguri'; font-weight: 600; padding: 4px 10px; border: 1px solid rgba(255,255,255,0.2); border-radius: 6px;">বাংলা</a>
                <?php else: ?>
                    <a href="?lang=en"
                        style="font-weight: 600; padding: 4px 10px; border: 1px solid rgba(255,255,255,0.2); border-radius: 6px;">EN</a>
                <?php endif; ?>

                <!-- Theme Toggle -->
                <button class="theme-toggle" onclick="toggleTheme()" title="Toggle Dark Mode"
                    aria-label="Toggle Dark Mode">
                    <!-- Moon Icon (for Light Mode) -->
                    <svg id="icon-moon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" style="display:block;">
                        <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
                    </svg>
                    <!-- Sun Icon (for Dark Mode) -->
                    <svg id="icon-sun" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" style="display:none;">
                        <circle cx="12" cy="12" r="5"></circle>
                        <line x1="12" y1="1" x2="12" y2="3"></line>
                        <line x1="12" y1="21" x2="12" y2="23"></line>
                        <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
                        <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
                        <line x1="1" y1="12" x2="3" y2="12"></line>
                        <line x1="21" y1="12" x2="23" y2="12"></line>
                        <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
                        <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
                    </svg>
                </button>

                <div class="nav-item user-slot">
                    <?php if (!empty($_SESSION['logged_in'])): ?>
                        <div class="nav-user dropdown" style="cursor:pointer; display:flex; align-items:center; gap:10px;">
                            <span class="dropdown-toggle" style="display:flex; align-items:center; gap:10px;">
                                <img src="assets/images/user.png" alt="User"
                                    style="width:36px; height:36px; border-radius:50%; border:2px solid var(--border);">
                                <span class="username"
                                    style="font-weight:600; max-width:120px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                                    <?= htmlspecialchars($_SESSION['user_name']); ?>
                                </span>
                            </span>

                            <div class="dropdown-menu">
                                <a href="my_bookings.php"><?= $t['my_bookings'] ?></a>
                                <div style="height:1px; background:var(--border); margin:4px 0;"></div>
                                <a href="dashboard.php"><?= $t['dashboard'] ?></a>
                                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                                    <a href="admin/dashboard.php" style="color:var(--primary)"><?= $t['admin_panel'] ?></a>
                                <?php endif; ?>
                                <div style="height:1px; background:var(--border); margin:6px 0;"></div>
                                <a href="logout.php" style="color:#ef4444"><?= $t['logout'] ?></a>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="signin.php" class="btn btn-primary" style="text-decoration:none;">
                            <?= $t['signin'] ?>
                        </a>
                    <?php endif; ?>
                </div>
            </nav>
        </div>
    </header>