<?php
session_start();
if (isset($_SESSION['user_id_user'])) {
    header('Location: ../index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login/Register - Mobile Legends Guide</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .auth-container {
            max-width: 400px;
            margin: 50px auto;
            padding: 20px;
            background: var(--card-bg);
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.3);
        }
        .auth-tabs {
            display: flex;
            margin-bottom: 20px;
            border-bottom: 2px solid var(--accent-color);
        }
        .auth-tab {
            flex: 1;
            padding: 10px;
            text-align: center;
            cursor: pointer;
            color: var(--text-color);
            transition: all 0.3s ease;
        }
        .auth-tab.active {
            background: var(--accent-color);
            color: var(--bg-color);
        }
        .auth-form {
            display: none;
        }
        .auth-form.active {
            display: block;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: var(--text-color);
        }
        .form-group input {
            width: 100%;
            padding: 8px;
            border: 1px solid var(--border-color);
            border-radius: 4px;
            background: var(--input-bg);
            color: var(--text-color);
        }
        .btn-auth {
            width: 100%;
            padding: 10px;
            background: var(--accent-color);
            color: var(--bg-color);
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .btn-auth:hover {
            background: var(--accent-hover);
        }
        .error-message {
            color: #ff4444;
            margin-bottom: 15px;
            text-align: center;
        }
        .success-message {
            color: #00C851;
            margin-bottom: 15px;
            text-align: center;
        }
    </style>
</head>
<body>
    <header>
        <h1>Mobile Legends Guide</h1>
        <input type="checkbox" id="nav-toggle" class="nav-toggle">
        <label for="nav-toggle" class="nav-toggle-label">
            <span></span>
        </label>
        <nav>
            <ul>
                <li><a href="../index.php">Home</a></li>
                <li><a href="../index.php#news">News</a></li>
                <li><a href="../index.php#spotlight">Pahlawan Unggulan</a></li>
                <li><a href="auth.php">Login</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <div class="auth-container">
            <div class="auth-tabs">
                <div class="auth-tab active" onclick="switchTab('login')">Login</div>
                <div class="auth-tab" onclick="switchTab('register')">Register</div>
            </div>

            <?php if (isset($_GET['error'])): ?>
                <div class="error-message">
                    <?php
                    switch($_GET['error']) {
                        case 'invalid':
                            echo "Username atau password salah.";
                            break;
                        case 'password_mismatch':
                            echo "Password tidak cocok.";
                            break;
                        case 'registration_failed':
                            echo "Registrasi gagal. Silakan coba lagi.";
                            break;
                        case 'username_exists':
                            echo "Username sudah digunakan. Silakan pilih yang lain.";
                            break;
                        case 'account_inactive':
                            echo "Akun Anda telah dinonaktifkan. Silakan hubungi admin.";
                            break;
                        default:
                            echo "Terjadi kesalahan. Silakan coba lagi.";
                    }
                    ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['success']) && $_GET['success'] == 'registered'): ?>
                <div class="success-message">
                    Registrasi berhasil! Silakan login.
                </div>
            <?php endif; ?>

            <form id="login-form" class="auth-form active" action="auth_process.php" method="POST">
                <input type="hidden" name="action" value="login">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit" class="btn-auth">Login</button>
            </form>

            <form id="register-form" class="auth-form" action="auth_process.php" method="POST">
                <input type="hidden" name="action" value="register">
                <div class="form-group">
                    <label for="reg-username">Username</label>
                    <input type="text" id="reg-username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="reg-password">Password</label>
                    <input type="password" id="reg-password" name="password" required>
                </div>
                <div class="form-group">
                    <label for="confirm-password">Konfirmasi Password</label>
                    <input type="password" id="confirm-password" name="confirm_password" required>
                </div>
                <button type="submit" class="btn-auth">Register</button>
            </form>
        </div>
    </main>

    <footer>
        <div class="footer-container">
            <div class="footer-section about">
                <h4>Tentang Kami</h4>
                <p>Mobile Legends Guide adalah sumber daya lengkap untuk pemain Mobile Legends, menyediakan informasi hero, build item optimal, panduan emblem, counter, synergy, dan tier list terbaru untuk membantu Anda meningkatkan performa di Land of Dawn.</p>
            </div>
            <div class="footer-section contact">
                <h4>Kontak</h4>
                <p><i class="fas fa-envelope"></i> info@mlguide.com</p>
                <p><i class="fas fa-phone"></i> +62 812 3456 7890</p>
                <p><i class="fas fa-map-marker-alt"></i> Land of Dawn, Indonesia</p>
            </div>
            <div class="footer-section social">
                <h4>Ikuti Kami</h4>
                <div class="social-icons">
                    <a href="#" target="_blank"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" target="_blank"><i class="fab fa-instagram"></i></a>
                    <a href="#" target="_blank"><i class="fab fa-twitter"></i></a>
                    <a href="#" target="_blank"><i class="fab fa-youtube"></i></a>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2024 Mobile Legends Guide. All rights reserved.</p>
        </div>
    </footer>

    <script>
        function switchTab(tab) {
            // Remove active class from all tabs and forms
            document.querySelectorAll('.auth-tab').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.auth-form').forEach(f => f.classList.remove('active'));
            
            // Add active class to selected tab and form
            document.querySelector(`.auth-tab[onclick="switchTab('${tab}')"]`).classList.add('active');
            document.getElementById(`${tab}-form`).classList.add('active');
        }
    </script>
</body>
</html> 