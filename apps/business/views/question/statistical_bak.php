<?php


/* @var $this yii\web\View */
/* @var $model common\models\Article */
/* @var $categoryMap array */

$this->title = '新建问卷';
$this->params['breadcrumbs'][] = ['label' => '文章内容', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
    #flot-placeholder{
        width:350px;
        height:300px;
    }

</style>

<div class="row">
    <div class="col-lg-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>样本量完成情况</h5>
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
                <h5>样本量完成分布图</h5>
            </div>
            <div class="ibox-content">
                <div class="flot-chart-pie-content" id="flot-pie-chart"></div>
            </div>
        </div>
    </div>

</div>
<!-- Flot -->
<script src="/static/js/plugins/flot/jquery.flot.js"></script>
<script src="/static/js/plugins/flot/jquery.flot.tooltip.min.js"></script>
<script src="/static/js/plugins/flot/jquery.flot.resize.js"></script>
<script src="/static/js/plugins/flot/jquery.flot.pie.js"></script>
<script src="/static/js/plugins/flot/jquery.flot.time.js"></script>
<script src="/static/js/demo/flot-demo.js"></script>
