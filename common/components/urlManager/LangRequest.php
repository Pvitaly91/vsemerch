<?php
namespace common\components\urlManager;

use Yii;
use yii\web\Request;

class LangRequest extends Request
{
    protected function resolvePathInfo()
    {

		foreach(Yii::$app->urlManager->languages as $lang){
			if(strpos('/'.$_SERVER['REQUEST_URI'], '/'.$lang)){Yii::$app->language = $lang;}
		}
	return parent::resolvePathInfo();
	}
}