<?php

namespace apps\api\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "upload_file_log".
 * @author HQM
 * @property integer $id
 * @property string $save_path
 * @property string $url_path
 * @property integer $created_at
 */
class UploadFileLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'upload_file_log';
    }

    /**
     * @return null|object|\yii\db\Connection the database connection used by this AR class.
     * @throws \yii\base\InvalidConfigException
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
            [['save_path'], 'string', 'max' => 50],
            [['url_path'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'save_path' => 'Save Path',
            'url_path' => 'Url Path',
            'created_at' => 'Created At',
        ];
    }

    /**
     * @author HQM 2018/11/20
     * @param $savePath
     * @param $urlPath
     * @return bool
     */
    public static function log($savePath, $urlPath)
    {
        $model = new self();
        $model->save_path = $savePath;
        $model->url_path = $urlPath;

        return $model->save() ? $model->id : false;
    }

}
