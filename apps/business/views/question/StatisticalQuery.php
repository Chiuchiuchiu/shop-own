<?php


/* @var $this yii\web\View */
/* @var $model common\models\Article */
/* @var $categoryMap array */

$this->title = '问卷统计分析';
$this->params['breadcrumbs'][] = ['label' => '调研统计分析', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
    #flot-placeholder{
        clear: both;
    }

</style>
<script src="/static/js/Chart/Chart.bundle.js"></script>
<script src="/static/js/Chart/utils.js"></script>
<script>
    var barChartDataDESC = {
        labels: [
            <?php
            $i=0;
            foreach ($HouseArr as $value){
                $i++;
                if($i==1){
                    echo "'".$value['site']."'";
                }else{

                    echo ",'".$value['site']."'";
                }
            }?>],
        datasets: [{
            label: '评分',
            backgroundColor: window.chartColors.blue,
            data: [<?php
                $i=0;
                foreach ($HouseArr as $value){
                    $i++;
                    if($i==1){ echo $value['Score'];
                    }else{ echo ",".$value['Score']."";
                    }
                }?>]
        }]

    };

    window.onload = function() {
        var ctx = document.getElementById('canvasasc').getContext('2d');
        window.myBar = new Chart(ctx, {
            type: 'bar',
            data: barChartDataDESC,
            options: {
                title: {
                    display: true,
                    text: '<?=$topTitle;?>好评'
                },
                tooltips: {
                    mode: 'index',
                    intersect: false
                },
                responsive: true,
                scales: {
                    xAxes: [{
                        stacked: true,
                    }],
                    yAxes: [{
                        stacked: true
                    }]
                }
            }
        });



    };

</script>
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title" style="height: 56px;">
                    <h5> 问卷 <?=$QuestionProject->title?> 在 <?=$ProjectName;?> 的调研详细</h5>

                        <a href="/question/statistical-query-export?question_project_id=<?=$QuestionProject->id;?>&region_id=<?=$region_id;?>&project_id=<?=$project_id;?>" target="_blank" class="btn btn-success pull-right">导出表格</a>
                    <?php if($project_id>0){?>
                        <a href="/question/user-list?project_id=<?=$project_id;?>" class="btn btn-success pull-right" style="margin-right: 10px;">待调研名单</a>
                    <?php }?>

                </div>
                <div class="ibox-content">

                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th>编号</th>
                            <th>标题</th>
                            <th>评分%</th>
                        </tr>
                        </thead>
                        <tbody><?php
                        $i=0;
                        foreach ($HouseArr as $value){
                        $i++;
                            ?><tr>
                            <td><?=$value['id'];?></td>
                            <td><?=$value['title'];?></td>
                            <td><?=$value['Score'];?></td>
                            </tr>
                            <?php }?>

                        </tbody>
                    </table>

                </div>
            </div>
        </div>

        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5> 问卷 <?=$QuestionProject->title?> 在 <?=$ProjectName;?></h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                            <i class="fa fa-wrench"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-user">
                            <li><a href="#">Config option 1</a>
                            </li>
                            <li><a href="#">Config option 2</a>
                            </li>
                        </ul>
                        <a class="close-link">
                            <i class="fa fa-times"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content">
                    <div>
                        <canvas id="canvasasc" style="height: 500px;"></canvas>
                    </div>
                </div>
            </div>
        </div>



    </div>

</div>

<!-- Flot -->



