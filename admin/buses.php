<?php
require_once 'header.php';

// Handle Add Bus
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $name = $_POST['name'];
    $company_id = $_POST['company_id'];
    $type = $_POST['type'];

    $stmt = $conn->prepare("INSERT INTO bus (name, company_id, type) VALUES (?, ?, ?)");
    $stmt->bind_param("sis", $name, $company_id, $type);

    if ($stmt->execute()) {
        echo "<script>window.location.href='buses.php';</script>";
    }
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $conn->query("DELETE FROM bus WHERE bus_id = $id");
    echo "<script>window.location.href='buses.php';</script>";
}

// Fetch Companies for dropdown
$companies = $conn->query("SELECT * FROM bus_company ORDER BY name");
?>

<div class="page-header">
    <h1 class="page-title">Manage Buses</h1>
    <button onclick="document.getElementById('addModal').style.display='block'" class="btn btn-primary">
        + Add New Bus
    </button>
</div>

<div class="card">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Bus Name</th>
                <th>Company</th>
                <th>Type</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $buses = $conn->query("
                SELECT b.*, c.name as company_name 
                FROM bus b 
                JOIN bus_company c ON c.company_id = b.company_id 
                ORDER BY b.bus_id DESC
            ");

            while ($bus = $buses->fetch_assoc()):
                ?>
                <tr>
                    <td>#<?= $bus['bus_id'] ?></td>
                    <td style="font-weight:600"><?= htmlspecialchars($bus['name']) ?></td>
                    <td><?= htmlspecialchars($bus['company_name']) ?></td>
                    <td>
                        <span class="badge"
                            style="background: <?= $bus['type'] == 'ac' ? '#e3f2fd; color: #0d47a1' : '#f3f4f6; color: #374151' ?>">
                            <?= strtoupper($bus['type']) ?>
                        </span>
                    </td>
                    <td>
                        <a href="?delete=<?= $bus['bus_id'] ?>" onclick="return confirm('Delete this bus?')"
                            style="color: #ef4444; text-decoration:none; font-weight:600;">
                            Delete
                        </a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- Add Modal -->
<div id="addModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:1000;">
    <div
        style="background:white; width:400px; padding:30px; border-radius:12px; margin: 100px auto; position:relative;">
        <h2 style="margin-top:0;">Add New Bus</h2>

        <form method="POST">
            <input type="hidden" name="action" value="add">

            <div style="margin-bottom:16px;">
                <label style="display:block; margin-bottom:8px; font-weight:600;">Bus Name</label>
                <input type="text" name="name" required
                    style="width:100%; padding:10px; border:1px solid #ccc; border-radius:6px;">
            </div>

            <div style="margin-bottom:16px;">
                <label style="display:block; margin-bottom:8px; font-weight:600;">Company</label>
                <select name="company_id" required
                    style="width:100%; padding:10px; border:1px solid #ccc; border-radius:6px;">
                    <?php while ($c = $companies->fetch_assoc()): ?>
                        <option value="<?= $c['company_id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div style="margin-bottom:24px;">
                <label style="display:block; margin-bottom:8px; font-weight:600;">Type</label>
                <select name="type" style="width:100%; padding:10px; border:1px solid #ccc; border-radius:6px;">
                    <option value="non-ac">Non-AC</option>
                    <option value="ac">AC</option>
                </select>
            </div>

            <div style="text-align:right;">
                <button type="button" onclick="document.getElementById('addModal').style.display='none'"
                    style="padding:10px 20px; border:none; background:transparent; cursor:pointer;">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Bus</button>
            </div>
        </form>
    </div>
</div>

</body>

</html>