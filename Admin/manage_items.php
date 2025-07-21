<?php
session_start();
if (!isset($_SESSION['user_id_admin']) || !isset($_SESSION['role_admin']) || $_SESSION['role_admin'] !== 'admin') {
    header("Location: login.php?error=unauthorized");
    exit();
}
require_once '../includes/db_connect.php';
// Ambil semua item
$items = [];
$res = $conn->query('SELECT * FROM items ORDER BY id ASC');
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $item_id = $row['id'];
        $recipe_count = 0;
        if ($item_id) {
            $q = $conn->query("SELECT COUNT(*) as total FROM item_recipes WHERE item_id=$item_id");
            if ($q && $r = $q->fetch_assoc()) $recipe_count = $r['total'];
        }
        $row['recipe_count'] = $recipe_count;
        $items[] = $row;
    }
}
// Tambahkan proses hapus item
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $del_id = intval($_GET['delete']);
    $conn->query("DELETE FROM items WHERE id=$del_id");
    header('Location: manage_items.php?msg=deleted');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Items - Admin</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <style>
        .item-table { width:100%; border-collapse:collapse; margin-top:30px; }
        .item-table th, .item-table td { border:1px solid #333; padding:8px 12px; text-align:center; }
        .item-table th { background:#232b4a; color:#fff; }
        .item-table td img { width:48px; height:48px; object-fit:cover; border-radius:8px; }
        .btn { padding:6px 16px; border-radius:6px; border:none; cursor:pointer; font-weight:bold; }
        .btn-add { background:#00bfff; color:#fff; margin-bottom:18px; }
        .btn-edit { background:#ffc107; color:#232b4a; }
        .btn-delete { background:#e53935; color:#fff; }
    </style>
</head>
<body>
    <div class="admin-wrapper">
        <?php include 'admin_sidebar.php'; ?>
        <main class="main-content">
            <?php $page_title = 'Manage Items'; include 'admin_header.php'; ?>
            <div class="content-body">
                <a href="add_item.php" class="btn btn-add">+ Add Item</a>
                <table class="item-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Recipe</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                        <tr>
                            <td><?php echo $item['id']; ?></td>
                            <td><?php if ($item['image_path']): ?>
                                <img src="<?php echo (strpos($item['image_path'], '../') === 0 ? $item['image_path'] : '../' . $item['image_path']); ?>" alt="img">
                            <?php endif; ?></td>
                            <td><?php echo htmlspecialchars($item['name']); ?></td>
                            <td><?php echo $item['recipe_count']; ?> komponen</td>
                            <td>
                                <a href="edit_item.php?id=<?php echo $item['id']; ?>" class="btn btn-edit">Edit</a>
                                <a href="manage_items.php?delete=<?php echo $item['id']; ?>" class="btn btn-delete" onclick="return confirm('Delete this item?')">Delete</a>
                                <a href="manage_recipe.php?id=<?php echo $item['id']; ?>" class="btn btn-add" style="background:#6c47ff;">Kelola Resep</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html> 