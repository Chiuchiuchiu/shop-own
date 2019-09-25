<?php


/* @var $this yii\web\View */
/* @var $model common\models\Article */
/* @var $categoryMap array */

$this->title = '管家调查完成数统计';
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
                    echo "'".$value['nickname']."'";
                }else{

                    echo ",'".$value['nickname']."'";
                }
            }?>],
        datasets: [{
            label: '调查完成数',
            backgroundColor: window.chartColors.blue,
            data: [<?php
                $i=0;
                foreach ($HouseArr as $value){
                    $i++;
                    if($i==1){ echo $value['numbers'];
                    }else{ echo ",".$value['numbers']."";
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
                    text: '调研完成数'
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
                    <h5> 问卷 <?=$QuestionProject->title?> 在 <?=$ProjectName;?> 管家调查完成数</h5>
                        <!---<a href="/question/statistical-query-export?question_project_id=<?=$QuestionProject->id;?>&region_id=<?=$region_id;?>&project_id=<?=$project_id;?>" target="_blank" class="btn btn-success pull-right">导出表格</a>-->
                </div>
                <div class="ibox-content">

                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th>编号</th>
                            <th>管家名称</th>
                            <th>调研完成数</th>
                        </tr>
                        </thead>
                        <tbody><?php
                        $i=0;
                        foreach ($HouseArr as $value){
                        $i++;
                            ?><tr>
                            <td><?=$value['id'];?></td>
                            <td><?=$value['nickname'];?></td>
                            <td><?=$value['numbers'];?></td>
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
                    <h5> 问卷 <?=$QuestionProject->title?> 在 <?=$ProjectName;?> 管家调查完成数</h5>
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



