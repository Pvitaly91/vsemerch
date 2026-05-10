<?php
namespace common\components\urlManager;

use common\models\Language;
use Yii;
use yii\helpers\ArrayHelper;

class LangUrlManager extends UrlManager
{
    public $languages = [];
	public $lang = 'ru';
    public $langParam = 'language';

    public function init()
    {
        $this->languages = Language::getLanguagesAsArray();

        parent::init();
    }
}