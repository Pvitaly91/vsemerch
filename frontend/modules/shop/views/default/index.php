<?php

use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use common\components\Text;

$this->title = Yii::t('app', 'Shop categories mall');
$this->registerMetaTag(['name' => 'description', 'content' => Yii::t('app', 'Shop categories mall')]);
$this->registerMetaTag(['name' => 'keywords', 'content' => Yii::t('app', 'Shop categories mall')]);
?>


<div class="head2 4">
    <div class="container">
        <div class="info2 col-md-12">
            <h1><?= Yii::t('app', 'Shop categories mall'); ?></h1>

            <nav class="breadcrumbs">
                <?= Breadcrumbs::widget([
                    'homeLink' => [
                        'label' => Yii::t('yii', 'Home'),
                        'url' => ['site/index'],
                    ],
                    'links' => [
                        Yii::t('app', 'Shop categories mall')
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

        </div>
    </div>
</div>
