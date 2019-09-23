<?php
/**
 * Created by PhpStorm.
 * User: HQM
 * Date: 2017/5/8
 * Time: 15:31
 */

namespace common\valueObject;


class TimeValue
{
    public static function dateDiff($fromTime, $time=null)
    {
        empty($time) && $time = time();
        empty($fromTime) && $fromTime = time();

        $second1 = date('Ymd', $fromTime);
        $second2 = date('Ymd', $time);

        if ($second1 < $second2) {
            $tmp = $second2;
            $second2 = $second1;
            $second1 = $tmp;
        }

        $number = $second1 - $second2;
        $day = '';

        switch ($number){
            case 0:
                $day = '今天';
                break;
            case 1:
                $day = '昨天';
                break;
            default:
                if($number > 30){
                    $day = date('Y-m-d H:i', $fromTime);
                } else {
                    $day = $number . '天前';
                }

                break;
        }

        return $day;
    }
}