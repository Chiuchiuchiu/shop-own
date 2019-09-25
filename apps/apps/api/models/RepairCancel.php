<?php
/**
 * Created by PhpStorm.
 * User: HQM
 */

namespace apps\api\models;


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