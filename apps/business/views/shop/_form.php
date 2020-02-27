<?php

use components\inTemplate\widgets\ActiveForm;
use common\models\FreightTemplate;
use yii\bootstrap\Modal;


/* @var $this yii\web\View */
?>

<?php \components\inTemplate\widgets\IBox::begin();
$form = ActiveForm::begin(['layout' => 'horizontal']);
?>

<?php

echo $form->field($model, 'name')->textInput();
echo $form->field($model, 'free_shipping')->radioList(
    FreightTemplate::freeMap(),
    ['item' => function ($index, $label, $name, $checked, $value) {
        $return = '<div class="radio i-checks checkbox-inline">
                        <label class="radioShipping">
                            <div class="iradio_square-green" style="position: relative;">
                                <input type="radio" name="FreightTemplate[free_shipping]" value="';
        $return .= $value;
        $return .= '" style="position: absolute; opacity: 0;">
                                <ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins>
                            </div>&nbsp;';
        $return .= $label;
        $return .= '</label></div>';

        return $return;
    }
    ]
);
?>

    <div class="form-group field-shop-name setShipping" style="display: none;">
        <label class="control-label col-sm-3" for="shop-name">运费设置</label>
        <div class="col-sm-9">
            <div class="input-group input-group-sm">
                <span class="input-group-addon">默认运费</span>
                <input type="text" class="form-control" name="FreightTemplate[first_amount]">
                <span class="input-group-addon">件内，</span>
                <input type="text" class="form-control" name="FreightTemplate[first_num]">
                <span class="input-group-addon">元</span>
                <span class="input-group-addon">每增加</span>
                <input type="text" class="form-control" name="FreightTemplate[continue_num]">
                <span class="input-group-addon">件，</span>
                <span class="input-group-addon">增加运费</span>
                <input type="text" class="form-control" name="FreightTemplate[continue_amount]">
                <span class="input-group-addon">元</span>
            </div>
        </div>
    </div>

<?php
echo $form->field($model, 'transport')->radioList(
    FreightTemplate::transportMap(),
    ['item' => function ($index, $label, $name, $checked, $value) {
        $return = '<div class="radio i-checks checkbox-inline">
                        <label class="radioTransport">
                            <div class="iradio_square-green" style="position: relative;">
                                <input type="radio" name="FreightTemplate[transport]" value="';
        $return .= $value;
        $return .= '" style="position: absolute; opacity: 0;">
                                <ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins>
                            </div>&nbsp;';
        $return .= $label;
        $return .= '</label></div>';

        return $return;
    }
    ]
);
?>
    <div class="form-group field-shop-name setTransport" style="display: none;">
        <div class="col-sm-12">
            <div class="col-sm-1"></div>
            <div class="col-sm-10">
                <table class="table">
                    <thead>
                    <tr>
                        <th class="text-center">送运到</th>
                        <th class="text-center">首件</th>
                        <th class="text-center">首费</th>
                        <th class="text-center">续件</th>
                        <th class="text-center">续费</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr class="success">
                        <td>
                            <button class="form-control" data-toggle="modal" data-target="#create-modal">设置地区</button>
                        </td>
                        <td class="col-lg-1"><input class="form-control" name=""/></td>
                        <td class="col-lg-2"><input class="form-control" name=""/></td>
                        <td class="col-lg-1"><input class="form-control" name=""/></td>
                        <td class="col-lg-2"><input class="form-control" name=""/></td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-sm-1"></div>

        </div>
    </div>
    <!--<div class="form-group field-freighttemplate-transport_type required">-->
    <!--    <label class="control-label col-sm-3" for="freighttemplate-transport_type">配送类型</label>-->
    <!--    <div class="col-sm-6">-->
    <!--        <input type="hidden" name="FreightTemplate[transport_type]" value=""><div id="freighttemplate-transport_type" class="isTransport"><div class="radio i-checks checkbox-inline"><label><div class="iradio_square-green" style="position: relative;"><input type="radio" name="FreightTemplate[transport_type]" value="1" style="position: absolute; opacity: 0;"><ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins></div> 全国</label></div>-->
    <!--            <div class="radio i-checks checkbox-inline"><label><div class="iradio_square-green" style="position: relative;"><input type="radio" name="FreightTemplate[transport_type]" value="2" style="position: absolute; opacity: 0;"><ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins></div> 部分地区</label></div></div>-->
    <!--        <div class="help-block help-block-error "></div>-->
    <!--    </div>-->
    <!---->
    <!--</div>-->


    <div class="row">
        <div class="form-group">
            <div class="text-center">
                <?= \yii\bootstrap\Html::submitButton('提交', ['class' => $model->isNewRecord ? 'btn btn-primary' : 'btn btn-success']) ?>
            </div>
        </div>
    </div>
<?php
ActiveForm::end();
\components\inTemplate\widgets\IBox::end();
?>

<?php \common\widgets\JavascriptBlock::begin(); ?>

    <script type="text/javascript">

        $(function () {
            $('.radioShipping').on('click', function () {
                var values = $(this).find('input').val();

                if (values == <?= FreightTemplate::FREE_SHIPPING_2?>) {
                    $('.setShipping').show();
                } else {
                    $('.setShipping').hide();
                    $('.setShipping').find('input').val('');
                }
            });

            $('.radioTransport').on('click', function () {

                var values = $(this).find('input').val();

                if (values == <?= FreightTemplate::TRANSPORT_ONLY?>) {
                    $(".setTransport").show();
                } else {
                    $('.setTransport').hide();
                    $('.setTransport').find('input').val('');
                }
            })
        });


    </script>

<?php \common\widgets\JavascriptBlock::end(); ?>