<?php
/**
 * @var $this \yii\web\View
 * @var $_model \common\models\SearchNotices
 * @var $_user \apps\www\models\Member
 * @var $memberHouse  \common\models\MemberHouse
 * @var $flowStyleID
 * @var integer $project
 * @var int $site
 */
use components\za\Html;

?>
    <style>

        form .pure-form .form-group textarea {
            padding: .5em;
            height: 8em;
            text-align: left;
        }
        /*---------*/
        .horizontal-ul {
            list-style: none;
            margin: 0px;
            padding: 0px;
        }

        .horizontal-ul li {
            float: left;
        }

        .horizontal-ul li span {
            color: #666;
        }

        .horizontal-menu-div {
            width: 100%;
            background-color: yellow;
            height: 47px;
        }

        .img-back {
            margin-left: 10px;
            margin-top: 5px;
        }
        /*---------*/
        .edit-title{
            width: 80%;text-align: center;margin-top: 5px;
        }
        .edit-title span{
            color: #ffffff!important;
            line-height: 33px;
        }

        .repair-cancel-footer {
            width: 100%;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 40px;
            display: -webkit-box;
            display: flex;
        }

        .repair-cancel-footer #back {
            background-color: white;
            letter-spacing: 0.3em;
        }
        .repair-cancel-footer #step {
            background-color: #F7B500;
            color: #fff;
        }
    </style>


    <div class="topPath horizontal-menu-div" style="background: #F8B708;width: 100%; ">
        <ul class="horizontal-ul">
            <li id="gohomeBtn" style="margin-top: 5px;">
                <img class="img-back" src="/static/images/leftback-icon.png">
            </li>
            <li class="edit-title">
                <span>确认领取物件</span>
            </li>

        </ul>
    </div>



    <div class="panel" id="notices-form">

<?php \components\za\ActiveForm::begin(); ?>
    <div class="pure-form">
<?= Html::hiddenInput($_model->formName() . '[id]', $_model->id, ['placeholder' => '请选择报修类型', 'data-required' => false]) ?>
    <label> </label>

    <label><span style="color: #ff2222">*</span>领取地点</label>
    <div class="form-group">
        <?= Html::textInput(  $_model->formName() .'[receive_address]', empty($_model->receive_address)?'':$_model->receive_address, ['placeholder' => '请填写领取地点', 'data-required' => true]) ?>

    </div>

    <label><span style="color: #ff2222">*</span>详细描述</label>
    <div class="form-group" style=" ">
        <?= Html::textarea(  $_model->formName() .'[receive_remark]', empty($_model->receive_remark)?'':$_model->receive_remark, ['placeholder' => '','data-required' => true,]) ?>
    </div>


  <!--  <button type="submit" class="btn btn-block btn-bottom-all btn-primary edit-form-button" data-disable="0" id="submitBtn">发布</button>-->
    <div class="repair-cancel-footer">
        <button type="button" id="back" class="btn-block">返回</button>
        <button type="submit" id="step" data-disable ='0' class="btn-block">确定</button>
    </div>
<?php \components\za\ActiveForm::end(); ?>


<?php
\common\widgets\JavascriptBlock::begin();
?>

    <script>
        $(function () {
            $('#back').click(function (){
                app.go('/search-notices/list?memberID=<?= $memberID ?>');
            });

            $('#notices-form').on('loaded', function () {


                $('form').on('submit', function () {
                    var _dis = $('#step').attr("data-disable");
                    if(_dis == 0) {
                        if (app.formValidate(this)) {
                            $('#step').attr("data-disable",1);
                            var val = $(this).serialize();
                            app.showLoading();
                            $.post('/search-notices/receive', val, function (res) {
                                app.hideLoading();
                                $('#step').attr("data-disable",0);
                                if (res.code === 0) {
                                    app.go('/search-notices/list?memberID=<?= $memberID ?>');

                                } else {
                                    app.tips().error(res.message);
                                }
                            }, 'json');
                        }
                    }
                    return false;
                })
            });

            //$('#step').on('click', function (){
            //    if (app.formValidate(this)){
            //        var _this = $('#_model-form').serialize();
            //        $.post('/search-notices/receive?id=<?//= $_model->id ?>//', _this, function (res) {
            //            if (res.code === 0) {
            //                app.go('/search-notices/list?memberID=<?//= $memberID ?>//');
            //            } else {
            //                app.tips().error(res.message);
            //            }
            //        }, 'json');
            //    }
            //});

            $("#gohomeBtn").on("click",function () {
                app.go('/search-notices/list?memberID=<?= $memberID ?>');
            });


        });
    </script>

<?php
\common\widgets\JavascriptBlock::end();
?>