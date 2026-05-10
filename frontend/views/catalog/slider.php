
<?if(is_array($photos) && !empty($photos)):?>

    <h3><?=Yii::t('app', 'OUR WORK')?></h3>

    <div class="slider-block__wrapper">
        <div class="slider-block">
            <div class="swiper-button-prev-3 swiper-button-prev">
                <svg
                    width="50"
                    height="50"
                    viewBox="0 0 50 50"
                    fill="none"
                    xmlns="http://www.w3.org/2000/svg"
                    >
                    <circle cx="25" cy="25" r="25" transform="rotate(-180 25 25)" fill="#DB4444" />
                    <path
                        d="M16.9373 25.7719C16.4516 25.3719 16.4516 24.6281 16.9373 24.2281L28.2359 14.9234C29.1118 14.2021 30.3219 15.266 29.7185 16.227L24.211 24.9999L29.7186 33.7731C30.3219 34.7341 29.1119 35.798 28.236 35.0767L16.9373 25.7719Z"
                        fill="white"
                        />
                </svg>
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
            <div class="swiper-3 swiper">
              
                <div class="swiper-wrapper-3 swiper-wrapper">
                    <?foreach($photos as $item):?>
                        <div class="swiper-slide-3 swiper-slide" big="<?= $item["big"] ?>" 
                            title="<?=$item["title"] ?>" >
                            <a href="<?=$item["big"]  ?>"  class="fancybox">
                                <img src="<?=$item["src"]  ?>" alt="<?= $item["alt"] ?>" />
                            </a>
                        </div>
                    
                    <?endforeach;?>
                    <?/*
                <div class="swiper-pagination"></div>*/?>
            </div>
        </div>
    </div>
<?endif;?>