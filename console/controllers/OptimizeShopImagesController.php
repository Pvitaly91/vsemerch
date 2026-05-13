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
    public $maxGrowthPercentForDimensions = 30;
    public $skipDimensionResizeBelowKb = 90;
    public $showSkipped = 1;
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
            'maxGrowthPercentForDimensions',
            'skipDimensionResizeBelowKb',
            'showSkipped',
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
        $skipDimensionResizeBelowBytes = $this->skipDimensionResizeBelowKb * 1024;

        $this->stdout("Scanning: {$rootPath}\n");
        $this->stdout("Minimum size: {$this->minSizeKb} KB, max dimensions: {$this->maxWidth}x{$this->maxHeight}, quality: {$this->quality}\n");
        $this->stdout("Skip dimension resize below: {$this->skipDimensionResizeBelowKb} KB\n");
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

                if ($extension === 'png') {
                    $stats['unsupportedFormat']++;
                    $this->skipped("png disabled: {$filePath}");
                    continue;
                }

                if (!$this->isSupportedExtension($extension)) {
                    $stats['unsupportedFormat']++;
                    $this->skipped("unsupported: {$filePath}");
                    continue;
                }

                if ($extension === 'webp' && !$this->supportsWebp()) {
                    $stats['unsupportedFormat']++;
                    $this->skipped("webp unsupported by GD: {$filePath}");
                    continue;
                }

                try {
                    $originalSize = $file->getSize();
                } catch (Throwable $e) {
                    $stats['errors']++;
                    $this->stderr("error: {$filePath}: {$e->getMessage()}\n");
                    continue;
                }

                $dimensions = $this->getImageDimensions($filePath);
                if ($dimensions === null) {
                    $stats['errors']++;
                    $this->stderr("error: {$filePath}: cannot read image dimensions\n");
                    continue;
                }

                if ($this->isTooLargeByDimensions($dimensions['width'], $dimensions['height']) && $originalSize < $skipDimensionResizeBelowBytes) {
                    $stats['skippedDimensionsBelowSizeLimit']++;
                    $this->skipped("skip dimensions below {$this->skipDimensionResizeBelowKb} KB: {$filePath} " . $this->formatBytes($originalSize) . ', ' . $this->formatDimensions($dimensions));
                    continue;
                }

                if (!$this->shouldOptimize($originalSize, $dimensions['width'], $dimensions['height'], $minBytes)) {
                    $stats['skippedSmallAndWithinDimensions']++;
                    $this->skipped("small and within dimensions: {$filePath} " . $this->formatBytes($originalSize) . ', ' . $this->formatDimensions($dimensions));
                    continue;
                }

                if (!$this->dryRun && $this->limit > 0 && $stats['optimized'] >= $this->limit) {
                    $stats['stoppedByLimit'] = true;
                    break;
                }

                try {
                    $result = $this->optimizeFile($filePath, $extension, $originalSize, $minBytes);
                    $stats['bytesBefore'] += $originalSize;

                    if ($result['status'] === 'optimized') {
                        $stats['optimized']++;
                        if ($result['optimizedByDimensions']) {
                            $stats['optimizedByDimensions']++;
                        }
                        $stats['bytesAfter'] += $result['newSize'];
                        $this->stdout(
                            "optimized: {$filePath} "
                            . $this->formatBytes($originalSize) . ' -> ' . $this->formatBytes($result['newSize'])
                            . ', ' . $this->formatDimensions($result['originalDimensions']) . ' -> ' . $this->formatDimensions($result['targetDimensions'])
                            . "\n"
                        );
                    } elseif ($result['status'] === 'wouldOptimize') {
                        $stats['wouldOptimize']++;
                        if ($result['optimizedByDimensions']) {
                            $stats['wouldOptimizeByDimensions']++;
                        }
                        $stats['bytesAfter'] += $result['newSize'];
                        $this->stdout(
                            "would optimize: {$filePath} "
                            . $this->formatBytes($originalSize) . ' -> ' . $this->formatBytes($result['newSize'])
                            . ', ' . $this->formatDimensions($result['originalDimensions']) . ' -> ' . $this->formatDimensions($result['targetDimensions'])
                            . "\n"
                        );
                    } elseif ($result['status'] === 'dimensionsGrowthLimit') {
                        $stats['skippedDimensionsGrowthLimit']++;
                        $stats['bytesAfter'] += $originalSize;
                        $this->stdout(
                            "skip dimensions growth: {$filePath} "
                            . $this->formatBytes($originalSize) . ' -> ' . $this->formatBytes($result['newSize'])
                            . ', ' . $this->formatDimensions($result['originalDimensions']) . ' -> ' . $this->formatDimensions($result['targetDimensions'])
                            . ', growth ' . sprintf('%.1f%%', $result['growthPercent'])
                            . "\n"
                        );
                    } else {
                        $stats['skippedNotWorthIt']++;
                        $stats['bytesAfter'] += $originalSize;
                        $this->skipped(
                            "not worth replacing: {$filePath} "
                            . $this->formatBytes($originalSize) . ' -> ' . $this->formatBytes($result['newSize'])
                            . ', ' . $this->formatDimensions($result['originalDimensions']) . ' -> ' . $this->formatDimensions($result['targetDimensions'])
                        );
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

    private function optimizeFile($filePath, $extension, $originalSize, $minBytes)
    {
        $image = $this->loadImage($filePath, $extension);
        if (!$image) {
            throw new \RuntimeException('cannot read image');
        }

        $tmpPath = $this->buildTemporaryPath($filePath);

        try {
            $resizeInfo = $this->resizeImage($image, $extension);
            $image = $resizeInfo['image'];

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
        $wasTooLargeBySize = $originalSize > $minBytes;
        $wasTooLargeByDimensions = $resizeInfo['originalWidth'] > $this->maxWidth || $resizeInfo['originalHeight'] > $this->maxHeight;
        $canReplaceBySaving = $wasTooLargeBySize && $newSize < $originalSize && $savingRatio >= self::MIN_SAVING_RATIO;
        $canReplaceByDimensionsShape = $wasTooLargeByDimensions
            && $resizeInfo['resized']
            && ($resizeInfo['targetWidth'] < $resizeInfo['originalWidth'] || $resizeInfo['targetHeight'] < $resizeInfo['originalHeight'])
            && $resizeInfo['targetWidth'] <= $this->maxWidth
            && $resizeInfo['targetHeight'] <= $this->maxHeight;
        $growthLimit = $originalSize * (1 + ($this->maxGrowthPercentForDimensions / 100));
        $canReplaceByDimensions = $canReplaceByDimensionsShape && $newSize <= $growthLimit;

        $commonResult = [
            'newSize' => $newSize,
            'optimizedByDimensions' => false,
            'growthPercent' => $this->calculateGrowthPercent($originalSize, $newSize),
            'originalDimensions' => [
                'width' => $resizeInfo['originalWidth'],
                'height' => $resizeInfo['originalHeight'],
            ],
            'targetDimensions' => [
                'width' => $resizeInfo['targetWidth'],
                'height' => $resizeInfo['targetHeight'],
            ],
        ];

        if (!$canReplaceBySaving && $canReplaceByDimensionsShape && !$canReplaceByDimensions) {
            @unlink($tmpPath);
            $commonResult['status'] = 'dimensionsGrowthLimit';
            return $commonResult;
        }

        if (!$canReplaceBySaving && !$canReplaceByDimensions) {
            @unlink($tmpPath);
            $commonResult['status'] = 'notWorthIt';
            return $commonResult;
        }

        $optimizedByDimensions = $canReplaceByDimensions;
        $result = $commonResult;
        $result['optimizedByDimensions'] = $optimizedByDimensions;

        if ($this->dryRun) {
            @unlink($tmpPath);
            $result['status'] = 'wouldOptimize';
            return $result;
        }

        if (!$this->replaceOriginal($filePath, $tmpPath)) {
            @unlink($tmpPath);
            throw new \RuntimeException('cannot replace original file');
        }

        $result['status'] = 'optimized';
        return $result;
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

        $targetDimensions = $this->calculateTargetDimensions($width, $height);
        $info = [
            'image' => $image,
            'originalWidth' => $width,
            'originalHeight' => $height,
            'targetWidth' => $targetDimensions['width'],
            'targetHeight' => $targetDimensions['height'],
            'resized' => $targetDimensions['resized'],
        ];

        if (!$targetDimensions['resized']) {
            return $info;
        }

        $targetWidth = $targetDimensions['width'];
        $targetHeight = $targetDimensions['height'];
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
        $info['image'] = $resized;

        return $info;
    }

    private function calculateTargetDimensions($width, $height)
    {
        $scale = min($this->maxWidth / $width, $this->maxHeight / $height, 1);

        return [
            'width' => max(1, (int)round($width * $scale)),
            'height' => max(1, (int)round($height * $scale)),
            'resized' => $scale < 1,
        ];
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
        $this->maxGrowthPercentForDimensions = max(0, (float)$this->maxGrowthPercentForDimensions);
        $this->skipDimensionResizeBelowKb = max(0, (int)$this->skipDimensionResizeBelowKb);
        $this->showSkipped = (int)$this->showSkipped === 1;
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

    private function getImageDimensions($filePath)
    {
        $info = @getimagesize($filePath);
        if (!$info || empty($info[0]) || empty($info[1])) {
            return null;
        }

        return [
            'width' => (int)$info[0],
            'height' => (int)$info[1],
        ];
    }

    private function shouldOptimize($originalSize, $width, $height, $minBytes)
    {
        return $originalSize > $minBytes
            || $this->isTooLargeByDimensions($width, $height);
    }

    private function isTooLargeByDimensions($width, $height)
    {
        return $width > $this->maxWidth || $height > $this->maxHeight;
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
        return in_array($extension, ['jpg', 'jpeg', 'webp'], true);
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
            'optimizedByDimensions' => 0,
            'wouldOptimizeByDimensions' => 0,
            'skippedSmallAndWithinDimensions' => 0,
            'skippedDimensionsBelowSizeLimit' => 0,
            'skippedDimensionsGrowthLimit' => 0,
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
            $this->stdout("Would optimize because dimensions exceeded limit: {$stats['wouldOptimizeByDimensions']}\n");
        }
        $this->stdout("Optimized because dimensions exceeded limit: {$stats['optimizedByDimensions']}\n");
        $this->stdout("Skipped below size and within dimensions: {$stats['skippedSmallAndWithinDimensions']}\n");
        $this->stdout("Skipped dimensions below {$this->skipDimensionResizeBelowKb} KB: {$stats['skippedDimensionsBelowSizeLimit']}\n");
        $this->stdout("Skipped by dimensions growth limit: {$stats['skippedDimensionsGrowthLimit']}\n");
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

    private function formatDimensions(array $dimensions)
    {
        return $dimensions['width'] . 'x' . $dimensions['height'];
    }

    private function calculateGrowthPercent($originalSize, $newSize)
    {
        if ($originalSize <= 0) {
            return 0;
        }

        return (($newSize - $originalSize) / $originalSize) * 100;
    }

    private function verbose($message)
    {
        if ($this->verbose) {
            $this->stdout($message . "\n");
        }
    }

    private function skipped($message)
    {
        if ($this->showSkipped || $this->verbose) {
            $this->stdout($message . "\n");
        }
    }
}
