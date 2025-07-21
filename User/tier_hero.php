<?php
require_once '../includes/db_connect.php';

// Ambil semua hero dari database
$sql = "SELECT id, name, role, lane, tier, image_path FROM heroes ORDER BY name ASC";
$result = $conn->query($sql);

$heroes_by_tier = [
    'SS' => [], 'S' => [], 'A' => [], 'B' => [], 'C' => [], 'D' => []
];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        if (array_key_exists($row['tier'], $heroes_by_tier)) {
            $heroes_by_tier[$row['tier']][] = $row;
        }
    }
}

// Urutan tier untuk ditampilkan
$tier_order = ['SS', 'S', 'A', 'B', 'C', 'D'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hero Tier List - Mobile Legends Guide</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/homepage.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #181c24 0%, #232b4a 100%);
        }
        .tier-list-container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 32px 18px 24px 18px;
            background: rgba(16,20,26,0.98);
            border-radius: 18px;
            box-shadow: 0 8px 32px #7B1FA233, 0 1.5px 8px #ffe60022;
        }
        .tier-section {
            margin-bottom: 48px;
            background: linear-gradient(120deg, #232b4a 80%, #181c24 100%);
            border-radius: 18px;
            padding: 28px 18px 24px 18px;
            box-shadow: 0 4px 24px #7B1FA244;
            border-left: 8px solid;
            position: relative;
        }
        .tier-SS { border-color: #ff4545; }
        .tier-S { border-color: #ff9f45; }
        .tier-A { border-color: #45a2ff; }
        .tier-B { border-color: #2ed573; }
        .tier-C { border-color: #a0a0a0; }
        .tier-D { border-color: #6a6a6a; }

        .tier-header {
            display: flex;
            align-items: center;
            gap: 18px;
            margin-bottom: 22px;
            cursor: pointer;
            user-select: none;
            background: rgba(255,255,255,0.04);
            border-radius: 12px;
            padding: 10px 18px 10px 10px;
            box-shadow: 0 2px 8px #ffe60022;
        }
        .tier-header .fa-chevron-down {
            margin-left: auto;
            font-size: 1.3em;
            transition: transform 0.2s;
            color: #ffe600;
        }
        .tier-header.collapsed .fa-chevron-down {
            transform: rotate(-90deg);
        }
        .tier-badge {
            font-size: 2.7rem;
            font-weight: bold;
            color: #fff;
            padding: 8px 28px;
            border-radius: 12px;
            min-width: 90px;
            text-align: center;
            box-shadow: 0 2px 12px #ffe60033;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .tier-badge-SS { background: linear-gradient(90deg,#ff4545 60%,#ffe600 100%); box-shadow:0 0 16px #ff4545aa; }
        .tier-badge-S { background: linear-gradient(90deg,#ff9f45 60%,#ffe600 100%); box-shadow:0 0 16px #ff9f45aa; }
        .tier-badge-A { background: linear-gradient(90deg,#45a2ff 60%,#ffe600 100%); box-shadow:0 0 16px #45a2ffaa; }
        .tier-badge-B { background: linear-gradient(90deg,#2ed573 60%,#ffe600 100%); box-shadow:0 0 16px #2ed573aa; }
        .tier-badge-C { background: linear-gradient(90deg,#a0a0a0 60%,#ffe600 100%); box-shadow:0 0 16px #a0a0a0aa; }
        .tier-badge-D { background: linear-gradient(90deg,#6a6a6a 60%,#ffe600 100%); box-shadow:0 0 16px #6a6a6aaa; }
        .tier-badge-SS::before { content: '⭐'; font-size:1.2em; margin-right:6px; }
        .tier-badge-S::before { content: '🥇'; font-size:1.2em; margin-right:6px; }
        .tier-badge-A::before { content: '🥈'; font-size:1.2em; margin-right:6px; }
        .tier-badge-B::before { content: '🥉'; font-size:1.2em; margin-right:6px; }
        .tier-badge-C::before { content: '🔹'; font-size:1.2em; margin-right:6px; }
        .tier-badge-D::before { content: '🔸'; font-size:1.2em; margin-right:6px; }

        .tier-description {
            color: #ffe600;
            font-size: 1.08rem;
            font-weight: 600;
            background: rgba(123,31,162,0.10);
            border-radius: 8px;
            padding: 7px 18px;
            margin-left: 8px;
            box-shadow: 0 1px 6px #ffe60022;
        }
        .hero-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: 24px;
            transition: max-height 0.3s, opacity 0.3s;
            overflow: hidden;
            margin-top: 8px;
        }
        .hero-grid.collapsed {
            max-height: 0;
            opacity: 0;
            pointer-events: none;
            padding: 0;
            margin: 0;
        }
        .hero-tier-card {
            background: linear-gradient(135deg,#23283a 60%,#181c24 100%);
            border-radius: 16px;
            overflow: hidden;
            text-align: center;
            transition: transform 0.2s, box-shadow 0.2s;
            text-decoration: none;
            color: white;
            cursor: pointer;
            pointer-events: auto;
            box-shadow: 0 2px 12px #ffe60022;
            border: 2.5px solid #ffe60033;
        }
        .hero-tier-card * { pointer-events: none; }
        .hero-tier-card:hover {
            transform: translateY(-7px) scale(1.04);
            box-shadow: 0 8px 32px #ffe60055;
            border: 2.5px solid #ffe600;
        }
        .hero-tier-card img {
            width: 100%;
            height: 120px;
            object-fit: cover;
            border-bottom: 2px solid #ffe60044;
        }
        .hero-tier-card .hero-name {
            padding: 12px 5px 10px 5px;
            font-weight: 700;
            font-size: 1.08rem;
            color: #ffe600;
            letter-spacing: 1px;
            text-shadow: 0 1px 6px #23283a99;
        }
        @media (max-width: 900px) {
            .tier-list-container { padding: 10px 2vw; }
            .tier-section { padding: 18px 6px 14px 6px; }
            .hero-grid { gap: 12px; }
        }
        @media (max-width: 600px) {
            .tier-list-container { padding: 2px 0; }
            .tier-section { padding: 8px 2px 8px 2px; }
            .hero-grid { grid-template-columns: 1fr 1fr; gap: 8px; }
            .hero-tier-card img { height: 80px; }
        }
    </style>
</head>
<body>
    <header>
        <h1>Mobile Legends Guide</h1>
        <input type="checkbox" id="nav-toggle" class="nav-toggle">
        <label for="nav-toggle" class="nav-toggle-label"><span></span></label>
        <nav>
            <ul>
                <li><a href="homepage.php">Home</a></li>
                <li><a href="hero.php">Hero</a></li>
                <li><a href="Item.php">Item</a></li>
                <li><a href="build.php">Build</a></li>
                <li class="active"><a href="tier_hero.php">Tier Hero</a></li>
                <li><a href=" http://localhost:5173/heroverse/fitur%20baru/dist/">Matchup</a></li>
                <li><a href="../includes/logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <main style="background:#10141a;min-height:100vh;">
        <div class="tier-list-container">
            <h2 style="color:white;text-align:center;margin-bottom:30px;">Hero Tier List</h2>
            <?php foreach ($tier_order as $tier): ?>
                <?php if (!empty($heroes_by_tier[$tier])): ?>
                    <section class="tier-section tier-<?php echo $tier; ?>">
                        <div class="tier-header" onclick="toggleTierGrid(this)">
                            <span class="tier-badge tier-badge-<?php echo $tier; ?>"><?php echo $tier; ?></span>
                            <p class="tier-description">Heroes in this tier have a very high impact and are often picked or banned.</p>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="hero-grid">
                            <?php foreach ($heroes_by_tier[$tier] as $hero): ?>
                                <a href="detailhero.php?id=<?= $hero['id'] ?>" class="hero-tier-card">
                                    <img src="../<?= htmlspecialchars($hero['image_path']) ?>" alt="<?= htmlspecialchars($hero['name']) ?>">
                                    <div class="hero-name"><?= htmlspecialchars($hero['name']) ?></div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </section>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
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
    function toggleTierGrid(header) {
        const grid = header.nextElementSibling;
        header.classList.toggle('collapsed');
        grid.classList.toggle('collapsed');
    }
    </script>
    <script>
      const icon = document.getElementById('chatbot-icon');
      const windowChat = document.getElementById('chatbot-window');
      icon.onclick = function() {
        windowChat.style.display = windowChat.style.display === 'none' ? 'block' : 'none';
      };
    </script>
    <script src="../js/script.js"></script>
</body>
</html> 