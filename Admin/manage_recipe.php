<?php
session_start();
if (!isset($_SESSION['user_id_admin']) || !isset($_SESSION['role_admin']) || $_SESSION['role_admin'] !== 'admin') {
    header("Location: login.php?error=unauthorized");
    exit();
}
require_once '../includes/db_connect.php';
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: manage_items.php');
    exit();
}
$item_id = intval($_GET['id']);
// Ambil data item utama
$stmt = $conn->prepare('SELECT * FROM items WHERE id=?');
$stmt->bind_param('i', $item_id);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows !== 1) {
    header('Location: manage_items.php');
    exit();
}
$item = $res->fetch_assoc();
// Tambah komponen resep
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['component_item_id'])) {
    $component_id = intval($_POST['component_item_id']);
    if ($component_id && $component_id !== $item_id) {
        // Cek duplikat
        $cek = $conn->query("SELECT * FROM item_recipes WHERE item_id=$item_id AND component_item_id=$component_id");
        if ($cek && $cek->num_rows == 0) {
            $conn->query("INSERT INTO item_recipes (item_id, component_item_id) VALUES ($item_id, $component_id)");
            $msg = 'Komponen berhasil ditambah!';
        } else {
            $msg = 'Komponen sudah ada.';
        }
    } else {
        $msg = 'Pilih komponen yang valid!';
    }
}
// Hapus komponen
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $del_id = intval($_GET['delete']);
    $conn->query("DELETE FROM item_recipes WHERE id=$del_id AND item_id=$item_id");
    header("Location: manage_recipe.php?id=$item_id");
    exit();
}
// Ambil semua komponen resep
$components = [];
$q = $conn->query("SELECT ir.id, i.name, i.image_path FROM item_recipes ir JOIN items i ON ir.component_item_id=i.id WHERE ir.item_id=$item_id");
if ($q) {
    while ($row = $q->fetch_assoc()) $components[] = $row;
}
// Ambil semua item lain untuk dropdown
$all_items = [];
$q2 = $conn->query("SELECT id, name FROM items WHERE id != $item_id ORDER BY name ASC");
if ($q2) {
    while ($row = $q2->fetch_assoc()) $all_items[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Resep - <?php echo htmlspecialchars($item['name']); ?></title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <style>
        .recipe-table { width:100%; border-collapse:collapse; margin-top:30px; }
        .recipe-table th, .recipe-table td { border:1px solid #333; padding:8px 12px; text-align:center; }
        .recipe-table th { background:#232b4a; color:#fff; }
        .recipe-table td img { width:40px; height:40px; object-fit:cover; border-radius:8px; }
        .btn { padding:6px 16px; border-radius:6px; border:none; cursor:pointer; font-weight:bold; }
        .btn-add { background:#00bfff; color:#fff; }
        .btn-delete { background:#e53935; color:#fff; }
        .form-inline { display:flex; gap:12px; align-items:center; margin-top:18px; }
        .msg { color:#ffb300; margin-bottom:12px; }
    </style>
</head>
<body>
    <div class="admin-wrapper">
        <main class="main-content">
            <header class="header">
                <div class="header-title">
                    <h1>Kelola Resep: <?php echo htmlspecialchars($item['name']); ?></h1>
                    <a href="manage_items.php" class="btn btn-cancel" style="background:#888;margin-top:10px;">&larr; Kembali</a>
                </div>
            </header>
            <div class="content-body">
                <?php if ($msg): ?><div class="msg"><?php echo $msg; ?></div><?php endif; ?>
                <h3>Komponen Resep</h3>
                <table class="recipe-table">
                    <thead>
                        <tr><th>Image</th><th>Name</th><th>Action</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($components as $c): ?>
                        <tr>
                            <td><?php if ($c['image_path']): ?><img src="<?php echo htmlspecialchars($c['image_path']); ?>" alt="img"><?php endif; ?></td>
                            <td><?php echo htmlspecialchars($c['name']); ?></td>
                            <td><a href="manage_recipe.php?id=<?php echo $item_id; ?>&delete=<?php echo $c['id']; ?>" class="btn btn-delete" onclick="return confirm('Hapus komponen ini?')">Hapus</a></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($components)): ?><tr><td colspan="3">Belum ada komponen.</td></tr><?php endif; ?>
                    </tbody>
                </table>
                <form method="post" class="form-inline">
                    <label>Tambah Komponen:</label>
                    <select name="component_item_id" required>
                        <option value="">-- Pilih Item --</option>
                        <?php foreach ($all_items as $ai): ?>
                        <option value="<?php echo $ai['id']; ?>"><?php echo htmlspecialchars($ai['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="btn btn-add">Tambah</button>
                </form>
            </div>
        </main>
    </div>
</body>
</html> 