<?php
require_once '../includes/db_connect.php';
session_start();
if (!isset($_SESSION['user_id_admin']) || !isset($_SESSION['role_admin']) || $_SESSION['role_admin'] !== 'admin') {
    header('Location: login.php?error=unauthorized');
    exit();
}
$admin_username = $_SESSION['username_admin'];
// Ambil daftar hero
$heroes = [];
$res = $conn->query('SELECT id, name FROM heroes ORDER BY name');
while ($row = $res->fetch_assoc()) {
    $heroes[] = $row;
}
$filter_hero = isset($_GET['hero_id']) ? intval($_GET['hero_id']) : 0;
$where = $filter_hero ? 'WHERE b.hero_id = ' . $filter_hero : '';
$sql = "SELECT b.*, h.name AS hero_name, u.username FROM builds b JOIN heroes h ON b.hero_id = h.id JOIN users u ON b.user_id = u.id $where ORDER BY b.created_at DESC";
$builds = $conn->query($sql);
if (isset($_POST['delete_build_id'])) {
    $build_id = intval($_POST['delete_build_id']);
    // Hapus relasi build_items, build_emblems, build_likes terlebih dahulu
    $stmt = $conn->prepare('DELETE FROM build_items WHERE build_id = ?');
    $stmt->bind_param('i', $build_id);
    $stmt->execute();
    $stmt = $conn->prepare('DELETE FROM build_emblems WHERE build_id = ?');
    $stmt->bind_param('i', $build_id);
    $stmt->execute();
    $stmt = $conn->prepare('DELETE FROM build_likes WHERE build_id = ?');
    $stmt->bind_param('i', $build_id);
    $stmt->execute();
    // Hapus build utama
    $stmt = $conn->prepare('DELETE FROM builds WHERE id = ?');
    $stmt->bind_param('i', $build_id);
    if ($stmt->execute()) {
        $delete_message = '<div class="alert alert-success">Build berhasil dihapus.</div>';
    } else {
        $delete_message = '<div class="alert alert-danger">Gagal menghapus build.</div>';
    }
}
if (isset($_POST['delete_all_dummy'])) {
    // Ambil semua build dummy (nama LIKE 'Top Build Hero %')
    $dummy_ids = [];
    $res = $conn->query("SELECT id FROM builds WHERE name LIKE 'Top Build Hero %'");
    while ($row = $res->fetch_assoc()) $dummy_ids[] = $row['id'];
    if ($dummy_ids) {
        $in = implode(',', $dummy_ids);
        $conn->query("DELETE FROM build_items WHERE build_id IN ($in)");
        $conn->query("DELETE FROM build_likes WHERE build_id IN ($in)");
        $conn->query("DELETE FROM build_emblems WHERE build_id IN ($in)"); // jika tabel ini ada
        $conn->query("DELETE FROM builds WHERE id IN ($in)");
        $delete_message = '<div class="alert alert-success">Semua build dummy berhasil dihapus.</div>';
    } else {
        $delete_message = '<div class="alert alert-info">Tidak ada build dummy yang ditemukan.</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Manage Builds</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <style>
        body { background: #f5f6fa; }
        .main-content { background: #f5f6fa; }
        .header, .header-title { background: #fff; border-radius: 0 0 18px 18px; box-shadow: 0 2px 12px #e0e0e0; }
        .header-title h1 { color: #ffe600; margin-bottom: 4px; }
        .header-title p { color: #23283a; font-weight: 500; }
        .content-body { margin-top: 32px; }
        .builds-table-container {
            background: #fff;
            border-radius: 18px;
            padding: 32px 28px;
            box-shadow: 0 4px 24px #e0e0e0;
            border: 1px solid #ececec;
        }
        .builds-table-container h1 { color: #ffe600; margin-bottom: 24px; }
        .filter-bar { margin-bottom: 24px; }
        .filter-bar label { color: #23283a; font-weight: 600; margin-right: 8px; }
        .filter-bar select { padding: 8px 16px; border-radius: 8px; border: 1px solid #ffe600; background: #fffbe6; color: #23283a; font-weight: 600; }
        table { width: 100%; border-collapse: collapse; margin-top: 18px; background: #fff; }
        th, td { padding: 12px 10px; border-bottom: 1px solid #ececec; text-align: left; }
        th { color: #23283a; font-size: 1.05rem; background: #fffbe6; }
        tr.official { background: #fffde7; }
        tr:hover { background: #f9fbe7; }
        .btn { padding: 7px 18px; border-radius: 8px; border: none; cursor: pointer; font-weight: 600; font-size: 1rem; }
        .btn-official { background: #ffe600; color: #23283a; }
        .btn-unofficial { background: #e0e0e0; color: #23283a; }
        .btn-delete { background: #ff4757; color: #fff; }
        .btn-add { background: #ffe600; color: #23283a; margin-bottom: 18px; box-shadow: 0 2px 8px #ffe60033; }
        .btn-add:hover, .btn-official:hover { background: #fff176; }
        .btn-delete:hover { background: #ff1744; }
        @media (max-width: 900px) {
            .builds-table-container { padding: 18px 4px; }
            th, td { padding: 8px 4px; }
        }
    </style>
</head>
<body>
    <div class="admin-wrapper">
        <?php include 'admin_sidebar.php'; ?>
        <main class="main-content">
            <header class="header">
                <div class="header-title">
                    <h1>Manage Builds</h1>
                    <p>Welcome back, <?php echo htmlspecialchars($admin_username); ?>!</p>
                </div>
            </header>
            <div class="content-body">
                <div class="builds-table-container">
                    <a href="add_build.php" class="btn btn-add">+ Add Official Build</a>
                    <div style="margin-bottom:16px;">
                        <form method="post" style="display:inline;">
                            <button type="submit" name="delete_all_dummy" class="btn btn-delete" onclick="return confirm('Hapus semua build dummy?')">Hapus Semua Build Dummy</button>
                        </form>
                    </div>
                    <form method="get" class="filter-bar">
                        <label for="hero_id">Filter by Hero:</label>
                        <select name="hero_id" id="hero_id" onchange="this.form.submit()">
                            <option value="0">All Heroes</option>
                            <?php foreach ($heroes as $hero): ?>
                                <option value="<?= $hero['id'] ?>"<?= $filter_hero == $hero['id'] ? ' selected' : '' ?>><?= htmlspecialchars($hero['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </form>
                    <table>
                        <tr>
                            <th>ID</th>
                            <th>Hero</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>User</th>
                            <th>Official</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                        <?php while ($build = $builds->fetch_assoc()): ?>
                            <tr class="<?= $build['is_official'] ? 'official' : '' ?>">
                                <td><?= $build['id'] ?></td>
                                <td><?= htmlspecialchars($build['hero_name']) ?></td>
                                <td><?= htmlspecialchars($build['name']) ?></td>
                                <td><?= htmlspecialchars($build['description']) ?></td>
                                <td><?= htmlspecialchars($build['username']) ?></td>
                                <td><?= $build['is_official'] ? 'Yes' : 'No' ?></td>
                                <td><?= $build['created_at'] ?></td>
                                <td>
                                    <?php if ($build['is_official']): ?>
                                        <form method="post" action="set_official.php" style="display:inline;">
                                            <input type="hidden" name="build_id" value="<?= $build['id'] ?>">
                                            <input type="hidden" name="official" value="0">
                                            <button class="btn btn-unofficial" type="submit">Unset Official</button>
                                        </form>
                                    <?php else: ?>
                                        <form method="post" action="set_official.php" style="display:inline;">
                                            <input type="hidden" name="build_id" value="<?= $build['id'] ?>">
                                            <input type="hidden" name="official" value="1">
                                            <button class="btn btn-official" type="submit">Set Official</button>
                                        </form>
                                    <?php endif; ?>
                                    <form method="post" action="delete_build.php" style="display:inline;" onsubmit="return confirm('Delete this build?');">
                                        <input type="hidden" name="build_id" value="<?= $build['id'] ?>">
                                        <button class="btn btn-delete" type="submit">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </table>
                </div>
            </div>
        </main>
    </div>
</body>
</html> 