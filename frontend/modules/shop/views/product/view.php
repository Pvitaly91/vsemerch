<?

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ListView;
use yii\widgets\Breadcrumbs;
use frontend\modules\shop\widgets\FilterCheckboxWidget;
use yii\web\View;

$title = $model->title;
$this->title = Html::encode($model->meta_title);
$this->registerCssFile('/new/css/product-card.css');
$this->registerCssFile('/new/css/fix.css');
$this->registerJsFile('/new/js/product-card/index.js', ['position' => View::POS_END]);
$flag = 1;//(isset($_GET["flag"])) ? 1 : 0;
?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.js" integrity="sha512-uURl+ZXMBrF4AwGaWmEetzrd+J5/8NRkWAvJx5sbPSSuOb0bZLqf+tOzniObO00BjHa/dD7gub9oCGMLPQHtQA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.css" integrity="sha512-H9jrZiiopUdsLpg94A333EfumgUBpO9MdbxStdeITo+KEIMaNfHNvwyjjDJb+ERPaRS6DpyRlKbvPUasNItRyw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <style>
    .ucfirst::first-letter{
       text-transform:capitalize;
    }
  </style>    
<script>
    
    function setSizeTable() {
        $(document).ready(function () {
            $(".description__characteristics-second div").width($(".product-characteristics-btn").width() + "px");
            $(".description__characteristics-second").width($(".product-characteristics-btn").width() + "px");

            $(".description__characteristics-first div").width($(".product-description-btn").width() + "px");
            $(".description__characteristics-first").width($(".product-description-btn").width() + "px");

            $(".main-block-mobile .description__characteristics-second div").width($(".main-block-mobile .product-characteristics-btn").width() + "px");
            $(".main-block-mobile .description__characteristics-second").width($(".main-block-mobile .product-characteristics-btn").width() + "px");

            $(".main-block-mobile .description__characteristics-first div").width($(".main-block-mobile .product-description-btn").width() + "px");
            $(".main-block-mobile .description__characteristics-first").width($(".main-block-mobile .product-description-btn").width() + "px");
        });
    }
    $(document).ready(function () {
         
        $('.skulink').click(function (event) {
            event.preventDefault();
            $('.sku').removeClass("activeColor");
            $(".color_" + $(this).attr('data-id')).addClass("activeColor");

            history.pushState(null, null, $(this).attr("href"));
            $.get("/ajax/productdata?id=" + $(this).attr('data-id'), function (data) {
                if (data["success"] == true) {
                    $("#ajax-body").html(data["html"]);
                    //$("#bottom-script").html(data["script"]);
                    $(".description__exposition").html(data["description"]);
                    $(".description__characteristics").html(data["option"]);
                    $(".ajax-gallery").html(data["gallery"]);
                    $(".main-title").html(data["title"]);
                    $("#mob-size-block").html($("#desc-size-block").html());
                    $("#mob-avail-block").html($("#desctop-avail-block").html());
                    setSizeTable();
                    <? if ($flag == 1): ?>
                        //alert();
                        // $("#desc-size-block").html( $("#size-block").html());
                    <? endif; ?>
                }

            });
        });
        $(".show-more-colors-but").click(function () {
            if ($(this).hasClass('mHide')) {
                $(this).html('Більше');
                $(this).removeClass("mHide");
                $(".review-block__colors .must-hide").addClass("hidden");

            } else {

                $(".review-block__colors .hidden").removeClass("hidden");
                $(this).html('Приховати');
                $(this).addClass("mHide");
            }

        });


        $(document).on("click", ".mini-img", function () {
            $(".main-img").attr("src", $(this).attr("data-src"));
            $(".main-img").attr("data-src", $(this).attr("data-src"));
            $(".main-img").attr("data-thumb", $(this).attr("data-src"));
          //data-thumb
            
            $(".fancybox-big").attr("href",$(this).attr("data-src"));
        
          //  alert();
        });
         $(document).on("click", ".fancybox-big", function (event) {
             event.preventDefault();
            // alert("fancybox-big");
             $.fancybox.open({
                src: $(this).attr("href"),
                type: 'image'
            });
         });
        $(document).on("click", "#buy-button", function () {
            qstring = "?id="+$("#product-id").val();
            
            if(typeof $("#size-id").val() != "undefined"){
                qstring += "&size_id="+$("#size-id").val();
            }
           
            $.get( "/shop/cart/add"+qstring, function( data ) {
                $(".header__cart").trigger("click");   
                //    //alert( "Load was performed." );
             });
           // alert(typeof $("#size-id").val());
        });
     
    });

    setSizeTable();
    $(window).on("resize", function () {
        setSizeTable();
    });
</script>

<main class="container">
    <section>
       <?
        $breadcrumbs[] = [
            "link" => \yii\helpers\Url::to(['/site/index']),
            "label" => Yii::t('yii', 'Home')
        ];
        $breadcrumbs[] = [
            "label" => Yii::t('shop', 'Shop')
        ];
        $breadcrumbs[] = [
            "label" => $prentCategoryName
        ];
        $breadcrumbs[] = [
            "link" => \yii\helpers\Url::to(['catalog/list', 'slug' => $model->category->slug, 'id' => $model->category->id]),
            "label" => $model->category->title
        ];
        $breadcrumbs[] = [
            "label" => $title,
        ];
        ?>

        <? include \Yii::$app->viewPath . "/catalog/breadcrumbs.php"; ?>
        

        
        
        <div class="main-block-new main-block-pc" >
            <!--review block-->
            <div class="review-block" >
                <div class="main-block-mobile 1main-block">
                    <? include "includes/body_mob.php" ?>
                </div>
                <div class="review-block__image ajax-gallery" >
                    <? include "includes/gallary.php" ?>
                </div>    
                <? include "includes/color.php" ?>

            </div>
            <div class="product-description" >
                <!--Product Description-->
                 
                <? include "includes/body.php" ?>
            </div>
        </div>
        <!--Main Block Mobile-->

    </section>
</main>
  
 