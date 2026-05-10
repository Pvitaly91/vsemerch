<?php 
namespace common\components;
class Text
{
    public static function getShort($text,$count){
        $text=strip_tags($text);
        if(mb_strlen($text, 'UTF-8')>$count)
        {
        //$pos = mb_strpos($text, ' ', $count, 'UTF-8');
        $text = mb_substr($text, 0, $count, 'UTF-8');
        return $text.'...';
        }
        else
        return $text;
    }
}