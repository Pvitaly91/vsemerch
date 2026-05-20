<?php

namespace common\services;

use common\models\Image;
use common\models\Product;
use Yii;

class ImagePathService
{
    public function getProductOriginalPath(Product $product, string $extension): string
    {
        return $this->join($this->getProductDir(), (int)$product->id . '.' . $this->normalizeExtension($extension));
    }

    public function getProductPreviewPath(Product $product, string $extension): string
    {
        return $this->join($this->getProductThumbDir(), 'preview_' . (int)$product->id . '.' . $this->normalizeExtension($extension));
    }

    public function getProductThumbPath(Product $product, string $extension): string
    {
        return $this->join($this->getProductThumbDir(), 'thumb_' . (int)$product->id . '.' . $this->normalizeExtension($extension));
    }

    public function getProductImageOriginalPath(Image $image, string $extension): string
    {
        return $this->join($this->getProductImageDir(), (int)$image->id . '.' . $this->normalizeExtension($extension));
    }

    public function getProductImageThumbPath(Image $image, string $extension): string
    {
        return $this->join($this->getProductImageThumbDir(), 'thumb_' . (int)$image->id . '.' . $this->normalizeExtension($extension));
    }

    public function getProductImageIcoPath(Image $image, string $extension): string
    {
        return $this->join($this->getProductImageThumbDir(), 'ico_' . (int)$image->id . '.' . $this->normalizeExtension($extension));
    }

    public function getProductDir(): string
    {
        return $this->alias('@frontend/web/upload/shop/products');
    }

    public function getProductThumbDir(): string
    {
        return $this->alias('@frontend/web/upload/shop/products/thumb');
    }

    public function getProductImageDir(): string
    {
        return $this->alias('@frontend/web/upload/shop/products/image');
    }

    public function getProductImageThumbDir(): string
    {
        return $this->alias('@frontend/web/upload/shop/products/image/thumb');
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
