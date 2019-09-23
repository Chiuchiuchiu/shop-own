<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "butler_election_activity".
 *
 * @property integer $id
 * @property int $status
 * @property string $statusText
 * @property integer $group
 * @property string $groupText
 * @property integer $project_house_id
 * @property integer $butler_id
 * @property string $name
 * @property string $phone
 * @property string $head_img
 * @property string $introduce
 * @property integer $number
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property Project $project
 * @property Butler $butler
 * @property RegionHouse $regionHouse
 */
class ButlerElectionActivity extends \yii\db\ActiveRecord
{
    const STATUS_DISABLE = 0;
    const STATUS_ACTIVATE = 1;
    const GROUP_BUTLER = 1;
    const GROUP_SECURITY = 2;

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'butler_election_activity';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status', 'group', 'project_house_id', 'butler_id', 'number', 'created_at', 'updated_at'], 'integer'],
            [['group', 'project_house_id', 'name', 'phone', 'head_img', 'introduce'], 'required'],
            [['name', 'phone'], 'string', 'max' => 20],
            [['head_img'], 'string', 'max' => 255],
            [['introduce'], 'string', 'max' => 500],
            [['name', 'phone', 'introduce'], 'filter', 'filter' => 'trim'],
        ];
    }

    public function getGroupText()
    {
        return self::groupMap()[$this->group];
    }

    public static function groupMap()
    {
        return [
            self::GROUP_BUTLER => '管家',
            self::GROUP_SECURITY => '保安',
        ];
    }

    public function getStatusText()
    {
        return self::statusMap()[$this->status];
    }

    public static function statusMap()
    {
        return [
            self::STATUS_DISABLE => '禁用',
            self::STATUS_ACTIVATE => '正常',
        ];
    }

    public static function statusMapLists()
    {
        return [
            '' => '全部',
            self::STATUS_DISABLE => '禁用',
            self::STATUS_ACTIVATE => '正常',
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '#',
            'status' => '状态',
            'statusText' => '状态',
            'group' => 'Group',
            'groupText' => '所在组',
            'project_house_id' => 'Project House ID',
            'butler_id' => 'Butler ID',
            'name' => '姓名',
            'phone' => '手机号',
            'head_img' => '头像',
            'introduce' => '个人介绍',
            'number' => '投票数',
            'created_at' => '报名时间',
            'updated_at' => 'Updated At',
        ];
    }

    public static function findOrCreate($butlerId)
    {
        $model = self::findOne(['butler_id' => $butlerId]);
        if(!$model){
            $model = new self();
            $model->butler_id = $butlerId;
        }

        return $model;
    }

    public function getProject()
    {
        return $this->hasOne(Project::className(), ['house_id' => 'project_house_id']);
    }

    public function getButler()
    {
        return $this->hasOne(Butler::className(), ['id' => 'butler_id']);
    }

    public function getRegionHouse()
    {
        return (isset($this->butler->regions) && $this->butler->regions) ? ArrayHelper::getColumn(House::find()->where(['house_id' => explode(',', $this->butler->regions)])->all(), 'house_name') : [];
    }

    public function getVoteCount($timeArr){
        $count = MemberVote::find()->where(['bsa_id' => $this->id])
            ->andFilterWhere(['BETWEEN', 'vote_time', $timeArr[0], $timeArr[1]])
            ->count();

        return $count;
    }
}
