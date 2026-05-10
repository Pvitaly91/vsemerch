
<div class="products-block1" >
            
                <script>
                    $(document).ready(function () {
                        /*
                         
                         ;
                         */
                        price = codeBox = null;
                        <? $remarcetingDebug = true; ?>
                        $(".main-page").click(function(){
                            img = $(this);
                            //alert(img.attr("_data-url") );
                             $(".product-status").show();
                             
                            if(img.attr("data-url") != undefined)
                               
                                window.location = img.attr("data-url");
                            else{
                            
                                $(".main-page").removeAttr("data-url");
                            }
                            $("#color_"+img.attr("data-id")+".color-active").trigger("click");
                            img.attr("data-url",img.attr("_data-url",));    
                        });
                        $(".products-block__cart-btncart ").click(function () {
                            id =  $(this).attr("data-id");
                            $.get("/shop/cart/add?id=" +id, function (data) {
                              
                                $(".header__cart").trigger("click");
                                $.get("/shop/cart/items-in-cart", function (data) {
                                    $('.cart__amount').html("(" + data + ")");
                                   // alert(price+" "+codeBox);
                                  //  alert(id);
                                    color = $("#color_"+id);
                                    if(price == null)
                                        price  = color.attr("data-price");
                                    if(codeBox == null)
                                        codeBox = color.attr("data-code");
                                  
                                    gtag('event', 'add_to_cart', { 
                                        'send_to': 'AW-10941815611',
                                        'value': price,
                                        'items': [{
                                        'id': codeBox,
                                        'google_business_vertical': 'retail'
                                        }]
                                    });
                                 
                                    <? if($remarcetingDebug == true):?>
                                    console.log("add to cart "+price+" "+codeBox);
                                    <? endif;?>  
                                     price = codeBox = null;    
                                })
                            })
                        });
                        $(".products-block__colors-item").click(function (event) {
                            event.preventDefault();
                            id = $(this).attr('data-id');
                            
                            
                            $("#prod_" + id + " .products-block__colors-item ").removeClass('color-active');
                            $(this).addClass('color-active');
                            price = $(this).attr('data-price'); 
                            codeBox = $(this).attr('data-code');
                            $("#img_" + id).attr("src", $(this).attr('data-image'));
                            $("#img_" + id).attr("data-url", $(this).attr('data-url'));
                            $("#price_" + id).html(price+" &#8372;");
                            $(".url_" + id).attr('href', $(this).attr('data-url'));
                            $("#url_" + id).html($(this).attr('data-title'));
                            no_available = $(this).attr('data-no-available');
                            cart = $("#cart_" + id);
                            cart.attr("data-id", $(this).attr('color-id'));
                            notAval = $("#no-available_" + id);
                            
                            if (no_available == 1) {
                                cart.addClass('zero-opacity');
                                notAval.html('<?= Yii::t('shop', 'Not available') ?>');
                                notAval.addClass('product-no-available');
                                notAval.removeClass('product-available');
                                //no-available_
                                notAval.show();
                                //alert();
                            } else {
                                cart.removeClass('zero-opacity');
                                notAval.html('<?= Yii::t('shop', 'Are available') ?>');
                                notAval.removeClass('product-no-available');
                                notAval.addClass('product-available');
                                notAval.hide();
                            }
                        });
                      
                       
                    });
                </script>
                <? if (is_array($products)): ?>
                    <? foreach ($products as $k => $model): ?>
                        <?
                        if ($k % $mid == false) {
                            $middle = $k + 1;
                        }
                        ?>

                        <? include 'product_item.php' ?> 

                    <? endforeach; ?>
                <? endif; ?>
            </div>
