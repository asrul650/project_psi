<?php
session_start();
// Jika admin sudah login, redirect ke dashboard
if (isset($_SESSION['user_id_admin']) && isset($_SESSION['role_admin']) && $_SESSION['role_admin'] === 'admin') {
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Mobile Legends Guide</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <!-- Tambahkan link Font Awesome untuk ikon mata -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <h2>Admin Panel Login</h2>
            <p>Please enter your credentials to access the dashboard.</p>
            
            <?php
            // Menampilkan pesan error jika ada
            if (isset($_GET['error'])) {
                $error_message = '';
                switch ($_GET['error']) {
                    case 'invalid_credentials':
                        $error_message = 'Invalid username or password.';
                        break;
                    case 'not_admin':
                        $error_message = 'Access denied. You are not an administrator.';
                        break;
                    default:
                        $error_message = 'An unknown error occurred.';
                }
                echo '<div class="alert alert-error">' . htmlspecialchars($error_message) . '</div>';
            }
            ?>

            <form action="auth_process.php" method="POST">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required autocomplete="off">
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <!-- Tambahkan wrapper untuk posisi ikon -->
                    <div class="password-wrapper">
                        <input type="password" id="password" name="password" required>
                        <!-- Ikon mata -->
                        <i class="fas fa-eye" id="toggle-password"></i>
                    </div>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn-login">Login</button>
                </div>
            </form>
        </div>
    </div>

    <!-- JavaScript untuk toggle password -->
    <script>
        const togglePassword = document.getElementById('toggle-password');
        const passwordInput = document.getElementById('password');

        togglePassword.addEventListener('click', function () {
            // Cek tipe input
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            // Ganti ikon mata
            this.classList.toggle('fa-eye-slash');
        });
    </script>
</body>
</html> 