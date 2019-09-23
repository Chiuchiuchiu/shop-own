<?php

namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "butler".
 *
 * @property integer $id
 * @property integer $group
 * @property string $groupText
 * @property integer $project_house_id
 * @property string $wechat_open_id
 * @property string $wechat_user_id
 * @property string $nickname
 * @property string $headimg
 * @property string $regions
 * @property integer $mana_number
 * @property integer $status
 * @property integer $created_at
 *
 *
 * @property array $region
 * @property array $regionData
 * @property Project $project
 * @property ButlerAuth $butlerAuth
 * @property string $projectName
 * @property string $regionHouseName
 */
class Butler extends \yii\db\ActiveRecord implements IdentityInterface
{
    const STATUS_ENABLE = 1;
    const STATUS_DISABLE = 0;
    const STATUS_TEST = 2;
    const GROUP_1 = 1;
    const GROUP_2 = 2;
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
        return 'butler';
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


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['wechat_user_id', 'nickname'], 'required'],
            [['group', 'status', 'created_at'], 'integer'],
            [['wechat_open_id', 'nickname'], 'string', 'max' => 50],
            [['headimg', 'regions'], 'string', 'max' => 255],
            ['status', 'default', 'value' => 1],
            ['regions', 'default', 'value' => ''],
            [['wechat_open_id'], 'unique'],
        ];
    }

    public static function statusMap()
    {
        return [
            1 => '正常',
            0 => '作废',
            2 => '测试',
        ];
    }

    public function getStatusText()
    {
        return self::statusMap()[$this->status];
    }

    public static function groupMap()
    {
        return [
            1 => '管家',
            2 => '保安',
            self::GROUP_F => '财务',
            self::GROUP_OF => '项目办公室',
            self::GROUP_PO => '行政人事',
            self::GROUP_OR => '秩序维护部',
            self::GROUP_E => '工程',
        ];
    }

    public function getGroupText()
    {
        return self::groupMap()[$this->group];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '#',
            'group' => '分组',
            'groupText' => '分组',
            'wechat_open_id' => 'Wechat Open ID',
            'nickname' => '昵称',
            'headimg' => '头像',
            'status' => '状态',
            'statusText' => '状态',
            'created_at' => '加入时间',
            'mana_number' => '管理户数'
        ];
    }

    public static function findIdentity($id)
    {
        return self::findOne($id);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAuthKey()
    {
        throw new NotSupportedException();

    }

    public function validateAuthKey($authKey)
    {
        throw new NotSupportedException();
    }

    public function getRegion()
    {
        return $this->hasMany(ButlerRegion::className(), ['butler_id' => 'id']);
    }

    public function getRegionData()
    {
        return $this->regions ? House::find()->where(['house_id' => explode(',', $this->regions)])->all() : [];
    }

    public function getProject()
    {
        return $this->hasOne(Project::className(), ['house_id' => 'project_house_id']);
    }

    public function getProjectName()
    {
        return $this->project instanceof Project ? $this->project->house_name : 'undefined';
    }

    public function getButlerAuth()
    {
        return $this->hasOne(ButlerAuth::className(), ['used_to' => 'id']);
    }

    public function getRegionHouseName()
    {
        $houseName = '';
        if(!empty($this->regions)){
            foreach($this->regionData as $row){
                /**
                 * @var \common\models\House $row
                 */
                $houseName .= '，' . $row->house_name;
            }
        }

        return $houseName;
    }

}
