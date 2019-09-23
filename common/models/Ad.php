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
 * @property integer $template_id
 * @property string $diy_json
 * @property string $url
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
class Ad extends \yii\db\ActiveRecord
{

    const STATUS_ACTIVE = 1;
    const STATUS_DRAFT = 2;
    const STATUS_DELETE = 0;

    const TYPE_1 = 1;   //欢迎页
    const TYPE_2 = 2;   //业主中心广告

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
        return 'ad';
    }


    public function rules()
    {
        return [
         //   [['title','pic', 'url', 'projects','type','start_time','end_time'], 'required'],
         [['title', 'projects','type' ], 'required'],
            [['status', 'created_at', 'updated_at', 'sort','type','start_time','end_time'], 'integer'],
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
            'type' => '广告类型',
            'template_id' => '广告模板ID',
            'start_time' => '广告开始时间',
            'end_time' => '广告结束时间',
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
    public function getUrlType($type)
    {
        $_url = $this->url;

        if($type == 2){
            $tip = "广告DIY";
        }else{
            if(strpos($_url,'/id/') >0){
                $tip = "商品详情页";
            }else{
                $tip = "商城首页";
            }
        }

        return $tip;
    }
    public function getTypeText()
    {
        $_types = array("其他","【欢迎页面】广告位","【业主中心】广告位","【商城聚焦】广告位","【物业缴费】广告位","【报事报修】广告位");
        return  $_types[$this->type];
    }

    public function getProject(){
        return $this->hasOne(Project::className(),['house_id'=>'project_id']);
    }
    public function getProjectName(){
        return $this->project?$this->project->house_name:'';
    }
}
