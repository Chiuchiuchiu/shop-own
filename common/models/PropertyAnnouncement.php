<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "property_announcement".
 *
 * @property integer $id
 * @property string $title
 * @property integer $project_house_id
 * @property string $pic
 * @property string $author
 * @property string $content
 * @property integer $status
 * @property string $statusText
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property string $summary
 * @property Project $project
 * @property string $projectName
 */
class PropertyAnnouncement extends \yii\db\ActiveRecord
{
    const STATUS_ACTIVE = 1;
    const STATUS_DRAFT = 2;
    const STATUS_DELETE = 0;


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'property_announcement';
    }

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
    public function rules()
    {
        return [
            [['title', 'project_house_id', 'pic', 'content', 'status'], 'required'],
            [['status', 'created_at', 'updated_at'], 'integer'],
            [['content'], 'string'],
            [['title'], 'string', 'max' => 100],
            [['pic'], 'string', 'max' => 50],
            [['author'], 'string', 'max' => 10],
        ];
    }

    public static function statusMap()
    {
        return [
            self::STATUS_DRAFT => '草稿',
            self::STATUS_ACTIVE => '正常',
            self::SCENARIO_DEFAULT => '删除'
        ];
    }

    public function getStatusText(){
        return self::statusMap()[$this->status];
    }

    public function getSummary()
    {
        $len=30;
        $res = str_replace(['&nbsp;'],'',strip_tags($this->content));

        return mb_substr($res,0,$len) . (mb_strlen($res)>$len ? '...' : '');
    }

    public function getProjectName(){
        return $this->project?$this->project->house_name:'';
    }

    public function getProject(){
        return $this->hasOne(Project::className(),['house_id'=>'project_house_id']);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '#',
            'title' => '标题',
            'project_house_id' => 'Project ID',
            'pic' => '缩略图',
            'author' => '作者',
            'content' => '内容',
            'status' => '状态',
            'statusText' => '状态',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }
}
