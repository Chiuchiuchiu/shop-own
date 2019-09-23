<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "parking_log".
 *
 * @property integer $id
 * @property integer $member_id
 * @property string $post_data
 * @property string $error_msg
 * @property string $error_code
 * @property string $plateno
 * @property integer $created_at
 */
class ParkingLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'parking_log';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('logDb');
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'updatedAtAttribute'=>null,
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['member_id', 'created_at'], 'integer'],
            [['post_data'], 'string'],
            [['error_msg'], 'string', 'max' => 255],
            [['error_code'], 'string', 'max' => 32],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'member_id' => 'Member ID',
            'post_data' => 'Post Data',
            'error_msg' => 'Error Msg',
            'error_code' => 'Error Code',
            'created_at' => 'Created At',
        ];
    }

    /**
     * @param integer $memberId
     * @param array|string $postData
     * @param string $errorMsg
     * @param string $errorCode
     * @param string $platenoI
     * @return bool
     */
    public static function writeLog($memberId, $postData, $errorMsg, $errorCode, $platenoI='')
    {
        //车牌号
        $plateno = '';
        if(isset($postData['plateno'])){
            $plateno = $postData['plateno'];
        }

        if(isset($postData['Data']['plateno'])){
            $plateno = $postData['Data']['plateno'];
        } elseif (!empty($platenoI)) {
            $plateno = $platenoI;
        }

        $model = new self();
        $model->member_id = $memberId;
        $model->post_data = serialize($postData);
        $model->error_msg = $errorMsg;
        $model->error_code = $errorCode;
        $model->plateno = $plateno;

        return $model->save();
    }

}
