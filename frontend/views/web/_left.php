<?
use yii\helpers\Url;
use frontend\models\WebCatalog;
?>
<ul class="nav nav-pills nav-stacked">
    <?foreach(WebCatalog::find()->all() as $item):?>
        <li role="presentation" <?if(!empty($_GET['translit']) && $_GET['translit']==$item->translit):?>class="active"<?php endif;?> ><a href="<?=Url::to(['web/show','translit'=>$item->translit])?>"><?=$item->name?></a></li>
    <?endforeach;?>
</ul>