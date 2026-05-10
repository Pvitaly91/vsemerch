<?php

use yii\helpers\Html;
use yii\widgets\Breadcrumbs;

$this->title = $model->meta_title;
$this->registerMetaTag(['name' => 'description', 'content' => $model->meta_description]);
$this->registerMetaTag(['name' => 'keywords', 'content' => $model->meta_keywords]);
?>


<div class="head2">
    <div class="container">
        <div class="info2 col-md-12">
            <h1><?= $model->name; ?></h1>


            <nav class="breadcrumbs">
                <?= Breadcrumbs::widget([
                    'homeLink' => [
                        'label' => Yii::t('yii', 'Home'),
                        'url' => ['site/index'],
                    ],
                    'links' => [
                        ['label' => Yii::t('app', 'Web'), 'url' => ['web/index']],
                        ['label' => $model->catalog->name, 'url' => ['web/show', 'translit' => $model->catalog->translit]],
                        $model->name
                    ],
                ]) ?>
            </nav>
        </div>
    </div>
</div>
<div class="body_box">
    <div class="top2"></div>
    <div class="content">
        <div class="container cnt">
            <div class="col-md-12">

                <p>Адрес сайта: <a href="<?= $model->link ?>" target="_blank" rel="nofollow" class="link3"><?= $model->link ?></a></p>
                <img src="<?= Yii::$app->request->baseUrl . '/upload/web-products/big/' . $model->image ?>" alt="<?= Html::encode($model->name) ?>" width="1000" height="1000" border="0" class='img-responsive'/>

                <br/>
                <?= $model->body; ?>
            </div>
        </div>
    </div>
</div>

