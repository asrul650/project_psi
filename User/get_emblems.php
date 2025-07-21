<?php
header('Content-Type: application/json');

function scanEmblems($baseDir, $section = '') {
    $emblems = [];
    $dir = $baseDir . ($section ? '/' . $section : '');
    if (!is_dir($dir)) return $emblems;
    $files = scandir($dir);
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') continue;
        $path = $dir . '/' . $file;
        if (is_dir($path)) {
            // Rekursif untuk subfolder
            $emblems = array_merge($emblems, scanEmblems($baseDir, ($section ? $section . '/' : '') . $file));
        } else {
            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            if (in_array($ext, ['png', 'jpg', 'jpeg', 'webp'])) {
                $emblems[] = [
                    'name' => pathinfo($file, PATHINFO_FILENAME),
                    'section' => $section ?: 'Main Emblems',
                    'image_path' => $path
                ];
            }
        }
    }
    return $emblems;
}

$baseDir = __DIR__ . '/../images/LOGO';
$sections = [
    'Main Emblems',
    'Ability Emblems - Section 1',
    'Ability Emblems - Section 2',
    'Ability Emblems - Section 3'
];

$allEmblems = [];
foreach ($sections as $section) {
    $allEmblems = array_merge($allEmblems, scanEmblems($baseDir, $section));
}

// Path untuk frontend (hilangkan __DIR__)
foreach ($allEmblems as &$emblem) {
    $emblem['image_path'] = str_replace(__DIR__ . '/../', '', $emblem['image_path']);
    $emblem['image_path'] = str_replace('\\', '/', $emblem['image_path']); // Windows fix
}
unset($emblem);

echo json_encode($allEmblems); 