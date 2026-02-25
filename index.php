<?php
require_once 'db.php';
$conn = getDbConnection();
include 'header.php';
?>

<!-- Hero Section -->
<section class="hero">
    <div class="hero-content">
        <h1 class="hero-title">
            <?= $t['hero_title_prefix'] ?> <span style="color:var(--accent)"><?= $t['hero_title_accent'] ?></span>
            <?= $t['hero_title_suffix'] ?>
        </h1>
        <div style="display:flex; gap:16px; justify-content:center;">
            <a href="javascript:void(0)" id="searchBtn" class="hero-btn">
                <?= $t['search_routes'] ?>
            </a>
            <a href="javascript:void(0)" id="viewBusesBtn" class="hero-btn btn-secondary">
                <?= $t['view_all_buses'] ?>
            </a>
        </div>
    </div>
</section>

<div class="container">

    <!-- Search Box -->
    <div class="search-box" id="search-box">
        <h2 class="text-center mb-4"><?= $t['where_to_go'] ?></h2>

        <form action="result.php" method="GET" class="search-form">
            <div class="form-group">
                <label><?= $t['from'] ?></label>
                <select name="source" required>
                    <option value=""><?= $t['select_source'] ?></option>
                    <?php
                    $stops = $conn->query("SELECT stop_id, stop_name FROM stop ORDER BY stop_name");
                    while ($row = $stops->fetch_assoc()) {
                        echo "<option value='{$row['stop_id']}'>" . htmlspecialchars($row['stop_name']) . "</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label><?= $t['to'] ?></label>
                <select name="destination" required>
                    <option value=""><?= $t['select_dest'] ?></option>
                    <?php
                    $stops->data_seek(0);
                    while ($row = $stops->fetch_assoc()) {
                        echo "<option value='{$row['stop_id']}'>" . htmlspecialchars($row['stop_name']) . "</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="form-group" style="align-self: center; margin-bottom: 2px;">
                <label style="cursor:pointer; display:flex; align-items:center; gap:8px;">
                    <input type="checkbox" name="student" value="1" style="width:auto;">
                    <?= $t['student_fare'] ?>
                </label>
            </div>

            <button type="submit" class="btn-primary" style="width:100%; height:48px; font-size:16px;">
                <?= $t['find_bus'] ?>
            </button>
        </form>
    </div>

    <!-- Bus List Section -->
    <div id="all-buses" style="margin-top: 80px; margin-bottom: 60px;">
        <h2 class="text-center mb-4" style="font-size: 2rem;"><?= $t['available_buses'] ?></h2>

        <?php
        $sql = "
        SELECT
            b.bus_id,
            b.name AS bus_name,
            b.type,
            bc.name AS company_name,
            COUNT(rs.stop_id) AS stop_count,
            GROUP_CONCAT(s.stop_name ORDER BY rs.stop_order SEPARATOR ' → ') AS full_route
        FROM bus b
        JOIN bus_company bc ON bc.company_id = b.company_id
        JOIN route r ON r.bus_id = b.bus_id
        JOIN route_stop rs ON rs.route_id = r.route_id
        JOIN stop s ON s.stop_id = rs.stop_id
        GROUP BY b.bus_id
        ORDER BY b.name
        ";

        $result = $conn->query($sql);

        if ($result->num_rows === 0) {
            echo "<p class='text-center'>{$t['no_buses']}</p>";
        }

        while ($bus = $result->fetch_assoc()) {
            $badge = ($bus['type'] === 'ac') ? 'badge-ac' : 'badge-non-ac';
            $imgFolder = 'assets/buses/';
            $busImg = $imgFolder . $bus['bus_id'] . '.png';

            // Check for specific image, otherwise use deterministic placeholder
            if (!file_exists($busImg)) {
                $busImg = $imgFolder . $bus['bus_id'] . '.jpg';
                if (!file_exists($busImg)) {
                    // Use modulo to pick a consistent placeholder (1-15) based on Bus ID
                    $placeholderId = ($bus['bus_id'] % 15) + 1;
                    $busImg = $imgFolder . $placeholderId . '.png';
                }
            }
            ?>
            <a href='bus.php?id=<?= $bus['bus_id'] ?>' class='bus-card'>
                <div class='bus-content'>
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                        <h3><?= htmlspecialchars($bus['bus_name']) ?></h3>
                        <span class='badge <?= $badge ?>'><?= strtoupper($bus['type']) ?></span>
                    </div>

                    <div class='bus-meta'>
                        <span>🏢 <?= htmlspecialchars($bus['company_name']) ?></span>
                        <span>🛑 <?= $bus['stop_count'] ?>     <?= $t['stops'] ?></span>
                    </div>

                    <div class='bus-route'>
                        <?= htmlspecialchars($bus['full_route']) ?>
                    </div>
                </div>
                <div style="margin-left: 20px; display:flex; flex-direction:column; align-items:flex-end; gap:10px;">
                    <div class='bus-image' style='background-image: url("<?= $busImg ?>");'></div>
                    <span class="btn btn-primary" style="font-size:12px; padding:6px 12px; text-decoration:none;">
                        <?= $t['reserve_seat'] ?>
                    </span>
                </div>
            </a>
            <?php
        }
        $conn->close();
        ?>
    </div>

</div>

<?php include 'footer.php'; ?>

<script>
    // Hero Parallax Effect
    const hero = document.querySelector('.hero');
    if (hero) {
        hero.addEventListener('mousemove', (e) => {
            const rect = hero.getBoundingClientRect();
            const x = (e.clientX - rect.left) / rect.width - 0.5;
            const y = (e.clientY - rect.top) / rect.height - 0.5;
            hero.style.transform = `perspective(1000px) rotateY(${x * 2}deg) rotateX(${-y * 2}deg)`;
        });
        hero.addEventListener('mouseleave', () => {
            hero.style.transform = 'none';
        });
    }

    // Smooth Scroll
    document.getElementById('searchBtn')?.addEventListener('click', () => {
        document.getElementById('search-box').scrollIntoView({ behavior: 'smooth' });
    });

    document.getElementById('viewBusesBtn')?.addEventListener('click', () => {
        document.getElementById('all-buses').scrollIntoView({ behavior: 'smooth' });
    });
</script>