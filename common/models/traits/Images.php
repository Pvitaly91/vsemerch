<?
namespace common\models\traits;

use common\Helpers\ShopImageStorage;
use console\models\Image;
use console\models\ImageTranslate;
use common\models\Language;
use Yii;
use PHPThumb\GD;
use yii\helpers\FileHelper;

trait Images {
    protected $thumbs = [
        'thumb' => ['width' => 300, 'height' => 300],
        'preview' => ['width' => 400, 'height' => 400],
    ];
    protected $ico = [
        'ico' => ['width' => 100, 'height' => 100],
        'thumb' => ['width' => 400, 'height' => 400],
    ];
    public $arrContextOptions = array(
        "ssl" => array(
            "verify_peer" => false,
            "verify_peer_name" => false,
        ),
    );
    public $FPath = '/frontend/web/upload/shop/products/';

    function isFile($file){
        // var_dump(parse_url($file));
        $result = false;
        $urlInfo = parse_url($file);
        if(isset($urlInfo["scheme"])){
             if($urlInfo["scheme"] == "ftp"){
                 $result = is_file($file);
             }else{
                 $ch = curl_init();
                 curl_setopt($ch, CURLOPT_URL, $file);
                 curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                 curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                 curl_exec($ch);
                 $info = curl_getinfo($ch);
                 curl_close($ch);
                 
                 if($info['http_code'] == "200"){
                     $result = true;
                 }
             }
        }
        return $result;
     }
    function getOrignalImgPath($id,$extention = "jpg"){
        
        return ShopImageStorage::productImagePath($id, $extention, ShopImageStorage::partnerForProductId($id));
    }
    function normalizeOriginalFoto($src,$dist,$flag = false){

     //   $_path = $path = $this->getOrignalImgPath($this->id);
    //   $path = $this->image;
        $this->ensureImageDirectory($dist);
        $sizes = @getimagesize($src);
        if (!$sizes || empty($sizes[0]) || empty($sizes[1])) {
            Yii::warning("Cannot read image size: $src");
            return false;
        }

        $w = $sizes[0];
        $h = $sizes[1];
        $k = $w/$h;
     //  echo $src." ".$dist."\r\n";
        if($flag == false && ($k > 0.7) && ($k < 1.3)){
            if (!copy($src, $dist)) {
                Yii::warning("Cannot copy image: $src -> $dist");
                return false;
            }
            $this->optimizeImportedImageFile($dist);
            return true;
        }

        if($w < $h)
            $w = $h;
        elseif($w > $h)
            $h = $w;
     // file_put_contents(Yii::getAlias('@frontend/web/upload/shop/products/' .$this->id.".txt"),"fsfsfsjskgjskgjs");
      // exit;
    //  var_dump($src);
    //  var_dump(file_exists($src));
    //  exit;
        if (true /* file_exists($src) */) {

            $mime = $sizes['mime'] ?? @mime_content_type($src);

            // Перевірка, що це взагалі картинка
            if (!$mime || strpos($mime, 'image/') !== 0) {
                Yii::warning("Skipping invalid image type: $src");
                return false;
            }

            switch ($mime) {
                case 'image/jpeg':
                    $existingImage = @imagecreatefromjpeg($src);
                    break;
                case 'image/png':
                    $existingImage = @imagecreatefrompng($src);
                    break;
                case 'image/webp':
                    if (function_exists('imagecreatefromwebp')) {
                        $existingImage = @imagecreatefromwebp($src);
                    } else {
                        Yii::warning("WEBP not supported. Skipping: $src");
                        return false;
                    }
                    break;
                default:
                    Yii::warning("Unsupported image type ($mime). Skipping: $src");
                    return false;
            }

            // Якщо GD не зміг відкрити картинку
            if (!$existingImage) {
                Yii::warning("Unable to decode image. Skipping: $src");
                return false;
            }

            // Створюємо результуюче зображення за твоєю логікою
            $newImage = imagecreatetruecolor($w, $h);
            $white = imagecolorallocate($newImage, 255, 255, 255);
            imagefill($newImage, 0, 0, $white);

            $existingWidth = imagesx($existingImage);
            $existingHeight = imagesy($existingImage);

            $destX = ($w - $existingWidth) / 2;
            $destY = ($h - $existingHeight) / 2;

            imagecopy($newImage, $existingImage,
                $destX, $destY,
                0, 0,
                $existingWidth, $existingHeight
            );

            $saved = $this->saveImportedImage($newImage, $dist, $this->extensionFromPath($dist), 85);

            imagedestroy($existingImage);
            imagedestroy($newImage);

            if (!$saved) {
                Yii::warning("Cannot save image: $dist");
                return false;
            }

            $this->optimizeImportedImageFile($dist);
            return true;
        }

        return false;
    }
    function normalizeFoto(){
        if($this->isAdmin()){
          
         //   $this->normalizeOriginalFoto();
          //  dd($this->getOrignalImgPath());
          //  $this->setPicture($this->image, $this->id);
           // exit;
        }
    }
    function getIconImg(){
        // /upload/shop/products/image/thumb
        // 
       // return "/upload/shop/products/image/thumb/ico_".$id.".jpg";
    }
    function getThumbImg(){
        // /upload/shop/products/image/thumb
        // 
      //  return "/upload/shop/products/image/thumb/ico_".$id.".jpg";
    }
    function getBigImg(){
       
        if(!empty($this->image)){
            $parts = explode(".",$this->image);
           
            $path = $this->getImageFileUrl('image');

            $partner = method_exists($this, 'hasAttribute') && $this->hasAttribute('partner') ? $this->partner : null;
            if($path && (ShopImageStorage::isPartitionedPartner($partner) || file_exists(Yii::getAlias('@frontend/web' . $path))))
                return $path;
            else
                return $this->getPic('image', 'preview', '/img/no_image.jpg');
        }
        
    }
     protected function fileUrlData($url) {
        $path = parse_url($url, PHP_URL_PATH);
        if (!$path) {
            $path = $url;
        }
        $name = urldecode(basename($path));
        $extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        return [
            'name' => $name,
            'extension' => $extension
        ];
    }
    protected function createThumbs($path, $thumbFilePath, $thumbs) {
        
   
        foreach ($thumbs as $profile => $config) {
            $thumbPath = $thumbFilePath[$profile];
              
            
            if (is_file($path)) {
                $mimeType = mime_content_type($path);
                // dd($config);
                if (in_array($mimeType, ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'])) {
                    //  dd([$path, $config]);
                    $thumb = new GD($path, $config);
                   // dd($config);
                    $processor = function (GD $thumb) use ($config) {
                       
                         $thumb->adaptiveResize($config['width'], $config['height']);
                     
                    };
                    call_user_func($processor, $thumb, $path);
                    FileHelper::createDirectory(pathinfo($thumbPath, PATHINFO_DIRNAME), 0775, true);
                    $thumb->save($thumbPath);
                }
            }
        }
    }
    function copyFtpFile(&$picture,&$image){
        if(!empty($picture) && !file_exists($image) && property_exists($this, 'ftpLogin') && property_exists($this, 'ftpConect') && $this->ftpLogin){
            try {
                $this->ensureImageDirectory($image);
                ftp_get($this->ftpConect, $image, $picture, FTP_BINARY);
                
            } catch (\Exception $e) {
                echo  $picture." ".$image." error: ".$e->getMessage()."\r\n";
                return false;
            }

            $picture = $image;
            return true;
        }else{
            return false;
        }
    }
     function setPicture($picture, $id){
       // $picture = $product["image"];
       //  print_r($picture); echo " \r\n";
        // print_r($id); echo " \r\n";
       
        if (!empty($picture)) {
            $fileData = $this->fileUrlData($picture);
            $image = $fileData['name'];
        }
      //  var_dump($fileData);
        $partner = ShopImageStorage::partnerForProductId($id);
        $image = ShopImageStorage::productImagePath($id, $fileData['extension'], $partner);
        
        
     
        if($this->copyFtpFile($picture,$image) || (!file_exists($image) && !empty($picture) && $this->isFile($picture))) {
         
            $this->normalizeOriginalFoto($picture,$image);
         
                $thumbFilePath = [
                    'thumb' => ShopImageStorage::productThumbPath($id, $fileData['extension'], 'thumb', $partner),
                    'preview' => ShopImageStorage::productThumbPath($id, $fileData['extension'], 'preview', $partner),
                ];
                
                $this->createThumbs($image, $thumbFilePath, $this->thumbs);
           // }
        }
       // exit;
    }
    function morePhotos(){
        $pictures = json_decode($this->more_photos);
        
        $this->setMorePhotos($pictures, $this->id);
    }
    function setMorePhotos($pictures,$id){
        $ids = [];
        
        if(is_array($pictures) && !empty($pictures)){
            foreach ($pictures as $key => $pic) {

                $fileData = $this->fileUrlData($pic);
                if (!$modelImage = Image::findOne(['product_id' => $id, 'image' => $fileData['name']])) {
                    $modelImage = new Image();
                }
                $modelImage->product_id = $id;
                $modelImage->image = $fileData['name'];
                $modelImage->save(false);

                $ids[] = $modelImage->id;
                foreach (Language::getLanguagesAsArray() as $language) {
                  
                    if (!$modelImageTranslate = ImageTranslate::findOne(['shop_product_image_id' => $modelImage->id, 'language' => $language])) {
                        $modelImageTranslate = new ImageTranslate();
                    }
                    $modelImageTranslate->shop_product_image_id = $modelImage->id;
                    $modelImageTranslate->language = $language;
                    $modelImageTranslate->alt = '';
                    $modelImageTranslate->save(false);
                }
                $partner = ShopImageStorage::partnerForProductId($id);
                $image = ShopImageStorage::galleryImagePath($modelImage->id, $fileData['extension'], $partner);
                    
                if ($this->copyFtpFile($pic,$image) || !file_exists($image) && isset($fileData['extension']) && $fileData['extension'] != "" && $this->isFile($pic)) {
                       $this->normalizeOriginalFoto($pic,$image);
                  //  if (!is_file($image)) {

                     //   copy($pic, $image, stream_context_create($this->arrContextOptions));

                        $thumbFilePath = [
                            'thumb' => ShopImageStorage::galleryThumbPath($modelImage->id, $fileData['extension'], 'thumb', $partner),
                            'ico' => ShopImageStorage::galleryThumbPath($modelImage->id, $fileData['extension'], 'ico', $partner),
                        ];
                        $this->createThumbs($image, $thumbFilePath, $this->ico);
                 //   }
                } 
            }
            if (isset($ids) && count($ids)) {
                Image::deleteAll([
                    'AND',
                    'product_id=:product_id',
                    ['not in', 'id', $ids]
                        ],
                        [':product_id' => $id]);
            }
        }    
    }

    protected function optimizeImportedImageFile($filePath)
    {
        if (!is_file($filePath) || !extension_loaded('gd')) {
            return false;
        }

        $options = $this->importedImageOptimizerOptions();
        $extension = $this->extensionFromPath($filePath);
        if (!in_array($extension, ['jpg', 'jpeg', 'webp'], true)) {
            return false;
        }
        if ($extension === 'webp' && !function_exists('imagecreatefromwebp')) {
            return false;
        }

        clearstatcache(true, $filePath);
        $originalSize = filesize($filePath);
        if ($originalSize === false || $originalSize <= 0) {
            return false;
        }

        $sizes = @getimagesize($filePath);
        if (!$sizes || empty($sizes[0]) || empty($sizes[1])) {
            return false;
        }

        $width = (int)$sizes[0];
        $height = (int)$sizes[1];
        $tooLargeByDimensions = $width > $options['maxWidth'] || $height > $options['maxHeight'];
        if ($tooLargeByDimensions && $originalSize < $options['skipDimensionResizeBelowBytes']) {
            return false;
        }
        if (!($originalSize > $options['minBytes'] || $tooLargeByDimensions)) {
            return false;
        }

        $image = $this->loadImportedImage($filePath, $extension);
        if (!$image) {
            return false;
        }

        $target = $this->calculateImportedImageTargetSize($width, $height, $options['maxWidth'], $options['maxHeight']);
        $resized = $target['width'] !== $width || $target['height'] !== $height;
        if ($resized) {
            $resizedImage = imagecreatetruecolor($target['width'], $target['height']);
            if (!imagecopyresampled($resizedImage, $image, 0, 0, 0, 0, $target['width'], $target['height'], $width, $height)) {
                imagedestroy($resizedImage);
                imagedestroy($image);
                return false;
            }
            imagedestroy($image);
            $image = $resizedImage;
        }

        $tmpPath = $filePath . '.import-opt-' . getmypid() . '-' . uniqid('', true);
        $saved = $this->saveImportedImage($image, $tmpPath, $extension, $options['quality']);
        imagedestroy($image);

        if (!$saved || !is_file($tmpPath)) {
            @unlink($tmpPath);
            return false;
        }

        clearstatcache(true, $tmpPath);
        $newSize = filesize($tmpPath);
        if ($newSize === false || $newSize <= 0) {
            @unlink($tmpPath);
            return false;
        }

        $savingRatio = ($originalSize - $newSize) / $originalSize;
        $canReplaceBySaving = $originalSize > $options['minBytes']
            && $newSize < $originalSize
            && $savingRatio >= 0.05;
        $canReplaceByDimensions = $tooLargeByDimensions
            && $resized
            && $target['width'] <= $options['maxWidth']
            && $target['height'] <= $options['maxHeight']
            && $newSize <= $originalSize * (1 + ($options['maxGrowthPercentForDimensions'] / 100));

        if (!$canReplaceBySaving && !$canReplaceByDimensions) {
            @unlink($tmpPath);
            return false;
        }

        if (!$this->replaceImportedImageFile($filePath, $tmpPath)) {
            @unlink($tmpPath);
            return false;
        }

        return true;
    }

    protected function importedImageOptimizerOptions()
    {
        return [
            'minBytes' => 400 * 1024,
            'maxWidth' => 1200,
            'maxHeight' => 1200,
            'quality' => 75,
            'maxGrowthPercentForDimensions' => 30,
            'skipDimensionResizeBelowBytes' => 90 * 1024,
        ];
    }

    protected function extensionFromPath($path)
    {
        $extension = strtolower(pathinfo(parse_url($path, PHP_URL_PATH) ?: $path, PATHINFO_EXTENSION));
        return $extension === 'jpe' ? 'jpg' : $extension;
    }

    protected function loadImportedImage($filePath, $extension)
    {
        if ($extension === 'jpg' || $extension === 'jpeg') {
            $image = @imagecreatefromjpeg($filePath);
            return $this->applyImportedJpegOrientation($image, $filePath);
        }

        if ($extension === 'webp' && function_exists('imagecreatefromwebp')) {
            return @imagecreatefromwebp($filePath);
        }

        if ($extension === 'png') {
            return @imagecreatefrompng($filePath);
        }

        return false;
    }

    protected function applyImportedJpegOrientation($image, $filePath)
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

    protected function calculateImportedImageTargetSize($width, $height, $maxWidth, $maxHeight)
    {
        $scale = min($maxWidth / $width, $maxHeight / $height, 1);

        return [
            'width' => max(1, (int)round($width * $scale)),
            'height' => max(1, (int)round($height * $scale)),
        ];
    }

    protected function saveImportedImage($image, $path, $extension, $quality)
    {
        $this->ensureImageDirectory($path);
        $extension = strtolower((string)$extension);

        if ($extension === 'jpg' || $extension === 'jpeg') {
            imageinterlace($image, true);
            return imagejpeg($image, $path, $quality);
        }

        if ($extension === 'webp' && function_exists('imagewebp')) {
            return imagewebp($image, $path, $quality);
        }

        if ($extension === 'png') {
            imagealphablending($image, false);
            imagesavealpha($image, true);
            return imagepng($image, $path, 8);
        }

        return false;
    }

    protected function replaceImportedImageFile($filePath, $tmpPath)
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
            $backupPath = $filePath . '.import-opt-backup-' . getmypid() . '-' . uniqid('', true);
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

    protected function ensureImageDirectory($filePath)
    {
        $dir = pathinfo($filePath, PATHINFO_DIRNAME);
        if ($dir && !is_dir($dir)) {
            FileHelper::createDirectory($dir, 0775, true);
        }
    }
}
