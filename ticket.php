<?php
require_once 'db.php';
require_once 'header.php';

if (!isset($_SESSION['logged_in'])) {
    header("Location: signin.php");
    exit;
}

if (!isset($_GET['bus_id'], $_GET['seats'], $_GET['date'])) {
    die("Invalid ticket request.");
}

$userId = $_SESSION['user_id'];
$busId = (int) $_GET['bus_id'];
$seats = explode(',', $_GET['seats']);
$date = $_GET['date'];

// Verify booking exists and belongs to user
$conn = getDbConnection();
$placeholders = implode(',', array_fill(0, count($seats), '?'));
$types = str_repeat('s', count($seats));

$sql = "
    SELECT 
        b.name AS bus_name, 
        bc.name AS company_name,
        bk.booking_date,
        s1.stop_name AS source,
        s2.stop_name AS destination
    FROM bookings bk
    JOIN bus b ON bk.bus_id = b.bus_id
    JOIN bus_company bc ON b.company_id = bc.company_id
    LEFT JOIN stop s1 ON bk.source_id = s1.stop_id
    LEFT JOIN stop s2 ON bk.dest_id = s2.stop_id
    WHERE bk.user_id = ? 
    AND bk.bus_id = ? 
    AND bk.booking_date = ? 
    AND bk.seat_number IN ($placeholders)
    LIMIT 1
";

$stmt = $conn->prepare($sql);
$params = array_merge([$userId, $busId, $date], $seats);
$stmt->bind_param("iis" . $types, ...$params);
$stmt->execute();
$ticket = $stmt->get_result()->fetch_assoc();

if (!$ticket) {
    die("<div class='container'><p>Ticket not found or access denied.</p></div>");
}
?>

<div class="container" style="padding-top: 40px; padding-bottom: 60px;">

    <div style="max-width: 700px; margin: 0 auto;">
        <div style="margin-bottom: 20px; text-align: right;">
            <button onclick="downloadPDF()" class="btn btn-primary">
                <?= $t['download_pdf'] ?? 'Download PDF' ?> ⬇️
            </button>
        </div>

        <div id="ticket-content"
            style="background: white; padding: 40px; border-radius: 16px; border: 1px solid #e2e8f0; position: relative; overflow: hidden;">

            <!-- Header -->
            <div
                style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px dashed #cbd5e1; padding-bottom: 20px; margin-bottom: 20px;">
                <div>
                    <h1 style="margin: 0; font-size: 24px; color: var(--primary);">BUS FARE</h1>
                    <p style="margin: 5px 0 0; color: var(--text-muted); font-size: 14px;">Electronic Ticket</p>
                </div>
                <div style="text-align: right;">
                    <h2 style="margin: 0; font-size: 20px;"><?= htmlspecialchars($ticket['company_name']) ?></h2>
                    <p style="margin: 5px 0 0; font-weight: 600;"><?= htmlspecialchars($ticket['bus_name']) ?></p>
                </div>
            </div>

            <!-- Journey -->
            <div style="background: #f8fafc; padding: 20px; border-radius: 12px; margin-bottom: 30px;">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div style="text-align: center; flex: 1;">
                        <div
                            style="font-size: 12px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px;">
                            From</div>
                        <div style="font-size: 18px; font-weight: 700; margin-top: 5px;">
                            <?= htmlspecialchars($ticket['source'] ?? 'Unknown') ?>
                        </div>
                    </div>
                    <div style="flex: 0.5; text-align: center; color: var(--text-muted); font-size: 24px;">➔</div>
                    <div style="text-align: center; flex: 1;">
                        <div
                            style="font-size: 12px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px;">
                            To</div>
                        <div style="font-size: 18px; font-weight: 700; margin-top: 5px;">
                            <?= htmlspecialchars($ticket['destination'] ?? 'Unknown') ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Details -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 30px;">
                <div>
                    <div style="margin-bottom: 20px;">
                        <div style="font-size: 13px; color: var(--text-muted);">Passenger Name</div>
                        <div style="font-weight: 600; font-size: 16px;">
                            <?= htmlspecialchars($_SESSION['user_name'] ?? 'Passenger') ?></div>
                    </div>
                    <div>
                        <div style="font-size: 13px; color: var(--text-muted);">Travel Date</div>
                        <div style="font-weight: 600; font-size: 16px;">
                            <?= date('l, F j, Y', strtotime($ticket['booking_date'])) ?>
                        </div>
                    </div>
                </div>
                <div style="text-align: right;">
                    <div style="margin-bottom: 20px;">
                        <div style="font-size: 13px; color: var(--text-muted);">Seat Numbers</div>
                        <div style="font-weight: 700; font-size: 18px; color: var(--primary);">
                            <?= implode(', ', $seats) ?>
                        </div>
                    </div>
                    <div>
                        <div style="font-size: 13px; color: var(--text-muted);">Total Passengers</div>
                        <div style="font-weight: 600; font-size: 16px;"><?= count($seats) ?></div>
                    </div>
                </div>
            </div>

            <!-- Footer / QR -->
            <div
                style="border-top: 2px dashed #cbd5e1; padding-top: 20px; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=<?= urlencode("BusTicket|{$ticket['booking_date']}|" . implode(',', $seats)) ?>"
                        alt="QR" style="mix-blend-mode: multiply;">
                </div>
                <div style="text-align: right;">
                    <p style="margin: 0; font-size: 12px; color: var(--text-muted);">Have a safe journey!</p>
                    <p style="margin: 5px 0 0; font-weight: 700; color: #cbd5e1;">BUS FARE</p>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- HTML2PDF Library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
    function downloadPDF() {
        const element = document.getElementById('ticket-content');
        const opt = {
            margin: 10,
            filename: 'BusTicket_<?= $date ?>.pdf',
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2 },
            jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
        };
        html2pdf().set(opt).from(element).save();
    }
</script>

<?php include 'footer.php'; ?>