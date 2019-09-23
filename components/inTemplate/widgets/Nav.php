<?php
/**
 * Created by
 * Author: zhao
 * Time: 16/5/11 15:52
 * Description:
 */

namespace components\inTemplate\widgets;


use components\rbac\RBACInterface;
use yii\base\Widget;
use yii\base\InvalidConfigException;
use \Yii;

/**
 * Class Nav
 * @package components\inTemplate\widgets
 * Description:
 * * For example:
 *
 * ```php
 * echo Nav::widget([
 *     'items' => [
 *         [
 *             'name' => 'Home',
 *             'url' => ['/index'],
 *             'linkOptions' => [...],
 *             'visible' => Yii::$app->user->isGuest,
 *             'items'=>[
 *                  ......
 *              ]
 *         ],
 *     ],
 *     'options' => ['class' =>'nav-pills'], // set this to nav-tab to get tab-styled navigation
 * ]);
 * ```
 */
class Nav extends Widget
{


    public $items = [];
    public $route = null;
    public $params = null;

    public function init()
    {
        if ($this->route === null && Yii::$app->controller !== null) {
            $this->route = Yii::$app->controller->getRoute();
        }
        if ($this->params === null) {
            $this->params = Yii::$app->request->getQueryParams();
        }
    }


    public function renderItems()
    {
        $items = [];
        foreach ($this->items as $i => $item) {
            $items[] = $this->renderItem($item);
        }

        return implode("\n", $items);
    }

    public function run()
    {
        return $this->renderItems();
    }


    protected function renderItem($item, $level = 1)
    {
        if (is_string($item)) {
            return $item;
        } else if ($item instanceof RBACInterface) {
            if (!$item->visible) {
                return '';//无权限
            }
            if (!isset($item->name)) {
                throw new InvalidConfigException("The 'name' option is required.");
            }
            $name = $item->name;
            $options=[];
//            $options = is_array($item->option) ? $item->option : []; //ArrayHelper::getValue($item, 'options', []);
            $items = $item->navItems;
            $url = $item->url;

            if ($level === 1) {
                $name = Html::tag('span', $name, ['class' => 'nav-label']);
                if (isset($item['icon'])) {
                    $name = Html::tag('i', '', ['class' => $item['icon']]) . $name;
                }
            }
            $active = $this->isItemActive($item);
            if (!is_array($items) || sizeof($items)<1) {
                $items = '';
            } else {
                $itemsHtml=[];
                foreach ($items as $val)
                    $itemsHtml[] = $this->renderItem($val, ($level + 1));

                $items = Html::tag('ul', implode("", $itemsHtml), ['class' => 'nav nav-' . ($level > 1 ? 'third' : 'second') . '-level']);
                $name .= Html::tag('span', '', ['class' => 'fa arrow']);
            }
            if ($active) {
                Html::addCssClass($options, 'active');
            }
            return Html::tag('li', Html::a($name, $url) . $items, $options);
        }
        return '';
    }

    protected function isItemActive(RBACInterface $item)
    {
        $active = false;
        //route最有先
        if (!empty($item->route)) {
            $active = $this->route == $item->route;
        }
        if (!$active && isset($item->url)) {
            $url = explode('/', ltrim($item->url, '/'));
            $url = $url[0] . '/' . (isset($url[1]) ? $url[1] : '');
            $active = ($item->url == '/' && Yii::$app->defaultRoute == $this->route)
                || $url == $this->route;
        }
        if (!$active && isset($item->navItems)) {
            foreach ($item->navItems as $val) {
                $active = $this->isItemActive($val);
                if ($active) break;
            }
        }
        return $active;

    }
}