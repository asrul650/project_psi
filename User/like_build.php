<?php
require_once '../includes/db_connect.php';
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id_user'])) {
    echo json_encode(['success'=>false, 'error'=>'Not logged in']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$build_id = isset($data['build_id']) ? intval($data['build_id']) : 0;
$action = $data['action'] ?? '';
$user_id = $_SESSION['user_id_user'];

if ($build_id <= 0 || !in_array($action, ['like','unlike'])) {
    echo json_encode(['success'=>false, 'error'=>'Invalid data']);
    exit();
}

if ($action === 'like') {
    $stmt = $conn->prepare('INSERT IGNORE INTO build_likes (build_id, user_id) VALUES (?, ?)');
    $stmt->bind_param('ii', $build_id, $user_id);
    $stmt->execute();
} else {
    $stmt = $conn->prepare('DELETE FROM build_likes WHERE build_id = ? AND user_id = ?');
    $stmt->bind_param('ii', $build_id, $user_id);
    $stmt->execute();
}
echo json_encode(['success'=>true]); 