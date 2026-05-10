<?

use yii\helpers\Url;
?>
<section class="container">
    <div class="title-block-2">
        <h1 class="section-title"><?= Yii::t('app', 'НАРУЖНАЯ РЕКЛАМА И ШИРОКОФОРМАТНАЯ ПЕЧАТЬ') ?></h1>
        <p class="product-block-title">(<?= Yii::t('app', 'дизайн, печать,производсво') ?>)</p>
    </div>
    <div class="slider-block-2">
        <div class="description-title-wrapper slider-block__description">
            <p class="slider-block__description-title">
<?= Yii::t('app', 'Design, printing and production of outdoor advertising, banners') ?><? /* Дизайн, друк і виробництво зовнішньої реклами, банерів */ ?>

            </p>
            <div class="btn-container">
                <a href="<?= Url::to(['production/show', 'slug' => 'naruzhnaya_reklama']) ?>" class="btn-more btn-more-2"><?= Yii::t('app', 'Подробнее') ?></a>
                <button class="btn slider-block__btn" onclick="openModal()"><?= Yii::t('app', 'Order feedback') ?><? /* Замовити зворотній зв’язок */ ?></button>
            </div>
        </div>
        <div class="swiper-2 swiper">
            <div class="swiper-wrapper-2 swiper-wrapper">
                <div class="swiper-slide-2 swiper-slide">
                    <img src="/new/img/main/slider-block-2/1.png" />
                </div>
                <div class="swiper-slide-2 swiper-slide">
                    <img src="/new/img/main/slider-block-2/2.png" />
                </div>

            </div>
            <div class="swiper-pagination"></div>
        </div>
        <div class="swiper-button-next">
            <svg
                width="50"
                height="50"
                viewBox="0 0 50 50"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
                >
                <circle cx="25" cy="25" r="25" fill="#DB4444" />
                <path
                    d="M33.0627 24.2281C33.5484 24.6281 33.5484 25.3719 33.0627 25.7719L21.7641 35.0766C20.8882 35.798 19.6781 34.734 20.2815 33.773L25.789 25.0001L20.2814 16.2269C19.6781 15.2659 20.8881 14.202 21.764 14.9233L33.0627 24.2281Z"
                    fill="white"
                    />
            </svg>
        </div>
    </div>
</section>