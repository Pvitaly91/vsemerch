   <?if(!empty($model->size) && count($model->size)>1):?>
        <?foreach($model->sortTableSize as $sortSize):?>
            <?php foreach ($model->size as $size):?>
                <? if($sortSize == $size->size->name):?>
                    <button
                        id="size-<?=$size->id?>"
                        data-price="<?=$size->price?>"
                        data-id="<?=$size->id?>"
                        data-code="<?=$size->code?>"
                        data-not_available="<?=$size->not_available?>"
                        class=" size-box <? if($size->not_available >0):?>product-description__size-button-no-active<? else:?> size<? endif;?>"
                    >
                        <?=$size->size->name?>
                    </button>
                    <? break;?>
                <? endif;?>
            <?php endforeach;?>
        <?php endforeach;?>  

      <?/*
        <?php foreach ($model->size as $size):?>
              
                    <button
                        id="size-<?=$size->id?>"
                        data-price="<?=$size->price?>"
                        data-id="<?=$size->id?>"
                        data-code="<?=$size->code?>"
                        data-not_available="<?=$size->not_available?>"
                        class=" size-box <? if($size->not_available >0):?>product-description__size-button-no-active<? else:?> size<? endif;?>"
                    >
                        <?=$size->size->name?>
                    </button>
                 
            <?php endforeach;?>
               */?>       
    <?endif;?>