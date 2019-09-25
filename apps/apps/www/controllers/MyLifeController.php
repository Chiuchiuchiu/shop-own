<?php
 
namespace apps\www\controllers;

use Yii;
use apps\www\module\viewShop\shopApi;
use common\models\Project;
use apps\api\models\MemberHouse;

use common\models\Meter;
use common\models\SysSwitch;

/*
* 我的生活
*/
class MyLifeController extends Controller
{
    public function actionIndex()
    {
        $projectId     = isset($this->project->house_id) ? $this->project->house_id : null;

        //-------------------feng -------------------商城店铺--------------------
        $_project = Project::find()->where(['house_id'=>$projectId])->all();
        $_url_key = $_project[0]['url_key'];

        $shopList = [];
        $ser = new shopApi();
        $result = $ser->getShopListByGroup($projectId);

        $memHouse = MemberHouse::findOne(['member_id' => $this->user->id]);
        $houseID  = 0;
        if ($memHouse != null) {
            $houseID = $memHouse['house_id'];
        }
        if($result['code'] == 200) {
            $shopList = $result['data'];
        }
        //-----------------end------------------------------------------------

       $navList       = [
            '<li>
                <a href="/activities/index"><i class="icon "  style="background-color: #3eb7b0;"><img src="/static/images/ico/huodong.png"  width="28"  height="28"></i>最新活动</a>
            </li>',
        ];

        if (SysSwitch::lxjProject('lxjProject', $projectId)) {
            $aUrl = '<li>
                  <a class="location-lxj" href="javascript: void(0);" data-h="' . Yii::$app->params['lxj_href'] . $this->user->phone . '"><i class="icon"  style="background-color: #FD781D;">
                  <img src="/static/images/ico/menjin.png"  width="28"  height="28"></i>门禁</a></li>';
            array_push($navList, $aUrl);
        }
        if (SysSwitch::inVal('projectParking', $projectId) && SysSwitch::inVal('projectShowParking', $projectId)) {
            $aUrl = '<li>
                  <a class="parking-url"><i class="icon "><img src="/static/images/ico/ico13.png"  width="40"  height="40"></i>停车费</a>
                  </li>';
            array_push($navList, $aUrl);
        }

        //------------
        $MeterCount = Meter::find()->where(['project_id' => $projectId])->count();

        return $this->renderPartial('index', [
            'projectInfo'          => $this->project,
            'shopList'             => $shopList,
            'pk'                   => $_url_key,
            'MeterCount'           => $MeterCount,
            'pid'                  => $projectId,
            'houseID'              => $houseID,
            'currentPage'	       => 'mylife',
            'aUrl'                 => $navList,
            'cdj_header_tip'       =>$this->CDJ_TIP,
        ]);

    }



}