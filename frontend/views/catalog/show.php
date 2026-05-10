<?php
use yii\helpers\Html;

$this->title = $model->meta_title;
$this->registerMetaTag(['name' => 'description', 'content' => $model->meta_description]);
$this->registerMetaTag(['name' => 'keywords', 'content' => $model->meta_keywords]);

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
                    
                    "label" => $model->name,
                ];    
                
foreach($model->products as $item){
   $photos[] = [
       "alt" => Html::encode($item->name),
       "src" => Yii::$app->request->baseUrl . '/upload/products/ico/' . $item->image,
       "title" =>Html::encode($item->name),  
       "big" =>Yii::$app->request->baseUrl . '/upload/products/' . $item->image,];
}
 ?>

<?= $this->render('/catalog/_show',["model" => $model,"title" => $model->name,'breadcrumbs'=>$breadcrumbs,"photos" => $photos,"viewName" => "preview"]) ?>