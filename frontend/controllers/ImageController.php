<?php

namespace frontend\controllers;

use common\models\Image as ProductImage;
use common\models\Product;
use common\services\ImageCacheProcessService;
use common\services\ImagePathService;
use common\services\PartnerImageResolver;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class ImageController extends Controller
{
    private const EXTENSIONS = ['jpg', 'jpeg', 'webp', 'png'];
    private const PRODUCT_PROFILES = ['original', 'preview', 'thumb'];
    private const PRODUCT_IMAGE_PROFILES = ['original', 'thumb', 'ico'];
    private const LOCAL_CACHE_TTL = 2592000;
    private const REMOTE_CACHE_TTL = 3600;
    private const NO_IMAGE_CACHE_TTL = 86400;
    private const MAX_REMOTE_BYTES = 31457280;

    private $paths;
    private $resolver;
    private $process;

    public function init()
    {
        parent::init();

        $this->paths = new ImagePathService();
        $this->resolver = new PartnerImageResolver();
        $this->process = new ImageCacheProcessService();
    }

    public function actionProduct($id, $profile = 'preview')
    {
        $profile = $this->normalizeProfile($profile, self::PRODUCT_PROFILES);
        $product = Product::findOne((int)$id);
        if ($product === null) {
            throw new NotFoundHttpException('Image not found.');
        }

        $localPath = $this->findProductLocalPath($product, $profile);
        if ($localPath !== null) {
            return $this->sendLocalImage($localPath);
        }

        $remoteUrl = $this->resolver->resolveProduct($product);
        $originalPath = $this->findProductLocalPath($product, 'original');
        if ($originalPath !== null) {
            $this->queueProductCache($product, $profile, $remoteUrl, $this->extensionFromPath($originalPath));
            return $this->sendLocalImage($originalPath);
        }

        if ($remoteUrl === null) {
            return $this->sendNoImage();
        }

        $this->queueProductCache($product, $profile, $remoteUrl, $this->targetExtension($remoteUrl));
        return $this->proxyRemoteImage($remoteUrl, 'product', (int)$product->id, $profile);
    }

    public function actionProductImage($id, $profile = 'thumb')
    {
        $profile = $this->normalizeProfile($profile, self::PRODUCT_IMAGE_PROFILES);
        $image = ProductImage::findOne((int)$id);
        if ($image === null) {
            throw new NotFoundHttpException('Image not found.');
        }

        $localPath = $this->findProductImageLocalPath($image, $profile);
        if ($localPath !== null) {
            return $this->sendLocalImage($localPath);
        }

        $remoteUrl = $this->resolver->resolveProductImage($image);
        $originalPath = $this->findProductImageLocalPath($image, 'original');
        if ($originalPath !== null) {
            $this->queueProductImageCache($image, $profile, $remoteUrl, $this->extensionFromPath($originalPath));
            return $this->sendLocalImage($originalPath);
        }

        if ($remoteUrl === null) {
            return $this->sendNoImage();
        }

        $this->queueProductImageCache($image, $profile, $remoteUrl, $this->targetExtension($remoteUrl));
        return $this->proxyRemoteImage($remoteUrl, 'product-image', (int)$image->id, $profile);
    }

    private function findProductLocalPath(Product $product, string $profile): ?string
    {
        foreach (self::EXTENSIONS as $extension) {
            $path = $this->paths->getProductProfilePath($product, $profile, $extension);
            if (is_file($path)) {
                return $path;
            }
        }

        return null;
    }

    private function findProductImageLocalPath(ProductImage $image, string $profile): ?string
    {
        foreach (self::EXTENSIONS as $extension) {
            $path = $this->paths->getProductImageProfilePath($image, $profile, $extension);
            if (is_file($path)) {
                return $path;
            }
        }

        return null;
    }

    private function queueProductCache(Product $product, string $profile, ?string $remoteUrl, string $extension): void
    {
        if ($remoteUrl === null || $this->process->isFreshFailed('product', (int)$product->id, $profile)) {
            return;
        }

        $target = $this->paths->getProductOriginalPath($product, $extension);
        if (!$this->paths->isInside($target, $this->paths->getProductDir())) {
            Yii::warning('Refusing image cache target outside products dir: ' . $target, __METHOD__);
            return;
        }

        $this->queueCacheOne(
            'product',
            (int)$product->id,
            $profile,
            $remoteUrl,
            $target,
            $this->paths->getProductThumbDir(),
            'thumb:300x300,preview:400x400'
        );
    }

    private function queueProductImageCache(ProductImage $image, string $profile, ?string $remoteUrl, string $extension): void
    {
        if ($remoteUrl === null || $this->process->isFreshFailed('product-image', (int)$image->id, $profile)) {
            return;
        }

        $target = $this->paths->getProductImageOriginalPath($image, $extension);
        if (!$this->paths->isInside($target, $this->paths->getProductImageDir())) {
            Yii::warning('Refusing image cache target outside product-image dir: ' . $target, __METHOD__);
            return;
        }

        $this->queueCacheOne(
            'product-image',
            (int)$image->id,
            $profile,
            $remoteUrl,
            $target,
            $this->paths->getProductImageThumbDir(),
            'thumb:400x400,ico:100x100'
        );
    }

    private function queueCacheOne(
        string $entity,
        int $id,
        string $profile,
        string $remoteUrl,
        string $target,
        string $thumbDir,
        string $profiles
    ): void {
        if (!$this->isAllowedRemoteUrl($remoteUrl)) {
            Yii::warning('Refusing image cache remote host: ' . $remoteUrl, __METHOD__);
            return;
        }

        $lock = $this->process->createLock($entity, $id, $profile, $remoteUrl, $target);
        if ($lock === null) {
            return;
        }

        $started = $this->process->runCacheOne(
            $remoteUrl,
            $target,
            $thumbDir,
            $profiles,
            $lock,
            $this->process->getLogPath($entity, $id, $profile),
            $this->process->getFailedPath($entity, $id, $profile)
        );

        if (!$started) {
            $this->process->releaseLock($lock);
        }
    }

    private function proxyRemoteImage(string $remoteUrl, string $entity, int $id, string $profile)
    {
        $remote = $this->fetchRemoteImage($remoteUrl);
        if ($remote === null) {
            $this->process->markFailed($entity, $id, $profile, 'Remote proxy failed.');
            return $this->sendNoImage();
        }

        $response = Yii::$app->response;
        $response->format = Response::FORMAT_RAW;
        $response->headers->set('Content-Type', $remote['mime']);
        $response->headers->set('Cache-Control', 'public, max-age=' . self::REMOTE_CACHE_TTL);
        $response->headers->set('Content-Length', (string)strlen($remote['body']));
        $response->content = $remote['body'];

        return $response;
    }

    private function fetchRemoteImage(string $url, int $redirects = 0): ?array
    {
        if ($redirects > 3 || !$this->isAllowedRemoteUrl($url) || !function_exists('curl_init')) {
            return null;
        }

        $headers = [
            'contentType' => null,
            'location' => null,
        ];
        $body = '';
        $tooLarge = false;

        $ch = curl_init($url);
        $options = [
            CURLOPT_RETURNTRANSFER => false,
            CURLOPT_HEADER => false,
            CURLOPT_CONNECTTIMEOUT => 3,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_USERAGENT => 'vsemerch-image-cache/1.0',
            CURLOPT_HEADERFUNCTION => function ($ch, $header) use (&$headers) {
                $length = strlen($header);
                $parts = explode(':', $header, 2);
                if (count($parts) === 2) {
                    $name = strtolower(trim($parts[0]));
                    $value = trim($parts[1]);
                    if ($name === 'content-type') {
                        $headers['contentType'] = $value;
                    } elseif ($name === 'location') {
                        $headers['location'] = $value;
                    }
                }

                return $length;
            },
            CURLOPT_WRITEFUNCTION => function ($ch, $chunk) use (&$body, &$tooLarge) {
                $length = strlen($chunk);
                if (strlen($body) + $length > self::MAX_REMOTE_BYTES) {
                    $tooLarge = true;
                    return 0;
                }

                $body .= $chunk;
                return $length;
            },
        ];

        if (defined('CURLOPT_PROTOCOLS')) {
            $protocols = 0;
            foreach (['CURLPROTO_HTTP', 'CURLPROTO_HTTPS', 'CURLPROTO_FTP'] as $constant) {
                if (defined($constant)) {
                    $protocols |= constant($constant);
                }
            }
            if ($protocols !== 0) {
                $options[CURLOPT_PROTOCOLS] = $protocols;
            }
        }

        curl_setopt_array($ch, $options);
        $ok = curl_exec($ch);
        $httpCode = (int)curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        curl_close($ch);

        if ($tooLarge || $ok === false) {
            return null;
        }

        if ($httpCode >= 300 && $httpCode < 400 && !empty($headers['location'])) {
            $redirectUrl = $this->resolveRedirectUrl($url, $headers['location']);
            return $redirectUrl === null ? null : $this->fetchRemoteImage($redirectUrl, $redirects + 1);
        }

        if ($httpCode !== 0 && ($httpCode < 200 || $httpCode >= 300)) {
            return null;
        }

        $mime = $this->normalizeMime($headers['contentType']);
        if ($headers['contentType'] !== null && strpos($mime ?? '', 'image/') !== 0) {
            return null;
        }

        if ($mime === null) {
            $mime = $this->detectMimeFromBuffer($body);
        }

        if ($mime === null || strpos($mime, 'image/') !== 0) {
            return null;
        }

        return [
            'body' => $body,
            'mime' => $mime,
        ];
    }

    private function sendLocalImage(string $path)
    {
        $response = Yii::$app->response;
        $response->headers->set('Cache-Control', 'public, max-age=' . self::LOCAL_CACHE_TTL);

        return $response->sendFile($path, basename($path), [
            'inline' => true,
            'mimeType' => $this->mimeType($path),
        ]);
    }

    private function sendNoImage()
    {
        $path = $this->paths->normalizePath(Yii::getAlias('@frontend/web/img/no_image.jpg'));
        if (!is_file($path)) {
            throw new NotFoundHttpException('Image not found.');
        }

        $response = Yii::$app->response;
        $response->headers->set('Cache-Control', 'public, max-age=' . self::NO_IMAGE_CACHE_TTL);

        return $response->sendFile($path, basename($path), [
            'inline' => true,
            'mimeType' => $this->mimeType($path),
        ]);
    }

    private function normalizeProfile(string $profile, array $allowed): string
    {
        $profile = strtolower(trim($profile));
        if (!in_array($profile, $allowed, true)) {
            throw new NotFoundHttpException('Image not found.');
        }

        return $profile;
    }

    private function targetExtension(string $remoteUrl): string
    {
        return $this->resolver->extensionFromUrl($remoteUrl) ?: 'jpg';
    }

    private function extensionFromPath(string $path): string
    {
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        return in_array($extension, self::EXTENSIONS, true) ? $extension : 'jpg';
    }

    private function isAllowedRemoteUrl(string $url): bool
    {
        $scheme = strtolower((string)parse_url($url, PHP_URL_SCHEME));
        if (!in_array($scheme, ['http', 'https', 'ftp'], true)) {
            return false;
        }

        $host = strtolower((string)parse_url($url, PHP_URL_HOST));
        if ($host === '') {
            return false;
        }

        $allowedHosts = Yii::$app->params['imageCacheAllowedHosts'] ?? [];
        foreach ($allowedHosts as $allowedHost) {
            $allowedHost = strtolower(trim((string)$allowedHost));
            if ($allowedHost === '') {
                continue;
            }
            if ($host === $allowedHost) {
                return true;
            }
            if (strpos($allowedHost, '*.') === 0 && substr($host, -strlen(substr($allowedHost, 1))) === substr($allowedHost, 1)) {
                return true;
            }
        }

        return false;
    }

    private function resolveRedirectUrl(string $currentUrl, string $location): ?string
    {
        $location = trim($location);
        if ($location === '') {
            return null;
        }

        if (parse_url($location, PHP_URL_SCHEME)) {
            return $location;
        }

        $parts = parse_url($currentUrl);
        if (empty($parts['scheme']) || empty($parts['host'])) {
            return null;
        }

        $port = isset($parts['port']) ? ':' . $parts['port'] : '';
        $base = $parts['scheme'] . '://' . $parts['host'] . $port;
        if (strpos($location, '/') === 0) {
            return $base . $location;
        }

        $path = $parts['path'] ?? '/';
        $dir = rtrim(str_replace('\\', '/', dirname($path)), '/');

        return $base . ($dir === '' ? '' : $dir) . '/' . $location;
    }

    private function normalizeMime(?string $contentType): ?string
    {
        if ($contentType === null) {
            return null;
        }

        $mime = strtolower(trim(explode(';', $contentType, 2)[0]));
        return $mime === '' ? null : $mime;
    }

    private function detectMimeFromBuffer(string $body): ?string
    {
        if ($body === '' || !function_exists('finfo_open')) {
            return null;
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        if ($finfo === false) {
            return null;
        }

        $mime = finfo_buffer($finfo, $body);
        finfo_close($finfo);

        return $mime ? strtolower($mime) : null;
    }

    private function mimeType(string $path): string
    {
        $mimeType = @mime_content_type($path);
        if ($mimeType) {
            return $mimeType;
        }

        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $map = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'webp' => 'image/webp',
        ];

        return $map[$extension] ?? 'application/octet-stream';
    }
}
