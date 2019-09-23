<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "repair_customer_evaluation".
 *
 * @property integer $id
 * @property integer $repair_id
 * @property integer $satisfaction
 * @property string $satisfactionText
 * @property integer $timeliness
 * @property string $timelinessText
 * @property string $customer_idea
 * @property integer $created_at
 */
class RepairCustomerEvaluation extends \yii\db\ActiveRecord
{

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
        return 'repair_customer_evaluation';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['repair_id', 'satisfaction', 'timeliness'], 'required'],
            [['repair_id', 'satisfaction', 'timeliness', 'created_at'], 'integer'],
            [['customer_idea'], 'filter', 'filter' => 'trim'],
            [['customer_idea'], 'string', 'max' => 255],
        ];
    }

    public function getSatisfactionText()
    {
        return self::satisfactionMap()[$this->satisfaction];
    }

    public static function satisfactionMap()
    {
        return [
            1 => '1星',
            2 => '2星',
            3 => '3星',
            4 => '4星',
            5 => '5星',
        ];
    }

    public function getTimelinessText()
    {
        return self::timelinessMap()[$this->timeliness];
    }

    public static function timelinessMap()
    {
        return [
            1 => '及时',
            2 => '一般',
            3 => '不及时',
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'repair_id' => 'Repair ID',
            'satisfaction' => 'Satisfaction',
            'timeliness' => 'Timeliness',
            'customer_idea' => 'Customer Idea',
            'created_at' => 'Created At',
        ];
    }

    public static function findOrCreate($repairId)
    {
        $self = self::find()->where(['repair_id' => $repairId])->one();
        if (!$self) {
            $self = new self();
            $self->repair_id = $repairId;
        }
        return $self;
    }

}
