<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "new_repair_log".
 *
 * @property integer $id
 * @property integer $butler_id
 * @property string $post_data
 * @property string $result
 * @property string $ip
 * @property integer $created_at
 */
class NewRepairLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'new_repair_log';
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
                'updatedAtAttribute' => null
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['butler_id', 'created_at'], 'integer'],
            [['post_data', 'result'], 'string'],
            [['ip'], 'string', 'max' => 32],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'butler_id' => 'Butler ID',
            'post_data' => 'Post Data',
            'result' => 'Result',
            'ip' => 'Ip',
            'created_at' => 'Created At',
        ];
    }

    public static function writesLog($postData, $result, $ip='-', $butlerId=0)
    {
        $model = new self();
        $model->post_data = serialize($postData);
        $model->result = serialize($result);
        $model->ip = $ip;
        $model->butler_id = $butlerId;

        return $model->save();
    }

}
