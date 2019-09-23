<?php

namespace apps\business\models;

use components\rbac\RBACPermissionInterface;
use yii\base\ErrorException;
use yii\base\NotSupportedException;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "user".
 *
 * @property integer $id
 * @property string $real_name
 * @property string $email
 * @property string $password
 * @property integer $group_id
 * @property integer $need_change_pw
 * @property integer $state
 *
 * @property string $groupNamar
 *
 * relationProperty
 * @property ManagerGroup $group
 * @property ManagerLoginLog $lastLoginLog
 */
class Manager extends \yii\db\ActiveRecord implements IdentityInterface, RBACPermissionInterface
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
     * @return null|Manager
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
        return 'manager';
    }

    public function scenarios()
    {
        return [
            'default' => ['email', 'real_name', 'group_id'],
            self::SCENARIO_LOGIN => ['email', 'password'],
            self::SCENARIO_CREATE_UPDATE => ['email', 'real_name', 'password', 'group_id', 'state'],
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
            [['email', 'password', 'group_id', 'real_name'], 'required'],
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
            ['state','default','value'=>self::NEED_CHANGE_PASSWORD_YES,'on'=>self::SCENARIO_CREATE_UPDATE],
            ['need_change_pw','in','range'=>array_keys(self::needChangePasswordMap())],
            ['state','in','range'=>array_keys(self::stateMap())]
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
            'state' => '状态',
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
     * @param $email
     * @return null|Manager
     * Description:
     */
    public static function findByEmail($email)
    {
        return Manager::findOne(['email' => $email]);
    }


    public function validateLogin($attribute)
    {
        $self = self::findByEmail($this->email);
        if (is_null($self)
            || $this->md5Password($this->password) != $self->password
            || $self->state == self::STATE_DELETE
        ) {
            $this->addError($attribute, '账户或密码错误');
        }
    }

    public function hashPassword($attribute)
    {
        if (!$this->hasErrors())
            $this->password = $this->md5Password($this->password);
    }

    private function md5Password($password)
    {
        return md5(sprintf("a_bit_in_the_morning_is_better_%s_than_nothing_all_day*^_^*", $password));
    }

    public function getGroupName()
    {
        return $this->group->name;
    }

    public function getGroup()
    {
        return $this->hasOne(ManagerGroup::className(), ['id' => 'group_id']);
    }

    public function getLastLoginLog()
    {
        return $this->hasOne(ManagerLoginLog::className(), ['manager_id' => 'id'])->orderBy('id DESC')->limit(1);
    }

}
