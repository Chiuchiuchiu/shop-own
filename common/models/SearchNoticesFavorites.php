<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/11
 * Time: 14:51
 */

namespace common\models;

/**
 * This is the model class for table "search_notices_favorites".
 *
 * @property integer $id
 * @property integer search_id
 * @property integer project_id
 * @property integer house_id
 * @property integer member_id
 * @property integer status
 * @property integer created_at
 */
class SearchNoticesFavorites extends \yii\db\ActiveRecord
{
    const STATUS_UN_FAVORITES = 0;
    const STATUS_FAVORITES = 1;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'search_notices_favorites';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['project_id', 'house_id', 'member_id', 'search_id', 'status'], 'integer'],
            [['created_at'], 'safe'],
        ];
    }
}