

    <h1 class="hide-if-mob main-title"><?=$title?></h1>
    <div id="ajax-body">
       
        <? include \Yii::$app->modules["shop"]->viewPath.'/product/product_body.php'?>
    </div>    
    <div class="product-description__characteristics">
        <div class="product-description__characteristic-btn-block">
            <button
                onclick="changeDescription()"
                id="description-btn"
                class="product-description-btn active-btn"
                
                >
                <?= Yii::t('shop', 'Description') ?>
               <?/* Опис*/?>
            </button>
            <button
                onclick="changeDescription()"
                id="characteristics-btn"
                class="product-characteristics-btn "
                >
                <?=Yii::t('shop', 'Specifications')?>
            </button>
        </div>
        <div id="description__characteristics" class="description__characteristics hidden">
            <? include \Yii::$app->modules["shop"]->viewPath.'/product/option_block.php'?>
        </div>
        <div id="description__exposition" class="description__exposition ">
            <?=($model->description)?$model->description:$model->title; ?>
        </div>
    </div>
