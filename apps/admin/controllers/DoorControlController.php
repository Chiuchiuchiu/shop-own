<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/10/23
 * Time: 17:19
 */

namespace apps\admin\controllers;


use common\models\House;
use common\models\LxjLog;
use common\valueObject\RangDateTime;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

class DoorControlController extends Controller
{
    public function actionLxj()
    {
        $dateTime = (new RangDateTime())->autoLoad($this->get());

        $house_id = $this->get('house_id', null);
        $projects = House::find()->select('project_house_id as house_id, house_name')
            ->where(['parent_id' => 0])
            ->asArray()
            ->orderBy('house_name')
            ->all();

        $projectsArray = [];
        $projectsArray[''] = '全部';
        $projectsArray['项目列表'] = ArrayHelper::map($projects, 'house_id', 'house_name');

        $dataProvider = new ActiveDataProvider();
        $dataProvider->query = LxjLog::find()
            ->andFilterWhere(['project_house_id' => $house_id])
            ->andFilterWhere(['BETWEEN', 'created_at', $dateTime->getStartTime(), $dateTime->getEndTime()])
            ->orderBy('id DESC');

        $dataProvider->setSort(false);

        return $this->render('lxj', [
            'dataProvider' => $dataProvider,
            'house_id' => $house_id,
            'projectsArray' => $projectsArray,
            'dateTime' => $dateTime
        ]);
    }

    public function actionLxjExport()
    {


    }

}