<?php

namespace common\Helpers;

use Yii;

class ShopImageStorage
{
    const PARTNER_ENEY = 'eney';
    const PRODUCTS_URL = '/upload/shop/products';

    private static $partnerByProductId = [];

    public static function isPartitionedPartner($partner)
    {
        return self::normalizePartner($partner) === self::PARTNER_ENEY;
    }

    public static function normalizePartner($partner)
    {
        $partner = strtolower(trim((string)$partner));
        $partner = preg_replace('/[^a-z0-9_\-]/', '', $partner);

        return $partner === '' ? null : $partner;
    }

    public static function partnerForProductId($productId)
    {
        $productId = (int)$productId;
        if ($productId <= 0) {
            return null;
        }

        if (!array_key_exists($productId, self::$partnerByProductId)) {
            self::$partnerByProductId[$productId] = Yii::$app->db
                ->createCommand('SELECT [[partner]] FROM {{%shop_product}} WHERE [[id]] = :id', [':id' => $productId])
                ->queryScalar();
        }

        return self::$partnerByProductId[$productId];
    }

    public static function extensionFromImageValue($image)
    {
        $image = trim((string)$image);
        if ($image === '') {
            return null;
        }

        $path = parse_url($image, PHP_URL_PATH);
        if (!$path) {
            $path = $image;
        }

        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        return $extension ?: null;
    }

    public static function productImageUrl($productId, $extension, $partner = null)
    {
        return self::productBaseUrl($partner) . '/' . (int)$productId . '.' . strtolower($extension);
    }

    public static function productThumbUrl($productId, $extension, $profile, $partner = null)
    {
        return self::productBaseUrl($partner) . '/thumb/' . $profile . '_' . (int)$productId . '.' . strtolower($extension);
    }

    public static function galleryImageUrl($imageId, $extension, $partner = null)
    {
        return self::galleryBaseUrl($partner) . '/' . (int)$imageId . '.' . strtolower($extension);
    }

    public static function galleryThumbUrl($imageId, $extension, $profile, $partner = null)
    {
        return self::galleryBaseUrl($partner) . '/thumb/' . $profile . '_' . (int)$imageId . '.' . strtolower($extension);
    }

    public static function productImagePath($productId, $extension, $partner = null)
    {
        return self::urlToPath(self::productImageUrl($productId, $extension, $partner));
    }

    public static function productThumbPath($productId, $extension, $profile, $partner = null)
    {
        return self::urlToPath(self::productThumbUrl($productId, $extension, $profile, $partner));
    }

    public static function galleryImagePath($imageId, $extension, $partner = null)
    {
        return self::urlToPath(self::galleryImageUrl($imageId, $extension, $partner));
    }

    public static function galleryThumbPath($imageId, $extension, $profile, $partner = null)
    {
        return self::urlToPath(self::galleryThumbUrl($imageId, $extension, $profile, $partner));
    }

    public static function legacyProductImageUrl($productId, $extension)
    {
        return self::PRODUCTS_URL . '/' . (int)$productId . '.' . strtolower($extension);
    }

    public static function legacyProductThumbUrl($productId, $extension, $profile)
    {
        return self::PRODUCTS_URL . '/thumb/' . $profile . '_' . (int)$productId . '.' . strtolower($extension);
    }

    public static function legacyGalleryImageUrl($imageId, $extension)
    {
        return self::PRODUCTS_URL . '/image/' . (int)$imageId . '.' . strtolower($extension);
    }

    public static function legacyGalleryThumbUrl($imageId, $extension, $profile)
    {
        return self::PRODUCTS_URL . '/image/thumb/' . $profile . '_' . (int)$imageId . '.' . strtolower($extension);
    }

    public static function existingPartnerOrLegacyUrl($partnerUrl, $legacyUrl, $emptyUrl = null)
    {
        if (is_file(self::urlToPath($partnerUrl))) {
            return $partnerUrl;
        }

        if (is_file(self::urlToPath($legacyUrl))) {
            return $legacyUrl;
        }

        return $partnerUrl ?: $emptyUrl;
    }

    public static function legacyUrlWithPartnerFallback($legacyUrl, $partnerUrl = null, $emptyUrl = null)
    {
        if ($partnerUrl || is_file(self::urlToPath($legacyUrl))) {
            return $legacyUrl;
        }

        return $emptyUrl;
    }

    public static function urlToPath($url)
    {
        return Yii::getAlias('@frontend/web' . $url);
    }

    private static function productBaseUrl($partner)
    {
        $partner = self::normalizePartner($partner);
        if ($partner === self::PARTNER_ENEY) {
            return self::PRODUCTS_URL . '/' . $partner;
        }

        return self::PRODUCTS_URL;
    }

    private static function galleryBaseUrl($partner)
    {
        return self::productBaseUrl($partner) . '/image';
    }
}
