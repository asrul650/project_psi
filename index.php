<?php
session_start();
if (isset($_SESSION['user_id_user'])) {
    header('Location: User/homepage.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mobile Legends Guide - Your Ultimate Companion</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/herocard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
    .feature-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 24px;
        justify-content: center;
        margin-bottom: 32px;
    }
    .feature-item {
        background: #232b4a;
        border-radius: 16px;
        box-shadow: 0 4px 16px rgba(0,0,0,0.12);
        padding: 28px 24px 22px 24px;
        min-width: 220px;
        max-width: 320px;
        width: 100%;
        text-align: center;
        color: #fff;
        display: flex;
        flex-direction: column;
        align-items: center;
        transition: box-shadow 0.2s, transform 0.2s;
    }
    .feature-item h4 {
        font-size: 1.3rem;
        margin-bottom: 10px;
        color: #ffe600;
        font-family: 'Oswald', sans-serif;
        letter-spacing: 1px;
    }
    .feature-item p {
        font-size: 1.05rem;
        color: #e6e6e6;
        margin-bottom: 0;
    }
    .feature-item:hover {
        box-shadow: 0 8px 32px rgba(0,0,0,0.18);
        transform: translateY(-6px) scale(1.03);
    }
    .spotlight-grid {
        display: flex;
        gap: 32px;
        justify-content: center;
        flex-wrap: wrap;
        margin: 32px 0 0 0;
        max-width: 100vw;
        padding: 0 16px;
        box-sizing: border-box;
    }
    .spotlight-card {
        box-sizing: border-box;
        position: relative;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 4px 24px rgba(0,0,0,0.18);
        background: #232b4a;
        min-height: 420px;
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
        width: 370px;
    }
    .spotlight-img-wrap {
        position: relative;
        width: 100%;
        height: 100%;
    }
    .spotlight-card img {
        width: 100%;
        height: 370px;
        object-fit: cover;
        display: block;
    }
    .spotlight-info {
        position: absolute;
        bottom: 0;
        left: 0; right: 0;
        background: linear-gradient(0deg, rgba(20,24,40,0.92) 80%, rgba(20,24,40,0.2) 100%);
        color: #fff;
        padding: 28px 22px 22px 22px;
    }
    .role-tag {
        display: inline-block;
        background: #45a2ff;
        color: #fff;
        font-size: 0.95em;
        border-radius: 6px;
        padding: 2px 10px;
        margin-bottom: 8px;
    }
    .btn-detail {
        display: inline-block;
        margin-top: 12px;
        background: #ff9f45;
        color: #232b4a;
        font-weight: bold;
        border-radius: 8px;
        padding: 8px 18px;
        text-decoration: none;
        transition: background 0.2s;
    }
    .btn-detail:hover {
        background: #ff4545;
        color: #fff;
    }
    @media (max-width: 1100px) {
        .spotlight-grid {
            gap: 18px;
        }
        .spotlight-card {
            width: 95vw;
            max-width: 370px;
        }
    }
    @media (max-width: 900px) {
        .feature-grid {
            flex-direction: column;
            align-items: center;
        }
        .feature-item {
            max-width: 95vw;
        }
    }
    @media (max-width: 700px) {
        .spotlight-grid {
            flex-direction: column;
            align-items: center;
        }
        .spotlight-card {
            width: 98vw;
            max-width: 370px;
        }
        .spotlight-card img {
            height: 220px;
        }
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
                <li><a href="index.php">Home</a></li>
                <li><a href="#news">News</a></li>
                <li><a href="#spotlight">Pahlawan Unggulan</a></li>
                <li><a href="includes/auth.php">Login</a></li>
            </ul>
        </nav>
    </header>

    <main class="landing-page">
        <section class="hero-banner">
            <div class="hero-content">
                <h2>Selamat Datang di Mobile Legends Guide!</h2>
                <p>Temukan strategi terbaik, build item optimal, panduan emblem, dan tier list terbaru untuk setiap hero Mobile Legends.</p>
                <a href="includes/auth.php" class="btn-explore">Mulai Jelajahi Hero</a>
            </div>
        </section>

        <section class="features" id="features">
            <h3>Fitur Unggulan</h3>
            <div class="feature-grid">
                <a href="includes/auth.php" class="feature-item" style="text-decoration:none;">
                    <h4>Tier List Terupdate</h4>
                    <p>Lihat ranking hero terbaru berdasarkan meta dan statistik.</p>
                </a>
                <a href="includes/auth.php" class="feature-item" style="text-decoration:none;">
                    <h4>Build & Guide Lengkap</h4>
                    <p>Rekomendasi build item, emblem, dan strategi terbaik untuk setiap hero.</p>
                </a>
                <a href="includes/auth.php" class="feature-item" style="text-decoration:none;">
                    <h4>Item</h4>
                    <p>Lihat dan pelajari semua item beserta efek dan rekomendasi penggunaannya.</p>
                </a>
                <a href="#" onclick="alert('Silakan login untuk menggunakan Chatbot AI!');return false;" class="feature-item" style="text-decoration:none;">
                    <h4>Chatbot AI</h4>
                    <p>Dapatkan bantuan, tanya tips, atau konsultasi strategi dengan chatbot AI 24 jam!</p>
                </a>
            </div>
            <h3>Fitur Premium</h3>
            <div class="feature-grid">
                <a href="http://localhost:5173/heroverse/fitur%20baru/dist/" class="feature-item" style="text-decoration:none;" target="_blank">
                    <h4>Matchup</h4>
                    <p>Lihat analisis matchup hero lawan dengan mudah dan efektif.</p>
                </a>
            </div>
        </section>

        <!-- News Section -->
<section class="news-section" id="news" style="background:var(--bg-dark);color:var(--light-text);padding:80px 0;">
    <h2 style="text-align:center;font-size:2.8rem;margin-bottom:50px;text-transform:uppercase;letter-spacing:1px;">Berita Terbaru</h2>
    <div class="feature-grid" style="display:grid;text-align:center;grid-template-columns:repeat(auto-fit, minmax(320px, 1fr));gap:40px;max-width:1400px;margin:0 auto;padding:0 30px;">
        <div class="feature-item" style="background:rgba(34,34,40,0.8);backdrop-filter:blur(6px);padding:35px;border-radius:15px;box-shadow:0 10px 20px rgba(0,0,0,0.3);transition:all 0.3s ease;border:1px solid rgba(255,255,255,0.1);">
            <div style="font-size:3rem;margin-bottom:20px;color:var(--secondary-color);"><i class="fas fa-bolt"></i></div>
            <h4 style="font-size:1.5rem;margin-bottom:20px;line-height:1.3;">Patch 1.8.90: Buff & Nerf Terbaru!</h4>
            <p style="font-size:1.1rem;line-height:1.6;margin-bottom:25px;color:rgba(255,255,255,0.9);">
                Moonton resmi merilis patch baru: hero X di-buff, hero Y di-nerf, dan item Z diubah dengan perubahan signifikan pada gameplay.
            </p>
            <a href="#" style="color:var(--secondary-color);font-weight:bold;font-size:1.1rem;text-decoration:none;display:inline-block;padding:8px 0;border-bottom:2px solid var(--secondary-color);">
                Baca selengkapnya <span style="margin-left:8px;">→</span>
            </a>
        </div>
        <div class="feature-item" style="background:rgba(34,34,40,0.8);backdrop-filter:blur(6px);padding:35px;border-radius:15px;box-shadow:0 10px 20px rgba(0,0,0,0.3);transition:all 0.3s ease;border:1px solid rgba(255,255,255,0.1);">
            <div style="font-size:3rem;margin-bottom:20px;color:var(--secondary-color);"><i class="fas fa-gift"></i></div>
            <h4 style="font-size:1.5rem;margin-bottom:20px;line-height:1.3;">Event Ramadan: Skin Gratis & Hadiah Menarik</h4>
            <p style="font-size:1.1rem;line-height:1.6;margin-bottom:25px;color:rgba(255,255,255,0.9);">
                Login setiap hari selama event Ramadan dan dapatkan skin gratis serta hadiah eksklusif lainnya termasuk avatar limited edition!
            </p>
            <a href="#" style="color:var(--secondary-color);font-weight:bold;font-size:1.1rem;text-decoration:none;display:inline-block;padding:8px 0;border-bottom:2px solid var(--secondary-color);">
                Cek detail event <span style="margin-left:8px;">→</span>
            </a>
        </div>
        <div class="feature-item" style="background:rgba(34,34,40,0.8);backdrop-filter:blur(6px);padding:35px;border-radius:15px;box-shadow:0 10px 20px rgba(0,0,0,0.3);transition:all 0.3s ease;border:1px solid rgba(255,255,255,0.1);">
            <div style="font-size:3rem;margin-bottom:20px;color:var(--secondary-color);"><i class="fas fa-fire"></i></div>
            <h4 style="font-size:1.5rem;margin-bottom:20px;line-height:1.3;">Kolaborasi MLBB x Naruto Resmi Dimulai!</h4>
            <p style="font-size:1.1rem;line-height:1.6;margin-bottom:25px;color:rgba(255,255,255,0.9);">
                Skin kolaborasi MLBB x Naruto sudah hadir! Dapatkan skin spesial Naruto, Sasuke, dan Sakura dengan efek skill eksklusif.
            </p>
            <a href="#" style="color:var(--secondary-color);font-weight:bold;font-size:1.1rem;text-decoration:none;display:inline-block;padding:8px 0;border-bottom:2px solid var(--secondary-color);">
                Lihat info kolaborasi <span style="margin-left:8px;">→</span>
            </a>
        </div>
        <div class="feature-item" style="background:rgba(34,34,40,0.8);backdrop-filter:blur(6px);padding:35px;border-radius:15px;box-shadow:0 10px 20px rgba(0,0,0,0.3);transition:all 0.3s ease;border:1px solid rgba(255,255,255,0.1);">
            <div style="font-size:3rem;margin-bottom:20px;color:var(--secondary-color);"><i class="fas fa-trophy"></i></div>
            <h4 style="font-size:1.5rem;margin-bottom:20px;line-height:1.3;">M5 World Championship: Siapa Jagoanmu?</h4>
            <p style="font-size:1.1rem;line-height:1.6;margin-bottom:25px;color:rgba(255,255,255,0.9);">
                Turnamen MLBB terbesar tahun ini telah dimulai! Saksikan tim favoritmu bertanding memperebutkan gelar juara dunia dengan total hadiah $2 juta.
            </p>
            <a href="#" style="color:var(--secondary-color);font-weight:bold;font-size:1.1rem;text-decoration:none;display:inline-block;padding:8px 0;border-bottom:2px solid var(--secondary-color);">
                Jadwal & hasil pertandingan <span style="margin-left:8px;">→</span>
            </a>
        </div>
    </div>
</section>

        <style>
        .spotlight-card {
            position: relative;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 24px rgba(0,0,0,0.18);
            background: #232b4a;
            min-height: 320px;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
        }
        .spotlight-img-wrap {
            position: relative;
            width: 100%;
            height: 100%;
        }
        .spotlight-card img {
            width: 100%;
            height: 320px;
            object-fit: cover;
            display: block;
        }
        .spotlight-info {
            position: absolute;
            bottom: 0;
            left: 0; right: 0;
            background: linear-gradient(0deg, rgba(20,24,40,0.92) 80%, rgba(20,24,40,0.2) 100%);
            color: #fff;
            padding: 24px 18px 18px 18px;
        }
        .role-tag {
            display: inline-block;
            background: #45a2ff;
            color: #fff;
            font-size: 0.95em;
            border-radius: 6px;
            padding: 2px 10px;
            margin-bottom: 8px;
        }
        .btn-detail {
            display: inline-block;
            margin-top: 12px;
            background: #ff9f45;
            color: #232b4a;
            font-weight: bold;
            border-radius: 8px;
            padding: 8px 18px;
            text-decoration: none;
            transition: background 0.2s;
        }
        .btn-detail:hover {
            background: #ff4545;
            color: #fff;
        }
        </style>

        <section class="hero-spotlight" id="spotlight">
            <h3>Pahlawan Unggulan</h3>
            <div class="spotlight-grid">
                <div class="spotlight-card">
                    <div class="spotlight-img-wrap">
                        <img src="images/HERO/Fighter/Kalea/kalea.png" alt="Kalea">
                        <div class="spotlight-info">
                            <h4>Kalea <span class="role-tag">Support / Fighter</span></h4>
                            <p>Support/Fighter yang sedang naik daun di meta terbaru! Memiliki kemampuan heal dan crowd control yang kuat.</p>
                            <a href="includes/auth.php" class="btn-detail">Lihat Detail</a>
                        </div>
                    </div>
                </div>
                <div class="spotlight-card">
                    <div class="spotlight-img-wrap">
                        <img src="images/HERO/Mage/Lunox/lunoxx.png" alt="Lunox">
                        <div class="spotlight-info">
                            <h4>Lunox <span class="role-tag">Mage</span></h4>
                            <p>Mage spesialis burst damage dan fleksibilitas mode cahaya/gelap, sangat efektif di mid lane.</p>
                            <a href="includes/auth.php" class="btn-detail">Lihat Detail</a>
                        </div>
                    </div>
                </div>
                <div class="spotlight-card">
                    <div class="spotlight-img-wrap">
                        <img src="images/HERO/Support/Diggie/Diggie.png" alt="Diggie">
                        <div class="spotlight-info">
                            <h4>Diggie <span class="role-tag">Support</span></h4>
                            <p>Support spesialis anti crowd control, sangat efektif melindungi tim dari efek stun dan lock musuh.</p>
                            <a href="includes/auth.php" class="btn-detail">Lihat Detail</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

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

    <script src="js/script.js"></script>
</body>
</html> 