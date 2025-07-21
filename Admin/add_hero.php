<?php
session_start();
require_once '../includes/db_connect.php';

// Cek otorisasi admin
if (!isset($_SESSION['user_id_admin']) || $_SESSION['role_admin'] !== 'admin') {
    header("Location: login.php?error=unauthorized");
    exit();
}

$error_message = '';
$success_message = '';

// Cek jika form telah disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari form
    $name = trim($_POST['name']);
    $role = trim($_POST['role']);
    $lane = trim($_POST['lane']);
    $tier = trim($_POST['tier']);
    $image_path = '';

    // Validasi data dasar
    if (empty($name) || empty($role) || empty($lane) || empty($tier)) {
        $error_message = 'Please fill in all required fields.';
    } else {
        // Proses upload gambar
        if (isset($_FILES['hero_image']) && $_FILES['hero_image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = '../assets/images/heroes/';
            // FIX: Cek dan buat direktori jika belum ada
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            // Buat nama file unik untuk menghindari konflik
            $file_name = uniqid() . '_' . basename($_FILES['hero_image']['name']);
            $target_file = $upload_dir . $file_name;
            $image_file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            // Cek apakah file adalah gambar
            $check = getimagesize($_FILES['hero_image']['tmp_name']);
            if ($check !== false) {
                // Pindahkan file ke direktori tujuan
                if (move_uploaded_file($_FILES['hero_image']['tmp_name'], $target_file)) {
                    // Simpan path relatif untuk database
                    $image_path = 'assets/images/heroes/' . $file_name;
                } else {
                    $error_message = 'Sorry, there was an error uploading your file.';
                }
            } else {
                $error_message = 'File is not an image.';
            }
        }

        // Jika tidak ada error, masukkan data ke database
        if (empty($error_message)) {
            $sql = "INSERT INTO heroes (name, role, lane, tier, image_path) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssss", $name, $role, $lane, $tier, $image_path);

            if ($stmt->execute()) {
                // Redirect ke halaman utama dengan pesan sukses
                header("Location: manage_heroes.php?success=hero_added");
                exit();
            } else {
                $error_message = 'Error: Could not save hero to the database.';
            }
        }
    }
}

$admin_username = $_SESSION['username_admin'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Hero - Admin Panel</title>
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
                    <li class="active"><a href="manage_heroes.php">Manage Hero Details</a></li>
                    <li><a href="manage_hero_tiers.php">Manage Hero Tiers</a></li>
                    <li><a href="#">Manage Items</a></li>
                    <li><a href="#">Manage Builds</a></li>
                    <li><a href="#">Manage Discussions</a></li>
                    <li><a href="#">Manage Users</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header class="header">
                <div class="header-title">
                    <h1>Add New Hero</h1>
                    <p>Enter the details for the new hero below.</p>
                </div>
            </header>

            <div class="content-body">
                <div class="form-container">
                    <?php if (!empty($error_message)): ?>
                        <div class="alert alert-error"><?php echo $error_message; ?></div>
                    <?php endif; ?>
                    
                    <form action="add_hero.php" method="POST" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="name">Hero Name</label>
                            <input type="text" id="name" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="role">Roles</label>
                            <input type="text" id="role" name="role" placeholder="e.g., Fighter, Mage" required>
                        </div>
                        <div class="form-group">
                            <label for="lane">Lanes</label>
                            <input type="text" id="lane" name="lane" placeholder="e.g., EXP Lane, Mid Lane" required>
                        </div>
                        <div class="form-group">
                            <label for="tier">Tier</label>
                            <select id="tier" name="tier" required>
                                <option value="SS">SS</option>
                                <option value="S">S</option>
                                <option value="A">A</option>
                                <option value="B">B</option>
                                <option value="C" selected>C</option>
                                <option value="D">D</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="hero_image">Hero Image</label>
                            <input type="file" id="hero_image" name="hero_image" accept="image/*">
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">Save Hero</button>
                            <a href="manage_heroes.php" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</body>
</html> 