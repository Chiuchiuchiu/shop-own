<?php

namespace apps\www\module\prepayLottery\models;

use Yii;

/**
 * This is the model class for table "project_red_envelope".
 *
 * @property integer $id
 * @property integer $project_house_id
 * @property integer $stock
 * @property integer $property_id
 * @property integer $status
 */
class ProjectRedEnvelope extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'project_red_envelope';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('eventDb');
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['project_house_id'], 'required'],
            [['project_house_id', 'stock', 'property_id', 'status'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'project_house_id' => 'Project House ID',
            'stock' => 'Stock',
            'property_id' => 'Property ID',
            'status' => 'Status',
        ];
    }
}
