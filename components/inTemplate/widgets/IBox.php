<?php
/**
 * Created by
 * Author: zhao
 * Time: 16/5/11 15:52
 * Description:
 */

namespace components\inTemplate\widgets;


use yii\base\Widget;
/**
 * Class Nav
 * @package components\inTemplate\widgets
 * Description: 创建通用的IBox面板
 * * For example:
 *
 * ```php
 * echo IBox::widget([
 *     'content' => 'string HTML',
 *      'title'  => 'name'
 * ]);
 * ```
 */
class IBox extends Widget
{


    public $content = null;
    public $title = null;
    public $addClass = null;
    public $addStyle = null;
    public $iboxContentStyle = null;

    public function init()
    {
        parent::init();
        if(is_null($this->title)){
            $this->title = $this->getView()->title;
        }
        if($this->title){
            $this->title = sprintf('<div class="ibox-title">%s</div>',$this->title);
        }
        if(is_null($this->content)){
            ob_start();
            ob_implicit_flush(false);
        }
    }


    public function run()
    {
        if(is_null($this->content)){
            $this->content = ob_get_clean();
        }
        echo $this->createDOM();
    }

    protected function createDOM()
    {
        return sprintf(
            '<div class="ibox float-e-margins %s" style="%s">%s<div class="ibox-content" style="%s">%s</div></div>',
            $this->addClass,
            $this->addStyle,
            $this->title,
            $this->iboxContentStyle,
            $this->content
        );
    }
}