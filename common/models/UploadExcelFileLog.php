<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "upload_excel_file_log".
 *
 * @property integer $id
 * @property string $path
 * @property integer $project_house_id
 * @property integer $created_at
 */
class UploadExcelFileLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'upload_excel_file_log';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('logDb');
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
            [['created_at'], 'integer'],
            [['path'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'path' => 'Path',
            'created_at' => 'Created At',
        ];
    }

    public static function savePath($path, $projectHouseId=0)
    {
        $model = new self();
        $model->path = trim($path);
        $model->project_house_id = $projectHouseId;

        return $model->save();
    }

}
