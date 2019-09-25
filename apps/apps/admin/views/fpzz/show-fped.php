<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/3/29
 * Time: 15:44
 */

/* @var $dataProvider \yii\data\ActiveDataProvider */
/* @var array $pdfList */

$this->title = '电子发票 PDF';
$this->params['breadcrumbs'][] = $this->title;

echo \components\inTemplate\widgets\BackBtn::widget(['url' => 'index']);
?>

<?php

echo \components\inTemplate\widgets\IBox::widget([
    'title' => '缴费明细',
    'content'=>\components\inTemplate\widgets\GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'spmc',
            'ggxh',
            'dw',
            'origin_amount',
        ],
    ])
]);

?>

<?php
    if(!empty($pdfList)){
        foreach ($pdfList as $row){
            ?>
            <label>地址：</label><a href="<?= $row['bill_pdf_url'] ?>" target="_blank">PDF</a>
            <iframe src="<?= $row['bill_pdf_url'] ?>" frameborder="0" width="100%" height="400px"></iframe>

<?php }} ?>
