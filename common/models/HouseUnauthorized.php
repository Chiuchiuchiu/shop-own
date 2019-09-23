<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "house_unauthorized".
 *
 * @property integer $house_id
 * @property integer $parent_id
 * @property integer $project_house_id
 * @property string $house_name
 * @property string $ancestor_name
 * @property integer $reskind
 * @property integer $level
 * @property string $room_status
 * @property integer $deepest_node
 * @property integer $created_at
 */
class HouseUnauthorized extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'house_unauthorized';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['house_id', 'parent_id', 'project_house_id', 'house_name', 'ancestor_name', 'reskind', 'level', 'created_at'], 'required'],
            [['house_id', 'parent_id', 'project_house_id', 'reskind', 'level', 'deepest_node', 'created_at'], 'integer'],
            [['house_name', 'ancestor_name'], 'string', 'max' => 255],
            [['room_status'], 'string', 'max' => 20],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'house_id' => 'House ID',
            'parent_id' => 'Parent ID',
            'project_house_id' => 'Project House ID',
            'house_name' => 'House Name',
            'ancestor_name' => 'Ancestor Name',
            'reskind' => 'Reskind',
            'level' => 'Level',
            'room_status' => 'Room Status',
            'deepest_node' => 'Deepest Node',
            'created_at' => 'Created At',
        ];
    }
}
