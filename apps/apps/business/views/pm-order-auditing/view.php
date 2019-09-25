<?php


/* @var $this yii\web\View */
/* @var $model \common\models\PmOrderAuditing */
/* @var $item \common\models\PmOrderItem */
/* @var $logs array */
/* @var $log \common\models\PmOrderAuditingLog */

$this->title = '账单审核';
$this->params['breadcrumbs'][] = $this->title;
echo \components\inTemplate\widgets\BackBtn::widget(['url'=>['index']]);
\components\inTemplate\widgets\IBox::begin();
$fee = 0;
$total = 0;
?>
<table class="table table-striped table-bordered">
    <thead>
    <tr>
        <th>ID</th>
        <th>住宅地址</th>
        <th>合同号</th>
        <th>缴费日期</th>
        <th>缴费项目</th>
        <th>账单号</th>
        <th>缴费金额</th>
        <th>手续费</th>
        <th>应结金额</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($model->pmOrderItem as $item):
        $_fee = round($item->amount * 0.006, 2);
        $_payed = $item->amount - $_fee;
        $total += round($item->amount, 2);
        $fee += $_fee;
        ?>
        <tr>

            <td><?= $item->id ?></td>
            <td><?= $item->pmOrder->house->showName ?></td>
            <td><?= $item->contract_no ?></td>
            <td><?= $item->bill_date ?></td>
            <td><?= $item->charge_item_name ?></td>
            <td><?= $item->bankBillNo ?></td>
            <td><?= money_format($item->amount, 2) ?></td>
            <td><?= money_format($_fee, 2) ?></td>
            <td><?= money_format($_payed, 2) ?></td>
        </tr>
    <?php endforeach; ?>

    </tbody>
    <thead>
    <tr>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th>合计</th>
        <th><?= money_format($total, 2) ?></th>
        <th><?= money_format($fee, 2) ?></th>
        <th><?= money_format(round($total - $fee, 2), 2) ?></th>
    </tr>
    </thead>
</table>
<?php
\components\inTemplate\widgets\IBox::end();
if(sizeof($logs)>0){
\components\inTemplate\widgets\IBox::begin(['title'=>'操作日志']);
    foreach ($logs as $log):
?>
        <p><?=date('Y-m-d H:i:s',$log->created_at)?> <?=$log->manager->real_name?> <?=$log->message?></p>
<?php
    endforeach;
\components\inTemplate\widgets\IBox::end();
}
if ($model->status == $model::STATUS_WAIT) {
    $form = \components\inTemplate\widgets\ActiveForm::begin();
    echo $form->submitButton($model, '同意该账单');
    \components\inTemplate\widgets\ActiveForm::end();
} ?>

