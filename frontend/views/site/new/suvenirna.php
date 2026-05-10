<?

use yii\helpers\Url;
?>
<section class="container">
    <div class="title-block">
        <h1 class="section-title"><?= Yii::t('app', 'СУВЕНИРНАЯ ПРОДУКЦИЯ') ?></h1>
        <p class="product-block-title">(<?= Yii::t('app', 'Дизайн и печать') ?>)</p>
    </div>
    <div class="products-block">
        <?
        if (empty($widgets_suveniry)) {
            $data = array(
                0 =>
                array(
                    'image_src' => '/new/img/main/souvernir-production/1.png',
                    'link_href' => '/catalog/salfetki',
                    'link_text_uk' => 'салфетки',
                    'link_text_ru' => 'салфетки',
                    'link_text_en' => 'napkins',
                ),
                1 =>
                array(
                    'image_src' => '/new/img/main/souvernir-production/2.png',
                    'link_href' => '/catalog/spichki',
                    'link_text_uk' => 'спички',
                    'link_text_ru' => 'спички',
                    'link_text_en' => 'matches',
                ),
                2 =>
                array(
                    'image_src' => '/new/img/main/souvernir-production/3.png',
                    'link_href' => '/catalog/ruchki',
                    'link_text_uk' => 'ручки',
                    'link_text_ru' => 'ручки',
                    'link_text_en' => 'pens',
                ),
                3 =>
                array(
                    'image_src' => '/new/img/main/souvernir-production/4.png',
                    'link_href' => '/catalog/futbolki',
                    'link_text_uk' => 'футболки',
                    'link_text_ru' => 'футболки',
                    'link_text_en' => 't-shirts',
                ),
                4 =>
                array(
                    'image_src' => '/new/img/main/souvernir-production/5.png',
                    'link_href' => '/catalog/kepki',
                    'link_text_uk' => 'кепки',
                    'link_text_ru' => 'кепки',
                    'link_text_en' => 'caps',
                ),
                5 =>
                array(
                    'image_src' => '/new/img/main/souvernir-production/6.png',
                    'link_href' => '/catalog/zonty',
                    'link_text_uk' => 'зонты',
                    'link_text_ru' => 'зонты',
                    'link_text_en' => 'umbrellas',
                ),
                6 =>
                array(
                    'image_src' => '/new/img/main/souvernir-production/7.png',
                    'link_href' => '/catalog/magnity',
                    'link_text_uk' => 'магниты',
                    'link_text_ru' => 'магниты',
                    'link_text_en' => 'magnets',
                ),
                7 =>
                array(
                    'image_src' => '/new/img/main/souvernir-production/8.png',
                    'link_href' => '/catalog/chashki',
                    'link_text_uk' => 'чашки',
                    'link_text_ru' => 'чашки',
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
                $model->type = 2;
                $model->sort = $sort;
                $model->save();
            }
        }
        ?>
        <? if (true): ?>
            <? foreach ($widgets_suveniry as $k => $item): ?>

                <div class="product-block__item <? if ($k > 3): ?>product-block__item-hidden-souvenir hidden<? endif; ?>">
                    <?= $item->getEditLink("&type=" . $item->type) ?>
                    <a href="<?= $item["link"] ?>" class="img-link">
                        <img src="<?= $item["ico"] ?>" />
                    </a>    
                    <a href="<?= $item["link"] ?>" class="btn product-block__item-btn"><?= $item["link_text"] ?></a>
                </div>
            <? endforeach; ?>
        <? else: ?>
            <div class="product-block__item">
                <img src="/new/img/main/souvernir-production/1.png" />
                <a href="<?= Url::to(['catalog/show', 'translit' => 'salfetki']) ?>" class="btn product-block__item-btn"><?= mb_strtolower(Yii::t('app', 'САЛФЕТКИ')) ?></a>
            </div>
            <div class="product-block__item">
                <img src="/new/img/main/souvernir-production/2.png" />
                <a href="<?= Url::to(['catalog/show', 'translit' => 'spichki']) ?>" class="btn product-block__item-btn"><?= mb_strtolower(Yii::t('app', 'СПИЧКИ')) ?></a>
            </div>
            <div class="product-block__item">
                <img src="/new/img/main/souvernir-production/3.png" />
                <a href="<?= Url::to(['catalog/show', 'translit' => 'ruchki']) ?>" class="btn product-block__item-btn"><?= mb_strtolower(Yii::t('app', 'РУЧКИ')) ?></a>
            </div>
            <div class="product-block__item">
                <img src="/new/img/main/souvernir-production/4.png" />
                <a href="<?= Url::to(['catalog/show', 'translit' => 'futbolki']) ?>" class="btn product-block__item-btn"><?= mb_strtolower(Yii::t('app', 'ФУТБОЛКИ')) ?></a>
            </div>
            <div class="product-block__item product-block__item-hidden-souvenir hidden">
                <img src="/new/img/main/souvernir-production/5.png" />
                <a href="<?= Url::to(['catalog/show', 'translit' => 'kepki']) ?>" class="btn product-block__item-btn"><?= mb_strtolower(Yii::t('app', 'КЕПКИ')) ?></a>
            </div>
            <div class="product-block__item product-block__item-hidden-souvenir hidden">
                <img src="/new/img/main/souvernir-production/6.png" />
                <a href="<?= Url::to(['catalog/show', 'translit' => 'zonty']) ?>" class="btn product-block__item-btn"><?= mb_strtolower(Yii::t('app', 'ЗОНТЫ')) ?></a>
            </div>
            <div class="product-block__item product-block__item-hidden-souvenir hidden">
                <img src="/new/img/main/souvernir-production/7.png" />
                <a href="<?= Url::to(['catalog/show', 'translit' => 'magnity']) ?>" class="btn product-block__item-btn"><?= mb_strtolower(Yii::t('app', 'МАГНИТЫ')) ?></a>
            </div>
            <div class="product-block__item product-block__item-hidden-souvenir hidden">
                <img src="/new/img/main/souvernir-production/8.png" />
                <a href="<?= Url::to(['catalog/show', 'translit' => 'chashki']) ?>" class="btn product-block__item-btn"><?= mb_strtolower(Yii::t('app', 'ЧАШКИ')) ?></a>
            </div> 
        <? endif; ?>
        <button id="show-more-souvenir" class="product-block__item-more">
            <div class="product-block__item-more-btn"><? /* Більше */ ?><?= Yii::t('app', 'More') ?></div>
        </button>
    </div>
</section>