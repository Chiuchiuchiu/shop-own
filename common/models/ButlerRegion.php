<?php

namespace common\models;

use Yii;
use yii\base\ErrorException;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "butler_region".
 *
 * @property integer $house_id
 * @property integer $butler_id
 * @property integer $room_status
 *
 * @property House $house
 * @property ButlerAuth $butlerAuth
 */
class ButlerRegion extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'butler_region';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['house_id', 'butler_id'], 'required'],
            [['house_id', 'butler_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'house_id' => 'House ID',
            'butler_id' => 'Butler ID',
            'room_status' => '房产状态'
        ];
    }

    /**
     * @param $houseId
     * @return array
     * Description:
     */
    public static function getRegionButler($houseId)
    {
        $house = House::findOne($houseId);
        if (!$house) return [];
        $houseIds = [$houseId];
        while ($row = $house->parent) {
            $houseIds[] = $row->house_id;
            $house = $row;
        }
        return ArrayHelper::getColumn(self::findAll(['house_id' => $houseIds]), 'butler_id');
    }

    public static function saveButlerRegion($butler_id, $house_ids, $butlerAuthId=0, $clearAll = false)
    {
        if (empty($house_ids)) return false;
        if (!is_array($house_ids)) $house_ids = [$house_ids];
        $houses = [];
        foreach ($house_ids as $house_id) {
            $houses[] = House::findOne($house_id);
            if (!end($houses) instanceof House) {
                return false;
            }
        }
        if ($clearAll) {
            ButlerRegion::deleteAll(['butler_id' => $butler_id]);
        }
        $house_ids = [];
        foreach ($houses as $house) {
            self::getAllHouseId($house, $house_ids);
        }

        $house_ids = array_unique($house_ids);
        if (sizeof($house_ids) == 0) return false;

        $tempNumber = 0;
        $tempSqlData = '';
        $tempHouseIds = [];
        foreach($house_ids as $key => $val){
            $tempNumber += 1;
            $tempHouseIds = explode('-', $val);

            $tempSqlData .= ",({$tempHouseIds[0]},{$butler_id},{$tempHouseIds[1]},{$butlerAuthId})";

            if($tempNumber > 49){
                $sql_data = trim($tempSqlData, ',');
                $tempSqlData = null;
                $sql = "REPLACE INTO " . self::tableName() . "(house_id, butler_id, room_status, butler_auth_id) VALUES " . $sql_data . ';';

                self::getDb()->createCommand($sql)->execute();
                $tempHouseIds=null;
                $tempNumber = 0;
            }
        }

        if(!empty($tempSqlData)){
            $sql_data = trim($tempSqlData, ',');
            $sql = "REPLACE INTO " . self::tableName() . "(house_id, butler_id, room_status, butler_auth_id) VALUES " . $sql_data . ';';
            self::getDb()->createCommand($sql)->execute();
        }
        unset($tempSqlData);
        unset($tempHouseIds);

        //更新redis，更新管家管辖区域下未认证房产 zhaowenxi 20180725
        if($butler_id != 0){
            if(!self::updateUnAuthHouse2Redis($butler_id, $house_ids)) return false;
        }

        return $house_ids;
    }

    /**
     * 更新redis，更新管家管辖的区域下未认证的房产
     * @param $butlerId
     * @param $houseIds
     * @return bool
     * @author zhaowenxi
     */
    private static function updateUnAuthHouse2Redis($butlerId, $houseIds){

        $unAuthHouse = ArrayHelper::getColumn(
            HouseUnauthorized::find()->select("house_id")
                ->andFilterWhere(["house_id" => $houseIds])
                ->all(),
        "house_id"
        );

        $butlerIdentify = 'butler_' . $butlerId;
        $redis = \Yii::$app->redis;
        $exists = $redis->exists($butlerIdentify);
        $exists && $redis->del($butlerIdentify);    //删除再添加

        foreach ($unAuthHouse as $k => $v){
            $redis->zadd($butlerIdentify, $k, $v);
        }

        return true;
    }

    private static function getAllHouseId(House &$house, &$house_ids)
    {
        //只保存"单元"
        $deepestNode = $house->deepest_node;
        if (in_array($house->reskind, [5, 6, 8, 11, 9]) && $deepestNode == 1){
            $roomStatus = 0;
            if(isset($house->room_status)){
                $n = intval(substr($house->room_status, 3, 2));
                switch ($n){
                    case 1:
                        $roomStatus = 14;
                        break;
                    case 10:
                    case 11:
                        $roomStatus = 15;
                        break;
                    default:
                        $roomStatus = 0;
                }
            }

            $house_ids[] = $house->house_id . '-' . $roomStatus;
        }

        if (sizeof($house->child)) {
            foreach ($house->child as $child) {
                self::getAllHouseId($child, $house_ids);
            }
        }
    }

    public function getHouse()
    {
        return $this->hasOne(House::className(), ['house_id' => 'house_id']);
    }

    public function getButlerAuth()
    {
        return $this->hasOne(ButlerAuth::className(), ['used_to' => 'butler_id']);
    }
}
