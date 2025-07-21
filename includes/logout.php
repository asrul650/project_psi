<?php
session_start();
require_once 'db_connect.php';

if (isset($_SESSION['user_id_user'])) {
    $user_id = $_SESSION['user_id_user'];
    // Update status jadi inactive
    $stmt = $conn->prepare("UPDATE users SET status = 'inactive' WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
}

session_destroy();
header('Location: ../index.php');
exit();
?> 