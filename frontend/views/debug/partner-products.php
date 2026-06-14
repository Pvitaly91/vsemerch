<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\LinkPager;

/* @var $this yii\web\View */
/* @var $partners array */
/* @var $partner string */
/* @var $pageSize int */
/* @var $pageSizes int[] */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $products common\models\Product[] */
/* @var $colors array */

$this->title = 'Debug products: ' . $partner;
$this->registerMetaTag(['name' => 'robots', 'content' => 'noindex,nofollow']);
$this->registerCssFile('/new/css/catalog.css');
$this->registerCssFile('/new/css/fix.css');
$this->registerCssFile('/new/css/fix-catalog.css');
$this->registerJsFile('/new/js/catalog-page/index.js', ['position' => View::POS_END]);

$pagination = $dataProvider->getPagination();
$count = $dataProvider->getCount();
$total = $dataProvider->getTotalCount();
$begin = $total > 0 ? $pagination->getPage() * $pagination->pageSize + 1 : 0;
$end = $total > 0 ? $begin + $count - 1 : 0;
$mid = 3;

?>
<main class="container debug-products-page">
    <section>
        <div class="navigation-block">
            <a href="<?= Url::to(['/site/index']) ?>"><span><?= Yii::t('yii', 'Home') ?></span></a>
            <svg width="5" height="8" viewBox="0 0 5 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M1 1L4 3.76923L1 7" stroke="#989898" stroke-linecap="round" />
            </svg>
            <p>Debug</p>
            <svg width="5" height="8" viewBox="0 0 5 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M1 1L4 3.76923L1 7" stroke="#989898" stroke-linecap="round" />
            </svg>
            <p><?= Html::encode($partner) ?></p>
        </div>

        <h1 style="margin-bottom: 24px; font-size: 30px;">Debug products by partner</h1>

        <form method="get" class="debug-products-filter">
            <label>
                Partner
                <?= Html::dropDownList('partner', $partner, $partners, ['class' => 'form-control']) ?>
            </label>
            <label>
                Per page
                <?= Html::dropDownList('per-page', $pageSize, array_combine($pageSizes, $pageSizes), ['class' => 'form-control']) ?>
            </label>
            <button type="submit" class="debug-products-submit">Show</button>
        </form>

        <div class="sort-titles-block">
            <p class="item-count">
                <?= Yii::t('shop', 'Showed') ?> <span><?= $begin ?>-<?= $end ?></span>
                <?= Yii::t('shop', 'From') ?> <span><?= $total ?></span>
                <?= Yii::t('shop', 'Possible') ?>
            </p>
        </div>

        <div class="main-block debug-products-main">
            <?php include Yii::getAlias('@frontend/modules/shop/views/catalog/main_block.php') ?>
        </div>

        <div class="more-btn">
            <?= LinkPager::widget(['pagination' => $pagination]) ?>
        </div>
    </section>
</main>

<?php
$this->registerCss(<<<CSS
.debug-products-filter {
    align-items: flex-end;
    display: flex;
    flex-wrap: wrap;
    gap: 16px;
    margin-bottom: 28px;
}
.debug-products-filter label {
    color: #333;
    display: flex;
    flex-direction: column;
    font-size: 14px;
    gap: 6px;
    min-width: 180px;
}
.debug-products-filter .form-control {
    border: 1px solid #d8d8d8;
    border-radius: 4px;
    min-height: 40px;
    padding: 8px 10px;
}
.debug-products-submit {
    background: #db4444;
    border: 0;
    border-radius: 4px;
    color: #fff;
    font-weight: 600;
    min-height: 40px;
    padding: 0 18px;
}
.debug-products-main {
    display: block;
}
.debug-products-page .products-block1 {
    align-items: stretch;
    display: grid;
    gap: 28px;
    grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
    padding: 20px 0 0;
    width: 100%;
}
.debug-products-page .products-block__card,
.debug-products-page .products-block__card.middle-item {
    display: flex;
    flex-direction: column;
    margin: 0 !important;
    max-width: none !important;
    min-height: 305px;
    width: 100%;
}
.debug-products-page .products-block__card-wrapper {
    flex: 0 0 auto;
}
.debug-products-page .products-block__card img.no-colors,
.debug-products-page .products-block__card-wrapper > a,
.debug-products-page .products-block__card-wrapper > a img {
    display: block;
    width: 100%;
}
.debug-products-page .products-block__card img.no-colors,
.debug-products-page .products-block__card-wrapper > a img {
    aspect-ratio: 1 / 1;
    height: auto;
    object-fit: contain;
}
.debug-products-page .products-block__card-title {
    align-items: flex-start;
    display: flex;
    flex: 1 0 auto;
    margin: 12px 14px 10px;
    min-height: 52px;
}
.debug-products-page .products-block__card-title a {
    display: -webkit-box;
    overflow: hidden;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 3;
}
.debug-products-page .products-block__card-footer1 {
    margin-top: auto;
}
.debug-products-page .product-status {
    padding-right: 14px;
}
.debug-products-page .products-block__card-price {
    padding-left: 14px;
}
@media (max-width: 720px) {
    .debug-products-page .products-block1 {
        gap: 18px;
        grid-template-columns: repeat(auto-fill, minmax(145px, 1fr));
    }
}
@media (max-width: 380px) {
    .debug-products-page .products-block1 {
        grid-template-columns: 1fr;
    }
}
CSS
);
?>
