<?php
/**
 * Created by
 * Author: zhao
 * Time: 2016/11/22 11:02
 * Description:
 */

namespace apps\api\models;


use yii\web\Linkable;

class Member extends \common\models\Member implements Linkable
{

    /**
     * 重写返回格式
     * @return array
     * @author zhaowenxi
     */
    public function fields()
    {
        return [
            'id',
            'wechat_open_id',
            'mp_open_id',
            'wechat_unionid',
            'nickname',
            'headimg',
            'name',
            'phone',
            'created_at' => function($model){
                return date('Y-m-d H:i:s', $model->created_at);
            }
        ];
    }

    /**
     * 返回集合中的详细路径信息
     * @return array
     * @author zhaowenxi
     */
    public function getLinks(){
        return [

//            Link::REL_SELF => Url::to(['member/view', 'id' => $this->id], true),
//            'edit' => Url::to(['member/update', 'id' => $this->id], true),

        ];
    }
}