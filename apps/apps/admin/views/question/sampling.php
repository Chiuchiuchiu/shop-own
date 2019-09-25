<?php


/* @var $this yii\web\View */
/* @var $model common\models\Article */
/* @var $categoryMap array */

$this->title = '样本量完成情况';
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
    var barChartData = {
        labels: [
            <?php
            $i=0;
            foreach ($dataProvider as $value){
                $i++;
                if($i==1){
                    echo "'".$value['name']."'";
                }else{

                    echo ",'".$value['name']."'";
                }
            }?>],
        datasets: [{
            label: '采样量',
            backgroundColor: window.chartColors.blue,
            data: [<?php
                $i=0;
                foreach ($dataProvider as $value){
                    $i++;
                    if($i==1){ echo $value['RegionCount'];
                    }else{ echo ",".$value['RegionCount']."";
                    }
                }?>]
        }]

    };
    window.onload = function() {
        var ctx = document.getElementById('canvas').getContext('2d');
        window.myBar = new Chart(ctx, {
            type: 'bar',
            data: barChartData,
            options: {
                title: {
                    display: true,
                    text: '样本量完成情况柱形图'
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

    document.getElementById('randomizeData').addEventListener('click', function() {
        barChartData.datasets.forEach(function(dataset) {
            dataset.data = dataset.data.map(function() {
                return randomScalingFactor();
            });
        });
        window.myBar.update();
    });
</script>
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>样本量完成情况  当前共<?=$CountRegion;?>个分公司 ，<?=$CountProject;?>个项目，共<?=$QuestAnswerCount;?>份样本</h5>
                </div>
                <div class="ibox-content">
                    <table border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <?php
                            $i=0;
                            foreach ($dataProvider as $value){
                                $i++;
                                ?>
                                <td width="300" valign="top">
                                    <table class="table table-bordered" width="100%">
                                        <thead>
                                        <tr>
                                            <th>项目</th>
                                            <th>样本量</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td style="background-color: #d6eaf7;"><?=$value['name']?></td>
                                            <td style="background-color: #d6eaf7;"><?=$value['RegionCount']?></td>
                                        </tr>
                                        <?php
                                        foreach ($value['ListArr'] as $v){
                                            ?>
                                            <tr>
                                                <td><?=$v['house_name']?></td>
                                                <td><?=$v['ProjectCount']?></td>
                                            </tr>
                                        <?php }?>
                                        </tbody>
                                    </table></td>

                                <?php

                                if($i%5==0) {
                                    echo '</tr><tr>';
                                }
                            }?>
                        </tr>
                    </table>


                </div>
            </div>
        </div>

        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>分公司采样<small>With custom colors.</small></h5>
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
                        <canvas id="canvas" style="height: 500px;"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>