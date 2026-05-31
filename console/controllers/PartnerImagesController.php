<?php

namespace console\controllers;

use common\Helpers\ShopImageStorage;
use Throwable;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\db\Query;

class PartnerImagesController extends Controller
{
    public $partner = ShopImageStorage::PARTNER_ENEY;
    public $apply = 0;
    public $overwrite = 0;
    public $limit = 0;
    public $deleteLegacyUnknown = 0;
    public $deleteDbRows = 1;

    private $stats = [];
    private $examples = [];
    private $productPartnerById = [];
    private $galleryOwnerById = [];
    private $deletedGalleryRows = [];
    private $ownerMapsLoaded = false;

    public function options($actionID)
    {
        return array_merge(parent::options($actionID), [
            'partner',
            'apply',
            'overwrite',
            'limit',
            'deleteLegacyUnknown',
            'deleteDbRows',
        ]);
    }

    public function actionMove()
    {
        set_time_limit(0);
        ini_set('memory_limit', '1024M');

        $this->partner = strtolower(trim((string)$this->partner));
        $this->apply = (int)$this->apply === 1;
        $this->overwrite = (int)$this->overwrite === 1;
        $this->limit = max(0, (int)$this->limit);
        $this->stats = $this->emptyStats();
        $this->examples = [];

        if (!ShopImageStorage::isPartitionedPartner($this->partner)) {
            $this->stderr("Unsupported partner storage: {$this->partner}. Only eney is enabled now.\n");
            return ExitCode::UNSPECIFIED_ERROR;
        }

        $this->stdout('partner: ' . $this->partner . PHP_EOL);
        $this->stdout('mode: ' . ($this->apply ? 'apply' : 'dry-run') . PHP_EOL);
        if ($this->overwrite) {
            $this->stdout("overwrite: enabled\n");
        }

        $this->moveMainImages();
        $this->moveGalleryImages();
        $this->printSummary();

        return $this->stats['errors'] > 0 ? ExitCode::UNSPECIFIED_ERROR : ExitCode::OK;
    }

    public function actionDeleteOrphans()
    {
        set_time_limit(0);
        ini_set('memory_limit', '1024M');

        $this->partner = strtolower(trim((string)$this->partner));
        $this->apply = (int)$this->apply === 1;
        $this->deleteLegacyUnknown = (int)$this->deleteLegacyUnknown === 1;
        $this->deleteDbRows = (int)$this->deleteDbRows === 1;
        $this->limit = max(0, (int)$this->limit);
        $this->stats = $this->emptyOrphanStats();
        $this->examples = [];
        $this->productPartnerById = [];
        $this->galleryOwnerById = [];
        $this->deletedGalleryRows = [];
        $this->ownerMapsLoaded = false;

        if (!ShopImageStorage::isPartitionedPartner($this->partner)) {
            $this->stderr("Unsupported partner storage: {$this->partner}. Only eney is enabled now.\n");
            return ExitCode::UNSPECIFIED_ERROR;
        }

        $this->stdout('partner: ' . $this->partner . PHP_EOL);
        $this->stdout('mode: ' . ($this->apply ? 'apply' : 'dry-run') . PHP_EOL);
        $this->stdout('delete legacy unknown: ' . ($this->deleteLegacyUnknown ? 'yes' : 'no') . PHP_EOL);
        $this->stdout('delete DB rows: ' . ($this->deleteDbRows ? 'yes' : 'no') . PHP_EOL);

        $this->loadOwnerMaps();
        $this->scanMainFiles(false);
        $this->scanGalleryFiles(false);
        $this->scanMainFiles(true);
        $this->scanGalleryFiles(true);
        $this->printSummary();

        return $this->stats['errors'] > 0 ? ExitCode::UNSPECIFIED_ERROR : ExitCode::OK;
    }

    private function moveMainImages()
    {
        $query = (new Query())
            ->select(['id', 'image'])
            ->from('{{%shop_product}}')
            ->where(['partner' => $this->partner])
            ->orderBy(['id' => SORT_ASC]);

        $processed = 0;
        foreach ($query->each(500) as $row) {
            $extension = ShopImageStorage::extensionFromImageValue($row['image']);
            if (!$extension) {
                continue;
            }

            $id = (int)$row['id'];
            $this->moveOne(
                ShopImageStorage::urlToPath(ShopImageStorage::legacyProductImageUrl($id, $extension)),
                ShopImageStorage::productImagePath($id, $extension, $this->partner),
                'main original ' . $id
            );
            $this->moveOne(
                ShopImageStorage::urlToPath(ShopImageStorage::legacyProductThumbUrl($id, $extension, 'thumb')),
                ShopImageStorage::productThumbPath($id, $extension, 'thumb', $this->partner),
                'main thumb ' . $id
            );
            $this->moveOne(
                ShopImageStorage::urlToPath(ShopImageStorage::legacyProductThumbUrl($id, $extension, 'preview')),
                ShopImageStorage::productThumbPath($id, $extension, 'preview', $this->partner),
                'main preview ' . $id
            );

            $processed++;
            if ($this->limit > 0 && $processed >= $this->limit) {
                break;
            }
        }
    }

    private function moveGalleryImages()
    {
        $query = (new Query())
            ->select(['i.id', 'i.image'])
            ->from(['i' => '{{%shop_product_image}}'])
            ->innerJoin(['p' => '{{%shop_product}}'], 'p.id = i.product_id')
            ->where(['p.partner' => $this->partner])
            ->orderBy(['i.id' => SORT_ASC]);

        $processed = 0;
        foreach ($query->each(500) as $row) {
            $extension = ShopImageStorage::extensionFromImageValue($row['image']);
            if (!$extension) {
                continue;
            }

            $id = (int)$row['id'];
            $this->moveOne(
                ShopImageStorage::urlToPath(ShopImageStorage::legacyGalleryImageUrl($id, $extension)),
                ShopImageStorage::galleryImagePath($id, $extension, $this->partner),
                'gallery original ' . $id
            );
            $this->moveOne(
                ShopImageStorage::urlToPath(ShopImageStorage::legacyGalleryThumbUrl($id, $extension, 'ico')),
                ShopImageStorage::galleryThumbPath($id, $extension, 'ico', $this->partner),
                'gallery ico ' . $id
            );
            $this->moveOne(
                ShopImageStorage::urlToPath(ShopImageStorage::legacyGalleryThumbUrl($id, $extension, 'thumb')),
                ShopImageStorage::galleryThumbPath($id, $extension, 'thumb', $this->partner),
                'gallery thumb ' . $id
            );

            $processed++;
            if ($this->limit > 0 && $processed >= $this->limit) {
                break;
            }
        }
    }

    private function scanMainFiles($legacy)
    {
        $baseUrl = $legacy
            ? ShopImageStorage::PRODUCTS_URL
            : ShopImageStorage::PRODUCTS_URL . '/' . $this->partner;

        $this->scanDirectory(
            ShopImageStorage::urlToPath($baseUrl),
            'main',
            false,
            $legacy
        );
        $this->scanDirectory(
            ShopImageStorage::urlToPath($baseUrl . '/thumb'),
            'main',
            true,
            $legacy
        );
    }

    private function scanGalleryFiles($legacy)
    {
        $baseUrl = $legacy
            ? ShopImageStorage::PRODUCTS_URL . '/image'
            : ShopImageStorage::PRODUCTS_URL . '/' . $this->partner . '/image';

        $this->scanDirectory(
            ShopImageStorage::urlToPath($baseUrl),
            'gallery',
            false,
            $legacy
        );
        $this->scanDirectory(
            ShopImageStorage::urlToPath($baseUrl . '/thumb'),
            'gallery',
            true,
            $legacy
        );
    }

    private function scanDirectory($directory, $entity, $thumb, $legacy)
    {
        if ($this->limit > 0 && $this->stats['files_to_delete'] >= $this->limit) {
            return;
        }

        if (!is_dir($directory)) {
            $this->addExample('missing directories', $directory);
            return;
        }

        foreach (new \DirectoryIterator($directory) as $file) {
            if (!$file->isFile()) {
                continue;
            }

            $id = $this->idFromImageFilename($file->getFilename(), $thumb);
            if ($id === null) {
                continue;
            }

            $this->stats['checked_files']++;
            $classification = $entity === 'main'
                ? $this->classifyMainFile($id, $legacy)
                : $this->classifyGalleryFile($id, $legacy);

            if ($classification === 'owned' || $classification === 'other_partner') {
                continue;
            }

            if ($classification === 'legacy_unknown' && !$this->deleteLegacyUnknown) {
                $this->stats['legacy_unknown_files']++;
                $this->addExample('legacy unknown files', $file->getPathname());
                continue;
            }
            if ($classification === 'legacy_unknown') {
                $this->stats['legacy_unknown_files']++;
            }

            $this->deleteFile($file->getPathname(), $classification);
            if ($entity === 'gallery') {
                $this->deleteGalleryDbRowIfNeeded($id, $classification);
            }

            if ($this->limit > 0 && $this->stats['files_to_delete'] >= $this->limit) {
                return;
            }
        }
    }

    private function classifyMainFile($productId, $legacy)
    {
        $partner = $this->productPartner($productId);
        if ($partner === null) {
            return $legacy ? 'legacy_unknown' : 'orphan';
        }

        if ($partner === $this->partner) {
            return 'owned';
        }

        return $legacy ? 'other_partner' : 'orphan';
    }

    private function classifyGalleryFile($imageId, $legacy)
    {
        $owner = $this->galleryOwner($imageId);
        if ($owner === null || empty($owner['product_id']) || $owner['partner'] === null) {
            return $legacy ? 'legacy_unknown' : 'orphan';
        }

        if ($owner['partner'] === $this->partner) {
            return 'owned';
        }

        return $legacy ? 'other_partner' : 'orphan';
    }

    private function idFromImageFilename($filename, $thumb)
    {
        if ($thumb) {
            if (!preg_match('/^(?:thumb|preview|ico)_(\d+)\.[a-z0-9]+$/i', $filename, $matches)) {
                return null;
            }
        } elseif (!preg_match('/^(\d+)\.[a-z0-9]+$/i', $filename, $matches)) {
            return null;
        }

        return (int)$matches[1];
    }

    private function productPartner($productId)
    {
        $productId = (int)$productId;
        if ($this->ownerMapsLoaded) {
            return $this->productPartnerById[$productId] ?? null;
        }

        if (!array_key_exists($productId, $this->productPartnerById)) {
            $partner = (new Query())
                ->select('partner')
                ->from('{{%shop_product}}')
                ->where(['id' => $productId])
                ->scalar();
            $this->productPartnerById[$productId] = $partner === false ? null : (string)$partner;
        }

        return $this->productPartnerById[$productId];
    }

    private function galleryOwner($imageId)
    {
        $imageId = (int)$imageId;
        if ($this->ownerMapsLoaded) {
            return $this->galleryOwnerById[$imageId] ?? null;
        }

        if (!array_key_exists($imageId, $this->galleryOwnerById)) {
            $row = (new Query())
                ->select([
                    'image_id' => 'i.id',
                    'product_id' => 'i.product_id',
                    'partner' => 'p.partner',
                ])
                ->from(['i' => '{{%shop_product_image}}'])
                ->leftJoin(['p' => '{{%shop_product}}'], 'p.id = i.product_id')
                ->where(['i.id' => $imageId])
                ->one();
            $this->galleryOwnerById[$imageId] = $row ?: null;
        }

        return $this->galleryOwnerById[$imageId];
    }

    private function loadOwnerMaps()
    {
        foreach ((new Query())->select(['id', 'partner'])->from('{{%shop_product}}')->each(1000) as $row) {
            $this->productPartnerById[(int)$row['id']] = (string)$row['partner'];
        }

        $query = (new Query())
            ->select([
                'image_id' => 'i.id',
                'product_id' => 'i.product_id',
                'partner' => 'p.partner',
            ])
            ->from(['i' => '{{%shop_product_image}}'])
            ->leftJoin(['p' => '{{%shop_product}}'], 'p.id = i.product_id');

        foreach ($query->each(1000) as $row) {
            $this->galleryOwnerById[(int)$row['image_id']] = $row;
        }

        $this->ownerMapsLoaded = true;
    }

    private function deleteFile($path, $classification)
    {
        $this->stats['files_to_delete']++;
        if ($classification === 'legacy_unknown') {
            $this->stats['legacy_unknown_files_to_delete']++;
        } else {
            $this->stats['partner_orphan_files']++;
        }
        $this->stats['bytes_to_delete'] += filesize($path) ?: 0;

        if (!$this->apply) {
            $this->addExample('files to delete', $path);
            return;
        }

        if (@unlink($path)) {
            $this->stats['deleted_files']++;
            return;
        }

        $this->stats['errors']++;
        $this->addExample('errors', 'cannot delete file: ' . $path);
    }

    private function deleteGalleryDbRowIfNeeded($imageId, $classification)
    {
        if (!$this->deleteDbRows || isset($this->deletedGalleryRows[$imageId])) {
            return;
        }

        $owner = $this->galleryOwner($imageId);
        if ($owner === null || !empty($owner['product_id']) && $owner['partner'] !== null) {
            return;
        }

        $this->deletedGalleryRows[$imageId] = true;
        $this->stats[$this->apply ? 'db_rows_deleted' : 'db_rows_to_delete']++;

        if (!$this->apply) {
            return;
        }

        try {
            \Yii::$app->db->createCommand()
                ->delete('{{%shop_product_image_translate}}', ['shop_product_image_id' => $imageId])
                ->execute();
            \Yii::$app->db->createCommand()
                ->delete('{{%shop_product_image}}', ['id' => $imageId])
                ->execute();
        } catch (Throwable $e) {
            $this->stats['errors']++;
            $this->addExample('errors', 'cannot delete DB row ' . $imageId . ': ' . $e->getMessage());
        }
    }

    private function moveOne($source, $destination, $label)
    {
        $this->stats['checked']++;

        $sourceExists = is_file($source);
        $destinationExists = is_file($destination);

        if (!$sourceExists) {
            if ($destinationExists) {
                $this->stats['already_moved']++;
            } else {
                $this->stats['missing']++;
            }
            return;
        }

        $size = filesize($source) ?: 0;

        if ($destinationExists) {
            if ($this->sameFile($source, $destination)) {
                $this->stats[$this->apply ? 'duplicate_sources_removed' : 'duplicate_sources_to_remove']++;
                $this->stats['bytes_to_free'] += $size;
                if ($this->apply && !@unlink($source)) {
                    $this->stats['errors']++;
                    $this->addExample('errors', $label . ': cannot remove duplicate source ' . $source);
                }
                return;
            }

            if (!$this->overwrite) {
                $this->stats['conflicts']++;
                $this->addExample('conflicts', $label . ': ' . $source . ' -> ' . $destination);
                return;
            }

            $this->stats[$this->apply ? 'overwritten' : 'to_overwrite']++;
        } else {
            $this->stats[$this->apply ? 'moved' : 'to_move']++;
        }

        $this->stats['bytes_to_move'] += $size;
        if (!$this->apply) {
            return;
        }

        try {
            $dir = dirname($destination);
            if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
                throw new \RuntimeException('cannot create directory ' . $dir);
            }

            if ($destinationExists && $this->overwrite && !@unlink($destination)) {
                throw new \RuntimeException('cannot overwrite destination ' . $destination);
            }

            if (!@rename($source, $destination)) {
                if (!@copy($source, $destination) || !@unlink($source)) {
                    throw new \RuntimeException('cannot move file');
                }
            }
        } catch (Throwable $e) {
            $this->stats['errors']++;
            $this->addExample('errors', $label . ': ' . $e->getMessage());
        }
    }

    private function sameFile($left, $right)
    {
        if (filesize($left) !== filesize($right)) {
            return false;
        }

        return sha1_file($left) === sha1_file($right);
    }

    private function emptyStats()
    {
        return [
            'checked' => 0,
            'to_move' => 0,
            'moved' => 0,
            'already_moved' => 0,
            'missing' => 0,
            'to_overwrite' => 0,
            'overwritten' => 0,
            'duplicate_sources_to_remove' => 0,
            'duplicate_sources_removed' => 0,
            'conflicts' => 0,
            'errors' => 0,
            'bytes_to_move' => 0,
            'bytes_to_free' => 0,
        ];
    }

    private function emptyOrphanStats()
    {
        return [
            'checked_files' => 0,
            'files_to_delete' => 0,
            'partner_orphan_files' => 0,
            'legacy_unknown_files' => 0,
            'legacy_unknown_files_to_delete' => 0,
            'deleted_files' => 0,
            'db_rows_to_delete' => 0,
            'db_rows_deleted' => 0,
            'bytes_to_delete' => 0,
            'errors' => 0,
        ];
    }

    private function addExample($key, $message)
    {
        if (!isset($this->examples[$key])) {
            $this->examples[$key] = [];
        }

        if (count($this->examples[$key]) < 10) {
            $this->examples[$key][] = $message;
        }
    }

    private function printSummary()
    {
        $this->stdout("\nSummary\n");
        foreach ($this->stats as $key => $value) {
            if (strpos($key, 'bytes_') === 0) {
                $value = $this->formatBytes($value);
            }
            $this->stdout($key . ': ' . $value . PHP_EOL);
        }

        foreach ($this->examples as $key => $items) {
            $this->stdout("\n{$key} examples\n");
            foreach ($items as $item) {
                $this->stdout($item . PHP_EOL);
            }
        }
    }

    private function formatBytes($bytes)
    {
        $bytes = (float)$bytes;
        $units = ['B', 'KB', 'MB', 'GB'];
        $index = 0;
        while ($bytes >= 1024 && $index < count($units) - 1) {
            $bytes /= 1024;
            $index++;
        }

        return sprintf($bytes >= 10 ? '%.1f %s' : '%.2f %s', $bytes, $units[$index]);
    }
}
