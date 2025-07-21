<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once '../includes/db_connect.php';

// Cek apakah request method adalah POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: login.php");
    exit();
}

$username = $_POST['username'];
$password = $_POST['password'];

// Hardcode default admin
if ($username === 'admin' && $password === 'password123') {
    session_regenerate_id(true);
    $_SESSION['user_id_admin'] = 1;
    $_SESSION['username_admin'] = 'admin';
    $_SESSION['role_admin'] = 'admin';
    header("Location: dashboard.php");
    exit();
}

// Validasi input dasar
if (empty($username) || empty($password)) {
    header("Location: login.php?error=empty_fields");
    exit();
}

// Cari user berdasarkan username
$sql = "SELECT id, username, password, role FROM users WHERE username = ? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    // Verifikasi password
    if (password_verify($password, $user['password'])) {
        // Cek apakah user adalah admin
        if ($user['role'] === 'admin') {
            // Regenerate session ID untuk keamanan
            session_regenerate_id(true);

            // Simpan data admin ke session
            $_SESSION['user_id_admin'] = $user['id'];
            $_SESSION['username_admin'] = $user['username'];
            $_SESSION['role_admin'] = $user['role'];

            // Redirect ke dashboard admin
            header("Location: dashboard.php");
            exit();
        } else {
            // Jika bukan admin, redirect dengan pesan error
            header("Location: login.php?error=not_admin");
            exit();
        }
    } else {
        // Password salah, redirect error
        header("Location: login.php?error=invalid_credentials");
        exit();
    }
} else {
    // Username tidak ditemukan, redirect error
    header("Location: login.php?error=invalid_credentials");
    exit();
}
?> 