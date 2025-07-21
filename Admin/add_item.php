<?php
session_start();
if (!isset($_SESSION['user_id_admin']) || !isset($_SESSION['role_admin']) || $_SESSION['role_admin'] !== 'admin') {
    header("Location: login.php?error=unauthorized");
    exit();
}
require_once '../includes/db_connect.php';
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $category = $_POST['category'];
    $image_path = trim($_POST['image_path']);
    $attr = trim($_POST['attr']);
    $desc = trim($_POST['description']);
    $tips = trim($_POST['tips']);
    $usage_desc = trim($_POST['usage_desc']);
    $synergy = trim($_POST['synergy']);
    $counter = trim($_POST['counter']);
    $recommended_heroes = trim($_POST['recommended_heroes']);
    $note = trim($_POST['note']);
    if ($name && $category) {
        $stmt = $conn->prepare("INSERT INTO items (name, category, image_path, attr, description, tips, usage_desc, synergy, counter, recommended_heroes, note) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('sssssssssss', $name, $category, $image_path, $attr, $desc, $tips, $usage_desc, $synergy, $counter, $recommended_heroes, $note);
        if ($stmt->execute()) {
            header('Location: manage_items.php?msg=added');
            exit();
        } else {
            $msg = 'Gagal menambah item.';
        }
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
    <title>Add Item - Admin</title>
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
            <?php $page_title = 'Add Item'; include 'admin_header.php'; ?>
            <div class="content-body">
                <div class="form-box">
                    <h2>Tambah Item</h2>
                    <?php if ($msg): ?><div class="msg"><?php echo $msg; ?></div><?php endif; ?>
                    <form method="post">
                        <div class="form-group">
                            <label>Nama Item</label>
                            <input type="text" name="name" required>
                        </div>
                        <div class="form-group">
                            <label>Kategori Item</label>
                            <select name="category" required>
                                <option value="">-- Pilih Kategori --</option>
                                <option value="Attack">Attack</option>
                                <option value="Magic">Magic</option>
                                <option value="Defense">Defense</option>
                                <option value="Movement">Movement</option>
                                <option value="Jungle">Jungle</option>
                                <option value="Roaming">Roaming</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Path Gambar</label>
                            <input type="text" name="image_path" placeholder="../images/ITEM/Attack/1. Malefic Gun/Malefic_Gun.webp">
                        </div>
                        <div class="form-group">
                            <label>Atribut (HTML diperbolehkan)</label>
                            <input type="text" name="attr">
                        </div>
                        <div class="form-group">
                            <label>Deskripsi</label>
                            <textarea name="description"></textarea>
                        </div>
                        <div class="form-group">
                            <label>Tips Singkat (Quote)</label>
                            <textarea name="tips" placeholder="Tips singkat penggunaan item..."></textarea>
                        </div>
                        <div class="form-group">
                            <label>Penjelasan Penggunaan (Usage Description)</label>
                            <textarea name="usage_desc" placeholder="Penjelasan detail penggunaan item..."></textarea>
                        </div>
                        <div class="form-group">
                            <label>Item Sinergi (pisahkan dengan koma)</label>
                            <input type="text" name="synergy" placeholder="Wind of Nature, Rose Gold Meteor">
                        </div>
                        <div class="form-group">
                            <label>Item Counter (pisahkan dengan koma)</label>
                            <input type="text" name="counter" placeholder="Dominance Ice">
                        </div>
                        <div class="form-group">
                            <label>Hero yang Cocok (pisahkan dengan koma)</label>
                            <input type="text" name="recommended_heroes" placeholder="Layla, Miya, Hanabi, Aulus, Freya">
                        </div>
                        <div class="form-group">
                            <label>Catatan</label>
                            <textarea name="note" placeholder="Catatan penting..."></textarea>
                        </div>
                        <button type="submit" class="btn">Tambah</button>
                        <a href="manage_items.php" class="btn btn-cancel">Batal</a>
                    </form>
                </div>
            </div>
        </main>
    </div>
</body>
</html> 