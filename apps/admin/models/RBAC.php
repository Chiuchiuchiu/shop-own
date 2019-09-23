<?php

namespace apps\admin\models;

use components\rbac\RBACInterface;
use components\arEasyCache\ActiveRecordCacheTrait;
use Yii;
/**
 * This is the model class for table "nav".
 *
 * @property integer $id
 * @property string $name
 * @property string $url
 * @property integer $order_id
 * @property integer $parent_id
 * @property string $route
 * @property string $auth_route
 * @property string $icon
 * @property integer $state
 * @property integer $type
 * @property array|RBAC $navItems
 * @property null|RBAC $parent
 *
 */
class RBAC extends \yii\db\ActiveRecord implements RBACInterface
{

    use ActiveRecordCacheTrait;

    const STATE_ACTIVE = 1;
    const STATE_DELETE = 0;

    const TYPE_NAV = 1;

    public $visible = true;

    public function __construct($config = [])
    {
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'rbac';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('adminSQLite');
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name', 'url', 'route', 'icon'], 'string'],
            [['parent_id', 'type'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'url' => 'Url',
            'order_id' => 'Order ID',
            'parent_id' => 'Parent ID',
            'route' => 'Route',
            'auth_route' => 'Auth Route',
            'icon' => 'Icon',
            'type' => 'Type',
        ];
    }

    /**
     * @param $name
     * @return null|self
     * Description:根据路由或者url去查找一个权限列表
     */
    public static function findByRoute($name)
    {
        $res = self::findOne(['route' => $name, 'state' => self::STATE_ACTIVE]);
        if (!$res) $res = self::findOne(['url' => '/' . $name]);
        return $res;
    }

    public static function findByAuthRoute($name)
    {
        $_name = explode('/',$name);
        $_name =$_name[0].'/*';
        $record = self::find()->orWhere(['like', 'auth_route',$name])
        ->orWhere(['like', 'auth_route',$_name]);
        foreach ($record->each() as $res) {
            if (!empty($res->auth_route)) {
                $res->auth_route = explode(',', $res->auth_route);

                if (in_array($name, $res->auth_route) || in_array($_name, $res->auth_route))
                    return $res;
            }
        }
        return null;
    }


    /**
     * @return self[]
     * Description:查找导航类型
     */
    public static function findNavActive()
    {
        return self::find()->where(
            ['state' => self::STATE_ACTIVE, 'parent_id' => 0, 'type' => self::TYPE_NAV]
        )->orderBy('order_id ASC')->all();
    }

    public function getNavItems()
    {
        return $this->hasMany(RBAC::className(), ['parent_id' => 'id'])->where(['state' => self::STATE_ACTIVE, 'type' => self::TYPE_NAV])->orderBy('order_id ASC');
    }

    public function getChild()
    {
        return $this->hasMany(self::className(), ['parent_id' => 'id'])->where(['state' => self::STATE_ACTIVE])->orderBy('order_id ASC');
    }

    public function getParent(){
        return $this->hasOne(self::className(), ['id' => 'parent_id'])->where(['state' => self::STATE_ACTIVE]);
    }
}
