<?php
session_start();
if (!isset($_SESSION['user_id_user'])) {
    header('Location: ../includes/auth.php');
    exit();
}
require_once '../includes/db_connect.php';
// Ambil kategori unik dari database
$categories = [];
$res = $conn->query('SELECT DISTINCT category FROM items ORDER BY category ASC');
if ($res) {
    while ($row = $res->fetch_assoc()) {
        if ($row['category']) $categories[] = $row['category'];
    }
}
// Ambil filter kategori dari GET
$filter = isset($_GET['category']) ? $_GET['category'] : '';
// Ambil item dari database
$items = [];
if ($filter && in_array($filter, $categories)) {
    $stmt = $conn->prepare('SELECT * FROM items WHERE category=? ORDER BY id ASC');
    $stmt->bind_param('s', $filter);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result) {
        while ($row = $result->fetch_assoc()) $items[] = $row;
    }
    $stmt->close();
} else {
    $res2 = $conn->query('SELECT * FROM items ORDER BY id ASC');
    if ($res2) {
        while ($row = $res2->fetch_assoc()) $items[] = $row;
    }
}
?>
<!DOCTYPE html> 
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mobile Legends Guide - Item</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/homepage.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .item-section {
            background: #181c23;
            border-radius: 10px;
            margin: 30px auto 0 auto;
            padding: 30px 30px 40px 30px;
            max-width: 1100px;
            box-shadow: 0 0 0 2px #00bfff;
        }
        .item-header {
            display: flex;
            gap: 40px;
            align-items: flex-start;
            margin-bottom: 40px;
        }
        .item-image {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: #222;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 16px rgba(0,0,0,0.5);
        }
        .item-image img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
        }
        .item-info {
            flex: 1;
        }
        .item-info h2 {
            color: #ffe600;
            font-size: 2.3rem;
            margin-bottom: 10px;
        }
        .item-info .item-attr {
            color: #fff;
            font-size: 1.1rem;
            margin-bottom: 10px;
        }
        .item-info .item-desc {
            color: #fff;
            font-size: 1rem;
            margin-bottom: 10px;
        }
        .item-recipe-box {
            background: #1a1f2e;
            border-radius: 12px;
            padding: 20px 30px;
            min-width: 320px;
            max-width: 350px;
            margin-left: auto;
        }
        .item-recipe-box h4 {
            color: #ffd700;
            font-size: 1.4rem;
            margin-bottom: 25px;
            text-align: center;
            font-family: 'Oswald', sans-serif;
            text-transform: uppercase;
        }
        .item-recipe-tree {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
        }
        .recipe-level {
            display: flex;
            justify-content: center;
            gap: 20px;
            position: relative;
            margin-bottom: 30px;
            width: 100%;
        }
        .recipe-item {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background: #181c23;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            z-index: 2;
        }
        .recipe-item img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            padding: 10px;
        }
        .recipe-item::after {
            content: 'Recipe';
            position: absolute;
            bottom: -25px;
            left: 50%;
            transform: translateX(-50%);
            color: #ffffff;
            font-size: 0.9rem;
            white-space: nowrap;
        }
        .recipe-lines {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
        }
        .recipe-line {
            position: absolute;
            background: #2a3040;
            z-index: 1;
        }
        .recipe-line.vertical {
            width: 2px;
            left: 50%;
            transform: translateX(-50%);
        }
        .recipe-line.horizontal {
            height: 2px;
            top: 35px;
        }
        .item-filter-section {
            margin: 40px auto 0 auto;
            max-width: 1100px;
            background: #181c23;
            border-radius: 10px;
            padding: 30px 30px 20px 30px;
            box-shadow: 0 0 0 2px #00bfff;
        }
        .item-filter-title {
            color: #fff;
            font-size: 1.8rem;
            text-align: center;
            margin-bottom: 25px;
            font-family: 'Oswald', sans-serif;
            text-transform: uppercase;
            letter-spacing: 1px;
            text-shadow: 0 0 10px rgba(0, 191, 255, 0.5);
        }
        .item-filter-btns {
            display: flex;
            justify-content: center;
            gap: 18px;
            margin-bottom: 32px;
            flex-wrap: nowrap;
        }
        .item-filter-btn {
            background: #232733;
            color: #fff;
            border: 2px solid #00bfff;
            border-radius: 25px;
            padding: 10px 30px;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
        }
        .item-filter-btn.active, .item-filter-btn:hover {
            background: #00bfff;
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 191, 255, 0.4);
        }
        .item-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 28px;
            background: #232733;
            border-radius: 15px;
            padding: 36px 30px 30px 30px;
            margin-bottom: 32px;
        }
        .item-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            background: rgba(255, 255, 255, 0.07);
            border-radius: 18px;
            padding: 15px 0 10px 0;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 1px solid transparent;
            min-height: 140px;
        }
        .item-card:hover {
            transform: translateY(-8px) scale(1.04);
            background: rgba(0, 191, 255, 0.08);
            border-color: #00bfff;
            box-shadow: 0 8px 20px rgba(0, 191, 255, 0.18);
        }
        .item-card-img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: #181c23;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 10px;
            margin-bottom: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.18);
            border: 2px solid #2a2f3f;
            transition: all 0.3s ease;
        }
        .item-card:hover .item-card-img {
            border-color: #00bfff;
            box-shadow: 0 6px 20px rgba(0, 191, 255, 0.22);
        }
        .item-card-img img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            filter: drop-shadow(0 2px 5px rgba(0, 0, 0, 0.18));
        }
        .item-card-name {
            color: #fff;
            font-size: 1.08rem;
            text-align: center;
            font-weight: 600;
            margin-top: 8px;
            transition: color 0.3s ease;
        }
        .item-card:hover .item-card-name {
            color: #00bfff;
        }
        @media (max-width: 900px) {
            .item-grid {
                grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
                gap: 18px;
                padding: 18px;
            }
            .item-filter-btns {
                gap: 10px;
            }
            .item-filter-btn {
                padding: 8px 18px;
                font-size: 1rem;
            }
        }
        @media (max-width: 600px) {
            .item-grid {
                grid-template-columns: repeat(auto-fill, minmax(90px, 1fr));
                gap: 10px;
                padding: 10px;
            }
            .item-card-img {
                width: 54px;
                height: 54px;
            }
            .item-card-name {
                font-size: 0.92rem;
            }
            .item-filter-title {
                font-size: 1.2rem;
            }
            .item-filter-btns {
                flex-wrap: wrap;
            }
        }
        .chatbot-fab {
            position: fixed;
            right: 32px;
            bottom: 32px;
            width: 64px;
            height: 64px;
            background: #007bff;
            color: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.2rem;
            box-shadow: 0 4px 16px rgba(0,0,0,0.18);
            z-index: 9999;
            transition: background 0.2s, transform 0.2s;
            text-decoration: none;
        }
        .chatbot-fab:hover {
            background: #0056b3;
            transform: scale(1.08);
            color: #fff;
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
                <li><a href="homepage.php">Home</a></li>
                <li><a href="hero.php">Hero</a></li>
                <li><a href="Item.php" class="active">Item</a></li>
                <li><a href="build.php">Build</a></li>
                <li><a href="tier_hero.php">Tier Hero</a></li>
                <li><a href=" http://localhost:5173/heroverse/fitur%20baru/dist/">Matchup</a></li>
                <li><a href="../includes/logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>
    <main style="background:#181c24;min-height:100vh;padding-top:20px;">
        <div class="item-filter-section">
            <h3 class="item-filter-title">Mobile Legends Items</h3>
            <div class="item-filter-btns">
                <a href="Item.php" class="item-filter-btn<?php if (!$filter) echo ' active'; ?>">All Items</a>
                <?php foreach ($categories as $cat): ?>
                    <a href="Item.php?category=<?php echo urlencode($cat); ?>" class="item-filter-btn<?php if ($filter === $cat) echo ' active'; ?>"><?php echo htmlspecialchars($cat); ?></a>
                <?php endforeach; ?>
            </div>
            <div class="item-grid" id="item-grid">
                <?php foreach ($items as $item): ?>
                    <?php
                    $img = $item['image_path'] ?? '';
                    if ($img && strpos($img, '../') !== 0) $img = '../' . $img;
                    ?>
                    <a href="detailitem.php?id=<?php echo $item['id']; ?>" class="item-card">
                        <div class="item-card-img">
                            <img src="<?php echo htmlspecialchars($img ?: '../images/wallpaper.jpg'); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                        </div>
                        <div class="item-card-name"><?php echo htmlspecialchars($item['name']); ?></div>
                    </a>
                <?php endforeach; ?>
                <?php if (empty($items)): ?>
                    <div style="color:#fff;text-align:center;width:100%;">Tidak ada item untuk kategori ini.</div>
                <?php endif; ?>
            </div>
        </div>
        <!-- Floating Chatbot Button -->
        <a href="chatbot.php" class="chatbot-fab" title="Buka Chatbot">
            <i class="fas fa-robot"></i>
        </a>
    </main>

    <!-- Chatbot Icon (Floating Button) -->
    <div id="chatbot-icon">
      <img src="https://cdn-icons-png.flaticon.com/512/4712/4712035.png" alt="Chatbot">
    </div>
    <!-- Chatbot Window (Popup, hidden by default) -->
    <div id="chatbot-window" style="display: none;">
      <iframe src="http://localhost:3000"></iframe>
      <button onclick="document.getElementById('chatbot-window').style.display='none'">&times;</button>
    </div>
    <style>
    #chatbot-icon {
        position: fixed;
        bottom: 32px;
        right: 32px;
        z-index: 9999;
        background: rgba(0, 123, 255, 0.85);
        border-radius: 50%;
        width: 70px;
        height: 70px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        box-shadow: 0 2px 12px rgba(0,0,0,0.25);
        transition: background 0.2s, box-shadow 0.2s, transform 0.2s;
    }
    #chatbot-icon img {
        width: 40px;
        height: 40px;
    }
    #chatbot-window {
        display: none;
        position: fixed;
        bottom: 90px;
        right: 32px;
        width: 420px;
        height: 620px;
        background: rgba(255,255,255,0.92);
        border-radius: 20px;
        box-shadow: 0 8px 32px rgba(0,0,0,0.28);
        z-index: 10000;
        overflow: hidden;
        backdrop-filter: blur(6px);
        border: 1.5px solid rgba(0,0,0,0.08);
        transition: box-shadow 0.2s, background 0.2s;
    }
    #chatbot-window iframe {
        width: 100%;
        height: 100%;
        border: none;
        background: transparent;
    }
    #chatbot-window button {
        position: absolute;
        top: 10px;
        right: 10px;
        background: #ff4545;
        color: #fff;
        border: none;
        border-radius: 50%;
        width: 32px;
        height: 32px;
        cursor: pointer;
        font-size: 22px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.18);
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background 0.2s;
    }
    #chatbot-window button:hover {
        background: #d32f2f;
    }
    </style>
    <script>
      const icon = document.getElementById('chatbot-icon');
      const windowChat = document.getElementById('chatbot-window');
      icon.onclick = function() {
        windowChat.style.display = windowChat.style.display === 'none' ? 'block' : 'none';
      };
    </script>
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
    <script src="../js/script.js"></script>
</body>
</html> 