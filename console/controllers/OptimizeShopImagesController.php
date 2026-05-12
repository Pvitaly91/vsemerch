<?php

namespace console\controllers;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Throwable;
use Yii;
use yii\console\Controller;
use yii\console\ExitCode;

/**
 * Optimizes imported shop images in place without changing paths or names.
 */
class OptimizeShopImagesController extends Controller
{
    const MIN_SAVING_RATIO = 0.05;

    public $path = '@frontend/web/upload/shop';
    public $minSizeKb = 500;
    public $maxWidth = 1600;
    public $maxHeight = 1600;
    public $quality = 82;
    public $pngCompression = 8;
    public $dryRun = 0;
    public $limit = 0;
    public $verbose = 0;

    private $lockHandle;

    public function options($actionID)
    {
        return array_merge(parent::options($actionID), [
            'path',
            'minSizeKb',
            'maxWidth',
            'maxHeight',
            'quality',
            'pngCompression',
            'dryRun',
            'limit',
            'verbose',
        ]);
    }

    public function actionIndex()
    {
        @set_time_limit(0);
        $this->ensureMemoryLimit('1024M');
        $this->normalizeOptions();

        if (!extension_loaded('gd')) {
            $this->stderr("PHP GD extension is required.\n");
            return ExitCode::UNSPECIFIED_ERROR;
        }

        $rootPath = Yii::getAlias($this->path);
        if (!is_dir($rootPath)) {
            $this->stderr("Directory does not exist: {$rootPath}\n");
            return ExitCode::UNSPECIFIED_ERROR;
        }

        $rootPath = rtrim($rootPath, DIRECTORY_SEPARATOR . '/');
        if (!$this->acquireLock()) {
            $this->stderr("Another optimize-shop-images process is already running.\n");
            return ExitCode::UNSPECIFIED_ERROR;
        }

        $stats = $this->createStats();
        $minBytes = $this->minSizeKb * 1024;

        $this->stdout("Scanning: {$rootPath}\n");
        $this->stdout("Minimum size: {$this->minSizeKb} KB, max dimensions: {$this->maxWidth}x{$this->maxHeight}, quality: {$this->quality}\n");
        if ($this->dryRun) {
            $this->stdout("Dry run: files will not be replaced.\n");
        }

        try {
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($rootPath, \FilesystemIterator::SKIP_DOTS),
                RecursiveIteratorIterator::LEAVES_ONLY
            );

            foreach ($iterator as $file) {
                if (!$file->isFile()) {
                    continue;
                }

                $stats['scannedFiles']++;
                $filePath = $file->getPathname();
                $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

                if (!$this->isKnownImageExtension($extension)) {
                    continue;
                }

                $stats['checkedImages']++;

                if (!$this->isSupportedExtension($extension)) {
                    $stats['unsupportedFormat']++;
                    $this->verbose("unsupported: {$filePath}");
                    continue;
                }

                if ($extension === 'webp' && !$this->supportsWebp()) {
                    $stats['unsupportedFormat']++;
                    $this->verbose("webp unsupported by GD: {$filePath}");
                    continue;
                }

                try {
                    $originalSize = $file->getSize();
                } catch (Throwable $e) {
                    $stats['errors']++;
                    $this->stderr("error: {$filePath}: {$e->getMessage()}\n");
                    continue;
                }

                if ($originalSize <= $minBytes) {
                    $stats['skippedSmall']++;
                    $this->verbose("small: {$filePath}");
                    continue;
                }

                if (!$this->dryRun && $this->limit > 0 && $stats['optimized'] >= $this->limit) {
                    $stats['stoppedByLimit'] = true;
                    break;
                }

                try {
                    $result = $this->optimizeFile($filePath, $extension, $originalSize);
                    $stats['bytesBefore'] += $originalSize;

                    if ($result['status'] === 'optimized') {
                        $stats['optimized']++;
                        $stats['bytesAfter'] += $result['newSize'];
                        $this->stdout("optimized: {$filePath} " . $this->formatBytes($originalSize) . ' -> ' . $this->formatBytes($result['newSize']) . "\n");
                    } elseif ($result['status'] === 'wouldOptimize') {
                        $stats['wouldOptimize']++;
                        $stats['bytesAfter'] += $result['newSize'];
                        $this->stdout("would optimize: {$filePath} " . $this->formatBytes($originalSize) . ' -> ' . $this->formatBytes($result['newSize']) . "\n");
                    } else {
                        $stats['skippedNotWorthIt']++;
                        $stats['bytesAfter'] += $originalSize;
                        $this->verbose("not worth replacing: {$filePath}");
                    }
                } catch (Throwable $e) {
                    $stats['errors']++;
                    $this->stderr("error: {$filePath}: {$e->getMessage()}\n");
                }
            }
        } catch (Throwable $e) {
            $stats['errors']++;
            $this->stderr("scan error: {$e->getMessage()}\n");
        } finally {
            $this->releaseLock();
        }

        $this->printSummary($stats);

        return $stats['errors'] > 0 ? ExitCode::UNSPECIFIED_ERROR : ExitCode::OK;
    }

    private function optimizeFile($filePath, $extension, $originalSize)
    {
        $image = $this->loadImage($filePath, $extension);
        if (!$image) {
            throw new \RuntimeException('cannot read image');
        }

        $tmpPath = $this->buildTemporaryPath($filePath);

        try {
            $image = $this->resizeImage($image, $extension);

            if (!$this->saveImage($image, $tmpPath, $extension)) {
                throw new \RuntimeException('cannot write optimized image');
            }
        } finally {
            if (is_resource($image) || $image instanceof \GdImage) {
                imagedestroy($image);
            }
        }

        clearstatcache(true, $tmpPath);
        $newSize = filesize($tmpPath);
        if ($newSize === false || $newSize <= 0) {
            @unlink($tmpPath);
            throw new \RuntimeException('optimized file is empty');
        }

        $savingRatio = ($originalSize - $newSize) / $originalSize;
        if ($newSize >= $originalSize || $savingRatio < self::MIN_SAVING_RATIO) {
            @unlink($tmpPath);
            return [
                'status' => 'notWorthIt',
                'newSize' => $newSize,
            ];
        }

        if ($this->dryRun) {
            @unlink($tmpPath);
            return [
                'status' => 'wouldOptimize',
                'newSize' => $newSize,
            ];
        }

        if (!$this->replaceOriginal($filePath, $tmpPath)) {
            @unlink($tmpPath);
            throw new \RuntimeException('cannot replace original file');
        }

        return [
            'status' => 'optimized',
            'newSize' => $newSize,
        ];
    }

    private function loadImage($filePath, $extension)
    {
        if ($extension === 'jpg' || $extension === 'jpeg') {
            $image = @imagecreatefromjpeg($filePath);
            return $this->applyJpegOrientation($image, $filePath);
        }

        if ($extension === 'png') {
            $image = @imagecreatefrompng($filePath);
            if ($image) {
                imagealphablending($image, false);
                imagesavealpha($image, true);
            }
            return $image;
        }

        if ($extension === 'webp' && $this->supportsWebp()) {
            $image = @imagecreatefromwebp($filePath);
            if ($image) {
                imagealphablending($image, false);
                imagesavealpha($image, true);
            }
            return $image;
        }

        return false;
    }

    private function applyJpegOrientation($image, $filePath)
    {
        if (!$image || !function_exists('exif_read_data')) {
            return $image;
        }

        $exif = @exif_read_data($filePath);
        if (empty($exif['Orientation'])) {
            return $image;
        }

        switch ((int)$exif['Orientation']) {
            case 2:
                imageflip($image, IMG_FLIP_HORIZONTAL);
                break;
            case 3:
                $image = imagerotate($image, 180, 0);
                break;
            case 4:
                imageflip($image, IMG_FLIP_VERTICAL);
                break;
            case 5:
                imageflip($image, IMG_FLIP_HORIZONTAL);
                $image = imagerotate($image, 270, 0);
                break;
            case 6:
                $image = imagerotate($image, 270, 0);
                break;
            case 7:
                imageflip($image, IMG_FLIP_HORIZONTAL);
                $image = imagerotate($image, 90, 0);
                break;
            case 8:
                $image = imagerotate($image, 90, 0);
                break;
        }

        return $image;
    }

    private function resizeImage($image, $extension)
    {
        $width = imagesx($image);
        $height = imagesy($image);

        if ($width <= 0 || $height <= 0) {
            throw new \RuntimeException('invalid image dimensions');
        }

        $scale = min($this->maxWidth / $width, $this->maxHeight / $height, 1);
        if ($scale >= 1) {
            return $image;
        }

        $targetWidth = max(1, (int)round($width * $scale));
        $targetHeight = max(1, (int)round($height * $scale));
        $resized = imagecreatetruecolor($targetWidth, $targetHeight);

        if ($extension === 'png' || $extension === 'webp') {
            imagealphablending($resized, false);
            imagesavealpha($resized, true);
            $transparent = imagecolorallocatealpha($resized, 0, 0, 0, 127);
            imagefilledrectangle($resized, 0, 0, $targetWidth, $targetHeight, $transparent);
        }

        if (!imagecopyresampled($resized, $image, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height)) {
            imagedestroy($resized);
            throw new \RuntimeException('cannot resize image');
        }

        imagedestroy($image);
        return $resized;
    }

    private function saveImage($image, $tmpPath, $extension)
    {
        if ($extension === 'jpg' || $extension === 'jpeg') {
            imageinterlace($image, true);
            return imagejpeg($image, $tmpPath, $this->quality);
        }

        if ($extension === 'png') {
            imagealphablending($image, false);
            imagesavealpha($image, true);
            return imagepng($image, $tmpPath, $this->pngCompression);
        }

        if ($extension === 'webp' && $this->supportsWebp()) {
            return imagewebp($image, $tmpPath, $this->quality);
        }

        return false;
    }

    private function replaceOriginal($filePath, $tmpPath)
    {
        $permissions = @fileperms($filePath);
        $mtime = @filemtime($filePath);

        if ($permissions !== false) {
            @chmod($tmpPath, $permissions & 0777);
        }
        if ($mtime !== false) {
            @touch($tmpPath, $mtime);
        }

        if (DIRECTORY_SEPARATOR === '\\') {
            $backupPath = $filePath . '.optimize-backup-' . getmypid() . '-' . uniqid('', true);
            if (!@rename($filePath, $backupPath)) {
                return false;
            }
            if (!@rename($tmpPath, $filePath)) {
                @rename($backupPath, $filePath);
                return false;
            }
            @unlink($backupPath);
            return true;
        }

        return @rename($tmpPath, $filePath);
    }

    private function acquireLock()
    {
        $lockPath = Yii::getAlias('@console/runtime/optimize-shop-images.lock');
        $lockDir = dirname($lockPath);
        if (!is_dir($lockDir) && !@mkdir($lockDir, 0775, true) && !is_dir($lockDir)) {
            $this->stderr("Cannot create lock directory: {$lockDir}\n");
            return false;
        }

        $this->lockHandle = @fopen($lockPath, 'c');
        if (!$this->lockHandle) {
            $this->stderr("Cannot open lock file: {$lockPath}\n");
            return false;
        }

        if (!flock($this->lockHandle, LOCK_EX | LOCK_NB)) {
            fclose($this->lockHandle);
            $this->lockHandle = null;
            return false;
        }

        ftruncate($this->lockHandle, 0);
        fwrite($this->lockHandle, getmypid() . PHP_EOL);

        return true;
    }

    private function releaseLock()
    {
        if ($this->lockHandle) {
            flock($this->lockHandle, LOCK_UN);
            fclose($this->lockHandle);
            $this->lockHandle = null;
        }
    }

    private function normalizeOptions()
    {
        $this->minSizeKb = max(1, (int)$this->minSizeKb);
        $this->maxWidth = max(1, (int)$this->maxWidth);
        $this->maxHeight = max(1, (int)$this->maxHeight);
        $this->quality = max(0, min(100, (int)$this->quality));
        $this->pngCompression = max(0, min(9, (int)$this->pngCompression));
        $this->dryRun = (int)$this->dryRun === 1;
        $this->limit = max(0, (int)$this->limit);
        $this->verbose = (int)$this->verbose === 1;
    }

    private function ensureMemoryLimit($minimum)
    {
        $current = ini_get('memory_limit');
        $currentBytes = $this->sizeToBytes($current);
        $minimumBytes = $this->sizeToBytes($minimum);

        if ($currentBytes !== -1 && $currentBytes < $minimumBytes) {
            @ini_set('memory_limit', $minimum);
        }
    }

    private function sizeToBytes($value)
    {
        $value = trim((string)$value);
        if ($value === '' || $value === '-1') {
            return -1;
        }

        $unit = strtolower(substr($value, -1));
        $number = (float)$value;

        switch ($unit) {
            case 'g':
                return (int)($number * 1024 * 1024 * 1024);
            case 'm':
                return (int)($number * 1024 * 1024);
            case 'k':
                return (int)($number * 1024);
            default:
                return (int)$number;
        }
    }

    private function buildTemporaryPath($filePath)
    {
        return $filePath . '.optimize-' . getmypid() . '-' . str_replace('.', '', uniqid('', true)) . '.tmp';
    }

    private function isKnownImageExtension($extension)
    {
        return in_array($extension, [
            'jpg',
            'jpeg',
            'png',
            'webp',
            'gif',
            'bmp',
            'tif',
            'tiff',
            'ico',
            'svg',
            'psd',
            'avif',
            'heic',
        ], true);
    }

    private function isSupportedExtension($extension)
    {
        return in_array($extension, ['jpg', 'jpeg', 'png', 'webp'], true);
    }

    private function supportsWebp()
    {
        return function_exists('imagecreatefromwebp') && function_exists('imagewebp');
    }

    private function createStats()
    {
        return [
            'scannedFiles' => 0,
            'checkedImages' => 0,
            'optimized' => 0,
            'wouldOptimize' => 0,
            'skippedSmall' => 0,
            'unsupportedFormat' => 0,
            'skippedNotWorthIt' => 0,
            'errors' => 0,
            'bytesBefore' => 0,
            'bytesAfter' => 0,
            'stoppedByLimit' => false,
        ];
    }

    private function printSummary(array $stats)
    {
        $saved = max(0, $stats['bytesBefore'] - $stats['bytesAfter']);

        $this->stdout("\nSummary\n");
        $this->stdout("Scanned files: {$stats['scannedFiles']}\n");
        $this->stdout("Checked image files: {$stats['checkedImages']}\n");
        $this->stdout("Optimized: {$stats['optimized']}\n");
        if ($this->dryRun) {
            $this->stdout("Would optimize: {$stats['wouldOptimize']}\n");
        }
        $this->stdout("Skipped below {$this->minSizeKb} KB: {$stats['skippedSmall']}\n");
        $this->stdout("Skipped unsupported format: {$stats['unsupportedFormat']}\n");
        $this->stdout("Skipped saving below 5%: {$stats['skippedNotWorthIt']}\n");
        $this->stdout("Errors: {$stats['errors']}\n");
        $this->stdout("Before: " . $this->formatBytes($stats['bytesBefore']) . "\n");
        $this->stdout("After: " . $this->formatBytes($stats['bytesAfter']) . "\n");
        $this->stdout("Saved: " . $this->formatBytes($saved) . "\n");
        if ($stats['stoppedByLimit']) {
            $this->stdout("Stopped by limit: {$this->limit}\n");
        }
    }

    private function formatBytes($bytes)
    {
        $bytes = (float)$bytes;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $index = 0;

        while ($bytes >= 1024 && $index < count($units) - 1) {
            $bytes /= 1024;
            $index++;
        }

        return sprintf($bytes >= 10 ? '%.1f %s' : '%.2f %s', $bytes, $units[$index]);
    }

    private function verbose($message)
    {
        if ($this->verbose) {
            $this->stdout($message . "\n");
        }
    }
}
