<?php

use yii\helpers\Html;
/* @var $this yii\web\View */
/* @var $model common\models\Product */

$this->title = Yii::t('shop', 'Update Product: Table size');

?>
<div class="product-update">

    <h1><?= Html::encode($this->title) ?></h1>
    <p>Product: <?=$pdoduct->title?></p>
    <p>Code: <?=$pdoduct->code?></p>
    <form action="<?=$_SERVER['REQUEST_URI']?>" method="POST">

     
        <? foreach($sizes as $fname => $size):?>
            <label> <?=$fname?>: <input name="<?=$fname?>" value="<?=$size['value']?>" style="width:100px;"></label>
        <? endforeach;?>   
        <br><button type="submit" class="btn btn-primary">Save</button>
    </form>

</div>