<?php
require_once 'db.php';
require_once 'header.php';

if (!isset($_SESSION['logged_in'])) {
    header('Location: signin.php');
    exit;
}

if (!isset($_GET['bus_id'], $_GET['seats'], $_GET['date'], $_GET['fare'])) {
    die("<div class='container'><p>Invalid payment request.</p></div>");
}

$busId = (int) $_GET['bus_id'];
$seats = explode(',', $_GET['seats']);
$date = $_GET['date'];
$unitFare = (float) $_GET['fare'];
$sourceId = isset($_GET['source']) ? (int) $_GET['source'] : 0;
$destId = isset($_GET['destination']) ? (int) $_GET['destination'] : 0;
$totalAmount = count($seats) * $unitFare;

$conn = getDbConnection();
$stmt = $conn->prepare("SELECT name, type FROM bus WHERE bus_id = ?");
$stmt->bind_param("i", $busId);
$stmt->execute();
$bus = $stmt->get_result()->fetch_assoc();

if (!$bus)
    die("Bus not found");
?>

<div class="container" style="padding-top: 40px; padding-bottom: 60px;">
    <div style="max-width: 600px; margin: 0 auto;">

        <h2 style="margin-bottom: 20px;">Secure Payment</h2>

        <!-- Order Summary -->
        <div class="card" style="margin-bottom: 30px;">
            <div
                style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid var(--border); padding-bottom:15px; margin-bottom:15px;">
                <div>
                    <h3 style="margin:0; font-size:18px;"><?= htmlspecialchars($bus['name']) ?></h3>
                    <span
                        class="badge <?= $bus['type'] === 'ac' ? 'badge-ac' : 'badge-non-ac' ?>"><?= strtoupper($bus['type']) ?></span>
                </div>
                <div style="text-align:right;">
                    <small style="color:var(--text-muted)">Date</small>
                    <div style="font-weight:600"><?= $date ?></div>
                </div>
            </div>

            <div style="margin-bottom: 10px; display:flex; justify-content:space-between;">
                <span>Seats (<?= count($seats) ?>)</span>
                <span style="font-weight:600"><?= implode(', ', $seats) ?></span>
            </div>
            <div style="margin-bottom: 15px; display:flex; justify-content:space-between;">
                <span>Fare per seat</span>
                <span>৳<?= $unitFare ?></span>
            </div>
            <div
                style="border-top:1px dashed var(--border); padding-top:15px; display:flex; justify-content:space-between; font-size:20px; font-weight:700; color:var(--primary);">
                <span>Total Amount</span>
                <span>৳<?= $totalAmount ?></span>
            </div>
        </div>

        <!-- Payment Method -->
        <div class="card">
            <h3 style="margin-bottom: 20px;">Choose Payment Method</h3>

            <div class="tabs" style="display:flex; gap:10px; margin-bottom:20px;">
                <button class="tab-btn active" onclick="switchTab('card')" id="tab-card">Credit/Debit Card</button>
                <button class="tab-btn" onclick="switchTab('mobile')" id="tab-mobile">Mobile Banking</button>
            </div>

            <!-- Card Form -->
            <form id="paymentForm" onsubmit="processPayment(event)">
                <div id="card-section">
                    <div class="form-group">
                        <label>Card Number</label>
                        <input type="text" placeholder="0000 0000 0000 0000" class="form-control" required
                            pattern="\d*">
                    </div>
                    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:20px;">
                        <div class="form-group">
                            <label>Expiry Date</label>
                            <input type="text" placeholder="MM/YY" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>CVC</label>
                            <input type="password" placeholder="123" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Cardholder Name</label>
                        <input type="text" placeholder="Your Name" class="form-control" required>
                    </div>
                </div>

                <!-- Mobile Banking Section -->
                <div id="mobile-section" style="display:none;">
                    <div style="display:flex; gap:20px; margin-bottom:20px; justify-content:center;">
                        <div class="mb-option" onclick="selectMb(this)"
                            style="border:2px solid var(--primary); padding:10px; border-radius:8px; cursor:pointer;">
                            <span style="font-weight:700; color:#e2136e;">Bkash</span>
                        </div>
                        <div class="mb-option" onclick="selectMb(this)"
                            style="border:1px solid var(--border); padding:10px; border-radius:8px; cursor:pointer;">
                            <span style="font-weight:700; color:#f6921e;">Nagad</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Mobile Number</label>
                        <input type="tel" placeholder="017XXXXXXXX" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>PIN (Fake)</label>
                        <input type="password" placeholder="XXXX" class="form-control">
                    </div>
                </div>

                <div style="margin-top:25px;">
                    <button type="submit" class="btn btn-primary"
                        style="width:100%; height:50px; font-size:16px; display:flex; justify-content:center; align-items:center; gap:10px;">
                        <span id="btn-text">Pay ৳<?= $totalAmount ?></span>
                        <div id="spinner" class="spinner" style="display:none;"></div>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .tab-btn {
        flex: 1;
        padding: 12px;
        background: #f1f5f9;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
        color: var(--text-muted);
    }

    .tab-btn.active {
        background: var(--primary);
        color: white;
    }

    .form-group {
        margin-bottom: 15px;
    }

    .form-control {
        width: 100%;
        padding: 12px;
        border: 1px solid var(--border);
        border-radius: 8px;
        font-size: 14px;
    }

    .spinner {
        width: 20px;
        height: 20px;
        border: 3px solid rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        border-top-color: white;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }
</style>

<script>
    function switchTab(type) {
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        document.getElementById('tab-' + type).classList.add('active');

        if (type === 'card') {
            document.getElementById('card-section').style.display = 'block';
            document.getElementById('mobile-section').style.display = 'none';
            document.querySelectorAll('#card-section input').forEach(i => i.required = true);
            document.querySelectorAll('#mobile-section input').forEach(i => i.required = false);
        } else {
            document.getElementById('card-section').style.display = 'none';
            document.getElementById('mobile-section').style.display = 'block';
            document.querySelectorAll('#card-section input').forEach(i => i.required = false);
            document.querySelectorAll('#mobile-section input').forEach(i => i.required = true);
        }
    }

    function selectMb(el) {
        document.querySelectorAll('.mb-option').forEach(d => {
            d.style.borderColor = 'var(--border)';
            d.style.borderWidth = '1px';
        });
        el.style.borderColor = 'var(--primary)';
        el.style.borderWidth = '2px';
    }

    async function processPayment(e) {
        e.preventDefault();

        const btn = document.querySelector('button[type="submit"]');
        const spinner = document.getElementById('spinner');
        const btnText = document.getElementById('btn-text');

        btn.disabled = true;
        btnText.style.display = 'none';
        spinner.style.display = 'block';

        // Simulate network delay
        await new Promise(r => setTimeout(r, 2000));

        // Call Booking API
        const bookingData = {
            bus_id: <?= $busId ?>,
            seats: <?= json_encode($seats) ?>,
            date: '<?= $date ?>',
            source_id: <?= $sourceId ?>,
            dest_id: <?= $destId ?>
        };

        try {
            const res = await fetch('book_seat.php', {
                method: 'POST',
                body: JSON.stringify(bookingData),
                headers: { 'Content-Type': 'application/json' }
            });
            const result = await res.json();

            if (result.success) {
                alert('Payment Successful! Booking Confirmed. ✅');
                // Redirect to Ticket Page
                window.location.href = `ticket.php?bus_id=${bookingData.bus_id}&seats=${bookingData.seats.join(',')}&date=${bookingData.date}&source=${bookingData.source_id}&dest=${bookingData.dest_id}`;
            } else {
                alert('Booking Failed: ' + result.message);
                btn.disabled = false;
                btnText.style.display = 'block';
                spinner.style.display = 'none';
            }
        } catch (err) {
            alert('Network Error');
            btn.disabled = false;
            btnText.style.display = 'block';
            spinner.style.display = 'none';
        }
    }
</script>
</body>

</html>