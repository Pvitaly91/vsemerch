<?
namespace common\models\traits;

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
        
        return Yii::getAlias('@frontend/web/upload/shop/products/' .$id.".".$extention);
    }
    function normalizeOriginalFoto($src,$dist,$flag = false){
       
     //   $_path = $path = $this->getOrignalImgPath($this->id);
    //   $path = $this->image;
        $sizes = getimagesize($src);
        $w = $sizes[0];
        $h = $sizes[1];
        $k = $w/$h;
     //  echo $src." ".$dist."\r\n";
        if($flag == false && ($k > 0.7) && ($k < 1.3)){
            copy($src, $dist);
            return;
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

			$mime = @mime_content_type($src);

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

			imagejpeg($newImage, $dist, 85);

			imagedestroy($existingImage);
			imagedestroy($newImage);
		}

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
           
            $path = "/upload/shop/products/".$this->id.".".end($parts);
           // return $path;
           
            if(file_exists(\Yii::$app->basePath."/web".$path))
                return $path;
            else
                return $this->getPic('image', 'preview', '/img/no_image.jpg');
        }
        
    }
     protected function fileUrlData($url) {
        $arr = explode('/', $url);
        $name = $arr[count($arr) - 1];
        $extension = substr(strrchr($name, '.'), 1);
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
        $image = Yii::getAlias('@frontend/web/upload/shop/products/' . $id . '.' . $fileData['extension']);
        
        
     
        if($this->copyFtpFile($picture,$image) || (!file_exists($image) && !empty($picture) && $this->isFile($picture))) {
         
            $this->normalizeOriginalFoto($picture,$image);
         
                $thumbFilePath = [
                    'thumb' => Yii::getAlias('@frontend/web/upload/shop/products/thumb/thumb_' . $id . '.' . $fileData['extension']),
                    'preview' => Yii::getAlias('@frontend/web/upload/shop/products/thumb/preview_' . $id. '.' . $fileData['extension']),
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
                $image = Yii::getAlias('@frontend/web/upload/shop/products/image/' . $modelImage->id . '.' . $fileData['extension']);
                    
                if ($this->copyFtpFile($pic,$image) || !file_exists($image) && isset($fileData['extension']) && $fileData['extension'] != "" && $this->isFile($pic)) {
                       $this->normalizeOriginalFoto($pic,$image);
                  //  if (!is_file($image)) {

                     //   copy($pic, $image, stream_context_create($this->arrContextOptions));

                        $thumbFilePath = [
                            'thumb' => Yii::getAlias('@frontend/web/upload/shop/products/image/thumb/thumb_' . $modelImage->id . '.' . $fileData['extension']),
                            'ico' => Yii::getAlias('@frontend/web/upload/shop/products/image/thumb/ico_' . $modelImage->id . '.' . $fileData['extension']),
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
}