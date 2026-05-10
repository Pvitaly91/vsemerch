<?php
namespace common\components;
use Yii;
use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
 
class LangWidget extends Widget{
	public $active;
	
	public function init(){
		parent::init();
		/*
                $link = (preg_replace(array('/\/(ru|en)/i','/$/'),'',$_SERVER['REQUEST_URI']));
                $pth = explode('/',$link);
				$pth = array_diff($pth, array(''));

                $base = (!empty($pth[1])) ? $pth[1] : '';

                $this->link['ru'] = !empty($link)?$link:'/';
                $this->link['en'] = '/en'.((count($pth)>0)?'/'.implode('/',$pth):'');
		*/
	}

	public function translateCurrentRequest($language)
	{
		$params = ArrayHelper::merge(
			['/' . ltrim(Yii::$app->requestedRoute, '/')],
			Yii::$app->request->getQueryParams(),
			[
				'language' => $language,
			]
		);
        $link = urldecode(Url::to($params));
        $partsLink = explode("/",$link);
        if(isset($partsLink[1]) && $partsLink[1] == "uk" && $language == "uk"){
            unset($partsLink[1]);
        }
        $link = implode("/",$partsLink);
        if($language == "uk" && $link == ""){
            $link = "/";
        }

		return $link;
	}
	
	public function run(){
        	return 	'<ul>
			<li><a href="'.$this->translateCurrentRequest('uk').'" '.(($this->active=='uk')?'class="active"':'').'>UA</a></li>
			<li><a href="'.$this->translateCurrentRequest('ru').'" '.(($this->active=='ru')?'class="active"':'').'>RU</a></li>
        </ul>';
            /*
		return 	'<ul>
			<li><a href="'.$this->translateCurrentRequest('uk').'" '.(($this->active=='uk')?'class="active"':'').'>UA</a></li>
			<li><a href="'.$this->translateCurrentRequest('ru').'" '.(($this->active=='ru')?'class="active"':'').'>RU</a></li>
			<li><a href="'.$this->translateCurrentRequest('en').'" '.(($this->active=='en')?'class="active"':'').'>En</a></li>
        </ul>';*/
	}
}
