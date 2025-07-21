<?php
session_start();
require_once '../includes/db_connect.php';

// Cek otorisasi admin
if (!isset($_SESSION['user_id_admin']) || $_SESSION['role_admin'] !== 'admin') {
    header("Location: login.php?error=unauthorized");
    exit();
}

// Ambil ID hero dari parameter GET
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: manage_heroes.php?error=invalid_id");
    exit();
}
$hero_id = intval($_GET['id']);

// Ambil data hero dari database
$sql = "SELECT * FROM heroes WHERE id = ? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $hero_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows !== 1) {
    header("Location: manage_heroes.php?error=hero_not_found");
    exit();
}
$hero = $result->fetch_assoc();

// Ambil semua hero untuk pilihan counter (kecuali diri sendiri)
$all_heroes = [];
$sql_all = "SELECT id, name FROM heroes WHERE id != ? ORDER BY name ASC";
$stmt_all = $conn->prepare($sql_all);
$stmt_all->bind_param("i", $hero_id);
$stmt_all->execute();
$res_all = $stmt_all->get_result();
while ($row = $res_all->fetch_assoc()) {
    $all_heroes[] = $row;
}
// Ambil counter hero yang sudah dipilih beserta deskripsi
$selected_counters = [];
$sql_c = "SELECT counter_hero_id, description FROM hero_counters WHERE hero_id = ?";
$stmt_c = $conn->prepare($sql_c);
$stmt_c->bind_param("i", $hero_id);
$stmt_c->execute();
$res_c = $stmt_c->get_result();
while ($row = $res_c->fetch_assoc()) {
    $selected_counters[$row['counter_hero_id']] = $row['description'];
}
// Ambil hero yang sudah meng-counter hero ini (countered by)
$selected_countered_by = [];
$selected_countered_by_desc = [];
$sql_cb = "SELECT hero_id, description FROM hero_counters WHERE counter_hero_id = ?";
$stmt_cb = $conn->prepare($sql_cb);
$stmt_cb->bind_param("i", $hero_id);
$stmt_cb->execute();
$res_cb = $stmt_cb->get_result();
while ($row = $res_cb->fetch_assoc()) {
    $selected_countered_by[$row['hero_id']] = true;
    $selected_countered_by_desc[$row['hero_id']] = $row['description'];
}

$error_message = '';
$success_message = '';

// Proses update jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $role = trim($_POST['role']);
    $lane = trim($_POST['lane']);
    $tier = trim($_POST['tier']);
    $win_rate = trim($_POST['win_rate']);
    $pick_rate = trim($_POST['pick_rate']);
    $ban_rate = trim($_POST['ban_rate']);
    $image_path = $hero['image_path'];
    $counter_hero_ids = isset($_POST['counter_hero_ids']) ? $_POST['counter_hero_ids'] : [];
    $counter_descriptions = isset($_POST['counter_descriptions']) ? $_POST['counter_descriptions'] : [];
    $countered_by_hero_ids = isset($_POST['countered_by_hero_ids']) ? $_POST['countered_by_hero_ids'] : [];
    $countered_by_descriptions = isset($_POST['countered_by_descriptions']) ? $_POST['countered_by_descriptions'] : [];

    // Validasi data dasar
    if (empty($name) || empty($role) || empty($lane) || empty($tier)) {
        $error_message = 'Please fill in all required fields.';
    } else {
        // Proses upload gambar jika ada file baru
        if (isset($_FILES['hero_image']) && $_FILES['hero_image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = '../assets/images/heroes/';
            // FIX: Cek dan buat direktori jika belum ada
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            $file_name = uniqid() . '_' . basename($_FILES['hero_image']['name']);
            $target_file = $upload_dir . $file_name;
            $image_file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
            $check = getimagesize($_FILES['hero_image']['tmp_name']);
            if ($check !== false) {
                if (move_uploaded_file($_FILES['hero_image']['tmp_name'], $target_file)) {
                    // Hapus gambar lama jika ada
                    if (!empty($hero['image_path']) && file_exists('../' . $hero['image_path'])) {
                        unlink('../' . $hero['image_path']);
                    }
                    $image_path = 'assets/images/heroes/' . $file_name;
                } else {
                    $error_message = 'Sorry, there was an error uploading your file.';
                }
            } else {
                $error_message = 'File is not an image.';
            }
        }

        // Jika tidak ada error, update data ke database
        if (empty($error_message)) {
            $sql = "UPDATE heroes SET name=?, role=?, lane=?, tier=?, image_path=?, win_rate=?, pick_rate=?, ban_rate=? WHERE id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssssddi", $name, $role, $lane, $tier, $image_path, $win_rate, $pick_rate, $ban_rate, $hero_id);
            if ($stmt->execute()) {
                // Update counter hero
                $conn->query("DELETE FROM hero_counters WHERE hero_id = $hero_id");
                if (!empty($counter_hero_ids)) {
                    $stmt_counter = $conn->prepare("INSERT INTO hero_counters (hero_id, counter_hero_id, description) VALUES (?, ?, ?)");
                    foreach ($counter_hero_ids as $counter_id) {
                        $desc = isset($counter_descriptions[$counter_id]) ? $counter_descriptions[$counter_id] : '';
                        $stmt_counter->bind_param("iis", $hero_id, $counter_id, $desc);
                        $stmt_counter->execute();
                    }
                }
                // Update countered by hero
                $conn->query("DELETE FROM hero_counters WHERE counter_hero_id = $hero_id");
                if (!empty($countered_by_hero_ids)) {
                    $stmt_countered = $conn->prepare("INSERT INTO hero_counters (hero_id, counter_hero_id, description) VALUES (?, ?, ?)");
                    foreach ($countered_by_hero_ids as $cb_id) {
                        $desc = isset($countered_by_descriptions[$cb_id]) ? $countered_by_descriptions[$cb_id] : '';
                        $stmt_countered->bind_param("iis", $cb_id, $hero_id, $desc);
                        $stmt_countered->execute();
                    }
                }
                header("Location: manage_heroes.php?success=hero_updated");
                exit();
            } else {
                $error_message = 'Error: Could not update hero in the database.';
            }
        }
    }
}

$admin_username = $_SESSION['username_admin'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Hero - Admin Panel</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="admin-wrapper">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h3>Admin Panel</h3>
            </div>
            <nav class="sidebar-nav">
                <ul>
                    <li><a href="dashboard.php">Dashboard</a></li>
                    <li class="active"><a href="manage_heroes.php">Manage Hero Details</a></li>
                    <li><a href="manage_hero_tiers.php">Manage Hero Tiers</a></li>
                    <li><a href="manage_items.php">Manage Items</a></li>
                    <li><a href="manage_builds.php">Manage Builds</a></li>
                    <li><a href="manage_users.php">Manage Users</a></li>
                    <li><a href="premium_requests.php">Approve Premium Users</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header class="header">
                <div class="header-title">
                    <h1>Edit Hero</h1>
                    <p>Edit the details for this hero below.</p>
                </div>
            </header>

            <div class="content-body">
                <div class="form-container">
                    <?php if (!empty($error_message)): ?>
                        <div class="alert alert-error"><?php echo $error_message; ?></div>
                    <?php endif; ?>
                    <form action="edit_hero.php?id=<?php echo $hero_id; ?>" method="POST" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="name">Hero Name</label>
                            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($hero['name']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="role">Roles</label>
                            <input type="text" id="role" name="role" value="<?php echo htmlspecialchars($hero['role']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="lane">Lanes</label>
                            <input type="text" id="lane" name="lane" value="<?php echo htmlspecialchars($hero['lane']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="tier">Tier</label>
                            <select id="tier" name="tier" required>
                                <?php
                                $tiers = ['SS', 'S', 'A', 'B', 'C', 'D'];
                                foreach ($tiers as $tier_option) {
                                    $selected = ($hero['tier'] === $tier_option) ? 'selected' : '';
                                    echo "<option value=\"$tier_option\" $selected>$tier_option</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="win_rate">Win Rate (%)</label>
                            <input type="number" step="0.01" id="win_rate" name="win_rate" value="<?php echo htmlspecialchars($hero['win_rate']); ?>">
                        </div>
                        <div class="form-group">
                            <label for="pick_rate">Pick Rate (%)</label>
                            <input type="number" step="0.01" id="pick_rate" name="pick_rate" value="<?php echo htmlspecialchars($hero['pick_rate']); ?>">
                        </div>
                        <div class="form-group">
                            <label for="ban_rate">Ban Rate (%)</label>
                            <input type="number" step="0.01" id="ban_rate" name="ban_rate" value="<?php echo htmlspecialchars($hero['ban_rate']); ?>">
                        </div>
                        <div class="form-group">
                            <label for="hero_image">Hero Image</label>
                            <?php if (!empty($hero['image_path'])): ?>
                                <img src="../<?php echo htmlspecialchars($hero['image_path']); ?>" alt="Current Image" class="table-img" style="max-width:80px;display:block;margin-bottom:8px;">
                            <?php endif; ?>
                            <input type="file" id="hero_image" name="hero_image" accept="image/*">
                        </div>
                        <div class="form-group">
                            <label for="counter_hero_ids">Counter Heroes</label>
                            <select id="counter_hero_ids" name="counter_hero_ids[]" multiple size="6" style="width:100%;" onchange="limitCounterHeroSelection()">
                                <?php foreach (
                                    $all_heroes as $h): ?>
                                    <option value="<?php echo $h['id']; ?>" <?php echo array_key_exists($h['id'], $selected_counters) ? 'selected' : ''; ?>><?php echo htmlspecialchars($h['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <small>Pilih maksimal 5 hero yang menjadi counter untuk hero ini.</small>
                            <!-- Deskripsi dihilangkan -->
                        </div>
                        <!-- Countered By Section (editable) -->
                        <div class="form-group">
                            <label for="countered_by_hero_ids">Di-counter oleh (Countered By)</label>
                            <select id="countered_by_hero_ids" name="countered_by_hero_ids[]" multiple size="6" style="width:100%;" onchange="showCounteredByDescriptions()">
                                <?php foreach ($all_heroes as $h): ?>
                                    <option value="<?php echo $h['id']; ?>" <?php echo isset($selected_countered_by[$h['id']]) ? 'selected' : ''; ?>><?php echo htmlspecialchars($h['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <small>Pilih hero yang meng-counter hero ini (bisa lebih dari satu).</small>
                            <div id="countered-by-descriptions-box">
                                <?php foreach ($all_heroes as $h): ?>
                                    <div class="countered-by-desc-row" id="cb-desc-row-<?php echo $h['id']; ?>" style="display:<?php echo isset($selected_countered_by[$h['id']]) ? 'block' : 'none'; ?>;margin-top:8px;">
                                        <label for="countered_by_desc_<?php echo $h['id']; ?>">Alasan <?php echo htmlspecialchars($h['name']); ?> meng-counter hero ini:</label>
                                        <textarea name="countered_by_descriptions[<?php echo $h['id']; ?>]" id="countered_by_desc_<?php echo $h['id']; ?>" rows="2" style="width:100%;"><?php echo isset($selected_countered_by_desc[$h['id']]) ? htmlspecialchars($selected_countered_by_desc[$h['id']]) : ''; ?></textarea>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">Update Hero</button>
                            <a href="manage_heroes.php" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
    <script>
    function showCounterDescriptions() {
        var select = document.getElementById('counter_hero_ids');
        var selected = Array.from(select.selectedOptions).map(opt => opt.value);
        <?php foreach ($all_heroes as $h): ?>
            document.getElementById('desc-row-<?php echo $h['id']; ?>').style.display = selected.includes('<?php echo $h['id']; ?>') ? 'block' : 'none';
        <?php endforeach; ?>
    }
    function showCounteredByDescriptions() {
        var select = document.getElementById('countered_by_hero_ids');
        var selected = Array.from(select.selectedOptions).map(opt => opt.value);
        <?php foreach ($all_heroes as $h): ?>
            document.getElementById('cb-desc-row-<?php echo $h['id']; ?>').style.display = selected.includes('<?php echo $h['id']; ?>') ? 'block' : 'none';
        <?php endforeach; ?>
    }
    function limitCounterHeroSelection() {
        var select = document.getElementById('counter_hero_ids');
        var selected = Array.from(select.selectedOptions);
        if (selected.length > 5) {
            // Deselect yang terakhir dipilih
            selected[selected.length - 1].selected = false;
            alert('Maksimal hanya bisa memilih 5 hero sebagai counter!');
        }
    }
    document.addEventListener('DOMContentLoaded', function() {
        showCounterDescriptions();
        showCounteredByDescriptions();
    });
    </script>
</body>
</html> 