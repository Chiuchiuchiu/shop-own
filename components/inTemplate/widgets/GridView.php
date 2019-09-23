<?php
/**
 * Created by
 * Author: zhao
 * Time: 16/5/16 14:13
 * Description:
 */

namespace components\inTemplate\widgets;

/**
 * Class GridView
 * @package components\inTemplate\widgets
 * Description:
 *
 * ```php
 * <?= GridView::widget([
 *     'dataProvider' => $dataProvider,
 *     'columns' => [
 *         'id',
 *         'name',
 *         'created_at:datetime',
 *         // ...
 *     ],
 * ]) ?>
 * ```
 */
class GridView extends \yii\grid\GridView
{
    public $layout = "{items}\n{pager}";
}