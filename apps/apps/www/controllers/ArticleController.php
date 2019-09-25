<?php
/**
 * Created by
 * Author: zhao
 * Time: 2016/11/24 17:44
 * Description:
 */

namespace apps\www\controllers;


use common\models\Article;
use common\models\ArticleCategory;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;

class ArticleController extends Controller
{
    public function actionIndex($id)
    {
        $article = Article::findOne($id);
        return $this->render('index', ['article' => $article]);
    }

    public function actionList($id=null,$pid=null)
    {
        if ($id === null) {
            if ($this->project || $pid!==null) {
                $res = ArticleCategory::findOne(['project_id' =>$pid===null?$this->project->house_id:$pid]);
                $id = $res->id;
            }else{
                $id=1;
            }
        }
        $category = ArticleCategory::findOne($id);
        if (!$category) {
            throw new NotFoundHttpException();
        }
        $categoryList = ArticleCategory::find()
            ->where([
                'status' => ArticleCategory::STATUS_ACTIVE,
                'project_id' => $category->project_id
            ])->all();
        $dataProvider = new ActiveDataProvider();
        $dataProvider->query = Article::find()
            ->where(['category_id' => $id,'status'=>Article::STATUS_ACTIVE])
            ->orderBy('post_at DESC');
        if ($this->isAjax && $this->get('page')) {
            return $this->renderPartial('list-cell', ['dataProvider' => $dataProvider]);
        } else {
            return $this->render('list', [
                'id' => $id,
                'category' => $category,
                'categoryList' => $categoryList,
                'dataProvider' => $dataProvider
            ]);
        }
    }
}