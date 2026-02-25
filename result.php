<?php
require_once 'db.php';
require_once 'header.php';

if (!isset($_GET['source'], $_GET['destination'])) {
    die("<div class='container mt-4'><p>Invalid request.</p></div>");
}

$source_id = (int) $_GET['source'];
$dest_id = (int) $_GET['destination'];
$student = isset($_GET['student']) ? 1 : 0;

$conn = getDbConnection();
$stop_stmt = $conn->prepare("SELECT stop_name, latitude, longitude FROM stop WHERE stop_id = ?");

$stop_stmt->bind_param("i", $source_id);
$stop_stmt->execute();
$source = $stop_stmt->get_result()->fetch_assoc();

$stop_stmt->bind_param("i", $dest_id);
$stop_stmt->execute();
$dest = $stop_stmt->get_result()->fetch_assoc();

$stop_stmt->close();
?>

<div class="container" style="padding-top: 40px; padding-bottom: 60px;">

    <!-- Header -->
    <div class="text-center mb-4">
        <h1 style="font-size: 2rem;"><?= $t['search_results'] ?></h1>
        <div style="font-size: 1.25rem; color: var(--text-muted); margin-top: 10px;">
            <span style="color: var(--primary); font-weight: 700;"><?= htmlspecialchars($source['stop_name']) ?></span>
            <span style="margin: 0 10px;">➔</span>
            <span style="color: var(--primary); font-weight: 700;"><?= htmlspecialchars($dest['stop_name']) ?></span>
        </div>
    </div>

    <?php
    $sql = "
    SELECT
        b.bus_id,      
        b.name AS bus_name,
        b.type,
        b.student_fair_allowed AS student_fare_allowed,
        bc.name AS company_name,
        ABS(rs1.stop_order - rs2.stop_order) * fp.per_stop_fare AS base_fare
    FROM route_stop rs1
    JOIN route_stop rs2 ON rs1.route_id = rs2.route_id
    JOIN route r ON r.route_id = rs1.route_id
    JOIN bus b ON b.bus_id = r.bus_id
    JOIN bus_company bc ON bc.company_id = b.company_id
    JOIN fare_policy fp
    WHERE rs1.stop_id = ? AND rs2.stop_id = ?
    ORDER BY b.name
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $source_id, $dest_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo "<div class='card text-center'><p>No direct buses found for this route.</p></div>";
    }

    echo "<div style='max-width: 800px; margin: 0 auto;'>";

    while ($row = $result->fetch_assoc()) {
        $base_fare = (int) $row['base_fare'];
        $final_fare = $base_fare;

        if ($student === 1 && $row['student_fare_allowed'] == 1 && $base_fare > 10) {
            $final_fare = $base_fare / 2;
        }

        $badge_class = ($row['type'] === 'ac') ? 'badge-ac' : 'badge-non-ac';
        $imgFolder = 'assets/buses/';
        $busImg = $imgFolder . $row['bus_id'] . '.png';

        // Check for specific image, otherwise use deterministic placeholder
        if (!file_exists($busImg)) {
            $busImg = $imgFolder . $row['bus_id'] . '.jpg';
            if (!file_exists($busImg)) {
                // Use modulo to pick a consistent placeholder (1-15) based on Bus ID
                $placeholderId = ($row['bus_id'] % 15) + 1;
                $busImg = $imgFolder . $placeholderId . '.png';
            }
        }
        ?>
        <div class="bus-card" style="flex-wrap: wrap;">
            <div class="bus-content" style="flex: 1; margin-right: 20px;">
                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <div style="display:flex; align-items:center; gap: 10px;">
                        <h3><?= htmlspecialchars($row['bus_name']) ?></h3>
                        <a href="bus.php?id=<?= $row['bus_id'] ?>&fare=<?= $final_fare ?>&source=<?= $source_id ?>&destination=<?= $dest_id ?>#booking-section"
                            class="btn btn-primary" style="font-size: 11px; padding: 4px 8px; text-decoration: none;">
                            <?= $t['reserve_seat'] ?>
                        </a>
                    </div>
                    <span class="badge <?= $badge_class ?>"><?= strtoupper($row['type']) ?></span>
                </div>

                <p style="color: var(--text-muted); margin-bottom: 10px;">
                    <?= $t['operated_by'] ?>     <?= htmlspecialchars($row['company_name']) ?>
                </p>

                <?php if ($student && $row['student_fare_allowed'] && $base_fare > 10): ?>
                    <div
                        style="background: #ecfdf5; color: #047857; padding: 6px 12px; border-radius: 8px; display: inline-block; font-size: 13px; font-weight: 600; margin-bottom: 8px;">
                        Student Fare Applied
                    </div>
                <?php endif; ?>

                <div style="font-size: 1.5rem; color: var(--primary); font-weight: 700;">
                    ৳ <?= number_format($final_fare, 2) ?>
                </div>
            </div>

            <div style="display: flex; flex-direction: column; gap: 10px; align-items: flex-end;">
                <div class="bus-image" style="background-image: url('<?= $busImg ?>');"></div>

                <?php if ($source['latitude'] && $dest['latitude']): ?>
                    <button
                        onclick="toggleMap(<?= $row['bus_id'] ?>, <?= $source['latitude'] ?>, <?= $source['longitude'] ?>, <?= $dest['latitude'] ?>, <?= $dest['longitude'] ?>)"
                        style="font-size: 12px; padding: 6px 12px; background: white; border: 1px solid var(--border); color: var(--text-main); cursor:pointer; border-radius:6px;">
                        <?= $t['view_path'] ?>
                    </button>
                <?php endif; ?>
            </div>

            <div id="map-<?= $row['bus_id'] ?>"
                style="display:none; width:100%; height: 250px; margin-top: 20px; border-radius: 12px; z-index: 1;"></div>
        </div>
        <?php
    }
    echo "</div>"; // End width wrapper
    
    $stmt->close();
    $conn->close();
    ?>
</div>

<script>
    function toggleMap(busId, lat1, lng1, lat2, lng2) {
        let mapDiv = document.getElementById('map-' + busId);
        if (mapDiv.style.display === 'none') {
            mapDiv.style.display = 'block';

            // Initialize Map
            if (mapDiv.dataset.initialized === "true") return; // Prevent re-init

            let map = L.map('map-' + busId).setView([lat1, lng1], 13);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(map);

            L.marker([lat1, lng1]).addTo(map).bindPopup('Start');
            L.marker([lat2, lng2]).addTo(map).bindPopup('End');

            let polyline = L.polyline([[lat1, lng1], [lat2, lng2]], { color: 'blue' }).addTo(map);
            map.fitBounds(polyline.getBounds(), { padding: [20, 20] });

            mapDiv.dataset.initialized = "true";
        } else {
            mapDiv.style.display = 'none';
        }
    }
</script>
</body>

</html>