<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "operation_log".
 *
 * @property integer $id
 * @property integer $member_id
 * @property string $path_info
 * @property string $ip
 * @property string $referrer
 * @property integer $created_at
 * @property integer $project_id
 * @property Project $project
 *
 * @property Member $member
 */
class OperationLog extends \yii\db\ActiveRecord
{
    public $totalCount;
    public $memberCount;

    public static $table = '';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return !empty(self::$table) ? self::$table : 'operation_log_' . date('Ym');
    }

    /**
     * @return object|\yii\db\Connection|null
     * @throws \yii\base\InvalidConfigException
     * @author zhaowenxi
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
            [['member_id', 'created_at', 'project_id'], 'integer'],
            [['path_info'], 'string', 'max' => 200],
            [['referrer'], 'string', 'max' => 100],
            [['ip'], 'string', 'max' => 15],
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
            'path_info' => '访问路径',
            'referrer' => '上一页',
            'ip' => 'Ip',
            'created_at' => 'Created At',
            'project_id' => '项目id',
            'totalCount' => '点击总数',
            'memberCount' => '点击人数',
        ];
    }

    public static function writeLog($userObj, $projectId)
    {
        $model            = new self();
        $model->path_info = Yii::$app->request->getHostInfo().Yii::$app->request->url;
        $model->ip        = Yii::$app->request->getUserIP() ?? '-';
        $model->referrer  = Yii::$app->request->getReferrer() ?? '-';
        isset($userObj->id) && $model->member_id = $userObj->id;
        (isset($projectId) && $projectId) && $model->project_id = $projectId;

        $model->save();

        return $model->getErrors();
    }

    public function getMember()
    {
        return $this->hasOne(Member::className(), ['id' => 'member_id']);
    }

    public function getProject()
    {
        return $this->hasOne(Project::className(), ['house_id' => 'project_id']);
    }
}
