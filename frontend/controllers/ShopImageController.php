<?php

namespace frontend\controllers;

use common\Helpers\ShopImageStorage;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class ShopImageController extends Controller
{
    public function actionProduct($file, $partner = null)
    {
        [$id, $extension] = $this->parseIdFile($file);
        $partner = $this->productPartner($id);

        return $this->sendFirstExistingFile($this->storagePaths($partner,
            ShopImageStorage::productImagePath($id, $extension, $partner),
            ShopImageStorage::urlToPath(ShopImageStorage::legacyProductImageUrl($id, $extension))
        ), function () use ($id) {
            return Yii::$app->runAction('image/product', ['id' => $id, 'profile' => 'original']);
        });
    }

    public function actionProductThumb($file, $partner = null)
    {
        [$profile, $id, $extension] = $this->parseThumbFile($file, ['thumb', 'preview']);
        $partner = $this->productPartner($id);

        return $this->sendFirstExistingFile($this->storagePaths($partner,
            ShopImageStorage::productThumbPath($id, $extension, $profile, $partner),
            ShopImageStorage::urlToPath(ShopImageStorage::legacyProductThumbUrl($id, $extension, $profile))
        ), function () use ($id, $profile) {
            return Yii::$app->runAction('image/product', ['id' => $id, 'profile' => $profile]);
        });
    }

    public function actionGallery($file, $partner = null)
    {
        [$id, $extension] = $this->parseIdFile($file);
        $partner = $this->galleryPartner($id);

        return $this->sendFirstExistingFile($this->storagePaths($partner,
            ShopImageStorage::galleryImagePath($id, $extension, $partner),
            ShopImageStorage::urlToPath(ShopImageStorage::legacyGalleryImageUrl($id, $extension))
        ), function () use ($id) {
            return Yii::$app->runAction('image/product-image', ['id' => $id, 'profile' => 'original']);
        });
    }

    public function actionGalleryThumb($file, $partner = null)
    {
        [$profile, $id, $extension] = $this->parseThumbFile($file, ['thumb', 'ico']);
        $partner = $this->galleryPartner($id);

        return $this->sendFirstExistingFile($this->storagePaths($partner,
            ShopImageStorage::galleryThumbPath($id, $extension, $profile, $partner),
            ShopImageStorage::urlToPath(ShopImageStorage::legacyGalleryThumbUrl($id, $extension, $profile))
        ), function () use ($id, $profile) {
            return Yii::$app->runAction('image/product-image', ['id' => $id, 'profile' => $profile]);
        });
    }

    private function parseIdFile($file)
    {
        if (!preg_match('/^(\d+)\.(jpe?g|png|gif|webp)$/i', $file, $matches)) {
            throw new NotFoundHttpException('Image not found.');
        }

        return [(int)$matches[1], strtolower($matches[2])];
    }

    private function parseThumbFile($file, array $allowedProfiles)
    {
        if (!preg_match('/^([a-z]+)_(\d+)\.(jpe?g|png|gif|webp)$/i', $file, $matches)) {
            throw new NotFoundHttpException('Image not found.');
        }

        $profile = strtolower($matches[1]);
        if (!in_array($profile, $allowedProfiles, true)) {
            throw new NotFoundHttpException('Image not found.');
        }

        return [$profile, (int)$matches[2], strtolower($matches[3])];
    }

    private function productPartner($id)
    {
        return Yii::$app->db
            ->createCommand('SELECT [[partner]] FROM {{%shop_product}} WHERE [[id]] = :id', [':id' => $id])
            ->queryScalar();
    }

    private function galleryPartner($imageId)
    {
        return Yii::$app->db
            ->createCommand(
                'SELECT p.[[partner]]
                FROM {{%shop_product_image}} i
                INNER JOIN {{%shop_product}} p ON p.[[id]] = i.[[product_id]]
                WHERE i.[[id]] = :id',
                [':id' => $imageId]
            )
            ->queryScalar();
    }

    private function sendFirstExistingFile(array $paths, callable $fallback)
    {
        foreach (array_unique(array_filter($paths)) as $path) {
            if (is_file($path)) {
                $response = Yii::$app->response;
                $response->headers->set('Cache-Control', 'public, max-age=31536000');
                $response->headers->set('Expires', gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT');

                return $response->sendFile($path, basename($path), [
                    'inline' => true,
                    'mimeType' => $this->mimeType($path),
                ]);
            }
        }

        return $fallback();
    }

    private function storagePaths($partner, $partnerPath, $legacyPath)
    {
        if (ShopImageStorage::isPartitionedPartner($partner)) {
            return [$partnerPath];
        }

        return [$legacyPath];
    }

    private function mimeType($path)
    {
        $mimeType = @mime_content_type($path);
        return $mimeType ?: 'application/octet-stream';
    }
}
