<?php
/**
 * Created by PhpStorm.
 * User: HQM
 * Date: 2018-12-04
 * Time: 10:02
 */

namespace apps\api\controllers;


use apps\api\models\Project;
use apps\api\models\Repair;
use apps\butler\models\RepairResponse;
use components\wechatSDK\WxMiniProgram;

class RepairNotifyController extends Controller
{
    public $modelClass = 'apps\api\models\Repair';
    protected $missPermission = ['repair-notify'];

    public function actions()
    {
        $actions = parent::actions();

        unset($actions['index']);
        unset($actions['delete']);
        unset($actions['update']);
        unset($actions['create']);

        return $actions;
    }

    /**
     *
     * 新视窗报事处理状态回调通知
     * {
     *   "ServiceID": "提交事务接口返回的BusinessID",
     *   "ServiceState" : "报事状态值"
     * }
     *
     * 接收反馈结果：code 0 失败，1 成功
     *
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public function actionStatusNotify()
    {
        $postData = $this->post();

        //完善通知业主逻辑后可删除
        $this->writeFilelog($postData, 'repairStatus');

        if(isset($postData['ServiceID']) && $postData['ServiceState'] > 0){
            $repairResponse = RepairResponse::findOne(['business_id' => $postData['ServiceID']]);
            if($repairResponse){
                $repairResponse->service_state = $postData['ServiceState'];
                $repairResponse->save();

                $repair = Repair::findOne(['id' => $repairResponse->repair_id]);
                $repair->status = $postData['ServiceState'];
                $repair->save();

                //待业主评价
                if(in_array($postData['ServiceState'], [3, 7])){
                    $wechatUserId = $repair->member->wechat_open_id;

                    $project = Project::findOne(['house_id' => $repair->project_house_id]);
                    $wechatUrl = 'http://'.$project->url_key.'.p.51homemoney.com';

                    $data = [
                        'touser' => $wechatUserId,
                        'mp_template_msg' => [
                            'appid' => 'wx17dc98a4a5614aec',
                            'template_id' => '78Mp_ZYMgjs2_g_ozaIBQvgeqO8Cjp1kIrVB94zE4f8',
                            'url' => $wechatUrl.'/new-repair/customer-evaluation?id='.$repair->id,
                            'miniprogram' => [],
                            'data' => [
                                'first' => [
                                    'value' => $repair->flowStyleText . '进展',
                                    'color' => '#173177',
                                ],
                                'keyword1' => [
                                    'value' => $repair->address,
                                ],
                                'keyword2' => [
                                    'value' => $repair->content,
                                ],
                                'keyword3' => [
                                    'value' => date('Y-m-d H:i:s', $repair->created_at),
                                ],
                                'keyword4' => [
                                    'value' => $repair->statusText,
                                    'color' => '#173177',
                                ],
                                'keyword5' => [
                                    'value' => '已抄送管家/工程',
                                ],
                                'remark' => [
                                    'value' => '如有疑问，请联系项目管家',
                                ],
                            ]
                        ]
                    ];

                    //小程序配置
                    $miniSdk = new WxMiniProgram(['appId' => 'wx5062c4fc26247812', 'appSecret' => '61f365746ac4d41073319a604b243ca7']);
                    $miniSdk->sendUniformMessage($data);

                    return ['code' => 1, 'message' => 'success'];
                }

                return ['code' => 1, 'message' => 'success'];
            } else {
                return ['code' => 1, 'message' => '未找到对应记录'];
            }
        } else {
            return ['code' => 0, 'message' => '参数为空或状态为0'];
        }

    }

}