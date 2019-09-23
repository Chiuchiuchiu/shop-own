<?php
/**
 * Created by PhpStorm.
 * User: HQM
 * Date: 2017/1/22
 * Time: 15:19
 */

namespace apps\admin\models;


class Member extends \common\models\Member
{
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'wechat_open_id' => '微信 Open ID',
            'nickname' => '昵称',
            'headimg' => '头像',
            'name' => '用户名',
            'phone' => '手机号',
            'created_at' => '创建时间',
        ];
    }
}