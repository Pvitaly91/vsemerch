<?
use yii\helpers\Html;
?> 
<input type="hidden" value="<?=$model->price?>" id="price"> 
<div class="col-md-8 price-big">
<?=$model->getEditLink()?>


    <?if(!empty($model->size) && count($model->size)>1):?>
        <?php foreach ($model->size as $size):?>
            <a href="#" data-price="<?=$size->price?>" data-id="<?=$size->id?>" data-code="<?=$size->code?>"  data-not_available="<?=$size->not_available?>" class="size-box <?if($size->not_available == "1"):?>disabled<? endif;?>"><?=$size->size->name?></a>
        <?php endforeach;?>
        <div class="both"></div>
    <?endif;?>



    <p class="price-text <?=(($model->price>0) ? '' : 'hidden')?>"><?=Yii::t('shop', 'Price')?>: <span><?= $model->price ?> &#8372;</span></p>
    <?if($model->price > 0 && $model->price_old > 0):?>
        <p class="old-price-text"><?=Yii::t('shop', 'Price Old')?>: <strike ><?= $model->price_old ?> &#8372;</strike></p>
    <?elseif($model->price == 0 && $model->price_old > 0):?>
        <p class="old-price-text"><?=Yii::t('shop', 'Price')?>: <?= $model->price_old ?> &#8372;</p>
    <?endif;?>
      
    <?if($model->not_available == 1):?>
        <p class="available-text not_available"><?=Yii::t('shop', 'Not available')?></p>
    <?else:?>
        <p class="available-text are_available"><?=Yii::t('shop', 'Are available')?></p>
    <?endif;?>
    <?if(!empty($model->code)):?>
        <p class="code-text"><?=Yii::t('shop', 'Code')?>: <span id="codeBox"><?=$model->code?></span></p>
    <?endif;?>
</div>
<div class="col-md-4">
    <?if($model->action):?>
        <span class="action"><?=Yii::t('shop', 'Action')?></span>
    <?endif;?>
    <?if($model->novelty):?>
        <span class="novelty"><?=Yii::t('shop', 'Novelty one')?></span>
    <?endif;?>
</div>
<?if(($model->price>0 && $model->not_available == 0) || ($model->size && $model->not_available == 0)):?>
    <div class=""><?= Html::a('<span class="glyphicon glyphicon-shopping-cart"></span>' . Yii::t('shop', 'Add to cart'), ['cart/add', 'id' => $model->id], ['class' => 'btn btn-lg btn-danger add_to_cart', 'disabled' => (($model->price>0 && $model->not_available == 0) ? false : true)]) ?></div>
<?endif;?>
<? if(IS_LOCAL == true || IS_PROD == true):?>
    <script>
    <? if(!empty($model->size) && count($model->size)>1):
            $fistCode = $firstPrice = false;
            foreach ($model->size as  $size){
                if($size->not_available == 0){
                    $fistCode = $size->code;
                    $firstPrice = $size->price;
                    break;
                }
                
            }
            ?>
            <? if($fistCode != false && $firstPrice != false):?>
                 /* with size*/
                    firstProce = '<?=$firstPrice; ?>';
                    firstCode = '<?=$fistCode; ?>';
                     gtag('event', 'view_item', {
                            'send_to': 'AW-10941815611',
                            'value': firstProce,
                            'items': [{
                            'id': firstCode,
                            'google_business_vertical': 'retail'
                            }]
                        });
                         console.log(firstProce+' '+firstCode);
                         
                        const sizeSku = document.getElementsByClassName("size-box");

                        for(i in sizeSku){
                            if(typeof sizeSku[i].addEventListener == "function"){
                                sizeSku[i].addEventListener("click",function(){
                                    price = this.getAttribute('data-price');
                                    code = this.getAttribute('data-code');
                                    document.getElementById('price').value = price;     
                                    gtag('event', 'view_item', {
                                        'send_to': 'AW-10941815611',
                                        'value': price,
                                        'items': [{
                                        'id': code ,
                                        'google_business_vertical': 'retail'
                                        }]
                                    });
                                    console.log(price+' '+code);
                                });
                            }
                        }
                  
                
            <? endif;?>
        <? else:?>
                /* no size*/
                _price = '<?=$model->price ?>';
                _code = '<?=$model->code ?>';
              gtag('event', 'view_item', {
                    'send_to': 'AW-10941815611',
                    'value': _price,
                    'items': [{
                    'id': _code,
                    'google_business_vertical': 'retail'
                    }]
                });
                 console.log(_price+' '+_code);
              
        <? endif;?>
        const cart = document.getElementsByClassName("add_to_cart");
       // console.log(cart);
        for( i in cart){
          
            if(typeof cart[i].addEventListener == "function"){
                cart[i].addEventListener("click",function(){
                    if(this.getAttribute('disabled') == null){  
                    
                        price = document.getElementById("price");
                        codeBox = document.getElementById("codeBox").innerHTML;
                        gtag('event', 'add_to_cart', { 
                            'send_to': 'AW-10941815611',
                            'value': price,
                            'items': [{
                            'id': codeBox,
                            'google_business_vertical': 'retail'
                            }]
                        });
                        console.log("add to cart "+price.value+" "+codeBox);
                   }    
                });
            }
        }
    </script>
<? endif;?>