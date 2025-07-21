<?php
include '../includes/db_connect.php';

$id = intval($_GET['id'] ?? 0);
if ($id) {
    // Cegah admin hapus dirinya sendiri (opsional, bisa dihapus jika tidak perlu)
    session_start();
    if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $id) {
        header('Location: manage_users.php?err=self');
        exit;
    }
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
}
header('Location: manage_users.php');
exit; 