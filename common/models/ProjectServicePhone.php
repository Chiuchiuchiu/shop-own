<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "project_service_phone".
 *
 * @property integer $id
 * @property integer $house_id
 * @property integer $project_house_id
 * @property integer $category_id
 * @property string $name
 * @property string $telephone
 * @property string $address
 * @property integer $status
 * @property integer $created_at
 *
 * @property Project $projectInfo
 * @property ProjectServiceCategory $projectCategory
 */
class ProjectServicePhone extends \yii\db\ActiveRecord
{

    const STATUS_DELETE = 0;
    const STATUS_ACTIVE = 1;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'project_service_phone';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'updatedAtAttribute' => false,
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['project_house_id', 'telephone', 'address', 'name'], 'required'],
            [['project_house_id', 'status', 'created_at'], 'integer'],
        ];
    }

    public static function statusMap(){
        return [
            self::STATUS_DELETE => '禁用',
            self::STATUS_ACTIVE => '正常',
        ];
    }

    public function getStatusText(){
        return self::statusMap()[$this->status];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '#',
            'project_house_id' => '所属项目',
            'telephone' => '联系电话',
            'name' => '名称',
            'status' => '状态',
            'statusText' => '状态',
            'address' => '通讯地址',
            'categoryName' => '分类名',
            'category_id' => '分类名',
        ];
    }

    public function getProjectCategory()
    {
        return $this->hasOne(ProjectServiceCategory::className(), ['id' => 'category_id']);
    }

    public function getProjectInfo()
    {
        return $this->hasOne(Project::className(), ['house_id' => 'project_house_id']);
    }

}
