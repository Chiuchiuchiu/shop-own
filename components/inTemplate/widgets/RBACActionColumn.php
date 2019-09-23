<?php
/**
 * Created by
 * Author: zhao
 * Time: 16/5/16 14:51
 * Description:
 */

namespace components\inTemplate\widgets;

use components\rbac\RBACPermissionInterface;
use Yii;
use yii\base\ErrorException;
use yii\helpers\Url;

/**
 * Class AccessActionColumn
 * @package components\inTemplate\widgets
 * Description:带有权限控制的ActionColumn
 */
class RBACActionColumn extends \yii\grid\ActionColumn
{

    public $template = "{update}";
    public $name = "";
    public $referrer = true;

    public function init()
    {
        parent::init();
        if (!Yii::$app->user->identity instanceof RBACPermissionInterface)
            throw new ErrorException("user need instanceof RBACPermissionInterface");
    }


    protected function initDefaultButtons()
    {
        if (!isset($this->buttons['view'])) {
            $this->buttons['view'] = function ($url, $model, $key) {
                $options = array_merge([
                    'title' => Yii::t('yii', 'View'),
                    'aria-label' => Yii::t('yii', 'View'),
                    'data-pjax' => '0',
                    'class' => 'btn btn-xs btn-info'
                ], $this->buttonOptions);
                return Html::a('<span class="glyphicon glyphicon-eye-open"></span> 查看', $url, $options);
            };
        }
        if (!isset($this->buttons['update'])) {
            $this->buttons['update'] = function ($url, $model, $key) {
                $options = array_merge([
                    'title' => Yii::t('yii', 'Update'),
                    'aria-label' => Yii::t('yii', 'Update'),
                    'data-pjax' => '0',
                    'class' => 'btn btn-xs btn-success'
                ], $this->buttonOptions);
                return Html::a('<i class="glyphicon glyphicon-pencil"></i> 编辑', $url.($this->referrer?(strpos($url,"?")>0?'&':'?').'_referrer='.urlencode('/'.Yii::$app->request->url):''), $options);
            };
        }
        if (!isset($this->buttons['pay-return'])) {
            $this->buttons['pay-return'] = function ($url, $model, $key) {
                $options = array_merge([
                    'title' => Yii::t('yii', 'pay-return'),
                    'aria-label' => Yii::t('yii', 'pay-return'),
                    'data-pjax' => '0',
                    'data-confirm'=>'您确定要退款吗？',
                    'class' => 'btn btn-xs btn-success'
                ], $this->buttonOptions);
                return Html::a('<i class="glyphicon glyphicon-pencil"></i> 退款', $url.($this->referrer?(strpos($url,"?")>0?'&':'?').'_referrer='.urlencode('/'.Yii::$app->request->url):''), $options);
            };
        }

        if (!isset($this->buttons['delete'])) {
            $this->buttons['delete'] = function ($url, $model, $key) {
                $options = array_merge([
                    'title' => Yii::t('yii', 'Delete'),
                    'aria-label' => Yii::t('yii', 'Delete'),
                    'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                    'data-method' => 'post',
                    'data-pjax' => '0',
                    'class' => 'btn btn-xs btn-danger'
                ], $this->buttonOptions);
                return Html::a('<i class="glyphicon glyphicon-trash"></i> 删除', $url.($this->referrer?(strpos($url,"?")>0?'&':'?').'_referrer='.urlencode('/'.Yii::$app->request->url):''), $options);
            };
        }

        //添加的 update-house
        if (!isset($this->buttons['update-house'])) {
            $this->buttons['update-house'] = function ($url, $model, $key) {
                $options = array_merge([
                    'title' => Yii::t('yii', 'Update'),
                    'aria-label' => Yii::t('yii', 'Update'),
                    'data-pjax' => '0',
                    'class' => 'btn btn-xs btn-success'
                ], $this->buttonOptions);
                return Html::a('<i class="glyphicon glyphicon-pencil"></i> 编辑', $url.($this->referrer?(strpos($url,"?")>0?'&':'?').'_referrer='.urlencode('/'.Yii::$app->request->url):''), $options);
            };
        }

        //编辑房子结构
        if (!isset($this->buttons['edit-structure'])) {
            $this->buttons['edit-structure'] = function ($url, $model, $key) {
                $options = array_merge([
                    'title' => Yii::t('yii', '编辑楼盘结构'),
                    'aria-label' => Yii::t('yii', '编辑楼盘结构'),
                    'data-pjax' => '0',
                    'class' => 'btn btn-xs btn-success'
                ], $this->buttonOptions);
                return Html::a('<i class="glyphicon glyphicon-pencil"></i> 编辑楼盘结构', $url.($this->referrer?(strpos($url,"?")>0?'&':'?').'_referrer='.urlencode('/'.Yii::$app->request->url):''), $options);
            };
        }

        if (!isset($this->buttons['update-structure'])) {
            $this->buttons['update-structure'] = function ($url, $model, $key) {
                $options = array_merge([
                    'title' => Yii::t('yii', 'Update'),
                    'aria-label' => Yii::t('yii', 'Update'),
                    'data-pjax' => '0',
                    'class' => 'btn btn-xs btn-success'
                ], $this->buttonOptions);
                return Html::a('<i class="glyphicon glyphicon-pencil"></i> 编辑', $url.($this->referrer?(strpos($url,"?")>0?'&':'?').'_referrer='.urlencode('/'.Yii::$app->request->url):''), $options);
            };
        }
    }

    protected function renderDataCellContent($model, $key, $index)
    {
        return preg_replace_callback('/\\{([\w\-\/]+)(\?[\w\-=%&]+)?\\}/', function ($matches) use ($model, $key, $index) {
            $name = $matches[1];
            if(!isset($matches[2])) $matches[2]='';
            $params = ltrim($matches[2],'?');
            $urlParams = $this->createUrlParam($name, $model, $key, $index);
            if (!Yii::$app->user->identity->hasPermission(trim(Url::toRoute($urlParams[0]), '/')))
                return '';

            if (isset($this->buttons[$name])) {
                return call_user_func($this->buttons[$name], Url::toRoute($urlParams), $model, $key);
            } else {
                $params = explode('&',$params);
                foreach($params as $row){
                    if(strpos($row,'=')===false){
                        $row.='='.$model->$row;
                    }
                    $row = explode('=',$row);
                    $urlParams[$row[0]]=$row[1];
                }
                if(!isset($urlParams['_referrer'])){
                    $urlParams['_referrer']  = urlencode('/'.Yii::$app->request->url);
                }
                return Html::a($this->name[$name], Url::toRoute($urlParams), [
                    'class' => 'btn btn-xs btn-info'
                ]);
            }
        }, $this->template);
    }

    public function createUrlParam($action, $model, $key, $index)
    {
        if (is_callable($this->urlCreator)) {
            return call_user_func($this->urlCreator, $action, $model, $key, $index);
        } else {
            $params = is_array($key) ? $key : ['id' => (string)$key];
            $params[0] = $this->controller ? $this->controller . '/' . $action : $action;
            return $params;
        }
    }
}