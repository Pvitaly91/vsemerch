<?php

use yii\helpers\Url;
?>
<?php
$items = [];
$body = '';

if (!empty($text)) {
    if (!empty($text->meta_title)) {
        $this->title = $text->meta_title;
    }
    if (!empty($text->meta_description)) {
        $this->registerMetaTag(['name' => 'description', 'content' => $text->meta_description]);
    }
    if (!empty($text->meta_keywords)) {
        $this->registerMetaTag(['name' => 'keywords', 'content' => $text->meta_keywords]);
    }

    $pattern = '/<([a-z][a-z0-9]*)[^>]*?(\/?)>/i';
    $body = preg_replace($pattern, '<$1$2>', $text->body);
} else {
    // Fallback meta when text not provided
    $this->title = Yii::t('app', 'Printing and souvenirs');
    $this->registerMetaTag(['name' => 'description', 'content' => Yii::t('app', 'Printing and souvenirs')]);
    $this->registerMetaTag(['name' => 'keywords', 'content' => Yii::t('app', 'Printing and souvenirs')]);
}

foreach($model as $item) {
    $items[] = [
        "label" => $item->name,
        "link" => Url::toRoute(['catalog/show','translit'=>$item->translit]),
        "img" => "/img/catalog/ico".$item->id.".png"
    ];
}
?>

<main class="container">
    <section>
        <?php
        $breadcrumbs[] = [
            "link" => \yii\helpers\Url::to(['site/index']),
            "label" => Yii::t('yii', 'Home')
        ];
        $breadcrumbs[] = [
            "link" => \yii\helpers\Url::to(['production/index']),
            "label" => Yii::t('app', 'Production')
        ];
        $breadcrumbs[] = [
            "label" => Yii::t('app', 'Printing and souvenirs'),
        ];
        ?>
        <?= $this->render('/catalog/breadcrumbs',['breadcrumbs'=>$breadcrumbs]) ?>
      
        <div class="product-card-block">

            <h1><?= Yii::t('app', 'Printing and souvenirs'); ?></h1>
            
                 
             <?= $this->render('/production/products-items',['items'=>$items]) ?>
            <div class="contect-block">
                
                <?= $body; ?>
            </div>


        </div>
    </section>
</main>

