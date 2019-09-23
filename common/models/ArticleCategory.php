<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "notice_category".
 *
 * @property integer $id
 * @property integer $project_id
 * @property string $name
 * @property integer $status
 */
class ArticleCategory extends \yii\db\ActiveRecord
{

    const STATUS_ACTIVE = 1;
    const STATUS_DELETE = 0;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'article_category';
    }

    public static function statusMap()
    {
        return [
            self::STATUS_ACTIVE => '正常',
            self::SCENARIO_DEFAULT => '删除'
        ];
    }
    public function getStatusText(){
        return self::statusMap()[$this->status];
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name','project_id'], 'required'],
            [['project_id','status'],'integer'],
            ['status','default','value'=>self::STATUS_ACTIVE],
            [['name'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'project_id'=>'项目id',
            'name' => '分类名',
            'status'=>'状态',
            'statusText'=>'状态'
        ];
    }
}
