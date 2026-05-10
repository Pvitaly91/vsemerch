<? use yii\helpers\Url;?>
<div id="ModalCart" class="modal">
    <div class="modal-content">


        <div class="container-popup">
            <div class="close-popup">
                <button onclick="closeCartModal()">
                    <svg width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M1 1L16 16" stroke="#989898" stroke-linecap="round"/>
                        <path d="M16 1L1 16" stroke="#989898" stroke-linecap="round"/>
                    </svg>
                </button>
            </div>

            <h3 class = "basket_name-popup"><?= Yii::t('shop', 'Cart') ?></h3>
            <div id="cart-result">

            </div>
        </div>
    </div>
</div>
<script>
    function updateCartData() {
        $.get('<?= Url::to(['/shop/cart/list']) ?>?flag', function (data) {

        console.log(data);
            $("#total").html(data['total']);
            $(".cart__amount").html("(" + data['count'] + ")");

            for (id in data['items']) {
                //alert(data['items'][id]['cost']);
                if(data['items'][id]['isDiscount'] == true){
                    $("#price_" + id+" .discount").html(data['items'][id]['oldPrice']);
                   
                    $("#cost_" + id+" .discount").html(data['items'][id]['oldCost']);
                }else{
                    $("#price_" + id+" .discount").html("");
                    $("#cost_" + id+" .discount").html("");
                }
                 $("#price_" + id+" .price-popup").html(data['items'][id]['price']);
                $("#cost_" + id+" .cost").html(data['items'][id]['cost']);
                $("#quantity_" + id).val(data['items'][id]['quantity']);
                $("#plus_" + id).attr("data-url", data['items'][id]['plus']);
                $("#minus_" + id).attr("data-url", data['items'][id]['minus']);
                $("#minus_" + id).attr("data-quantity", data['items'][id]['quantity']);
            }
            if(data["min_price_msg"] == false)
               $(".text-min-price").addClass("hidden");
            else if(data["min_price_msg"] == true)
                $(".text-min-price").removeClass("hidden");    
            if(data["allow_order"] == true){
                
                $("#order-button").removeClass("disabled");
            }else{
               
                $("#order-button").addClass("disabled");
            }
            if (data['total'] == "0") {
                $.get("/shop/cart/list", function (data) {
                    $("#cart-result").html(data);
                });
            }
        });
    }
    function checksize(id) {
        //   alert($("#item_"+id).attr("data-size") == 1)
        if ($("#item_" + id).attr("data-size") == 1) {

            return "&size=1";
        } else {
            return "";
        }
    }
    $(document).on("click","#order-button", function(event){
        if($(this).hasClass("disabled") == true){
            event.preventDefault();
        }
    });
    $(document).on("click", ".minus", function () {

        quantity = $(this).attr("data-quantity");

        if (quantity > 1) {

            $.get($(this).attr("data-url"), function (data) {

                updateCartData();
            });
        }

    });
    function isNumeric(input) {
    return /^[0-9]+$/.test(input);
  }
    $(document).on("keyup", ".input_q", function () {
       if( isNumeric($(this).val())){
          
            $.get($(this).attr("data-url")+"&quantity="+$(this).val(), function (data) {
            
            updateCartData();
            });
           
       }
    });
    $(document).on("click", ".plus", function () {

        $.get($(this).attr("data-url"), function (data) {
            
            updateCartData();
        });
    });
    $(document).on("click", ".remove", function () {
        // alert($(this).attr("data-id"));
        id = $(this).attr("data-id");
        $.get($(this).attr("data-url"), function (data) {

            $(".item_" + id).remove();
            updateCartData();
        });
    });

    // Open the modal
    function openCartModal() {
        var modal = document.getElementById("ModalCart");
        modal.style.display = "flex";
        setTimeout(function () {
            modal.classList.add("show");
        }, 10);
    }

    // Close the modal
    function closeCartModal() {
        var modal = document.getElementById("ModalCart");
        modal.classList.remove("show");
        setTimeout(function () {
            modal.style.display = "none";
        }, 200);
    }


</script>
<style>
    .text-min-price{
         margin-top:10px;
         margin-bottom: 10px;
         
         width:100%;
         text-align: center;
    }
    .disabled p{
        color:#ccc;
    }
    .disabled:hover{
        cursor:not-allowed;
    }
</style>

