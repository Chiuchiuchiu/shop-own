<?php
/**
 * Created by PhpStorm.
 * User: HQM
 * Date: 2017/3/28
 * Time: 19:44
 */

/* @var $dataProvider \yii\data\ActiveDataProvider*/
/* @var $model \common\models\PmOrderFpzzItem */
/* @var $pmOrderFpzz \common\models\PmOrderFpzz*/

/* @var $id int*/

echo \components\inTemplate\widgets\BackBtn::widget(['url' => ['detail?id=' . $id]]);

\components\inTemplate\widgets\IBox::begin();
echo \components\inTemplate\widgets\IBox::widget([
    'content'=>\components\inTemplate\widgets\GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'spmc',
            'ggxh',
            'dw',
            /*[
                'label' => '商品数量',
                'value' => function (\common\models\PmOrderFpzzItem $model){
                    return empty($model->sl) ? '' : $model->sl;
                },
            ],
            [
                'label' => '单价',
                'value' => function (\common\models\PmOrderFpzzItem $model){
                    return empty($model->sl) ? '' : $model->dj;
                }
            ],
            'je',*/
            'origin_amount',
            'slv',
//            'se',
            'isUseText',
        ],
    ])
]);

\components\inTemplate\widgets\IBox::end();
?>

<?php \common\widgets\JavascriptBlock::begin() ?>

<script type="text/javascript">

    $(function (){
        var html = '<tr><td>含税合计</td><td colspan="9"><?= $model->origin_amount ?></td></tr>';
        $('table tbody').append(html)
    })

</script>

<?php \common\widgets\JavascriptBlock::end() ?>
