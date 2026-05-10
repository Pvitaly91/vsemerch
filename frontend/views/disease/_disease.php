<?
use yii\helpers\Url;
?>
<div class="view">
    <img src="<?=Yii::$app->request->baseUrl?>/img/zab<?=$item->id?>.png" border="0" />
    <div class="mask">

        <p>
            <a href="<?=Url::to(['disease/show','translit'=>$item->translit])?>" class="info">
                <img src="<?=Yii::$app->request->baseUrl?>/img/zab<?=$item->id?>_ico.png" border="0" /><br />
                <?=$item->name?>
            </a>
        </p>
    </div>
</div>