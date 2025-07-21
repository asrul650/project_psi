<?php
require_once '../includes/db_connect.php';

// Ambil semua hero dari database
$sql = "SELECT * FROM heroes ORDER BY name ASC";
$result = $conn->query($sql);

// Siapkan filter role
$roles = ['All', 'Tank', 'Fighter', 'Assassin', 'Mage', 'Marksman', 'Support'];
$filter = isset($_GET['role']) ? $_GET['role'] : 'All';
$heroes = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $heroes[] = $row;
    }
}
$filtered_heroes = $filter === 'All' ? $heroes : array_filter($heroes, function($h) use ($filter) {
    return stripos($h['role'], $filter) !== false;
});

// Jika ada hero_id, tampilkan detail build hero
$show_detail = false;
$hero_detail = null;
if (isset($_GET['hero_id']) && is_numeric($_GET['hero_id'])) {
    $hero_id = intval($_GET['hero_id']);
    foreach ($heroes as $h) {
        if ($h['id'] == $hero_id) {
            $hero_detail = $h;
            $show_detail = true;
            break;
        }
    }
}

if ($show_detail && $hero_detail) {
    // Query build official
    $official_builds = [];
    $stmt = $conn->prepare("SELECT * FROM builds WHERE hero_id=? AND is_official=1 ORDER BY created_at DESC");
    $stmt->bind_param("i", $hero_detail['id']);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) $official_builds[] = $row;
    $stmt->close();

    // Query build user, urut like terbanyak
    $user_builds = [];
    $stmt = $conn->prepare("SELECT b.*, (SELECT COUNT(*) FROM build_likes bl WHERE bl.build_id = b.id) as like_count FROM builds b WHERE b.hero_id=? AND b.is_official=0 ORDER BY like_count DESC, b.created_at DESC");
    $stmt->bind_param("i", $hero_detail['id']);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) $user_builds[] = $row;
    $stmt->close();
}

// Ambil data item dari database
$items_by_category = [
    'attack' => [], 'defense' => [], 'magic' => [], 'movement' => [], 'jungle' => []
];
$item_sql = "SELECT * FROM items ORDER BY category, name";
$item_res = $conn->query($item_sql);
if ($item_res && $item_res->num_rows > 0) {
    while ($item = $item_res->fetch_assoc()) {
        $cat = strtolower($item['category']);
        if (isset($items_by_category[$cat])) {
            $items_by_category[$cat][] = [
                'id' => (int)$item['id'],
                'name' => $item['name'],
                'price' => isset($item['price']) ? (int)$item['price'] : 0,
                'image' => $item['image_path'] ? '../' . $item['image_path'] : '../assets/images/default_item.png'
            ];
        }
    }
}

// Ambil data emblem dari folder
function get_emblems($dir, $section) {
    $base = "images/LOGO/$dir/";
    if (!is_dir($base)) return [];
    $files = array_diff(scandir($base), ['.','..']);
    $result = [];
    foreach ($files as $f) {
        if (preg_match('/\.(png|jpg|jpeg|webp)$/i', $f)) {
            $name = pathinfo($f, PATHINFO_FILENAME);
            $result[] = [
                'name' => ucwords(str_replace(['_', '-'], ' ', $name)),
                'file' => $base . $f,
                'section' => $section
            ];
        }
    }
    return $result;
}
$main_emblems = get_emblems('Main Emblems', 'main');
$ability1 = get_emblems('Ability Emblems - Section 1', 'ability1');
$ability2 = get_emblems('Ability Emblems - Section 2', 'ability2');
$ability3 = get_emblems('Ability Emblems - Section 3', 'ability3');
$emblemsData = [
    'main' => $main_emblems,
    'ability1' => $ability1,
    'ability2' => $ability2,
    'ability3' => $ability3
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Build - Mobile Legends Guide</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/homepage.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body { background: #181c24; }
        .build-title {
            color: #ffffff;
            text-align: center;
            font-size: 1.5rem;
            font-weight: bold;
            margin-top: 25px;
            margin-bottom: 15px;
            letter-spacing: 2px;
            text-shadow: 0 2px 12px #232b4a99;
        }
        .build-subtitle {
            color: #bfc8e2;
            text-align: center;
            font-size: 1.15rem;
            margin-bottom: 32px;
        }
        .build-filter-bar {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 12px;
            margin-bottom: 30px;
        }
        .build-filter-btn {
            background: none;
            border: 2px solid #ffe600;
            color: #ffe600;
            font-size: 1.08rem;
            font-weight: 700;
            padding: 7px 22px;
            border-radius: 22px;
            cursor: pointer;
            transition: background 0.2s, color 0.2s, border 0.2s;
            margin-bottom: 0;
        }
        .build-filter-btn.active, .build-filter-btn:hover {
            background: linear-gradient(45deg, #ffd700, #ffed4a);
            color: #23283a;
            border: 2px solid #ffe600;
        }
        .build-search-bar {
            display: flex;
            justify-content: center;
            margin-bottom: 32px;
        }
        .build-search-box {
            position: relative;
            max-width: 420px;
            width: 100%;
        }
        .build-search-box input {
            width: 100%;
            padding: 12px 18px 12px 45px;
            border-radius: 24px;
            border: 2px solid #2c2c2c;
            background: #23283a;
            color: #ffffff;
            font-size: 1.1rem;
            outline: none;
            box-shadow: 0 2px 8px #ffe60022;
        }
        .build-search-box .fa-search {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: #ffe600;
            font-size: 1.2em;
        }
        .build-hero-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 32px;
            padding: 0 16px 40px 16px;
            max-width: 900px;
            margin: 0 auto;
        }
        .build-hero-card {
            background: #23283a;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 16px rgba(0,0,0,0.18);
            transition: transform 0.2s, box-shadow 0.2s;
            cursor: pointer;
            display: flex;
            flex-direction: column;
            align-items: stretch;
            border: 2px solid transparent;
        }
        .build-hero-card:hover {
            transform: translateY(-8px) scale(1.03);
            box-shadow: 0 8px 32px #ffe60055;
            border: 2px solid #ffe600;
        }
        .build-hero-card img {
            width: 100%;
            height: 160px;
            object-fit: cover;
            background: #181c24;
        }
        .build-hero-card-name {
            color: #fff;
            font-size: 1.15rem;
            font-weight: 700;
            text-align: center;
            padding: 14px 0 10px 0;
            background: none;
            letter-spacing: 1px;
        }
        @media (max-width: 900px) {
            .build-hero-grid { grid-template-columns: 1fr 1fr 1fr; gap: 18px; }
        }
        @media (max-width: 600px) {
            .build-hero-grid { grid-template-columns: 1fr 1fr; gap: 10px; }
            .build-hero-card img { height: 100px; }
        }
        /* Slide/Modal Build Hero */
        .build-slide {
            position: fixed;
            top: 0;
            right: -100%;
            width: 100%;
            height: 100vh;
            background: #181c24;
            z-index: 1000;
            transition: right 0.3s ease-in-out;
            overflow-y: auto;
        }
        .build-slide.active {
            right: 0;
        }
        .build-slide-header {
            background: #23283a;
            padding: 20px 24px;
            border-bottom: 2px solid #ffe600;
            display: flex;
            align-items: center;
            gap: 16px;
        }
        .build-slide-close {
            background: none;
            border: none;
            color: #ffe600;
            font-size: 1.5rem;
            cursor: pointer;
            padding: 8px;
            border-radius: 50%;
            transition: background 0.2s;
        }
        .build-slide-close:hover {
            background: rgba(255,230,0,0.1);
        }
        .build-slide-content {
            padding: 24px;
            max-width: 900px;
            margin: 0 auto;
        }
        .build-slide-hero-info {
            display: flex;
            gap: 24px;
            align-items: center;
            background: #23283a;
            border-radius: 16px;
            padding: 24px 20px;
            margin-bottom: 32px;
            box-shadow: 0 4px 24px #ffe60022;
        }
        .build-slide-hero-img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid #ffe600;
            box-shadow: 0 2px 12px #ffe60033;
        }
        .build-slide-hero-details h2 {
            color: #ffe600;
            font-size: 1.8rem;
            font-weight: 800;
            margin-bottom: 8px;
            letter-spacing: 1px;
        }
        .build-slide-hero-details p {
            color: #bfc8e2;
            font-size: 1rem;
            margin: 4px 0;
        }
        .build-slide-hero-details b {
            color: #ffe600;
        }
        .build-create-btn {
            background: #ffe600;
            color: #23283a;
            font-weight: 700;
            padding: 12px 32px;
            border-radius: 24px;
            border: none;
            font-size: 1.1rem;
            margin-bottom: 24px;
            cursor: pointer;
            box-shadow: 0 2px 8px #ffe60033;
            transition: transform 0.2s;
        }
        .build-create-btn:hover {
            transform: translateY(-2px);
        }
        .build-section {
            margin-bottom: 24px;
        }
        .build-section-title {
            color: #ffe600;
            font-weight: 700;
            font-size: 1.2rem;
            margin-bottom: 12px;
        }
        .build-card {
            background: #232b4a;
            border-radius: 12px;
            padding: 18px 16px;
            margin-bottom: 12px;
            box-shadow: 0 2px 8px #ffe60022;
            border-left: 4px solid #ffe600;
        }
        .build-card-title {
            color: #fff;
            font-weight: 600;
            margin-bottom: 8px;
        }
        .build-card-desc {
            color: #bfc8e2;
            font-size: 0.95rem;
            margin-bottom: 8px;
        }
        .build-card-likes {
            color: #ffe600;
            font-size: 0.9rem;
        }
        /* Build Creation Form */
        .build-form-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.8);
            z-index: 2000;
            display: none;
            align-items: center;
            justify-content: center;
        }
        .build-form-overlay.active {
            display: flex;
        }
        .build-form-container {
            background: #23283a;
            border-radius: 20px;
            padding: 32px;
            max-width: 800px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 8px 32px rgba(255,230,0,0.3);
            border: 2px solid #ffe600;
        }
        .build-form-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            padding-bottom: 16px;
            border-bottom: 2px solid #ffe600;
        }
        .build-form-title {
            color: #ffe600;
            font-size: 1.5rem;
            font-weight: 700;
        }
        .build-form-close {
            background: none;
            border: none;
            color: #ffe600;
            font-size: 1.5rem;
            cursor: pointer;
            padding: 8px;
            border-radius: 50%;
            transition: background 0.2s;
        }
        .build-form-close:hover {
            background: rgba(255,230,0,0.1);
        }
        .build-form-group {
            margin-bottom: 20px;
        }
        .build-form-label {
            color: #ffe600;
            font-weight: 600;
            margin-bottom: 8px;
            display: block;
        }
        .build-form-input {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #3a4151;
            border-radius: 12px;
            background: #181c24;
            color: #fff;
            font-size: 1rem;
            transition: border-color 0.2s;
        }
        .build-form-input:focus {
            outline: none;
            border-color: #ffe600;
        }
        .build-form-textarea {
            min-height: 100px;
            resize: vertical;
        }
        .build-category-tabs {
            display: flex;
            gap: 8px;
            margin-bottom: 16px;
            flex-wrap: wrap;
        }
        .build-category-tab {
            background: #3a4151;
            color: #bfc8e2;
            border: none;
            padding: 8px 16px;
            border-radius: 20px;
            cursor: pointer;
            font-size: 0.9rem;
            transition: all 0.2s;
        }
        .build-category-tab.active {
            background: #ffe600;
            color: #23283a;
        }
        .build-items-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: 12px;
            margin-bottom: 20px;
        }
        .build-item-card {
            background: #181c24;
            border: 2px solid #3a4151;
            border-radius: 12px;
            padding: 12px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
            position: relative;
        }
        .build-item-card:hover {
            border-color: #ffe600;
            transform: translateY(-2px);
        }
        .build-item-card.selected {
            border-color: #ffe600;
            background: rgba(255,230,0,0.1);
        }
        .build-item-card img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 8px;
        }
        .build-item-card .item-name {
            color: #fff;
            font-size: 0.8rem;
            font-weight: 600;
            line-height: 1.2;
        }
        .build-item-card .item-price {
            color: #ffe600;
            font-size: 0.7rem;
            margin-top: 4px;
        }
        .build-item-card .selected-indicator {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #ffe600;
            color: #23283a;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
            font-weight: bold;
        }
        .build-preview {
            background: #181c24;
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 24px;
            border: 2px solid #3a4151;
        }
        .build-preview-title {
            color: #ffe600;
            font-weight: 700;
            margin-bottom: 16px;
            font-size: 1.1rem;
        }
        .build-preview-items {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }
        .build-preview-item {
            background: #23283a;
            border-radius: 8px;
            padding: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
            border: 1px solid #3a4151;
        }
        .build-preview-item img {
            width: 32px;
            height: 32px;
            border-radius: 4px;
        }
        .build-preview-item .item-name {
            color: #fff;
            font-size: 0.8rem;
            font-weight: 600;
        }
        .build-preview-item .remove-item {
            background: #ff4757;
            color: white;
            border: none;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            cursor: pointer;
            font-size: 0.7rem;
        }
        .build-form-actions {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
        }
        .build-form-btn {
            padding: 12px 24px;
            border-radius: 24px;
            border: none;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s;
        }
        .build-form-btn:hover {
            transform: translateY(-2px);
        }
        .build-form-btn.primary {
            background: #ffe600;
            color: #23283a;
        }
        .build-form-btn.secondary {
            background: #3a4151;
            color: #fff;
        }
        /* Build Actions */
        .build-actions {
            display: flex;
            gap: 8px;
            margin-top: 12px;
        }
        .build-action-btn {
            background: none;
            border: 1px solid #3a4151;
            color: #bfc8e2;
            padding: 6px 12px;
            border-radius: 16px;
            cursor: pointer;
            font-size: 0.8rem;
            transition: all 0.2s;
        }
        .build-action-btn:hover {
            border-color: #ffe600;
            color: #ffe600;
        }
        .build-action-btn.liked {
            background: #ffe600;
            color: #23283a;
            border-color: #ffe600;
        }
        .build-stats {
            display: flex;
            gap: 16px;
            margin-top: 8px;
            font-size: 0.8rem;
            color: #bfc8e2;
        }
        .build-stat {
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .build-stat i {
            color: #ffe600;
        }
        @media (max-width: 768px) {
            .build-slide {
                width: 100%;
                right: -100%;
            }
            .build-slide-hero-info {
                flex-direction: column;
                text-align: center;
            }
            .build-form-container {
                padding: 20px;
                margin: 20px;
            }
            .build-items-grid {
                grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
            }
        }
        .build-form-tabs { display: flex; gap: 8px; margin-bottom: 18px; }
        .build-form-tab { flex:1; background: #ececec; color: #23283a; border: none; border-radius: 12px 12px 0 0; padding: 12px 0; font-weight: 700; font-size: 1.1rem; cursor: pointer; transition: background 0.2s; }
        .build-form-tab.active { background: #ffe600; color: #23283a; }
        .build-form-tab-content { background: #fff; border-radius: 0 0 12px 12px; padding: 18px 8px; }
        .emblem-section { margin-bottom: 18px; }
        .emblem-section-title { color: #ffe600; font-weight: 700; font-size: 1.1rem; margin-bottom: 8px; }
        .emblem-grid { display: flex; flex-wrap: wrap; gap: 12px; }
        .emblem-card { background: #f5f6fa; border: 2px solid #ececec; border-radius: 10px; padding: 10px 8px; text-align: center; cursor: pointer; width: 90px; transition: border 0.2s, box-shadow 0.2s; }
        .emblem-card.selected, .emblem-card:hover { border: 2px solid #ffe600; box-shadow: 0 2px 8px #ffe60033; }
        .emblem-card img { width: 48px; height: 48px; object-fit: contain; margin-bottom: 6px; }
        .emblem-name { color: #23283a; font-size: 0.85rem; font-weight: 600; }
        .build-preview-emblems { display: flex; gap: 12px; flex-wrap: wrap; margin-top: 8px; }
        .emblem-preview { display: flex; flex-direction: column; align-items: center; gap: 4px; background: #fffbe6; border-radius: 8px; padding: 8px 10px; }
        .emblem-preview img { width: 32px; height: 32px; }
        .emblem-preview span { color: #23283a; font-size: 0.8rem; font-weight: 600; }
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
                <li><a href="build.php" class="active">Build</a></li>
                <li><a href="tier_hero.php">Tier Hero</a></li>
                <li><a href=" http://localhost:5173/heroverse/fitur%20baru/dist/">Matchup</a></li>
                <li><a href="../includes/logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>
    <main style="background:#181c24;min-height:100vh;">
        <div class="build-title">Select your hero to view and create optimal item builds</div>
        <div class="build-search-bar">
            <div class="build-search-box">
                <input type="text" id="search-hero" placeholder="Search heroes....">
                <span class="fa fa-search"></span>
            </div>
        </div>
        <div class="build-filter-bar">
            <?php foreach ($roles as $role): ?>
                <form method="get" style="display:inline;">
                    <input type="hidden" name="role" value="<?= $role ?>">
                    <button type="submit" class="build-filter-btn<?= $filter === $role ? ' active' : '' ?>"><?= $role ?></button>
                </form>
            <?php endforeach; ?>
        </div>
        <div class="build-hero-grid" id="build-hero-grid">
            <?php if (count($filtered_heroes) > 0): ?>
                <?php foreach ($filtered_heroes as $hero): ?>
                <div class="build-hero-card" data-hero-name="<?= strtolower(htmlspecialchars($hero['name'])) ?>" data-hero-id="<?= $hero['id'] ?>" onclick="showBuildSlide(<?= $hero['id'] ?>, '<?= htmlspecialchars($hero['name']) ?>', '<?= htmlspecialchars($hero['role']) ?>', '<?= htmlspecialchars($hero['lane']) ?>', '<?= htmlspecialchars($hero['tier']) ?>', '<?= htmlspecialchars($hero['image_path']) ?>')">
                    <img src="../<?= $hero['image_path'] ? htmlspecialchars($hero['image_path']) : 'assets/images/default_hero.png' ?>" alt="<?= htmlspecialchars($hero['name']) ?>">
                    <div class="build-hero-card-name"><?= htmlspecialchars($hero['name']) ?></div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="color:#fff;text-align:center;">Tidak ada hero di database.</p>
            <?php endif; ?>
        </div>
    </main>

    <!-- Build Slide/Modal -->
    <div class="build-slide" id="build-slide">
        <div class="build-slide-header">
            <button class="build-slide-close" onclick="closeBuildSlide()">
                <i class="fas fa-times"></i>
            </button>
            <h3 style="color:#ffe600;margin:0;">Hero Builds</h3>
        </div>
        <div class="build-slide-content" id="build-slide-content">
            <!-- Content will be loaded here -->
        </div>
    </div>

    <!-- Build Creation Form -->
    <div class="build-form-overlay" id="build-form-overlay">
        <div class="build-form-container">
            <div class="build-form-header">
                <h3 class="build-form-title">Create New Build</h3>
                <button class="build-form-close" onclick="closeBuildForm()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form id="build-form">
                <div class="build-form-group">
                    <label class="build-form-label">Build Name</label>
                    <input type="text" class="build-form-input" id="build-name" placeholder="Enter build name..." required>
                </div>
                
                <div class="build-form-group">
                    <label class="build-form-label">Description</label>
                    <textarea class="build-form-input build-form-textarea" id="build-description" placeholder="Describe your build strategy..."></textarea>
                </div>
                
                <div class="build-form-group">
                    <div class="build-form-tabs">
                        <button type="button" class="build-form-tab active" data-tab="items">Items</button>
                    </div>
                    <div class="build-form-tab-content" id="tab-items">
                        <div class="build-category-tabs">
                            <button type="button" class="build-category-tab active" data-category="attack">Attack</button>
                            <button type="button" class="build-category-tab" data-category="defense">Defense</button>
                            <button type="button" class="build-category-tab" data-category="magic">Magic</button>
                            <button type="button" class="build-category-tab" data-category="movement">Movement</button>
                            <button type="button" class="build-category-tab" data-category="jungle">Jungle</button>
                        </div>
                        <div class="build-items-grid" id="build-items-grid">
                            <!-- Items will be loaded here -->
                        </div>
                    </div>
                </div>
                
                <div class="build-preview" id="build-preview" style="display: none;">
                    <div class="build-preview-title">Selected Items</div>
                    <div class="build-preview-items" id="build-preview-items">
                        <!-- Selected items will be shown here -->
                    </div>
                </div>
                
                <div class="build-form-actions">
                    <button type="button" class="build-form-btn secondary" onclick="closeBuildForm()">Cancel</button>
                    <button type="submit" class="build-form-btn primary">Create Build</button>
                </div>
            </form>
        </div>
    </div>

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

    <script>
    window.itemsData = <?php echo json_encode($items_by_category); ?>;
    // Global variables
    let currentHeroId = null;
    let selectedItems = [];
    let currentCategory = 'attack';
    let currentHeroData = {};
    let itemsData = window.itemsData;

    document.getElementById('search-hero').addEventListener('input', function() {
        const val = this.value.trim().toLowerCase();
        document.querySelectorAll('#build-hero-grid .build-hero-card').forEach(card => {
            const name = card.getAttribute('data-hero-name');
            card.style.display = (!val || name.includes(val)) ? '' : 'none';
        });
    });

    function showBuildSlide(heroId, heroName, heroRole, heroLane, heroTier, heroImage) {
        currentHeroId = heroId;
        currentHeroData = { heroName, heroRole, heroLane, heroTier, heroImage };
        loadBuildsAjax(heroId, heroName, heroRole, heroLane, heroTier, heroImage);
        document.getElementById('build-slide').classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeBuildSlide() {
        document.getElementById('build-slide').classList.remove('active');
        document.body.style.overflow = 'auto';
    }

    function loadBuildsAjax(heroId, heroName, heroRole, heroLane, heroTier, heroImage) {
        fetch('./get_builds.php?hero_id=' + heroId)
            .then(res => res.text())
            .then(text => {
                let data;
                try {
                    data = JSON.parse(text);
                } catch (e) {
                    console.error('Response bukan JSON valid:', text);
                    alert('Gagal load builds: Response bukan JSON valid. Cek console untuk detail.');
                    return;
                }
                if (!data.success) {
                    alert('Gagal load builds: ' + (data.error || 'Unknown error'));
                    return;
                }
                window.lastBuildsData = data;
                renderBuildSlide(
                    heroName || currentHeroData.heroName,
                    heroRole || currentHeroData.heroRole,
                    heroLane || currentHeroData.heroLane,
                    heroTier || currentHeroData.heroTier,
                    heroImage || currentHeroData.heroImage
                );
            })
            .catch(err => {
                console.error('Fetch error:', err);
                alert('Gagal load builds (fetch error).');
            });
    }

    function renderBuildSlide(heroName, heroRole, heroLane, heroTier, heroImage) {
        heroName = heroName || '-';
        heroRole = heroRole || '-';
        heroLane = heroLane || '-';
        heroTier = heroTier || '-';
        heroImage = heroImage || 'assets/images/default_hero.png';
        let data = window.lastBuildsData || { official: [], user: [] };
        const content = `
            <div class="build-slide-hero-info">
                <img src="../${heroImage}" alt="${heroName}" class="build-slide-hero-img">
                <div class="build-slide-hero-details">
                    <h2>${heroName}</h2>
                    <p>Role: <b>${heroRole}</b></p>
                    <p>Lane: <b>${heroLane}</b></p>
                    <p>Tier: <b>${heroTier}</b></p>
                </div>
            </div>
            <button class="build-create-btn" onclick="openBuildForm()">+ Create your own build</button>
            <div class="build-section">
                <div class="build-section-title">Official Builds</div>
                ${data.official && data.official.length > 0 ? data.official.map(build => createBuildCard(build, 'official')).join('') : '<div style=\'color:#bfc8e2\'>Belum ada official build.</div>'}
            </div>
            <div class="build-section">
                <div class="build-section-title">User Builds</div>
                ${data.user && data.user.length > 0 ? data.user.map(build => createBuildCard(build, 'user')).join('') : '<div style=\'color:#bfc8e2\'>Belum ada user build.</div>'}
            </div>
        `;
        document.getElementById('build-slide-content').innerHTML = content;
    }

    function createBuildCard(build, type) {
        const itemsHtml = build.items.map(itemId => {
            const item = findItemById(itemId);
            return item ? `<img src="${item.image}" alt="${item.name}" title="${item.name}" onerror="this.src='../assets/images/default_item.png'">` : '';
        }).join('');
        return `
            <div class="build-card">
                <div class="build-card-title">${build.name}</div>
                <div class="build-card-desc">${build.description}</div>
                <div class="build-preview-items" style="margin: 12px 0;">
                    ${itemsHtml}
                </div>
                <div class="build-actions">
                    <button class="build-action-btn${build.liked ? ' liked' : ''}" onclick="toggleLikeAjax(${build.id}, '${type}', this)">
                        <i class="fas fa-heart"></i> <span>${build.like_count || 0}</span>
                    </button>
                </div>
                <div class="build-stats">
                    <div class="build-stat">
                        <i class="fas fa-user"></i> ${build.author}
                    </div>
                    <div class="build-stat">
                        <i class="fas fa-calendar"></i> ${build.created_at}
                    </div>
                </div>
            </div>
        `;
    }

    function findItemById(itemId) {
        for (let category in itemsData) {
            const item = itemsData[category].find(item => item.id === itemId);
            if (item) {
                // Normalisasi path gambar
                item.image = normalizePath(item.image);
                return item;
            }
        }
        return null;
    }

    function openBuildForm() {
        document.getElementById('build-form-overlay').classList.add('active');
        loadItemsByCategory('attack');
        document.body.style.overflow = 'hidden';
    }

    function closeBuildForm() {
        document.getElementById('build-form-overlay').classList.remove('active');
        document.body.style.overflow = 'auto';
        selectedItems = [];
        updateBuildPreview();
    }

    function loadItemsByCategory(category) {
        currentCategory = category;
        const items = itemsData[category] || [];
        const grid = document.getElementById('build-items-grid');
        
        grid.innerHTML = items.map(item => `
            <div class="build-item-card" onclick="toggleItemSelection(${item.id})" data-item-id="${item.id}">
                <img src="${normalizePath(item.image)}" alt="${item.name}" onerror="this.src='../assets/images/default_item.png'">
                <div class="item-name">${item.name}</div>
                <div class="item-price">${item.price > 0 ? item.price + ' Gold' : 'Free'}</div>
            </div>
        `).join('');
        
        // Update category tabs
        document.querySelectorAll('.build-category-tab').forEach(tab => {
            tab.classList.remove('active');
        });
        document.querySelector(`[data-category="${category}"]`).classList.add('active');
    }

    function toggleItemSelection(itemId) {
        const index = selectedItems.indexOf(itemId);
        if (index > -1) {
            selectedItems.splice(index, 1);
        } else {
            if (selectedItems.length < 6) {
                selectedItems.push(itemId);
            } else {
                alert('Maximum 6 items allowed per build!');
                return;
            }
        }
        
        updateItemCards();
        updateBuildPreview();
    }

    function updateItemCards() {
        document.querySelectorAll('.build-item-card').forEach(card => {
            const itemId = parseInt(card.getAttribute('data-item-id'));
            if (selectedItems.includes(itemId)) {
                card.classList.add('selected');
                if (!card.querySelector('.selected-indicator')) {
                    card.innerHTML += '<div class="selected-indicator">✓</div>';
                }
            } else {
                card.classList.remove('selected');
                const indicator = card.querySelector('.selected-indicator');
                if (indicator) indicator.remove();
            }
        });
    }

    function updateBuildPreview() {
        const preview = document.getElementById('build-preview');
        const previewItems = document.getElementById('build-preview-items');
        
        if (selectedItems.length > 0) {
            preview.style.display = 'block';
            previewItems.innerHTML = selectedItems.map(itemId => {
                const item = findItemById(itemId);
                return item ? `
                    <div class="build-preview-item">
                        <img src="${item.image}" alt="${item.name}">
                        <span class="item-name">${item.name}</span>
                        <button class="remove-item" onclick="removeItem(${itemId})">×</button>
                    </div>
                ` : '';
            }).join('');
        } else {
            preview.style.display = 'none';
        }
    }

    function removeItem(itemId) {
        const index = selectedItems.indexOf(itemId);
        if (index > -1) {
            selectedItems.splice(index, 1);
            updateItemCards();
            updateBuildPreview();
        }
    }

    function toggleLikeAjax(buildId, type, btn) {
        // Toggle like/unlike
        const liked = btn.classList.contains('liked');
        fetch('like_build.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                build_id: buildId,
                action: liked ? 'unlike' : 'like'
            })
        })
        .then(res => res.json())
        .then(data => {
            if (!data.success) {
                alert('Gagal like/unlike: ' + (data.error || 'Unknown error'));
                return;
            }
            // Setelah sukses, reload data build agar jumlah like update dari backend
            loadBuildsAjax(currentHeroId, currentHeroData.heroName, currentHeroData.heroRole, currentHeroData.heroLane, currentHeroData.heroTier, currentHeroData.heroImage);
        })
        .catch(() => alert('Gagal like/unlike build.'));
    }

    function copyBuild(buildId) {
        // Copy build to clipboard (later implementation)
        alert('Build copied to clipboard!');
    }

    function editBuild(buildId) {
        // Edit build functionality (later implementation)
        alert('Edit build functionality coming soon!');
    }

    // Event listeners
    document.addEventListener('DOMContentLoaded', function() {
        // Category tab clicks
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('build-category-tab')) {
                const category = e.target.getAttribute('data-category');
                loadItemsByCategory(category);
            }
        });

        // Form submission
        document.getElementById('build-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const buildName = document.getElementById('build-name').value;
            const buildDescription = document.getElementById('build-description').value;
            
            if (selectedItems.length === 0) {
                alert('Please select at least one item for your build!');
                return;
            }
            
            fetch('create_build.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    hero_id: currentHeroId,
                    name: buildName,
                    description: buildDescription,
                    items: selectedItems
                })
            })
            .then(async res => {
                let data;
                try {
                    data = await res.json();
                } catch (e) {
                    alert('Gagal membuat build: Response bukan JSON valid. Status: ' + res.status);
                    return;
                }
                if (!data.success) {
                    let msg = 'Gagal membuat build: ' + (data.error || 'Unknown error');
                    if (data.debug) {
                        msg += '\n\nDebug Info:\n' + JSON.stringify(data.debug, null, 2);
                    }
                    msg += '\n\nFull Response:\n' + JSON.stringify(data, null, 2);
                    alert(msg);
                    return;
                }
                alert('Build created successfully!');
                closeBuildForm();
                loadBuildsAjax(currentHeroId, currentHeroData.heroName, currentHeroData.heroRole, currentHeroData.heroLane, currentHeroData.heroTier, currentHeroData.heroImage);
            })
            .catch(() => alert('Gagal membuat build.'));
        });

        // Close slide when clicking outside
        document.getElementById('build-slide').addEventListener('click', function(e) {
            if (e.target === this) {
                closeBuildSlide();
            }
        });

        // Close form when clicking outside
        document.getElementById('build-form-overlay').addEventListener('click', function(e) {
            if (e.target === this) {
                closeBuildForm();
            }
        });
    });

    function normalizePath(path) {
        if (!path) return '../assets/images/default_item.png';
        if (path.startsWith('..')) return path;
        if (path.startsWith('/')) return '..' + path;
        return '../' + path;
    }
    </script>
</body>
</html> 