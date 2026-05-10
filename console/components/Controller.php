<?php
namespace console\components;

use PHPThumb\GD;
use Yii;
use yii\helpers\FileHelper;

Class Controller extends \yii\console\Controller
{
    protected $thumbs = [
        'thumb' => ['width' => 300, 'height' => 300],
        'preview' => ['width' => 400, 'height' => 400],
    ];
    protected $ico = [
        'ico' => ['width' => 100, 'height' => 100],
        'thumb' => ['width' => 400, 'height' => 400],
    ];

    protected function createThumbs($path, $thumbFilePath, $thumbs)
    {
        foreach ($thumbs as $profile => $config) {
            $thumbPath = $thumbFilePath[$profile];
            if (is_file($path)) {
                $mimeType = mime_content_type($path);
                if(in_array($mimeType, ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'])) {
                    $thumb = new GD($path, $config);

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

    protected function fileUrlData($url)
    {
        $arr = explode('/', $url);
        $name = $arr[count($arr) - 1];
        $extension = substr(strrchr($name, '.'), 1);
        return [
            'name' => $name,
            'extension' => $extension
        ];
    }
}