<?php
require_once 'db.php';
require_once 'header.php';
$conn = getDbConnection();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("<div class='container'><p>Invalid bus ID</p></div>");
}

$busId = (int) $_GET['id'];
$isAdmin = isset($_SESSION['user_id']) && $_SESSION['role'] === 'admin';

// Fetch Bus Data
$sql = "SELECT b.name AS bus_name, b.type, bc.name AS company_name, 
        GROUP_CONCAT(COALESCE(s.latitude, 0), ',', COALESCE(s.longitude, 0) ORDER BY rs.stop_order SEPARATOR '|') AS route_coords,
        GROUP_CONCAT(s.stop_name ORDER BY rs.stop_order SEPARATOR ' → ') AS route
        FROM bus b 
        JOIN bus_company bc ON bc.company_id = b.company_id
        JOIN route r ON r.bus_id = b.bus_id 
        JOIN route_stop rs ON rs.route_id = r.route_id
        JOIN stop s ON s.stop_id = rs.stop_id
        WHERE b.bus_id = ? GROUP BY b.bus_id";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $busId);
$stmt->execute();
$stmt->execute();
$bus = $stmt->get_result()->fetch_assoc();

if (!$bus)
    die("<div class='container'><p>Bus not found</p></div>");

// Fetch Reviews
$reviews_sql = "
    SELECT r.*, u.name as user_name 
    FROM bus_rating r 
    JOIN users u ON u.id = r.user_id 
    WHERE r.bus_id = ? 
    ORDER BY r.created_at DESC
";
$r_stmt = $conn->prepare($reviews_sql);
$r_stmt->bind_param("i", $busId);
$r_stmt->execute();
$reviews_result = $r_stmt->get_result();
$reviews = $reviews_result->fetch_all(MYSQLI_ASSOC);

// Calculate Average
$total_rating = 0;
$count = count($reviews);
if ($count > 0) {
    foreach ($reviews as $r)
        $total_rating += $r['rating'];
    $avg_rating = round($total_rating / $count, 1);
} else {
    $avg_rating = 0;
}

// Images
$imgDir = "assets/buses/" . $busId;
$images = is_dir($imgDir) ? glob("$imgDir/*.{jpg,jpeg,png,webp}", GLOB_BRACE) : [];
?>

<div class="container" style="padding-top: 40px; padding-bottom: 60px;">

    <!-- Title -->
    <div style="margin-bottom: 30px;">
        <h1 style="margin-bottom: 10px;"><?= htmlspecialchars($bus['bus_name']) ?></h1>
        <div style="display:flex; gap: 12px; align-items:center;">
            <span class="badge <?= $bus['type'] === 'ac' ? 'badge-ac' : 'badge-non-ac' ?>">
                <?= strtoupper($bus['type']) ?>
            </span>
            <span style="color: var(--text-muted);">
                <?= $t['operated_by'] ?> <?= htmlspecialchars($bus['company_name']) ?>
            </span>
            <span style="color: var(--accent); font-weight: 700; display:flex; align-items:center; gap:4px;">
                ⭐ <?= $avg_rating > 0 ? $avg_rating . '/5' : 'New' ?>
                <span style="font-weight:400; color:var(--text-muted); font-size:14px;">(<?= $count ?> reviews)</span>
            </span>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1.5fr 1fr; gap: 40px;">

        <!-- Left Column: Gallery & Route -->
        <div>
            <!-- Gallery -->
            <div
                style="background: white; border-radius: 16px; overflow: hidden; box-shadow: var(--shadow-md); margin-bottom: 30px;">
                <img src="<?= $images[0] ?? 'assets/buses/default.jpg' ?>" id="mainImage"
                    style="width: 100%; height: 400px; object-fit: cover;">
                <?php if (count($images) > 1): ?>
                    <div style="display: flex; gap: 10px; padding: 12px; overflow-x: auto;">
                        <?php foreach ($images as $img): ?>
                            <img src="<?= $img ?>" onclick="document.getElementById('mainImage').src=this.src"
                                style="width: 80px; height: 60px; object-fit: cover; border-radius: 8px; cursor: pointer;">
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Route Map -->
            <div class="card">
                <h3><?= $t['route_info'] ?></h3>
                <div class="bus-route" style="margin: 16px 0; font-size: 16px;">
                    <?= htmlspecialchars($bus['route']) ?>
                </div>
                <div id="map" style="height: 350px; border-radius: 12px; z-index: 1;"></div>
            </div>

            <!-- Seat Reservation -->
            <div class="card" style="margin-top: 30px;" id="booking-section">
                <h3><?= $t['seat_map'] ?></h3>

                <div style="margin: 20px 0; display:flex; align-items:center; gap: 10px;">
                    <label><?= $t['select_date'] ?>:</label>
                    <input type="date" id="bookingDate" value="<?= date('Y-m-d') ?>" min="<?= date('Y-m-d') ?>"
                        style="padding: 8px; border:1px solid var(--border); border-radius:6px;">
                </div>

                <!-- Legend -->
                <div style="display:flex; gap:15px; margin-bottom:20px; font-size:14px;">
                    <div style="display:flex; align-items:center; gap:5px;">
                        <div style="width:20px; height:20px; border:1px solid #ddd; border-radius:4px;"></div>
                        <?= $t['available'] ?>
                    </div>
                    <div style="display:flex; align-items:center; gap:5px;">
                        <div style="width:20px; height:20px; background:#ef4444; border-radius:4px;"></div>
                        <?= $t['booked'] ?>
                    </div>
                    <div style="display:flex; align-items:center; gap:5px;">
                        <div style="width:20px; height:20px; background:#10b981; border-radius:4px;"></div>
                        <?= $t['selected'] ?>
                    </div>
                </div>

                <!-- Bus Layout (Driver + Seats) -->
                <div
                    style="background:#f1f5f9; padding:20px; border-radius:12px; max-width:350px; margin:0 auto; position:relative;">

                    <!-- Driver Seat -->
                    <div style="text-align:right; margin-bottom: 20px;">
                        <div
                            style="display:inline-block; width:40px; height:40px; border:2px solid #64748b; border-radius:8px; text-align:center; line-height:36px; color:#64748b; font-size:12px;">
                            👮
                        </div>
                    </div>

                    <!-- Seat Grid -->
                    <div id="seatGrid" style="display:grid; grid-template-columns: repeat(5, 1fr); gap:10px;">
                        <!-- JS will populate -->
                    </div>
                </div>

                <div style="margin-top:20px; border-top:1px solid var(--border); padding-top:20px;">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px;">
                        <span style="font-weight:600;"><?= $t['selected'] ?>: <span id="selectedCount"
                                style="color:var(--primary)">0</span></span>
                        <span style="font-weight:700; font-size:18px;"><?= $t['total_price'] ?>: ৳<span
                                id="totalPrice">0</span></span>
                    </div>
                    <button id="bookBtn" class="btn btn-primary" style="width:100%" disabled onclick="bookSeats()">
                        <?= $t['proceed_pay'] ?>
                    </button>
                    <p id="bookingMsg"
                        style="text-align:center; margin-top:10px; font-size:14px; color:var(--text-muted);"></p>
                </div>
            </div>
        </div>

        <!-- Right Column: Ratings & Actions -->
        <div>
            <div class="card" style="position: sticky; top: 100px;">
                <h3><?= $t['rate_this_bus'] ?></h3>

                <?php if (isset($_SESSION['logged_in'])): ?>
                    <form action="rate_bus.php" method="POST">
                        <input type="hidden" name="bus_id" value="<?= $busId ?>">

                        <div class="rating-input"
                            style="display:flex; flex-direction:row-reverse; justify-content:flex-end; gap:5px; margin: 15px 0;">
                            <input type="radio" name="rating" value="5" id="r5" required hidden><label for="r5"
                                style="font-size:24px; color:#cbd5e1; cursor:pointer;">★</label>
                            <input type="radio" name="rating" value="4" id="r4" hidden><label for="r4"
                                style="font-size:24px; color:#cbd5e1; cursor:pointer;">★</label>
                            <input type="radio" name="rating" value="3" id="r3" hidden><label for="r3"
                                style="font-size:24px; color:#cbd5e1; cursor:pointer;">★</label>
                            <input type="radio" name="rating" value="2" id="r2" hidden><label for="r2"
                                style="font-size:24px; color:#cbd5e1; cursor:pointer;">★</label>
                            <input type="radio" name="rating" value="1" id="r1" hidden><label for="r1"
                                style="font-size:24px; color:#cbd5e1; cursor:pointer;">★</label>
                        </div>
                        <style>
                            .rating-input input:checked~label,
                            .rating-input label:hover,
                            .rating-input label:hover~label {
                                color: #f59e0b !important;
                            }
                        </style>

                        <textarea name="comment" placeholder="<?= $t['write_exp'] ?>" rows="3" required></textarea>

                        <button type="submit" class="btn btn-primary"
                            style="width:100%; margin-top:10px;"><?= $t['submit_review'] ?></button>
                    </form>
                <?php else: ?>
                    <div style="text-align:center; padding: 20px 0;">
                        <p style="color: var(--text-muted); margin-bottom: 10px;"><?= $t['sign_in_to_rate'] ?></p>
                        <a href="signin.php" class="btn btn-primary" style="width:100%"><?= $t['signin'] ?></a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Reviews List -->
            <div style="margin-top: 30px;">
                <h3><?= $t['user_reviews'] ?></h3>
                <?php if ($count === 0): ?>
                    <p style="color: var(--text-muted);"><?= $t['no_reviews'] ?></p>
                <?php else: ?>
                    <div style="display:flex; flex-direction:column; gap:16px; margin-top:16px;">
                        <?php foreach ($reviews as $r): ?>
                            <div class="card" style="padding: 16px;">
                                <div style="display:flex; justify-content:space-between; margin-bottom:8px;">
                                    <span style="font-weight:600;"><?= htmlspecialchars($r['user_name']) ?></span>
                                    <span style="color:#f59e0b;">
                                        <?= str_repeat('★', $r['rating']) ?><span
                                            style="color:#cbd5e1;"><?= str_repeat('★', 5 - $r['rating']) ?></span>
                                    </span>
                                </div>
                                <p style="color:var(--text-main); font-size:15px; margin-bottom:8px;">
                                    <?= nl2br(htmlspecialchars($r['comment'])) ?>
                                </p>
                                <small style="color:var(--text-muted); font-size:12px;">
                                    <?= date('M j, Y', strtotime($r['created_at'])) ?>
                                </small>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>

<script>
    // Leaflet Map Logic
    const routeCoords = "<?= $bus['route_coords'] ?>";
    if (routeCoords) {
        const points = routeCoords.split('|').map(p => {
            const [lat, lng] = p.split(',');
            return [parseFloat(lat), parseFloat(lng)];
        }).filter(p => p[0] != 0);

        if (points.length > 0) {
            const map = L.map('map');
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(map);
            const polyline = L.polyline(points, { color: '#4f6ef7', weight: 4 }).addTo(map);
            map.fitBounds(polyline.getBounds());
        } else {
            document.getElementById('map').innerHTML = '<p class="text-center p-4">No map data available.</p>';
        }
    }

    // --- Seat Booking Logic ---
    const busId = <?= $busId ?>;

    // Get fare and route info from URL
    const urlParams = new URLSearchParams(window.location.search);
    const SEAT_PRICE = parseInt(urlParams.get('fare')) || 500;
    const SOURCE_ID = urlParams.get('source');
    const DEST_ID = urlParams.get('destination');

    let selectedSeats = [];

    const dateInput = document.getElementById('bookingDate');
    const seatGrid = document.getElementById('seatGrid');
    const selectedCount = document.getElementById('selectedCount');
    const totalPrice = document.getElementById('totalPrice');
    const bookBtn = document.getElementById('bookBtn');
    const bookingMsg = document.getElementById('bookingMsg');

    async function loadSeats() {
        const date = dateInput.value;
        const res = await fetch(`fetch_seats.php?bus_id=${busId}&date=${date}`);
        const data = await res.json();

        renderGrid(data.capacity, data.booked);
    }

    function renderGrid(capacity, bookedSeats) {
        seatGrid.innerHTML = '';
        selectedSeats = [];
        updateSummary();

        const rows = Math.ceil(capacity / 4);
        const cols = ['A', 'B', 'aisle', 'C', 'D'];

        for (let r = 1; r <= rows; r++) {
            cols.forEach(col => {
                if (col === 'aisle') {
                    const aisle = document.createElement('div');
                    seatGrid.appendChild(aisle);
                    return;
                }

                // Skip if current seat index > capacity (for last row partials)
                const seatNum = col + r;

                const btn = document.createElement('button');
                btn.className = 'seat-btn';
                btn.innerText = seatNum;

                if (bookedSeats.includes(seatNum)) {
                    btn.classList.add('booked');
                    btn.disabled = true;
                } else {
                    btn.onclick = () => toggleSeat(btn, seatNum);
                }

                seatGrid.appendChild(btn);
            });
        }
    }

    function toggleSeat(btn, seatNum) {
        if (selectedSeats.includes(seatNum)) {
            selectedSeats = selectedSeats.filter(s => s !== seatNum);
            btn.classList.remove('selected');
        } else {
            selectedSeats.push(seatNum);
            btn.classList.add('selected');
        }
        updateSummary();
    }

    function updateSummary() {
        selectedCount.innerText = selectedSeats.length;
        totalPrice.innerText = selectedSeats.length * SEAT_PRICE;
        bookBtn.disabled = selectedSeats.length === 0;
    }

    function bookSeats() {
        const date = dateInput.value;
        const seatString = selectedSeats.join(',');

        if (selectedSeats.length === 0) {
            alert("Please select at least one seat.");
            return;
        }

        // Redirect to fake payment gateway with source/dest
        const url = `payment.php?bus_id=${busId}&seats=${seatString}&date=${date}&fare=${SEAT_PRICE}&source=${SOURCE_ID}&destination=${DEST_ID}`;
        window.location.href = url;
    }

    // Init
    dateInput.addEventListener('change', loadSeats);
    loadSeats();
</script>
<style>
    .seat-btn {
        height: 40px;
        border-radius: 6px;
        border: 1px solid #cbd5e1;
        background: white;
        cursor: pointer;
        font-size: 13px;
        color: var(--text-main);
        transition: all 0.2s;
    }

    .seat-btn:hover:not(:disabled) {
        border-color: var(--primary);
        color: var(--primary);
    }

    .seat-btn.selected {
        background: #10b981;
        border-color: #10b981;
        color: white;
    }

    .seat-btn.booked {
        background: #ef4444;
        border-color: #ef4444;
        color: white;
        cursor: not-allowed;
        opacity: 0.6;
    }
</style>
</body>

</html>