<?
use yii\helpers\Url;
?>
<section class="container">
	<h1 class="section-title"><?= Yii::t('app', 'НОВОСТИ И АКЦИИ') ?></h1>
    <div class="news-block">
        <? foreach ($news as $item): ?>
            <div class="news-block__item" >
                <div class="news-block__item-title">
                    <p><?= $item->title ?></p>
                </div>
                <div class='news-img' style=" background-image: url('<?= Yii::$app->request->baseUrl . '/upload/news/big/' . $item->image ?>'); "></div>
              <?/*  <img src="<?= Yii::$app->request->baseUrl . '/upload/news/big/' . $item->image ?>" />
                */?><a href="<?= Url::to(['news/show', 'translit' => $item->translit, 'id' => $item->id]) ?>" class="news-block__item-more">
                    <p><?= Yii::t('app', 'Подробнее') ?></p>
                </a>
            </div>
        <? endforeach; ?>  
    </div>
</section>