<?php
/**
 * Created by PhpStorm.
 * User: HQM
 * Date: 2017/5/6
 * Time: 17:06
 */
/* @var $model \common\models\Repair */

$this->title = '报事报修详情页';
$this->params['breadcrumbs'][] = $this->title;

echo \components\inTemplate\widgets\BackBtn::widget(['url' => ['index']]);

\components\inTemplate\widgets\IBox::begin();
?>

<div class="row">
    <div class="ibox-content">
        <form method="get" class="form-horizontal">
            <div class="form-group">
                <label class="col-sm-2 control-label">用户名</label>
                <div class="col-sm-10">
                    <?= $model->name ?>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">手机号</label>
                <div class="col-sm-10">
                    <?= $model->tel ?>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">地址</label>
                <div class="col-sm-10">
                    <?= $model->address ?>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">类型</label>
                <div class="col-sm-10">
                    <?= $model->flowStyleText ?>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">提交日期</label>
                <div class="col-sm-10">
                    <?= date('Y-m-d', $model->created_at) ?>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">描述：</label>
                <div class="col-sm-10">
                    <?= $model->content ?>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">状态：</label>
                <div class="col-sm-10">
                    <?= $model->statusText ?>
                </div>
            </div>
        </form>
    </div>
</div>

<?php
\components\inTemplate\widgets\IBox::end();