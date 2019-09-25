<?php

namespace apps\api\models;

/**
 * This is the model class for table "member_car".
 *
 * @property integer $id
 * @property integer $type  1：临卡 2：月卡
 * @property integer $member_id
 * @property string $plate_number
 * @property integer $created_at
 */
class MemberCar extends \apps\www\models\MemberCar
{

}
