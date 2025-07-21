<?php
session_start();
if (!isset($_SESSION['user_id_admin']) || !isset($_SESSION['role_admin']) || $_SESSION['role_admin'] !== 'admin') {
    header("Location: login.php?error=unauthorized");
    exit();
}
require_once '../includes/db_connect.php';

// Handle status toggle
if (isset($_POST['toggle_status'])) {
    $user_id = $_POST['user_id'];
    $new_status = $_POST['new_status'];
    
    $update_sql = "UPDATE users SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("si", $new_status, $user_id);
    
    if ($stmt->execute()) {
        header("Location: manage_users.php?success=status_updated");
        exit();
    } else {
        header("Location: manage_users.php?error=update_failed");
        exit();
    }
}

// Ambil data user dari database
$sql = "SELECT * FROM users ORDER BY id ASC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Admin</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .user-table { width:100%; border-collapse:collapse; margin-top:30px; }
        .user-table th, .user-table td { border:1px solid #333; padding:8px 12px; text-align:center; }
        .user-table th { background:#232b4a; color:#fff; }
        .btn { padding:6px 16px; border-radius:6px; border:none; cursor:pointer; font-weight:bold; }
        .btn-add { background:#00bfff; color:#fff; margin-bottom:18px; }
        .btn-edit { background:#ffc107; color:#232b4a; }
        .btn-delete { background:#e53935; color:#fff; }
        .btn-toggle { background:#28a745; color:#fff; }
        .role-badge { padding:4px 8px; border-radius:4px; font-size:12px; font-weight:bold; }
        .role-admin { background:#ff6b6b; color:#fff; }
        .role-user { background:#4ecdc4; color:#fff; }
        .status-badge { padding:4px 8px; border-radius:4px; font-size:12px; font-weight:bold; }
        .status-active { background:#28a745; color:#fff; }
        .status-inactive { background:#e53935; color:#fff; }
        .success-message { background:#d4edda; color:#155724; padding:10px; border-radius:4px; margin-bottom:20px; }
        .error-message { background:#f8d7da; color:#721c24; padding:10px; border-radius:4px; margin-bottom:20px; }
    </style>
</head>
<body>
    <div class="admin-wrapper">
        <?php include 'admin_sidebar.php'; ?>
        <main class="main-content">
            <?php $page_title = 'Manage Users'; include 'admin_header.php'; ?>
            <div class="content-body">
                <?php if (isset($_GET['success']) && $_GET['success'] == 'status_updated'): ?>
                    <div class="success-message">User status updated successfully!</div>
                <?php endif; ?>
                <?php if (isset($_GET['error']) && $_GET['error'] == 'update_failed'): ?>
                    <div class="error-message">Failed to update user status!</div>
                <?php endif; ?>
                
                <a href="add_user.php" class="btn btn-add"><i class="fas fa-plus"></i> Add User</a>
                <table class="user-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Role</th>
                            <th>Created At</th>
                            <th>Last Login</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result && $result->num_rows > 0): ?>
                            <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= $row['id'] ?></td>
                                <td><?= htmlspecialchars($row['username']) ?></td>
                                <td><span class="role-badge role-<?= $row['role'] ?>"><?= ucfirst($row['role']) ?></span></td>
                                <td><?= date('d/m/Y H:i', strtotime($row['created_at'])) ?></td>
                                <td>
                                    <?php if ($row['last_login_time']): ?>
                                        <?= date('d/m/Y H:i', strtotime($row['last_login_time'])) ?>
                                    <?php else: ?>
                                        <span style="color:#999;">Never</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="status-badge status-<?= $row['status'] ?>"><?= ucfirst($row['status']) ?></span>
                                </td>
                                <td>
                                    <a href="edit_user.php?id=<?= $row['id'] ?>" class="btn btn-edit"><i class="fas fa-edit"></i> Edit</a>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="user_id" value="<?= $row['id'] ?>">
                                        <input type="hidden" name="new_status" value="<?= $row['status'] == 'active' ? 'inactive' : 'active' ?>">
                                        <button type="submit" name="toggle_status" class="btn btn-toggle" onclick="return confirm('Are you sure you want to <?= $row['status'] == 'active' ? 'deactivate' : 'activate' ?> this user?')">
                                            <i class="fas fa-<?= $row['status'] == 'active' ? 'ban' : 'check' ?>"></i> 
                                            <?= $row['status'] == 'active' ? 'Deactivate' : 'Activate' ?>
                                        </button>
                                    </form>
                                    <a href="delete_user.php?id=<?= $row['id'] ?>" class="btn btn-delete" onclick="return confirm('Are you sure you want to delete this user?')"><i class="fas fa-trash"></i> Delete</a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="7">No users found in the database.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html> 