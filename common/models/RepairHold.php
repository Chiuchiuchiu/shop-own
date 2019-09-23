<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "repair_hold".
 *
 * @property integer $id
 * @property integer $repair_id
 * @property integer $butler_id
 * @property integer $project_id
 * @property string $content
 * @property integer $status
 * @property integer $updated_at
 * @property integer $created_at
 *
 * @property Repair $repair
 */
class RepairHold extends \yii\db\ActiveRecord
{

    const STATUS_WAIT = 1;
    const STATUS_YES = 2;
    const STATUS_NO = 3;
    const STATUS_OPEN = 4;

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'updatedAtAttribute'=>null,
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'repair_hold';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['repair_id', 'butler_id'], 'required'],
            [['repair_id', 'created_at','status','butler_id'], 'integer'],
            ['status', 'default', 'value' => 1],
            [['content'], 'filter', 'filter' => 'trim'],
            [['content'], 'string', 'max' => 255],
        ];
    }

    public function getStatusText()
    {
        return self::statusMap()[$this->status];
    }

    public static function statusMap()
    {
        return [
            '' => '全部',
            self::STATUS_WAIT => '审批中',
            self::STATUS_YES => '审核通过',
            self::STATUS_NO => '审核不通过',
//            self::STATUS_OPEN => '重新启动报事报修',
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'content' => '暂挂内容',
            'statusText' => '暂挂审批状态',
            'created_at' => '创建时间',
            'updated_at' => '审批时间',
        ];
    }

    public static function createOrUpdate($repairId)
    {
        $model = self::findOne(['repair_id' => $repairId]);
        if(!$model){
            $model = new self();
        }

        return $model;
    }

    public function getRepair()
    {
        return $this->hasOne(Repair::className(), ['id' => 'repair_id']);
    }
}
