<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "house_relevance".
 *
 * @property integer $id
 * @property integer $house_id
 * @property integer $project_id
 * @property integer $area_id
 * @property integer $with_house_id
 * @property integer $parent_ids
 * @property integer $created_at
 *
 * @property array $houseName
 */
class HouseRelevance extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'house_relevance';
    }

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
    public function rules()
    {
        return [
            [['house_id', 'with_house_id', 'created_at', 'project_id', 'area_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'house_id' => 'House ID',
            'project_id' => 'Project ID',
            'area_id' => 'Area ID',
            'with_house_id' => 'With House ID',
            'parent_ids' => 'Parent Ids',
            'created_at' => 'Created At',
        ];
    }

    /**
     * @param $id
     * @return HouseRelevance
     * Description:
     */
    public static function findOrCreate($id)
    {
        $self = self::find()->where(['house_id' => $id])->one();
        if (!$self) {
            $self = new self();
        }
        return $self;
    }

    public function getHouseName()
    {
        return $this->with_house_id ? House::find()->select(['ancestor_name', 'house_id'])->where(['house_id' => $this->with_house_id])->one() : null;
    }
}
