<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "activity".
 *
 * @property integer $id
 * @property string $uid
 * @property string $title
 * @property string $pic
 * @property string $bg_color
 * @property string $btn_color
 * @property string $btn_text
 * @property string $comment_tag
 * @property string $site
 * @property integer $project_id
 * @property integer $status
 * @property integer $click_numbers
 * @property integer $auth_numbers
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $options1
 * @property string $options2
 */
class Activity extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'activity';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'bg_color', 'btn_color', 'btn_text'], 'required'],
            [['comment_tag', 'site', 'options1', 'options2'], 'string'],
            [['project_id', 'status', 'click_numbers', 'auth_numbers', 'created_at', 'updated_at'], 'integer'],
            [['uid'], 'string', 'max' => 250],
            [['title', 'pic', 'bg_color', 'btn_color', 'btn_text'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels(){
        return [
            'id' => '编号',
            'title' => '标题',
            'pic' => '图',
            'bg_color'=>'背景颜色',
            'btn_color'=>'按钮颜色',
            'btn_text'=>'按钮文字',
            'comment_tag'=>'评论标签',
            'options1'=>'选项1',
            'options2'=>'选项2',
            'project_id'=>'项目方',
            'click_numbers' => '项目点击数',
            'auth_numbers' => '被转移认证数',
            'site' => '活动简介',
            'status' => '状态',
            'created_at' => '项目时间',
            'updated_at' => '最后编辑时间',
        ];
    }
    public function getProject(){
        return $this->hasOne(Project::className(),['house_id'=>'project_id']);
    }
    public function getProjectName(){
        return $this->project?$this->project->house_name:'';
    }
}
