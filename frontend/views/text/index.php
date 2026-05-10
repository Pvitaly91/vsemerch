<?php
use yii\widgets\Breadcrumbs;
use yii\web\View;
use yii\helpers\Html;

$this->title = $text->meta_title;
$this->registerMetaTag(['name' => 'description', 'content' => $text->meta_description]);
$this->registerMetaTag(['name' => 'keywords', 'content' => $text->meta_keywords]);

$breadcrumbs[] = [
                    "link" => \yii\helpers\Url::to(['site/index']),
                    "label" => Yii::t('yii', 'Home')
                ];
        
                $breadcrumbs[] = [
                    
                    "label" => $text->title,
                ];    
            
             $photos = [];
             if (!empty($text->fotos)) {
                 foreach($text->fotos as $item){
                    $photos[] = [
                        "alt" => Html::encode($item->name),
                        "src" => Yii::$app->request->baseUrl.'/upload/text_fotos/ico/'.$item->image,
                        "title" =>Html::encode($item->name),  
                        "big" =>Yii::$app->request->baseUrl.'/upload/text_fotos/'.$item->image];
                 }
             } 
             ?>

<?= $this->render('/catalog/_show',[
    "model" => $text,
    'breadcrumbs'=>$breadcrumbs,
    "photos" =>$photos,
    "title" =>$text->title,
    "viewName" => "/design/_show"
]) ?>
