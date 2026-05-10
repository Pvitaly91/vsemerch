<?

use yii\web\View;
use yii\helpers\Html;

//dd($model);
?>

	<main class="container">
			<section>
            
<?
        $b = $model->body;
        $bodyPart = explode("<ul>", $b);
        $body = $bodyPart[0];
        unset($bodyPart[0]);
    //    $pattern = '/<([a-z][a-z0-9]*)[^>]*?(\/?)>/i';
     //   $stripped_html = preg_replace($pattern, '<$1$2>', $body);
        
            function removeAttributesExceptSrcHref($html) {
    $pattern = '/<([a-zA-Z]+)(?:\s+([^>]*?))?(\s*\/?)>/';
    $allowedAttributes = ['src', 'href'];

    $html = preg_replace_callback($pattern, function($matches) use ($allowedAttributes) {
        $tag = $matches[1];
        $attributes = $matches[2] ?? '';
        $selfClosing = $matches[3] ?? '';

        if ($attributes !== '') {
            preg_match_all('/([\w-]+)(?:=("[^"]*"|\'[^\']*\'|[^\s>]+))?/', $attributes, $attrMatches, PREG_SET_ORDER);

            $newAttributes = [];

            foreach ($attrMatches as $attrMatch) {
                $attrName = $attrMatch[1];
                $attrValue = $attrMatch[2] ?? '';

                if (in_array($attrName, $allowedAttributes, true)) {
                    $newAttributes[] = $attrValue !== '' ? $attrName . '=' . $attrValue : $attrName;
                }
            }

            $attributes = !empty($newAttributes) ? ' ' . implode(' ', $newAttributes) : '';
        } else {
            $attributes = '';
        }

        return "<{$tag}{$attributes}{$selfClosing}>";
    }, $html);

    return $html;
}
        $stripped_html = removeAttributesExceptSrcHref($body);
        ?>
                <?= $this->render('breadcrumbs',['breadcrumbs'=>$breadcrumbs]) ?>
                  <?=$model->getEditLink()?>
                <? if(false && isset($topPhotos)):?>
                
                <div class="topPhotos">
                    <div class="photo-item">
                        <a href="<?=Yii::$app->request->baseUrl?>/img/Set_new_Avto-500x500.jpg" rel="shadowbox[gal]" class="fancybox" title="Внешняя реклама">
                            <img src="<?=Yii::$app->request->BaseUrl?>/img/Set_new_Avto-500x500.jpg" class="img-responsive" border="0" alt="Внешняя реклама" />
                        </a>
                    </div>
                  
                    <div class="photo-item">
                        <a href="<?=Yii::$app->request->baseUrl?>/img/Set_new_Naruzhka-500x500.jpg" rel="shadowbox[gal]" class="fancybox" title="Внешняя реклама">
                            <img src="<?=Yii::$app->request->BaseUrl?>/img/Set_new_Naruzhka-500x500.jpg"  class="img-responsive" border="0" alt="Внешняя реклама" />
                        </a>
                    </div>
                    <div class="photo-item">
                        <a href="<?=Yii::$app->request->baseUrl?>/img/Set_new_Promo-500x500.jpg" rel="shadowbox[gal]" class="fancybox" title="Внешняя реклама">
                            <img src="<?=Yii::$app->request->BaseUrl?>/img/Set_new_Promo-500x500.jpg"  class="img-responsive" border="0" alt="Внешняя реклама" />
                        </a>
                    </div>
                </div>
                <? endif;?>
				<div class="product-card-block">
                    
					<? if(isset($viewName)) {
                        echo $this->render($viewName,["model" => $model,"title" => $title,"body" =>$stripped_html,"_body" => $body]);
                    }
                    ?>
                    <?
                        $subItems = [];
                 
                        foreach($bodyPart as $k => $item ){
                                $itemsParts = explode("</ul>",$item); 
                                $item = "<ul>".$itemsParts[0]."</ul>";
            
                                $dom = new DOMDocument();
                                $dom->loadHTML('<?xml encoding="UTF-8">' .$item);

                                $ul = $dom->getElementsByTagName('ul')->item(0);
                                $li_elements = $ul->getElementsByTagName('li');

                                $characteristics = [];
          
                                foreach ($li_elements as $li) {
                                    $text = $li->nodeValue;
                                    $characteristics[] = $text;
                                    
                                    if(!isset($subItems[$k]["length"]) || strlen($text) < $subItems[$k]["length"] ){
                                        $subItems[$k]["length"] = strlen($text);
                                    }
                                }
                                $subItems[$k]["characteristics"] =  $characteristics;
                                if(isset($itemsParts[1]))
                                    $subItems[$k]["desc"] =  $itemsParts[1];
                        }   
                       
                    if(is_array($subItems)):
                       foreach($subItems as $item):
                    ?>
                        <div class="product-card-block__characteristics">
                            <? if($item["length"] < 120):?>
                                <ul class="characteristics-list">
                                    <? foreach($item["characteristics"] as $char):?>
                                    <li><?=$char?></li>
                                    <? endforeach;?>
                                </ul>
                            <? else:?>
                                <? foreach($item["characteristics"] as $char):?>

                                    <div class="characteristics-block__item">
                                        <p>
                                            <?=$char ?>
                                        </p>
                                    </div>
                                <? endforeach;?>
                            <? endif;?>
                            <? if( isset($item["desc"])):?>
                                <div style="margin-top:20px;">
                                    <?=$item["desc"]?>
                                </div>
                            <? endif;?>
                            
                        </div>
                        <? endforeach;?>
                    <? endif;?>
                    <?= $this->render('slider',['photos'=>$photos]) ?>
					
					
				</div>
			</section>
		</main>
  <? if(isset($photos) || isset($topPhotos)):?>
 
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.js" integrity="sha512-uURl+ZXMBrF4AwGaWmEetzrd+J5/8NRkWAvJx5sbPSSuOb0bZLqf+tOzniObO00BjHa/dD7gub9oCGMLPQHtQA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.css" integrity="sha512-H9jrZiiopUdsLpg94A333EfumgUBpO9MdbxStdeITo+KEIMaNfHNvwyjjDJb+ERPaRS6DpyRlKbvPUasNItRyw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script>
        $(document).ready(function(){
            $('.fancybox').fancybox();
        })
    </script>
  <? endif;?>