<?php
/**
 * Created by PhpStorm.
 * User: dtfeng
 * Date: 2019/4/4
 * Time: 17:14
 */

namespace apps\www\controllers;

use apps\www\models\Member;
use common\models\MemberHouse;
use common\models\SearchNotices;
use common\models\SearchNoticesFavorites;
use yii\data\ActiveDataProvider;
use yii\data\Pagination;
use yii\helpers\VarDumper;
use components\wechatSDK\WechatSDK;
use common\models\Project;

class SearchNoticesController extends Controller
{
    /**
     * 寻物启事列表页面
     * @author dtfeng
     * @Date: 2019/4/4
     * @Time: 17:41
     * @description
     * @return string
     */
    public function actionList($status = 0, $flowStyleID = 'w', $type = 0, $kw = '' )
    {
        $projectName = null;
        $hasHouse    = false;

        $model = MemberHouse::find()
        ->where([
            'member_id' => $this->user->id,
            'status' => [MemberHouse::STATUS_ACTIVE],
        ])
        ->with('house')->all();

        if ($this->project != null) {
            foreach ($model as $row) {
                /* @var $row MemberHouse */
                if ($row->house->project->house_id == $this->project->house_id) {
                    $hasHouse = $row->house_id;
                }
            }

            $projectName = $this->project->house_name;
        }

        $_where = array(
            'project_id' => $this->project->house_id,
        );
        if ($type == "1") {
            $_where['member_id'] = $this->user->id;
        }
        if ($status > 0) {
            $_where['status'] = $status - 1;
        }

        if ($kw != '') {
            $_where['title'] = array("like", $kw);
        }
        $_orderby = "status ASC,updated_at DESC";

        $_pageSize = 2;
        $_page = 0;
        $count = SearchNotices::find()->where($_where)->count();

        $pages = new Pagination(['totalCount' =>$count, 'pageSize' => $_pageSize,'page'=>$_page]);
        $_list = SearchNotices::find()->where($_where)->offset($pages->limit * $_page)->limit($pages->limit)->orderBy($_orderby);

     //   $_list = SearchNotices::find()->where($_where)->orderBy($_orderby);
        $dataProvider        = new ActiveDataProvider();
        $dataProvider->query = $_list;
        if ($this->isAjax && $this->get('page')) {
            return $this->renderPartial('list-cell', ['dataProvider' => $dataProvider]);
        } else {
            $_array = array(
                'dataProvider' => $dataProvider,
                'status'       => $status,
                'hasHouse'     => $hasHouse,
                'projectName'  => $projectName,
                'flowStyleID'  => $flowStyleID,
                'type'         => $type,
            );
            return $this->render('list', $_array);
        }
    }

    /**
     * 详情
     * @author dtfeng
     * @Date: 2019/4/9
     * @Time: 8:36
     * @description
     */
    public function actionView($id = 0)
    {
        $model  = null;
        $_where = null;

        if ($this->project != null) {
            $_where = array(
                'id'         => $id,
                'project_id' => $this->project->house_id,
            );
        }
        $model = SearchNotices::findOne($_where);
        // var_dump($model);die;
        if (!$model) {
            throw new NotFoundHttpException();
        }
        $_minfo = Member::findOne(['id' => $model->member_id]);

        $_where2    = array(
            'search_id' => $id,
            'member_id' => $this->user->id,
            'status'    => SearchNoticesFavorites::STATUS_FAVORITES,
        );
        $_favorites = SearchNoticesFavorites::findOne($_where2);
        $_isFav     = 0;
        if ($_favorites != null) {
            $_isFav = 1;
        }

        return $this->render('view', ['model' => $model, "minfo" => $_minfo, 'isFav' => $_isFav]);
    }

    /**
     * 寻物启事信息发吧
     * @author dtfeng
     * @Date: 2019/4/4
     * @Time: 17:42
     * @description
     * @return string
     */
    public function actionIndex($id = 0)
    {
        $hasHouse = false;
        $model    = MemberHouse::find()
            ->where([
                'member_id' => $this->user->id,
                'status'    => [MemberHouse::STATUS_ACTIVE],
            ])
            ->with('house')->all();

        foreach ($model as $row) {
            /* @var $row MemberHouse */
            if ($row->house->project->house_id == $this->project->house_id) {
                $hasHouse = $row->house_id;
            }
        }

        if ($this->isPost && $this->isAjax) {
            $postData = $this->post('SearchNotices');
            $goUrl    = '/search-notices/list?memberID=';
            $Id       = $postData['id'];

            $_model = SearchNotices::findOne(['id' => $Id, 'member_id' => $this->user->id]);

            if ($_model != null) {
                $_model->title        = $postData['title'];
                $_model->lose_address = $postData['lose_address'];
                $_model->describtions = $postData['describtions'];
                $_model->linkman      = $postData['linkman'];
                $_model->tel          = $postData['tel'];
                $_model->pics         = $postData['pics'];
                $_model->updated_at      = time();
                $res                  = $_model->save();
            } else {
                $_model = new SearchNotices();
                $_model->load($this->post());
                $_model->member_headimg  = $this->user->headimg;
                $_model->member_id       = $this->user->id;
                $_model->member_nickname = $this->user->nickname;
                $_model->project_id      = isset($this->project->house_id) ? $this->project->house_id : 0;
                $_model->house_id        = $hasHouse;
                $_model->type            = 1;
                $_model->status          = 0;
                $_model->created_at      = time();
                $_model->updated_at      = time();

                $res = $_model->save();
            }

            if ($res) {
                return $this->renderJsonSuccess(['goUrl' => $goUrl]);
            } else {
                return $this->renderJsonFail('error', -1, ['errorMsg' => $_model->getFirstErrors()]);
            }
        } else {
            if ($this->project != null) {
                $_where = array(
                    'id'         => $id,
                    'project_id' => $this->project->house_id,
                );
            }
            $model = SearchNotices::findOne($_where);
            if ($model == null) {
                $model = new SearchNotices();
            }
        }

        $params['_model'] = $model;
        $params['_user']  = $this->user;
        $params['treaty'] = "公约内容";            // 需要提供文案
        return $this->render("index", $params);
    }

    /**
     * 领取物件
     * @author dtfeng
     * @Date: 2019/4/13
     * @Time: 8:36
     * @description
     * @param int $id
     * @return string
     */
    public function actionReceive($id = 0)
    {
        if ($this->isPost) {
            $postData = $this->post('SearchNotices');
            $Id       = $postData['id'];

            $_model = SearchNotices::findOne(['id' => $Id, 'status' => SearchNotices::STATUS_WAIT, 'member_id' => $this->user->id]);
            if (!$_model) {
                return $this->renderJsonFail($Id);
            } else {
                $_model->receive_address = $postData['receive_address'];
                $_model->receive_remark  = $postData['receive_remark'];
                $_model->status          = SearchNotices::STATUS_RECEIVE;
            }

            if ($_model->save()) {
                return $this->renderJsonSuccess(array("code" => 1, 'message' => '更新成功'));
            } else {
                return $this->renderJsonSuccess(array("code" => 0, 'message' => '更新失败'));
            }
        } else {
            $model = SearchNotices::findOne(['id' => $id, ['member_id' => $this->user->id]]);
        }

        return $this->render('receive', ['_model' => $model, 'memberID' => $this->user->id]);
    }

    /**
     * 更新【已领】
     * @author dtfeng
     * @Date: 2019/4/11
     * @Time: 9:47
     * @description
     * @param int $id
     */
    public function actionAjax($id = 0)
    {
        $model = SearchNotices::findOne(['id' => $id, ['member_id' => $this->user->id]]);
        if (!$model) {
            throw new NotFoundHttpException();
        }

        if ($this->isPost) {
            $model->status     = SearchNotices::STATUS_RECEIVE;
            $model->updated_at = time();
            if ($model->save()) {
                return $this->renderJsonSuccess(array("code" => 1, 'message' => '更新成功'));
            } else {
                return $this->renderJsonSuccess(array("code" => 0, 'message' => '更新失败'));
            }
        }

        return $this->render('list');
    }

    /**
     * 添加/取消 收藏
     * @author dtfeng
     * @Date: 2019/4/12
     * @Time: 7:57
     * @description
     * @param int $id
     * @param int $status
     * @return string
     */
    public function actionAddFavorites($id = 0, $status = 1)
    {
        $model = SearchNotices::findOne(['id' => $id]);
        if (!$model) {
            throw new NotFoundHttpException();
        }
        if ($this->isGet) {
            $houseID     = 0;
            $modelMHouse = MemberHouse::find()
                ->where([
                    'member_id' => $this->user->id,
                    'status'    => [MemberHouse::STATUS_ACTIVE],
                ])
                ->with('house')->all();

            foreach ($modelMHouse as $row) {
                /* @var $row MemberHouse */
                if ($row->house->project->house_id == $this->project->house_id) {
                    $houseID = $row->house_id;
                }
            }
            $_where1  = array(
                'search_id' => $id,
                'member_id' => $this->user->id
            );
            $modelFav = SearchNoticesFavorites::findOne($_where1);

            if ($modelFav == null) {
                $modelFav             = new SearchNoticesFavorites();
                $modelFav->search_id  = $id;
                $modelFav->member_id  = $this->user->id;
                $modelFav->house_id   = $houseID;
                $modelFav->project_id = isset($this->project->house_id) ? $this->project->house_id : 0;
                $modelFav->status     = SearchNoticesFavorites::STATUS_FAVORITES;
                $modelFav->created_at = time();

                $res = $modelFav->save();
            } else {
                $modelFav->status = $status;
                $res              = $modelFav->save();
            }
            if ($res != false) {
                return $this->renderJsonSuccess(array("code" => 1, 'message' => '收藏成功'));
            } else {
                return $this->renderJsonSuccess(array("code" => 0, 'message' => '收藏成功'));
            }
        }
    }

    /**
     * 我的收藏
     * @author dtfeng
     * @Date: 2019/4/13
     * @Time: 14:51
     * @description
     */
    public function actionMyFavorites($status = 0, $kw = '', $type = '2')
    {
        $projectName = null;
        $hasHouse = null;

        $model = MemberHouse::find()
            ->where([
                'member_id' => $this->user->id,
                'status' => [MemberHouse::STATUS_ACTIVE],
            ])
            ->with('house')->all();

        if ($this->project != null) {
            foreach ($model as $row) {
                /* @var $row MemberHouse */
                if ($row->house->project->house_id == $this->project->house_id) {
                    $hasHouse = $row->house_id;
                }
            }

            $projectName = $this->project->house_name;
        }


        $_where = array(
            'search_notices.project_id' => $this->project->house_id,
        );

        if ($kw != '') {
            $_where['search_notices.title'] = array("like", $kw);
        }
        $_orderby = "search_notices_favorites.created_at DESC";
        //  $_list    = SearchNotices::find()->where($_where)->orderBy($_orderby);


        //-----------
        $_list = SearchNotices::find()
            ->joinWith('favorites')
            ->where(['search_notices_favorites.member_id' => $this->user->id, 'search_notices_favorites.status' => SearchNoticesFavorites::STATUS_FAVORITES])
            ->andFilterWhere($_where)
            ->orderBy($_orderby);
        //-----------

        // var_dump($_list );die;

        $dataProvider        = new ActiveDataProvider();
        $dataProvider->query = $_list;
        if ($this->isAjax && $this->get('page')) {
            return $this->renderPartial('list-cell', ['dataProvider' => $dataProvider]);
        } else {
            $hasHouse = false;
            $_array = array(
                'dataProvider' => $dataProvider,
                'hasHouse'     => $hasHouse,
                'status'       => $status,
                'projectName'  => $projectName,
                'type'         => $type,
            );
            return $this->render('list', $_array);
        }
    }


    /**
     * 测试分享
     * @author dtfeng
     * @Date: 2019/4/22
     * @Time: 14:32
     * @description
     */
    public function actionTestShare($projectId='0'){

         $this->layout = false;
        $_project = Project::findOne($projectId);
        if(!$_project){
            throw new NotFoundHttpException();
        }
        $_url = \Yii::$app->request->hostInfo . \Yii::$app->request->url;

        $_wechatJs = (new WechatSDK(\Yii::$app->params['wxPay']))->getJsSign($_url);
  //  var_dump($_wechatJs);die;
        $_array = array(
            'groupName' => $_project->house_name,
            'appId'     => $_wechatJs['appId'],
            'timestamp' => $_wechatJs['timestamp'],
            'nonceStr'  => $_wechatJs['nonceStr'],
            'signature' => $_wechatJs['signature'],
            'url'       => $_wechatJs['url'],
        );
     //  var_dump($_array);die;
        return $this->render('test-share',$_array);
    }

}