<?php

namespace common\services;

use Yii;
use yii\helpers\FileHelper;

class ImageCacheProcessService
{
    const LOCK_TTL = 1800;
    const FAILED_TTL = 3600;

    public function getBinaryPath(): ?string
    {
        $root = $this->projectRoot();
        $relative = $this->isWindows()
            ? ['tools', 'image-optimizer-cpp', 'build', 'Release', 'vsemerch-image-optimizer.exe']
            : ['tools', 'image-optimizer-cpp', 'build', 'vsemerch-image-optimizer'];

        $path = $root;
        foreach ($relative as $part) {
            $path .= DIRECTORY_SEPARATOR . $part;
        }

        $path = $this->normalizePath($path);
        if (!is_file($path)) {
            return null;
        }

        $cacheOneSource = $this->normalizePath($root . DIRECTORY_SEPARATOR . 'tools' . DIRECTORY_SEPARATOR . 'image-optimizer-cpp' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'CacheOne.cpp');
        if (is_file($cacheOneSource) && @filemtime($path) < @filemtime($cacheOneSource)) {
            Yii::warning('Image cache binary is older than cache-one source; rebuild tools/image-optimizer-cpp before enabling lazy cache.', __METHOD__);
            return null;
        }

        return $path;
    }

    public function runCacheOne(
        string $remoteUrl,
        string $target,
        string $thumbDir,
        string $profiles,
        string $lock,
        string $log,
        ?string $failed = null,
        int $maxWidth = 1200,
        int $maxHeight = 1200,
        int $quality = 75
    ): bool {
        $binary = $this->getBinaryPath();
        if ($binary === null) {
            Yii::warning('Image cache binary was not found; remote image will be proxied only.', __METHOD__);
            return false;
        }

        if ($this->isLinux()) {
            @chmod($binary, 0755);
        }

        $this->ensureDirectory(dirname($log));
        if ($failed !== null) {
            $this->ensureDirectory(dirname($failed));
        }

        $args = [
            $binary,
            'cache-one',
            '--remote-url',
            $remoteUrl,
            '--target',
            $target,
            '--thumb-dir',
            $thumbDir,
            '--profiles',
            $profiles,
            '--max-width',
            (string)$maxWidth,
            '--max-height',
            (string)$maxHeight,
            '--quality',
            (string)$quality,
            '--lock',
            $lock,
            '--log',
            $log,
        ];

        if ($failed !== null) {
            $args[] = '--failed';
            $args[] = $failed;
        }

        $command = $this->escapeCommand($args);
        if ($this->isWindows()) {
            $command = 'start /B "" ' . $command . ' > NUL 2>&1';
        } else {
            $command = 'nohup ' . $command . ' > /dev/null 2>&1 &';
        }

        $handle = @popen($command, 'r');
        if ($handle === false) {
            Yii::warning('Cannot start image cache process.', __METHOD__);
            return false;
        }

        @pclose($handle);
        return true;
    }

    public function isWindows(): bool
    {
        return DIRECTORY_SEPARATOR === '\\';
    }

    public function isLinux(): bool
    {
        return PHP_OS_FAMILY === 'Linux';
    }

    public function getLockPath(string $entity, int $id, string $profile): string
    {
        return $this->runtimePath('locks', $this->markerName($entity, $id, $profile, 'lock'));
    }

    public function getFailedPath(string $entity, int $id, string $profile): string
    {
        return $this->runtimePath('failed', $this->markerName($entity, $id, $profile, 'failed'));
    }

    public function getLogPath(string $entity, int $id, string $profile): string
    {
        return $this->runtimePath('logs', $this->markerName($entity, $id, $profile, 'log'));
    }

    public function createLock(string $entity, int $id, string $profile, string $remoteUrl, string $target): ?string
    {
        $lockPath = $this->getLockPath($entity, $id, $profile);
        $this->ensureDirectory(dirname($lockPath));
        $this->removeStaleLock($lockPath);

        $handle = @fopen($lockPath, 'x');
        if ($handle === false) {
            return null;
        }

        $payload = [
            'created_at' => time(),
            'entity' => $entity,
            'id' => $id,
            'profile' => $profile,
            'remote_url' => $remoteUrl,
            'target' => $target,
        ];

        fwrite($handle, json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        fclose($handle);

        return $lockPath;
    }

    public function isFreshFailed(string $entity, int $id, string $profile, int $ttl = self::FAILED_TTL): bool
    {
        $path = $this->getFailedPath($entity, $id, $profile);
        if (!is_file($path)) {
            return false;
        }

        $mtime = @filemtime($path);
        if ($mtime !== false && time() - $mtime < $ttl) {
            return true;
        }

        @unlink($path);
        return false;
    }

    public function markFailed(string $entity, int $id, string $profile, string $message): void
    {
        $path = $this->getFailedPath($entity, $id, $profile);
        $this->ensureDirectory(dirname($path));
        $payload = [
            'created_at' => time(),
            'entity' => $entity,
            'id' => $id,
            'profile' => $profile,
            'message' => $message,
        ];

        @file_put_contents($path, json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    }

    public function releaseLock(string $lockPath): void
    {
        @unlink($lockPath);
    }

    public function isRuntimePath(string $path): bool
    {
        return $this->isInside($path, $this->runtimeDir());
    }

    private function removeStaleLock(string $lockPath): void
    {
        if (!is_file($lockPath)) {
            return;
        }

        $mtime = @filemtime($lockPath);
        if ($mtime !== false && time() - $mtime > self::LOCK_TTL) {
            @unlink($lockPath);
        }
    }

    private function runtimePath(string $type, string $file): string
    {
        $path = $this->normalizePath($this->runtimeDir() . DIRECTORY_SEPARATOR . $type . DIRECTORY_SEPARATOR . $file);
        if (!$this->isRuntimePath($path)) {
            throw new \RuntimeException('Invalid image cache runtime path.');
        }

        return $path;
    }

    private function runtimeDir(): string
    {
        return $this->normalizePath(Yii::getAlias('@frontend/runtime/image-cache'));
    }

    private function projectRoot(): string
    {
        return $this->normalizePath(dirname(Yii::getAlias('@frontend')));
    }

    private function markerName(string $entity, int $id, string $profile, string $extension): string
    {
        $entity = preg_replace('/[^a-z0-9\-]/i', '', $entity);
        $profile = preg_replace('/[^a-z0-9\-]/i', '', $profile);

        return $entity . '-' . (int)$id . '-' . $profile . '.' . $extension;
    }

    private function ensureDirectory(string $dir): void
    {
        if (!is_dir($dir)) {
            FileHelper::createDirectory($dir, 0775, true);
        }
    }

    private function escapeCommand(array $args): string
    {
        return implode(' ', array_map('escapeshellarg', $args));
    }

    private function normalizePath(string $path): string
    {
        return str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
    }

    private function isInside(string $path, string $baseDir): bool
    {
        $path = $this->normalizeForCompare($path);
        $baseDir = rtrim($this->normalizeForCompare($baseDir), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        return strpos($path, $baseDir) === 0;
    }

    private function normalizeForCompare(string $path): string
    {
        $path = $this->normalizePath($path);
        $real = realpath($path);
        if ($real !== false) {
            $path = $real;
        }

        $path = rtrim($path, DIRECTORY_SEPARATOR);
        if ($this->isWindows()) {
            $path = strtolower($path);
        }

        return $path;
    }
}
