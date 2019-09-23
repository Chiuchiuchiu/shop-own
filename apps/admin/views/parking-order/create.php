<?php
/**
 * @var \common\models\ProjectParkingOneToOne $model
 * @var array $projectList
 * Created by PhpStorm.
 * User: mickey
 * Date: 2018/8/27
 * Time: 16:16
 */

$this->title = '项目配置';
$this->params['breadcrumbs'][] = $this->title;
echo \components\inTemplate\widgets\BackBtn::widget([
    'name' => '返回项目',
    'option'=>[
        'class'=>'btn btn-w-m btn-white pull-left'
    ]
]);
?>

<?php \components\inTemplate\widgets\IBox::begin();
$form = \components\inTemplate\widgets\ActiveForm::begin(['layout' => 'horizontal']);
echo $form->field($model,'name')->textInput();
echo $form->ajaxUpload($model, 'file', 'pic', 'pic', '图标');
echo $form->field($model,'app_id')->textInput();
echo $form->field($model,'app_key')->textInput();
echo $form->field($model,'parking_id')->textInput();
echo $form->field($model, 'type')->dropDownList(\common\models\ProjectParkingOneToOne::typeMap());
?>

<div class="form-group">
    <label class="control-label col-sm-3">项目</label>
    <div class="col-sm-7">
        <div class="row">
            <div class="col-sm-4">

                <?php echo \components\inTemplate\widgets\Chosen::widget([
                    'name' => $model->formName() . '[project_house_id]',
                    'value' => $model->project_house_id,
                    'items' => $projectList,
                    'addClass' => 'c-project'
                ])?>

            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="form-group">
        <div class="text-center">
            <?= \yii\bootstrap\Html::submitButton('提交', ['class' => $model->isNewRecord ? 'btn btn-primary' : 'btn btn-success']) ?>
        </div>
    </div>
</div>
<?php
\components\inTemplate\widgets\ActiveForm::end();
\components\inTemplate\widgets\IBox::end();
?>
