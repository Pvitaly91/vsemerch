<?php
namespace common\components;

use common\models\Language;
use yii\helpers\ArrayHelper;

Class I18n extends \metalguardian\i18n\components\I18n
{
    public function init()
    {
        $this->languages = Language::getLanguagesAsArray();

        parent::init();
    }
}