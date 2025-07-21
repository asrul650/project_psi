<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
require_once '../includes/db_connect.php';

// Mapping nama hero ke file gambar unik
$heroImageMap = [
    'Kalea' => 'assets/images/heroes/Kalea.png',
    // Tambahkan mapping hero lain di sini jika perlu
];

$sql = "SELECT id, name, role, tier, image_path, lane FROM heroes ORDER BY name ASC";
$result = $conn->query($sql);
$heroes = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Path gambar: cek file benar-benar ada, jika tidak pakai default
        $defaultAvatar = '/heroverse/assets/images/default_hero.png';
        $avatar = $defaultAvatar;
        // Cek mapping manual dulu
        if (isset($heroImageMap[$row['name']])) {
            $imgPath = $_SERVER['DOCUMENT_ROOT'] . '/heroverse/' . $heroImageMap[$row['name']];
            if (file_exists($imgPath)) {
                $avatar = '/heroverse/' . $heroImageMap[$row['name']];
            }
        } elseif (isset($row['image_path']) && $row['image_path']) {
            $imgPath = $_SERVER['DOCUMENT_ROOT'] . '/heroverse/' . ltrim($row['image_path'], '/');
            if (file_exists($imgPath)) {
                $avatar = '/heroverse/' . ltrim($row['image_path'], '/');
            }
        }
        $row['avatar'] = $avatar;
        unset($row['image_path']);
        // Ambil lane dari kolom database jika ada
        $lanes = [];
        if (!empty($row['lane'])) {
            $lanes = array_map('trim', explode(',', $row['lane']));
        }
        // Data dummy/manual untuk analisis, gunakan id hero dan isi untuk kedua sisi
        if ($row['name'] === 'Akai') {
            $row['counters'] = [26, 18, 42];
            $row['synergies'] = [ ['heroId' => 45, 'score' => 5.2], ['heroId' => 26, 'score' => 4.1] ];
            $row['lanes'] = !empty($lanes) ? $lanes : ['Jungle', 'Roamer'];
            $row['winRate'] = 54.2;
        } elseif ($row['name'] === 'Alice') {
            $row['counters'] = [26, 18, 29];
            $row['synergies'] = [ ['heroId' => 43, 'score' => 5.2] ];
            $row['lanes'] = !empty($lanes) ? $lanes : ['EXP Lane'];
            $row['winRate'] = 51.7;
        } elseif ($row['name'] === 'Atlas') {
            $row['counters'] = [18, 29, 42, 15];
            $row['synergies'] = [ ['heroId' => 43, 'score' => 4.5], ['heroId' => 18, 'score' => 4.2] ];
            $row['lanes'] = !empty($lanes) ? $lanes : ['Roam'];
            $row['winRate'] = 52.1;
        } elseif ($row['name'] === 'Barats') {
            $row['counters'] = [29, 33, 42];
            $row['synergies'] = [ ['heroId' => 26, 'score' => 4.2], ['heroId' => 18, 'score' => 3.8] ];
            $row['lanes'] = !empty($lanes) ? $lanes : ['EXP Lane', 'Roam'];
            $row['winRate'] = 50.5;
        } elseif ($row['name'] === 'Baxia') {
            $row['counters'] = [18, 33, 42];
            $row['synergies'] = [ ['heroId' => 18, 'score' => 3.8], ['heroId' => 29, 'score' => 4.0] ];
            $row['lanes'] = !empty($lanes) ? $lanes : ['Roam'];
            $row['winRate'] = 49.7;
        } elseif ($row['name'] === 'Belerick') {
            $row['counters'] = [18, 26, 42];
            $row['synergies'] = [ ['heroId' => 29, 'score' => 4.0], ['heroId' => 33, 'score' => 3.5] ];
            $row['lanes'] = !empty($lanes) ? $lanes : ['Roamer', 'EXP Lane'];
            $row['winRate'] = 48.9;
        } elseif ($row['name'] === 'Franco') {
            $row['counters'] = [15, 37, 26, 18];
            $row['synergies'] = [ ['heroId' => 17, 'score' => 4.1], ['heroId' => 42, 'score' => 3.7] ];
            $row['lanes'] = !empty($lanes) ? $lanes : ['Roam'];
            $row['winRate'] = 49.9;
        } elseif ($row['name'] === 'Fredrinn') {
            $row['counters'] = [42, 37, 26];
            $row['synergies'] = [ ['heroId' => 16, 'score' => 4.3], ['heroId' => 15, 'score' => 3.9] ];
            $row['lanes'] = !empty($lanes) ? $lanes : ['EXP Lane', 'Roam'];
            $row['winRate'] = 51.5;
        } else {
            $row['counters'] = [];
            $row['synergies'] = [];
            $row['lanes'] = !empty($lanes) ? $lanes : [];
            $row['winRate'] = 50;
        }
        unset($row['lane']);
        $heroes[] = $row;
    }
}
echo json_encode($heroes); 