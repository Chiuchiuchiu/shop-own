<?php
/**
 * Created by PhpStorm.
 * User: dtfeng
 * Date: 2019/4/29
 * Time: 14:51
 */

namespace common\models;

/**
 * This is the model class for table "third_party_view_history".
 *
 * @property integer $id
 * @property integer $type
 * @property integer $model
 * @property integer $project_id
 * @property integer $house_id
 * @property integer $member_id
 * @property string  $member_nickname
 * @property string  $remark
 * @property string  $describtions
 * @property integer $click_place
 * @property string  $pic
 * @property integer $status
 * @property integer $created_at
 * @property project $project
 *
 */
class ThirdpartyViewHistory extends \yii\db\ActiveRecord
{
    const STATUS_UN_FAVORITES = 0;
    const STATUS_FAVORITES = 1;

    const CLICK_BANNER = 1;
    const CLICK_ICON = 2;
    const CLICK_DIY = 3;
    const CLICK_Goods = 4;

    public $picGroupBy;
    public $projectCount;
    public $memberCount;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'third_party_view_history';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['project_id', 'house_id', 'member_id', 'type','model', 'status', 'click_place'], 'integer'],
            [['describtions', 'remark', 'member_nickname'], 'string'],
            [['created_at'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'picGroupBy' => '点击次数',
            'projectCount' => '点击次数',
            'memberCount' => '点击人数',
        ];
    }

    public function getProject()
    {
        return $this->hasOne(Project::className(), ['house_id' => 'project_id']);
    }
}