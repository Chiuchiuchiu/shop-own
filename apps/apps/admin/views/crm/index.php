<?php

use components\inTemplate\widgets\Html;
use components\inTemplate\widgets\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $search string */
/* @var array $butler */
/* @var array $projectList */
/* @var array $searchProjectList */
/* @var integer $butlerId */
/* @var integer $searchProjectId */
/* @var \common\valueObject\RangDateTime $dateTime */

$this->title = '走访指标模板';
$this->params['breadcrumbs'][] = $this->title;

echo \components\inTemplate\widgets\ZTree::widget([]);
?>

<div class="col-sm-12">


<?php
\components\inTemplate\widgets\IBox::begin(['title' => false]);
\components\ajaxUpload\widgets\AjaxUpload::widget([]);
?>

<p><strong style="color: red;font-size: 1.2em;">注：如果下载模板中没有需要添加的管家名，请操作“<a href="/butler-auth">管家管理</a>”添加管家流程。管家列表以表格已列举的管家为准，不可在表格添加已列举之外的管家</strong></p>

<?php

\components\inTemplate\widgets\IBox::end();
?>

<div class="row">
    <div class="col-lg-5">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>指标模板&nbsp;&nbsp;<small style="color: red">注：“下载模板”/“上传模板数据”，需要指定项目</small></h5>
            </div>
            <div class="ibox-content">

                <div class="col-sm-8">
                    <?php echo \components\inTemplate\widgets\Chosen::widget([
                        'name' => 'projectId',
                        'items' => $projectList,
                    ])?>
                </div>

                <div class="text-center">
                    <a id="downloadTemplate" class="btn btn-success btn-bitbucket">
                        下载导入模板
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>模板批量导入</h5>
            </div>
            <div class="ibox-content">
                <form role="form" class="form-inline" id="uploadFile">
                    <div class="form-group"><label for="exampleInputEmail2" class="sr-only">Email address</label>
                        <input name="UploadObject[file]" required id="excelFile" class="form-control" type="file" accept=".csv, application/vnd.ms-excel, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet">
                    </div>
                    <button id="submitFile" class="btn btn-white" type="submit">文件上传</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-2">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>新增</h5>
            </div>
            <div class="ibox-content">
                <div class="text-center">
                    <a href="/crm/create?" class="btn btn-success btn-bitbucket">
                        单个新增
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="modal-form" style="display: none;" aria-hidden="true" class="modal fade in">
    <div class="modal-backdrop fade in"></div>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-6">
                        <p class="text-center send-tips">
                            正在提交
                        </p>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="display: none">
                <button type="button" class="btn btn-white closeModal" data-dismiss="modal">关闭</button>
            </div>
        </div>
    </div>
</div>

<?php
\components\inTemplate\widgets\IBox::begin(['title'=>'搜索']);
$from = \components\inTemplate\widgets\ActiveForm::begin([
    'method' => 'get',
    'action' => \yii\helpers\Url::to(['crm/']),
])
?>

<div class="form-group">

    <div class="col-sm-2">
        <?= \kartik\date\DatePicker::widget([
            'model' => $dateTime,
            'attribute' => 'startDate',
            'options' => ['placeholder' => '年份', 'value' => $years, 'name' => 'years'],
            'type' => \kartik\date\DatePicker::TYPE_INPUT,
            'language' => 'zh-CN',
            'value' => date('Y', time()),
            'pluginOptions' => [
                'autoclose' => true,
                'format' => 'yyyy',
                'startView' => 2,
                'minViewMode' => 2,
                'maxViewMode' => 2,
            ]
        ]) ?>
    </div>

    <div class="col-sm-2">
        <?php echo \components\inTemplate\widgets\Chosen::widget([
            'name' => 'searchProjectId',
            'value' => '',
            'items' => $searchProjectList,
        ])?>
    </div>

    <div class="col-sm-2">
        <?php echo \components\inTemplate\widgets\Chosen::widget([
            'name' => 'butler_id',
            'value' => $butlerId,
            'items' => $butler,
        ])?>
    </div>

    <div class="col-sm-2">

        <?php echo \yii\bootstrap\Html::submitButton('查询', ['class' => 'btn btn-info btn-block']) ?>
    </div>
</div>

<?php
\components\inTemplate\widgets\ActiveForm::end();
\components\inTemplate\widgets\IBox::end();
?>

<div class="ibox float-e-margins " style="">
    <div class="ibox-content">
        <div class="form-group">

            <div class="col-sm-1">
                <a class="btn btn-info" href="/crm/export-indicators?searchProjectId=<?= $searchProjectId ?>&butlerId=<?= $butlerId ?>">
                    导出当前明细
                </a>
            </div>

        </div>

    </div>
</div>
</div>

<div class="col-sm-12">

    <div class="col-sm-2 ibox-content" style="min-height: 1200px;overflow: auto;">
        <div>
            <p class="">未走访房产列表：<text id="proName" class="text-navy"></text></p>
            <p class="text-danger">管家：<text id="buName" class="text-navy"></text></p>
        </div>
        <div class="zTreeDemoBackground left">
            <ul id="treeDemo" class="ztree"></ul>
        </div>
    </div>

    <div class="col-sm-10">

<?php

\components\inTemplate\widgets\IBox::begin(['title' => '各管家指标列表']);
echo GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        'id',
        [
            'label' => '管家名称',
            'format' => 'raw',
            'value' => function(\common\models\ButlerVisitIndicators $model){
                return Html::tag('p', $model->butlerNickName, ['class' => empty($model->butlerNickName) ? 'text-danger' : 'text-info']);
            }
        ],
        [
            'label' => '项目',
            'format' => 'raw',
            'value' => function(\common\models\ButlerVisitIndicators $model){
                return Html::tag('p', $model->butler->projectName);
            }
        ],
        'butlerRegionHouseName',
        'management_number',
        'reside_number',
        [
            'label' => '年份',
            'format' => 'raw',
            'value' => function(\common\models\ButlerVisitIndicators $model){
                return Html::tag('p', $model->years, ['class' => 'text-info']);
            }
        ],
        'the_first_quarter',
        'second_quarter',
        'third_quarter',
        'fourth_quarter',
        [
            'label' => '半年已走访数',
            'format' => 'raw',
            'value' => function(\common\models\ButlerVisitIndicators $model){
                $butlerClass = 'butler_' . $model->butler_id;
                return Html::tag('p', '-', ['class' => 'text-info bu '.$butlerClass, 'data-id' => $model->butler_id]);
            }
        ],
        'created_at:datetime',
        'updated_at:datetime',
        [
            'label' => '后台管理员名称（录入）',
            'format' => 'raw',
            'value' => function(\common\models\ButlerVisitIndicators $model){
                $pmManageName = '总后台管理员';
                if(isset($model->pmManager)){
                    $pmManageName = $model->pmManager->real_name;
                }
                return Html::tag('p', $pmManageName, ['class' => 'text-info']);
            }
        ],
        [
            'class' => \components\inTemplate\widgets\RBACActionColumn::className(),
            'template'=>'{show} {update} {delete}',
            'buttons'=>[
                    'show'=>function($url, $model, $key){
                        return Html::a('未走访房产', '#', ['class' => 'btn btn-xs btn-primary show', 'data-id' => $model->butler_id, 'data-name' => $model->butlerNickName, 'data-pro' => $model->butler->projectName]);
                    }
                ],
        ],
    ],
]);
\components\inTemplate\widgets\IBox::end();

?>

</div>
</div>

<?php \common\widgets\JavascriptBlock::begin(); ?>

<script>
    var setting = {
        check: {
            enable: true,
            nocheckInherit: false,
            chkDisabledInherit: true
        },
        view: {
            selectedMulti: true
        },
        async: {
            enable: true,
            url:"/crm/no-visit-house",
            autoParam:["id", "name=n", "level=lv"],
            otherParam:{"otherParam":"zTreeAsyncTest"},
            dataFilter: filter
        },
        callback: {
            beforeClick: beforeClick,
            beforeAsync: beforeAsync,
            onAsyncError: onAsyncError,
            onAsyncSuccess: onAsyncSuccess,
            onCheck: zTreeOnCheck
        }
    };
    function zTreeOnCheck(event, treeId, treeNode) {
        var checked = treeNode.checked;
        console.log((treeNode?treeNode.id:"root") + "checked " +(checked?"true":"false"));
        if(checked){
            // AddLibCheck((treeNode?treeNode.id:"root"));
        }else{
            DelLibCheck((treeNode?treeNode.id:"root"));
        }

    };


    function  AddLibCheck(str){
        var tree_text = $('#tree_text').val();
        var new_tree_tree = tree_text+','+str;
        $('#tree_text').val(new_tree_tree);
        $('#TreeExport').html('导出选择的数据');
        $('#TreeExport').attr('href','/auth/house-child-not?SysID='+new_tree_tree);
    }


    function  DelLibCheck(str){
        var tree_text = $('#tree_text').val();
        new_str = tree_text.replace(','+str,'');
        $('#tree_text').val(new_str);
        $('#TreeExport').html('导出选择的数据');
        $('#TreeExport').attr('href','/auth/house-child-not?SysID='+new_str);
    }
    function filter(treeId, parentNode, childNodes) {
        if (!childNodes) return null;
        for (var i=0, l=childNodes.length; i<l; i++) {
            childNodes[i].name = childNodes[i].name.replace(/\.n/g, '.');
        }
        return childNodes;
    }
    function beforeClick(treeId, treeNode) {
        if (!treeNode.isParent) {
            //    alert("请选择父节点");
            return false;
        } else {
            return true;
        }
    }
    var log, className = "dark";
    function beforeAsync(treeId, treeNode) {
        className = (className === "dark" ? "":"dark");
        showLog("[ "+getTime()+" beforeAsync ]&nbsp;&nbsp;&nbsp;&nbsp;" + ((!!treeNode && !!treeNode.name) ? treeNode.name : "root") );
        return true;
    }
    function onAsyncError(event, treeId, treeNode, XMLHttpRequest, textStatus, errorThrown) {
        showLog("[ "+getTime()+" onAsyncError ]&nbsp;&nbsp;&nbsp;&nbsp;" + ((!!treeNode && !!treeNode.name) ? treeNode.name : "root") );
    }
    function onAsyncSuccess(event, treeId, treeNode, msg) {
        showLog("[ "+getTime()+" onAsyncSuccess ]&nbsp;&nbsp;&nbsp;&nbsp;" + ((!!treeNode && !!treeNode.name) ? treeNode.name : "root") );
    }

    function showLog(str) {
        if (!log) log = $("#log");
        log.append("<li class='"+className+"'>"+str+"</li>");
        if(log.children("li").length > 8) {
            log.get(0).removeChild(log.children("li")[0]);
        }
    }
    function getTime() {
        var now= new Date(),
            h=now.getHours(),
            m=now.getMinutes(),
            s=now.getSeconds(),
            ms=now.getMilliseconds();
        return (h+":"+m+":"+s+ " " +ms);
    }

    function refreshNode(e) {
        var zTree = $.fn.zTree.getZTreeObj("treeDemo"),
            type = e.data.type,
            silent = e.data.silent,
            nodes = zTree.getSelectedNodes();
        if (nodes.length == 0) {
            //  alert("请先选择一个父节点");
        }
        for (var i=0, l=nodes.length; i<l; i++) {
            zTree.reAsyncChildNodes(nodes[i], type, silent);
            if (!silent) zTree.selectNode(nodes[i]);
        }
    }

    $(document).ready(function(){
        $("#refreshNode").bind("click", {type:"refresh", silent:false}, refreshNode);
        $("#refreshNodeSilent").bind("click", {type:"refresh", silent:true}, refreshNode);
        $("#addNode").bind("click", {type:"add", silent:false}, refreshNode);
        $("#addNodeSilent").bind("click", {type:"add", silent:true}, refreshNode);
    });
</script>

<?php \common\widgets\JavascriptBlock::end(); ?>

<?php \common\widgets\JavascriptBlock::begin(); ?>

    <script type="text/javascript">

        $('#downloadTemplate').click(function (){
            var projectId = $("select[name='projectId'] option:selected").val();
            var projectName = $("select[name='projectId'] option:selected").html();

            location.href="/crm/download-template?projectId="+projectId+"&projectName="+projectName;

            console.log(projectName);

        });

        $('#uploadFile').on('submit', function (){

            $('#excelFile').ajaxfileupload({
                action: '/default/upload',
                validate_extensions: false,
                params: {
                    extra: 'info'
                },
                onComplete: function(response) {
                    console.table(response);
                    importExcel(response.data.savePath);
                    //eval('response = '+response+';');
                },
                onStart: function() {
                    $('.send-tips').html('正在上传…………');
                    $('#modal-form').show();
                },
                onCancel: function() {
                    console.log('no file selected');
                }
            }).change();

            return false;
        });

        var importExcel = function (filePath){
            var projectId = $("select[name='projectId'] option:selected").val();

            $('#excelFile').val('');
            $.ajax({
                type: 'GET',
                url: '/crm/import-excel',
                data: {filePath: filePath, projectId:projectId},
                success: function (res){
                    if(res.code === 0){
                        $('.send-tips').html('上传成功…………');
                        $('#modal-form').hide();
                        window.location.reload();
                    } else {
                        $('.send-tips').html(res.message);
                        $('.modal-footer').show();
                    }
                },
                error: function (res){
                    $('.send-tips').html('系统错误…………');
                },
                dataType: 'JSON'
            });
        };

        $("select[name='searchProjectId']").on('change', function(){
            var v = $(this).val();
            if(v.length < 1){
                return;
            }

            $.ajax({
                type: 'GET',
                url: '/crm/get-project-butler-list',
                data: {projectHouseId:v},
                beforeSend: function(){
                    $('.send-tips').html('获取管家列表…………');
                    $('#modal-form').show();
                },
                success: function(res){
                    var html = '<option value="">管家</option>';
                    if(res.data.length > 0){
                        $.each(res.data, function(key, value){
                            html += '<option value="'+value.id+'">'+value.nickname+'</option>';
                        });
                    }

                    $("select[name='butler_id']").empty();
                    $("select[name='butler_id']").html(html);
                    $("select[name='butler_id']").trigger('chosen:updated');
                    $('#modal-form').hide();
                },
                dataType: 'json'
            });

        });

        $('.closeModal').click(function (){
            $('.modal-footer').hide();
            $('#modal-form').hide();
        });

        //未走访
        $('.show').on('click', function (){
            let butlerId = $(this).attr('data-id');
            let buName = $(this).attr('data-name');
            let proName = $(this).attr('data-pro');

            $('#buName').text(buName);
            $('#proName').text(proName);

            setting.async.otherParam = {"butlerId": butlerId,"otherParam":"zTreeAsyncTest"};
            $.fn.zTree.init($("#treeDemo"), setting);

        });


        var countButlerVi = function (){
            let butlerIds = [];
            let years = $('#rangdatetime-startdate').val();
            $('.bu').each(function (){
                let v = $(this).attr('data-id');
                butlerIds.push(v)
            });
            butlerIds = butlerIds.join('-');

            $.getJSON('/crm/butler-vi-info',{butlerIds: butlerIds, years: years}, function (res){
                if(res.code == 0){
                        $.each(res.data, function (index, value){
                            $('.butler_'+value.id).text(value.c);
                        })
                }
            })
        };

        countButlerVi();

    </script>

<?php \common\widgets\JavascriptBlock::end(); ?>