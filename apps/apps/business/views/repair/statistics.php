<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/9/19
 * Time: 14:44
 */

$this->title = '报事报修数据统计';
$this->params['breadcrumbs'][] = $this->title;

\components\inTemplate\widgets\IBox::begin(['title'=>'搜索']);
$from = \components\inTemplate\widgets\ActiveForm::begin(['method' => 'get', 'action' => \yii\helpers\Url::to(['repair/index'])])
?>

<div class="form-group">

    <div class="col-sm-7">
        <label>项目</label>
        <div id="region_ctr" class="row">
            <div class="col-sm-4 sl ctr-template">

                <?php echo \components\inTemplate\widgets\Chosen::widget([
                    'name' => 'house_id',
                    'value' => $house_id,
                    'items' => $projectsArray,
                ])?>

            </div>

        </div>
    </div>
</div>

<div class="form-group">

    <div class="col-sm-2">
        <label>&nbsp;</label>
        <?php echo \yii\bootstrap\Html::submitButton('查询', ['class' => 'btn btn-info btn-block']) ?>
    </div>

</div>

<?php
\components\inTemplate\widgets\ActiveForm::end();
\components\inTemplate\widgets\IBox::end();
?>

<div>

</div>
