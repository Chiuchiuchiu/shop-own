<?php
/**
 * Created by PhpStorm.
 * User: zhaowenxi
 * Date: 2018/9/20
 * Time: 10:59
 */

namespace apps\api\controllers;

use common\models\Article;
use common\models\Banner;
use common\models\ProjectParkingOneToOne;

class IndexController extends Controller
{
    public $modelClass = 'apps\api\models\Member';

    public function actions()
    {
        $actions =  parent::actions();

        unset($actions['index']);
        unset($actions['delete']);
        unset($actions['update']);
        unset($actions['create']);
        unset($actions['view']);

        return $actions;
    }

    /**
     * 首页
     * @author zhaowenxi
     */
    public function actionIndex(){

        $res = ['banner' => [], 'article' => [], 'specialMenu' => []];

        //banner
        $res['banner'] = Banner::find()->select(['id', 'pic', 'url'])->orderBy('id DESC')->asArray()->all();
        if($res['banner']){
            foreach ($res['banner'] as &$v){
                $v['pic'] = \Yii::getAlias($v['pic']);
            }
        }

        //文章
        $article1 = Article::find()->select(["CONCAT('最新') AS categoryName,id,pic,title,FROM_UNIXTIME(`post_at`, '%Y-%m-%d') AS date"])
            ->where(['project_id' => 0, 'status' => Article::STATUS_ACTIVE])
            ->orderBy('post_at DESC')->limit(10)->asArray()->all();
        if($article1){
            foreach ($article1 as &$v1){
                $v1['pic'] = \Yii::getAlias($v1['pic']);
            }
        }

        $article2 = Article::find()->select(["CONCAT('动态') AS categoryName,id,pic,title,FROM_UNIXTIME(`post_at`, '%Y-%m-%d') AS date"])
            ->where(['project_id' => 0, 'category_id' => 426, 'status' => Article::STATUS_ACTIVE])
            ->orderBy('post_at DESC')->limit(10)->asArray()->all();
        if($article2){
            foreach ($article2 as &$v2){
                $v2['pic'] = \Yii::getAlias($v2['pic']);
            }
        }

        $article3 = Article::find()->select(["CONCAT('活动') AS categoryName,id,pic,title,FROM_UNIXTIME(`post_at`, '%Y-%m-%d') AS date"])
            ->where(['project_id' => 0, 'category_id' => 563, 'status' => Article::STATUS_ACTIVE])
            ->orderBy('post_at DESC')->limit(10)->asArray()->all();
        if($article3){
            foreach ($article3 as &$v3){
                $v3['pic'] = \Yii::getAlias($v3['pic']);
            }
        }

        $res['article'] = [
            'categories' => ['最新', '动态', '活动'],
            'list' => [$article1, $article2, $article3]
        ];

        //固定的导航栏
        $res['menu'] = [
            [
                "name"=> "物业缴费",
                "icon"=> "https://www.51homemoney.com/static/images/ico/ico1.png",
                "url"=> "/pages/chooseBill/main"
            ],
            [
                "name"=> "报事报修",
                "icon"=> "https://www.51homemoney.com/static/images/ico/ico2.png",
                "url"=> "/pages/repairList/main?flow_style=w&status=0"
            ],
            [
                "name"=> "投诉/建议",
                "icon"=> "https://www.51homemoney.com/static/images/ico/ico3.png",
                "url"=> "/pages/repairList/main?flow_style=8&status=0"
            ],
            [
                "name"=> "物业动态",
                "icon"=> "https://www.51homemoney.com/static/images/ico/ico4.png",
                "url"=> "/pages/articleList/main"
            ],
            [
                "name"=> "业主中心",
                "icon"=> "https://www.51homemoney.com/static/images/ico/ico11.png",
                "url"=> "/pages/owner-center/main?id=34"
            ],
//            [
//                "name"=> "话费充值",
//                "icon"=> "https://www.51homemoney.com/static/images/ico/ico6.png",
//                "url"=> "/pages/life-server/main?id=34"
//            ]
        ];


        // ------feng--0429--add ---跳转第三方小程序（不能超过10个）----
        $res['thirdmenu'] = [
            [
                "name"=> "去旅游",
                "icon"=> "https://www.51homemoney.com/static/images/ico/jzy_logo.png",
                "appId"=> "wxd455640d23500538",
                "path"=> "pages/home/dashboard/index",
                "path2"=> "packages/ump/coupon-pack/fetch/index?alias=zh2g3sy0",           // 大礼包领取页面
                "expect_time"=>"1564588799000"             // 活动过期时间
            ],
        ];



        $res['notice'] = '最新消息，新认证房产有红包，最高享200元。活动时间：2019.01~2019.12';

        return $this->renderJsonSuccess(200, $res);
    }

    /**
     * 首页
     * @author feng
     * @time 2019/9/12
     */
    public function actionIndexNew(){
        $_get = $this->get();
        $projectId = $_get['projectId'];


        $res = ['banner' => [], 'article' => [], 'specialMenu' => []];


        //banner
        $res['banner'] = Banner::find()->where(['like','projects',$projectId])->select(['id', 'pic', 'url'])->orderBy('id DESC')->asArray()->all();
        if($res['banner']){
            foreach ($res['banner'] as &$v){
                $v['pic'] = \Yii::getAlias($v['pic']);
            }
        }

        //文章
        $article1 = Article::find()->select(["CONCAT('最新') AS categoryName,id,pic,title,FROM_UNIXTIME(`post_at`, '%Y-%m-%d') AS date"])
            ->where(['project_id' => 0, 'status' => Article::STATUS_ACTIVE])
            ->orderBy('post_at DESC')->limit(10)->asArray()->all();
        if($article1){
            foreach ($article1 as &$v1){
                $v1['pic'] = \Yii::getAlias($v1['pic']);
            }
        }

        $article2 = Article::find()->select(["CONCAT('动态') AS categoryName,id,pic,title,FROM_UNIXTIME(`post_at`, '%Y-%m-%d') AS date"])
            ->where(['project_id' => 0, 'category_id' => 426, 'status' => Article::STATUS_ACTIVE])
            ->orderBy('post_at DESC')->limit(10)->asArray()->all();
        if($article2){
            foreach ($article2 as &$v2){
                $v2['pic'] = \Yii::getAlias($v2['pic']);
            }
        }

        $article3 = Article::find()->select(["CONCAT('活动') AS categoryName,id,pic,title,FROM_UNIXTIME(`post_at`, '%Y-%m-%d') AS date"])
            ->where(['project_id' => 0, 'category_id' => 563, 'status' => Article::STATUS_ACTIVE])
            ->orderBy('post_at DESC')->limit(10)->asArray()->all();
        if($article3){
            foreach ($article3 as &$v3){
                $v3['pic'] = \Yii::getAlias($v3['pic']);
            }
        }

        $res['article'] = [
            'categories' => ['最新', '动态', '活动'],
            'list' => [$article1, $article2, $article3]
        ];

        //固定的导航栏
        $res['menu'] = [
            [
                "name"=> "物业缴费",
                "icon"=> "https://www.51homemoney.com/static/images/ico/ico1.png",
                "url"=> "/pages/chooseBill/main"
            ],
            [
                "name"=> "报事报修",
                "icon"=> "https://www.51homemoney.com/static/images/ico/ico2.png",
                "url"=> "/pages/repairList/main?flow_style=w&status=0"
            ],
            [
                "name"=> "投诉/建议",
                "icon"=> "https://www.51homemoney.com/static/images/ico/ico3.png",
                "url"=> "/pages/repairList/main?flow_style=8&status=0"
            ],
            [
                "name"=> "物业动态",
                "icon"=> "https://www.51homemoney.com/static/images/ico/ico4.png",
                "url"=> "/pages/articleList/main"
            ],
            [
                "name"=> "业主中心",
                "icon"=> "https://www.51homemoney.com/static/images/ico/ico11.png",
                "url"=> "/pages/owner-center/main?id=34"
            ],
        ];


        // ------feng--0429--add ---跳转第三方小程序（不能超过10个）----
        $res['thirdmenu'] = [
            [
                "name"=> "去旅游",
                "icon"=> "https://www.51homemoney.com/static/images/ico/jzy_logo.png",
                "appId"=> "wxd455640d23500538",
                "path"=> "pages/home/dashboard/index",
                "path2"=> "packages/ump/coupon-pack/fetch/index?alias=zh2g3sy0",           // 大礼包领取页面
                "expect_time"=>"1564588799000",             // 活动过期时间
                "type"=> 2,
            ],
        ];


        // 优泊到家
        $type = 3;
        $packingList= ProjectParkingOneToOne::find()->where(['project_house_id'=>$projectId,'type'=>$type])->select(["project_house_id,name,pic,app_id"])->all();
        if(!empty($packingList)){
            $vo = $packingList[0];

            $packing = [
                "name"=> $vo['name'],
                "icon"=> \Yii::getAlias($vo['pic']),
                "path"=> "pages/index/index",
                "appId"=> $vo['app_id'],
                "type"=> 4,
            ];
            array_push($res['thirdmenu'],$packing);

        }
        // ----end----

        $res['notice'] = '最新消息，新认证房产有红包，最高享200元。活动时间：2019.01~2019.12';

        return $this->renderJsonSuccess(200, $res);
    }


    /**
     * 上传图片
     * @return string
     * @author zhaowenxi
     */
    public function actionUpload()
    {
        $base64_string = $this->post('base64_string');
        $base64_string = base64_decode($base64_string);
        $name = md5(uniqid() . time() . rand(0, 9999999));
        $name = strtoupper(base_convert(substr($name, rand(0, 24), 8), 16, 32)) . '.jpg';
        $path = '/' . date('Wy');
        $savePath = \Yii::getAlias(\Yii::$app->params['attached.path']) . 'public' . $path;
        if (!file_exists($savePath)) {
            @mkdir($savePath);
        }
        if (!is_dir($savePath)) {
            return $this->renderJsonFail(41000);
        }
        file_put_contents($savePath . '/' . $name, $base64_string);
        $savePath = '@cdnUrl/' . $path . '/' . $name;
        return $this->renderJsonSuccess(200, ['url' => \Yii::getAlias($savePath), 'saveUrl' => $savePath]);
    }

    /**
     * 默认404接口
     * @author zhaowenxi
     */
    public function actionError(){

        return $this->renderJsonFail(40004);

    }
}