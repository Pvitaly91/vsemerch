<?
use yii\helpers\Url;
use dmstr\widgets\Alert;
$this->title = $text->meta_title;
$this->registerMetaTag(['name' => 'description', 'content' => $text->meta_description]);
$this->registerMetaTag(['name' => 'keywords', 'content' => $text->meta_keywords]);
?>


<? $this->registerCssFile('/new/css/styles.css');?>
<? $this->registerCssFile('/new/css/form.css');?>
<? $this->registerCssFile('/new/css/main-page-fix.css');?>


<div class="slider-block">
  
			<div class="slider-block__description">
				<p><?= Yii::t('app', 'We will help your business to work') ?><?/*Допоможемо вашому бізнесу працювати*/?><strong><?= Yii::t('app', 'more effective') ?><?/*ефективніше*/?>!</strong></p>
				<svg
					width="246"
					height="10"
					viewBox="0 0 246 10"
					fill="none"
					xmlns="http://www.w3.org/2000/svg"
				>
					<path
						fill-rule="evenodd"
						clip-rule="evenodd"
						d="M245.173 2.74933C244.216 1.73595 235.644 1.35428 209.841 1.13901C185.766 0.932104 157.763 0.10347 108.741 0.909667C71.2694 1.52713 24.5636 3.42857 24.5245 4.3402C24.4962 5.00158 26.3136 5.00944 43.7021 4.3239C59.7702 3.69925 68.0027 3.5288 78.0541 3.33046C37.7225 4.94659 29.0927 5.90555 7.60112 6.89962C-5.87688 7.52788 1.91851 9.07544 6.83439 8.88752C6.87984 8.88727 69.5552 6.54044 75.4596 6.61514C76.0042 6.63001 70.0361 6.98456 62.1565 7.40326C40.1565 8.59672 44.3072 10.3608 60.7966 9.44786C116.278 6.37221 168.404 5.22723 208.489 6.20376C221.154 6.50927 222.563 6.50151 222.583 6.03675C222.618 5.2145 219.191 4.60808 213.787 4.51275C205.342 4.34483 172.823 3.91619 158.104 3.81857C156.968 3.80695 157.528 3.4645 158.53 3.42324C164.539 3.15781 212.167 3.07395 230.883 3.22103C244.648 3.32391 245.649 3.3007 245.173 2.74933Z"
						fill="#DB4444"
					/>
				</svg>
				<button class="btn slider-block__btn" onclick="openModal()" ><?= Yii::t('app', 'Order feedback') ?><?/*Замовити зворотній зв’язок*/?></button>
			</div>
			<? if($banners):?>
				<div class="swiper-1 swiper">
					<div class="swiper-wrapper-1 swiper-wrapper">
						<? foreach($banners as $banner):?>
						<div class="swiper-slide-1 swiper-slide">
							<img src="<?=$banner->bannerPath.$banner->image?>" />
						</div>
						<? endforeach;?>
						
						
					</div>
					<div class="swiper-pagination"></div>
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
			<? endif;?>	
		</div>    
        <main class="container">
            
			<? require 'suvenirna.php';?>
			<!--Поліграфічна продукція-->
			<? require 'poligrafia.php';?>
			<!--Зовнішня реклама і широкоформатний дух-->
			<? require 'outer_ads.php';?>
			<!--Дизайн послуги-->
			<? require 'design.php';?>
			<!--Новини і акції-->
			<? require 'news.php';?>
			<section class="container">
				<div class="callback-block">
					<h2>
						<?= Yii::t('app', 'Закажите обратный звонок чтобы узнать подробнее об интересующей Вас информации!') ?>
					</h2>
					<button class="btn slider-block__btn" onclick="openModal()"><?= Yii::t('app', 'Order feedback') ?><?/*Замовити зворотній зв’язок*/?></button>
					<p><?= Yii::t('app', 'Learn more in social networks') ?><?/*Дізнавайся більше у соціальних мережах*/?></p>
					<div class="socials">
                        <? include Yii::$app->viewPath."/layouts/social.php";?>
						
					</div>
				</div>
                
			</section>
            <? include "popup.php"?>
            
		</main>
<script src="/new/js/main-page/_showMore.js"></script>