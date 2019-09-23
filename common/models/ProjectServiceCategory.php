<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "project_service_category".
 *
 * @property integer $id
 * @property integer $project_house_id
 * @property string $name
 * @property integer $status
 */
class ProjectServiceCategory extends \yii\db\ActiveRecord
{

    const STATUS_DELETE = 0;
    const STATUS_ACTIVE = 1;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'project_service_category';
    }


//    public function behaviors()
//    {
//        return [
//            [
//                'class' => TimestampBehavior::className(),
//            ]
//        ];
//    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['project_house_id', 'name'], 'required'],
            [['project_house_id', 'status'], 'integer'],
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
            'project_house_id' => '项目ID',
            'name' => '名字',
            'status' => '状态',
            'statusText' => '状态',
        ];
    }

    public function getProjectInfo()
    {
        return $this->hasOne(Project::className(), ['house_id' => 'project_house_id']);
    }
}
