<?php
exit;
// Нема таймаутів при запуску з CLI
ini_set('max_execution_time', 0);
set_time_limit(0);
ignore_user_abort(true);
ini_set('memory_limit', '2048M');

$dir = '/var/www/agcity/agcity.com.ua/frontend/web/upload/shop/products';

$maxWidth = 1200;
$quality  = 75;

$files = scandir($dir);
 
foreach ($files as $file) {

    if ($file === '.' || $file === '..') continue;

    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    if ($ext !== 'jpg' && $ext !== 'jpeg') continue;

    $path = $dir . '/' . $file;

    if (!is_file($path) || !is_writable($path)) {
        echo "⚠️ Skip (no write): $file\n";
        continue;
    }

    $info = @getimagesize($path);
    if (!$info) {
        echo "⚠️ Skip (bad image): $file\n";
        continue;
    }

    [$origWidth, $origHeight] = $info;

    // Головна умова: ширина > 1200px
    if ($origWidth <= $maxWidth) {
        echo "⏭ Skip (width ≤ 1200): $file ({$origWidth}px)\n";
        continue;
    }

    $img = @imagecreatefromjpeg($path);
    if (!$img) {
        echo "❌ Error load: $file\n";
        continue;
    }

    $ratio = $maxWidth / $origWidth;
    $newWidth = $maxWidth;
    $newHeight = intval($origHeight * $ratio);

    $newImg = imagecreatetruecolor($newWidth, $newHeight);

    imagecopyresampled($newImg, $img,
        0, 0, 0, 0,
        $newWidth, $newHeight,
        $origWidth, $origHeight
    );

    imagejpeg($newImg, $path, $quality);

    imagedestroy($img);
    imagedestroy($newImg);

    echo "✅ Resized: $file ({$origWidth}x{$origHeight} → {$newWidth}x{$newHeight})\n";
}

echo "\n✅ Completed!\n";
