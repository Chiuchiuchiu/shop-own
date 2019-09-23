<?php
/**
 * @var $type string
 * @var $model \common\models\MemberHouse
 * @var $identity integer
 */
?>
<div class="panel tac" id="auth-result">
    <?php $form = \components\za\ActiveForm::begin(['id' => 'ap-form', 'action' => 'auth/save']); ?>
    <div style="display: none">
        <?php echo $form->field($model, 'house_id')->hiddenInput()->label(false) ?>
        <?php echo $form->field($model, 'identity')->hiddenInput()->label(false) ?>
    </div>
    <?php \components\za\ActiveForm::end() ?>
    <?php
    if ($type == 'warning'):
        ?>
        <div class="icon icon-warning"></div>
        <h2>很抱歉</h2>
        <p>当前无法查询到对应房屋的业主信息,<br/>
            <span style="color:#E64340">您的信息已自动提交人工审核！</span><br />
            <span style="color:#E64340">注：房产信息如果有误，可以点击下方【删除】！</span>
        </p>

        <p id="house-address">
            房产信息：<?= $model->house->ancestor_name ?>
        </p>

        <a class="btn btn-block btn btn-empty" style="color: #E64340;" id="del-house" data-house="<?= $model->house_id ?>">删除</a>

        <?= \components\za\Html::a("查看我的房产", '/house/', ['class' => 'btn btn-block btn btn-primary', 'data-auth' => 1]) ?>
        <?= \components\za\Html::a("返回修改", ['index'], ['class' => 'btn btn-block btn btn-empty']) ?>
        <?php
    endif;
    if ($type == 'error'):
        ?>
        <div class="icon icon-error"></div>
        <h2>认证失败</h2>
        <p>您的信息不符,<span style="color:#E64340">已自动提交人工审核！</span></p>
        <?= \components\za\Html::a("查看我的房产", '/house/', ['class' => 'btn btn-block btn btn-primary', 'data-auth' => 1]) ?>
        <?= \components\za\Html::a("返回修改", [
        $model instanceof \common\models\MemberHouse && $model->house_id && $model->identity ?
            'auth/step2?houseId=' . $model->house_id . '&identity=' . $model->identity :
            'index'], ['class' => 'btn btn-block btn btn-empty']) ?>
        <?php
    endif;
    if ($type == 'success'):
        ?>
        <div class="icon icon-success"></div>
        <h2>操作成功</h2>
        <p></p>
        <?= \components\za\Html::a("去缴费", "/house/choose-bill", ['class' => 'btn btn-block btn btn-primary']) ?>
        <?php
    endif;
    ?>
</div>

<div style="height: 6em">

</div>

<?php \common\widgets\JavascriptBlock::begin() ?>
<script>
    $('#auth-result').bind('loaded', function () {
        $('a[data-auth]').bind('click', function () {
            $.post('/auth/save', $('#ap-form').serialize(), function (res) {
                if (res.code === 0) {
                    app.go('/house');
                } else {
                    app.tips().error(res.message)
                }
            }, 'json')
        });

        $('#del-house').click(function (){
            var id = $(this).attr('data-house');
            if (window.confirm("确定要删除该处房子吗？")) {
                $('#house-address,#del-house').remove();
                $.getJSON('/auth/del-house?id=' + id, function (res) {

                });
            }
        });

    })
</script>
<?php \common\widgets\JavascriptBlock::end() ?>
