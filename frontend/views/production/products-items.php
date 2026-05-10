<?
    use yii\helpers\Url;
?>
<div class="product-items-body" >
    <? if(is_array($items)):?>
        <? foreach ($items as $item): ?>

            <div class="view3">
                <div class="circl">
                    <a href="<?= $item["link"] ?>">
                        <? if(file_exists(Yii::$app->basePath."/web".$item["img"])):?>
                            <img src="<?= Yii::$app->request->BaseUrl . $item["img"] ?>" width="113" height="113" border="0" />
                        <? else:?>
                            <img src="<?= Yii::$app->request->BaseUrl ."/img/catalog/ico45.png" ?>" width="113" height="113" border="0" />
                        <? endif;?>
                    </a>
                </div>
                <div class="mask">
                    <a href="<?= $item["link"] ?>">
                        <?= $item["label"] ?>
                    </a>
                </div>
            </div>

        <? endforeach; ?>   
    <? endif;?>
</div>