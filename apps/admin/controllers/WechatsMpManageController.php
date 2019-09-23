<?php
/**
 * 统一微信订阅号管理
 * User: HQM
 * Date: 2017/2/15
 * Time: 11:13
 */

namespace apps\admin\controllers;


use app\models\WpUser;
use apps\admin\models\Manager;
use apps\pm\models\WechatsConfigPublic;
use apps\pm\models\WpPublic;
use apps\pm\models\WpPublicLink;
use common\models\House;
use common\models\MemberHouse;
use common\models\PmOrder;
use common\models\PmOrderItem;
use common\models\Project;
use common\valueObject\RangDateTime;
use components\cryptographic\AuthCode;
use yii\data\ActiveDataProvider;

class WechatsMpManageController extends Controller
{
    public function actionIndex($search = null)
    {
        $publicUseWechatsMp = WechatsConfigPublic::find()->where(['project_id' => 0])->one();

        $projectAmount = Project::find()->count('house_id');

        $dataProvider = new ActiveDataProvider();
        $dataProvider->query = WechatsConfigPublic::find()
            ->where(['>', 'project_id', 0])
            ->andFilterWhere(['like', 'project_name', $search])->orderBy('all_users_total DESC');

        $dataProvider->setSort(false);

        return $this->render('index', get_defined_vars());
    }

    public function actionOpenWmp($id)
    {
        $findResult = $this->getProjectAccountWechatId($id);

        if (!$findResult) {
            $this->setFlashError('该项目未绑定公众号');
            return $this->redirect(['index']);
        }

        $syncWechatsConfigToWpPublicResult = WpPublic::findOne(['public_id' => $findResult->public_id]);
        $syncPmManagerToWmpUserResult = $this->syncPmManagerToWmpUser($this->user);
        if($syncPmManagerToWmpUserResult){
            $insertWmpPublicLinkResult = $this->insertWmpPublicLink($syncPmManagerToWmpUserResult, $syncWechatsConfigToWpPublicResult);
        }


        $userId = $findResult->uid;
        $userName = md5($this->user->email);
        $time = time();

        $autoCode = new AuthCode();
        $wmp_auto_k = $autoCode->authCode($findResult->token, 'ENCODE');

        $url_key = isset($findResult->project->url_key) ? $findResult->project->url_key : 0;
        $wmp_pro = $autoCode->authCode($url_key, 'ENCODE');

        $sign = md5($userId . $userName . $_SERVER['HTTP_USER_AGENT'] . $time);
        $token = $userId . ',' . $time . ',' . $userName . ',' . $sign;

        $wmpAutoToken = $autoCode->authCode($findResult->token, 'ENCODE');

        setcookie('wmp_sign', $sign, 0, '/', \Yii::$app->params['domain.root']);
        setcookie('wmp_token', $token, 0, '/', \Yii::$app->params['domain.root']);
        setcookie('wmp_auto_k', $wmp_auto_k, 0, '/', \Yii::$app->params['domain.root']);
        setcookie('wmp_proUK', $wmp_pro, 0, '/', \Yii::$app->params['domain.root']);
        setcookie('wmp_autK', $wmpAutoToken, 0, '/', \Yii::$app->params['domain.root']);
        if ($id) {
            $projectName = $findResult->projectName;
        } else {
            $projectName = $findResult->public_name;
        }

          return $this->render('open-wmp', get_defined_vars());
    }
    public function actionDemo()
    {
        print_r($_COOKIE);

    }

    public function actionWeekData($type=0)
    {
        $dateTime = (new RangDateTime())->autoLoad($this->get());

        switch ($type){
            case 0:
                $result = House::find()
                    ->select('COUNT(member_house.house_id) as count')
                    ->leftJoin('member_house', 'house.house_id = member_house.house_id')
                    ->leftJoin('project', 'house.project_house_id = project.house_id')
                    ->where(
                        [
                            'member_house.status'=>MemberHouse::STATUS_ACTIVE,
                            'member_house.is_first'=>1,
                        ]
                    )
                    ->andFilterWhere(['BETWEEN', 'member_house.updated_at', $dateTime->getStartTime(), $dateTime->getEndTime()])
                    ->asArray()
                    ->one();

                if(empty($dateTime->getStartTime())){
                    $sql = "SELECT p.`house_name`,tb2.* FROM `project` p RIGHT JOIN (SELECT tb1.group,h.`project_house_id`,COUNT(1) count FROM (SELECT * FROM `member_house` WHERE `status` = 2 AND `is_first`=1 GROUP BY `house_id`) tb1 LEFT JOIN `house` h on tb1.house_id=h.`house_id` GROUP BY tb1.group,h.`project_house_id` order BY h.`project_house_id`) tb2 on p.`house_id` = tb2.project_house_id";
                } else {
                    $sql = "SELECT p.`house_name`,tb2.* FROM `project` p RIGHT JOIN (SELECT tb1.group,h.`project_house_id`,COUNT(1) count FROM (SELECT * FROM `member_house` WHERE `status` = 2 AND `is_first`=1 AND updated_at BETWEEN  ". $dateTime->getStartTime() ." AND ". $dateTime->getEndTime() ." GROUP BY `house_id`) tb1 LEFT JOIN `house` h on tb1.house_id=h.`house_id` GROUP BY tb1.group,h.`project_house_id` order BY h.`project_house_id`) tb2 on p.`house_id` = tb2.project_house_id";
                }

                $rs = \Yii::$app->db->createCommand($sql)->queryAll();

                break;
            case 1:

                $result['amount'] = PmOrder::find()
                    ->where(
                        [
                            'pm_order.status'=>PmOrder::STATUS_PAYED,
                        ]
                    )
                    ->andFilterWhere(['BETWEEN', 'pm_order.payed_at', $dateTime->getStartTime(), $dateTime->getEndTime()])
                    ->sum('total_amount');

                $dataProvider = new ActiveDataProvider();
                $dataProvider->query = PmOrder::find()
                    ->select('project.house_id AS pHouseId, project.house_name, pm_order.house_id,SUM(pm_order.total_amount) as total_amount, COUNT(pm_order.house_id) number')
                    ->leftJoin('project', 'pm_order.project_house_id = project.house_id')
                    ->where(
                        [
                            'pm_order.status'=>PmOrder::STATUS_PAYED,
                        ]
                    )
                    ->andFilterWhere(['BETWEEN', 'pm_order.payed_at', $dateTime->getStartTime(), $dateTime->getEndTime()])
                    ->groupBy('project.house_name, pm_order.house_id')
                    ->orderBy('project.house_name');
                break;
        }


        return $this->render('week-data', get_defined_vars());
    }

    /**
     * 导出不去重认证数
     * @param int $type
     * @param int $distinct 是否去重
     * @throws \yii\db\Exception
     * @author zhaowenxi
     */
    public function actionExport($type=0, $distinct=0)
    {
        $dateTime = (new RangDateTime())->autoLoad($this->get());
        $rs = null;
        $str = '';
        $projectName = '周报表--';

        switch ($type){
            case 0:
                $str="项目名,分公司,总数,类型,认证数\n";
                $projectName .= '认证';

                if(empty($dateTime->getStartTime())){
                    $sql = "SELECT p.`house_name`,tb2.* FROM `project` p RIGHT JOIN (SELECT tb1.group,h.`project_house_id`,COUNT(1) count FROM (SELECT * FROM `member_house` WHERE `status` = 2 " . ($distinct ? " AND `is_first` = 1 GROUP BY `house_id`" : '') . ") tb1 LEFT JOIN `house` h on tb1.house_id=h.`house_id` GROUP BY tb1.group,h.`project_house_id` order BY h.`project_house_id`) tb2 on p.`house_id` = tb2.project_house_id";
                } else {
                    $sql = "SELECT p.`house_name`,tb2.* FROM `project` p RIGHT JOIN (SELECT tb1.group,h.`project_house_id`,COUNT(1) count FROM (SELECT * FROM `member_house` WHERE `status` = 2 " . ($distinct ? " AND `is_first` = 1 " : '') . "AND updated_at BETWEEN  ". $dateTime->getStartTime() ." AND ". $dateTime->getEndTime() . ($distinct ? " GROUP BY `house_id`" : '') . " ) tb1 LEFT JOIN `house` h on tb1.house_id=h.`house_id` GROUP BY tb1.group,h.`project_house_id` order BY h.`project_house_id`) tb2 on p.`house_id` = tb2.project_house_id";
                }

                $rs = \Yii::$app->db->createCommand($sql)->queryAll();

                $fileName = $projectName . $dateTime->getStartDate().'至'.$dateTime->getEndDate(). ($distinct ? "去重" : '') . '认证数据.csv';
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename=' . iconv ( 'utf-8', 'gbk//IGNORE', $fileName ));
                echo iconv( 'utf-8', 'gbk//IGNORE', $str );

                $array = [];
                foreach($rs as $rKey => $rVal){
                    $array = [$rVal['house_name']];

                    $projectRegionName = Project::findOne(['house_id' => $rVal['project_house_id']])->projectRegionName;
                    array_push($array, $projectRegionName);

                    isset($rVal['charge_item_name']) && array_push($array, $rVal['charge_item_name']);

                    if(isset($rVal['group'])){
                        switch ($rVal['group']){
                            case 1:
                                $parkingSpacesAmount = \common\models\House::find()
                                    ->where(['house.project_house_id'=>$rVal['project_house_id'], 'reskind' => 5])
                                    ->count();
                                array_push($array, $parkingSpacesAmount);
                                array_push($array, \common\models\MemberHouse::groupMap()[$rVal['group']]);
                            break;
                            case 2:
                                $parkingSpacesAmount = \common\models\House::find()
                                    ->where(['house.project_house_id'=>$rVal['project_house_id'], 'reskind' => [9, 11]])
                                    ->count();
                                array_push($array, $parkingSpacesAmount);
                                array_push($array, \common\models\MemberHouse::groupMap()[$rVal['group']]);
                            break;
                        }
                    }

                    isset($rVal['count']) && array_push($array, $rVal['count']);
                    isset($rVal['amount']) && array_push($array, number_format($rVal['amount'],2,'.',''));

                    $str = implode(',', $array)."\n";
                    echo iconv('utf-8', 'gbk//IGNORE', $str);

                }

                unset($array);

            break;
            case 1:
                $str="项目全称,分公司,房地全称,房产类型,缴费笔数,缴费额\n";
                $projectName .= '缴费';

                $rs = PmOrder::find()
                    ->select('project.house_id AS pHouseId, project.house_name, pm_order.house_id,SUM(pm_order.total_amount) as total_amount, COUNT(pm_order.house_id) number')
                    ->leftJoin('project', 'pm_order.project_house_id = project.house_id')
                    ->where(
                        [
                            'pm_order.status'=>PmOrder::STATUS_PAYED,
                        ]
                    )
                    ->andFilterWhere(['BETWEEN', 'pm_order.payed_at', $dateTime->getStartTime(), $dateTime->getEndTime()])
                    ->groupBy('project.house_name, pm_order.house_id')
                    ->orderBy('project.house_name');

                $fileName = $projectName . $dateTime->getStartDate().'至'.$dateTime->getEndDate().'账单.csv';
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename=' . iconv ( 'utf-8', 'gbk//IGNORE', $fileName ));
                echo iconv( 'utf-8', 'gbk//IGNORE', $str );

                foreach ($rs->each() as $row) {
                    /**
                     * @var $row PmOrder
                     */
                    $str = implode(',', [
                            $row->house->project->house_name,
                            $row->house->project->projectRegionName,
                            $row->house->ancestor_name,
                            $row->house->reskind,
                            $row->number,
                            number_format($row->total_amount, 2, '.', ''),]) . "\n";

                    echo mb_convert_encoding($str, 'GBK', 'UTF8');
                }

            break;
        }

        die();
    }

    /**
     * 同步用户数据
     */
    public function actionSyncWechatsUsers()
    {

        return $this->render('sync-wechats-users');
    }

    /**
     * 查看微信数据
     */
    public function actionShowWechatsData()
    {
        $wmpManageInfo = WpUser::findOne(['uid' => 1]);

        if ($wmpManageInfo) {
            $autoCode = new AuthCode();
            $managerLoginName = $autoCode->authCode($wmpManageInfo->login_name, 'ENCODE');

            $time = time();
            $sign = md5($managerLoginName . $_SERVER['HTTP_USER_AGENT'] . $time);
            $token = $managerLoginName . ',' . $time  . ',' . $sign;

            setcookie('sd_sign', $sign, 0, '/', \Yii::$app->params['domain.root']);
            setcookie('sd_token', $token, 0, '/', \Yii::$app->params['domain.root']);

        }

        return $this->render('show-wechats-data', get_defined_vars());
    }

    protected function getProjectAccountWechatId($id)
    {
        $res = WechatsConfigPublic::findOne(['project_id' => $id]);
        return isset($res->id) ? $res : 0;
    }

    /**
     * @param object $userObj  WpUser
     * @param object $mpObj WpPublic
     * @return WpPublicLink|bool|null|static
     */
    private function insertWmpPublicLink($userObj, $mpObj)
    {
        if(!$mpObj) return false;
        /**
         * @var $userObj WpUser
         * @var $mpObj WpPublic
         */
        $model = WpPublicLink::find()->where(['uid' => $userObj->uid, 'mp_id' => $mpObj->id])->one();

        if(!$model){
            $model = new WpPublicLink();
            $model->uid = $userObj->uid;
        }

        $model->mp_id = $mpObj->id;
        $model->is_creator = 1;
        if($model->save()){
            return $model;
        }

        return false;
    }

    private function syncPmManagerToWmpUser($obj)
    {
        /**
         * @var $obj Manager
         */
        $model = WpUser::findOne(['sync_user_id' => $obj->id]);

        if(!$model){
            $model = new WpUser();
        }

        $model->nickname = $obj->email;
        $model->password = $obj->password;
        $model->language = 'zh-cn';
        $model->reg_time = time();
        $model->login_name = $obj->email;
        $model->is_audit = 1;
        $model->is_init = 1;
        $model->reg_ip = \Yii::$app->request->getUserIP();
        $model->sync_user_id = $obj->id;
        if($model->save()){
            return $model;
        }

        return false;
    }

}