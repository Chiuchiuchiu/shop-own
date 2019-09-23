<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "repair_response".
 *
 * @property integer $repair_id
 * @property string $services_id
 * @property string $response_data
 * @property string $code
 * @property string $error_msg
 * @property integer $business_id
 * @property int $service_state
 * @property integer $flow_id
 * @property string $level_name
 * @property integer $created_at
 */
class RepairResponse extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'repair_response';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['repair_id'], 'required'],
            [['repair_id', 'created_at', 'business_id', 'flow_id'], 'integer'],
            [['response_data'], 'string'],
            [['code'], 'string', 'max' => 10],
            [['error_msg', 'services_id', 'level_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'repair_id' => 'Repair ID',
            'code' => 'Code',
            'error_msg' => 'Error Msg',
            'response_data' => 'Response Data',
            'created_at' => 'Created At',
        ];
    }

    /**
     * @param integer $repairId
     * @param string $code
     * @param string $errorMsg
     * @param string $responseData
     * @param string $servicesId
     * @param int $businessId
     * @param int $flowID
     * @return bool
     */
    public static function log($repairId, $code, $errorMsg, $responseData, $servicesId='', $businessId=0, $flowID=0)
    {
        $model = self::findOrCreate($repairId);
        $model->code = $code;
        $model->error_msg = $errorMsg;
        $model->response_data = serialize($responseData);
        $model->services_id = $servicesId;
        $model->business_id = $businessId;
        $model->flow_id = $flowID;
        $model->created_at = time();

        return $model->save();
    }

    public static function findOrCreate($repairId)
    {
        $model = self::findOne(['repair_id' => $repairId]);
        if(!$model){
            $model = new self();
            $model->repair_id = $repairId;
        }
        return $model;
    }

}
