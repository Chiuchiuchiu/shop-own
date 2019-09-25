<?php
/**
 * Created by PhpStorm.
 * User: zhaowenxi
 * Date: 2018/9/20
 * Time: 10:59
 */

namespace apps\api\controllers;

use apps\api\models\Article;
use common\models\ArticleCategory;
use yii\data\ActiveDataProvider;
use yii\data\Pagination;

class ArticleController extends Controller
{
    public $modelClass = 'apps\api\models\Article';

    public function actions()
    {
        $actions =  parent::actions();

        unset($actions['index']);
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['save']);

        return $actions;
    }

    /**
     * 文章列表
     * @author HQM 2018/11/28
     * @param int $id
     * @return string
     */
    public function actionList($id = 0)
    {
        $res = ['info' => []];

        if(empty($id)){
            return $this->renderJsonFail(40010);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => Article::find()
                ->where(['category_id' => $id,'status' => Article::STATUS_ACTIVE])
                ->orderBy('post_at DESC'),
            'pagination' => new Pagination([
                'validatePage' => false,
            ]),
        ]);

        foreach ($dataProvider->getModels() as $v){
            $res['info'][] = [
                'id' => $v->id,
                'pic' => \Yii::getAlias($v->pic),
                'title' => $v->title,
                'showType' => $v->show_type,
                'date' => date("Y-m", $v->post_at),
                'content' => $v->summary,
            ];
        }

        return $this->renderJsonSuccess(200, $res);
    }

    /**
     * 文章分类
     * @author HQM 2018/11/28
     * @param int $projectId
     * @return string
     */
    public function actionCategory($projectId = 0)
    {
        $res = ArticleCategory::find()
            ->select('id,name')
            ->where(['project_id' => $projectId, 'status' => ArticleCategory::STATUS_ACTIVE])
            ->asArray()
            ->all();

        if(!$res){
            $res = ArticleCategory::find()
                ->select('id,name')
                ->where(['project_id' => 0, 'status' => ArticleCategory::STATUS_ACTIVE])
                ->asArray()
                ->all();
        }

        return $this->renderJsonSuccess(200, $res);
    }

    /**
     * 文章详情
     * @author HQM 2018/11/28
     * @param $id
     * @return string
     */
    public function actionDetail($id=0)
    {
        $res = [];

        if(empty($id)){
            return $this->renderJsonFail(40010);
        }

        $data = Article::findOne($id);

        if($data){

            $res = [
                'id' => $data->id,
                'title' => $data->title,
                'pic' => \Yii::getAlias($data->pic),
                'date' => date("Y-m-d", $data->post_at),
                'content' => $data->content,
                'projectName' => $data->projectName,
            ];
        }

        return $this->renderJsonSuccess(200, $res);
    }
}