<?php
require_once '../includes/db_connect.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    exit('Invalid hero ID');
}
$hero_id = intval($_GET['id']);
$sql = "SELECT * FROM heroes WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $hero_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows !== 1) {
    exit('Hero not found');
}
$hero = $result->fetch_assoc();

// Data manual (deskripsi, skill, dsb)
$hero_details = [
    'Kalea' => [
        'description' => 'Dengan ekor ular yang anggun, Kalea adalah gadis keturunan Ular Raksasa yang berenang dengan lincah di Land of Dawn. Ia dikenal sombong namun sangat peduli pada rekan-rekannya. Dengan kekuatan air, ia mampu menciptakan zona air yang memperkuat serangan dan memberikan efek penyembuhan.',
        'skills' => [
            [
                'name' => 'Surge of Life',
                'icon' => '../images/HERO/Fighter/Kalea/Surge of Life.png',
                'desc' => 'Setelah menggunakan skill, Kalea menciptakan <span style="color:#00e5ff">Zona Air</span> di tanah. Ketika dia menggunakan skill di dalam zona, Kalea akan menyerap Zona Air untuk memperkuat tiga Basic Attack berikutnya dengan efek berbeda:<br><br><b>Basic Attack Pertama:</b> Kalea bergerak ke target, memberikan <span style="color:#ffc107">(+100% Total Physical Attack)</span> <span style="color:#ff4444">Physical Damage</span>.<br><b>Basic Attack Kedua:</b> Kalea mengeluarkan serangan beruntun, memberikan 200 <span style="color:#ffc107">(+100% Total Physical Attack)</span> <span style="color:#ff4444">Physical Damage</span> dan memulihkan 120 <span style="color:#00e676">(+10% Total HP)</span> <span style="color:#00bcd4">(+60% Total Magic Power)</span> HP untuk dirinya dan rekan di sekitar dengan HP terendah.<br><b>Basic Attack Ketiga:</b> Kalea menyerang area di sekitar target dengan ekornya, memberikan 100 <span style="color:#ffc107">(+100% Total Physical Attack)</span> <span style="color:#ff4444">Physical Damage</span> dan menciptakan Zona Air baru. Basic Attack yang diperkuat tidak dipengaruhi oleh Attack Speed.'
            ],
            [
                'name' => 'Wavebreaker',
                'icon' => '../images/HERO/Fighter/Kalea/Wavebreaker.png',
                'desc' => 'Kalea menghantam tanah, menciptakan gelombang yang memberikan 200 <span style="color:#00e676">(+5% Total HP)</span> <span style="color:#ff4444">Physical Damage</span> dan menyebabkan 40% slow ke lawan di sekitar selama 1.5 detik.'
            ],
            [
                'name' => 'Tidal Strike',
                'icon' => '../images/HERO/Fighter/Kalea/Tidal Strike.png',
                'desc' => 'Kalea menyerang ke depan, memberikan 180 <span style="color:#ffc107">(+80% Total Physical Attack)</span> <span style="color:#ff4444">Physical Damage</span> ke lawan di jalur.'
            ],
            [
                'name' => 'Tsunami Slam',
                'icon' => '../images/HERO/Fighter/Kalea/Tsunami Slam.png',
                'desc' => 'Kalea memanggil tsunami besar, memberikan 400 <span style="color:#ffc107">(+150% Total Physical Attack)</span> <span style="color:#ff4444">Physical Damage</span> ke semua lawan di area besar.'
            ]
        ],
        'skill_priority' => 'Tingkatkan Skill 1 di Level 1 dan prioritaskan meningkatkan Skill 2 dengan Skill 1 sebagai cadangan. Tingkatkan Ultimate kapan pun tersedia.',
        'combos' => [
            [
                'title' => 'Combo Dasar',
                'sequence' => [
                    '../images/HERO/Fighter/Kalea/Wavebreaker.png',
                    '../images/HERO/Fighter/Kalea/Tidal Strike.png',
                    '../images/HERO/Fighter/Kalea/Basic Attact.png'
                ],
                'desc' => 'Gunakan Wavebreaker untuk engage, lanjutkan dengan Tidal Strike dan Basic Attack yang diperkuat.'
            ],
            [
                'title' => 'Combo Ultimate',
                'sequence' => [
                    '../images/HERO/Fighter/Kalea/Tidal Strike.png',
                    '../images/HERO/Fighter/Kalea/Tsunami Slam.png',
                    '../images/HERO/Fighter/Kalea/Wavebreaker.png',
                    '../images/HERO/Fighter/Kalea/Basic Attact.png'
                ],
                'desc' => 'Mulai dengan Tidal Strike, lalu Tsunami Slam, Wavebreaker, dan Basic Attack.'
            ]
        ],
        'youtube' => 'https://youtu.be/iPMNdmB3omk',
    ],
    'Benedetta' => [
        'description' => 'Petarung wanita yang melindungi Barren Lands dari invasi iblis. Benedetta adalah Assassin/Fighter dengan mobilitas tinggi dan burst damage mematikan, sangat efektif untuk pick-off dan outplay lawan.',
        'skills' => [
            [
                'name' => 'Elapsed Daytime (Passive)',
                'icon' => '../images/HERO/Assassin/Benedetta/Elapsed Daytime.png',
                'desc' => 'Saat menahan tombol <b>Basic Attack</b>, Benedetta memasuki Mode Swordout dan terus mengumpulkan Sword Intent. Jika tombol <b>Basic Attack</b> dilepas setelah Sword Intent terisi penuh, Benedetta akan menggunakan Swordout Slash ke arah di depannya dan berlari ke depan, memberikan <span style="color:#ffc107">(+215% Total Physical Attack)</span> <span style="color:#ff4444">Physical Damage</span> ke lawan di jalurnya (dianggap sebagai Damage Skill dan berkurang menjadi 50% pada Minion dan Creep).<br><br>Benedetta hanya dapat menggunakan Swordout Slash saat Sword Intent terisi penuh dan dia memasuki Mode Swordout dengan menahan tombol <b>Basic Attack</b>.<br>Benedetta juga dapat memperoleh Sword Intent dengan memberikan Damage melalui Basic Attack dan Skill.'
            ],
            [
                'name' => 'Phantom Slash',
                'icon' => '../images/HERO/Assassin/Benedetta/Phantom Slash.png',
                'desc' => 'Benedetta mundur dengan cepat dan meninggalkan bayangan di depan. Setelah jeda singkat, bayangannya menebas ke depan dalam area berbentuk kipas, memberikan 200 <span style="color:#ffc107">(+75% Total Physical Attack)</span> <span style="color:#ff4444">Physical Damage</span> dan menyebabkan 60% Slow kepada lawan selama 0.5 detik.<br><br>Sementara itu, Benedetta berlari ke depan untuk menebas, memberikan 75 <span style="color:#ffc107">(+100% Total Physical Attack)</span> <span style="color:#ff4444">Physical Damage</span>. (Jika target terkena Hit oleh bayangannya, Damage yang diberikan Benedetta akan meningkat menjadi 200%).'
            ],
            [
                'name' => 'An Eye for An Eye',
                'icon' => '../images/HERO/Assassin/Benedetta/An Eye for An Eye.png',
                'desc' => 'Benedetta mengangkat senjatanya untuk bertahan, menjadi Kebal terhadap Crowd Control dan memblokir semua Damage selama 0.8 detik. Setelah ini, dia menyerang ke arah target, memberikan 300 <span style="color:#ffc107">(+80% Total Physical Attack)</span> <span style="color:#ff4444">Physical Damage</span>.<br><br>Jika Benedetta berhasil menghindari Damage selama bertahan, dia akan memperoleh Sword Intent penuh. Jika dia berhasil menghindari Crowd Control apa pun, serangan berikutnya akan menyebabkan Stun ke target selama 1.5 detik.'
            ],
            [
                'name' => 'Alecto: Final Blow (Ultimate)',
                'icon' => '../images/HERO/Assassin/Benedetta/Alecto Final Blow.png',
                'desc' => 'Benedetta menggenggam Alecto, bergerak ke depan (Benedetta tidak dapat ditargetkan saat bergerak), dan menebas setelah jeda singkat, meledakkan Sword Intent di jalurnya. Selama 2.5 detik, Sword Intent memberikan hingga total 1560 <span style="color:#ffc107">(+1105% Extra Physical Attack)</span> <span style="color:#ff4444">Physical Damage</span> ke lawan dan menyebabkan 50% Slow ke lawan di dalamnya.'
            ]
        ],
        'skill_priority' => 'Tingkatkan Skill 1 di Level 1 dan prioritaskan meningkatkan Skill 1 dengan Skill 2 sebagai cadangan. Tingkatkan Ultimate kapan pun tersedia.',
        'combos' => [
            [
                'title' => 'Kombo Team Fight',
                'sequence' => [
                    '../images/HERO/Assassin/Benedetta/Basic Attack.png',
                    '../images/HERO/Assassin/Benedetta/Alecto Final Blow.png',
                    '../images/HERO/Assassin/Benedetta/Phantom Slash.png',
                    '../images/HERO/Assassin/Benedetta/An Eye for An Eye.png'
                ],
                'desc' => 'Saat Team Fight, gunakan Swordout Slash untuk mendekati lawan, lalu gunakan Ultimate Benedetta saat lawan terkepung, dan lanjutkan dengan Skill 1 untuk Damage tambahan dan Skill 2 untuk menahan efek CC.'
            ],
            [
                'title' => 'Kombo Laning',
                'sequence' => [
                    '../images/HERO/Assassin/Benedetta/Basic Attack.png',
                    '../images/HERO/Assassin/Benedetta/Phantom Slash.png',
                    '../images/HERO/Assassin/Benedetta/Alecto Final Blow.png',
                    '../images/HERO/Assassin/Benedetta/Basic Attack.png'
                ],
                'desc' => 'Saat fase Laning, kumpulkan Sword Intent dengan menahan tombol Basic Attack, lalu gunakan Swordout Slash setelah Sword Intent penuh untuk menyerang lawan. Lanjutkan dengan Skill 1 dan coba isi Sword Intent hingga penuh kembali dengan menghalangi serangan lawan dengan Skill 2, lalu akhiri kombo dengan Swordout Slash lagi.'
            ]
        ],
        'youtube' => 'https://youtu.be/FCahwmv4HF0',
    ],
    'Diggie' => [
        'description' => 'Ilmuwan burung hantu yang bisa mengendalikan waktu. Diggie adalah Support yang sangat kuat dalam memberikan perlindungan dan crowd control untuk timnya.',
        'skills' => [
            [
                'name' => 'Young Again (Passive)',
                'icon' => '../images/HERO/Support/Diggie/Young Again.png',
                'desc' => 'Setelah tereliminasi, Diggie memutar ulang waktu dan kembali menjadi telur, dia dapat bergerak dan memperoleh Skill baru. Diggie tidak dapat ditargetkan di wujud telur dan akan bangkit kembali setelah beberapa waktu.'
            ],
            [
                'name' => 'Auto Alarm Bomb',
                'icon' => '../images/HERO/Support/Diggie/Auto Alarm Bomb.png',
                'desc' => 'Diggie melemparkan Owl Alarm ke lokasi target yang tetap diam selama 25 detik atau hingga bereaksi terhadap lawan pertama di sekitar, mengejarnya dan meledak saat mengenainya. Ledakan memberikan 350 <span style="color:#00e5ff">(+100% Total Magic Power)</span> <span style="color:#b388ff">Magic Damage</span> ke lawan di sekitar dan menyebabkan 30% Slow.<br><br>Diggie memperoleh 2 Stack <span style="color:#ffb300">Dangerous Sparks</span> setiap kali Hero lawan terkena ledakan (hingga 60 Stack), setiap Stack meningkatkan Damage Skill sebesar 1%. Diggie kehilangan setengah dari Stack saat tereliminasi, tetapi akan mendapatkan 1 Stack <span style="color:#ffb300">Dangerous Sparks</span> setiap kali Skill-nya mengenai Hero lawan saat dalam Mode Egg.<br><br>Hingga 5 Owl Alarm dapat muncul pada waktu bersamaan.'
            ],
            [
                'name' => 'Reverse Time',
                'icon' => '../images/HERO/Support/Diggie/Reverse Time.png',
                'desc' => 'Diggie meninggalkan Mark ke Hero yang ditargetkan selama 4 detik dan target akan ditarik kembali ke lokasi sebelumnya setelah durasi berakhir, memberikan 150 <span style="color:#00e5ff">(+100% Total Magic Power)</span> <span style="color:#b388ff">Magic Damage</span> dan terkena efek Slow sebesar 80%.<br><br>Jika lawan keluar dari lingkaran, tarikan akan aktif lebih awal.'
            ],
            [
                'name' => 'Time Journey (Ultimate)',
                'icon' => '../images/HERO/Support/Diggie/Time Journey.png',
                'desc' => 'Diggie menghapus semua Debuff ke Hero satu tim di sekitar (termasuk dirinya) dan memberikan Shield 650 <span style="color:#00e5ff">(+300% Total Magic Power)</span> serta menjadi Kebal terhadap Crowd Control selama 2 detik. Dia juga memperoleh 50% Movement Speed tambahan selama 0.5 detik.'
            ]
        ],
        'skill_priority' => 'Tingkatkan Skill 1 di Level 1 dan prioritaskan meningkatkan Skill 1 dengan Skill 2 sebagai cadangan. Tingkatkan Ultimate kapan pun tersedia.',
        'combos' => [
            [
                'title' => 'Kombo Team Fight',
                'sequence' => [
                    '../images/HERO/Support/Diggie/Auto Alarm Bomb.png',
                    '../images/HERO/Support/Diggie/Reverse Time.png',
                    '../images/HERO/Support/Diggie/Time Journey.png'
                ],
                'desc' => 'Saat Team Fight, gunakan Skill 1 untuk mengganggu lawan. Jika lawan mencoba menginisiasi, tarik mereka kembali dengan Skill 2. Jika rekan tim terkena Crowd Control atau menerima banyak Damage, gunakan Ultimate untuk menyelamatkan mereka.'
            ],
            [
                'title' => 'Kombo Laning',
                'sequence' => [
                    '../images/HERO/Support/Diggie/Reverse Time.png',
                    '../images/HERO/Support/Diggie/Auto Alarm Bomb.png'
                ],
                'desc' => 'Saat fase Laning, gunakan Skill 2 untuk menahan lawan di lokasi mereka, lalu lemparkan alarm dengan Skill 1.'
            ]
        ],
        'youtube' => 'https://youtu.be/Jhs78OEHwy8',
    ],
    'Joy' => [
        'description' => 'Si "Anak Ajaib" yang kembali ke Leonin setelah bertahun-tahun mengembara.',
        'skills' => [
            [
                'name' => 'Humph, Joy\'s Angry!',
                'icon' => '../images/HERO/Assassin/Joy/humph Joy\'s Angry.png',
                'desc' => 'Joy memasuki Mode Angry! setiap kali Skill-nya mengenai lawan non-Minion, memberikan 100 <span style="color:#00e5ff">(+80% Total Magic Power)</span> <span style="color:#b388ff">Magic Damage</span> dan meningkatkan 60% Movement Speed yang berkurang seiring waktu selama 4 detik (terkena Leonin Crystal juga akan memberikan efek Movement Speed). Efek ini memiliki Cooldown selama 4 detik untuk setiap target.<br><br>Basic Attack Joy akan memberikan 65 <span style="color:#ffc107">(+65% Total Physical Attack)</span> <span style="color:#00e5ff">(+65% Total Magic Power)</span> <span style="color:#b388ff">Magic Damage</span>.'
            ],
            [
                'name' => 'Look, Leonin Crystal!',
                'icon' => '../images/HERO/Assassin/Joy/Look Leonin Crystal.png',
                'desc' => 'Joy memanggil Leonin Crystal ke lokasi target, memberikan 200 <span style="color:#00e5ff">(+40% Total Magic Power)</span> <span style="color:#b388ff">Magic Damage</span> ke lawan di sekitar dan menyebabkan 30% Slow ke mereka selama 1 detik. Leonin Crystal berlangsung hingga selama 2 detik.'
            ],
            [
                'name' => 'Meow, Rhythm of Joy!',
                'icon' => '../images/HERO/Assassin/Joy/Meow Rhythm of Joy.png',
                'desc' => 'Joy bergerak ke arah target, memberikan 120 <span style="color:#00e5ff">(+55% Total Magic Power)</span> <span style="color:#b388ff">Magic Damage</span> ke lawan di jalurnya. Mengenai lawan atau <span style="color:#ffd600">Leonin Crystal</span> membuat Joy dapat menggunakan kembali Skill-nya dalam 0.8 detik dan mendapatkan 1 Beat Energy. Menggunakan Skill ini sesuai ketukan akan meningkatkan 100% Damage Skill dan memberikan 80 <span style="color:#00e5ff">(+60% Total Magic Power)</span> Shield yang bertahan selama 3 detik.<br><br>Skill ini dapat digunakan hingga 4 kali berturut-turut.'
            ],
            [
                'name' => 'Ha, Electrifying Beats!',
                'icon' => '../images/HERO/Assassin/Joy/Ha Electrifying Beast.png',
                'desc' => 'Joy meningkatkan 15 <span style="color:#00e5ff">(+10% Total Magic Power)</span> Physical & Magic Defense serta memperoleh 30% Movement Speed. Selama 3 detik berikutnya, dia berdentum 8 kali, memberikan 120 <span style="color:#00e5ff">(+40% Total Magic Power)</span> <span style="color:#b388ff">Magic Damage</span> setiap berdentum ke lawan di sekitar.<br><br>Setiap <span style="color:#ffd600">Meow, Rhythm of Joy!</span> digunakan sesuai ketukan meningkatkan 40% Damage Skill ini. Jika ritme sempurna tercapai dengan mengenai setiap ketukan dengan <span style="color:#ffd600">Meow, Rhythm of Joy!</span>, Joy juga memperoleh 40% Spell Vamp saat Skill ini aktif.<br><br>Skill ini terbuka saat Joy memperoleh Beat Energy. Menggunakan Skill ini dengan 3 Beat Energy akan menghapus semua Debuff pada Joy dan membuatnya kebal Slow selama Skill ini aktif.'
            ]
        ],
        'skill_priority' => 'Tingkatkan Skill 2 di Level 1 dan prioritaskan meningkatkan Skill 2 dengan Skill 1 sebagai cadangan. Tingkatkan Ultimate kapan pun tersedia.',
        'combos' => [
            [
                'title' => 'Kombo Team Fight',
                'sequence' => [
                    '../images/HERO/Assassin/Joy/Look Leonin Crystal.png',
                    '../images/HERO/Assassin/Joy/Meow Rhythm of Joy.png',
                    '../images/HERO/Assassin/Joy/Ha Electrifying Beast.png'
                ],
                'desc' => 'Di Team Fight, panggil Leonin Crystal dengan Skill 1 dan gunakan sebagai pijakan Skill 2 untuk memasuki pertarungan. Terus gunakan Skill 2 sesuai ketukan untuk membuka Ultimate. Terakhir, gunakan Ultimate untuk mengeliminasi lawan.'
            ],
            [
                'title' => 'Kombo Laning',
                'sequence' => [
                    '../images/HERO/Assassin/Joy/Look Leonin Crystal.png',
                    '../images/HERO/Assassin/Joy/Meow Rhythm of Joy.png'
                ],
                'desc' => 'Di fase Laning, letakkan Skill 1 di atas lawan, lalu gunakan Skill 2 untuk bergerak melewati kedua target dan terus gunakan Skill 2 sesuai ketukan untuk menghasilkan Damage.'
            ]
        ],
        'youtube' => 'https://youtu.be/HNvtvhygKTY',
    ],
    // Tambahkan hero lain jika perlu
];

// Ambil hero counter dari DB
$counter_heroes = [];
$sql = "SELECT h.id, h.name, h.role, h.image_path, hc.description FROM hero_counters hc
        JOIN heroes h ON hc.counter_hero_id = h.id
        WHERE hc.hero_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $hero_id);
$stmt->execute();
$counter_result = $stmt->get_result();
while ($row = $counter_result->fetch_assoc()) {
    $counter_heroes[] = $row;
}

// Query untuk hero yang di-counter oleh hero ini (meng-counter)
$sql2 = "SELECT h.id, h.name, h.image_path, hc.description FROM hero_counters hc
         JOIN heroes h ON hc.hero_id = h.id
         WHERE hc.counter_hero_id = ?";
$stmt2 = $conn->prepare($sql2);
$stmt2->bind_param("i", $hero_id);
$stmt2->execute();
$countered_result = $stmt2->get_result();
$countered_heroes = [];
while ($row = $countered_result->fetch_assoc()) {
    $countered_heroes[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Hero - <?php echo htmlspecialchars($hero['name']); ?></title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/homepage.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .detail-hero-container {
            display: flex;
            gap: 24px;
            background: #10141a;
            padding: 20px;
            max-width: 1200px;
            margin: 30px auto;
            border-radius: 8px;
        }
        .detail-hero-left {
            flex: 0 0 250px;
            text-align: center;
            position: relative;
        }
        .hero-profile-image {
            position: relative;
            display: inline-block;
        }
        .hero-main-role-logo {
            position: absolute;
            top: 8px;
            right: 8px;
            width: 32px;
            height: 32px;
            background-color: rgba(0,0,0,0.6);
            border-radius: 4px;
            padding: 4px;
        }
        .detail-hero-left img.profile-pic {
            width: 100%;
            height: auto;
            border-radius: 8px;
            margin-bottom: 12px;
        }
        .detail-hero-left h2 { margin-bottom: 8px; }
        .hero-role-icons { display: flex; justify-content: center; gap: 10px; margin-bottom: 8px; }
        .role-icon { width: 28px; height: 28px; background-color: #3a4151; border-radius: 50%; display: flex; align-items: center; justify-content: center; padding: 4px; box-sizing: border-box;}
        .role-icon img { width: 100%; height: auto; }
        .detail-hero-stats { display: flex; justify-content: space-around; background: #1a2028; padding: 8px; border-radius: 6px; margin-bottom: 8px; }
        .stat-item { color: #fff; }
        .stat-item .value { font-size: 1.1rem; font-weight: bold; }
        .stat-item .label { font-size: 0.8rem; color: #8a96b1; }
        .detail-hero-desc { color: #8a96b1; font-size: 0.9rem; margin-top: 12px; }
        .detail-hero-right { flex: 1; background: #1a2028; border-radius: 8px; padding: 16px; position: relative; }
        .back-button { position: absolute; top: 16px; right: 16px; width: 32px; height: 32px; background: #3a4151; color: #fff; border-radius: 50%; text-align: center; line-height: 32px; font-size: 1.2rem; text-decoration: none; transition: background 0.2s; }
        .back-button:hover { background: #00aaff; }
        .detail-hero-tabs { display: flex; gap: 20px; border-bottom: 1px solid #3a4151; }
        .detail-hero-tab { color: #8a96b1; font-size: 1rem; font-weight: 600; padding: 8px 12px; cursor: pointer; border: none; background: none; border-bottom: 2px solid transparent; transition: all 0.2s; }
        .detail-hero-tab.active { color: #fff; border-bottom-color: #00aaff; }
        .skill-content { padding-top: 20px; }
        .skill-selector { display: flex; gap: 16px; margin-bottom: 16px; }
        .skill-icon { width: 60px; height: 60px; border-radius: 50%; border: 2px solid #5a6171; cursor: pointer; transition: all 0.2s; }
        .skill-icon.active { border-color: #00aaff; box-shadow: 0 0 10px #00aaff; }
        .skill-detail .name { font-size: 1.3rem; color: #fff; margin-bottom: 4px; }
        .skill-detail .type { display: inline-block; background: #3a4151; color: #00aaff; font-size: 0.8rem; padding: 2px 8px; border-radius: 4px; }
        .skill-detail .desc { color: #bfc8e2; font-size: 0.95rem; line-height: 1.6; margin-top: 12px; }
        .priority-skill, .combo-skill { margin-top: 24px; }
        .priority-skill h4, .combo-skill h4 { color: #fff; margin-bottom: 12px; }
        .combo-item { background: #10141a; padding: 12px; border-radius: 6px; margin-bottom: 12px; }
        .combo-sequence { display: flex; align-items: center; gap: 8px; margin-top: 8px; }
        .combo-sequence img { width: 40px; height: 40px; border-radius: 50%; }
        .combo-sequence .fa-arrow-right { color: #5a6171; }
        .combo-section {
            display: flex;
            flex-direction: column;
            gap: 30px;
            margin-top: 30px;
        }
        .combo-box {
            background: #181c23;
            border-radius: 12px;
            padding: 28px 32px 20px 32px;
            box-shadow: 0 0 0 2px #232733;
        }
        .combo-title {
            color: #fff;
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 18px;
        }
        .combo-sequence {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 15px;
        }
        .combo-sequence img {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: #232733;
            border: 2px solid #232733;
            object-fit: cover;
        }
        .combo-sequence .fa-arrow-right {
            color: #7abaff;
            font-size: 1.5rem;
        }
        .combo-desc {
            color: #b8c7e0;
            font-size: 1rem;
            margin-top: 0;
        }
        .hero-detail-card {
            position: relative;
            background: none;
            color: #fff;
            border-radius: 18px;
            overflow: hidden;
            width: 100%;
            max-width: 320px;
            margin: 0 auto 24px auto;
            font-family: 'Poppins', sans-serif;
            box-shadow: 0 2px 12px 0 rgba(0,0,0,0.18);
            display: flex;
            flex-direction: column;
            min-height: 420px;
        }
        .hero-bg-image {
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center center;
            z-index: 1;
            filter: brightness(0.7) blur(1.5px);
            transition: filter 0.2s;
        }
        .hero-card-overlay {
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: linear-gradient(180deg, rgba(20,24,40,0.55) 60%, rgba(20,24,40,0.85) 100%);
            z-index: 2;
            backdrop-filter: blur(2.5px);
        }
        .hero-info {
            position: relative;
            z-index: 3;
            padding: 32px 18px 22px 18px;
            display: flex;
            flex-direction: column;
            align-items: center;
            height: 100%;
            justify-content: flex-end;
        }
        .hero-name {
            margin: 12px 0 4px 0;
            font-size: 1.6em;
            font-weight: 600;
            text-align: center;
            text-shadow: 0 2px 8px #000a;
        }
        .hero-roles-icon {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-bottom: 12px;
        }
        .role-icon-img {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #2e3a5e;
            border-radius: 6px;
            padding: 3px 6px;
            height: 32px;
        }
        .role-icon-img img {
            height: 24px;
            width: 24px;
            object-fit: contain;
            display: block;
        }
        .hero-stats-bar {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 18px;
            margin-bottom: 10px;
            margin-top: 8px;
            width: 100%;
        }
        .hero-stats {
            display: flex;
            flex-direction: row;
            gap: 18px;
        }
        .stat {
            text-align: center;
            min-width: 60px;
            background: none;
            border-radius: 6px;
            padding: 0 4px;
        }
        .stat-value {
            color: #ffe082;
            font-size: 1.15em;
            font-weight: 600;
            display: block;
            text-shadow: 0 2px 8px #000a;
        }
        .stat-label {
            display: block;
            color: #b0b8d0;
            font-size: 0.92em;
            margin-top: 2px;
            text-shadow: 0 2px 8px #000a;
        }
        .hero-section-title {
            color: #b0d0ff;
            font-size: 1.1em;
            margin: 10px 0 2px 0;
            text-align: center;
            text-shadow: 0 2px 8px #000a;
        }
        .hero-desc {
            color: #e0e6f7;
            font-size: 1em;
            margin-top: 18px;
            background: rgba(20,24,40,0.55);
            border-radius: 12px;
            padding: 16px 14px;
            text-align: center;
            box-shadow: 0 2px 8px #0003;
            text-shadow: 0 2px 8px #000a;
            min-height: 80px;
            max-width: 95%;
            font-style: italic;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        @media (max-width: 600px) {
            .hero-detail-card {
                max-width: 98vw;
            }
            .hero-image-container {
                height: 160px;
            }
        }
        .counter-hero-img-row {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }
        .counter-hero-img-col {
            display: flex;
            flex-direction: row;
            align-items: flex-start;
            gap: 16px;
            background: #232b4a;
            border-radius: 10px;
            padding: 10px 16px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            max-width: 600px;
            margin-bottom: 6px;
        }
        .counter-hero-img {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #fff;
            box-shadow: 0 2px 6px rgba(0,0,0,0.15);
            background: #222;
            display: inline-block;
            vertical-align: middle;
            margin-right: 0;
        }
        .counter-hero-img-desc {
            color: #b0d0ff;
            font-size: 0.98em;
            text-align: left;
            margin-top: 0;
            max-width: 500px;
            word-break: break-word;
            white-space: normal;
            line-height: 1.5;
            padding: 2px 6px;
        }
        @media (max-width: 700px) {
            .counter-hero-img-col {
                flex-direction: column;
                align-items: center;
                max-width: 98vw;
                padding: 8px 4px;
            }
            .counter-hero-img-desc {
                max-width: 98vw;
                text-align: center;
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
        <label for="nav-toggle" class="nav-toggle-label"><span></span></label>
        <nav>
            <ul>
                <li><a href="homepage.php">Home</a></li>
                <li><a href="hero.php">Hero</a></li>
                <li><a href="Item.php">Item</a></li>
                <li><a href="#">Build</a></li>
                <li><a href="tier_hero.php">Tier Hero</a></li>
                <li><a href="../includes/logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>
    <main style="background:#10141a;min-height:100vh;">
        <div class="detail-hero-container">
            <div class="detail-hero-left">
                <div class="hero-detail-card">
                  <img src="../<?php echo htmlspecialchars($hero['image_path']); ?>" alt="<?php echo htmlspecialchars($hero['name']); ?>" class="hero-bg-image">
                  <div class="hero-card-overlay"></div>
                  <div class="hero-info">
                    <h2 class="hero-name"><?php echo htmlspecialchars($hero['name']); ?></h2>
                    <div class="hero-roles-icon">
                      <?php foreach (explode(',', $hero['role']) as $role): ?>
                        <span class="role-icon-img" title="<?php echo trim($role); ?>">
                          <img src="../images/LOGO/Main Emblems/<?php echo trim($role); ?>.webp" alt="<?php echo trim($role); ?>" />
                        </span>
                      <?php endforeach; ?>
                    </div>
                    <div class="hero-stats-bar">
                      <div class="hero-stats">
                        <div class="stat">
                          <span class="stat-value"><?php echo htmlspecialchars($hero['win_rate']); ?>%</span>
                          <span class="stat-label">Win Rate</span>
                        </div>
                        <div class="stat">
                          <span class="stat-value"><?php echo htmlspecialchars($hero['pick_rate']); ?>%</span>
                          <span class="stat-label">Pick Rate</span>
                        </div>
                        <div class="stat">
                          <span class="stat-value"><?php echo htmlspecialchars($hero['ban_rate']); ?>%</span>
                          <span class="stat-label">Ban Rate</span>
                        </div>
                      </div>
                      <div class="stat-refresh" title="Refresh Stats">
                        <i class="fas fa-sync-alt"></i>
                      </div>
                    </div>
                    <div class="hero-section-title">
                      <i class="fas fa-chart-bar"></i> Win Rate
                    </div>
                    <div class="hero-desc">
                      <em><?php echo isset($hero_details[$hero['name']]) ? $hero_details[$hero['name']]['description'] : ''; ?></em>
                    </div>
                  </div>
                </div>
            </div>
            <div class="detail-hero-right">
                <a href="hero.php" class="back-button" title="Kembali ke Daftar Hero"><i class="fas fa-undo"></i></a>
                <div class="detail-hero-tabs">
                    <button class="detail-hero-tab active">SKILL</button>
                    <button class="detail-hero-tab">Meng-Counter</button>
                    <button class="detail-hero-tab">PANDUAN</button>
                </div>
                <div id="skill-content" class="skill-content">
                    <div class="skill-selector">
                        <?php if (isset($hero_details[$hero['name']]['skills'])): ?>
                            <?php foreach ($hero_details[$hero['name']]['skills'] as $index => $skill): ?>
                                <img src="<?php echo $skill['icon']; ?>" class="skill-icon<?php echo $index === 0 ? ' active' : ''; ?>" data-index="<?php echo $index; ?>">
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <div id="skill-detail" class="skill-detail">
                        <?php if (isset($hero_details[$hero['name']]['skills'][0])): ?>
                            <h3 class="name"><?php echo $hero_details[$hero['name']]['skills'][0]['name']; ?></h3>
                            <p class="desc"><?php echo $hero_details[$hero['name']]['skills'][0]['desc']; ?></p>
                        <?php endif; ?>
                    </div>
                    <?php if (isset($hero_details[$hero['name']]['skill_priority'])): ?>
                    <div class="priority-skill">
                        <h4>Prioritas Skill</h4>
                        <p style="color:#bfc8e2;"> <?php echo $hero_details[$hero['name']]['skill_priority']; ?> </p>
                    </div>
                    <?php endif; ?>
                    <?php if (isset($hero_details[$hero['name']]['combos'])): ?>
                    <div class="combo-section">
                        <?php foreach ($hero_details[$hero['name']]['combos'] as $combo): ?>
                        <div class="combo-box">
                            <div class="combo-title"><?php echo $combo['title']; ?></div>
                            <div class="combo-sequence">
                                <?php foreach ($combo['sequence'] as $i => $img): ?>
                                    <img src="<?php echo $img; ?>" alt="Combo">
                                    <?php if ($i < count($combo['sequence']) - 1): ?><span class="fa fa-arrow-right"></span><?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                            <div class="combo-desc">
                                <?php echo $combo['desc']; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
                <div id="counter-content" class="skill-content" style="display:none;">
                    <div class="counter-box">
                        <div class="counter-title">Meng-Counter</div>
                        <div class="counter-desc">
                            <?php echo !empty($countered_heroes) ? "{$hero['name']} meng-counter hero berikut:" : "Belum ada data."; ?>
                        </div>
                        <div class="counter-hero-img-row">
                            <?php foreach ($countered_heroes as $h): ?>
                                <div class="counter-hero-img-col">
                                    <img src="../<?php echo htmlspecialchars($h['image_path']); ?>" alt="<?php echo htmlspecialchars($h['name']); ?>" class="counter-hero-img">
                                    <?php if (!empty($h['description'])): ?>
                                        <div class="counter-hero-img-desc"><?php echo htmlspecialchars($h['description']); ?></div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="counter-box">
                        <div class="counter-title">Di-Counter oleh</div>
                        <div class="counter-desc">
                            <?php echo !empty($counter_heroes) ? "{$hero['name']} di-counter oleh hero berikut:" : "Belum ada data."; ?>
                        </div>
                        <div class="counter-hero-img-row">
                            <?php foreach ($counter_heroes as $h): ?>
                                <div class="counter-hero-img-col">
                                    <img src="../<?php echo htmlspecialchars($h['image_path']); ?>" alt="<?php echo htmlspecialchars($h['name']); ?>" class="counter-hero-img">
                                    <?php if (!empty($h['description'])): ?>
                                        <div class="counter-hero-img-desc"><?php echo htmlspecialchars($h['description']); ?></div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <div id="panduan-content" class="skill-content" style="display:none;">
                    <?php if (isset($hero_details[$hero['name']]['youtube'])): ?>
                    <div class="youtube-guide-card" onclick="window.open('<?php echo $hero_details[$hero['name']]['youtube']; ?>','_blank')" style="cursor:pointer;max-width:420px;margin:32px auto 0 auto;box-shadow:0 4px 24px rgba(0,0,0,0.18);border-radius:16px;overflow:hidden;background:#181c23;transition:box-shadow 0.2s;">
                        <div style="position:relative;">
                            <img src="https://img.youtube.com/vi/<?php echo preg_replace('/.*v=|.*be\//','',$hero_details[$hero['name']]['youtube']); ?>/hqdefault.jpg" alt="Panduan Video" style="width:100%;display:block;">
                            <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);background:rgba(0,0,0,0.6);border-radius:50%;padding:18px 20px;">
                                <svg width="48" height="48" viewBox="0 0 48 48"><circle cx="24" cy="24" r="24" fill="#fff" fill-opacity="0.7"/><polygon points="20,16 36,24 20,32" fill="#e53935"/></svg>
                            </div>
                        </div>
                        <div style="padding:18px 18px 14px 18px;">
                            <div style="font-size:1.15em;font-weight:600;color:#fff;margin-bottom:6px;">Panduan Video YouTube</div>
                            <div style="color:#b0b8d0;font-size:0.98em;">Klik untuk menonton panduan di YouTube</div>
                        </div>
                    </div>
                    <?php else: ?>
                    <div style="text-align:center;color:#b0b8d0;margin-top:32px;">Panduan video belum tersedia untuk hero ini.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <!-- Floating Chatbot Button -->
        <a href="chatbot.php" class="chatbot-fab" title="Buka Chatbot">
            <i class="fas fa-robot"></i>
        </a>
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

    <script src="../js/script.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const skills = <?php echo isset($hero_details[$hero['name']]['skills']) ? json_encode($hero_details[$hero['name']]['skills']) : '[]'; ?>;
            const skillIcons = document.querySelectorAll('.skill-icon');
            const skillDetail = document.getElementById('skill-detail');
            skillIcons.forEach(icon => {
                icon.addEventListener('click', function() {
                    const index = this.dataset.index;
                    const skill = skills[index];
                    skillIcons.forEach(i => i.classList.remove('active'));
                    this.classList.add('active');
                    skillDetail.innerHTML = `<h3 class=\"name\">${skill.name}</h3><p class=\"desc\">${skill.desc}</p>`;
                });
            });

            // Tab switching logic
            const tabs = document.querySelectorAll('.detail-hero-tab');
            const skillContent = document.getElementById('skill-content');
            const counterContent = document.getElementById('counter-content');
            const panduanContent = document.getElementById('panduan-content');
            tabs.forEach((tab, idx) => {
                tab.addEventListener('click', function() {
                    tabs.forEach(t => t.classList.remove('active'));
                    this.classList.add('active');
                    skillContent.style.display = idx === 0 ? '' : 'none';
                    counterContent.style.display = idx === 1 ? '' : 'none';
                    panduanContent.style.display = idx === 2 ? '' : 'none';
                });
            });
        });
    </script>
</body>
</html> 