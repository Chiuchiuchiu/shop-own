<?php


/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var array $projectRegion */
/* @var int $projectId */
/* @var string $pDomain */

use common\models\Project;
use \components\inTemplate\widgets\Html;

$this->title = '楼盘管理';
$this->params['breadcrumbs'][] = $this->title;

?>

<?php
\components\inTemplate\widgets\IBox::begin(['title'=>'搜索']);
\components\inTemplate\widgets\ActiveForm::begin(['layout' => 'horizontal', 'method' => 'get', 'action' => \yii\helpers\Url::toRoute('/parking-order/project')]);
?>
    <div class="form-group">

        <div class="col-sm-2">
            <div class="input-group m-b col-sm-12">

                <?php echo \components\inTemplate\widgets\Chosen::widget([
                    'name' => 'projectId',
                    'value' => $projectId,
                    'items' => $projects,
                ])?>

            </div>
        </div>

        <div class="col-sm-2">
            <div class="input-group m-b">
                <span class="input-group-btn">
                    <button type="submit" class="btn btn-primary">查找</button>
                </span>
            </div>
        </div>
    </div>
<?php
\components\inTemplate\widgets\ActiveForm::end();
\components\inTemplate\widgets\IBox::end();
?>

<?php \components\inTemplate\widgets\IBox::begin(['title' => '-', 'iboxContentStyle' => 'padding: 0px 20px;']); ?>

    <p>
        <button class="btn btn-success get-order" type="button">获取项目订单数</button>
        <a class="btn btn-primary" type="button" href="create">新增项目</a>
        <input type="hidden" id="sum-status" value="0">
    </p>

<?php \components\inTemplate\widgets\IBox::end(); ?>

    <div class="col-lg-1">
        <div>总条数：<?= $dataProvider->totalCount ?></div>
    </div>

<?=\components\inTemplate\widgets\IBox::widget([
    'content'=>\components\inTemplate\widgets\GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'id',
            [
                'label' => '项目',
                'format' => 'raw',
                'value' => function(\common\models\ProjectParkingOneToOne $model){
                    return Html::tag('p', $model->project->house_name, ['class' => 'text-info project-n', 'data-id' => $model->project_house_id]);
                }
            ],
            'typeText',
            'app_id',
            'app_key',
            'parking_id',
            [
                'label' => '订单数',
                'format' => 'raw',
                'value' => function(\common\models\ProjectParkingOneToOne $model){
                    return Html::tag('p', '-', ['class' => 'text-info', 'id' => "pro_{$model->project_house_id}"]);
                }
            ],
            [
                'label' => '链接地址',
                'format' => 'raw',
                'value' => function (\common\models\ProjectParkingOneToOne $model) use($pDomain){
                    $projectUrlKey = 'http://' . $model->project->url_key . '.';
                    return $projectUrlKey . $pDomain . $model->parkingUrl;
                }
            ],
            [
                'label' => '草料二维码（生成二维码）',
                'format' => 'raw',
                'value' => function (){
                    return Html::a('生成二维码', 'https://cli.im/url', ['target' => '_blank']);
                }
            ],
            [
                'class' => \components\inTemplate\widgets\RBACActionColumn::className(),
                'template' => ' {delete}',
            ]
        ],
    ])
])?>

<?php \common\widgets\JavascriptBlock::begin(); ?>

<script>
    $('.get-order').on('click', function (){
        var projectIds = [];
        $.each($('.project-n'), function (){
            projectIds.push($(this).attr('data-id'));
        });
        projectIds = projectIds.join(',');

        $.getJSON('/parking-order/project-order-c', {projectIds:projectIds}, function (res){
            $(res.data).each(function (key, index){
                $('#pro_'+index.house_id).html(index.c);
            });
        })

    });
</script>

<?php \common\widgets\JavascriptBlock::end(); ?>
