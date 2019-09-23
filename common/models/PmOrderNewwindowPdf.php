<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "pm_order_newwindow_pdf".
 *
 * @property integer $id
 * @property integer $pm_order_fpzz_id
 * @property integer $member_id
 * @property string $tax_amount
 * @property string $not_tax_amount
 * @property string $fpjym
 * @property string $bill_num
 * @property string $bill_code
 * @property string $bill_pdf_url
 * @property string $bill_jpg_url
 * @property string $ref_bill_num
 * @property string $ref_bill_code
 * @property string $ref_bill_pdf_url
 * @property string $ref_bill_jpg_url
 * @property string $save_path
 * @property integer $created_at
 */
class PmOrderNewwindowPdf extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pm_order_newwindow_pdf';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'updatedAtAttribute' => false,
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pm_order_fpzz_id', 'created_at'], 'integer'],
            [['bill_num', 'bill_code', 'ref_bill_num', 'ref_bill_code'], 'string', 'max' => 30],
            [['bill_pdf_url', 'bill_jpg_url', 'ref_bill_pdf_url', 'ref_bill_jpg_url'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'pm_order_fpzz_id' => 'Pm Order Fpzz ID',
            'tax_amount' => '税额',
            'not_tax_amount' => '不含税金额',
            'fpjym' => '校验码',
            'bill_num' => '蓝字发票号码',
            'bill_code' => '蓝字发票代码',
            'bill_pdf_url' => '发票PDF地址',
            'bill_jpg_url' => '发票JPG地址',
            'ref_bill_num' => '红字发票号码',
            'ref_bill_code' => '红字发票代码',
            'ref_bill_pdf_url' => '红字发票PDF地址',
            'ref_bill_jpg_url' => '红字发票JPG地址',
            'save_path' => 'PDF 保存路径',
            'created_at' => '创建时间',
        ];
    }

    /**
     * @param int $pmOrderFpzzId
     * @param string $billNum
     * @param string $billCode
     * @param string $billPdfUrl
     * @return bool
     */
    public static function insertCreate($pmOrderFpzzId, $billNum, $billCode, $billPdfUrl)
    {
        $model = new self();
        $model->pm_order_fpzz_id = $pmOrderFpzzId;
        $model->bill_num = $billNum;
        $model->bill_code = $billCode;
        $model->bill_pdf_url = $billPdfUrl;

        return $model->save();
    }

}
