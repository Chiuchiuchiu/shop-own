<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%user}}".
 *
 * @property integer $uid
 * @property string $nickname
 * @property string $password
 * @property string $truename
 * @property string $mobile
 * @property string $email
 * @property integer $sex
 * @property string $headimgurl
 * @property string $city
 * @property string $province
 * @property string $country
 * @property string $language
 * @property integer $score
 * @property integer $experience
 * @property string $unionid
 * @property integer $login_count
 * @property string $reg_ip
 * @property integer $reg_time
 * @property string $last_login_ip
 * @property integer $last_login_time
 * @property integer $status
 * @property integer $is_init
 * @property integer $is_audit
 * @property integer $subscribe_time
 * @property string $remark
 * @property integer $groupid
 * @property integer $come_from
 * @property string $login_name
 * @property string $login_password
 * @property integer $manager_id
 * @property integer $level
 * @property string $membership
 * @property integer $sync_user_id
 */
class WpUser extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('wmpDb');
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['nickname'], 'string'],
            [['sex', 'score', 'experience', 'login_count', 'reg_time', 'last_login_time', 'status', 'is_init', 'is_audit', 'subscribe_time', 'groupid', 'come_from', 'manager_id', 'level', 'sync_user_id'], 'integer'],
            [['password', 'email', 'remark', 'login_name'], 'string', 'max' => 100],
            [['truename'], 'string', 'max' => 40],
            [['mobile', 'city', 'province', 'country', 'reg_ip', 'last_login_ip'], 'string', 'max' => 30],
            [['headimgurl', 'login_password'], 'string', 'max' => 255],
            [['language'], 'string', 'max' => 20],
            [['unionid', 'membership'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'uid' => 'Uid',
            'nickname' => 'Nickname',
            'password' => 'Password',
            'truename' => 'Truename',
            'mobile' => 'Mobile',
            'email' => 'Email',
            'sex' => 'Sex',
            'headimgurl' => 'Headimgurl',
            'city' => 'City',
            'province' => 'Province',
            'country' => 'Country',
            'language' => 'Language',
            'score' => 'Score',
            'experience' => 'Experience',
            'unionid' => 'Unionid',
            'login_count' => 'Login Count',
            'reg_ip' => 'Reg Ip',
            'reg_time' => 'Reg Time',
            'last_login_ip' => 'Last Login Ip',
            'last_login_time' => 'Last Login Time',
            'status' => 'Status',
            'is_init' => 'Is Init',
            'is_audit' => 'Is Audit',
            'subscribe_time' => 'Subscribe Time',
            'remark' => 'Remark',
            'groupid' => 'Groupid',
            'come_from' => 'Come From',
            'login_name' => 'Login Name',
            'login_password' => 'Login Password',
            'manager_id' => 'Manager ID',
            'level' => 'Level',
            'membership' => 'Membership',
            'sync_user_id' => 'Sync User ID',
        ];
    }
}
