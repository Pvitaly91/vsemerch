<?php

namespace common\services;

use common\models\Image;
use common\models\Product;

class PartnerImageResolver
{
    public function resolveProduct(Product $product): ?string
    {
        $remote = $this->attributeValue($product, 'remote_image_url');
        if ($remote !== null) {
            return $remote;
        }

        return $this->remoteUrl((string)$product->image);
    }

    public function resolveProductImage(Image $image): ?string
    {
        $remote = $this->attributeValue($image, 'remote_image_url');
        if ($remote !== null) {
            return $remote;
        }

        return $this->remoteUrl((string)$image->image);
    }

    public function extensionFromUrl(?string $url): ?string
    {
        if ($url === null || trim($url) === '') {
            return null;
        }

        $path = parse_url($url, PHP_URL_PATH);
        if (!$path) {
            return null;
        }

        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if ($extension === 'jpe') {
            $extension = 'jpg';
        }

        return in_array($extension, ['jpg', 'jpeg', 'webp', 'png'], true) ? $extension : null;
    }

    private function attributeValue($model, string $attribute): ?string
    {
        if (!method_exists($model, 'hasAttribute') || !$model->hasAttribute($attribute)) {
            return null;
        }

        $value = trim((string)$model->getAttribute($attribute));
        return $this->remoteUrl($value);
    }

    private function remoteUrl(string $url): ?string
    {
        $url = trim($url);
        if ($url === '') {
            return null;
        }

        $scheme = strtolower((string)parse_url($url, PHP_URL_SCHEME));
        if (!in_array($scheme, ['http', 'https', 'ftp'], true)) {
            return null;
        }

        return $url;
    }
}
