<?php
session_start();
require_once '../includes/db_connect.php';
if (!isset($_SESSION['role_admin']) || $_SESSION['role_admin'] !== 'admin') {
    header('Location: logout.php');
    exit();
}
// Approve/reject logic
if (isset($_GET['approve'])) {
    $id = intval($_GET['approve']);
    $conn->query("UPDATE premium_requests SET status='approved' WHERE id=$id");
    $row = $conn->query("SELECT user_id FROM premium_requests WHERE id=$id")->fetch_assoc();
    $conn->query("UPDATE users SET is_premium=1 WHERE id={$row['user_id']}");
    echo "<script>alert('User di-approve jadi premium!');location.href='premium_requests.php';</script>";
    exit();
}
if (isset($_GET['reject'])) {
    $id = intval($_GET['reject']);
    $conn->query("UPDATE premium_requests SET status='rejected' WHERE id=$id");
    echo "<script>alert('Request ditolak!');location.href='premium_requests.php';</script>";
    exit();
}
$result = $conn->query("SELECT pr.*, u.username FROM premium_requests pr JOIN users u ON pr.user_id=u.id ORDER BY pr.created_at DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Request Upgrade Premium</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
<div class="admin-wrapper">
    <?php include 'admin_sidebar.php'; ?>
    <main class="main-content">
        <header class="header">
            <div class="header-title">
                <h1>Request Upgrade Premium</h1>
                <p>Approve atau reject permintaan upgrade premium user.</p>
            </div>
        </header>
        <div class="content-body">
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Bukti Transfer</th>
                            <th>Status</th>
                            <th>Waktu</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['username']); ?></td>
                            <td>
                                <?php if ($row['bukti_transfer']): ?>
                                    <img src="<?php echo $row['bukti_transfer']; ?>" class="table-img" alt="Bukti Transfer" style="cursor:pointer" onclick="openModal('<?php echo htmlspecialchars($row['bukti_transfer']); ?>')">
                                <?php else: ?>
                                    <span style="color:#ccc;">(Belum ada)</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge <?php echo $row['status']; ?>">
                                    <?php echo ucfirst($row['status']); ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                            <td>
                                <?php if ($row['status'] == 'pending'): ?>
                                    <a href="premium_requests.php?approve=<?php echo $row['id']; ?>" class="btn-action btn-primary" title="Approve"><i class="fas fa-check"></i></a>
                                    <a href="premium_requests.php?reject=<?php echo $row['id']; ?>" class="btn-action btn-secondary" title="Reject"><i class="fas fa-times"></i></a>
                                <?php else: ?>
                                    <span style="color:#aaa;">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>
<!-- Modal Preview Gambar -->
<div id="imgModal" class="img-modal" style="display:none;">
    <span class="img-modal-close" onclick="closeModal()">&times;</span>
    <img class="img-modal-content" id="imgModalSrc">
</div>
</body>
</html>
<style>
.img-modal {
    display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100vw; height: 100vh;
    background: rgba(0,0,0,0.7); justify-content: center; align-items: center;
}
.img-modal.show { display: flex; }
.img-modal-content {
    max-width: 90vw; max-height: 80vh; border-radius: 12px; box-shadow: 0 4px 24px rgba(0,0,0,0.25);
    background: #fff;
}
.img-modal-close {
    position: absolute; top: 40px; right: 60px; color: #fff; font-size: 48px; font-weight: bold; cursor: pointer; z-index: 10001;
    text-shadow: 0 2px 8px #000;
}
@media (max-width:600px) {
    .img-modal-close { top: 16px; right: 24px; font-size: 36px; }
}
</style>
<script>
function openModal(src) {
    var modal = document.getElementById('imgModal');
    var img = document.getElementById('imgModalSrc');
    img.src = src;
    modal.classList.add('show');
    modal.style.display = 'flex';
}
function closeModal() {
    var modal = document.getElementById('imgModal');
    modal.classList.remove('show');
    modal.style.display = 'none';
}
document.addEventListener('DOMContentLoaded', function() {
    var modal = document.getElementById('imgModal');
    modal.addEventListener('click', function(e) {
        if (e.target === modal) closeModal();
    });
});
</script> 