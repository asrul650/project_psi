<?php
session_start();
require_once '../includes/db_connect.php';
if (!isset($_SESSION['role_admin']) || $_SESSION['role_admin'] !== 'admin') {
    header('Location: logout.php');
    exit();
}
// Statistik
$total_requests = $conn->query("SELECT COUNT(*) as total FROM premium_requests")->fetch_assoc()['total'] ?? 0;
$total_premium = $conn->query("SELECT COUNT(*) as total FROM users WHERE is_premium=1")->fetch_assoc()['total'] ?? 0;
$total_pending = $conn->query("SELECT COUNT(*) as total FROM premium_requests WHERE status='pending'")->fetch_assoc()['total'] ?? 0;
// Approve/reject logic
if (isset($_GET['approve'])) {
    $id = intval($_GET['approve']);
    $conn->query("UPDATE premium_requests SET status='approved' WHERE id=$id");
    $row = $conn->query("SELECT user_id FROM premium_requests WHERE id=$id")->fetch_assoc();
    $conn->query("UPDATE users SET is_premium=1 WHERE id={$row['user_id']}");
    echo "<script>alert('User di-approve jadi premium!');location.href='premium_dashboard.php';</script>";
    exit();
}
if (isset($_GET['reject'])) {
    $id = intval($_GET['reject']);
    $conn->query("UPDATE premium_requests SET status='rejected' WHERE id=$id");
    echo "<script>alert('Request ditolak!');location.href='premium_dashboard.php';</script>";
    exit();
}
$result = $conn->query("SELECT pr.*, u.username FROM premium_requests pr JOIN users u ON pr.user_id=u.id ORDER BY pr.created_at DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Approve Premium</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
    body { background: #181c24; color: #fff; font-family: 'Poppins', Arial, sans-serif; }
    .premium-stats { display: flex; gap: 32px; justify-content: center; margin: 40px 0 24px 0; }
    .stat-box { background: #232b4a; border-radius: 14px; box-shadow: 0 4px 24px rgba(0,0,0,0.18); padding: 28px 36px; text-align: center; min-width: 180px; }
    .stat-box h3 { color: #ffe600; font-size: 2.1em; margin: 0 0 8px 0; }
    .stat-box span { color: #bfc8e2; font-size: 1.1em; }
    .premium-table { width: 100%; max-width: 900px; margin: 0 auto 40px auto; border-collapse: collapse; background: #232b4a; border-radius: 14px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.18); }
    .premium-table th, .premium-table td { padding: 16px 12px; text-align: center; }
    .premium-table th { background: #26305a; color: #ffe600; font-size: 1.1em; }
    .premium-table tr:not(:last-child) { border-bottom: 1px solid #2c2c2c; }
    .badge { padding: 6px 16px; border-radius: 8px; font-weight: bold; font-size: 1em; }
    .badge.pending { background: #ff9f45; color: #232b4a; }
    .badge.approved { background: #4caf50; color: #fff; }
    .badge.rejected { background: #f44336; color: #fff; }
    .btn-action { padding: 7px 18px; border: none; border-radius: 7px; font-weight: bold; font-size: 1em; cursor: pointer; margin: 0 4px; transition: background 0.2s; }
    .btn-approve { background: #4caf50; color: #fff; }
    .btn-approve:hover { background: #388e3c; }
    .btn-reject { background: #f44336; color: #fff; }
    .btn-reject:hover { background: #b71c1c; }
    img.bukti-img { max-width: 120px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.18); }
    h2 { text-align: center; color: #ffe600; margin: 32px 0 18px 0; }
    </style>
</head>
<body>
<div class="admin-wrapper">
<?php include 'admin_sidebar.php'; ?>
<main class="main-content">
<h2>Dashboard Approve Premium</h2>
<div class="premium-stats">
    <div class="stat-box">
        <h3><?php echo $total_requests; ?></h3>
        <span>Total Request</span>
    </div>
    <div class="stat-box">
        <h3><?php echo $total_premium; ?></h3>
        <span>User Premium</span>
    </div>
    <div class="stat-box">
        <h3><?php echo $total_pending; ?></h3>
        <span>Pending</span>
    </div>
</div>
<table class="premium-table">
    <tr>
        <th>User</th>
        <th>Bukti Transfer</th>
        <th>Status</th>
        <th>Waktu</th>
        <th>Aksi</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
    <tr>
        <td><?php echo htmlspecialchars($row['username']); ?></td>
        <td>
            <?php if ($row['bukti_transfer']): ?>
                <a href="<?php echo $row['bukti_transfer']; ?>" target="_blank"><img src="<?php echo $row['bukti_transfer']; ?>" class="bukti-img"></a>
            <?php else: ?>
                <span style="color:#ccc;">(Belum ada)</span>
            <?php endif; ?>
        </td>
        <td><span class="badge <?php echo $row['status']; ?>"><?php echo ucfirst($row['status']); ?></span></td>
        <td><?php echo htmlspecialchars($row['created_at']); ?></td>
        <td>
            <?php if ($row['status'] == 'pending'): ?>
                <a href="premium_dashboard.php?approve=<?php echo $row['id']; ?>" class="btn-action btn-approve">Approve</a>
                <a href="premium_dashboard.php?reject=<?php echo $row['id']; ?>" class="btn-action btn-reject">Reject</a>
            <?php else: ?>
                <span style="color:#aaa;">-</span>
            <?php endif; ?>
        </td>
    </tr>
    <?php endwhile; ?>
</table>
</main>
</div>
</body>
</html> 