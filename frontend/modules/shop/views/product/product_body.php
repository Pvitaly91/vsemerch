<?= $model->getEditLink() ?>
<?  if(!isset($flag)){
	$flag = 0;
}?>
<? if (true && (IS_LOCAL == true || IS_PROD == true)): ?>
    <script>
    <? $remarcetingDebug = true; ?>
        $(document).ready(function () {
    <?
    if (!empty($model->size) && count($model->size) > 1):
        $fistCode = $firstPrice = false;
        foreach ($model->size as $size) {
            if ($size->not_available == 0) {
                $fistCode = $size->code;
                $firstPrice = $size->price;
                break;
            }
        }
        ?>
        <? if ($fistCode != false && $firstPrice != false): ?>
                    /* with size*/

                    firstProce = '<?= $firstPrice; ?>';
                    firstCode = '<?= $fistCode; ?>';
                    gtag('event', 'view_item', {
                        'send_to': 'AW-10941815611',
                        'value': firstProce,
                        'items': [{
                                'id': firstCode,
                                'google_business_vertical': 'retail'
                            }]
                    });
            <? if ($remarcetingDebug == true): ?>
                        console.log("view color item:" + firstProce + ' ' + firstCode);
            <? endif; ?>
                    const sizeSku = document.getElementsByClassName("size-box");

                    for (i in sizeSku) {
                        if (typeof sizeSku[i].addEventListener == "function") {

                            sizeSku[i].addEventListener("click", function () {

                                price = this.getAttribute('data-price');
                                code = this.getAttribute('data-code');

                                // document.getElementById('price').value = price;     
                                gtag('event', 'view_item', {
                                    'send_to': 'AW-10941815611',
                                    'value': price,
                                    'items': [{
                                            'id': code,
                                            'google_business_vertical': 'retail'
                                        }]
                                });
                                <? if ($remarcetingDebug == true): ?>
                                    console.log("view size item:" + price + ' ' + code);
                                <? endif; ?>
                            });
                        }
                    }


        <? endif; ?>
    <? else: ?>
                /* no size*/
                _price = '<?= $model->price ?>';
                _code = '<?= $model->code ?>';
                gtag('event', 'view_item', {
                    'send_to': 'AW-10941815611',
                    'value': _price,
                    'items': [{
                            'id': _code,
                            'google_business_vertical': 'retail'
                        }]
                });
        <? if ($remarcetingDebug == true): ?>
                    console.log("view simple product" + _price + ' ' + _code);
        <? endif; ?>

    <? endif; ?>
            const cart = document.getElementsByClassName("add_to_cart");
            // console.log(cart);
            for (i in cart) {

                if (typeof cart[i].addEventListener == "function") {
                    cart[i].addEventListener("click", function () {
                        if (this.getAttribute('disabled') == null) {

                            price = document.getElementById("price").value;
                            codeBox = document.getElementById("codeBox").innerHTML;
                            gtag('event', 'add_to_cart', {
                                'send_to': 'AW-10941815611',
                                'value': price,
                                'items': [{
                                        'id': codeBox,
                                        'google_business_vertical': 'retail'
                                    }]
                            });
                            <? if ($remarcetingDebug == true): ?>
                                console.log("add to cart " + price + " " + codeBox);
                            <? endif; ?>
                        }
                    });
                }
            }
        });
    </script>
    <input type="hidden" id="price" value="<?= $model->price ?>">
<? endif; ?>
    
<div <? if ($flag != 1): ?>class="hide-if-mob"<? endif; ?>  id="size-block">
    <p><?= Yii::t('shop', 'Code') ?>: <span class="code product-code" ><?= $model->code ?></span></p>
    <input type="hidden" value="<?= $model->id; ?>" id="product-id" >
    <? if (!empty($model->size) && count($model->size) > 1): ?>
        <input type="hidden" value="" id="size-id" >
        <script>
            $(document).ready(function () {
                        
                $(".product-description__size-button-no-active").click(function (event) {
                    <? if(!isset($_GET["size"])):?>
                        event.preventDefault();
                    <? else:?>
                        if( $(this).attr('data-id') == "<?=$_GET["size"]?>"){
                             $(".size-active").removeClass("size-active");
                            $(".product-description__availability").html('<?= Yii::t('shop', 'Not available') ?>');
                            $(".product-description__availability").addClass('not-avalable');
                            $(".product-description__price span").html('');
                            $("#buy-button").attr('disabled','disabled')
                        //   $(".product-description__button").addAttr('disabled','disabled');
                       //    $(".product-description__price")
                        }
                    <? endif;?>  
                    
                })
                $(".product-description__size button.size").click(function () {
                    <?// if(!isset($_GET["size"])):?>
                     $(".size-active").removeClass("size-active");
                    <?// endif;?>
                  
                    size = $(this);
                    size.addClass("size-active");
					<? if(isset($_GET["size"])): ?>
						 $("#size-<?=$_GET["size"]?>").addClass('size-active')
					<? endif;?>	 
						if (size.hasClass('product-description__size-button-no-active') == false) {
							$("#price").val(size.attr('data-price'));
							$(".product-description__price span").html(size.attr('data-price') + " ₴");
							$(".product-code").html(size.attr('data-code'));
							$(".product-description__button[disabled=disabled]").removeAttr('disabled');
							$(".product-description__price").width("50%");
							$("#size-id").val(size.attr("data-id"));
							$(".product-description__availability").removeClass('not-avalable');
							 $(".product-description__availability").html('<?= Yii::t('shop', 'Are available') ?>');
						}
					
                });
                <? if(isset($_GET["size"])):?>
                    $("#size-<?=$_GET["size"]?>").trigger("click")
                   $("button#size-<?=$_GET["size"]?>").addClass('size-active');
                <? endif;?>
            });
        </script>
        <div class="product-description__size" id="desc-size-block">
            <? include 'includes/size.php'; ?>
        </div>
    <? endif; ?>
    <div id="desctop-avail-block">    
        <? include 'includes/avail_block.php' ?>
    </div>  
</div>  
<div id="desctop-buy-block">

    <? include 'includes/buy_block.php'; ?>

</div>
<?// dd($sortTableSizee );?>
   
     <?if(!empty($model->size) && isset($model->size[0]) &&  $model->size[0]->params != null):?>
    
        <div class=" table_sizez" >
            <h3><?= Yii::t('shop', 'Table of sizes') ?><?/*Таблиця розмірів*/?></h3>

            <div class="product-block-note">
            <?/*<table>
                    <tbody>
                        <tr>
                            <th rowspan="2" class="size_icon"><img src="/img/size.jpg"></th>
                            
                             <?php foreach ($model->size as $size):?>
                               
                                    <th><?=$size->size->name?></th>
                                   
                            <? endforeach;?>    
                            
                        </tr>
                        <tr>
                            <?php foreach ($model->size as $size):?>
                            <td><?=$size->params?></td>
                          <? endforeach;?>
                        </tr>
                       
                    </tbody>
                </table>*/?>
                <table>
                    <tbody>
                        <tr>
                            <th rowspan="2" class="size_icon"><img src="/img/size.jpg"></th>
                            <?foreach($model->sortTableSize as $sortSize):?>
                                <?php foreach ($model->size as $size):?>
                                    <? if($sortSize == $size->size->name):?>
                                        <th><?=$size->size->name?></th>
                                        <? break;?>
                                    <? endif;?>    
                                <? endforeach;?>
                            <? endforeach;?>    
                            
                        </tr>
                        <tr>
                        <?foreach($model->sortTableSize as $sortSize):?>
                                <?php foreach ($model->size as $size):?>
                                    <? if($sortSize == $size->size->name):?>
                                        <td><?=$size->params?></td>
                                        <? break;?>
                                    <? endif;?>    
                                <? endforeach;?>
                            <? endforeach;?>    
                        </tr>
                       
                    </tbody>
                </table>
            </div>
           

        </div>
        <div class="description_after_details" >
            <?= Yii::t('shop', 'For textiles, the permissible variation from the technical parameters is +- 5%') ?>
        </div>
    <? endif;?>   
 