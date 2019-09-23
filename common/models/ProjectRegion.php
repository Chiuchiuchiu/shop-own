<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "project_region".
 *
 * @property integer $id
 * @property integer $status
 * @property string $name
 * @property integer $created_at
 */
class ProjectRegion extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'project_region';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status', 'created_at'], 'integer'],
            [['name'], 'string', 'max' => 32],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'status' => 'Status',
            'name' => 'Name',
            'created_at' => 'Created At',
        ];
    }
    public static function SonProject($id){
        $List =Project::find()->where(['project_region_id'=>$id])->orderBy('house_id','desc')->select(['house_id','house_name'])->all();
        return $List;
    }
}
