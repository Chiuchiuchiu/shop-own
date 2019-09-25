<?php

use yii\helpers\Html;
use components\inTemplate\widgets\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '账单审核';
$this->params['breadcrumbs'][] = $this->title;
\components\inTemplate\widgets\IBox::begin();
echo GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        'id',
        'date',
        'totalMoney',
        'statusText',
        [
            'class' => \components\inTemplate\widgets\RBACActionColumn::className(),
            'template' => '{view?id}'
        ],
    ],
]);
\components\inTemplate\widgets\IBox::end();