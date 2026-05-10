<?

use yii\helpers\Url;
?>
<section class="container">
    <div class="title-block">
        <h1 class="section-title"><?= Yii::t('app', 'ПОЛИГРАФИЧЕСКАЯ ПРОДУКЦИЯ') ?></h1>
        <p class="product-block-title">(<?= Yii::t('app', 'Дизайн и печать') ?>)</p>
    </div>
    <div class="products-block">
        <?
        if(empty($widgets)){    
            $data = array(
                0 =>
                array(
                    'image_src' => '/new/img/main/poligraph-production/1.png',
                    'link_href' => '/catalog/vizitki',
                    'link_text_uk' => 'візитки',
                    'link_text_ru' => 'визитки',
                    'link_text_en' => 'business cards',
                ),
                1 =>
                array(
                    'image_src' => '/new/img/main/poligraph-production/2.png',
                    'link_href' => '/catalog/katalogi',
                    'link_text_uk' => 'каталоги',
                    'link_text_ru' => 'каталоги',
                    'link_text_en' => 'catalogs',
                ),
                2 =>
                array(
                    'image_src' => '/new/img/main/poligraph-production/3.png',
                    'link_href' => '/catalog/listovki',
                    'link_text_uk' => 'листівки',
                    'link_text_ru' => 'листовки',
                    'link_text_en' => 'leaflets',
                ),
                3 =>
                array(
                    'image_src' => '/new/img/main/poligraph-production/4.png',
                    'link_href' => '/catalog/flaery',
                    'link_text_uk' => 'флаєра',
                    'link_text_ru' => 'флаера',
                    'link_text_en' => 'flyers',
                ),
                4 =>
                array(
                    'image_src' => '/new/img/main/poligraph-production/5.png',
                    'link_href' => '/catalog/birki_i_cenniki',
                    'link_text_uk' => 'бірки і цінники',
                    'link_text_ru' => 'бирки и ценники',
                    'link_text_en' => 'tags and price lists',
                ),
                5 =>
                array(
                    'image_src' => '/new/img/main/poligraph-production/6.png',
                    'link_href' => '/catalog/plakaty',
                    'link_text_uk' => 'плакати',
                    'link_text_ru' => 'плакаты',
                    'link_text_en' => 'posters',
                ),
                6 =>
                array(
                    'image_src' => '/new/img/main/poligraph-production/7.png',
                    'link_href' => '/catalog/korobki',
                    'link_text_uk' => 'коробки',
                    'link_text_ru' => 'коробки',
                    'link_text_en' => 'boxes',
                ),
                7 =>
                array(
                    'image_src' => '/new/img/main/poligraph-production/8.png',
                    'link_href' => '/catalog/nakleyki',
                    'link_text_uk' => 'наклейки',
                    'link_text_ru' => 'наклейки',
                    'link_text_en' => 'stickers',
                ),
                8 =>
                array(
                    'image_src' => '/img/disignweb/папки.png',
                    'link_href' => '/catalog/papki',
                    'link_text_uk' => 'папки',
                    'link_text_ru' => 'папки',
                    'link_text_en' => 'folders',
                ),
                9 =>
                array(
                    'image_src' => '/img/disignweb/меню.png',
                    'link_href' => '/catalog/menyu',
                    'link_text_uk' => 'щоденник 2023',
                    'link_text_ru' => 'ежедневник 2023',
                    'link_text_en' => 'diary 2023',
                ),
                10 =>
                array(
                    'image_src' => '/img/disignweb/открытки.png',
                    'link_href' => '/catalog/otkrytki',
                    'link_text_uk' => 'листівки',
                    'link_text_ru' => 'открытки',
                    'link_text_en' => 'greeting cards',
                ),
                11 =>
                array(
                    'image_src' => '/img/disignweb/карты.png',
                    'link_href' => '/catalog/plastik',
                    'link_text_uk' => 'пластикові картки',
                    'link_text_ru' => 'пластиковые карты',
                    'link_text_en' => 'plastic cards',
                ),
                12 =>
                array(
                    'image_src' => '/img/disignweb/конверты.png',
                    'link_href' => '/catalog/konverty',
                    'link_text_uk' => 'конверти',
                    'link_text_ru' => 'конверты',
                    'link_text_en' => 'envelopes',
                ),
                13 =>
                array(
                    'image_src' => '/img/disignweb/календари.png',
                    'link_href' => '/catalog/kalendari',
                    'link_text_uk' => 'календарі',
                    'link_text_ru' => 'календари',
                    'link_text_en' => 'calendars',
                ),
                14 =>
                array(
                    'image_src' => '/img/disignweb/буклеты.png',
                    'link_href' => '/catalog/buklety',
                    'link_text_uk' => 'буклети',
                    'link_text_ru' => 'брошюры',
                    'link_text_en' => 'brochures',
                ),
                15 =>
                array(
                    'image_src' => '/img/disignweb/брошюры.png',
                    'link_href' => '/catalog/broshyury',
                    'link_text_uk' => 'брошури',
                    'link_text_ru' => 'брошюры',
                    'link_text_en' => 'brochures',
                ),
                16 =>
                array(
                    'image_src' => '/img/disignweb/бланки.png',
                    'link_href' => '/catalog/blanki',
                    'link_text_uk' => 'бланки',
                    'link_text_ru' => 'бланки',
                    'link_text_en' => 'forms',
                ),
                17 =>
                array(
                    'image_src' => '/img/disignweb/блокноты.png',
                    'link_href' => '/catalog/bloknoty',
                    'link_text_uk' => 'блокноти',
                    'link_text_ru' => 'блокноты',
                    'link_text_en' => 'notebooks',
                ),
                18 =>
                array(
                    'image_src' => '/img/disignweb/пакеты.png',
                    'link_href' => '/catalog/pakety',
                    'link_text_uk' => 'пакети',
                    'link_text_ru' => 'пакеты',
                    'link_text_en' => 'bags',
                ),
                19 =>
                array(
                    'image_src' => '/img/disignweb/стаканчики.png',
                    'link_href' => '/catalog/bumazhnye_stakany',
                    'link_text_uk' => 'стакани',
                    'link_text_ru' => 'стаканы',
                    'link_text_en' => 'cups',
                ),
            );

            foreach ($data as $sort => $item) {
                $model = new common\models\Widget();
                $model->old_image = $item["image_src"];
                $model->link_href = $item["link_href"];
                $model->link_text_ru = $item["link_text_ru"];
                $model->link_text_uk = $item["link_text_uk"];
                $model->link_text_en = $item["link_text_en"];
                $model->active = 1;
                $model->type = 1;
                $model->sort = $sort;
                $model->save();
            }
        }
        ?><? if (true): ?>

            <? foreach ($widgets as $k => $item): ?>

                <div class="product-block__item <? if ($k > 3): ?>product-block__item-hidden hidden<? endif; ?>">
                    <?= $item->getEditLink("&type=" . $item->type) ?>
                    <a href="<?= $item["link"] ?>" class="img-link">
                        <img src="<?= $item["ico"] ?>" />
                    </a>    
                    <a href="<?= $item["link"] ?>" class="btn product-block__item-btn"><?= $item["link_text"] ?></a>
                </div>
            <? endforeach; ?>
        <? else: ?>
            <div class="product-block__item">
                <img src="/new/img/main/poligraph-production/1.png" />
                <a href="<?= Url::to(['catalog/show', 'translit' => 'vizitki']) ?>" class="btn product-block__item-btn"><?= mb_strtolower(Yii::t('app', 'ВИЗИТКИ')) ?></a>
            </div>
            <div class="product-block__item">
                <img src="/new/img/main/poligraph-production/2.png" />
                <a href="<?= Url::to(['catalog/show', 'translit' => 'katalogi']) ?>" class="btn product-block__item-btn"><?= mb_strtolower(Yii::t('app', 'КАТАЛОГИ')) ?></a>
            </div>
            <div class="product-block__item">
                <img src="/new/img/main/poligraph-production/3.png" />
                <a href="<?= Url::to(['catalog/show', 'translit' => 'listovki']) ?>" class="btn product-block__item-btn"><?= mb_strtolower(Yii::t('app', 'ЛИСТОВКИ')) ?></a>
            </div>
            <div class="product-block__item">
                <img src="/new/img/main/poligraph-production/4.png" />
                <a href="<?= Url::to(['catalog/show', 'translit' => 'flaery']) ?>" class="btn product-block__item-btn"><?= mb_strtolower(Yii::t('app', 'ФЛАЕРА')) ?></a>
            </div>
            <div class="product-block__item product-block__item-hidden hidden">
                <img src="/new/img/main/poligraph-production/5.png" />
                <a href="<?= Url::to(['catalog/show', 'translit' => 'birki_i_cenniki']) ?>" class="btn product-block__item-btn"><?= mb_strtolower(Yii::t('app', 'БИРКИ И ЦЕННИКИ')) ?></a>
            </div>
            <div class="product-block__item product-block__item-hidden hidden">
                <img src="/new/img/main/poligraph-production/6.png" />
                <a href="<?= Url::to(['catalog/show', 'translit' => 'plakaty']) ?>" class="btn product-block__item-btn"><?= mb_strtolower(Yii::t('app', 'ПЛАКАТЫ')) ?></a>
            </div>
            <div class="product-block__item product-block__item-hidden hidden">
                <img src="/new/img/main/poligraph-production/7.png" />
                <a href="<?= Url::to(['catalog/show', 'translit' => 'korobki']) ?>" class="btn product-block__item-btn"><?= mb_strtolower(Yii::t('app', 'КОРОБКИ')) ?></a>
            </div>
            <div class="product-block__item product-block__item-hidden hidden">
                <img src="/new/img/main/poligraph-production/8.png" />
                <a href="<?= Url::to(['catalog/show', 'translit' => 'nakleyki']) ?>" class="btn product-block__item-btn"><?= mb_strtolower(Yii::t('app', 'НАКЛЕЙКИ')) ?></a>
            </div>
            <div class="product-block__item product-block__item-hidden hidden">
                <img src="<?= Yii::$app->request->baseUrl ?>/img/disignweb/папки.png" />
                <a href="<?= Url::to(['catalog/show', 'translit' => 'papki']) ?>" class="btn product-block__item-btn"><?= mb_strtolower(Yii::t('app', 'ПАПКИ')) ?></a>
            </div>
            <div class="product-block__item product-block__item-hidden hidden">
                <img src="<?= Yii::$app->request->baseUrl ?>/img/disignweb/меню.png" />
                <a href="<?= Url::to(['catalog/show', 'translit' => 'menyu']) ?>" class="btn product-block__item-btn"><?= mb_strtolower(Yii::t('app', 'МЕНЮ')) ?></a>
            </div>
            <div class="product-block__item product-block__item-hidden hidden">
                <img src="<?= Yii::$app->request->baseUrl ?>/img/disignweb/открытки.png" />
                <a href="<?= Url::to(['catalog/show', 'translit' => 'otkrytki']) ?>" class="btn product-block__item-btn"><?= mb_strtolower(Yii::t('app', 'ОТКРЫТКИ')) ?></a>
            </div>
            <div class="product-block__item product-block__item-hidden hidden">
                <img src="<?= Yii::$app->request->baseUrl ?>/img/disignweb/карты.png" />
                <a href="<?= Url::to(['catalog/show', 'translit' => 'plastik']) ?>" class="btn product-block__item-btn"><?= mb_strtolower(Yii::t('app', 'ПЛАСТИКОВЫЕ КАРТЫ')) ?></a>
            </div>
            <div class="product-block__item product-block__item-hidden hidden">
                <img src="<?= Yii::$app->request->baseUrl ?>/img/disignweb/конверты.png" />
                <a href="<?= Url::to(['catalog/show', 'translit' => 'konverty']) ?>" class="btn product-block__item-btn"><?= mb_strtolower(Yii::t('app', 'КОНВЕРТЫ')) ?></a>
            </div>
            <div class="product-block__item product-block__item-hidden hidden">
                <img src="<?= Yii::$app->request->baseUrl ?>/img/disignweb/календари.png" />
                <a href="<?= Url::to(['catalog/show', 'translit' => 'kalendari']) ?>" class="btn product-block__item-btn"><?= mb_strtolower(Yii::t('app', 'КАЛЕНДАРИ')) ?></a>
            </div>
            <div class="product-block__item product-block__item-hidden hidden">
                <img src="<?= Yii::$app->request->baseUrl ?>/img/disignweb/буклеты.png" />
                <a href="<?= Url::to(['catalog/show', 'translit' => 'buklety']) ?>" class="btn product-block__item-btn"><?= mb_strtolower(Yii::t('app', 'БУКЛЕТЫ')) ?></a>
            </div>
            <div class="product-block__item product-block__item-hidden hidden">
                <img src="<?= Yii::$app->request->baseUrl ?>/img/disignweb/брошюры.png" />
                <a href="<?= Url::to(['catalog/show', 'translit' => 'broshyury']) ?>" class="btn product-block__item-btn"><?= mb_strtolower(Yii::t('app', 'БРОШЮРЫ')) ?></a>
            </div>
            <div class="product-block__item product-block__item-hidden hidden">
                <img src="<?= Yii::$app->request->baseUrl ?>/img/disignweb/бланки.png" />
                <a href="<?= Url::to(['catalog/show', 'translit' => 'blanki']) ?>" class="btn product-block__item-btn"><?= mb_strtolower(Yii::t('app', 'БЛАНКИ')) ?></a>
            </div>
            <div class="product-block__item product-block__item-hidden hidden">
                <img src="<?= Yii::$app->request->baseUrl ?>/img/disignweb/блокноты.png" />
                <a href="<?= Url::to(['catalog/show', 'translit' => 'bloknoty']) ?>" class="btn product-block__item-btn"><?= mb_strtolower(Yii::t('app', 'БЛОКНОТЫ')) ?></a>
            </div>
            <div class="product-block__item product-block__item-hidden hidden">
                <img src="<?= Yii::$app->request->baseUrl ?>/img/disignweb/пакеты.png" />
                <a href="<?= Url::to(['catalog/show', 'translit' => 'pakety']) ?>" class="btn product-block__item-btn"><?= mb_strtolower(Yii::t('app', 'ПАКЕТЫ')) ?></a>
            </div>
            <div class="product-block__item product-block__item-hidden hidden">
                <img src="<?= Yii::$app->request->baseUrl ?>/img/disignweb/стаканчики.png" />
                <a href="<?= Url::to(['catalog/show', 'translit' => 'bumazhnye_stakany']) ?>" class="btn product-block__item-btn"><?= mb_strtolower(Yii::t('app', 'СТАКАНЫ')) ?></a>
            </div>
        <? endif; ?>
        <button id="show-more-poligraph" class="product-block__item-more">
            <div class="product-block__item-more-btn"><?= Yii::t('app', 'More') ?><? /* Більше */ ?></div>
        </button>
    </div>
</section>
<?
//return;
/*
$dom = new DOMDocument();
$html = str_replace("product-block__item product-block__item-hidden hidden", "product-block__item", $html);
$dom->loadHTML('<?xml encoding="utf-8" ?>' . $html); // Specify the correct character encoding

$productItems = [];

$divElements = $dom->getElementsByTagName('div');
foreach ($divElements as $divElement) {
    if ($divElement->getAttribute('class') === 'product-block__item') {
        $productItem = [];

        $imgElement = $divElement->getElementsByTagName('img')->item(0);
        $productItem['image_src'] = $imgElement->getAttribute('src');

        $linkElement = $divElement->getElementsByTagName('a')->item(0);
        $productItem['link_href'] = $linkElement->getAttribute('href');
        $productItem['link_text_uk'] = $linkElement->nodeValue;

        $productItems[] = $productItem;
    }
}
*/
?>