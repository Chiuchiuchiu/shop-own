<?php
/**
 * Created by
 * Author: zhao
 * Time: 16/5/16 22:54
 * Description:
 */

namespace components\inTemplate\widgets;


use components\rbac\RBACInterface;
use components\rbac\RBACPermissionInterface;
use yii\base\ErrorException;
use yii\base\InvalidParamException;
use yii\base\Widget;
use yii\helpers\Inflector;

/**
 * Class RBACButton
 * @package components\inTemplate\widgets
 * Description:
 * ```php
//  RBACButton:widget([
//      'route'=>'controller/action',
//      'url'=>'controller/action?id=3',
//      'icon'=>'fa fa-icon',
//      'option'=>[
//          'class'=>'btn'
//      ]
// ])
 *
 * ```
 */
class RBACButton extends Widget
{
    public $route=null;
    public $url=null;
    public $icon;
    public $option=[];
    public $label=null;

    private $access=null;
    public function init()
    {
        if(!\Yii::$app->user->identity instanceof RBACPermissionInterface)
            throw new ErrorException("user need instanceof RBACPermissionInterface");
        $this->route = Inflector::camel2id($this->route);
        $this->access = \Yii::$app->rbac->findByRoute($this->route);
        if(is_null($this->access)) $this->access = \Yii::$app->rbac->findByAuthRoute($this->route);
        if(is_null($this->access)) throw new InvalidParamException($this->route);
        if(!is_null($this->icon)){
            $this->access->icon=$this->icon;
        }
        if(!is_null($this->label))
            $this->access->name = $this->label;
        parent::init();
    }
    public function run()
    {
        if($this->access instanceof RBACInterface && \Yii::$app->user->identity->hasPermission($this->access))
            return Html::a(
                empty($this->access->icon)?
                $this->access->name:sprintf('<span class="%s"></span> %s',$this->access->icon,$this->access->name),
                empty($this->url)?$this->access->url:$this->url,
                $this->option);
        else
            return '';
    }

}