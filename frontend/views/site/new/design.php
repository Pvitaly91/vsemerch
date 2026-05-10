<?
use yii\helpers\Url;
?>
<section class="container">
    <div class="title-block-2">
        <h1 class="section-title"><?= Yii::t('app', 'ДИЗАЙН') ?></h1>
        <p class="product-block-title title-hidden">(<?= Yii::t('app', 'Дизайн и печать') ?>)</p>
    </div>
    <div class="services-block">
        <div class="service-block__item">
            <img src="/new/img/main/services-block/1.png" />
            <div class="service-block__item-description">
                <h3><?=mb_strtolower(Yii::t('app', 'ДИЗАЙН ПОЛИГРАФИИ')) ?></h3>
                <p class="p-bold"><?= mb_strtolower(Yii::t('app', '(КАТАЛОГИ, БУКЛЕТЫ, ПРЕЗЕНТАЦИИ)')) ?></p>
                <p><?= Yii::t('app', 'Дизайн полиграфии — это разработка графического дизайна под печатную продукцию.') ?></p>
                <a href="<?= Url::to(['design/show', 'slug' => 'dizayn_poligrafii_katalogi_buklety_prezentacii']) ?>" class="btn slider-block__btn"><?= Yii::t('app', 'Подробнее') ?></a>
            </div>
        </div>
        <div class="service-block__item">
            <div class="service-block__item-description">
                <h3><?= mb_strtolower(Yii::t('app', 'ДИЗАЙН ПОД КЛЮЧ')) ?></h3>
                <p class="p-bold"><?= mb_strtolower(Yii::t('app', '(ЛОГОТИПА, ФИРМЕННОГО СТИЛЯ, СЛОГАНА, БРЕНДБУКА)')) ?></p>
                <p>
                   <?= Yii::t('app', 'Логотип и фирменный стиль — это визуальный образ компании, то что делает вас уникальными и запоминающимися') ?>
                </p>
                <a href="<?= Url::to(['design/show', 'slug' => 'razrabotka_logotipa_firmennogo_stilya_slogana_brendbuka']) ?>" class="btn slider-block__btn"><?= Yii::t('app', 'Подробнее') ?></a>
            </div>
            <img src="/new/img/main/services-block/2.png" />
        </div>
    </div>
</section>