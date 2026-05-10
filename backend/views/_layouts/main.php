<?php

/* @var $this \yii\web\View */
/* @var $content string */

use backend\assets\AppAsset;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use common\widgets\Alert;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php
    NavBar::begin([
        'brandLabel' => 'Админ панель',
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);
    $menuItems = [
        ['label' => 'Пользователи', 'url' => ['/users/index']],
    ];
    if (Yii::$app->user->isGuest) {
        $menuItems[] = ['label' => 'Login', 'url' => ['/site/login']];
    } else {
        $menuItems[] = '<li>'
            . Html::beginForm(['/login/logout'], 'post')
            . Html::submitButton(
                'Выход (' . Yii::$app->user->identity->username . ')',
                ['class' => 'btn btn-link']
            )
            . Html::endForm()
            . '</li>';
    }
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => $menuItems,
    ]);
    NavBar::end();
    ?>
    <div class="container">
    <div class="col-md-3">

    <div class="well">
        <?php
        echo Nav::widget([
            'encodeLabels' => false,
            'items'=>[
                 ['label'=>'<span class="glyphicon glyphicon-file"></span> Текстовые страницы', 'url'=>['/text/index']],
               // ['label'=>'<span class="glyphicon glyphicon-repeat"></span> Галерея', 'url'=>['/fotos-cat/index']],
                ['label'=>'<span class="glyphicon glyphicon-list-alt"></span> Новости', 'url'=>['/news/index']],
                ['label'=>'<span class="glyphicon glyphicon-th"></span> Каталог', 'url'=>['/catalog/index']],
                ['label'=>'<span class="glyphicon glyphicon-th"></span> Заболевания', 'url'=>['/disease/index']],

                ['label'=>'<span class="glyphicon glyphicon-th"></span> Опции', 'url'=>['/option/index']],
                ['label'=>'<span class="glyphicon glyphicon-repeat"></span> Слайдер', 'url'=>['/slider/index']],
            ],
        ]); ?>
    </div>

    </div>
    <div class="col-md-9">


        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>

    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; My Company <?= date('Y') ?></p>

        <p class="pull-right"><?= Yii::powered() ?></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
