<?

use yii\widgets\Breadcrumbs;
use yii\web\View;

?>
<?
$this->title = $text->meta_title;
$this->registerMetaTag(['name' => 'description', 'content' => $text->meta_description]);
$this->registerMetaTag(['name' => 'keywords', 'content' => $text->meta_keywords]);

?>
<div class="head2">
    <div class="container">
        <div class="info2 col-md-12">
            <h1><?= $text->title; ?></h1>


            <nav class="breadcrumbs">
                <?= Breadcrumbs::widget([
                    'homeLink' => [
                        'label' => Yii::t('yii', 'Home'),
                        'url' => ['site/index'],
                    ],
                    'links' => [$text->title],
                ]) ?>
            </nav>
        </div>
    </div>
</div>
<div class="body_box">
    <div class="top2"></div>
    <div class="content">
        <div class="container cnt">



                    <div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
                        <ol class="carousel-indicators">
                            <?php foreach ($model as $key => $item):?>
                            <li data-target="#carousel-example-generic" data-slide-to="<?=$key?>" class="<?=($key===0 ? 'active' : '')?>"></li>
                            <?php endforeach;?>
                        </ol>

                        <div class="carousel-inner" role="listbox">


                            <?php foreach ($model as $key => $item):?>
                            <div class="item <?=($key===0 ? 'active' : '')?>">
                                <img src="/upload/portfolio-slider/<?=$item->image?>" alt="<?=$item->title?>" />

                                <?if(!empty($item->body)):?>
                                <div class="carousel-caption">
                                    <?if(!empty($item->title)):?>
                                    <h2><?=$item->title?></h2>
                                    <?endif;?>
                                    <p><?=$item->body?></p>
                                </div>
                                <?endif;?>
                            </div>
                            <?php endforeach;?>

                        </div>

                        <a class="left carousel-control" href="#carousel-example-generic" role="button" data-slide="prev">
                            <span class="glyphicon glyphicon-chevron-left"></span>
                        </a>

                        <a class="right carousel-control" href="#carousel-example-generic" role="button" data-slide="next">
                            <span class="glyphicon glyphicon-chevron-right"></span>
                        </a>
                    </div>




            <?= $text->body; ?>

        </div>

    </div>
</div>
</div>