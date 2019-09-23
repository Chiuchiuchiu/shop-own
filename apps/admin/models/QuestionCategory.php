<?php

namespace apps\admin\models;

use Yii;

/**
 * This is the model class for table "quest_cate".
 *
 * @property integer $id
 * @property integer $parent_id
 * @property string $title
 * @property string $deleted_at
 * @property string $created_at
 * @property string $updated_at
 */
class QuestionCategory extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'quest_cate';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['parent_id'], 'integer'],
            [['deleted_at', 'created_at', 'updated_at'], 'safe'],
            [['title'], 'string', 'max' => 250],
        ];
    }


    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '编号',
            'parent_id' => '上级分类',
            'title' => '分类标题',
            'deleted_at' => 'Deleted At',
            'created_at' => '添加时间',
            'updated_at' => 'Updated At',
        ];
    }
    public  static function view($id){

        if (($model = self::findOne($id)) !== null) {
            return $model['title'];
        } else {
            return '--';
        }
    }
    public  static function List($id){
        $List = self::find()->where(['parent_id'=>$id])->orderBy('id desc')->asArray()->all();
        return $List;
    }

    public  static function parentAll(){

        $List = self::find()->where(['parent_id'=>0])->select('id,title')->orderBy('id asc')->asArray()->all();
        $List[]=['id'=>0,'title'=>'顶级分类'];
        return $List;
    }
    public function getCategory()
    {
        return $this->hasOne(QuestionCategory::className(), ['id' => 'parent_id']);
    }
    public static function getCategorycount($id)
    {
        $count = Question::find()->where(['category_id'=>$id])->count();
        return $count;
    }
}
