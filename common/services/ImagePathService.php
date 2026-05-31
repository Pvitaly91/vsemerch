<?php

namespace common\services;

use common\Helpers\ShopImageStorage;
use common\models\Image;
use common\models\Product;
use Yii;

class ImagePathService
{
    public function getProductOriginalPath(Product $product, string $extension): string
    {
        return $this->normalizePath(ShopImageStorage::productImagePath(
            $product->id,
            $this->normalizeExtension($extension),
            $product->partner
        ));
    }

    public function getProductPreviewPath(Product $product, string $extension): string
    {
        return $this->normalizePath(ShopImageStorage::productThumbPath(
            $product->id,
            $this->normalizeExtension($extension),
            'preview',
            $product->partner
        ));
    }

    public function getProductThumbPath(Product $product, string $extension): string
    {
        return $this->normalizePath(ShopImageStorage::productThumbPath(
            $product->id,
            $this->normalizeExtension($extension),
            'thumb',
            $product->partner
        ));
    }

    public function getProductImageOriginalPath(Image $image, string $extension): string
    {
        return $this->normalizePath(ShopImageStorage::galleryImagePath(
            $image->id,
            $this->normalizeExtension($extension),
            $this->partnerForImage($image)
        ));
    }

    public function getProductImageThumbPath(Image $image, string $extension): string
    {
        return $this->normalizePath(ShopImageStorage::galleryThumbPath(
            $image->id,
            $this->normalizeExtension($extension),
            'thumb',
            $this->partnerForImage($image)
        ));
    }

    public function getProductImageIcoPath(Image $image, string $extension): string
    {
        return $this->normalizePath(ShopImageStorage::galleryThumbPath(
            $image->id,
            $this->normalizeExtension($extension),
            'ico',
            $this->partnerForImage($image)
        ));
    }

    public function getProductDir(Product $product = null): string
    {
        if ($product !== null) {
            return dirname($this->getProductOriginalPath($product, 'jpg'));
        }

        return $this->getProductBaseDir();
    }

    public function getProductThumbDir(Product $product = null): string
    {
        if ($product !== null) {
            return dirname($this->getProductThumbPath($product, 'jpg'));
        }

        return $this->join($this->getProductBaseDir(), 'thumb');
    }

    public function getProductImageDir(Image $image = null): string
    {
        if ($image !== null) {
            return dirname($this->getProductImageOriginalPath($image, 'jpg'));
        }

        return $this->getProductImageBaseDir();
    }

    public function getProductImageThumbDir(Image $image = null): string
    {
        if ($image !== null) {
            return dirname($this->getProductImageThumbPath($image, 'jpg'));
        }

        return $this->join($this->getProductImageBaseDir(), 'thumb');
    }

    public function getProductBaseDir(): string
    {
        return $this->alias('@frontend/web/upload/shop/products');
    }

    public function getProductImageBaseDir(): string
    {
        return $this->alias('@frontend/web/upload/shop/products/image');
    }

    public function getProductProfilePath(Product $product, string $profile, string $extension): string
    {
        if ($profile === 'original') {
            return $this->getProductOriginalPath($product, $extension);
        }
        if ($profile === 'preview') {
            return $this->getProductPreviewPath($product, $extension);
        }
        if ($profile === 'thumb') {
            return $this->getProductThumbPath($product, $extension);
        }

        throw new \InvalidArgumentException('Unsupported product image profile: ' . $profile);
    }

    public function getProductImageProfilePath(Image $image, string $profile, string $extension): string
    {
        if ($profile === 'original') {
            return $this->getProductImageOriginalPath($image, $extension);
        }
        if ($profile === 'thumb') {
            return $this->getProductImageThumbPath($image, $extension);
        }
        if ($profile === 'ico') {
            return $this->getProductImageIcoPath($image, $extension);
        }

        throw new \InvalidArgumentException('Unsupported product-image profile: ' . $profile);
    }

    public function normalizePath(string $path): string
    {
        return str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
    }

    public function isInside(string $path, string $baseDir): bool
    {
        $path = $this->normalizeForCompare($path);
        $baseDir = rtrim($this->normalizeForCompare($baseDir), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        return strpos($path, $baseDir) === 0;
    }

    private function alias(string $alias): string
    {
        return $this->normalizePath(Yii::getAlias($alias));
    }

    private function join(string $dir, string $file): string
    {
        return rtrim($this->normalizePath($dir), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $file;
    }

    private function normalizeExtension(string $extension): string
    {
        $extension = strtolower(trim($extension));
        return ltrim($extension, '.');
    }

    private function partnerForImage(Image $image)
    {
        if ($image->product) {
            return $image->product->partner;
        }

        return ShopImageStorage::partnerForProductId($image->product_id);
    }

    private function normalizeForCompare(string $path): string
    {
        $path = $this->normalizePath($path);
        $real = realpath($path);
        if ($real !== false) {
            $path = $real;
        }

        $path = rtrim($path, DIRECTORY_SEPARATOR);
        if (DIRECTORY_SEPARATOR === '\\') {
            $path = strtolower($path);
        }

        return $path;
    }
}
