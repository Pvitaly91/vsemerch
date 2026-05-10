<?
//naavnist
$ext = (isset(\common\Helpers\Partners::$ext[$model->partner]))?\common\Helpers\Partners::$ext[$model->partner]:[];
$ext[] = "naavnist";
$ext[] = "status";
$ext[] = "dimensions";

?>
<style>
    .description__characteristics-second div {
       
        min-height: 59px; 
        height: auto;
    }
</style>
<script>
    
    $(document).ready(function(){
        function fixHeight(){
             c = $(".value_v");
        
            for(i=0; i< c.length; i++){
                vh = $(c[i]).height();
                nh = $("#name_c"+$(c[i]).attr("data-id")).height();
               if(vh == "0")
                   vh = "59";

                if(vh != nh)
                   $("#name_c"+$(c[i]).attr("data-id")).height(vh+"px"); 

            }
        }
        fixHeight();
        $("#description-btn, #characteristics-btn").click(function(){
            fixHeight();
        })
       
    });
</script>
<div class="description__characteristics-first">
 
    <?php foreach ($model->option as $k => $item):?>
    <? if(in_array($item->slug_option,$ext)) continue; ?>
    <? if($item->slug_option == "tm" && $item->option != "TM") continue; ?>
     <?// dd($item->translation->id);?>
        <div  id="name_c<?=$k?>" data-slug="<?=$item->slug_option?>">
            <? $id = NULL?>
            <p><?//=$item->slug_option?> <?=$model->getPartnerPropertyTrans($item->option,$id)?> <?= $model->getOptionEditLink($id) ?></p>
        </div>
    <?php endforeach;?>

</div>
<div class="description__characteristics-second">
    <?php foreach ($model->option as $k => $item):?>
       <? if(in_array($item->slug_option,$ext)) continue; ?>
        <? if($item->slug_option == "tm" && $item->option != "TM") continue; ?>
        <div class="value_v" data-id="<?=$k?>">
            <p><?=str_replace("  "," ",str_replace(",", ", ", $item->value))?></p>
        </div>
    <?php endforeach;?>
</div>