<?php
include '../includes/db_connect.php';
include 'admin_header.php';
include 'admin_sidebar.php';

$id = intval($_GET['id'] ?? 0);
$error = '';
if (!$id) { header('Location: manage_users.php'); exit; }

// Ambil data user
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
if (!$user) { header('Location: manage_users.php'); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role = $_POST['role'];
    $status = $_POST['status'];
    $password = $_POST['password'];
    if ($role && $status) {
        if ($password) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET password=?, role=?, status=? WHERE id=?");
            $stmt->bind_param('sssi', $hash, $role, $status, $id);
        } else {
            $stmt = $conn->prepare("UPDATE users SET role=?, status=? WHERE id=?");
            $stmt->bind_param('ssi', $role, $status, $id);
        }
        if ($stmt->execute()) {
            header('Location: manage_users.php');
            exit;
        } else {
            $error = 'Gagal update user.';
        }
    } else {
        $error = 'Role dan Status wajib diisi!';
    }
}
?>
<div class="admin-content">
    <h2>Edit User</h2>
    <?php if ($error): ?><div style="color:red;"> <?= $error ?> </div><?php endif; ?>
    <form method="post">
        <label>Username:</label><br>
        <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" readonly><br>
        <label>Password (kosongkan jika tidak ingin ganti):</label><br>
        <input type="password" name="password"><br>
        <label>Role:</label><br>
        <select name="role">
            <option value="user" <?= $user['role']==='user'?'selected':'' ?>>User</option>
            <option value="admin" <?= $user['role']==='admin'?'selected':'' ?>>Admin</option>
        </select><br>
        <label>Status:</label><br>
        <select name="status">
            <option value="active" <?= $user['status']==='active'?'selected':'' ?>>Active</option>
            <option value="inactive" <?= $user['status']==='inactive'?'selected':'' ?>>Inactive</option>
        </select><br><br>
        <button type="submit">Update</button>
        <a href="manage_users.php">Batal</a>
    </form>
</div>
<?php include 'admin_footer.php'; ?> 