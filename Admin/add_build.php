<?php
require_once '../includes/db_connect.php';
session_start();
if (!isset($_SESSION['user_id_admin']) || !isset($_SESSION['role_admin']) || $_SESSION['role_admin'] !== 'admin') {
    header('Location: login.php?error=unauthorized');
    exit();
}
$admin_id = $_SESSION['user_id_admin'];

// Ambil semua hero
$heroes = [];
$res = $conn->query('SELECT id, name FROM heroes ORDER BY name');
while ($row = $res->fetch_assoc()) $heroes[] = $row;

// Ambil semua item dan kelompokkan per kategori
$items_by_category = [];
$res = $conn->query('SELECT * FROM items ORDER BY category, name');
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $cat = $row['category'];
        if (!isset($items_by_category[$cat])) $items_by_category[$cat] = [];
        $items_by_category[$cat][] = $row;
    }
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $hero_id = intval($_POST['hero_id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $desc = trim($_POST['description'] ?? '');
    $item_ids = $_POST['items'] ?? [];
    if ($hero_id <= 0 || !$name || count($item_ids) != 6) {
        $error = 'Lengkapi semua field dan pilih 6 item.';
    } else {
        $stmt = $conn->prepare('INSERT INTO builds (hero_id, user_id, name, description, is_official, created_at) VALUES (?, ?, ?, ?, 1, NOW())');
        $stmt->bind_param('iiss', $hero_id, $admin_id, $name, $desc);
        if ($stmt->execute()) {
            $build_id = $stmt->insert_id;
            $item_stmt = $conn->prepare('INSERT INTO build_items (build_id, item_id) VALUES (?, ?)');
            foreach ($item_ids as $item_id) {
                $item_stmt->bind_param('ii', $build_id, $item_id);
                $item_stmt->execute();
            }
            header('Location: manage_builds.php?success=1');
            exit();
        } else {
            $error = 'Gagal menambah build: ' . $stmt->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Official Build</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <style>
        .form-group { margin-bottom: 18px; }
        label { font-weight: 600; }
        select, input[type=text], textarea { width: 100%; padding: 8px; border-radius: 6px; border: 1.5px solid #ccc; }
        .item-category-title { font-size:1.1em; color:#23283a; margin:18px 0 8px 0; font-weight:700; }
        .item-list { display: flex; flex-wrap: wrap; gap: 8px; }
        .item-list label { background: #f5f5f5; border-radius: 6px; padding: 6px 10px; cursor: pointer; border: 1px solid #ddd; }
        .item-list input[type=checkbox] { margin-right: 6px; }
        .btn { background: #ffe600; color: #23283a; font-weight: 700; border: none; border-radius: 6px; padding: 10px 24px; cursor: pointer; }
        .btn:hover { background: #fff176; }
        .error { color: #d32f2f; margin-bottom: 12px; }
    </style>
</head>
<body>
    <div class="admin-wrapper">
        <?php include 'admin_sidebar.php'; ?>
        <main class="main-content">
            <header class="header">
                <div class="header-title">
                    <h1>Add Official Build</h1>
                </div>
            </header>
            <div class="content-body" style="max-width:520px;margin:0 auto;">
                <?php if ($error): ?><div class="error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
                <form method="post">
                    <div class="form-group">
                        <label for="hero_id">Hero</label>
                        <select name="hero_id" id="hero_id" required>
                            <option value="">-- Pilih Hero --</option>
                            <?php foreach ($heroes as $h): ?>
                                <option value="<?= $h['id'] ?>"><?= htmlspecialchars($h['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="name">Build Name</label>
                        <input type="text" name="name" id="name" maxlength="64" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea name="description" id="description" rows="3" maxlength="255" required></textarea>
                    </div>
                    <?php foreach ($items_by_category as $cat => $items): if (!$items) continue; ?>
                        <div class="item-category-title"><?= htmlspecialchars($cat) ?></div>
                        <div class="item-list">
                            <?php foreach ($items as $item): ?>
                                <label><input type="checkbox" name="items[]" value="<?= $item['id'] ?>" onclick="return limitItems(this)" /> <?= htmlspecialchars($item['name']) ?></label>
                            <?php endforeach; ?>
                        </div>
                    <?php endforeach; ?>
                    <button type="submit" class="btn">Tambah Build</button>
                    <a href="manage_builds.php" class="btn" style="background:#eee;color:#23283a;margin-left:12px;">Batal</a>
                </form>
            </div>
        </main>
    </div>
    <script>
    function limitItems(checkbox) {
        const checked = document.querySelectorAll('.item-list input[type=checkbox]:checked');
        if (checked.length > 6) {
            checkbox.checked = false;
            alert('Pilih maksimal 6 item!');
            return false;
        }
        return true;
    }
    </script>
</body>
</html> 