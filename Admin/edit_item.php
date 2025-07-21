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
$id = intval($_GET['id']);
$msg = '';
// Ambil data lama
$stmt = $conn->prepare('SELECT * FROM items WHERE id=?');
$stmt->bind_param('i', $id);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows !== 1) {
    header('Location: manage_items.php');
    exit();
}
$item = $res->fetch_assoc();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $category = $_POST['category'];
    $image_path = trim($_POST['image_path']);
    $desc = trim($_POST['description']);
    $tips = trim($_POST['tips']);
    $usage_desc = trim($_POST['usage_desc']);
    $synergy = trim($_POST['synergy']);
    $counter = trim($_POST['counter']);
    $recommended_heroes = trim($_POST['recommended_heroes']);
    $note = trim($_POST['note']);
    if ($name && $category) {
        $stmt2 = $conn->prepare("UPDATE items SET name=?, image_path=?, description=?, category=?, tips=?, usage_desc=?, synergy=?, counter=?, recommended_heroes=?, note=? WHERE id=?");
        $stmt2->bind_param('ssssssssssi', $name, $image_path, $desc, $category, $tips, $usage_desc, $synergy, $counter, $recommended_heroes, $note, $id);
        if ($stmt2->execute()) {
            header('Location: manage_items.php?msg=updated');
            exit();
        } else {
            $msg = 'Gagal update item.';
        }
        $stmt2->close();
    } else {
        $msg = 'Nama dan kategori item wajib diisi!';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Item - Admin</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <style>
        .form-box { max-width: 480px; margin: 40px auto; background: #232b4a; border-radius: 12px; padding: 32px 28px; color: #fff; }
        .form-box h2 { margin-bottom: 24px; }
        .form-group { margin-bottom: 18px; }
        .form-group label { display:block; margin-bottom:6px; font-weight:600; }
        .form-group input, .form-group select, .form-group textarea { width:100%; padding:8px 10px; border-radius:6px; border:1px solid #444; background:#181c23; color:#fff; }
        .form-group textarea { min-height: 60px; }
        .btn { padding:8px 24px; border-radius:6px; border:none; background:#00bfff; color:#fff; font-weight:bold; cursor:pointer; }
        .btn-cancel { background:#e53935; margin-left:10px; }
        .msg { color:#ffb300; margin-bottom:12px; }
    </style>
</head>
<body>
    <div class="admin-wrapper">
        <?php include 'admin_sidebar.php'; ?>
        <main class="main-content">
            <?php $page_title = 'Edit Item'; include 'admin_header.php'; ?>
            <div class="content-body">
                <div class="form-box">
                    <h2>Edit Item</h2>
                    <?php if ($msg): ?><div class="msg"><?php echo $msg; ?></div><?php endif; ?>
                    <form method="post">
                        <div class="form-group">
                            <label>Nama Item</label>
                            <input type="text" name="name" value="<?php echo htmlspecialchars($item['name']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Kategori Item</label>
                            <select name="category" required>
                                <option value="">-- Pilih Kategori --</option>
                                <?php
                                $types = ['Attack','Magic','Defense','Movement','Jungle','Roaming'];
                                foreach ($types as $t) {
                                    $sel = ($item['category'] === $t) ? 'selected' : '';
                                    echo "<option value='$t' $sel>$t</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Path Gambar</label>
                            <input type="text" name="image_path" value="<?php echo htmlspecialchars($item['image_path']); ?>">
                        </div>
                        <div class="form-group">
                            <label>Deskripsi</label>
                            <textarea name="description"><?php echo htmlspecialchars($item['description']); ?></textarea>
                        </div>
                        <div class="form-group">
                            <label>Tips Singkat (Quote)</label>
                            <textarea name="tips" placeholder="Tips singkat penggunaan item..."><?php echo htmlspecialchars($item['tips']); ?></textarea>
                        </div>
                        <div class="form-group">
                            <label>Penjelasan Penggunaan (Usage Description)</label>
                            <textarea name="usage_desc" placeholder="Penjelasan detail penggunaan item..."><?php echo htmlspecialchars($item['usage_desc']); ?></textarea>
                        </div>
                        <div class="form-group">
                            <label>Item Sinergi (pisahkan dengan koma)</label>
                            <input type="text" name="synergy" value="<?php echo htmlspecialchars($item['synergy']); ?>" placeholder="Wind of Nature, Rose Gold Meteor">
                        </div>
                        <div class="form-group">
                            <label>Item Counter (pisahkan dengan koma)</label>
                            <input type="text" name="counter" value="<?php echo htmlspecialchars($item['counter']); ?>" placeholder="Dominance Ice">
                        </div>
                        <div class="form-group">
                            <label>Hero yang Cocok (pisahkan dengan koma)</label>
                            <input type="text" name="recommended_heroes" value="<?php echo htmlspecialchars($item['recommended_heroes']); ?>" placeholder="Layla, Miya, Hanabi, Aulus, Freya">
                        </div>
                        <div class="form-group">
                            <label>Catatan</label>
                            <textarea name="note" placeholder="Catatan penting..."><?php echo htmlspecialchars($item['note']); ?></textarea>
                        </div>
                        <button type="submit" class="btn">Update</button>
                        <a href="manage_items.php" class="btn btn-cancel">Batal</a>
                    </form>
                </div>
            </div>
        </main>
    </div>
</body>
</html> 