<? use yii\helpers\Html;?>
<div class="product-card-block__about">
    <div class="content"  >
	<? if($model->imageAvatar != "no_image.jpg"):?>
        	<img alt="<?= Html::encode($model->alt) ?>" class="main-img" align="left" src="<?= Yii::$app->request->baseUrl . '/upload/catalog/big/' . $model->imageAvatar ?>" />
        <? endif;?>
	<h1><?= $title; ?></h1>
        <?= $body ?>
    </div>
</div>