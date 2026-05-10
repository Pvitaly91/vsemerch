<?

use yii\widgets\Breadcrumbs;
use yii\web\View;
?>
<?
$this->title = $text->meta_title;
$this->registerMetaTag(['name' => 'description', 'content' => $text->meta_description]);
$this->registerMetaTag(['name' => 'keywords', 'content' => $text->meta_keywords]);
$breadcrumbs[] = [
    "link" => \yii\helpers\Url::to(['site/index']),
    "label" => Yii::t('yii', 'Home')
];

$breadcrumbs[] = [
    "label" => $text->title,
];
?>
<? $this->registerCssFile('/new/css/gallery.css');?>
<main class="container">
    <section>
        <?= $this->render('/catalog/breadcrumbs', ['breadcrumbs' => $breadcrumbs]) ?>
        <h1><?=$text->title?></h1>
        <div class="topPhotos">
            <?php foreach ($model as $key => $item): ?>
                <div class="photo-item <?= ($key === 0 ? 'active' : '') ?>">
                    <a href="/upload/portfolio-slider/<?= $item->image ?>" class="fancybox">
                    <img src="/upload/portfolio-slider/<?= $item->image ?>" alt="<?= $item->title ?>" />
                    </a>
                    <? if (  !empty($item->body)): ?>
                        <div class="carousel-caption">
                            <? if (!empty($item->title)): ?>
                                <h2><?= $item->title ?></h2>
                            <? endif; ?>
                           <?/* <p><?= $item->body ?></p>*/?>
                        </div>
                    <? endif; ?>
                </div>
            <?php endforeach; ?>
        </div>    
    </section>
</main>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.js" integrity="sha512-uURl+ZXMBrF4AwGaWmEetzrd+J5/8NRkWAvJx5sbPSSuOb0bZLqf+tOzniObO00BjHa/dD7gub9oCGMLPQHtQA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.css" integrity="sha512-H9jrZiiopUdsLpg94A333EfumgUBpO9MdbxStdeITo+KEIMaNfHNvwyjjDJb+ERPaRS6DpyRlKbvPUasNItRyw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script>
        $(document).ready(function(){
            $('.fancybox').fancybox();
        })
    </script>