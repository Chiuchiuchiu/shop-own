<?php

namespace apps\admin\models;

use Yii;

/**
 * This is the model class for table "question".
 *
 * @property integer $id
 * @property string $title
 * @property string $site
 * @property integer $type_id
 * @property integer $status
 * @property string $content
 * @property integer $type_isp
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 */
class Question extends \yii\db\ActiveRecord
{
    const STATUS_UNABLE = 2;
    const STATUS_ACTIVE = 1;
    const TYPE_PUBLIC = 1;
    const TYPE_HOUSE = 2;
    const TYPE_OFFICE = 3;
    const TYPE_DEVELOP = 4;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'question';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title'], 'required'],
            [['site', 'content'], 'string'],
            [['type_isp','type_id','status'], 'integer'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['title'], 'string', 'max' => 255],
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
            'site' => '短标签',
            'content' => 'Content',
            'typeText' => '适用范围',
            'type_id' => '适用范围',
            'statusText' => '状态',
            'created_at' => '添加时间',
            'updated_at' => 'Updated At',
            'deleted_at' => 'Deleted At',
        ];
    }

    public static function statusMap(){
        return [
            self::STATUS_UNABLE => '隐藏',
            self::STATUS_ACTIVE => '正常',
        ];
    }

    public function getStatusText(){
        return self::statusMap()[$this->status];
    }

    public static function typeMap()
    {
        return [
            1 => '公共',
            self::TYPE_HOUSE => '住宅',
            self::TYPE_OFFICE => '写字楼',
            self::TYPE_DEVELOP => '开发商、居委会、业主委员会',
        ];
    }

    public function getTypeText()
    {
        return self::typeMap()[$this->type_id];
    }
}
