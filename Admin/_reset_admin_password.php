<?php
require_once '../includes/db_connect.php';

// --- Konfigurasi Admin ---
$admin_username = 'admin';
$admin_password = 'password123';
// -------------------------

// Hash password baru
$hashed_password = password_hash($admin_password, PASSWORD_DEFAULT);

// Cek apakah user 'admin' sudah ada
$sql_check = "SELECT id FROM users WHERE username = ? LIMIT 1";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("s", $admin_username);
$stmt_check->execute();
$result = $stmt_check->get_result();

if ($result->num_rows > 0) {
    // Jika user ada, UPDATE passwordnya
    $sql_update = "UPDATE users SET password = ? WHERE username = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("ss", $hashed_password, $admin_username);
    if ($stmt_update->execute()) {
        echo "<h1>Sukses!</h1>";
        echo "<p>Password untuk user '<b>" . htmlspecialchars($admin_username) . "</b>' telah berhasil direset.</p>";
        echo "<p>Silakan login kembali dengan:</p>";
        echo "<ul>";
        echo "<li>Username: <b>" . htmlspecialchars($admin_username) . "</b></li>";
        echo "<li>Password: <b>" . htmlspecialchars($admin_password) . "</b></li>";
        echo "</ul>";
        echo "<p style='color:red;'><b>PENTING:</b> Hapus file ini ('_reset_admin_password.php') dari server Anda sekarang juga demi keamanan!</p>";
    } else {
        echo "<h1>Error!</h1>";
        echo "<p>Gagal mengupdate password admin. Error: " . $conn->error . "</p>";
    }
} else {
    // Jika user tidak ada, buat user admin baru
    $role = 'admin';
    $sql_insert = "INSERT INTO users (username, password, role) VALUES (?, ?, ?)";
    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->bind_param("sss", $admin_username, $hashed_password, $role);
    if ($stmt_insert->execute()) {
        echo "<h1>Sukses!</h1>";
        echo "<p>User admin baru '<b>" . htmlspecialchars($admin_username) . "</b>' telah berhasil dibuat.</p>";
        echo "<p>Silakan login dengan:</p>";
        echo "<ul>";
        echo "<li>Username: <b>" . htmlspecialchars($admin_username) . "</b></li>";
        echo "<li>Password: <b>" . htmlspecialchars($admin_password) . "</b></li>";
        echo "</ul>";
        echo "<p style='color:red;'><b>PENTING:</b> Hapus file ini ('_reset_admin_password.php') dari server Anda sekarang juga demi keamanan!</p>";
    } else {
        echo "<h1>Error!</h1>";
        echo "<p>Gagal membuat user admin baru. Error: " . $conn->error . "</p>";
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Admin Password</title>
    <style>
        body { font-family: sans-serif; padding: 20px; background-color: #f4f4f4; }
        h1 { color: #333; }
        p { color: #555; }
        ul { list-style-type: none; padding: 0; }
        li { background-color: #fff; border: 1px solid #ddd; margin-bottom: 5px; padding: 10px; }
    </style>
</head>
<body>
</body>
</html> 