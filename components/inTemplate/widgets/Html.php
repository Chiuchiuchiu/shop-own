<?php
/**
 * Created by
 * Author: zhao
 * Time: 16/7/30 14:06
 * Description:
 */

namespace components\inTemplate\widgets;


class Html extends \yii\bootstrap\Html
{
    public static function dropDownList($name, $selection = null, $items = [], $options = [])
    {
        if(!isset($options['class'])) $options['class'] = 'form-control m-b';
        return parent::dropDownList($name, $selection, $items, $options);
    }

    public static function input($type, $name = null, $value = null, $options = [])
    {
        if(!isset($options['class'])) $options['class'] = 'form-control';
        return parent::input($type,$name, $value, $options);
    }

    public static function button($content = 'Button', $options = [])
    {
        if(!isset($options['color'])) $options['color']= $options['color']= 'primary';
        if(!isset($options['class'])) $options['class'] = 'btn btn-w-m btn-'.$options['color'];
        unset($options['color']);
        return parent::button($content, $options);
    }

    public static function textarea($name, $value = '', $options = [])
    {
        if(!isset($options['class'])) $options['class'] = 'form-control';
        return parent::textarea($name, $value, $options);
    }

    /**
     * @param $text
     * @param null $url
     * @param array $options
     * @return string
     * Description:
     * color
     *  -info
     *  -primary
     *  -success
     *  -warning
     *  -danger
     *  -white
     */
    public static function buttonA($text, $url = null, $options = []){
        if(!isset($options['color'])) $options['color']= $options['color']= 'info';
        if(!isset($options['class'])) $options['class'] = 'btn btn-xs btn-'.$options['color'];
        
        return self::a($text,$url,$options);

    }
    public static function a($text, $url = null, $options = [])
    {
        return parent::a($text, $url, $options);
    }

    public static function img($src, $options = [])
    {
        $src = \Yii::getAlias($src);
        return parent::img($src, $options);
    }


}