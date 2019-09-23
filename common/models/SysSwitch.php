<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "sys_switch".
 *
 * @property string $name
 * @property string $value
 * @property string $true_member_id
 * @property string $false_member_id
 */
class SysSwitch extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sys_switch';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'value', 'true_member_id', 'false_member_id'], 'required'],
            [['name'], 'string', 'max' => 50],
            [['true_member_id', 'false_member_id'], 'string', 'max' => 200],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => 'Name',
            'value' => 'Value',
            'true_member_id' => 'True Member ID',
            'false_member_id' => 'False Member ID',
        ];
    }

    public static function getValue($name)
    {
        $id = Yii::$app->user->id;
        $model = self::findOne(['name'=>$name]);
        if(!$model){
            return false;
        }
        if(!empty($model->true_member_id)){
            $model->true_member_id = explode(',',$model->true_member_id);
            if(in_array($id,$model->true_member_id))
                return true;
        }
        if(!empty($model->false_member_id)){
            $model->false_member_id = explode(',',$model->false_member_id);
            if(in_array($id,$model->false_member_id))
                return false;
        }

        return false;
    }

    public static function inValue($name, $value)
    {
        $model = self::findOne(['name'=>$name]);
        if(!$model){
            return false;
        }

        if(!empty($model->true_member_id)){
            $model->true_member_id = explode(',',$model->true_member_id);
            if(in_array($value,$model->true_member_id))
                return true;
        }

        return false;
    }

    public static function lxjProject($name, $projectId)
    {
        $model = self::findOne(['name'=>$name]);
        if(!$model){
            return false;
        }

        if(!empty($model->value)){
            $model->value = explode(',',$model->value);
            if(in_array($projectId, $model->value))
                return true;
        }

        return false;
    }

    public static function inVal($name, $value)
    {
        if(empty($value)){
            return false;
        }

        $model = self::findOne(['name'=>$name]);
        if(!$model){
            return false;
        }

        if(!empty($model->value)){
            $model->value = explode(',',$model->value);
            if(in_array($value, $model->value))
                return true;
        }

        return false;
    }

}
