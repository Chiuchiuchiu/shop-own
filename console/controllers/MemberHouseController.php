<?php
/**
 * Created by PhpStorm.
 * User: mickey
 * Date: 2018/6/27
 * Time: 17:29
 */

namespace console\controllers;


use common\models\AuthHouseNotificationMember;
use common\models\MemberHouse;
use common\models\MemberPromotionCode;
use common\models\WechatRedPack;
use yii\console\Controller;
use yii\helpers\ArrayHelper;

class MemberHouseController extends Controller
{
    /**
     * 删除业主误认证房产（认证送红包） param：memberId,houseId
     * @param int $memberId
     * @param int $houseId
     * @throws \yii\db\StaleObjectException
     */
    public function actionDeleteReceiveRedHouse(int $memberId, int $houseId)
    {
        $memberHouse = MemberHouse::findOne(['member_id' => $memberId, 'house_id' => $houseId]);

        if($memberHouse) {
            $memberHouse->delete();
            $this->stdout("-------删除用户房产\n");
            $memberPCode = MemberPromotionCode::findOne(['house_id' => $houseId, 'member_id' => $memberId]);
            if ($memberPCode) {
                $memberPCode->delete();

                $this->stdout("----删除优惠记录\n");
            }

            $auth = AuthHouseNotificationMember::findOne(['member_id' => $memberId, 'house_id' => $houseId]);
            if ($auth) {
                $auth->delete();
                $this->stdout("------删除认证通知\n");
            }

            $redPack = WechatRedPack::findOne(['house_id' => $houseId, 'member_id' => $memberId, 'even_name' => '201712']);
            if ($redPack) {
                $redPack->delete();
                $this->stdout("--------删除红包\n");
            }

        } else {
            $this->stdout("-----not found\n");
        }

        $this->stdout("done\n");
        exit(0);
    }

    public function actionDeleteMemberHouse($member_id = [989,1170]){
        $houses989 = MemberHouse::find()->alias('mh')->select('mh.house_id')
            ->leftJoin('house','house.house_id = mh.house_id')
            ->where(['mh.member_id' => $member_id[0]])
            ->groupBy('house.project_house_id')
            ->limit(10)->all();

        $houses1170 = MemberHouse::find()->alias('mh')->select('mh.house_id')
            ->leftJoin('house','house.house_id = mh.house_id')
            ->where(['mh.member_id' => $member_id[1]])
            ->groupBy('house.project_house_id')
            ->limit(10)->all();


        $houses989Where = ArrayHelper::getColumn($houses989, 'house_id');
        $houses11709Where = ArrayHelper::getColumn($houses1170, 'house_id');

        $mergeWhere = array_merge($houses989Where, $houses11709Where);

        MemberHouse::deleteAll(['IN', 'house_id', $mergeWhere]);

        $this->stdout("-----Done\n");
    }
}