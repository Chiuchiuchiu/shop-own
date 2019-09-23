<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "member_phone_auth_log".
 *
 * @property integer $id
 * @property string $post_data
 * @property string $result
 * @property string $ip
 * @property integer $created_at
 */
class MemberPhoneAuthLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'member_phone_auth_log';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('logDb');
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['post_data', 'result'], 'string'],
            [['created_at'], 'integer'],
            [['ip'], 'string', 'max' => 32],
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'updatedAtAttribute' => null
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'post_data' => 'Post Data',
            'result' => 'Result',
            'ip' => 'Ip',
            'created_at' => 'Created At',
        ];
    }


    public static function writesLog($postData, $result, $ip='-')
    {
        $model = new self();
        $model->post_data = serialize($postData);
        $model->result = serialize($result);
        $model->ip = $ip;

        return $model->save();
    }

}
