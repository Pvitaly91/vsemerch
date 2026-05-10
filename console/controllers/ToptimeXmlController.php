<?php 
namespace console\controllers;

use console\components\Controller;
use yii\helpers\Inflector;

class ToptimeXmlController extends Controller {
    use \console\controllers\traits\common, \common\models\traits\Images;
    public $partnerName = "toptime";
    public $data = [];
    public $loadPics = true;
    public $priceRatio = 40.5;
    public $koef = 1.25;
    function getCategoriesDataArray($id = false){
        $cat[1]= "Футболки";
        $cat[2]= "Поло";
        $cat[3]= "Светри";
        $cat[4]= "Фліси";
        $cat[5]= "Білизна чоловіча";
        $cat[6]= "Кепки";
        $cat[7]= "Шапки";
        $cat[8]= "Шнурки";
        $cat[9]= "Запльнички";
        $cat[10]= "Футболки з круглим вирізом";
        $cat[11]= "Футболки з v-подібним вирізом";
        $cat[12]= "Футболки з довгим рукавом";
        $cat[13]= "Майки";
        $cat[14]= "Реглани";
        $cat[15]= "Кенгуру";
        $cat[16]= "Кенгуру на замок";
        $cat[17]= "Ручки";
        $cat[18]= "Розпродаж";
        $cat[19]= "Куртки";
        if($id == false)
            return $cat;
        elseif(isset($cat[$id])){
            return $cat[$id];
        }    
    }   
    function clearTitle($title,$size){
        $pTitle = explode(",", $title);
        foreach($pTitle as $k => $v){
            $pTitle[$k] = str_replace($size,"", $v);
        }
        $title = implode(",", $pTitle);
        $title = trim(str_replace("  "," ", $title));
     
        if($title[strlen($title)-1] == ","){
            $title = substr($title,0,-1);
          
        }
        return $title;
    }
    function initData()
    {
        $this->initMap();
        if(!empty($this->data))
            return;

        $xml_file = simplexml_load_file('https://toptime.com.ua/xml/toptime.xml');
       
        $num = 100000000000000;
        $i = 0;
        $products = $ids = [];
        $ext = [
            "id_category",
            "count1",
            "count2",
            "count3",
            "count4",
            "code",
            "prints"            
        ]; 

        $map["article"] = "code";
        $map["name"] =  "title";
        $map["content"] =  "description";
        $map["photo"] = "image";
        $map["price"] = "price";

        foreach ($xml_file->item as $item) {
           if($num < $i ){
                break;
            }
            $arr = [];
            foreach ($item as $name => $value){

                if((string)$item->$name != "" && !in_array($name, $ext)){
                    if(strpos($name,"_ru") === false ){
                       
                        $optionValue = (string)$item->$name; 
                        $name = str_replace("_ua","",$name);
                      
                      if(!in_array($name, $ext)){
                            if(isset($map[$name])){
                                $arr[strtolower($map[$name])] = $optionValue;
                            }else{
                                $arr["options"][strtolower($name)] = $optionValue;
                            } 
                        }
                    }else{
                        
                        $optionValue = (string)$item->$name; 
                        $name = str_replace("_ru","",$name);
                        if(!in_array($name, $ext)){
                            if(isset($map[$name])){
                                $arr[strtolower($map[$name])."_ru"] = $optionValue;
                            }else{
                                $arr["options_ru"][strtolower($name)] = $optionValue;
                            } 
                        }
                    }
                }
            
                
            }
			
			if(empty($arr["price"]))
			{
			   continue;
			} 
			if(isset($arr["price"])){		   
				$arr["price"] = $this->priceRatio * $arr["price"];
				$arr["price"] = $arr["price"] * $this->koef;
			}	
            //count2
            $arr['not_available'] = ((string)$item->count2 > 0) ? 0 : 1;
            $arr["is_main"] = 1;
            $arr["meta_description"] = $arr["meta_title"] = $arr["title"];
            $arr["slug"] =  Inflector::slug($arr["title"]);
            $arr["partner_id"] = (string)$item->code;// $arr["article"];
            $codeParts = explode(" ", $arr["code"]);
            $arr["sku_group"] = $codeParts[0];
            if(
                (string)$item->price == ""
                || $arr["slug"] == ""
            ){
                continue;
            }

            if(!in_array($arr["sku_group"], $ids)){
                $arr["is_main"] = 1;
                $ids[] = $arr["sku_group"];
            }else{
                $arr["is_main"] = 0; 
            }

            if((string)$item->id_category == ""){
                $key = "БЕЗ КАТЕГОРИИ";
            }else{
                $key = $this->getCategoriesDataArray((string)$item->id_category);
                if($key == ""){
                    $key = "БЕЗ КАТЕГОРИИ";
                  
                }
            }
           // $cats[(string)$item->id_category] = (string)$item->id_category;
            
            
            $slug = Inflector::slug($key);

            if(!isset($this->data[$slug])){
                $this->data[$slug] = [ "cat" => [
                        "translates" =>[
                            "title" => $key,
                            "meta_title" => $key,
                            "meta_description" => $key,
                        ],
                        "slug" => $slug,
                        "partner_slug" => $slug,
                        "partner_id" => $slug,
                        "partner" => $this->partnerName, 
                    ]    
                ];
            }
            $attr = [];
           // var_dump($arr['not_available']); echo "\r\n";
           
            \common\Helpers\Partners::availableProp($arr['not_available'], $attr);
          //  var_dump($attr); echo "\r\n";
            
          //  var_dump($arr["options"]); echo "\r\n";
          //  if($arr["options"] == NULL){
          //     print_r($arr);     
          //  }
            $arr["options"] = array_merge(
                $arr["options"],
                $attr["uk"]);
            if((string)$item->size != ""){
                $arr["price"] = "0.00";
                $arr["meta_title"] = $arr["meta_description"] = $arr["title"] = $this->clearTitle($arr["title"],$arr["options"]["size"]);
                if(isset($arr["title_ru"])){
                    $arr["meta_title_ru"] = $arr["meta_description_ru"] =$arr["title_ru"] = $this->clearTitle($arr["title_ru"],$arr["options"]["size"]);
                }
                $price= (string)$item->price * $this->priceRatio;
                $price = $price * $this->koef;
                $size =  [
                    "name" => $arr["options"]["size"],
                    "not_available" => $arr["not_available"],
                    "price" => $price,
                    "code" => (string)$item->code,
                     
                ];

              //  if((string)$item->Dimensions != ""){
                //    $size["params"] = (string)$item->Dimensions;
              //  }
               // $colCode = (string)$item->color; //$arr["options"]["colorcode"];
                if(!isset($this->data[$slug]['products'][(string)$item->article])){
                    $arr["sizes"][] = $size;
                    $this->data[$slug]['products'][(string)$item->article] = $arr;
                }else{
                    $this->data[$slug]['products'][(string)$item->article]["sizes"][] = $size;
                }
            }else  
                $this->data[$slug]['products'][] = $arr;

            $i++;
         //  print_r($this->data);
       //  exit;
         //   print_r($arr);
          //  exit;
        }
      // print_r($this->data);
       // exit;
    }
    function actionRemakeAll() {
         
        echo "start ".date("d-m-Y H:i:s")." \r\n";
        $this->delAll();
         echo date("d-m-Y H:i:s")." del all products \r\n";
        $this->dellAllCats();
        echo date("d-m-Y H:i:s")." del all cats \r\n";
        $this->initData(); // init data
        $this->createPartnerMainCat();
        echo "init data ".date("d-m-Y H:i:s")." \r\n";
     
        $this->makeCats();
        echo date("d-m-Y H:i:s")." make cats \r\n";
    //    $this->deleteEmptyCats();
     //   echo date("d-m-Y H:i:s")." del empty cats \r\n";
        echo "end ".date("d-m-Y H:i:s")." \r\n";
    }
    function actionUpdate() {
     
        echo "start ".date("d-m-Y H:i:s")." \r\n";
        $this->initData(); 
      
        $this->createPartnerMainCat();
        echo "init data ".date("d-m-Y H:i:s")." \r\n";
        $this->makeCats();
        echo date("d-m-Y H:i:s")." make cats \r\n";
   
        echo "end ".date("d-m-Y H:i:s")." \r\n";
    }
    function actionCreateCats(){
        $this->initData(); // init data
        $this->createPartnerMainCat(); 
        $this->makeCats();
    }
    
    function actionIndex(){
        
        $this->initData(); // init data
        
 
        $this->makeCats();
        
       
    }
}