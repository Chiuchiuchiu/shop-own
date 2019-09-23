<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "security_per_labels".
 *
 * @property integer $id
 * @property integer $bea_id
 * @property integer $in_labels_id
 *
 * @property IndividualLabels $individualLabels
 */
class SecurityPerLabels extends \yii\db\ActiveRecord
{
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'updatedAtAttribute' => null
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'security_per_labels';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['bea_id', 'in_labels_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'bea_id' => 'Bea ID',
            'in_labels_id' => 'In Labels ID',
        ];
    }

    public function getIndividualLabels()
    {
        return $this->hasOne(IndividualLabels::className(), ['id' => 'in_labels_id']);
    }

}
