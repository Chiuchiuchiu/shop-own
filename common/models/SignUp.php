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
 * @property string $options1
 * @property string $options2
 */
class SignUp extends \yii\db\ActiveRecord
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
        return 'activity_signup';
    }


    public function rules()
    {
        return [
            [['surname','telephone', 'uid','project_id','member_id'], 'required'],
            [['site'], 'string'],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            [['status', 'created_at', 'updated_at', 'member_id','activity_id'], 'integer']
        ];
    }
    public static function count($id){
        $Count = self::find()->where(['activity_id'=>$id])->count();
        return $Count;
    }
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'uid' => '流水号',
            'surname' => '真实姓名',
            'telephone' => '联系电话',
            'site'=>'详细信息',
            'project_id'=>'项目方',
            'member_id' => '会员',
            'ancestor_name' => '所在房产',
            'comment' => '评论内容',
            'options1'=>'选项1',
            'options2'=>'选项2',
            'star_number' => '星级',
            'status' => '状态',
            'created_at' => '报名时间',
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
