<?php
namespace apps\api\controllers;


use apps\api\models\Butler;
use apps\api\models\Member;
use common\models\MemberHouse;
use common\models\MeterHouse;
use common\models\MeterLog;
use common\models\MeterUpload;
use components\wechatSDK\QYWechatSDK;
use yii\data\ActiveDataProvider;
use yii\data\Pagination;
use yii\db\Exception;
use yii\helpers\ArrayHelper;

class MeterController extends Controller
{
    public $modelClass = 'apps\api\models\Meter';

    public function actions()
    {
        $actions =  parent::actions();

        unset($actions['index']);
        unset($actions['delete']);
        unset($actions['update']);
        unset($actions['create']);

        return $actions;
    }

    /**
     * 设备列表
     * @param int $type
     * @author zhaowenxi
     */
    public function actionList($type = 0){

        $res = [];

        switch ($type){
            case 1: $typeName = "电表";break;
            case 2: $typeName = "冷水表";break;
            default: $typeName = "";break;
        }

        $MemberHouseArr= MemberHouse::find()
            ->where(['status'=>2,'member_id'=>$this->userId])
            ->select(['house_id'])
            ->all();

        $memberHouseId = ArrayHelper::getColumn($MemberHouseArr, 'house_id');

        $dataProvider = new ActiveDataProvider([
            'query' => MeterHouse::find()
                ->where(['in','house_id',$memberHouseId])
                ->andWhere(['type_id'=>0])
                ->andFilterWhere(['meter_type' => $typeName])
                ->orderBy("status asc,id desc"),
            'pagination' => new Pagination([
                'validatePage' => false,
            ])
        ]);

        foreach ($dataProvider->getModels() as $v){
            $res[] = [
                'id' => $v->id,
                'uid' => $v->uid,
                'status' => $v->statusText,
                'ancestorName' => $v->ancestor_name,
                'lastMeterData' => $v->last_meter_data,
                'lastMeterTime' => date("Y-m-d H:i:s", $v->last_meter_time),
                'type' => $v->meter_type,
            ];
        }

        return $this->renderJsonSuccess(200, $res);
    }

    /**
     * 统计列表
     * @author zhaowenxi
     */
    public function actionCount(){

        $res = ['wait' => 0, 'water' => 0, 'electricity' => 0];

        $MemberHouseArr= MemberHouse::find()
            ->where(['status'=>2,'member_id'=>$this->userId])
            ->select(['house_id'])
            ->all();

        $memberHouseId = ArrayHelper::getColumn($MemberHouseArr, 'house_id');

        $dataProvider = new ActiveDataProvider([
            'query' => MeterHouse::find()
                ->where(['in','house_id',$memberHouseId])
                ->andWhere(['type_id'=>0])
                ->orderBy("status asc,id desc")
        ]);

        foreach ($dataProvider->getModels() as $v){
            switch ($v->meter_type){
                case "电表": $res['electricity']++;break;
                case "冷水表": $res['water']++;break;
            }

            $v->status == 2 && $res['wait']++;
        }

        return $this->renderJsonSuccess(200, $res);
    }

    public function actionDetail($id = 0){

        $res = [];

        if(empty($id)){
            return $this->renderJsonFail(40010);
        }

        $data = MeterHouse::findOne($id);

        if($data){

            $res = [
                'id'            => $data->id,
                'uid'           => $data->uid,
                'name'          => $data->ownername,
                'ancestorName'  => $data->ancestor_name,
                'lastMeterData' => $data->last_meter_data,
                'lastMeterTime' => date("Y-m-d", $data->last_meter_time),
            ];
        }

        return $this->renderJsonSuccess(200, $res);
    }


    public function actionUpdate($id){

        $post = $this->post();
        var_dump($post);exit;
        $post['meterData'] === $post['dataConfirm'] || $this->renderJsonFail(90001);

        $meterHouse = MeterHouse::findOne($id);

        $post['meterData'] < $meterHouse->last_meter_data && $this->renderJsonFail(90002);

        $meterHouse->status == 2 || $this->renderJsonFail(41003);

        $model = new MeterLog();
        $model->uid             = $meterHouse->uid;
        $model->last_meter_data = $meterHouse->last_meter_data;
        $model->last_meter_time = $meterHouse->last_meter_time;
        $model->meter_data      = $post['meterData'];
        $model->meter_type      = $meterHouse->meter_type;
        $model->house_id        = $meterHouse->house_id;
        $model->project_id      = $meterHouse->project_id;
        $model->member_id       = $this->userId;
        $model->meter_id        = $meterHouse->meter_id;
        $model->status          = 2;
        $model->meter_time      = time();
        $model->created_at      = time();

        $transaction = MeterHouse::getDb()->beginTransaction();

        try{
            if(!$model->save()) throw new \Exception(41001);

            if(isset($post['pics']) && $post['pics']){

                $picModel = new MeterUpload();

                $picModel->meter_id       = $model->meter_id;
                $picModel->meter_house_id = $meterHouse->house_id;
                $picModel->project_id     = $meterHouse->project_id;
                $picModel->status         = 1;
                $picModel->uid            = $meterHouse->uid;
                $picModel->pic            = $post['pics'];
                $picModel->created_at     = time();

                if(!$picModel->save()) throw new \Exception(41000);
            }

            $meterHouse->status= $meterHouse::STATUS_WAIT_SUMMARY;

            if(!$meterHouse->save()) throw new \Exception(41001);

            $transaction->commit();

            self::projectMeter($model->id);

            $this->renderJsonSuccess(200);exit;

        }catch (\Exception $e){

            $transaction->rollBack();

            $this->renderJsonFail($e->getMessage());
        }
    }

    /**
     * 业主抄表后通知管家
     * @param $id
     * @author zhaowenxi
     */
    public static function projectMeter($id){

        $Items = MeterLog::findOne(['id'=>$id]);

        //微信通知仅通知管辖区域下的管家
        $butler = Butler::find()->select('wechat_user_id')
            ->leftJoin('butler_region', 'butler_region.butler_id = butler.id')
            ->where(['butler_region.house_id' => $Items->house_id, 'status' => Butler::STATUS_ENABLE])->all();

        $users = '';

        if($butler){
            $users = ArrayHelper::getColumn($butler, 'wechat_user_id');
            $users = implode($users,'|');
        }

        $userItem = Member::findOne(['id'=>$Items->member_id]);

        $ancestor_name = $Items->ancestor_name();

        $surname = $userItem->name == '' ? $userItem->nickname : $userItem->name;

        $content = "水电抄表审核\n业主:【{$surname}】\n房产:【{$ancestor_name}】\n仪表类型:【{$Items->meter_type}】\n仪表读数:【{$Items->meter_data}】\n<a href=\"http://butler.51homemoney.com/meter/index\">点击查看</a>";

        $data = [
            'touser' => $users,
            'msgtype' => 'text',
            'agentid' => '53', //中奥通讯录应用 ID
            'text' => [
                'content' => $content
            ],
            'safe' => 0,
        ];

        (new QYWechatSDK(\Yii::$app->params['wechatQY']))->sendMsg($data);
    }
}