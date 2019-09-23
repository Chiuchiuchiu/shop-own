<?php

namespace common\models;

use components\arEasyCache\ActiveRecordCacheTrait;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "house".
 *
 * @property integer $house_id
 * @property integer $parent_id
 * @property integer $project_house_id
 * @property string $house_name
 * @property string $ancestor_name
 * @property string $house_alias_name
 * @property integer $reskind
 * @property string $room_status
 * @property string $room_status_name
 * @property string $belong_floor
 * @property integer $level
 * @property integer $deepest_node
 * @property integer $show_status
 * @property integer $ordering
 * @property integer $updated_at
 *
 *
 * @property boolean $hasChild
 * @property ProjectHouseStructure $structure
 * @property string $showName
 * @property array $child
 * @property House $parent
 * @property array $showChild
 * @property project $project
 *
 * @property HouseExt $houseExt
 * @property HouseBillOutline $houseBillOutline
 */
class House extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'house';
    }

    public function behaviors()
    {
        return [
            [
                'class'=>TimestampBehavior::className(),
                'createdAtAttribute'=>null,
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['house_id', 'parent_id', 'project_house_id', 'house_name', 'ancestor_name', 'reskind', 'level', 'deepest_node', 'show_status'], 'required'],
            [['house_id', 'parent_id', 'reskind', 'level', 'deepest_node', 'show_status', 'updated_at', 'project_house_id', 'ordering'], 'integer'],
            [['house_name', 'ancestor_name', 'house_alias_name'], 'string', 'max' => 255],
            [['room_status'], 'string', 'max' => 20],
            [['belong_floor'], 'string', 'max' => 16],
            [['room_status_name'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'house_id' => '#',
            'parent_id' => 'Parent ID',
            'project_house_id' => '项目ID',
            'house_name' => '房产名称',
            'ancestor_name' => '完整房产名称',
            'house_alias_name' => '房子别名',
            'reskind' => 'Reskind',
            'room_status' => 'Room Status',
            'room_status_name' => 'Room Status Name',
            'belong_floor' => 'Belong Floor',
            'level' => 'Level',
            'deepest_node' => 'Deepest Node',
            'show_status' => '是否在前端显示',
            'showStatusText' => '是否在前端显示',
            'updated_at' => 'Updated At',
            'ordering' => '排序',
        ];
    }

    public function getHasChild()
    {
        return $this->hasOne(self::className(), ['parent_id' => 'house_id'])->count() > 0;
    }

    /**
     * @param $id
     * @return House
     * Description:
     */
    public static function findOrCreate($id)
    {
        $self = self::find()->where(['house_id' => $id])->one();
        if (!$self) {
            $self = new self();
            $self->house_id = $id;
        }
        return $self;
    }

    public function getChild()
    {
        return $this->hasMany(self::className(), ['parent_id' => 'house_id']);
    }

    /**
     * 显示所有需要显示的下级
     * @param null $child
     * @param array $res
     * @return array
     */
    public function getShowChild($child = null, &$res = [])
    {
        $child = is_null($child) ? $this->child : $child;
        foreach ($child as $row) {
            if ($row->show_status == 0){
                switch ($row->structure->type) {
                    case 1:
                        $res[] = $row;
                        break;
                    case 2:
                        $this->getShowChild($row->child, $res);
                        break;
                    case 3:
                        continue;
                }
            }
        }
        return $res;
    }

    public static function showStatusMap()
    {
        return [
            0 => '显示',
            1 => '隐藏'
        ];
    }

    public function getShowStatusText()
    {
        return self::showStatusMap()[$this->show_status];
    }

    public function getStructure()
    {
        return $this->hasOne(ProjectHouseStructure::className(), ['project_house_id' => 'project_house_id', 'reskind' => 'reskind']);
    }

    public function getShowName($part = '')
    {
        $obj = $this;
        $res[] = $obj->house_name;

        while ($row = $obj->parent) {

            if (isset($row->structure->type) && $row->structure->type == 1)
                if($row->parent_id==0)
                    $res[] = $row->project->house_name;
                else
                    $res[] = $row->house_name;
            $obj = $row;
        }
        $res = array_reverse($res);
        $str= implode($part, $res);
        return $str;
    }

    public function getParent()
    {
        return $this->hasOne(self::className(), ['house_id' => 'parent_id']);
    }

    public function getProject()
    {
        return $this->hasOne(Project::className(), ['house_id' => 'project_house_id']);
    }

    public static function getAllChildIds(House $house, &$condition = null, &$res)
    {
        if (!$condition instanceof \Closure || call_user_func($condition, $house)) {
            $res[] = $house->house_id;
        }
        if (sizeof($house->child)) {
            foreach ($house->child as $child)
                self::getAllChildIds($child, $condition, $res);
        }
    }

    public function parentHas($ids)
    {
        $obj = $this;
        while ($row = $obj->parent) {
            if (in_array($row->house_id, $ids))
                return true;
            $obj = $row;
        }
        return false;
    }
    public function getHouseExt(){
        return $this->hasOne(HouseExt::className(),['house_id'=>'house_id']);
    }

    public function getHouseBillOutline(){
        return $this->hasOne(HouseBillOutline::className(),['house_id'=>'house_id']);
    }

    public function getMemberHouse(){
        return $this->hasOne(MemberHouse::className(), ['house_id' => 'house_id']);
    }
}
