<?php require_once 'header.php';

// Fetch Stats
$stats = [
    'users' => $conn->query("SELECT COUNT(*) as c FROM users")->fetch_assoc()['c'],
    'buses' => $conn->query("SELECT COUNT(*) as c FROM bus")->fetch_assoc()['c'],
    'reviews' => $conn->query("SELECT COUNT(*) as c FROM bus_rating")->fetch_assoc()['c']
];
?>

<div class="page-header">
    <h1 class="page-title">Dashboard Overview</h1>
</div>

<div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; margin-bottom: 40px;">

    <div class="card" style="border-left: 4px solid #4f6ef7;">
        <h3 style="margin:0; color:#64748b; font-size: 14px; text-transform: uppercase;">Total Users</h3>
        <p style="font-size: 36px; font-weight:800; margin: 10px 0 0; color: #1e293b;">
            <?= number_format($stats['users']) ?>
        </p>
    </div>

    <div class="card" style="border-left: 4px solid #10b981;">
        <h3 style="margin:0; color:#64748b; font-size: 14px; text-transform: uppercase;">Active Buses</h3>
        <p style="font-size: 36px; font-weight:800; margin: 10px 0 0; color: #1e293b;">
            <?= number_format($stats['buses']) ?>
        </p>
    </div>

    <div class="card" style="border-left: 4px solid #f59e0b;">
        <h3 style="margin:0; color:#64748b; font-size: 14px; text-transform: uppercase;">Total Reviews</h3>
        <p style="font-size: 36px; font-weight:800; margin: 10px 0 0; color: #1e293b;">
            <?= number_format($stats['reviews']) ?>
        </p>
    </div>

</div>

<div class="card">
    <h2 style="font-size: 18px; margin-bottom: 20px;">Recent Reviews</h2>

    <table>
        <thead>
            <tr>
                <th>Bus</th>
                <th>User</th>
                <th>Rating</th>
                <th>Comment</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $q = $conn->query("
                SELECT r.*, b.name as bus_name, u.name as user_name 
                FROM bus_rating r 
                JOIN bus b ON b.bus_id = r.bus_id 
                JOIN users u ON u.id = r.user_id 
                ORDER BY r.created_at DESC 
                LIMIT 5
            ");

            while ($row = $q->fetch_assoc()):
                ?>
                <tr>
                    <td style="font-weight:600;"><?= htmlspecialchars($row['bus_name']) ?></td>
                    <td><?= htmlspecialchars($row['user_name']) ?></td>
                    <td>
                        <span style="color:#f59e0b">★</span> <?= $row['rating'] ?>
                    </td>
                    <td style="max-width:300px; opacity: 0.8; font-size: 13px;">
                        <?= substr(htmlspecialchars($row['comment']), 0, 50) ?>...
                    </td>
                    <td><?= date('M d, Y', strtotime($row['created_at'])) ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>

</html>