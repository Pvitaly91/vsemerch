<?

use yii\helpers\Url;
?>
<?
$this->title = $text->meta_title;
$this->registerMetaTag(['name' => 'description', 'content' => $text->meta_description]);
$this->registerMetaTag(['name' => 'keywords', 'content' => $text->meta_keywords]);


    $pattern = '/<([a-z][a-z0-9]*)[^>]*?(\/?)>/i';
    $body = preg_replace($pattern, '<$1$2>', $text->body);
    
   
     $breadcrumbs[] = [
            "link" => \yii\helpers\Url::to(['site/index']),
            "label" => Yii::t('yii', 'Home')
        ];
      
        $breadcrumbs[] = [
            "label" => $text->title,
        ];
     $items[] = [
        "label" => Yii::t('app', 'Development (logo, corporate identity, slogan, brand book)'),
        "link" => Url::toRoute(['design/show','slug'=>'razrabotka_logotipa_firmennogo_stilya_slogana_brendbuka']),
        "img" => "/img/pr_ico_design2.png"
    ];

    $items[] = [
        "label" => Yii::t('app', 'Print design any (catalogs, brochures, presentations)'),
        "link" => Url::to(['design/show','slug'=>'dizayn_poligrafii_katalogi_buklety_prezentacii']),
        "img" => "/img/pr_ico_design3.png"
    ];    
?>
 
<?= $this->render('/production/view',["items" => $items,"body" => $body,"text" => $text,'breadcrumbs'=>$breadcrumbs]) ?>

