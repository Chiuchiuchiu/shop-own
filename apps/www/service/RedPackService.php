<?php
/**
 * Created by
 * Author: zhao
 * Time: 2016/12/29 09:24
 * Description:
 */

namespace apps\www\service;


use apps\www\models\Member;
use common\models\PmOrder;
use common\models\WechatRedPack;
use components\wechatSDK\WechatSDK;

class RedPackService extends Service
{
    public static function sendByPmOrder($pmOrderId){
        $pmOrder = PmOrder::findOne($pmOrderId);
        if(!$pmOrder || !in_array($pmOrder->status,[PmOrder::STATUS_PAYED,PmOrder::STATUS_TEST_PAYED])){
            return self::fail(-1,"订单无效");
        }
        if($pmOrder->status=PmOrder::STATUS_TEST_PAYED){
            $pmOrder->total_amount=200;
        }
        $rand = mt_rand(500,1000);
        $redPackAmount = round($rand*$pmOrder->total_amount/100000,2);
        if($redPackAmount<1){
            return self::fail(1,"金额不足");
        }
        $redPack = WechatRedPack::findOne(['pm_order_id'=>$pmOrder]);
        if(!$redPack){
            $redPack = new WechatRedPack(['pm_order_id'=>$pmOrder]);
            $redPack->member_id = $pmOrder->member_id;
            $redPack->pm_order_id = $pmOrder->id;
            $redPack->amount = $redPackAmount;
            $redPack->number = WechatRedPack::createNumber();
            if(!$redPack->save()){
                return self::fail(-1,"redPack save Error",$redPack->getErrors());
            }
        }
        $test = '';
        if($redPack->amount<=2){
            $test = '点点滴滴都是爱〜';
        }elseif($redPack->amount<=4){
            $test = '哇红包一开新年到〜';
        }elseif($redPack->amount<=6){
            $test = '红包到手喜事多〜';
        }else{
            $test = '财神到家钱不愁花〜';
        }
        $res = WechatSDK::sendRedPack($redPack->member->wechat_open_id,$redPack->number,$redPack->amount,1,'物业送红包',$test);
        $redPack->completed_at = time();
        $redPack->status= $res['return_code']=='SUCCESS'?WechatRedPack::STATUS_SEND:WechatRedPack::STATUS_ERROR;
        $redPack->result= serialize($res);
        $redPack->save();
        return self::success(['redPack'=>$redPack]);
    }

    public static function sendByAuth($member_id,$house_id,$project_id){
        $member = Member::findOne($member_id);
        if(!$member){
            return self::fail(-1,'memberNotFind');
        }
        if(WechatRedPack::find()->where(['even_name'=>'auth','even_key'=>$project_id])->count()>1600){
            return self::fail(-2,'No RedPack');
        }
        $redPack = WechatRedPack::findOne(['pm_order_id'=>$member_id.'-'.$house_id]);
        if(!$redPack){
            $redPack = new WechatRedPack(['pm_order_id'=>$member_id.'-'.$house_id]);
            $redPack->member_id = $member_id;
            $redPack->amount = 1;
            $redPack->even_name = 'auth';
            $redPack->even_key = $project_id;
            $redPack->remark = '认证送红包';
            $redPack->number = WechatRedPack::createNumber();
            if(!$redPack->save()){
                return self::fail(-1,"redPack save Error",$redPack->getErrors());
            }
        }
        $res = WechatSDK::sendRedPack($redPack->member->wechat_open_id,$redPack->number,$redPack->amount,1,'物业送红包','');
        $redPack->completed_at = time();
        $redPack->status= $res['return_code']=='SUCCESS'?WechatRedPack::STATUS_SEND:WechatRedPack::STATUS_ERROR;
        $redPack->result= serialize($res);
        $redPack->save();
        return self::success(['redPack'=>$redPack]);
    }

    public static function sendBy2017($git_id,$member_id){
        $member = Member::findOne($member_id);
        if(!$member){
            return self::fail(-1,'memberNotFind');
        }
        if(WechatRedPack::find()->where(['even_name'=>'2017','even_key'=>$git_id])->count()>1600){
            return self::fail(-2,'No RedPack');
        }
        $redPack = WechatRedPack::findOne(['pm_order_id'=>$git_id]);
        if(!$redPack){
            $redPack = new WechatRedPack(['pm_order_id'=>$git_id]);
            $redPack->member_id = $member_id;
            $redPack->amount = 1;
            $redPack->even_name = 'auth';
            $redPack->even_key = $git_id;
            $redPack->remark = '缴费送红包';
            $redPack->number = WechatRedPack::createNumber();
            if(!$redPack->save()){
                return self::fail(-1,"redPack save Error",$redPack->getErrors());
            }
        }
        $res = WechatSDK::sendRedPack($redPack->member->wechat_open_id,$redPack->number,$redPack->amount,1,'物业送红包','');
        $redPack->completed_at = time();
        $redPack->status= $res['return_code']=='SUCCESS'?WechatRedPack::STATUS_SEND:WechatRedPack::STATUS_ERROR;
        $redPack->result= serialize($res);
        $redPack->save();
        return self::success(['redPack'=>$redPack]);
    }
}