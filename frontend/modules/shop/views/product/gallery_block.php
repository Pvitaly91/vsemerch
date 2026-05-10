<?
use yii\helpers\Html;
$first = $item["morePhode"][0];
?>  
 <a href="<?=$first["image"] ?>" class="fancybox-big">    
    <img class="main-img fancybox"
            data-thumb="<?=$first["ico"] ?>"
            data-src="<?=$first["image"] ?>"
            src="<?= $first["thumb"] ?>"
      
           <?/* style="min-width: 441px; max-width:441px"*/?>
        />
 </a>
 <div class="review-block__image-alternatives">
    <? if(isset($item["morePhode"])):?>
        <? foreach($item["morePhode"] as $photo):?>
		<? if(isset($item["model"])):?>
            <button class="mini-img"
                    data-id="<?=$item["model"]->id?>"
                    data-src="<?= $photo["image"] ?>">
             
          
                <img src="<?= $photo["ico"] ?>" style="max-width:50px"
                         alt="<?//= Html::encode($image->alt) ?>"/>
            </button>
		<? endif;?>	
        <? endforeach;?>
    <? endif;?>  
 </div>
