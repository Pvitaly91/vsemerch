<?php
use yii\widgets\Breadcrumbs;
use yii\web\View;
use yii\helpers\Html;
if(isset($model)){
    $this->title = $model->meta_title;
    $this->registerMetaTag(['name' => 'description', 'content' => $model->meta_description]);
    $this->registerMetaTag(['name' => 'keywords', 'content' => $model->meta_keywords]);
}
?>
 <? $breadcrumbs[] = [
                    "link" => \yii\helpers\Url::to(['site/index']),
                    "label" => Yii::t('yii', 'Home')
                ];
           
                 $breadcrumbs[] = [
                    "link" => \yii\helpers\Url::to(['design/index']),
                    "label" => Yii::t('app', 'Design')
                ];
             
                $breadcrumbs[] = [
                    
                    "label" => $text->title,
                ];    
            
             foreach($text->fotos as $item){
                $photos[] = [
                    "alt" => Html::encode($item->name),
                    "src" => Yii::$app->request->baseUrl.'/upload/text_fotos/ico/'.$item->image,
                    "title" =>Html::encode($item->name),  
                    "big" =>Yii::$app->request->baseUrl.'/upload/text_fotos/'.$item->image];
             } 
           
                ?>

<?= $this->render('/catalog/_show',["model" => $text,'breadcrumbs'=>$breadcrumbs,"photos" =>$photos, "title" =>$text->title ,"viewName" => "/design/_show"]) ?>