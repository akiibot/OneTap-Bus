<?php
require_once 'db.php';
require_once 'header.php';

if (!isset($_SESSION['logged_in'])) {
    header("Location: signin.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$conn = getDbConnection();

// Fetch Bookings
$sql = "
    SELECT bk.booking_id, bk.seat_number, bk.booking_date, bk.created_at, bk.source_id, bk.dest_id, bk.bus_id,
           b.name AS bus_name, b.type, bc.name AS company_name
    FROM bookings bk
    JOIN bus b ON bk.bus_id = b.bus_id
    JOIN bus_company bc ON b.company_id = bc.company_id
    WHERE bk.user_id = ?
    ORDER BY bk.booking_date DESC, bk.created_at DESC
";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Database error: " . $conn->error);
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="container" style="padding-top: 40px; padding-bottom: 60px;">
    <h2 class="mb-4 text-center"><?= $t['bookings_title'] ?></h2>

    <?php if ($result->num_rows === 0): ?>
        <div class="card text-center" style="padding: 40px;">
            <p style="font-size: 1.2rem; color: var(--text-muted);"><?= $t['no_bookings'] ?></p>
            <a href="index.php" class="btn btn-primary" style="margin-top: 10px; display: inline-block;">
                <?= $t['search_routes'] ?>
            </a>
        </div>
    <?php else: ?>
        <div class="row">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="col-md-6" style="margin-bottom: 20px;">
                    <div class="card booking-card" style="display: flex; gap: 20px; align-items: center;">

                        <!-- Left: Date Badge -->
                        <div
                            style="text-align: center; min-width: 80px; padding: 10px; background: var(--bg-body); border-radius: 8px; border: 1px solid var(--border);">
                            <div style="font-size: 1.5rem; font-weight: 700; color: var(--primary);">
                                <?= date('d', strtotime($row['booking_date'])) ?>
                            </div>
                            <div style="font-size: 0.9rem; text-transform: uppercase; font-weight: 600;">
                                <?= date('M', strtotime($row['booking_date'])) ?>
                            </div>
                            <div style="font-size: 0.8rem; color: var(--text-muted);">
                                <?= date('Y', strtotime($row['booking_date'])) ?>
                            </div>
                        </div>

                        <!-- Right: Info -->
                        <div style="flex: 1;">
                            <div style="display: flex; justify-content: space-between; align-items: start;">
                                <div>
                                    <h3 style="margin: 0; font-size: 1.25rem;"><?= htmlspecialchars($row['bus_name']) ?></h3>
                                    <p style="margin: 4px 0 0; color: var(--text-muted); font-size: 0.9rem;">
                                        <?= htmlspecialchars($row['company_name']) ?> | <?= strtoupper($row['type']) ?>
                                    </p>
                                </div>
                                <span
                                    style="background: #dbeafe; color: #1e40af; padding: 4px 10px; border-radius: 20px; font-size: 0.8rem; font-weight: 600;">
                                    confirmed
                                </span>
                            </div>

                            <div>
                                <a href="ticket.php?bus_id=<?= $row['bus_id'] ?>&seats=<?= $row['seat_number'] ?>&date=<?= $row['booking_date'] ?>"
                                    class="btn btn-primary" style="font-size: 12px; padding: 4px 8px; margin-top: 8px;">
                                    View Ticket
                                </a>
                            </div>

                            <hr style="margin: 12px 0; border: 0; border-top: 1px solid var(--border);">

                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <div>
                                    <span style="color: var(--text-muted); font-size: 0.9rem;"><?= $t['seat'] ?>:</span>
                                    <span
                                        style="font-weight: 700; font-size: 1.1rem; margin-left: 5px; color: var(--text-main);">
                                        <?= htmlspecialchars($row['seat_number']) ?>
                                    </span>
                                </div>
                                <div>
                                    <span style="color: var(--text-muted); font-size: 0.9rem;"><?= $t['booking_id'] ?>:</span>
                                    <span style="font-family: monospace; font-weight: 600;">#<?= $row['booking_id'] ?></span>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php endif; ?>
</div>

<?php
$stmt->close();
$conn->close();
include 'footer.php';
?>