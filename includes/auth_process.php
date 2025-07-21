<?php
require_once 'db_connect.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'login') {
            $username = $_POST['username'];
            $password = $_POST['password'];

            $sql = "SELECT id, username, password, role, is_premium, status FROM users WHERE username = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows == 1) {
                $user = $result->fetch_assoc();
                
                // Check if user is active
                if ($user['status'] == 'inactive') {
                    header('Location: auth.php?error=account_inactive');
                    exit();
                }
                
                if (password_verify($password, $user['password'])) {
                    // Update last login time dan status aktif
                    $update_sql = "UPDATE users SET last_login_time = CURRENT_TIMESTAMP, status = 'active' WHERE id = ?";
                    $update_stmt = $conn->prepare($update_sql);
                    $update_stmt->bind_param("i", $user['id']);
                    $update_stmt->execute();
                    
                    $_SESSION['user_id_user'] = $user['id'];
                    $_SESSION['username_user'] = $user['username'];
                    $_SESSION['role_user'] = isset($user['role']) ? $user['role'] : 'user';
                    $_SESSION['is_premium'] = isset($user['is_premium']) ? $user['is_premium'] : 0;
                    header('Location: ../User/homepage.php');
                    exit();
                }
            }
            header('Location: auth.php?error=invalid');
            exit();
        }
        elseif ($_POST['action'] == 'register') {
            $username = $_POST['username'];
            $password = $_POST['password'];
            $confirm_password = $_POST['confirm_password'];

            // === PENGECEKAN BARU DIMULAI DI SINI ===
            // Cek apakah username sudah ada di database
            $sql_check = "SELECT id FROM users WHERE username = ? LIMIT 1";
            $stmt_check = $conn->prepare($sql_check);
            $stmt_check->bind_param("s", $username);
            $stmt_check->execute();
            $result_check = $stmt_check->get_result();

            if ($result_check->num_rows > 0) {
                // Jika username sudah ada, kembali dengan pesan error
                header('Location: auth.php?error=username_exists');
                exit();
            }
            $stmt_check->close();
            // === PENGECEKAN SELESAI ===

            if ($password !== $confirm_password) {
                header('Location: auth.php?error=password_mismatch');
                exit();
            }

            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $sql = "INSERT INTO users (username, password) VALUES (?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $username, $hashed_password);

            if ($stmt->execute()) {
                header('Location: auth.php?success=registered');
            } else {
                header('Location: auth.php?error=registration_failed');
            }
            exit();
        }
    }
}
header('Location: auth.php');
exit();
?> 