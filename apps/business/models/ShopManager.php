<?php

namespace apps\business\models;

use components\rbac\RBACPermissionInterface;
use yii\base\ErrorException;
use yii\base\NotSupportedException;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "shop-manager".
 *
 * @property integer $id
 * @property string $shop_id
 * @property string $name
 * @property string $email
 * @property string $mobile
 * @property string $password
 * @property integer $manager_group
 * @property integer $need_change_pw
 * @property integer $status
 * @property integer $created_at
 * @property integer $last_login_at
 *
 * @property ShopManagerGroup $group
 * @property ShopManagerLoginLog $lastLoginLog
 */
class ShopManager extends \yii\db\ActiveRecord implements IdentityInterface, RBACPermissionInterface
{
    const SCENARIO_LOGIN = 'login';
    const SCENARIO_CREATE_UPDATE = 'createUpdate';
    const SCENARIO_UPDATE = 'Update';
    const SCENARIO_CHANGE_PASSWORD = 'changePassword';
    const SCENARIO_CREATE_REGION = 'createRegion';

    const STATE_ACTIVE = 1;
    const STATE_DELETE = 0;

    const NEED_CHANGE_PASSWORD_YES = 1;
    const NEED_CHANGE_PASSWORD_NO = 0;
    public $confirmPassword = null;

    /**
     * @param int|string $id
     * @return null|ShopManager
     * Description:
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    public function getId()
    {
        return $this->getPrimaryKey();
    }

    public function getAuthKey()
    {
        throw new NotSupportedException('"getAuthKey" is not implemented.');
    }

    public function validateAuthKey($authKey)
    {
        throw new NotSupportedException('"getAuthKey" is not implemented.');
    }


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'shop_manager';
    }

    public function scenarios()
    {
        return [
            'default' => ['email', 'real_name', 'manager_group', 'mobile', 'shop_id'],
            self::SCENARIO_LOGIN => ['mobile', 'password'],
            self::SCENARIO_CREATE_UPDATE => ['email', 'real_name', 'password', 'group_id', 'status'],
            self::SCENARIO_UPDATE => ['email', 'real_name', 'group_id', 'state'],
            self::SCENARIO_CHANGE_PASSWORD => ['password', 'confirmPassword'],
        ];
    }

    public static function stateMap(){
        return [
            self::STATE_ACTIVE=>'正常',
            self::STATE_DELETE=>'删除'
        ];
    }

    public static function needChangePasswordMap(){
        return [
            self::NEED_CHANGE_PASSWORD_YES=>'正常',
            self::NEED_CHANGE_PASSWORD_NO=>'删除'
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['email', 'password', 'group_id', 'real_name', 'shop_id'], 'required'],
            ['email', 'unique', 'on' => self::SCENARIO_CREATE_UPDATE],
            [['group_id'], 'integer'],
            ['email', 'email'],
            ['real_name', 'string', 'max' => 5, 'min' => 0],

            ['confirmPassword', 'compare', 'compareAttribute' => 'password', 'on' => self::SCENARIO_CHANGE_PASSWORD],
            ['password', 'string', 'max' => 100, 'min' => 6],
            ['password', 'validateLogin', 'on' => self::SCENARIO_LOGIN],
            ['password', 'hashPassword', 'on' => [self::SCENARIO_CREATE_UPDATE, self::SCENARIO_CHANGE_PASSWORD]],
            ['password', 'string', 'max' => 32],

            ['need_change_pw','default','value'=>self::NEED_CHANGE_PASSWORD_YES,'on'=>self::SCENARIO_CREATE_UPDATE],
            ['status','default','value'=>self::NEED_CHANGE_PASSWORD_YES,'on'=>self::SCENARIO_CREATE_UPDATE],
            ['need_change_pw','in','range'=>array_keys(self::needChangePasswordMap())],
            ['status','in','range'=>array_keys(self::stateMap())]
        ];
    }


    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '#',
            'real_name' => '用户名',
            'email' => '邮箱',
            'password' => '密码',
            'group_id' => '用户组',
            'status' => '状态',
            'need_change_pw' => '必须修改密码',
            'confirmPassword' => '确认密码',

        ];
    }

    /**
     * @param $permission
     * @return bool
     * @throws ErrorException
     * Description:
     */
    public function hasPermission($permission)
    {
        return $this->group->hasPermission($permission);
    }

    /**
     * @param $mobile
     * @return null|ShopManager
     * Description:
     */
    public static function findByMobile($mobile)
    {
        return ShopManager::findOne(['mobile' => $mobile]);
    }

    public function validateLogin($attribute)
    {
        $self = self::findByMobile($this->mobile);
        if (is_null($self)
            || $this->md5Password($this->password) != $self->password
            || $self->status == self::STATE_DELETE
        ) {
            $this->addError($attribute, '账户或密码错误！');
        }
    }

    public function hashPassword($attribute)
    {
        if (!$this->hasErrors())
            $this->password = $this->md5Password($this->password);
    }

    private function md5Password($password)
    {
        return md5(sprintf("chiu_%s", $password));
    }

    public function getGroupName()
    {
        return $this->group->name;
    }

    public function getGroup()
    {
        return $this->hasOne(ShopManagerGroup::className(), ['id' => 'manager_group']);
    }

    public function getLastLoginLog()
    {
        return $this->hasOne(ShopManagerLoginLog::className(), ['manager_id' => 'id'])->orderBy('id DESC')->limit(1);
    }

}
