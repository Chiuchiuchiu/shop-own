<?php


/* @var $this yii\web\View */
/* @var $model common\models\Article */
/* @var $categoryMap array */

$this->title = '调查项目统计';
$this->params['breadcrumbs'][] = ['label' => '调研统计分析', 'url' => ['question-query']];
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
            foreach ($personDesc as $value){
                $i++;
                if($i==1){
                    echo "'".$value['name']."'";
                }else{

                    echo ",'".$value['name']."'";
                }
            }?>],
        datasets: [{
            label: '百分比',
            backgroundColor: window.chartColors.blue,
            data: [<?php
                $i=0;
                foreach ($personDesc as $value){
                    $i++;
                    if($i==1){ echo $value['Score'];
                    }else{ echo ",".$value['Score']."";
                    }
                }?>]
        }]

    };

    var barChartDataASC = {
        labels: [
            <?php
            $i=0;
            foreach ($personAsc as $value){
                $i++;
                if($i==1){
                    echo "'".$value['name']."'";
                }else{

                    echo ",'".$value['name']."'";
                }
            }?>],
        datasets: [{
            label: '百分比',
            backgroundColor: window.chartColors.blue,
            data: [<?php
                $i=0;
                foreach ($personAsc as $value){
                    $i++;
                    if($i==1){ echo $value['Score'];
                    }else{ echo ",".$value['Score']."";
                    }
                }?>]
        }]

    };
    window.onload = function() {
        var ctx = document.getElementById('canvasdesc').getContext('2d');
        window.myBar = new Chart(ctx, {
            type: 'bar',
            data: barChartDataDESC,
            options: {
                title: {
                    display: true,
                    text: '<?=$questionLib->title;?>评分'
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

        var ctxasc = document.getElementById('canvasasc').getContext('2d');
        window.myBar = new Chart(ctxasc, {
            type: 'bar',
            data: barChartDataASC,
            options: {
                title: {
                    display: true,
                    text: '<?=$questionLib->title;?>评分'
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
                    <h5><?=$questionLib->title;?></h5>
                    <?php
                    if($region_id==0){?>
                        <a href="/question/question-query-export?question_project_id=<?=$question_project_id;?>&question_id=<?=$questionLib->id;?>" class="btn btn-success pull-right">导出表格</a>
                    <?php }else{?>
                        <a href="/question/question-query-export?question_project_id=<?=$question_project_id;?>&question_id=<?=$questionLib->id;?>&region_id=<?=$region_id;?>" class="btn btn-success pull-right">导出表格</a>
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
                            foreach ($personDesc as $value){
                                $i++;
                                ?>
                        <tr>
                                <td><?=$i;?></td>
                                <td><?php
                                    if($region_id==0){
                                        echo '<a href="/question/question-query-item?question_project_id='.$question_project_id.'&question_id='.$questionLib->id.'&region_id='.$value['id'].'" title="点击查看详细项目">'.$value['name'].'</a>';
                                    }else{
                                        echo $value['name'];
                                    }?></td>
                                <td><?=$value['Score'];?></td>
                        </tr><?php }?>
                        </tbody>
                    </table>

                </div>
            </div>
        </div>

        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5><?=$questionLib->title;?> 排名靠前 </h5>
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
                        <canvas id="canvasdesc" style="height: 500px;"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5><?=$questionLib->title;?> 排名靠后 </h5>
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



