<?

use yii\helpers\Url;
?>
<?
$this->title = $text->meta_title;
$this->registerMetaTag(['name' => 'description', 'content' => $text->meta_description]);
$this->registerMetaTag(['name' => 'keywords', 'content' => $text->meta_keywords]);

    $pattern = '/<([a-z][a-z0-9]*)[^>]*?(\/?)>/i';
    $body = preg_replace($pattern, '<$1$2>', $text->body);
    
    $items[] = [
        "label" => Yii::t('app', 'Printing and souvenirs'),
        "link" => Url::toRoute(['catalog/index']),
        "img" => "/img/pr_ico_production1.png"
    ];

    $items[] = [
        "label" => Yii::t('app', 'OUTDOOR ADVERTISING'),
        "link" => Url::to(['production/show', 'slug' => 'naruzhnaya_reklama']),
        "img" => "/img/pr_ico_production2.png"
    ];
     $breadcrumbs[] = [
            "link" => \yii\helpers\Url::to(['site/index']),
            "label" => Yii::t('yii', 'Home')
        ];
        $breadcrumbs[] = [
            "link" => \yii\helpers\Url::to(['production/index']),
            "label" => Yii::t('app', 'Production')
        ];
        $breadcrumbs[] = [
            "link" => \yii\helpers\Url::to(['catalog/index']),
            "label" => Yii::t('app', 'Printing and souvenirs')
        ];
        $breadcrumbs[] = [
            "label" => $text->title,
        ];
?>

<?= $this->render('/production/view',["items" => $items,"body" => $body,"text" => $text,'breadcrumbs'=>$breadcrumbs]) ?>

