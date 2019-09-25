<?php namespace apps\www\controllers;

use common\models\Butler;
use common\models\House;
use common\models\Member;
use common\models\MemberHouse;
use common\models\MeterLog;
use common\models\MeterUpload;
use common\models\Project;
use common\models\MeterHouse;
use yii\data\ActiveDataProvider;
use components\wechatSDK\QYWechatSDK;
use yii\base\ErrorException;
use yii\db\Exception;
use yii\web\NotFoundHttpException;
use yii\helpers\ArrayHelper;

class MeterController extends Controller
{
    public $enableCsrfValidation = false;

    public function actionIndex()
    {

        $projectName = $this->project->house_name;

        $hasHouse = $this->hasHouse();

        $MeCount  = MeterLog::find()
            ->where(['member_id'=>$this->user->id,'status'=>4])
            ->count();
        $MembrHouseArr= MemberHouse::find()
            ->where(['status'=>2,'member_id'=>$this->user->id])
            ->select(['house_id'])
            ->all();
        $memberHouseId = ArrayHelper::getColumn($MembrHouseArr, 'house_id');
        $MeterCount = MeterHouse::find()
            ->where(['status'=>2,'type_id'=>0])
            ->andWhere(['in','house_id',$memberHouseId])
            ->count();

        $MeterCount1 = MeterHouse::find()
            ->where(['meter_type'=>'电表','type_id'=>0])
            ->andWhere(['in','house_id',$memberHouseId])
            ->count();

        $MeterCount2 = MeterHouse::find()
            ->where(['meter_type'=>'冷水表','type_id'=>0])
            ->andWhere(['in','house_id',$memberHouseId])
            ->count();

        return $this->renderPartial('index', [
            'member'            => $this->user,
            'MeCount'           => $MeCount,
            'MeterCount'        => $MeterCount,
            'MeterCount1'       => $MeterCount1,
            'MeterCount2'       => $MeterCount2,
            'projectName'       => $projectName,
            'hasHouse'          => $hasHouse,
            'cdj_header_tip'    => $this->CDJ_TIP,
        ]);

    }
    public function actionHistory($type=null){


        if($type==null){
            $type=0;
        }
        $MeterCount1  = MeterLog::find()
            ->where(['member_id'=>$this->user->id,'status'=>4])
            ->count();
        $MeterCount2  = MeterLog::find()
            ->where(['member_id'=>$this->user->id,'meter_type'=>'电表','status'=>4])
            ->count();
        $MeterCount3  = MeterLog::find()
            ->where(['member_id'=>$this->user->id,'meter_type'=>'冷水表','status'=>4])
            ->count();

        if($type==0){
            $MeterList = MeterLog::find()
                ->where(['member_id'=>$this->user->id,'status'=>4])
                ->orderBy('id desc,updated_at asc')
                ->all();
        }else{
            if($type==2){
                $meter_type = '冷水表';
            }else{
                $meter_type = '电表';
            }
            $MeterList = MeterLog::find()
                ->where(['member_id'=>$this->user->id,'status'=>4,'meter_type'=>$meter_type])
                ->orderBy('id desc,updated_at asc')
                ->all();
        }


        return $this->renderPartial('history', [
            'MeterList'=>$MeterList,
            'type'=>$type,
            'MeterCount1'=>$MeterCount1,
            'MeterCount2'=>$MeterCount2,
            'MeterCount3'=>$MeterCount3]);
    }
    public function actionDevice($type=null)
    {
     if($type==0){
         $MembrHouseArr= MemberHouse::find()
             ->where(['status'=>2,'member_id'=>$this->user->id])
             ->select(['house_id'])
             ->all();
         $memberHouseId = ArrayHelper::getColumn($MembrHouseArr, 'house_id');
         $MeterList = MeterHouse::find()
             ->where(['in','house_id',$memberHouseId])
             ->andWhere(['type_id'=>0])
             ->orderby('status asc,id desc')
             ->limit(10)
             ->all();
     }else{
         if($type==1){
             $typs = '电表';
         }else{
             $typs = '冷水表';
         }

         $MembrHouseArr= MemberHouse::find()
             ->where(['status'=>2,'member_id'=>$this->user->id])
             ->select(['house_id'])
             ->all();
         $memberHouseId = ArrayHelper::getColumn($MembrHouseArr, 'house_id');
         $MeterList = MeterHouse::find()
             ->where(['meter_type'=>$typs,'type_id'=>0])
             ->andWhere(['in','house_id',$memberHouseId])
             ->orderby('id desc')
             ->limit(10)
             ->all();
     }
        return $this->renderPartial('list', [
            'member' => $this->user,
            'MeterList'=>$MeterList,
            'type'=>$type
        ]);
    }

    /**
     * 业主抄表页面
     * @return string
     * @author zhaowenxi
     */
    public function actionCreate($id=null)
    {

        $uid = date("YmdHis").mt_rand(1000000,9999999);

        $Items = MeterHouse::findOne(['id'=>$id]);
        $MeterHouse = new MeterHouse();
        return $this->render('create', [
            'member' => $this->user,
            'uid'=>$uid,
            'MeterHouse'=>$MeterHouse,
            'Items'=>$Items
        ]);
    }

    /**
     * 业主提交抄表ajax
     * @return string
     * @author zhaowenxi
     */
    public function actionSave(){
        $data_confirm = $this->post('data_confirm');
        $meter_data = $this->post('meter_data');
        $meter_id = $this->post('meter_id');
        $meter_house_id = $this->post('meter_house_id');
        $meter_type = $this->post('meter_type');

        $UploadUid = $this->post('UploadUid');

        $MeterHouse = MeterHouse::findOne(['id'=>$meter_house_id]);

        if($meter_data<$MeterHouse->last_meter_data){
            return  $this->renderJson(['code'=>1,'message'=>'读数不能小于上期读数']);
        }

        $ItemsCount = MeterLog::find()->where(['meter_data'=>$meter_data])
            ->andWhere(['>','meter_time',time()-60])
            ->count();
        if($ItemsCount>0){
            return  $this->renderJson(['code'=>1,'message'=>'请不要重复提交']);
        }

        if($data_confirm!==$meter_data){
            return  $this->renderJson(['code'=>1,'message'=>'确认读数错误']);
        }

        $Lib = new MeterLog();
        $Lib->uid = $UploadUid;
        $Lib->last_meter_data = $MeterHouse->last_meter_data;
        $Lib->last_meter_time = $MeterHouse->last_meter_time;
        $Lib->meter_data = $meter_data;
        $Lib->meter_type = $meter_type;
        $Lib->house_id = $MeterHouse->house_id;
        $Lib->project_id = $MeterHouse->project_id;
        $Lib->member_id = $this->user->id;
        $Lib->meter_id = $MeterHouse->meter_id;
        $Lib->status=2;
        $Lib->meter_time = time();
        $Lib->created_at = time();

        if($Lib->save()) {

            //保存图片
            if($this->post('MeterHouse')['pics']){
                $picModel = new MeterUpload();

                $picModel->meter_id = $meter_id;
                $picModel->meter_house_id = $meter_house_id;
                $picModel->project_id = $MeterHouse->project_id;
                $picModel->status = 1;
                $picModel->uid = $UploadUid;
                $picModel->pic = $this->post('MeterHouse')['pics'];
                $picModel->created_at = time();
                $picModel->save();
            }

            $MeterHouse->status= $MeterHouse::STATUS_WAIT_SUMMARY;
            $MeterHouse->save();
            self::projectMeter($Lib->id);
            return  $this->renderJson(['code'=>0,'message'=>'读数提交成功']);
        }else{
            return  $this->renderJson(['code'=>1,'message'=>'读数提交错误']);
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

        $UserItem = Member::findOne(['id'=>$Items->member_id]);

        $ancestor_name = $Items->ancestor_name();

        $surname = $UserItem->name == '' ? $UserItem->nickname : $UserItem->name;

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

    /**
     * 上传抄表图片
     * @return string
     * @throws Exception
     * @author zhaowenxi
     */
    public function actionMeterUpload()
    {
        $base64_string = $this->post('base64_string');
        $UploadUid = $this->post('UploadUid');
        $base64_string = base64_decode($base64_string);
        $name = date("YmdHis").mt_rand(1000000,9999999) . '.jpg';
        $savePath =dirname(__DIR__).'/web/Uploads/'.date('Ymd');
        $saveURL = '/Uploads/'.date('Ymd').'/'.$name;

        if (!file_exists($savePath)) {
            @mkdir($savePath);
        }
        if (!is_dir($savePath)) {
            throw new Exception('can not make dir');
        }
        file_put_contents($savePath . "//" . $name, $base64_string);

        $LibPic = new MeterUpload();
        $LibPic->uid = $UploadUid;
        $LibPic->pic = $saveURL;
        $LibPic->status = 0;
        $LibPic->created_at = time();
        $LibPic->save();
        return $this->renderJsonSuccess(['url' => $saveURL, 'saveUrl' =>$saveURL,'UploadUid'=>$UploadUid]);
    }
}