<?php
namespace common\components;

use omgdef\multilingual\MultilingualTrait;
use yii\db\ActiveQuery;

class MultilingualQuery extends ActiveQuery
{
    use MultilingualTrait;
}