<?php
/**
 * Created by PhpStorm.
 * User: feng
 * Date: 2019/4/8
 * Time: 14:23
 */

namespace common\models;
use Yii;
use yii\behaviors\TimestampBehavior;



/**
 * This is the model class for table "search_notices".
 *
 * @property integer $id
 * @property integer $type
 * @property string $typeText
 * @property integer $house_id
 * @property string $project_id
 * @property integer $member_id
 * @property string  $member_nickname;
 * @property string  $member_headimg;
 * @property integer $claimant_id
 * @property string $title
 * @property string $tel
 * @property string $linkman
 * @property string $describtions
 * @property string $pics
 * @property integer $status
 * @property string $lose_address
 * @property string $lose_time
 * @property integer $receive_address
 * @property integer $receive_remark
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property Favorites $favorites
 */
class SearchNotices extends \yii\db\ActiveRecord
{
    const STATUS_WAIT = 0;
    const STATUS_RECEIVE = 1;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'search_notices';
    }

    public function getFavorites(){
        return $this->hasOne(SearchNoticesFavorites::className(),['search_id'=>'id']);
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['project_id', 'house_id', 'member_id', 'status'], 'integer'],
            [['title','describtions','pics', 'lose_address', 'lose_time', 'receive_address','receive_remark', 'tel','member_nickname','linkman','member_headimg'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
        ];
    }
}