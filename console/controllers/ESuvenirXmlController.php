<?php 
namespace console\controllers;

use console\components\Controller;
use yii\helpers\Inflector;
class ESuvenirXmlController extends Controller {
    use \console\controllers\traits\common, \common\models\traits\Images;
    private const IS_PROD = false;

    public $partnerName = "esuvenir";

    public $data = [];
    public $loadPics = true;

    public $sizeTeble = [
        "S",
        "M",
        "L",
        "XL",
        "XXL",
    ];
    public $priceRatio = 39;
    public $photoList;

    public $ruDate = [];
    public function getXmlPath($lng){
        if($lng == "ua") 
            $file_name = "es_products_export_ua.xml";
        elseif($lng == "ru")
            $file_name ="es_products_export_ru.xml";

        
        if(self::IS_PROD == false){
            $path = "/var/www/html/frontend/web/upload/".$file_name;
        }    
        
        return (file_exists($path) && is_file($path))?$path:false;
    }
    function getRuDate($map,$ext){
        $ext[] = "sku";
        $ext[] = "price";
        $ext[] = "weight";
        $ext[] = "height";
        $ext[] = "width";

        $ftpUser = getenv('AGCITY_FTP_USER') ?: 'Agcity';
        $ftpPassword = getenv('AGCITY_FTP_PASSWORD') ?: '';
        $path = "ftp://{$ftpUser}:{$ftpPassword}@176.9.83.91/es_products_export_ru.xml";
        $xml_file = simplexml_load_file($path);
        foreach ($xml_file->item as $item) {
            $items = (array)$item->catalog_product_attribute->item;
            foreach($items as $fName => $value) {
                if(!in_array($fName,$ext) && $value != "" &&  $value != "No Brand"){
                    if(isset($map[$fName]))
                        $key = $map[$fName];
                  //  elseif(isset($this->optionMap[$fName]))
                  //      $key = $this->optionMap[$fName];
                    else
                        $key = $fName;

                    $this->ruDate[$items["sku"]][$key] = (string)$value;
                }
            }
        }
    }
    function initData()
    {

        $this->initMap();
        $map["sku"] = "code";
        $map["name"] =  "title";
        $map["short_description"] =  "description";
        $map["price"] = "price";
        $ext = ["image","archive","print_width","print_height","avail_print_methods","deep"];
        $this->getRuDate($map,$ext);
       // print_r($this->ruDate);
       // exit;
        $this->ftpConnect();
       //exit;
        $allImagesUser = getenv('ALL_IMAGES_FTP_USER') ?: 'all_images';
        $allImagesPassword = getenv('ALL_IMAGES_FTP_PASSWORD') ?: '';
        $this->photoList = array_flip(scandir("ftp://{$allImagesUser}:{$allImagesPassword}@176.9.83.91/"));
        $this->sizeTeble = array_reverse($this->sizeTeble);
        /*if(($path = $this->getXmlPath($lng)) === false){
            echo "File not found \r\n";
            exit;
        }*/

        
        if(!empty($this->data))
            return;
       // $imgPath = "ftp://{$allImagesUser}:{$allImagesPassword}@176.9.83.91/";
        $imgPath = "";
        //file_get_contents("ftp://{$allImagesUser}:{$allImagesPassword}@176.9.83.91/95851002%2F1___4.jpg");
        $path = "ftp://{$ftpUser}:{$ftpPassword}@176.9.83.91/es_products_export_ukr.xml";
        $xml_file = simplexml_load_file($path);
        
        $num = 100000000000000;
        $i = 0;
        $cats = $ids = [];

        
       
        foreach ($xml_file->item as $item) {
            $photos = [];
            $key = (string)$item->catalog_product_attribute_set->item->group;
            //exit;
            if($num < $i ){
                 break;
             }
             $arr = [];

             
         //  exit;
             $items = $item->catalog_product_attribute->item;
          //  print_r($items);
             $not_available = ((int)$item->inventory_source_item->item->qty > 0) ? 0 : 1;
             foreach ($items as  $item){
                foreach ($item as $name => $value){
                    if((string)$item->archive == "Yes")
                        continue;

               // print_r($value);
                   // print_r($item);
                    if((string)$item->$name != "" && !in_array($name, $ext)){
                    
                        
                            $optionValue = (string)$item->$name; 
                            $name = str_replace("_ua","",$name);
                        
                        if(!in_array($name, $ext)){
                                if(isset($map[$name])){
                                    $arr[strtolower($map[$name])] = $optionValue;
                                }else{
                                    $arr["options"][strtolower($name)] = $optionValue;
                                } 
                            }
                    
                    }
              //  $cats[]
                    
                }
               

                if(isset($arr["code"]) &&  $arr["code"] != ""){
                    $skuGroup = substr($arr["code"],0,6);  
					if($skuGroup == "951014"){
						$skuGroup = substr($arr["code"],0,8);
						//echo $skuGroup." ".$arr["code"] ."\n";
					}
                }
                else{
                   continue;
                }
			//	echo $skuGroup." ".$arr["code"] ."\n";
                $arr["price"] = $this->priceRatio*$arr["price"];
                $photoFileName = str_replace("/","%2F",$arr["code"]);
              //  echo $photoFileName."\r\n";
                for($i = 0; $i<=6; $i++){
                    $fName = $photoFileName."___".$i.".jpg";
                    if(isset($this->photoList[$fName])){
                        if($i == 0)
                            $arr["image"] = $imgPath.$fName;
                        else
                            $arr["more_photo"][] = $imgPath.$fName;
                    }
                }
              //  echo $photoFileName." ";
              //  print_r(array_search($photoFileName,$this->photoList));
             //   echo " \r\n";
               // exit;
                //$arr["image"] = $imgPath.$arr["code"]."___0.jpg";
               
               
                $size = "";
                foreach ($this->sizeTeble as $_size){
                    if(strlen($_size) > 1){   
                        if(strpos($arr["code"], $_size) !== false){
                            $size = $_size; 
                            break;
                        }
                    }elseif(substr($arr["code"],-1) == $_size){
                        $size = $_size; 
                        break;
                    }
                } 
                if($size == ""){
                    $tSizes = $this->sizeTeble;
                    $tSizes[] = ["2XL"];
                    $tSizes[] = ["3XL"];
                    foreach($tSizes as $_size){
						if(is_array($_size)){
							foreach($_size as $__size){
								if(strpos($arr["title"], " ".$__size." ") !== false){
									$size = $__size; 
									break;
								}
							}			
						}else{
							if(strpos($arr["title"], " ".$_size." ") !== false){
								$size = $_size; 
								break;
							}
						}
                    }
                }   
            //    echo $arr["code"]." ".$arr["title"]." color:".$arr["options"]['color']." ".$size."\r\n";
                
                //count2
               // $arr['not_available'] = ((string)$item->count2 > 0) ? 0 : 1;
                if($size != ""){
                    $arr["title"] = str_replace(" ".$size." "," ",$arr["title"]);
                    $arr["options"]["size"] = $size;
                }
                $arr["is_main"] = 1;
                $arr["meta_description"] = $arr["meta_title"] = $arr["title"];

                $ruDate = $this->ruDate[$arr["code"]];

                if(isset($arr["options"])){
                    foreach ($arr["options"] as $name => $value){
                        if(isset($ruDate[$name])){
                            $arr["options_ru"][$name] = $ruDate[$name];
                        }
                    }
                }    
                if(isset($ruDate["title"]))
                    $arr["title_ru"] = $arr["meta_title_ru"] = $ruDate["title"];

                if(isset($ruDate["description"]))
                    $arr["description_ru"] =  $arr["meta_description_ru"] =  $ruDate["title"];

                $arr["slug"] =  Inflector::slug($arr["title"]);
                $arr["partner_id"] = (string)$item->sku;// $arr["article"];
                $arr["not_available"] = $not_available;
                
               // $codeParts = explode(" ", $arr["code"]);
                $arr["sku_group"] = $skuGroup;
                if(
                    (string)$item->price == ""
                    || $arr["slug"] == ""
                    || $arr["price"] == ""
                ){
                    continue;
                }
                //if(!isset($arr["options"]["color"]) && $size )
                //    echo "NO COLOR ".$arr["sku_group"]." ".$arr["code"]." ".$size."\r\n";
                //elseif((isset($arr["options"]["color"]) && $size))
                //    echo $arr["sku_group"]." ".$arr["code"]." ".$arr["options"]["color"]." ".$size."\r\n";
              //  if((isset($arr["options"]["color"]) && $size))
            //        $cats[$arr["sku_group"]][md5($arr["sku_group"].$arr["options"]["color"])][] = $size;
                $attr = [];
                \common\Helpers\Partners::availableProp($arr['not_available'], $attr);
                $lng = "uk";
                $arr["options"] = array_merge(
                    $arr["options"],
                    $attr[$lng]);
    
                if(!in_array($arr["sku_group"], $ids)){
                    $arr["is_main"] = 1;
                    $ids[] = $arr["sku_group"];
                }else{
                    $arr["is_main"] = 0; 
                }
               
              /*  if((string)$item->id_category == ""){
                    $key = "БЕЗ КАТЕГОРИИ";
                }else{
                    $key = $this->getCategoriesDataArray((string)$item->id_category);
                    if($key == ""){
                        $key = "БЕЗ КАТЕГОРИИ";
                      
                    }
                }*/
               // $cats[(string)$item->id_category] = (string)$item->id_category;
                
                
                $slug = Inflector::slug($key);
    
                if(!isset($this->data[$slug])){
                    $this->data[$slug] = [ "cat" => [
                            "translates" =>[
                                "title" => $key,
                                "meta_title" => $key,
                                "meta_description" => $key,
                                "seo" => $key,
                            ],
                            "slug" => $slug,
                            "partner_slug" => $slug,
                            "partner_id" => $slug,
                            "partner" => $this->partnerName, 
                        ]    
                    ];
                }
                
                if($size != ""){
                   
                    $arr["meta_title"] = $arr["meta_description"] = $arr["title"];// = $this->clearTitle($arr["title"],$size);
                   
                    $sizeArr =  [
                        "name" => $size,
                        "not_available" => $arr["not_available"],
                        "price" => $arr["price"],
                        "code" => $arr["code"],
                         
                    ];
                    $arr["price"] = "0.00";
               
                  //  if((string)$item->Dimensions != ""){
                    //    $size["params"] = (string)$item->Dimensions;
                  //  }
                   // $colCode = (string)$item->color; //$arr["options"]["colorcode"];

                    $sizeid = $arr["sku_group"].$arr["options"]["color"];
                    if(!isset($this->data[$slug]['products'][$sizeid])){
                        $arr["sizes"][] = $sizeArr;
                        $this->data[$slug]['products'][$sizeid] = $arr;
                    
                    }else{
                    
                        $this->data[$slug]['products'][$sizeid]["sizes"][] = $sizeArr;
                    }

                }else{  
                    $this->data[$slug]['products'][] = $arr;
                }
                  
             //   if(isset($this->data[$slug]['products'][$arr["code"]]["sizes"]) && count($this->data[$slug]['products'][$arr["code"]]["sizes"]) >1){
              //      print_r($this->data[$slug]['products']);exit;
              //  }
                    
                $i++;
             }
             
          //  print_r($arr);
             
        }
      //  print_r( $cats);
      //  exit;
      // print_r($this->data);
       // exit;
      //  print_r($codes2);
       // print_r($codes);
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
        $this->initData("ru");
      
        $this->createPartnerMainCat();
        echo "init data ".date("d-m-Y H:i:s")." \r\n";
        $this->makeCats();
        echo date("d-m-Y H:i:s")." make cats \r\n";
   
        echo "end ".date("d-m-Y H:i:s")." \r\n";
    }
    function actionCreateCats(){
        $this->initData("ru"); // init data
        $this->createPartnerMainCat(); 
        $this->makeCats();
    }
    
    function actionIndex(){
        
        $this->initData("ru"); // init data
        
 //
      //  $this->makeCats();
        
       
    }
   
}
