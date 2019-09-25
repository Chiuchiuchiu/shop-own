<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/6/8
 * Time: 15:46
 */
/* @var $newDataProvider \yii\data\ActiveDataProvider */
$this->title = '投票管理';
$this->params['breadcrumbs'][] = $this->title;

use \common\models\ButlerElectionActivity;
?>

<?php \components\inTemplate\widgets\IBox::begin(); ?>
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">

        <div class="col-lg-3">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>最美卫士</h5>
                </div>
                <div class="ibox-content">

                    <table class="table">
                        <tbody>
                        <tr>
                            <td>参与人数：</td>
                            <td><?= ButlerElectionActivity::find()->where(['group' => \common\models\ButlerElectionActivity::GROUP_SECURITY])->count() ?></td>
                        </tr>
                        <tr>
                            <td>累计投票：</td>
                            <td class="text-danger">
                                <?=
                                    ButlerElectionActivity::find()
                                        ->select('number')
                                        ->where(['group' => \common\models\ButlerElectionActivity::GROUP_SECURITY])
                                        ->sum('number')
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td>备选项目：</td>
                            <td>
                                <?=
                                    ButlerElectionActivity::find()
                                        ->select('number, project_house_id')
                                        ->where(['group' => \common\models\ButlerElectionActivity::GROUP_SECURITY])
                                        ->orderBy('number DESC')
                                        ->one()->project->house_name
                                    ?? '-';
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td>最高票数：</td>
                            <td class="text-danger">
                                <?=
                                    ButlerElectionActivity::find()
                                        ->select('number')
                                        ->where(['group' => \common\models\ButlerElectionActivity::GROUP_SECURITY])
                                        ->orderBy('number DESC')->one()->number
                                    ?? 0;
                                    ?>
                            </td>
                        </tr>
                        <tr>
                            <td>开始时间：</td>
                            <td>
                                <?=
                                    date('Y-m-d',
                                        ButlerElectionActivity::find()
                                            ->select('created_at')
                                            ->where(['group' => \common\models\ButlerElectionActivity::GROUP_SECURITY])
                                            ->orderBy('created_at ASC')
                                            ->one()->created_at ?? time()
                                    )
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td>截止投票：</td>
                            <td><?= date('Y-m-d', time()) ?></td>
                        </tr>
                        <tr>
                            <td>
<!--                                <a href="vote-create-or-update?id=1">管理</a>-->
                            </td>
                            <td>
                                <a href="vote-detail?group=2">查看</a>
                            </td>
                        </tr>
                        </tbody>
                    </table>

                </div>
            </div>
        </div>

        <div class="col-lg-3">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>最美管家</h5>
                </div>
                <div class="ibox-content">

                    <table class="table">
                        <tbody>
                        <tr>
                            <td>参与人数：</td>
                            <td><?= ButlerElectionActivity::find()->where(['group' => \common\models\ButlerElectionActivity::GROUP_BUTLER])->count() ?></td>
                        </tr>
                        <tr>
                            <td>累计投票：</td>
                            <td class="text-danger">
                                <?=
                                ButlerElectionActivity::find()
                                    ->where(['group' => \common\models\ButlerElectionActivity::GROUP_BUTLER])
                                    ->select('number')
                                    ->sum('number')
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td>备选项目：</td>
                            <td>
                                <?=
                                ButlerElectionActivity::find()
                                    ->select('number, project_house_id')
                                    ->where(['group' => \common\models\ButlerElectionActivity::GROUP_BUTLER])
                                    ->orderBy('number DESC')->one()->project->house_name
                                ?? '-';
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td>最高票数：</td>
                            <td class="text-danger">
                                <?=
                                    ButlerElectionActivity::find()
                                        ->where(['group' => \common\models\ButlerElectionActivity::GROUP_BUTLER])
                                        ->select('number')
                                        ->orderBy('number DESC')->one()->number
                                ?? 0;
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td>开始时间：</td>
                            <td>
                                <?=
                                    date('Y-m-d',
                                        ButlerElectionActivity::find()
                                        ->select('created_at')
                                        ->orderBy('created_at ASC')
                                        ->one()->created_at
                                        ?? time()
                                    )
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td>截止投票：</td>
                            <td><?= date('Y-m-d', time()) ?></td>
                        </tr>
                        <tr>
                            <td>
<!--                                <a href="vote-create-or-update?id=1">管理</a>-->
                            </td>
                            <td>
                                <a href="vote-detail?group=1">查看</a>
                            </td>
                        </tr>
                        </tbody>
                    </table>

                </div>
            </div>
        </div>

    </div>
</div>
<?php \components\inTemplate\widgets\IBox::end(); ?>
