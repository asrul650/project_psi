<?php
session_start();
require_once '../includes/db_connect.php';

// Cek otorisasi admin
if (!isset($_SESSION['user_id_admin']) || $_SESSION['role_admin'] !== 'admin') {
    header("Location: login.php?error=unauthorized");
    exit();
}

$success_message = '';
$error_message = '';

// Proses update jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tiers'])) {
    $tiers = $_POST['tiers'];
    $conn->begin_transaction();
    try {
        $sql = "UPDATE heroes SET tier = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);

        foreach ($tiers as $hero_id => $tier) {
            $hero_id_int = intval($hero_id);
            // Validasi tier untuk keamanan
            $allowed_tiers = ['SS', 'S', 'A', 'B', 'C', 'D'];
            if (in_array($tier, $allowed_tiers)) {
                $stmt->bind_param("si", $tier, $hero_id_int);
                $stmt->execute();
            }
        }
        $stmt->close();
        $conn->commit();
        $success_message = "Hero tiers have been updated successfully!";
    } catch (mysqli_sql_exception $exception) {
        $conn->rollback();
        $error_message = "Error updating tiers: " . $exception->getMessage();
    }
}

// Ambil semua data hero dari database, diurutkan berdasarkan nama
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
if ($search !== '') {
    $sql_heroes = $conn->prepare("SELECT id, name, role, lane, tier FROM heroes WHERE name LIKE ? ORDER BY name ASC");
    $like = "%$search%";
    $sql_heroes->bind_param("s", $like);
    $sql_heroes->execute();
    $result_heroes = $sql_heroes->get_result();
} else {
    $sql_heroes = "SELECT id, name, role, lane, tier FROM heroes ORDER BY name ASC";
    $result_heroes = $conn->query($sql_heroes);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Hero Tiers - Admin Panel</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="admin-wrapper">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h3>Admin Panel</h3>
            </div>
            <nav class="sidebar-nav">
                <ul>
                    <li><a href="dashboard.php">Dashboard</a></li>
                    <li><a href="manage_heroes.php">Manage Hero Details</a></li>
                    <li class="active"><a href="manage_hero_tiers.php">Manage Hero Tiers</a></li>
                    <li><a href="manage_items.php">Manage Items</a></li>
                    <li><a href="manage_users.php">Manage Users</a></li>
                    <li><a href="manage_builds.php">Manage Builds</a></li>
                    <li><a href="premium_requests.php">Approve Premium Users</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header class="header">
                <div class="header-title">
                    <h1>Manage Hero Tiers</h1>
                    <p>Quickly update tiers for multiple heroes.</p>
                </div>
            </header>

            <div class="content-body">
                <?php if (!empty($success_message)): ?>
                    <div class="alert alert-success"><?php echo $success_message; ?></div>
                <?php endif; ?>
                <?php if (!empty($error_message)): ?>
                    <div class="alert alert-error"><?php echo $error_message; ?></div>
                <?php endif; ?>

                <!-- Search Form -->
                <form method="GET" action="manage_hero_tiers.php" style="margin-bottom: 20px;">
                    <input type="text" name="search" placeholder="Search hero name..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                    <button type="submit">Search</button>
                </form>

                <form action="manage_hero_tiers.php" method="POST">
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Role</th>
                                    <th>Lane</th>
                                    <th style="width: 120px;">Tier</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($result_heroes && $result_heroes->num_rows > 0): ?>
                                    <?php while($hero = $result_heroes->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($hero['name']); ?></td>
                                            <td><?php echo htmlspecialchars($hero['role']); ?></td>
                                            <td><?php echo htmlspecialchars($hero['lane']); ?></td>
                                            <td>
                                                <select name="tiers[<?php echo $hero['id']; ?>]" class="tier-select">
                                                    <?php
                                                    $tier_options = ['SS', 'S', 'A', 'B', 'C', 'D'];
                                                    foreach ($tier_options as $option) {
                                                        $selected = ($hero['tier'] === $option) ? 'selected' : '';
                                                        echo "<option value=\"$option\" $selected>$option</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4">No heroes found in the database.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="form-actions" style="text-align: right; margin-top: 20px;">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Tier Changes</button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html> 