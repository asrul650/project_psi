<?php
session_start();
require_once '../includes/db_connect.php';

// Cek otorisasi admin
if (!isset($_SESSION['user_id_admin']) || $_SESSION['role_admin'] !== 'admin') {
    header("Location: login.php?error=unauthorized");
    exit();
}

// Ambil ID hero dari parameter GET
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: manage_heroes.php?error=invalid_id");
    exit();
}
$hero_id = intval($_GET['id']);

// Ambil data hero untuk mengetahui path gambar
$sql = "SELECT image_path FROM heroes WHERE id = ? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $hero_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows !== 1) {
    header("Location: manage_heroes.php?error=hero_not_found");
    exit();
}
$hero = $result->fetch_assoc();

// Hapus gambar hero jika ada
if (!empty($hero['image_path']) && file_exists('../' . $hero['image_path'])) {
    unlink('../' . $hero['image_path']);
}

// Hapus data hero dari database
$sql = "DELETE FROM heroes WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $hero_id);
$stmt->execute();

header("Location: manage_heroes.php?success=hero_deleted");
exit(); 