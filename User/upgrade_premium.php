<?php
session_start();
require_once '../includes/db_connect.php';
if (!isset($_SESSION['user_id_user'])) {
    header('Location: ../includes/auth.php');
    exit();
}
$user_id = $_SESSION['user_id_user'];
$msg = '';
if (isset($_POST['submit'])) {
    $file = $_FILES['bukti'];
    $target = '../uploads/bukti_' . $user_id . '_' . time() . '_' . basename($file['name']);
    if (move_uploaded_file($file['tmp_name'], $target)) {
        $stmt = $conn->prepare("INSERT INTO premium_requests (user_id, bukti_transfer) VALUES (?, ?)");
        $stmt->bind_param("is", $user_id, $target);
        $stmt->execute();
        $msg = "<p style='color:green'>Bukti transfer berhasil dikirim. Tunggu konfirmasi admin.</p>";
    } else {
        $msg = "<p style='color:red'>Upload gagal.</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Upgrade ke Premium</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
    body { background: #181c24; color: #fff; font-family: 'Poppins', Arial, sans-serif; }
    .upgrade-container { max-width: 420px; margin: 60px auto; background: #232b4a; border-radius: 18px; box-shadow: 0 4px 24px rgba(0,0,0,0.18); padding: 36px 32px; }
    h2 { color: #ffe600; text-align: center; margin-bottom: 18px; }
    label { font-weight: 600; }
    input[type=file] { margin: 12px 0 18px 0; }
    button { background: #ff9f45; color: #232b4a; font-weight: bold; border: none; border-radius: 8px; padding: 10px 28px; font-size: 1.1rem; cursor: pointer; transition: background 0.2s; }
    button:hover { background: #ff4545; color: #fff; }
    .info { background: #26305a; border-radius: 8px; padding: 16px; margin-bottom: 18px; }
    </style>
</head>
<body>
<div class="upgrade-container">
    <h2>Upgrade ke Premium</h2>
    <div class="info">
        <b>Transfer ke:</b><br>
        MANDIRI 1460018177753, ASRUL RADITIO<br>
        <b>Nominal:</b> Rp 50.000<br>
        <b>Setelah transfer, upload bukti di bawah ini:</b>
    </div>
    <?php echo $msg; ?>
    <form action="" method="post" enctype="multipart/form-data">
        <label>Upload Bukti Transfer:</label><br>
        <input type="file" name="bukti" required accept="image/*"><br>
        <button type="submit" name="submit">Kirim Bukti</button>
    </form>
    <div style="margin-top:18px;text-align:center;">
        <a href="homepage.php" style="color:#ffe600;text-decoration:underline;">Kembali ke Dashboard</a>
    </div>
</div>
</body>
</html> 