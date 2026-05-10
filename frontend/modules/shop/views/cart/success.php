<?php

use \yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use common\widgets\Alert;

$title = Yii::t('shop', 'Cart');

/* @var $this yii\web\View */
/* @var $products common\models\Product[] */
$this->registerCssFile('/new/css/product-card-3.css');
?>

<? $oreders = \Yii::$app->session->getFlash('gdm')[0]?>
<? if(IS_PROD == true):?>
    <? if(!empty($oreders)):?>
        <script>

            let PRODUCT_LIST = <?= json_encode($oreders["PRODUCT_LIST"])?>;

             gtag('event', 'purchase', {
            'send_to': 'AW-10941815611',
            'transaction_id': <?=$oreders["orderId"]?>,
                'value': <?=$oreders["total"]?>,
                'items': PRODUCT_LIST
        });
        console.log(PRODUCT_LIST);

        </script>
     <? endif;?> 
<? endif;?>     
 <style>
     .footer-pc{
        position: fixed;
        bottom: 0px;
    }
    
</style>
<main class="container">
    <section>
        <? if(is_array($success) && isset($success[0])):?>
           
            <div id="w0-success-0" class="alert-success alert fade in">
                <?=$success[0]?>
            </div>
        <? endif;?>
     </section> 
</main>
