<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/5/6
 * Time: 15:07
 */

namespace apps\www\models;


class RepairCancel extends \common\models\RepairCancel
{
    public static function findOrCreate($repairId)
    {
        $model = self::findOne(['repair_id' => $repairId]);
        if(!$model){
            $model = new self();
            $model->repair_id = $repairId;
        }

        return $model;
    }

}