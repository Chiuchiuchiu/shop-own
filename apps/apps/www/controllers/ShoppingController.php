<?php
 
namespace apps\www\controllers;
use common\models\Ad;
use common\models\Project;
use apps\api\models\MemberHouse;

use apps\www\module\viewShop\shopApi;
/*
* 商城
*/
class ShoppingController extends Controller
{
  public $enableCsrfValidation = false;

    public function actionIndex()
    {
        $projectId     = isset($this->project->house_id) ? $this->project->house_id : null;

        // 入驻商家
        $_project = Project::find()->where(['house_id'=>$projectId])->all();
        $_url_key = $_project[0]['url_key'];

        $shopList = [];
        $ser = new shopApi();
        $result = $ser->getShopList($projectId);


        if($result['code'] == 200) {
            $shopList = $result['data'];
        }
        $kw = "";
        $_get = $this->get();
        if(!empty($_get)){
            $kw = $_get['kw'];
        }
        // 推荐商品
        $reGoods = [];
        $grlt = $ser->getReGoodsList($projectId,$kw);
        if($grlt['code'] == 200){
            $reGoods = $grlt['data'];
        }
  //  var_dump($reGoods);
        //------------------
        $ad_list = Ad::find()
            ->where(['type'=>3])
            ->andWhere(['<', 'start_time', time()])
            ->andWhere(['>', 'end_time', time()])
            ->andWhere(['like', 'projects', ",".$projectId .","])
            ->select("id,title,pic,url,diy_json")
            ->all();
    //  var_dump($ad_list);die;
        //------
        $memHouse = MemberHouse::findOne(['member_id' => $this->user->id]);
        $houseID  = 0;
        if ($memHouse != null) {
            $houseID = $memHouse['house_id'];
        }


        return $this->renderPartial('index', [
            'projectInfo'           => $this->project,
            'ad_list'               => $ad_list,
            'shopList'              => $shopList,
            'pk'                    => $_url_key,
            'pid'                   => $projectId,
            'reGoods'               => $reGoods,
            'houseID'               => $houseID,
            'currentPage'           => 'shopping',
            'shopping_host'         => 'https://shop.51homemoney.com',
            'kw'                    => $kw,
            'cdj_header_tip'       =>$this->CDJ_TIP,
        ]);

    }

}