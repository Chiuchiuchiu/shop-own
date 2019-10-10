<?php
/**
 * Created by
 * Author: zhao
 * Time: 2016/11/14 16:27
 * Description:
 */

namespace apps\admin\controllers;


use apps\admin\valueObject\FileCache;
use common\models\House;
use common\models\HouseRelevance;
use common\models\Project;
use common\models\ProjectRegion;
use components\newWindow\NewWindow;
use yii\base\ErrorException;
use common\models\ProjectHouseStructure;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;


class ShopController extends Controller
{
    public function actionIndex($search=null, $status=null)
    {
        var_dump(123);exit;

        //分公司
        $projectRegion[''] = '全部';
        $projectRegionInfo = $this->projectRegionCache();
        $projectRegionInfo = ArrayHelper::map($projectRegionInfo, 'id', 'name');
        $projectRegion['项目列表'] = $projectRegionInfo;

        $dataProvider = new ActiveDataProvider();
        $dataProvider->query = Project::find()
            ->andFilterWhere(['status' => $status, 'project_region_id' => $projectRegionId])
            ->andFilterWhere(['like', 'house_name', $search]);
        $dataProvider->setSort(false);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'projectRegionId' => $projectRegionId,
            'projectRegion' => $projectRegion,
            'search' => $search,
            'status' => $status,
        ]);
    }

    public function actionJumpPm($key)
    {
        $urlKey = $key;
        $token = md5(str_shuffle(microtime()) . $_SERVER['HTTP_USER_AGENT'] . $key);
        \Yii::$app->cache->set($token, $token, 30);

        $this->redirect('http://'. $urlKey . '.' . \Yii::$app->params['domain.pm'] . '?token=' . $token);
    }

    public function actionUpdate($id)
    {
        $model = $this->findProject($id);
        $projectRegion = $this->projectRegionCache();
        $projectRegionInfo = ArrayHelper::map($projectRegion, 'id', 'name');

        if ($this->isPost) {
            if ($model->load($this->post())) {
                $projectRegionId = $this->post('Project')['project_region_id'];
                $model->project_region_id = $projectRegionId;

                if($model->save()){
                    $this->setFlashSuccess();
                    return $this->backRedirect();
                }

            } else {
                $this->setFlashErrors($model->getErrors());
            }
        }
        return $this->render('update', ['model' => $model, 'projectRegion' => $projectRegionInfo]);
    }

    public function actionCreate()
    {
        $model = new Project();
        $projectRegion = $this->projectRegionCache();
        $projectRegionInfo = ArrayHelper::map($projectRegion, 'id', 'name');

        if ($this->isPost) {
            $postData = $this->post();
            $sName = $projectName = $postData[$model->formName()]['house_name'];
//            $projectName = str_ireplace('项目', '', $projectName);

            /*$projectExists = Project::find()->where("house_name like :projectName")
                ->addParams([':projectName' => '%' . $projectName . '%'])
                ->one();*/

            $projectExists = Project::find()->where(['house_name' => $projectName])->one();

            if ($projectExists) {
                $this->setFlashError('操作失败', '该楼盘已存在');
                return $this->backRedirect();
            } else {
                $res = (new NewWindow())->project($sName);

                if (empty($res['Response']['Data']['Record'])){
                    $this->setFlashError('操作失败', '该楼盘不存在');
                    return $this->backRedirect();
                }

                $transaction = \Yii::$app->db->beginTransaction();
                $pmManagerGroupModel = new PmManagerGroup();
                $pmManagerModel = new PmManager();

                $data = $this->post();
                $data[$model->formName()]['house_id'] = $res['Response']['Data']['Record'][0]['ProjectID'];
                $data[$model->formName()]['house_name'] = $res['Response']['Data']['Record'][0]['ProjectName'];

                if ($model->load($data) && $model->save()){

                    $pmManagerGroupModel->project_house_id = $res['Response']['Data']['Record'][0]['ProjectID'];
                    $pmManagerGroupModel->name = '管理员';
                    $pmManagerGroupModel->permission = 'a:1:{s:6:"option";a:1:{s:4:"root";b:1;}}';
                    $pmManagerGroupModel->state = 2;

                    if ($pmManagerGroupModel->save()){
                        $pmManagerModel->group_id = $pmManagerGroupModel->id;
                        $pmManagerModel->project_house_id = $pmManagerGroupModel->project_house_id;
                        $pmManagerModel->password = md5(sprintf("a_bit_in_the_morning_is_better_%s_than_nothing_all_day*^_^*", '123456'));
                        $pmManagerModel->real_name = 'admin';
                        $pmManagerModel->name = 'admin_'.$model->url_key;
                        $pmManagerModel->state = 1;

                        if ($pmManagerModel->save()){
                            $transaction->commit();
                            $this->setFlashSuccess();
                            return $this->backRedirect();
                        }
                    }
                }

                $transaction->rollBack();
                $this->setFlashErrors($model->getErrors());
                return $this->render('create', ['model' => $model]);
            }
        }

        return $this->render('create', ['model' => $model, 'projectRegion' => $projectRegionInfo]);
    }

    public function actionSearchProjects()
    {
        $projectName = trim($this->get('projectName'));
        if(!isset($projectName)){
            return $this->renderJsonFail('请输入需要查询的项目名');
        }

        $initArray = [];
        $res = (new NewWindow())->project($projectName);
        if (is_array($res['Response']['Data']['Record']) && !empty($res['Response']['Data']['Record'])){
            foreach($res['Response']['Data']['Record'] as $key => $val){
                if($val['CityName'] != '已撤场项目'){
                    $initArray[] = $val['ProjectName'];
                }
            }
            return $this->renderJsonSuccess($initArray);
        }

        return $this->renderJsonFail('无数据');
    }

    public function actionSyncHouses($projectId)
    {
        $model = Project::find()->where(['house_id' => $projectId])->one();
        if (empty($model)){
            throw new NotFoundHttpException();
        }

        return $this->render('sync-houses', ['model' => $model]);
    }

    public function actionSyncHousesIframe($projectId, $ids = '')
    {
        if(empty($ids)) {
            $ids = $projectId;
        } elseif($ids == 'implode') {
            $ids = \Yii::$app->cache->get('houseIDs_' . $projectId);
            \Yii::$app->cache->delete('houseIDs_' . $projectId);
        }

        $ids = explode(',',$ids);
        $ids = array_flip($ids);
        $model = Project::find()->where(['house_id' => $projectId])->one();
        if (empty($model)){
            throw new NotFoundHttpException();
        }

        $houseIds = $this->SyncProjectHouse($ids, $projectId);

        return $this->renderPartial('sync-houses-iframe', get_defined_vars());
    }

    public function actionDetail($id, $house_id){
        $model = $this->findProject($id);
        $house = House::findOne($house_id);
        if (!$house) {
            return new NotFoundHttpException();
        }
        $dataProvider = new ActiveDataProvider();
        $dataProvider->query = House::find()->where(['parent_id' => $house_id]);
        $dataProvider->setSort(false);
        return $this->render('detail', get_defined_vars());
    }

    /**
     * @param $id
     * @return Project
     * @throws NotFoundHttpException
     * Description:
     */
    private function findProject($id){
        $model = Project::findOne(['house_id' => $id]);
        if(!$model){
            throw new NotFoundHttpException();
        }
        return $model;
    }

    public function actionUpdateHouse($id)
    {
        $model = House::findOrCreate($id);
        $houseRelevanceModel = HouseRelevance::findOrCreate($id);
        if ($this->isPost) {
            $originPostData = $this->post('origin');
            $houseRelevanceModelPostData = $this->post($houseRelevanceModel->formName());
            if ($model->load($this->post()) && $model->save()) {
                if ($houseRelevanceModelPostData['house_id'] && $houseRelevanceModelPostData['with_house_id']) {
                    if ($houseRelevanceModel->load($this->post()) && $houseRelevanceModel->save()) {
                        $this->houseReInsertOrUpdate($originPostData, $houseRelevanceModelPostData);
                    }
                }
                $this->setFlashSuccess();
                return $this->backRedirect();
            } else {
                $this->setFlashErrors($model->getErrors());
            }
        }
        return $this->render('update-house', get_defined_vars());
    }

    public function actionUpdateOrdering($house_id)
    {
        $model = House::findOne($house_id);
        if (!$model) {
            return $this->renderJsonFail("找不到对应的数据");
        }

        $model->house_id = $house_id;
        $model->ordering = $this->get('ordering');
        if ($model->save()) {
            return $this->renderJsonSuccess([
                'ordering' => $this->get('ordering'),
            ]);
        } else {
            return $this->renderJsonFail("更新排序值失败");
        }
    }

    /**
     * @author HQM 2018/11/26
     * 下载小程序二维码
     * @param $projectId
     */
    public function actionDownloadQrcode($projectId)
    {
        $project = Project::findOne(['house_id' => $projectId]);
        $qrcode = \Yii::getAlias($project->mp_qrcode);

        $fileName = $project->house_name . '小程序.jpg';
        header('Content-Type: image/jpeg');
        header('Content-Disposition: attachment; filename='.$fileName);
        ob_clean();
        flush();
        readfile($qrcode);

        exit(0);
    }

    private function SyncProjectHouse(&$houseIds, $projectId, $times=0)
    {
        static $done=[];
        if($times > 30){
            return $done;
        }
        reset($houseIds);
        $houseId = current(array_keys($houseIds));
        $res = (new NewWindow())->houseStructure($houseId);
        unset($houseIds[$houseId]);
        $done[]=$houseId;
        if (isset($res['Response']['Data']['NWRespCode'])) {
            if ($res['Response']['Data']['NWRespCode'] == '0000') {
                foreach ($res['Response']['Data']['Record'] as $row) {
                    $this->saveHouse($row, $houseId, $projectId);
                    if($houseId != $row['HouseID'])
                        $houseIds[$row['HouseID']]=1; //这个id加入到队列，获取下级
                    if (isset($res['Response']['Data']['lsChild']) && is_array($res['Response']['Data']['lsChild'])) {
                        foreach ($res['Response']['Data']['lsChild'] as $v)
                            $houseIds[$v['HouseID']]=1; //增加该houseId 入队
                    }
                }
            }elseif($res['Response']['Data']['NWRespCode'] == 'Cannot find column [ParentID].'){
            }
        }else{
            throw new ErrorException("no data");
        }
        if(sizeof($houseIds) > 0){
            $this->SyncProjectHouse($houseIds, $projectId, ++$times);
        }
        //save成功后，如果队列[houseId]还有元素，则递归进行
        return $done;
    }

    private function saveHouse($row, $parentId, $projectId)
    {
        $house = House::findOrCreate($row['HouseID']);
        if (empty($house->parent_id))
            $house->parent_id = $row['Level'] == 1 ? 0 : $parentId;
        $house->project_house_id = $projectId;
        $house->house_name = $row['HouseName'];
        $house->ancestor_name = $row['AncestorName'];
        $house->reskind = $row['Reskind'];
        $house->room_status = $row['RoomStatus'] . "";
        $house->room_status_name = $row['RoomStatusName'];
        $house->belong_floor = trim($row['BelongFloor']);
        $house->level = $row['Level'];
        $house->deepest_node = $row['DeepestNode'];
        $house->show_status = 0;

        if (!$house->save()) {
            throw new ErrorException(serialize($house->getErrors()));
        }
    }

    public function actionEditStructure($id)
    {
        $projectModel = Project::findOne(['house_id' => $id]);
        $model = ProjectHouseStructure::find()->where(['project_house_id' => $id])->one();

        if ($projectModel && !$model) {
            $findData = ProjectHouseStructure::find()->select(['parent_reskind', 'name', 'reskind', 'type', 'group'])
                ->where(['project_house_id' => 0])
                ->all();

            if (!$findData) {
                return new NotFoundHttpException();
            } else {
                    $rows = [];
                    $houseStructuresCount = count($findData);
                    if ($houseStructuresCount == 14) {
                        foreach ($findData as $key2 => $value2) {
                            $rows[$key2][] = $value2['parent_reskind'];
                            $rows[$key2][] = $value2['name'];
                            $rows[$key2][] = $value2['reskind'];
                            $rows[$key2][] = $value2['type'];
                            $rows[$key2][] = $value2['group'];
                            $rows[$key2][] = $id;
                        }
                    }
                    if(!empty($rows)){
                        \Yii::$app->db->createCommand()->batchInsert('project_house_structure', ['parent_reskind', 'name', 'reskind', 'type', 'group', 'project_house_id'], $rows)->execute();
                    }

                }
        }

        $dataProvider = new ActiveDataProvider();
        $dataProvider->query = ProjectHouseStructure::find()->where(['project_house_id' => $id]);
        $dataProvider->setSort(false);
        return $this->render('edit-structure', get_defined_vars());
    }

    public function actionUpdateStructure($id)
    {
        $model = $this->findProjectStructure($id);

        if ($this->isPost) {
            if ($model->load($this->post()) && $model->save()) {
                $this->setFlashSuccess();
                return $this->backRedirect();
//                return $this->renderJsonSuccess(0);
            } else {
                $this->setFlashErrors($model->getErrors());
//                $this->renderJsonFail($model->getErrors());
            }
        }
        return $this->render('update-structure', get_defined_vars());
    }

    public function actionQueryHouseChild($id)
    {
        $list = House::find()->where(['parent_id' => $id])->asArray()->select('house_id, house_name, reskind, project_house_id, parent_id')->all();
        return $this->renderJsonSuccess(['list' => $list]);
    }

    public function actionHouseQuery($houseId, $group = 1)
    {
        /**
         * @var $house House
         */
        $house = House::findOne($houseId);
        if (!$house) {
            return $this->renderJsonFail("找不到对应的数据");
        } else {
            $res = array_filter($house->showChild, function ($row) use ($group) {
                $needs = $group == 1 ? [2, 3, 4, 5, 6, 7, 8] : [2, 9, 10, 11];
                return in_array($row->reskind, $needs);
            });
            ArrayHelper::multisort($res, ['ordering', 'house_id'], [SORT_DESC, SORT_ASC]);
            $list = [];
            foreach ($res as $key => $row) {
                $list[] = [
                    'house_id' => $row['house_id'],
                    'house_name' => $row['house_alias_name'] ? $row['house_alias_name'] : $row['house_name'],
                ];

            }
            $structure = ProjectHouseStructure::find()
                ->where(['project_house_id' => $house->project_house_id, 'group' => $group, 'type' => 1])
                ->orderBy('ordering DESC')->select('name')
                ->asArray()->all();
            $structure = ArrayHelper::getColumn($structure, 'name');
            return $this->renderJsonSuccess([
                'list' => $list,
                'structure' => $structure
            ]);
        }
    }

    protected function findHouseParentIds($houseId, &$resArr = [])
    {
        $res = House::find()->select(['parent_id', 'house_id'])->where(['house_id' => $houseId])->asArray()->one();
        if ($res['parent_id'] != 0) {
            $resArr[] = $res['parent_id'];
            $this->findHouseParentIds($res['parent_id'], $resArr);
        }

        return $resArr;
    }

    protected function findProjectStructure($id)
    {
        $model = ProjectHouseStructure::findOne($id);

        if (!$model) {
            return new NotFoundHttpException();
        }
        return $model;
    }

    protected function houseReInsertOrUpdate($postData, $houseRelevanceModelData)
    {
        $model = HouseRelevance::find()->where(['house_id' => $postData['WithHouseId'], 'with_house_id' => $postData['HouseId']])->one();

        if ($model) {
            /**
             * @var $model HouseRelevance
             */
            $model->house_id = $houseRelevanceModelData['with_house_id'];
            $model->with_house_id = $houseRelevanceModelData['house_id'];
            return $model->save();
        } else {

            $model = new HouseRelevance();
            $model->house_id = $houseRelevanceModelData['with_house_id'];
            $model->with_house_id = $houseRelevanceModelData['house_id'];
            return $model->insert();
        }
    }

    protected function projectRegionCache($ex=7200)
    {
        $key = 'projectRegionLists';
        $projectRegionLists = FileCache::init()->get($key);
        if(empty($projectRegionLists)){
            $projectRegionLists = ProjectRegion::find()->select('id, name')->asArray()->all();
            FileCache::init()->set($key, $projectRegionLists, $ex);
        }

        return $projectRegionLists;
    }

}