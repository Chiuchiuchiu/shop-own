<?php


/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $model \common\models\Project */
/* @var $house \common\models\House */

use components\inTemplate\widgets\Html;

$this->title = $house->ancestor_name.' - 楼盘明细';
$this->params['breadcrumbs'][] = $this->title;

echo $house->parent_id>0?\components\inTemplate\widgets\BackBtn::widget([
    'url' => ['detail', 'id' => $model->house_id, 'house_id' => $house->parent_id],
    'name' => '返回上一级',
]):'';

echo \components\inTemplate\widgets\BackBtn::widget([
    'url' => ['index'],
    'name' => '返回项目',
    'option'=>[
        'class'=>'btn btn-w-m btn-white pull-right'
    ]
]);
echo \components\inTemplate\widgets\IBox::widget([
    'content' => \components\inTemplate\widgets\GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'house_id',
            'house_name',
            'ancestor_name',
            'house_alias_name',
            [
                'label' => '是否在前端显示',
                'value' => 'showStatusText',
            ],
            'ordering',
            'reskind',
            'level',
            [
                'class' => \components\inTemplate\widgets\RBACActionColumn::className(),
                'template' => '{update-house?house_id} {detail}',
                'buttons' => [
                    'detail' => function ($url, $house, $key) use ($model) {
                        /**
                         * @var $house \common\models\House
                         */
                        return $house->hasChild ? Html::a(
                            "查看明细",
                            ['detail', 'id' => $model->house_id, 'house_id' => $house->house_id],
                            ['class' => 'btn btn-info btn-xs']
                        ) : '';
                    },
                ]
            ],
        ],
    ])
]);
?>

<?php \common\widgets\JavascriptBlock::begin(); ?>

<script type="text/javascript">

    $(function (){
        var tdValue = 0;
        $('#w0>table>tbody>tr').each(function (){
            $(this).children('td:eq(5)').bind('dblclick', function (){
                tdValue = parseInt($(this).html()) ? parseInt($(this).html()) : parseInt($(this).children('input').val()) ? parseInt($(this).children('input').val()) : 0;
                updateOrdering(this);
            });
        });
        function updateOrdering(obj)
        {
            $(obj).html("<input onkeyup='this.value=this.value.replace(/\\D/g,\"\")' type='text' id='edit' value='"+tdValue+"'>");
            var houseId = $(obj).parent().attr('data-key');
            $(obj).children('input').keyup(function (e){
                if (e.keyCode == 13) {
                    tdValue = $(obj).children('input').val();
                    $.getJSON('update-ordering', {house_id:houseId, ordering:tdValue}, function (res){
                        if (res.code == 0){
                            tdValue = res.data.ordering;
                        }
                    });
                    $(obj).html(tdValue);
                }
            });
        }
    });

</script>

<?php \common\widgets\JavascriptBlock::end(); ?>
