<ul class="vertical"  >
    <? if(isset($item["morePhode"])):?>
        <? foreach($item["morePhode"] as $photo):?>
            <li data-thumb="<?= $photo["ico"] ?>" data-id="<?=$item["model"]->id?>" class=""
                data-src="<?= $photo["image"] ?>">
                <img src="<?= $photo["thumb"] ?>" style="width:400px"
                         alt="<?//= Html::encode($image->alt) ?>"/>
            </li>
        <? endforeach;?>
    <? endif;?>  
</ul>