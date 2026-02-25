<?php
require_once 'header.php';

// Handle Add Stop
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_stop') {
    $name = trim($_POST['stop_name']);
    $lat = !empty($_POST['lat']) ? $_POST['lat'] : NULL;
    $lng = !empty($_POST['lng']) ? $_POST['lng'] : NULL;

    if ($name) {
        $stmt = $conn->prepare("INSERT INTO stop (stop_name, latitude, longitude) VALUES (?, ?, ?)");
        $stmt->bind_param("sdd", $name, $lat, $lng);
        $stmt->execute();
        echo "<script>window.location.href='routes.php?tab=stops';</script>";
    }
}

// Handle Delete Stop
if (isset($_GET['delete_stop'])) {
    $id = (int) $_GET['delete_stop'];
    $conn->query("DELETE FROM stop WHERE stop_id = $id");
    echo "<script>window.location.href='routes.php?tab=stops';</script>";
}

$tab = $_GET['tab'] ?? 'stops';
?>

<div class="page-header">
    <h1 class="page-title">Manage Routes & Stops</h1>

    <div>
        <a href="?tab=stops" class="btn <?= $tab == 'stops' ? 'btn-primary' : '' ?>"
            style="text-decoration:none; background:<?= $tab == 'stops' ? 'var(--admin-accent)' : '#fff' ?>; color:<?= $tab == 'stops' ? '#fff' : '#333' ?>">Stops</a>
        <a href="?tab=routes" class="btn <?= $tab == 'routes' ? 'btn-primary' : '' ?>"
            style="text-decoration:none; background:<?= $tab == 'routes' ? 'var(--admin-accent)' : '#fff' ?>; color:<?= $tab == 'routes' ? '#fff' : '#333' ?>">Routes</a>
    </div>
</div>

<?php if ($tab === 'stops'): ?>

    <div style="display:grid; grid-template-columns: 1fr 2fr; gap: 24px;">
        <!-- Add Stop Form -->
        <div class="card" style="height:fit-content">
            <h2 style="margin-top:0; font-size:18px;">Add New Stop</h2>
            <form method="POST">
                <input type="hidden" name="action" value="add_stop">

                <label>Stop Name</label>
                <input type="text" name="stop_name" required placeholder="e.g. Shahbag">

                <div style="display:flex; gap:10px;">
                    <div style="flex:1">
                        <label>Latitude (Optional)</label>
                        <input type="text" name="lat" placeholder="23.7...">
                    </div>
                    <div style="flex:1">
                        <label>Longitude (Optional)</label>
                        <input type="text" name="lng" placeholder="90.3...">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" style="width:100%">Add Stop</button>
            </form>
        </div>

        <!-- Stops List -->
        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Coords</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $stops = $conn->query("SELECT * FROM stop ORDER BY stop_name");
                    while ($s = $stops->fetch_assoc()):
                        ?>
                        <tr>
                            <td><?= $s['stop_id'] ?></td>
                            <td style="font-weight:600"><?= htmlspecialchars($s['stop_name']) ?></td>
                            <td style="color:#64748b; font-size:13px;">
                                <?= ($s['latitude'] && $s['longitude']) ? "{$s['latitude']}, {$s['longitude']}" : "—" ?>
                            </td>
                            <td>
                                <a href="?delete_stop=<?= $s['stop_id'] ?>" onclick="return confirm('Delete?')"
                                    style="color:#ef4444;text-decoration:none;">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

<?php else: ?>

    <div class="card">
        <p style="padding:40px; text-align:center; color:#64748b;">
            Route management involves complex logic (assigning stops to buses in order).<br>
            Please manage Stops first, then we can implement the drag-and-drop route builder in Phase 2.
        </p>
    </div>

<?php endif; ?>

</body>

</html>