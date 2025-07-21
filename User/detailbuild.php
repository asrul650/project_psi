function normalizePath($path) {
    if (!$path) return '../assets/images/default_item.png';
    if (strpos($path, '..') === 0) return $path;
    if (strpos($path, '/') === 0) return '..' . $path;
    return '../' . $path;
}

<img src="<?= normalizePath($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" onerror="this.src='../assets/images/default_item.png'">
<img src="<?= normalizePath($emblem['file']) ?>" alt="<?= htmlspecialchars($emblem['name']) ?>" onerror="this.src='../assets/images/default_item.png'"> 