<?php
require_once '../includes/db_connect.php';
session_start();
if (!isset($_SESSION['user_id_user']) || ($_SESSION['role_user'] ?? '') !== 'admin') {
    header('Location: login.php');
    exit();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $build_id = intval($_POST['build_id'] ?? 0);
    $official = intval($_POST['official'] ?? 0);
    if ($build_id > 0) {
        $stmt = $conn->prepare('UPDATE builds SET is_official = ? WHERE id = ?');
        $stmt->bind_param('ii', $official, $build_id);
        $stmt->execute();
    }
}
header('Location: builds_admin.php');
exit(); 