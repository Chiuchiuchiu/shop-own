<?php
/**
 * Created by PhpStorm.
 * User: zhaowenxi
 * Date: 2018/9/20
 * Time: 10:59
 */

namespace apps\api\controllers;

use apps\api\models\Project;
use apps\api\models\House;
use apps\api\models\Member;
use apps\www\models\WechatRedPackLog;
use common\models\AuthHouseNotificationMember;
use common\models\MemberHouse;
use common\models\MemberPromotionCode;
use common\models\WechatRedPack;
use common\models\ThirdpartyViewHistory;

class ShopController extends Controller
{
    public $modelClass = 'apps\api\models\Member';

    public function actions()
    {
        $actions =  parent::actions();

        unset($actions['index']);
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['save']);

        return $actions;
    }

    /**
     * 通过union_id统计业主房产数
     * @author zhaowenxi
     */
    public function actionHouseCount(){

        $res = ['count' => 0];

        $unionId = $this->get('id', '');

        if(empty($unionId)){
            return $this->renderJsonFail(40010, $unionId);
        }

        $res['count'] = MemberHouse::find()
            ->where(['`m`.`wechat_unionid`' => $unionId, 'status' => MemberHouse::STATUS_ACTIVE])
            ->leftJoin("`house` AS h ON `h`.`house_id` = `member_house`.`house_id`")
            ->leftJoin("`member` AS m ON `m`.`id` = `member_house`.`member_id`")
            ->count();

        return $this->renderJsonSuccess(200, $res);
    }

    public function actionProjects(){

        $res['data'] = Project::find()->select('house_id,url_key,house_name')
            ->where(['status' => Project::STATUS_ACTIVE])
            ->orderBy('project_region_id ASC')->asArray()->all();

        return $this->renderJsonSuccess(200, $res);

    }
}