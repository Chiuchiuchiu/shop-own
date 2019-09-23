<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "butler_labels".
 *
 * @property integer $id
 * @property integer $butler_id
 * @property integer $in_labels_id
 *
 * @property IndividualLabels $individualLabels
 */
class ButlerLabels extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'butler_labels';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['butler_id', 'in_labels_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'butler_id' => 'Butler ID',
            'in_labels_id' => 'In Labels ID',
        ];
    }

    public function getIndividualLabels()
    {
        return $this->hasOne(IndividualLabels::className(), ['id' => 'in_labels_id']);
    }

}
