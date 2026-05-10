<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        //'js/bootstrap-3.3.6-dist/css/bootstrap.min.css',
        'css/loader.css',
        //'css/hover-min.css',
        'js/jquery-checkradios/css/jquery.checkradios.min.css',
        'css/style.css',
    ];
    public $js = [
        //'js/device/device.min.js',
        'js/jquery-checkradios/js/jquery.checkradios.min.js',
        'js/main.js',
        //'js/bootstrap-3.3.6-dist/js/bootstrap.min.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
        'yii\bootstrap\BootstrapPluginAsset',
    ];
}
