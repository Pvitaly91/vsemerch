<?php
namespace common\models\traits;

use common\models\CategoriesMap;

trait EditLink{
    public $section = [
        'common\\models\\Category' => "shop/category/update",
        'common\\models\\Product' => "shop/product/update",
        'frontend\\models\\News' => "blog/save",
        "frontend\\models\\Text" => "text/save",
        'frontend\\models\\Catalog' => "catalog/save",
        "common\\models\\Widget" => "widgets/save"
    ];
    function getSectionName(){
        $fullLink = false;
  
        if(isset($this->section[__CLASS__]) && ($link = $this->section[__CLASS__]) == true){
            $fullLink = "/admin/".$link."?id=".$this->id;
        }
        return $fullLink;
    }
    function isAdmin(){
        $adminUsers[] = 2;
        $result = false;
        if(in_array(\Yii::$app->User->GetId(), $adminUsers))
            $result =  true;
        
        return $result;
    }
    function getEditLink($params = false){
     
        if($this->isAdmin() 
            && ($link = $this->getSectionName()) == true
                
         ){
            
            $str =  '<a class="editLinck" href="'.$link.$params.'" target="blank" >Редактировать</a>';  

            if(get_class($this) == 'common\\models\\Product' && $this->size){
                $str .= ' <a class="editLinck" href="/admin/shop/product/table-size?id='.$this->id.'" target="blank" >Таблица размеров</a>' ;  
            }
			
			if(get_class($this) == 'common\\models\\Category'){
                if(isset($this->partner) && isset(\common\Helpers\Partners::$partners[$this->partner]) && !CategoriesMap::find()->where(["partner_slug" => $this->slug])->one() ){
                    $str .=  ' <a class="editLinck" href="/admin/categories-map/create?type=subcat&id='.$this->id.'" target="blank" >Переместить категорию</a>';  
                    $str .=  ' <a class="editLinck" href="/admin/categories-map/create?type=merge&id='.$this->id.'" target="blank" >Склеить категорию</a>';  
                }else{
                    
                    if(($catMap = CategoriesMap::find()->where(['site_slug' => $this->slug])->all()) == true)
                    {
                      //  dd($catMap);
                        //merge
                        if(\common\Helpers\Partners::isMain($this->partner)){
                            foreach($catMap as $item){
                                $catName = \common\models\Category::find()
                                        ->where(["slug" => $item->partner_slug])
                                        ->andWhere(["partner" => $item->partner])->one();

                                if($catName != null)
                                    $str .= '<a class="editLinck delSubCatMap" style=" color:red; text-align:center; margin-left:10px" href="/catmapdelete?id='.$this->id.'&pId='.$catName->id.'" target="blank" >Удалить: '.$item->partner.' '.$catName->title.' X</a> ';

                            }
                        }
                     }elseif(isset(\common\Helpers\Partners::$partners[$this->partner])){
                        //subcat
                       $str .= '<a class="editLinck delSubCatMap" style=" color:red; text-align:center; margin-left:10px" href="/catmapdelete?id='.$this->id.'" target="blank" >Удалить склейку категории X</a> ';
                    }
                 } 
            }
          
            return $str;
        }else
            return "";
    }
    function getOptionEditLink($optionID){
        if($this->isAdmin() && $optionID != NULL){
            return '<a class="editLinck small" href="/admin/i18n/translation/update?id='.$optionID.'" target="blank" >Редактировать</a>';  
        }
    }
    function getOptionMergeLink($slug,$cat_id,&$_map){
       // echo $slug." ".$cat_id." ".$propId;
         if($this->isAdmin() && $slug != NULL && isset($_map[$slug])){
            return '<a class="editLinck small" href="/admin/options-map/create?slug='.$slug.'&catId='.$cat_id.'&partner='.$_map[$slug].'" target="blank" >Склеить '.$_map[$slug].'</a>';  
        }
    }
    
    function getMappedPropsEditLink($slug,$catid,$propId){
       // dd($slug);
        if($this->isAdmin() && $slug != NULL){
            $mapping =\common\models\OptionMap::find()->where(["=","site_slug",$slug])->andWhere(["=","category_id",$catid])->all();
            $str = "";
            
            foreach($mapping as $map){
           
               // $option = \common\models\ProductOption::find()->where(['=',"slug_option",$map->partner_slug])->andWhere(['=',"partner",$map->partner])->one();
     
                $str .='<a class="editLinck small delOptionMap" style="display:block; color:red; text-align:center;" data-id="'.$map->id.'" href="#" target="blank" >'. strtoupper($map->partner)." ".$this->getPartnerPropertyTrans($map->partner_slug).' X</a> ';  
            }
              
            return $str; 
        }
    }
    function getPartnerPropertyTrans($key,&$id = false){
        
       if(($sm = \metalguardian\i18n\models\SourceMessage::find()->where(["=", "category", "partner_proprties_trans"])->andWhere(["=", "message", $key])->one()) == true){
           $id = $sm->id;
           if(($mes = \metalguardian\i18n\models\Message::find()->where(["id" => $sm->id])->andWhere(["language" => \Yii::$app->language])->one()) == true){
              return $mes->translation;
           }else{
                return $key;
           }
           
           
       }else{
           return $key;
       }
    }
   function mappedItems(){
     
       if(!isset(\common\Helpers\Partners::$partners[$this->partner])){
            $categoryMap = \common\models\CategoriesMap::find()->where(["=","site_slug",$this->slug])->all();
          //  dd($categoryMap); 
            $str = "";
            foreach($categoryMap as $map){
                $cat = \common\models\Category::find()
                        ->where(["=","slug",$map->partner_slug])
                        ->andWhere(["=","partner",$map->partner])
                        ->one();
              
                $str .='<a class="editLinck delCatMap" style=" color:red; text-align:center;" data-id="'.$map->id.'" href="#" target="blank" >'. strtoupper($map->partner)." ".$cat->title.' X</a> ';  
           
            }
            return $str;
       }

   }    
  
}