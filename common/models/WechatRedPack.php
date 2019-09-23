<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "wechat_redpack".
 *
 * @property integer $id
 * @property integer $member_id
 * @property string $number
 * @property string $amount
 * @property string $pm_order_id
 * @property integer $house_id
 * @property integer $project_house_id
 * @property string $even_name
 * @property string $even_key
 * @property string $remark
 * @property string $result
 * @property integer $status
 * @property integer $created_at
 * @property integer $completed_at
 *
 * @property Member $member
 * @property House $house
 * @property Project $project
 */
class WechatRedPack extends \yii\db\ActiveRecord
{

    const STATUS_WAIT = 0 ;
    const STATUS_SEND = 1 ;
    const STATUS_ERROR = 2 ;
    public function behaviors()
    {
        return [
            [
                'class'=>TimestampBehavior::className(),
                'updatedAtAttribute'=>null,
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'wechat_red_pack';
    }

    public static function getDb()
    {
        return Yii::$app->get('eventDb');
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['member_id', 'amount', 'pm_order_id'], 'required'],
            [['member_id', 'created_at', 'status','completed_at'], 'integer'],
            ['completed_at','default','value'=>0],
            ['status','default','value'=>0],
            ['pm_order_id','string','max'=>30],
            ['remark','default','value'=>''],
            ['result','default','value'=>''],
            ['number','default','value'=>self::createNumber()],
            [['amount'], 'number'],
//            [['remark'], 'string', 'max' => 200],
//            [['even_name'], 'string', 'max' => 20],
//            [['even_key'], 'string', 'max' => 20],
//            [['result'], 'string', 'max' => 500],
        ];
    }

    public static function createNumber(){
        //请求基因+时间基因
        $str = serialize($_SERVER);
        $str = crc32(md5($str)).'';
        //时间基因
        $t = str_replace('.','',microtime(true)).mt_rand(1,9);
        $str = strrev($t).$str;

        return substr($str,0,20);

    }
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'member_id' => '用户名',
            'amount' => '金额',
            'pm_order_id' => 'Pm Order ID',
            'remark' => 'Remark',
            'result' => 'Result',
            'status' => '状态',
            'created_at' => '时间',
            'completed_at' => 'Completed At',
            'number' => '红包订单号'
        ];
    }

    public function getMember(){
        return $this->hasOne(Member::className(),['id'=>'member_id']);
    }

    public function getHouse()
    {
        return $this->hasOne(House::className(), ['house_id' => 'house_id']);
    }

    public function getProject()
    {
        return $this->hasOne(Project::className(), ['house_id' => 'project_house_id']);
    }

}
