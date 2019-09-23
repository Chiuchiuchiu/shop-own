<?php
/**
 * Created by PhpStorm.
 * User: zhaowenxi
 * Date: 2018/10/29
 * Time: 10:06
 */

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "mini_wx_login_log".
 *
 * @property integer $id
 * @property string $params
 * @property string $result
 * @property string $ip
 * @property integer $created_at
 */
class MiniWxLoginLog extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'mini_wx_login_log';
    }

    /**
     * @return null|object|\yii\db\Connection
     * @throws \yii\base\InvalidConfigException
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
            [['id', 'created_at'], 'integer'],
            [['result','params'], 'string'],
            [['ip'], 'string', 'max' => 20],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'params' => '请求参数',
            'result' => '返回参数',
            'ip' => 'Ip',
            'created_at' => 'Created At',
        ];
    }

    public static function writesLog($params, $result, $ip='-')
    {
        $model = new self();
        $model->params = serialize($params);
        $model->result = serialize($result);
        $model->ip = $ip;

        return $model->save();
    }
}