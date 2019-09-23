<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "project_house_structure".
 *
 * @property integer $id
 * @property integer $project_house_id
 * @property integer $parent_reskind
 * @property string $name
 * @property integer $reskind
 * @property integer $type
 * @property integer $level
 * @property integer $group 1:住宅；2:车位
 * @property integer $ordering
 *
 * @property Project $project
 * @property string $getGroupText
 * @property string $typeText
 */
class ProjectHouseStructure extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'project_house_structure';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['project_house_id', 'name', 'reskind', 'type', 'parent_reskind'], 'required'],
            [['project_house_id', 'reskind', 'type','ordering','group'], 'integer'],
            ['ordering', 'integer', 'max' => 999],
            [['name'], 'string', 'max' => 4],
        ];
    }

    public static function groupMap()
    {
        return [
            0 => '无',
            1 => '住宅',
            2 => '车位'
        ];
    }

    public function getGroupText()
    {
        return self::groupMap()[$this->group];
    }

    public static function typeMap()
    {
        return [
            0 => '隐藏（0）',
            1 => '正常显示（1）',
            2 => '整合（2）'
        ];
    }

    public function getTypeText()
    {
        return self::typeMap()[$this->type];
    }

    public static function reskindMap()
    {
        return [
            0 => '0(未知)',
            1 => '1(小区)',
            2 => '2(组团)',
            3 => '3(大楼)',
            4 => '4(单元)',
            5 => '5(房间)',
            6 => '6(别墅)',
            7 => '7(排屋)',
            8 => '8(储藏室)',
            9 => '9(车位)',
            10 => '10(停车场)',
            11 => '11(车区)',
            13 => '13(自行车位)',
            14 => '14(广告位)',
            15 => '15(卫星收视)',
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'project_house_id' => '楼盘ID',
            'parent_reskind' => '父级 Reskind',
            'name' => '名称',
            'reskind' => 'Reskind',
            'type' => '类型',
            'typeText' => '类型',
            'level' => 'Level',
            'group' => '所在组',
            'groupText' => '所在组',
            'ordering' => '排序',
        ];
    }

    public function getProject()
    {
        return $this->hasOne(Project::className(), ['house_id' => 'project_house_id']);
    }
}
