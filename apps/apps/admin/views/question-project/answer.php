<?php

use \apps\admin\models\QuestionProject;
use \common\models\QuestionAnswer;
use \common\models\MemberHouse;
use components\inTemplate\widgets\Html;
use components\inTemplate\widgets\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $search string */

$this->title = $TopTitle.' 调查问卷详细';
$this->params['breadcrumbs'][] = $this->title;
?>
<?php
\components\inTemplate\widgets\IBox::begin(['title'=>'搜索']);
$from = \components\inTemplate\widgets\ActiveForm::begin(['method' => 'get', 'action' => \yii\helpers\Url::to(['/question-project/answer-list'])])
?>

    <div class="form-group">
        <div class="col-sm-2">
            <label>&nbsp;</label>

              <select class="input-group form-control" name="project_region_id" id="project_region_id" onchange="ProjectChoose();">
                 <option value="" <?php if($project_region_id==''){ echo ' selected';}?> >请选择分公司</option>
                <?php foreach ($ProjectRegion as $v){?>
                    <option value="<?=$v['id']?>" <?php if($project_region_id==$v['id']){ echo ' selected';}?> ><?=$v['name']?></option>
                <?php }?>
            </select>
        </div>
        <div class="col-sm-2">
            <label>&nbsp;</label>
            <select class="input-group form-control" name="project_id" id="project_id">
                <option value="" <?php if($project_id==''){ echo ' selected';}?> >请选择项目</option>
                <?php foreach ($Project as $v){?>
                    <option value="<?=$v['house_id']?>"  <?php if($project_id==$v['house_id']){ echo ' selected';}?>><?=$v['house_name']?></option>
                <?php }?>
            </select>
        </div>
        <div class="col-sm-2">
            <label>&nbsp;</label>
            <select class="input-group form-control" name="is_chose" id="is_chose">
                    <option value="" <?php if($is_chose==''){ echo ' selected';}?> >不筛选是否在计划名单内</option>
                    <option value="1"  <?php if($is_chose==1){ echo ' selected';}?>>名单内</option>
                    <option value="2"  <?php if($is_chose==2){ echo ' selected';}?>>名单外</option>
            </select>
        </div>
        <div class="col-sm-2">
            <label>&nbsp;</label>
            <input type="text" name="keywords" placeholder="请输入关键字姓名|手机|物业单位" value="<?=$keywords;?>" class="form-control">
        </div>

        <div class="col-sm-2">
            <label>&nbsp;</label>
            <input type="hidden" name="id" value="<?=$id;?>">
            <?php echo \yii\bootstrap\Html::submitButton('查询', ['class' => 'btn btn-info btn-block']) ?>
        </div>
    </div>
<?php
\components\inTemplate\widgets\ActiveForm::end();
\components\inTemplate\widgets\IBox::end();
?>

<a href="/question-project/answer-export<?php
echo '?id='.$id.'&project_region_id='.$project_region_id.'&project_id='.$project_id.'&keywords='.$keywords.'&is_chose='.$is_chose;
?>" class="btn btn-success pull-right" target="_blank">导出问卷</a>
    <div class="col-lg-12">
        <div><?php  echo $TopTitle.' 共'.$dataProvider->totalCount.'条数据'; ?></div>
    </div>
<?= \components\inTemplate\widgets\IBox::widget(['content' => GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        'id',
        'surname',
        [
            'label'=>'身份类型',
            'format' => 'raw',
            'value' => function(QuestionAnswer $model){
                $feeCycleName = MemberHouse::identityHouse($model->member_house_id,$model->member_id);
                return $feeCycleName;
            }
        ],
        'telephone',
        'ancestor_name',
        [
            'label'=>'所在项目',
            'format' => 'raw',
            'value' => function(QuestionAnswer $model){
                $feeCycleName =  isset($model->project->house_name) ? $model->project->house_name : '--';
                return $feeCycleName;
            }
        ],
        [
            'label'=>'所在分公司',
            'format' => 'raw',
            'value' => function(QuestionAnswer $model){
                $feeCycleName =  isset($model->projectregion->name) ? $model->projectregion->name : '--';
                return $feeCycleName;
            }
        ],
        [
            'label'=>'答题详细',
            'format' => 'raw',
            'value' => function(QuestionAnswer $model){
                return Html::a('答题详细', 'javascript:void(0);', ['class' => 'btn btn-xs btn-success','onclick'=>'AnswerChoose(\''.$model->id.'\');']);
            }
        ],

         [
            'label'=>'在名单内',
            'format' => 'raw',
            'value' => function(QuestionAnswer $model){
             if($model->is_chose==0){
                 return '<font color="red">否</font>';
             }else{
                 return '<font color="#23c6c8">是</font>';
             }
            }
        ],



        'created_at',
//        [
//            'label'=>'修改',
//            'format' => 'raw',
//            'value' => function(QuestionAnswer $model){
//                return Html::a('修改', 'javascript:void(0);', ['class' => 'btn btn-xs btn-success','onclick'=>'AnswerEdit(\''.$model->id.'\');']);
//            }
//        ],
//        [
//            'label'=>'操作',
//            'format' => 'raw',
//            'value' => function(QuestionAnswer $model){
//                return Html::a('删除', '/question-project/answer-del?id='.$model->id.'&question_project_id='.$model->question_project_id, ['class' => 'btn btn-xs btn-success']);
//            }
//        ]
    ],
])
]); ?>
<script>
    function ProjectChoose() {
        $.ajax({
            type: 'GET',
            url: "/question/project-choose?Region_id=" + $('#project_region_id').val(),
            timeout: 3000, //超时时间：30秒
            dataType:'json',
            success: function (data) {
                $('select[name=project_id]').empty();
                $('select[name=project_id]').append("<option data-id='0' value='0'>不选择子项目</option>");
                $.each(data.houseArr,function(n,value){

                    $('select[name=project_id]').append("<option data-id='"+value['house_id']+"' value='"+value['house_id']+"'>"+value['house_name']+"</option>");
                });
                $('select[name=project_id]').trigger("change");
            },
            error: function (data) {
                alert(data);
            }
        });
    }

    function AnswerEdit(id) {
        layer.open({
            type: 2,
            title: '编辑问卷',
            shadeClose: true,
            shade: 0.8,
            area : ['500px' , '400px'],
            content: '/question-project/answer-edit?id=' + id + '&SysID=' + Math.random()
        });
    }


</script>
