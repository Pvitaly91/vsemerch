<? if(isset($skus) && is_array($skus) && !empty($skus)):?>
    <div id="colorBlock">
    <? foreach($skus as $item):?>
        <? if(isset($item["morePhode"])):?>
            <a href="<?=$item["link"]?>" class="skulink" data-id="<?=$item["model"]->id?>">
               
            </a>
        <? endif;?>
    <? endforeach;?>
    </div> 
<? endif;?>