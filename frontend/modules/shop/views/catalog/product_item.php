<?
use yii\helpers\Html;

use yii\helpers\Url;


$listThumbUrl = $model->getLazyPic('thumb');
?>
<div class="products-block__card <? if ($middle == $k): ?>middle-item<? endif; ?>"  id="prod_<?= $model->id ?>">
        <? if($model->isAdmin() && (isset(\common\Helpers\Partners::$partners[$model->partner]))):?>
            <label style="margin-left:15px; "><input  type="checkbox" name="productsId[]" value="<?=$model->id ?>"> Переместить продукт</label>
        <? endif;?>    
        <div class="products-block__card-wrapper">
            <? if(is_array($colors[$model->id]) && count($colors[$model->id]) > 1):?>
                <? if (isset($colors[$model->id])): ?>
                    <div class="products-block__colors" id="colors_<?=$model->id?>">
                        <?
                        $firstUlr = false;
                        $i =0;
                        foreach ($colors[$model->id] as $id => $color):
                            $i++;
                            if($firstUlr == false && isset($colorModel))
                               $firstUlr = Url::to(['/shop/product/view', 'slug' => $colorModel->slug, 'id' => $colorModel->id]); 
                            ?>
                            <? $colorModel = $color["model"]; ?>
                            <? $colorThumbUrl = $colorModel->getLazyPic('thumb'); ?>
                            <button class="products-block__colors-item <? if ($model->id == $colorModel->id): ?>color-active<? endif; ?>" id="color_<?= $model->id ?>" color-id="<?= $colorModel->id ?>"  data-id="<?= $model->id ?>"

                                    data-url="<?= Url::to(['/shop/product/view', 'slug' => $colorModel->slug, 'id' => $colorModel->id]) ?>"
                                    data-image="<?= $colorThumbUrl ?>"
                                    data-price="<?= $colorModel->price ?>"
                                    data-code="<?= $colorModel->code ?>"
                                    data-id="<?= $colorModel->id ?>"
                                    data-no-available="<?= $colorModel->not_available ?>"
                                    ><img style="margin-left:0px;" src="<?= $colorThumbUrl ?>" alt="<?= Html::encode($colorModel->title) ?>"> </button>
                        <? endforeach; ?>    
                    </div>
                <? endif; ?>
                <? /*  <a class="url_<?=$model->id?>" href="<?=Url::to(['/shop/product/view', 'slug' => $model->slug, 'id' => $model->id])?>"> */ ?>
                 
                    <? echo Html::img($listThumbUrl, ['alt' => Html::encode($model->title), "id" => "img_" . $model->id,"data-id" => $model->id, "class" => "main-page", "_data-url" =>  $firstUlr ]); ?>
                <? /* </a> */ ?>
            <? else:?>
                <? //echo Html::img($model->getPic('image', 'thumb', '/img/no_image.jpg'), ['alt' => Html::encode($model->title)]); ?>
                <a href="<?= Url::to(['/shop/product/view', 'slug' => $model->slug, 'id' => $model->id]) ?>"  
                        data-price="<?= $model->price ?>"
                        data-code="<?= $model->code ?>"
                        id="color_<?= $model->id ?>"
                >
                    <img class="no-colors" src="<?=$listThumbUrl?>" alt="<?= Html::encode($model->title)?>">
                </a>
            <? endif;?>
        </div>
    
    <p class="products-block__card-title"> <a id="url_<?= $model->id ?>" class="url_<?= $model->id ?>" href="<?= Url::to(['/shop/product/view', 'slug' => $model->slug, 'id' => $model->id]) ?>"><? if($model->partner == "eney")?><?= Html::encode($model->title) ?></a></p>
    <div class="products-block__card-footer1">
         <? if ($model->price > 0): ?>
            <p class="products-block__card-price" id="price_<?= $model->id ?>"><?= $model->price ?> &#8372;</p>
        <?else:?>
            <? if ($model->price > 0 && $model->price_old > 0 && $model->price_old < $model->price): ?>
                <p class="products-block__card-price"><strike><?= $model->price_old ?> &#8372;</strike></p>
            <? elseif ($model->price == 0 && $model->price_old > 0): ?>
                <p class="products-block__card-price"><?= $model->price_old ?> &#8372;</p>
            <? endif; ?>
        <? endif; ?>
        <? if ($model->not_available == 1): ?>
            <span class="product-no-available product-status <? if (!empty($model->size) && count($model->size) >1): ?>is-size<? endif;?>" id="no-available_<?= $model->id ?>"><?= Yii::t('shop', 'Not available') ?></span>
        <? else: ?>
            <span class="<? if (!empty($model->size) && count($model->size) >1): ?>is-size<? endif;?> <? if (empty($model->size) && count($model->size) < 1): ?>_product-available<? endif; ?> product-available product-status" id="no-available_<?= $model->id ?>"><?= Yii::t('shop', 'Are available') ?></span>
        <? endif; ?>

    </div>
    <? if (empty($model->size) && count($model->size) < 1): ?>
        <button class="products-block__cart-btncart <? if ($model->not_available == 1): ?>zero-opacity<? endif; ?>" id="cart_<?= $model->id ?>" data-id="<?= $model->id ?>" >
            <svg
                width="20"
                height="20"
                viewBox="0 0 20 20"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
                >
                <path
                    d="M6.00179 20C5.45162 20 4.98048 19.804 4.58837 19.412C4.19625 19.02 4.00052 18.5493 4.00119 18C4.00119 17.45 4.19725 16.979 4.58937 16.587C4.98148 16.195 5.45229 15.9993 6.00179 16C6.55195 16 7.02309 16.196 7.41521 16.588C7.80732 16.98 8.00305 17.4507 8.00238 18C8.00238 18.55 7.80632 19.021 7.41421 19.413C7.02209 19.805 6.55128 20.0007 6.00179 20ZM16.0048 20C15.4546 20 14.9835 19.804 14.5913 19.412C14.1992 19.02 14.0035 18.5493 14.0042 18C14.0042 17.45 14.2002 16.979 14.5923 16.587C14.9845 16.195 15.4553 15.9993 16.0048 16C16.5549 16 17.0261 16.196 17.4182 16.588C17.8103 16.98 18.006 17.4507 18.0054 18C18.0054 18.55 17.8093 19.021 17.4172 19.413C17.0251 19.805 16.5543 20.0007 16.0048 20ZM6.00179 15C5.25156 15 4.68473 14.6707 4.30128 14.012C3.91783 13.3533 3.90116 12.6993 4.25127 12.05L5.60167 9.6L2.0006 2H0.97529C0.691873 2 0.45847 1.904 0.275082 1.712C0.0916939 1.52 0 1.28267 0 1C0 0.71667 0.0960286 0.479003 0.288086 0.287003C0.480143 0.0950034 0.717547 -0.000663206 1.0003 3.46021e-06H2.62578C2.80917 3.46021e-06 2.98422 0.0500035 3.15094 0.150004C3.31765 0.250004 3.44269 0.39167 3.52605 0.575003L4.20125 2H18.9556C19.4058 2 19.7142 2.16667 19.8809 2.5C20.0476 2.83334 20.0393 3.18334 19.8559 3.55L16.3049 9.95C16.1215 10.2833 15.8797 10.5417 15.5796 10.725C15.2795 10.9083 14.9378 11 14.5543 11H7.10211L6.00179 13H17.0301C17.3135 13 17.5469 13.096 17.7303 13.288C17.9137 13.48 18.0054 13.7173 18.0054 14C18.0054 14.2833 17.9093 14.521 17.7173 14.713C17.5252 14.905 17.2878 15.0007 17.0051 15H6.00179Z"
                    fill="white"
                    />
            </svg>
        </button>
    <? endif; ?>

</div>
