<?php
namespace console\controllers;

use console\components\Controller;
use yii\helpers\Inflector;



class BergamoXmlController extends Controller {
    use \console\controllers\traits\common, \common\models\traits\Images;
    
    public $partnerName = "bergamo";
    public $data = [];
    public $loadPics = true;
    function initData(){
       // ini_set('memory_limit', '2G');
        //echo "fssfs";
		$this->_setIsMainStatus('bergamo');
        $this->_setIsMainStatus('totobi');
        $this->_setIsMainStatus('esuvenir');
        $this->_setIsMainStatus('eney');
        
       // exit;
		ini_set('memory_limit', '1024M');
        $this->initMap();
        if(!empty($this->data))
            return;
        
        $xml_file = file_get_contents('ftp://stock:Stua9000@ftp.bergamo.ua/items.xml');
        $xml = new \SimpleXMLElement($xml_file);
		
        $productIds = [];
        $i = 0;
       // echo count($xml->Item);
        
        $noCats = $usetProds = [];
        $it = [];
        // model:
        //image
        //slug
        //code
  
        //category_id
        //
        // translates:
        //title
        //description
        //meta_title
        //meta_description
        
        //ALTER TABLE `shop_product_size`
            //CHANGE `params` `params` varchar(24) COLLATE 'utf8_general_ci' NULL AFTER `not_available`;
        $map["Artikul"] = "code";
        $map["Name"] =  "title";
        $map["Opisanie"] =  "description";
        $map["Picture1"] = "image";
        
        $ext = [
            "FilterGroup",
            "artikulmodel",
            "CodePodatkova",
            "NameForDocuments",
            "Poznica",
            "Amountsupply",
            "Datesupply",
            "Indupakovka",
            "Subgroup",
            "Mupakovka",
            "Bupakovka",
            "Group",
            "Ostatok",
            "Store",
            "Odezhda",
            "type_id",
            "Code",
           "Colornumber",
            "colorcode" 
        ];
        $num = 10000000000000;
        $i = 0;
        $products = $ids = [];
        foreach ($xml->Item as $item) {
			
	
            if($num < $i ){
                break;
            }
            
         //   if($item->Odezhda == "false" )
         //       continue;
            
            $arr = [];
          
           foreach ($item as $name => $value){
                
                
                
                if(substr($name,0,7) != "Picture"){
                    if(!in_array($name, $ext) && (string)$item->$name != ""){
                        $optionValue = (string)$item->$name; 
                        if($name == "Color"){
                            $optParts = explode("_",$optionValue);
                            $optionValue = $optParts[0];
                        }
                        
                        if(isset($map[$name])){
                            $arr[strtolower($map[$name])] = $optionValue;
                        }else{
                            $arr["options"][strtolower($name)] = $optionValue;
                        } 
                        
                    }
                }elseif($name != "Picture1"){
                    $arr["more_photo"][] = (!empty((string)$item->$name )) ? 'ftp://stock:Stua9000@ftp.bergamo.ua/kartinki_dlya_saita/' . (string)$item->$name : null;
            
                }
                
            }
            $arr["image"] = (!empty((string)$item->Picture1)) ? 'ftp://stock:Stua9000@ftp.bergamo.ua/kartinki_dlya_saita/' . (string)$item->Picture1 : null;
            $arr["is_main"] = 1;
			if(!isset($arr["title"]) || (isset($arr["title"]) && $arr["title"] == '')){
				continue;
			}
            $arr["meta_description"] = $arr["meta_title"] = $arr["title"];
            $arr["slug"] =  Inflector::slug($arr["title"]);
            $arr["partner_id"] = (string)$item->Artikul;
            $arr['price'] = (float)str_replace(' ', '', (string)$item->Price);
        
             $arr["sku_group"] = substr((string)$item->Artikul,0,-strlen((string)$item->Colornumber)); //str_replace((string)$item->Colornumber, '', (string)$item->Artikul );
          //  $arr["options"]["sku_group"] = $arr["sku_group"];
		  
            if($arr['price'] == "0" || $arr["title"] == "")
                continue;
            $arr['not_available'] = ($item->InStock > 0) ? 0 : 1;
			
           // $arr["options"]['ostatok'] = $item->Ostatok;
              $i++;
            /*if((string)$item->Dimensions != ""){
                 $i++;
            }else{
                continue;
            }*/
           
          
           // echo $i."\r\n";
            if(!in_array($arr["sku_group"], $ids)){
                $arr["is_main"] = 1;
                $ids[] = $arr["sku_group"];
            }else{
                $arr["is_main"] = 0; 
            }

         
            /////////////Category////////////////////
            $Group = (string)$item->Group;
            $Subgroup = (string)$item->Subgroup;
           
            if($Subgroup == "")
               $key =$Group;
            else
               $key = $Subgroup;
            
            $key = ($key == "")?"БЕЗ КАТЕГОРИИ": $key;
            
          // if(!isset($usetProds[$Name])) 
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
            \common\Helpers\Partners::availableProp($arr['not_available'], $attr);
            $arr["options"] = array_merge($arr["options"],$attr["uk"]);
            if($item->Odezhda == "true"){
				if(isset($arr["options"]["razmer"])){
					$size =  [
						"name" => $arr["options"]["razmer"],
						"not_available" => $arr["not_available"],
						"price" => $arr["price"],
						"code" => $arr["code"],
						 
					];
				}
                if((string)$item->Dimensions != ""){
                    $size["params"] = (string)$item->Dimensions;
                }
                $colCode = (string)$item->colorcode; //$arr["options"]["colorcode"];
                if(!isset($this->data[$slug]['products'][$arr["sku_group"]."_".$colCode])){
                    $arr["sizes"][] = $size;
                    $this->data[$slug]['products'][$arr["sku_group"]."_".$colCode] = $arr;
                }else{
                    $this->data[$slug]['products'][$arr["sku_group"]."_".$colCode]["sizes"][] = $size;
                }
            }else  
                $this->data[$slug]['products'][] = $arr;
            
       
       //    print_r($this->data);
       //     exit;
        }   
           //    
            //    $this->data[$slug]['products'][] = $arr;
           //  $usetProds[$Name] = "";
          //  }
             
       
        
       // exit;
    //  print_r($this->data);
    //  exit;
        // $this->data["products"] = $products;
          // SIZE 
              /* 
               "name",
               "product_id", 
               "not_available",
                "price"
               "code"
              
              */
           
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