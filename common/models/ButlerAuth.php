<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "butler_auth".
 *
 * @property integer $id
 * @property integer $project_house_id
 * @property string $account
 * @property string $password
 * @property integer $status
 * @property integer $group
 * @property string $groupText
 * @property string $region
 * @property integer $created_at
 * @property integer $used_at
 * @property integer $used_to
 * @property string $statusText
 * @property array $regionData
 * @property Project $project
 * @property string $projectName
 *
 * @property Butler $butler
 */
class ButlerAuth extends \yii\db\ActiveRecord
{
    const DEFINE_ID = 1000;
    const STATUS_DEFINE = 1;
    const STATUS_USED = 2;
    const GROUP_B = 1;
    const GROUP_S = 2;
    const GROUP_F = 3;
    const GROUP_OF = 4;
    const GROUP_PO = 5;
    const GROUP_OR = 6;
    const GROUP_E = 7;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'butler_auth';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'updatedAtAttribute' => null,
            ]
        ];
    }

    public static function statusMap()
    {
        return [
            self::STATUS_DEFINE => '未使用',
            self::STATUS_USED => '已使用'
        ];
    }

    public function getStatusText()
    {
        return self::statusMap()[$this->status];
    }

    public static function groupMap()
    {
        return [
            self::GROUP_B => '管家',
            self::GROUP_S => '保安',
            self::GROUP_F => '财务',
            self::GROUP_OF => '项目办公室',
            self::GROUP_PO => '行政人事',
            self::GROUP_OR => '秩序维护部',
            self::GROUP_E => '工程',
        ];
    }

    public function getGroupText()
    {
        $text = '未分配';
        if(isset(self::groupMap()[$this->group])){
            $text = self::groupMap()[$this->group];
        }

        return $text;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['account', 'password','project_house_id'], 'required'],
            [['account', 'status', 'group', 'created_at', 'used_at', 'used_to'], 'integer'],
            ['used_at', 'default', 'value' => 0],
            ['used_to', 'default', 'value' => 0],
            ['status', 'default', 'value' => 1],
            ['region', 'string'],
            [['account'], 'unique'],
            ['account', 'match', 'pattern'=>'/^1[3-9]\d{9}$/'],
            [['password'], 'string', 'max' => 32],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'account' => '手机号码',
            'password' => '密码',
            'status' => 'Status',
            'group' => '分组',
            'groupText' => '分组',
            'statusText' => '状态',
            'created_at' => '创建时间',
            'used_at' => '使用时间',
            'used_to' => '',
        ];
    }

    public function getRegionData()
    {
        return $this->region ? House::find()->where(['house_id' => explode(',', $this->region)])->all() : [];
    }
    public function getProject(){
        return $this->hasOne(Project::className(),['house_id'=>'project_house_id']);
    }
    public function getProjectName(){
        return $this->project instanceof Project?$this->project->house_name:'undefined';
    }

    public function getButler()
    {
        return $this->hasOne(Butler::className(), ['id' => 'used_to']);
    }

}