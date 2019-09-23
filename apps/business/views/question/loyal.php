<?php


/* @var $this yii\web\View */
/* @var $model common\models\Article */
/* @var $categoryMap array */

$this->title = '忠诚度统计';
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
            foreach ($RegionDesc as $value){
                $i++;
                if($i==1){
                    echo "'".$value['name']."'";
                }else{

                    echo ",'".$value['name']."'";
                }
            }?>],
        datasets: [{
            label: '忠诚度',
            backgroundColor: window.chartColors.blue,
            data: [<?php
                $i=0;
                foreach ($RegionDesc as $value){
                    $i++;
                    if($i==1){ echo $value['loyal'];
                    }else{ echo ",".$value['loyal']."";
                    }
                }?>]
        }]

    };

    var barChartDataASC = {
        labels: [
            <?php
            $i=0;
            foreach ($RegionAsc as $value){
                $i++;
                if($i==1){
                    echo "'".$value['name']."'";
                }else{

                    echo ",'".$value['name']."'";
                }
            }?>],
        datasets: [{
            label: '忠诚度',
            backgroundColor: window.chartColors.blue,
            data: [<?php
                $i=0;
                foreach ($RegionAsc as $value){
                    $i++;
                    if($i==1){ echo $value['loyal'];
                    }else{ echo ",".$value['loyal']."";
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
                    text: '<?=$topTitle;?>忠诚度'
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
                    text: '<?=$topTitle;?>忠诚度'
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
                <div class="ibox-title">
                    <h5><?=$topTitle;?></h5>
                </div>
                <div class="ibox-content">

                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th>编号</th>
                            <th>标题</th>
                            <th>忠诚度%</th>
                            <th>点击详细</th>
                            <th>编号</th>
                            <th>标题</th>
                            <th>忠诚度%</th>
                            <th>点击详细</th>
                            <th>编号</th>
                            <th>标题</th>
                            <th>忠诚度%</th>
                            <th>点击详细</th>
                        </tr>
                        </thead>
                        <tbody><tr><?php
                        $i=0;
                        foreach ($RegionDesc as $value){
                        $i++;
                            ?>
                            <td><?=$i;?></td>
                            <td><?=$value['name'];?></td>
                            <td><?=$value['loyal'];?></td>
                            <td>
                                <?php if($region_id==0){?>
                                <a href="/question/loyal?region_id=<?=$value['id'];?>">查看下级</a>
                            <?php }?></td>
                            <?php
                            if($i%3==0) {
                                echo '</tr><tr>';
                            }
                        }
                            ?>
                        </tr>
                        </tbody>
                    </table>

                </div>
            </div>
        </div>

        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5><?=$topTitle;?> 排名靠前 </h5>
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
                    <h5><?=$topTitle;?> 排名靠后 </h5>
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



