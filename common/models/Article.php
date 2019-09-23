<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "notice".
 *
 * @property integer $id
 * @property string $title
 * @property string $show_type
 * @property integer $project_id
 * @property integer $category_id
 * @property string $pic
 * @property string $author
 * @property integer $post_at
 * @property string $content
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 *
 *
 * @property ArticleCategory $category
 * @property string $categoryName
 * @property string $showTypeText
 *
 * @property string $summary
 * @property Project $project
 * @property string $projectName
 */
class Article extends \yii\db\ActiveRecord
{

    const STATUS_ACTIVE = 1;
    const STATUS_DRAFT = 2;
    const STATUS_DELETE = 0;

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className()
            ]
        ];
    }


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'article';
    }

    public static function statusMap()
    {
        return [
            self::STATUS_DRAFT => '草稿',
            self::STATUS_ACTIVE => '正常',
            self::SCENARIO_DEFAULT => '删除'
        ];
    }
    public static function showTypeMap(){
        return [
          1=>'图文列表',
          2=>'大图展示',
          3=>'无图',
        ];
    }

    public function getStatusText(){
        return self::statusMap()[$this->status];
    }

    public function getShowTypeText(){
        return self::showTypeMap()[$this->show_type];
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'content', 'pic', 'category_id','project_id','show_type'], 'required'],
            [['content'], 'string'],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            [['status', 'created_at', 'updated_at', 'post_at'], 'integer'],
            [['title'], 'string', 'max' => 100],
            [['author'], 'string', 'max' => 10],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => '标题',
            'pic' => '图',
            'author'=>'作者',
            'project_id'=>'项目方',
            'category_id' => '栏目',
            'categoryName' => '栏目',
            'show_type' => '展示方式',
            'showTypeText' => '展示方式',
            'content' => 'Content',
            'post_at' => '发布时间',
            'status' => '状态',
            'statusText' => '状态',
            'created_at' => '创建时间',
            'updated_at' => '最后编辑时间',
        ];
    }

    public function getCategory()
    {
        return $this->hasOne(ArticleCategory::className(), ['id' => 'category_id']);
    }

    public function getCategoryName()
    {
        return $this->category->name;
    }

    public function getSummary(){
        $len=30;
        $res = str_replace(['&nbsp;'],'',strip_tags($this->content));

        return mb_substr($res,0,$len) . (mb_strlen($res)>$len?'...':'');
    }
    public function getProject(){
        return $this->hasOne(Project::className(),['house_id'=>'project_id']);
    }
    public function getProjectName(){
        return $this->project?$this->project->house_name:'';
    }
}
