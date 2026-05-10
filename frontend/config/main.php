<?php
/**
 * Debug function
 * d($var);
 */
function d($var,$caller=null)
{
    if(!isset($caller)){
        $caller = array_shift(debug_backtrace(1));
    }
    echo '<code>File: '.$caller['file'].' / Line: '.$caller['line'].'</code>';
    echo '<pre>';
    yii\helpers\VarDumper::dump($var, 10, true);
    echo '</pre>';
}

/**
 * Debug function with die() after
 * dd($var);
 */
function dd($var)
{
    $caller = array_shift(debug_backtrace(1));
    d($var,$caller);
    die();
}
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-frontend',
    'language'=>'uk',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'frontend\controllers',
    'components' => [
	    'urlManager'=>[
			'enablePrettyUrl' => true,
			'showScriptName' => false,
            //'enableStrictParsing' => true,
                        'normalizer' => [
                            'class' => 'yii\web\UrlNormalizer',
                            'action' => \yii\web\UrlNormalizer::ACTION_REDIRECT_TEMPORARY, // use temporary redirection instead of permanent
                        ],
			'rules'=>[
//                '<language:(ru|uk|en)>'=>'site/index',
//                '<language:(ru|uk|en)>/text/<slug:\w+>'=>'text/index',
//                '<language:(ru|uk|en)>/call/success'=>'call/success',
//                '<language:(ru|uk|en)>/mail/success'=>'mail/success',
//                '<language:(ru|uk|en)>/news'=>'news/index',
//                '<language:(ru|uk|en)>/news/<translit:[\w\-\ ]+>-<id:\d+>'=>'news/show',
//                '<language:(ru|uk|en)>/products/<translit:[\w\-\ ]+>-<id:\d+>'=>'products/show',
//                '<language:(ru|uk|en)>/products'=>'products/index',
//                '<language:(ru|uk|en)>/disease/<translit:[\w\-\ ]+>'=>'disease/show',
//                '<language:(ru|uk|en)>/disease'=>'disease/index',
//                '<language:(ru|uk|en)>/partners'=>'partners/index',
//                '<language:(ru|uk|en)>/contacts'=>'contacts/index',
//                '<language:(ru|uk|en)>/production'=>'production/index',
//                '<language:(ru|uk|en)>/production/<slug:\w+>'=>'production/show',
//                '<language:(ru|uk|en)>/catalog/<translit:[\w\-\ ]+>'=>'catalog/show',
//                '<language:(ru|uk|en)>/catalog'=>'catalog/index',
//                '<language:(ru|uk|en)>/web/<translit:[\w\-\ ]+>'=>'web/show',
//                '<language:(ru|uk|en)>/web'=>'web/index',
//                '<language:(ru|uk|en)>/web/portfolio/<translit:[\w\-\ ]+>-<id:\d+>'=>'web-products/show',
//                '<language:(ru|uk|en)>/design'=>'design/index',
//                '<language:(ru|uk|en)>/design/<slug:[\w\-\ ]+>'=>'design/show',
//                '<language:(ru|uk|en)>/shop/catalog/<slug:[\w\-\ ]+>' => 'shop/catalog/list',
//

                ''=>'site/index',
                'gallery/<id:\d+>'=>'gallery/show',
                'about'=>'text/about',
                'contact'=>'text/contact',
                'text/<slug:\w+>'=>'text/index',
                'articles'=>'articles/index',
                'articles/<slug:[\w\-\ \_]+>-<id:\d+>'=>'articles/show',

                'blog'=>'blog/index',
                'blog/<translit:[\w\-\ ]+>-<id:\d+>'=>'blog/show',

                'news'=>'news/index',
                'news/<translit:[\w\-\ ]+>-<id:\d+>'=>'news/show',
                'products/<translit:[\w\-\ ]+>-<id:\d+>'=>'products/show',
                'products'=>'products/index',
                'disease/<translit:[\w\-\ ]+>'=>'disease/show',
                'disease'=>'disease/index',
                'partners'=>'partners/index',
                'contacts'=>'contacts/index',
                'production'=>'production/index',
                'production/<slug:\w+>'=>'production/show',
                'catalog/<translit:[\w\-\ ]+>'=>'catalog/show',
                'catalog'=>'catalog/index',
                'web/<translit:[\w\-\ ]+>'=>'web/show',
                'web'=>'web/index',
                'web/portfolio/<translit:[\w\-\ ]+>-<id:\d+>'=>'web-products/show',
                'design'=>'design/index',
                'design/<slug:[\w\-\ ]+>'=>'design/show',
                'shop/catalog/search-ajax' => 'shop/catalog/search-ajax',
                'shop/catalog/<slug:[\w\-\ ]+>-<id:\d+>/<page:\d+>' => 'shop/catalog/list',
                'shop/catalog/<slug:[\w\-\ ]+>-<id:\d+>' => 'shop/catalog/list',
                       
                'shop/product/<slug:[\w\-\ ]+>-<id:\d+>' => 'shop/product/view',             
                'test' => 'site/test',  
                 'b.jpg' => 'site/test',
                'catmapdelete' => 'category-map/delete',    
                'api/getCitys' => 'np/citys',   
                'api/getDepartaments' => 'np/departaments', 
                'wayforpay' => 'wayforpay/index',
                'returnwayforpay' => 'wayforpay/returnwayforpay',   
                'servicewayforpay' => 'wayforpay/servicewayforpay',
                'ajax/productdata' => 'shop/product/productdata',            
                'login' => 'login/index',
               
                 'logout' => 'login/logout'             
            ],
            'class'=>'common\components\urlManager\LangUrlManager',
            'enableDefaultLanguageUrlCode' => false,
            'enableLanguagePersistence' => false,
		],
	    'request' => [
            'baseUrl' => '',
            //'class' => 'common\components\urlManager\LangRequest',
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'assetManager' => [
            'appendTimestamp' => true,
            'bundles' => [
                'yii\web\JqueryAsset' => [
                    'sourcePath' => null,   // do not publish the bundle
                    'js' => [
                        'js/jquery-11.0.min.js',
                    ]
                ],
            ],
        ],
        'cart' => [
            'class' => 'yz\shoppingcart\ShoppingCart',
        ],
    ],
    'modules' => [
        'shop' => [
            'class' => 'frontend\modules\shop\Module',
        ],
    ],
    'params' => $params,
];
