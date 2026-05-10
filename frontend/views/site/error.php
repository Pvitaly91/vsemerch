<?php

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

use yii\helpers\Html;
use yii\widgets\Breadcrumbs;

$this->title = $name;
?>
<style>
     .footer-pc{
        position: fixed;
        bottom: 0px;
    }
    
</style>

<div class="head2">
    <div class="container">
        <div class="info2 col-md-12">
            <h1><?= Html::encode($this->title) ?></h1>


            <nav class="breadcrumbs">
                <?= Breadcrumbs::widget([
                    'homeLink' => [
                        'label' => Yii::t('yii', 'Home'),
                        'url' => ['site/index'],
                    ],
                    'links' => [Html::encode($this->title)],
                ]) ?>
            </nav>
        </div>
    </div>
</div>
<div class="container cnt">

    <p>
        The above error occurred while the Web server was processing your request.
    </p>
    <p>
        Please contact us if you think this is a server error. Thank you.
    </p>
</div>
