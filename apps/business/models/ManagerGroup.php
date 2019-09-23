<?php

namespace apps\business\models;

use Yii;
use yii\base\ErrorException;
use yii\helpers\Inflector;

/**
 * This is the model class for table "groups".
 *
 * @property integer $id
 * @property string $name
 * @property string $permission
 * @property string $state
 */
class ManagerGroup extends \yii\db\ActiveRecord
{
    const STATE_ACTIVE = 1;
    const STATE_DELETE = 0;
    const STATE_PRIVATE = 2;//保护性组，目前不会在列表显示出来，别无他用

    const GROUP_SUB_ACCESS = 4;//子账户组ID
    const GROUP_ROOT = 1;//root组ID

    const SCENARIO_UPDATE = 'update';
    const SCENARIO_CREATE = 'create';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'manager_group';
    }


    /**
     * @return array|null
     * Description:返回解析后的权限
     */
    public function getPermission()
    {
        return unserialize($this->permission);
    }


    public function setPermission()
    {
        $this->permission = is_array($this->permission) ? serialize($this->permission) : (string)$this->permission;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 40],
            ['permission', 'setPermission', 'on' => [self::SCENARIO_UPDATE, self::SCENARIO_CREATE]],
            ['permission', 'string'],
            ['state','default','value'=>1,'on'=>self::SCENARIO_CREATE],
            ['state', 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '#',
            'name' => '角色名称',
            'permission' => '权限',
            'state' => '状态'
        ];
    }

    /**
     * @param RBAC|string $permission
     * @return bool
     * @throws ErrorException
     * Description:
     */
    public function hasPermission($permission)
    {
        if (is_null($permission))
            return false;
        $userPermission = $this->getPermission();

        if($permission instanceof RBAC)
            $rbac = $permission;
        else{
            $permission = Inflector::camel2id($permission);
            $rbac = RBAC::findByRoute(Inflector::camel2id($permission));
            if (is_null($rbac)) {
                $rbac = RBAC::findByAuthRoute($permission);
                is_null($rbac) && Yii::error("RBAC '{$permission}' undefined", self::className());
            }
        }
        if (isset($userPermission['option']['root']) && $userPermission['option']['root'])
            return true;
        Yii::trace("check permission {$rbac->name}", self::className());
        return $rbac && is_array($userPermission['route']) && in_array($rbac->id, $userPermission['route']);
    }

    public function checkOptionPermission($key)
    {
        $userPermission = $this->getPermission();
        return isset($userPermission['option'][$key]) && $userPermission['option'][$key];
    }
}
