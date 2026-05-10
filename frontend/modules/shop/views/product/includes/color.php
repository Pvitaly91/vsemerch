

<?  if(isset($skus) && is_array($skus) && !empty($skus) && count($skus) > 1):?>
    <div class="review-block__colors">
        <? foreach($skus as $k => $item):?>
            <a href="<?=$item["link"]?><?=(isset($_GET["flag"]))?"?flag=1":""?>" class="review-block__colors-item review-block__colors-item-1 <? if($k > 13):?>hidden must-hide<? endif;?> skulink" data-id="<?=$item["model"]->id?>" >
                 <img  class="sku <? if($item["model"]->id == $active_id):?>activeColor<? endif;?> color_<?=$item["model"]->id?>"  src="<?= $item["morePhode"]["0"]["ico"]?>">
            </a>
        <? endforeach;?>
    </div>
    <? if(count($skus) > 14):?>
        <div class="more-btn" >
            <button class="show-more-colors-but"><?= Yii::t('app', 'More') ?></button>
        </div>
    <? endif; ?>
<? endif; ?>