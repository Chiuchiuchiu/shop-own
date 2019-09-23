<?php

namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "member".
 *
 * @property integer $id
 * @property string $wechat_open_id
 * @property string $mp_open_id
 * @property string $wechat_unionid
 * @property string $nickname
 * @property string $headimg
 * @property string $name
 * @property string $phone
 * @property integer $created_at
 *
 * @property string $showName
 */
class Member extends \yii\db\ActiveRecord implements IdentityInterface
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'member';
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
            [['wechat_open_id', 'nickname'], 'required'],
            [['created_at'], 'integer'],
            [['wechat_open_id', 'wechat_unionid', 'nickname'], 'string', 'max' => 50],
            [['headimg'], 'string', 'max' => 255],
            [['name'], 'string', 'max' => 10],
            [['name'], 'default', 'value' => ''],
            [['phone'], 'string','max'=>11],
            [['phone'], 'default','value'=>0],
            [['wechat_open_id'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'wechat_open_id' => 'Wechat Open ID',
            'mp_open_id' => 'Mp Open ID',
            'wechat_unionid' => 'wechat_unionid',
            'nickname' => 'Nickname',
            'headimg' => 'Headimg',
            'created_at' => 'Created At',
        ];
    }

    public static function findIdentity($id)
    {
        return self::findOne($id);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAuthKey()
    {
        throw new NotSupportedException();

    }

    public function validateAuthKey($authKey)
    {
        throw new NotSupportedException();
    }

    public function getShowName(){
        return $this->name?$this->name:$this->nickname;
    }

}
