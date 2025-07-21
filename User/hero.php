<?php
require_once '../includes/db_connect.php';

// Ambil semua hero dari database
$sql = "SELECT * FROM heroes ORDER BY name ASC";
$result = $conn->query($sql);

// Siapkan filter role
$roles = ['Semua', 'Tank', 'Fighter', 'Assassin', 'Mage', 'Marksman', 'Support'];
$filter = isset($_GET['role']) ? $_GET['role'] : 'Semua';
$heroes = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $heroes[] = $row;
    }
}
$filtered_heroes = $filter === 'Semua' ? $heroes : array_filter($heroes, function($h) use ($filter) {
    return stripos($h['role'], $filter) !== false;
});
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Hero - Mobile Legends Guide</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/homepage.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .hero-filter-bar {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 18px;
            background: #181c24;
            padding: 32px 0 18px 0;
            margin-bottom: 32px;
            box-shadow: 0 2px 12px rgba(123,31,162,0.08);
            border-radius: 18px;
            max-width: 900px;
            margin-left: auto;
            margin-right: auto;
        }
        .hero-filter-bar .filter-btn {
            background: linear-gradient(90deg, #23283a 60%, #181c24 100%);
            border: 2px solid #ffd700;
            color: #ffeb3b;
            font-size: 1.1rem;
            font-weight: 700;
            padding: 10px 28px;
            border-radius: 24px;
            cursor: pointer;
            transition: background 0.2s, color 0.2s, border 0.2s, box-shadow 0.2s;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            letter-spacing: 1px;
        }
        .hero-filter-bar .filter-btn.active {
            background: linear-gradient(45deg, #ffd700, #ffed4a);
            color: #23283a;
            border: 2px solid #FFEB3B;
            box-shadow: 0 4px 16px rgba(123,31,162,0.15);
        }
        .hero-list-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 32px;
            padding: 0 16px 40px 16px;
            max-width: 1200px;
            margin: 0 auto;
            justify-items: center;
        }
        .hero-card {
            background: #23283a;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 16px rgba(0,0,0,0.18);
            transition: transform 0.2s, box-shadow 0.2s;
            cursor: pointer;
            display: flex;
            flex-direction: column;
            align-items: stretch;
            width: 100%;
            max-width: 300px;
        }
        .hero-card:hover {
            transform: translateY(-8px) scale(1.03);
            box-shadow: 0 8px 32px rgba(123,31,162,0.18);
        }
        .hero-card img {
            width: 100%;
            height: 240px;
            object-fit: cover;
            background: #181c24;
        }
        .hero-card-name {
            color: #fff;
            font-size: 1.2rem;
            font-weight: 700;
            text-align: center;
            padding: 16px 0 12px 0;
            background: none;
            letter-spacing: 1px;
        }
        .hero-card-role {
            color: #ffeb3b;
            font-size: 1rem;
            text-align: center;
            margin-bottom: 4px;
        }
        .hero-card-lane {
            color: #bfc8e2;
            font-size: 0.95rem;
            text-align: center;
            margin-bottom: 12px;
        }
        @media (max-width: 900px) {
            .hero-filter-bar {
                max-width: 100%;
                padding: 18px 0 10px 0;
                gap: 10px;
            }
        }
        @media (max-width: 600px) {
            .hero-list-grid {
                grid-template-columns: 1fr 1fr;
                gap: 16px;
            }
            .hero-card img {
                height: 140px;
            }
            .hero-filter-bar {
                flex-wrap: wrap;
                gap: 6px;
                padding: 10px 0 6px 0;
                border-radius: 10px;
            }
            .hero-filter-bar .filter-btn {
                font-size: 0.95rem;
                padding: 7px 12px;
                border-radius: 7px 7px 0 0;
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
                <li><a href="hero.php" class="active">Hero</a></li>
                <li><a href="Item.php">Item</a></li>
                <li><a href="build.php">Build</a></li>
                <li><a href="tier_hero.php">Tier Hero</a></li>
                <li><a href=" http://localhost:5173/heroverse/fitur%20baru/dist/">Matchup</a></li>
                <li><a href="../includes/logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>
    <main style="background:#181c24;min-height:100vh;padding-top:30px;">
        <div style="display:flex;justify-content:center;margin-bottom:10px;">
            <div style="position:relative;max-width:420px;width:100%;">
                <input type="text" id="search-hero" placeholder="Search heroes..." style="width:100%;padding:12px 18px 12px 45px;border-radius:24px;border:2px solid #2c2c2c;background:#23283a;color:#ffffff;font-size:1.1rem;outline:none;box-shadow:0 2px 8px #ffe60022;">
                <span style="position:absolute;left:20px;top:50%;transform:translateY(-50%);color:#ffe600;font-size:1.2em;"><i class="fas fa-search"></i></span>
            </div>
        </div>
        <div class="hero-filter-bar">
            <?php foreach ($roles as $role): ?>
                <form method="get" style="display:inline;">
                    <input type="hidden" name="role" value="<?= $role ?>">
                    <button type="submit" class="filter-btn<?= $filter === $role ? ' active' : '' ?>"><?= $role ?></button>
                </form>
            <?php endforeach; ?>
        </div>
       
        <div class="hero-list-grid" id="hero-list-grid">
            <?php if (count($filtered_heroes) > 0): ?>
                <?php foreach ($filtered_heroes as $hero): ?>
                <a href="detailhero.php?id=<?= $hero['id'] ?>" class="hero-card" data-hero-name="<?= strtolower(htmlspecialchars($hero['name'])) ?>">
                    <img src="../<?= $hero['image_path'] ? htmlspecialchars($hero['image_path']) : 'assets/images/default_hero.png' ?>" alt="<?= htmlspecialchars($hero['name']) ?>">
                    <div class="hero-card-name"><?= htmlspecialchars($hero['name']) ?></div>
                    <div class="hero-card-role"><?= htmlspecialchars($hero['role']) ?></div>
                    <div class="hero-card-lane"><?= htmlspecialchars($hero['lane']) ?></div>
                </a>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="color:#fff;text-align:center;">Tidak ada hero di database.</p>
            <?php endif; ?>
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
    <script>
    document.getElementById('search-hero').addEventListener('input', function() {
        const val = this.value.trim().toLowerCase();
        document.querySelectorAll('#hero-list-grid .hero-card').forEach(card => {
            const name = card.getAttribute('data-hero-name');
            card.style.display = (!val || name.includes(val)) ? '' : 'none';
        });
    });
    </script>
</body>
</html> 