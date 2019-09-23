<?php

use components\inTemplate\widgets\Html;
use components\inTemplate\widgets\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $search string */

$this->title = '调查问卷';
$this->params['breadcrumbs'][] = $this->title;
?>



<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5> 请选择调研问卷以及子项目</h5>
                </div>
                <div class="ibox-content">
                    <?php
                    \components\inTemplate\widgets\ActiveForm::begin(['layout' => 'horizontal', 'method' => 'get', 'action' => \yii\helpers\Url::toRoute('/question/question-query-item')]); ?>


                    <table width="90%" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td width="120">&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td  align="center" height="45">调研问卷</td>
                            <td><select name="question_project_id" id="question_project_id" onchange="ProjectChoose();">
                                    <option value="0">==请选择调研问卷==</option>
                                    <?php foreach ($QuestionProject as $v){
                                        echo '<option value="'.$v->id.'">'.$v->title.'</option>';
                                    }?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td  align="center"  height="45">调研题目</td>
                            <td><select name="question_id" id="question_id">
                                    <option value="0">==请先选择调研问卷==</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                            <td><button type="submit" class="btn btn-success pull-left">提交查询</button> </td>
                        </tr>
                    </table>
                    <?php
                    \components\inTemplate\widgets\ActiveForm::end();
                    ?>

                </div>
            </div>
        </div>


    </div>

</div>

<script>
    function ProjectChoose() {
        $.ajax({
            type: 'GET',
            url: "/question/question-choose?question_project_id=" + $('#question_project_id').val(),
            timeout: 3000, //超时时间：30秒
            dataType:'json',
            success: function (data) {
                $('select[name=question_id]').empty();
                $.each(data.houseArr,function(n,value){
                    $('select[name=question_id]').append("<option data-id='"+value['id']+"' value='"+value['id']+"'>"+value['title']+"</option>");
                });
                $('select[name=question_id]').trigger("change");
            },
            error: function (data) {
                alert(data);
            }
        });
    }
</script>


