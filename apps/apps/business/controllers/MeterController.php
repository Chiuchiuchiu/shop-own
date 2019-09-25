<?php
namespace apps\business\controllers;

use common\models\MeterHouse;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

class MeterController extends Controller
{
    /**
     * 水电抄表首页
     * @param int $type         1水表2电表
     * @param int $house_id     项目id
     * @param null $search      房号或表号
     * @return string
     * @author zhaowenxi
     */
    public function actionIndex($type = 1, $house_id = 0,$search = null)
    {
        $dataProvider = new ActiveDataProvider();

        $projects = $this->projectCache();

        $projectsArray[''] = '全部';
        $projectsArray['项目列表'] = ArrayHelper::map($projects, 'house_id', 'house_name');

        //强制选择项目id，不然数据太大
        if($house_id){

            $typeStr = $type == 1 ? "冷水表" : "电表";

            $dataProvider->query = MeterHouse::find()
                ->where(['like', 'ancestor_name', $search])
                ->orWhere(['uid' => $search])
                ->andFilterWhere(['meter_type' => $typeStr, 'project_id' => $house_id])
                ->orderBy('id DESC');
        }

        return $this->render('index', [
            'projects' => $projectsArray,
            'house_id' => $house_id,
            'search' => $search,
            'type' => $type,
            'dataProvider' => $dataProvider,
        ]);
    }
}
