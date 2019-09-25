<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/10/24
 * Time: 11:55
 */

namespace apps\www\controllers;


use common\models\PropertyAnnouncement;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;

class PropertyAnnouncementController extends Controller
{
    public function actionList()
    {
        $projectHouseId = isset($this->project->house_id) ? $this->project->house_id : 0;

        $dataProvider = new ActiveDataProvider();
        $dataProvider->query = PropertyAnnouncement::find()
            ->where(['project_house_id' => $projectHouseId, 'status' => PropertyAnnouncement::STATUS_ACTIVE])
            ->orderBy('id DESC');
        $dataProvider->setSort(false);

        if ($this->isAjax && $this->get('page')) {
            return $this->renderPartial('list-cell', ['dataProvider' => $dataProvider]);
        } else {
            return $this->render('list', [
                'dataProvider' => $dataProvider
            ]);
        }
    }

    public function actionDetail($id)
    {
        $model = PropertyAnnouncement::findOne(['id' => $id]);

        return $this->render('detail', ['model' => $model]);
    }

}