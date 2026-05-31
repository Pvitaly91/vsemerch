<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\Markdown;

?>
<?php /** @var $model \common\models\Product */

if($model->is_main == null || Yii::$app->request->get('filters') != NULL)
    $model->checkAvaiableSize();

//dd($model->attributes);
?>
<div class="col-md-4">
    <div class="well item_product">
        <div class="col-xs-12">
        <?if($model->action):?>
            <span class="action"><?=Yii::t('shop', 'Action')?></span>
        <?endif;?>
        <?if($model->novelty):?>
            <span class="novelty"><?=Yii::t('shop', 'Novelty one')?></span>
        <?endif;?>
        </div>
        <div class="col-md-12">
            <a href="<?=Url::to(['/shop/product/view', 'slug' => $model->slug, 'id' => $model->id])?>">
            <?php

            echo Html::img($model->getLazyPic('thumb'), ['class' => 'img-responsive', 'alt' => Html::encode($model->title)]);

            ?>
            </a>
        </div>
        <div class="col-xs-12">
            <a class="link_name" href="<?=Url::to(['/shop/product/view', 'slug' => $model->slug, 'id' => $model->id])?>"><h4><?= Html::encode($model->title) ?></h4></a>
        </div>

        <div class="col-xs-12">
            <div class="row">
                <div class="col-xs-8 price">
                    <?if($model->price > 0):?>
                        <p><?= $model->price ?> &#8372;</p>
                    <?endif;?>
                    <?if($model->price > 0 && $model->price_old > 0):?>
                        <p><strike><?= $model->price_old ?> &#8372;</strike></p>
                    <?elseif($model->price == 0 && $model->price_old > 0):?>
                        <p><?= $model->price_old ?> &#8372;</p>
                    <?endif;?>
                    <?if($model->not_available == 1):?>
                        <p class="not_available"><?=Yii::t('shop', 'Not available')?></p>
                    <?else:?>
                        <p class="are_available"><?=Yii::t('shop', 'Are available')?></p>
                    <?endif;?>
                </div>
                <?if($model->price > 0 && $model->not_available == 0):?>
                    <div class="col-xs-4"><?= Html::a('<span class="glyphicon glyphicon-shopping-cart"></span>', ['cart/add', 'id' => $model->id],
                            [
                                'class' => 'btn btn-danger add_to_cart',
                                'data-price' => $model->price, 
                                'data-id' => $model->code, 
                            ]) ?></div>
                <?endif;?>
            </div>
        </div>
    </div>
</div>
