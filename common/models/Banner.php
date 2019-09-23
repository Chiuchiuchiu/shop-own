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
 * @property string $projects
 * @property integer $sort
 * @property string $type
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
class Banner extends \yii\db\ActiveRecord
{

    const STATUS_ACTIVE = 1;
    const STATUS_DRAFT = 2;
    const STATUS_DELETE = 0;

    const TYPE_1 = 1;   //商城首页
    const TYPE_2 = 2;   //商品详细页

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
        return 'banner';
    }


    public function rules()
    {
        return [
            [['title','pic', 'url', 'projects'], 'required'],
            [['status', 'created_at', 'updated_at', 'sort'], 'integer'],
            [['title'], 'string', 'max' => 100]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '编号',
            'title' => '标题',
            'pic' => '图',
            'url' => '跳转地址',
            'status' => '状态',
            'projects' => '参与项目',
            'projectText' => '参与项目',
            'sort' => '排序',
            'created_at' => '项目时间',
            'updated_at' => '最后编辑时间',
        ];
    }

    public static function typeMap()
    {
        return [
            self::TYPE_1 => ['name' => "商城首页", 'url' => "https://shop.51homemoney.com/Mobile/index/index/sid/%s"],
            self::TYPE_2 => ['name' => "商品详情页", 'url' => "https://shop.51homemoney.com/Mobile/Shop/detail/sid/%s/id/%s"],
        ];
    }

    public function getTypeText()
    {
        return self::typeMap()[$this->type]['name'];
    }

    public function getProject(){
        return $this->hasOne(Project::className(),['house_id'=>'project_id']);
    }
    public function getProjectName(){
        return $this->project?$this->project->house_name:'';
    }
}
