<?php
session_start();
if (!isset($_SESSION['user_id_user'])) {
    header('Location: ../includes/auth.php');
    exit();
}
require_once '../includes/db_connect.php';

$user_id = $_SESSION['user_id_user'];
// Ambil data user
$stmt = $conn->prepare('SELECT username, role, created_at, profile_pic FROM users WHERE id=?');
$stmt->bind_param('i', $user_id);
$stmt->execute();
$stmt->bind_result($username, $role, $created_at, $profile_pic);
$stmt->fetch();
$stmt->close();
$profile_pic_url = $profile_pic ? '../' . $profile_pic : '../assets/images/default_profile.png';

// Handle upload foto profil
$upload_msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_profile'])) {
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','webp'];
        if (in_array($ext, $allowed)) {
            $new_name = 'uploads/profile_' . $user_id . '_' . time() . '.' . $ext;
            if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], '../' . $new_name)) {
                $stmt = $conn->prepare('UPDATE users SET profile_pic=? WHERE id=?');
                $stmt->bind_param('si', $new_name, $user_id);
                $stmt->execute();
                $stmt->close();
                $profile_pic_url = '../' . $new_name;
                $upload_msg = 'Foto profil berhasil diupdate!';
            } else {
                $upload_msg = 'Gagal upload file.';
            }
        } else {
            $upload_msg = 'Format file tidak didukung.';
        }
    } else {
        $upload_msg = 'Pilih file gambar terlebih dahulu.';
    }
}
// Handle ganti password
$pw_msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $old = $_POST['old_password'] ?? '';
    $new = $_POST['new_password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';
    if ($new !== $confirm) {
        $pw_msg = 'Konfirmasi password tidak cocok!';
    } else {
        $stmt = $conn->prepare('SELECT password FROM users WHERE id=?');
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $stmt->bind_result($hash);
        $stmt->fetch();
        $stmt->close();
        if (!password_verify($old, $hash)) {
            $pw_msg = 'Password lama salah!';
        } else {
            $new_hash = password_hash($new, PASSWORD_DEFAULT);
            $stmt = $conn->prepare('UPDATE users SET password=? WHERE id=?');
            $stmt->bind_param('si', $new_hash, $user_id);
            $stmt->execute();
            $stmt->close();
            $pw_msg = 'Password berhasil diubah!';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya - Mobile Legends Guide</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .profile-container {
            max-width: 420px;
            margin: 40px auto;
            background: #232b4a;
            border-radius: 18px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.18);
            color: #fff;
            font-family: 'Lato', sans-serif;
            padding: 32px 28px 28px 28px;
        }
        .profile-pic {
            width: 110px;
            height: 110px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #ffe600;
            margin: 0 auto 18px auto;
            display: block;
            background: #fff;
        }
        .profile-info {
            text-align: center;
            margin-bottom: 24px;
        }
        .profile-info h2 {
            font-family: 'Oswald', sans-serif;
            font-size: 2rem;
            margin-bottom: 6px;
        }
        .profile-info .role {
            color: #ffe600;
            font-weight: bold;
            font-size: 1.1rem;
        }
        .profile-info .created {
            color: #bfc8e2;
            font-size: 0.98rem;
            margin-top: 4px;
        }
        .form-section {
            margin-bottom: 28px;
        }
        .form-section label {
            font-weight: 600;
            margin-bottom: 6px;
            display: block;
        }
        .form-section input[type="file"] {
            margin-bottom: 10px;
        }
        .form-section input[type="password"] {
            width: 100%;
            padding: 8px;
            border-radius: 8px;
            border: 1px solid #555;
            background: #2e3356;
            color: #fff;
            margin-bottom: 10px;
        }
        .form-section button {
            background: #7B1FA2;
            color: #ffe600;
            border: none;
            border-radius: 8px;
            padding: 8px 22px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            margin-top: 6px;
        }
        .form-section button:hover {
            background: #ffe600;
            color: #232b4a;
        }
        .msg {
            text-align: center;
            margin-bottom: 10px;
            color: #ffe600;
            font-weight: bold;
        }
        .back-btn {
            display: inline-block;
            margin-top: 18px;
            background: #4CAF50;
            color: #fff;
            border-radius: 8px;
            padding: 8px 18px;
            text-decoration: none;
            font-weight: bold;
            transition: background 0.2s;
        }
        .back-btn:hover {
            background: #388E3C;
            color: #fff;
        }
    </style>
</head>
<body>
    <header>
        <h1>Mobile Legends Guide</h1>
    </header>
    <div class="profile-container">
        <img src="<?= htmlspecialchars($profile_pic_url) ?>" class="profile-pic" alt="Foto Profil">
        <div class="profile-info">
            <h2><?= htmlspecialchars($username) ?></h2>
            <div class="role">Role: <?= htmlspecialchars($role) ?></div>
            <div class="created">Bergabung: <?= date('d M Y', strtotime($created_at)) ?></div>
        </div>
        <div class="form-section">
            <form method="post" enctype="multipart/form-data">
                <label>Ganti Foto Profil</label>
                <input type="file" name="profile_pic" accept="image/*">
                <button type="submit" name="upload_profile">Upload</button>
            </form>
            <?php if ($upload_msg): ?><div class="msg"><?= htmlspecialchars($upload_msg) ?></div><?php endif; ?>
        </div>
        <div class="form-section">
            <form method="post">
                <label>Ganti Password</label>
                <input type="password" name="old_password" placeholder="Password lama" required>
                <input type="password" name="new_password" placeholder="Password baru" required>
                <input type="password" name="confirm_password" placeholder="Konfirmasi password baru" required>
                <button type="submit" name="change_password">Ganti Password</button>
            </form>
            <?php if ($pw_msg): ?><div class="msg"><?= htmlspecialchars($pw_msg) ?></div><?php endif; ?>
        </div>
        <a href="homepage.php" class="back-btn"><i class="fas fa-arrow-left"></i> Kembali ke Dashboard</a>
    </div>
</body>
</html> 