<?php
/**
 * 北京中税电子发票接口
 * Created by PhpStorm.
 * User: HQM
 * Date: 2017/3/18
 * Time: 11:07
 */

namespace components\Tcis;

use yii\base\ErrorException;

class Tcis
{
    protected $pkey = null;
    protected $url = null;
    protected $advserUrl = '';
    protected $info = [];
    protected $error = [];
    protected $success = [1, 3];

    private $appId;
    private $appSecret;

    public const P_KEY = 'Tcis_Info';

    /**
     * Tcis constructor.
     * @param null $appId
     * @param null $appSecret
     * @throws ErrorException
     */
    public function __construct($appId=null, $appSecret=null)
    {
        $this->url = \Yii::$app->params['tcis']['host'] . \Yii::$app->params['tcis']['url'];
        $this->advserUrl = \Yii::$app->params['tcis']['host'] . \Yii::$app->params['tcis']['advser_url'];
        $this->appId = empty($appId) ? \Yii::$app->params['tcis']['appId'] : $appId;
        $this->appSecret = empty($appSecret) ? \Yii::$app->params['tcis']['appSecret'] : $appSecret;

        $reciptInfo = \Yii::$app->cache->get(self::P_KEY . $this->appSecret);

        if(empty($reciptInfo)){
            $res = $this->getActsign($this->appId, $this->appSecret);
            if($res){
                $this->pkey = $res['map']['pkey'];
                $this->info = $res;
            }
        } else {
            $this->pkey = $reciptInfo['map']['pkey'];
            $this->info = $reciptInfo;
        }
    }

    /**
     * 开具电子发票
     * @param $gfmc
     * @param $gfdzdh
     * @param $jehj
     * @param array $spobj
     * @param array $array
     * @param string $skr
     * @param string $fhr
     * @return mixed
     * @throws ErrorException
     */
    public function getOpenfp($gfmc, $gfdzdh, $jehj, $spobj=[], $array=[], $skr='陆雨君', $fhr='陆雨君')
    {
        $asInfo = $this->getInfo();

        $data = [
            'taxType' => 0,
            'xfnsrsbh' => $asInfo['map']['nsrsbh'],
            'gfnsrsbh' => '',
            'gfmc' => $gfmc,
            'gfyhmczh' => '',
            'gfdzdh' => $gfdzdh,
            'jehj' => $jehj,
            'spobj' => $spobj,
            'phone' => '',
            'email' => '',
            'wechat' => '',
            'kpr' => '',
            'skr' => $skr,
            'fhr' => $fhr,
            'memo' => '',
            'pkey' => $this->pkey,
            'type' => '00XX0200',
        ];

        if(is_array($array)) {
            $data = array_merge($data, $array);
        }

        $httpUrl = $this->advserUrl . 'inv/openfp';
        $postResult = $this->post($httpUrl, $data);

        return $postResult;
    }

    /**
     * 开具电子发票 （带回调地址）
     * @param $gfmc
     * @param $gfdzdh
     * @param $jehj
     * @param array $spobj
     * @param array $array
     * @param string $kprId
     * @return mixed
     * @throws ErrorException
     */
    public function getAdvSerOpenfp($gfmc, $gfdzdh, $jehj, $spobj=[], $array=[], $kprId='91440113562297459M')
    {
        $asInfo = $this->getInfo();

        $data = [
            'taxType' => 0,
            'xfnsrsbh' => $asInfo['map']['nsrsbh'],
            'gfnsrsbh' => '',
            'gfmc' => $gfmc,
            'gfyhmczh' => '',
            'gfdzdh' => $gfdzdh,
            'jehj' => $jehj,
            'spobj' => $spobj,
            'phone' => '',
            'email' => '',
            'wechat' => '',
            'kpr' => '',
            'skr' => '陆雨君',
            'fhr' => '陆雨君',
            'memo' => '',
            'pkey' => $this->pkey,
            'type' => '00XX0200',
            'kprId' => $kprId,
            'sendUrl' => \Yii::$app->request->hostInfo . "/tcis/fpzz-notify",
        ];

        if(is_array($array)) {
            $data = array_merge($data, $array);
        }

        $httpUrl = $this->advserUrl . 'st/openfp';
        $postResult = $this->post($httpUrl, $data);

        return $postResult;
    }

    /**
     * 查询开票结果
     * @param string $id
     * @return mixed
     * @throws ErrorException
     */
    public function getQueryKpResult(string $id )
    {
        $asInfo = $this->getInfo();

        $data = [
            'id' => $id,
            'nsrsbh' => $asInfo['map']['nsrsbh'],
            'pkey' => $this->pkey,
        ];

        $httpUrl = $this->advserUrl . 'inv/queryKpResult';

        return $this->post($httpUrl, $data);
    }

    /**
     * 查询企业所有发票
     * @param $stime
     * @param $etime
     * @param int $pageNumber
     * @param int $pageSize
     * @param string $fpdm
     * @param string $fphm
     * @param array $array
     * @return mixed
     * @throws ErrorException
     */
    public function getQuestfp($stime, $etime, $pageNumber=0, $pageSize=10, $fpdm='', $fphm='', $array=[])
    {
        $asInfo = $this->getInfo();

        $data = [
            'nsrsbh' => $asInfo['map']['nsrsbh'],
            'gfsh' => '',
            'gfmc' => '',
            'stime' => $stime,
            'etime' => $etime,
            'fpdm' => $fpdm,
            'fphm' => $fphm,
            'pageNumber' => $pageNumber,
            'pageSize' => $pageSize,
            'pkey' => $this->pkey,
        ];

        if(is_array($array)){
            $data = array_merge($data, $array);
        }

        $httpUrl = $this->advserUrl . 'inv/questfp';
        return $this->post($httpUrl, $data);
    }

    /**
     * 提取发票电子版式文件
     * @param string $fpdm
     * @param string $fphm
     * @return mixed
     * @throws ErrorException
     */
    public function getFp(string $fpdm, string $fphm)
    {
        $asInfo = $this->getInfo();

        $data = [
            'nsrsbh' => $asInfo['map']['nsrsbh'],
            'fpdm' => $fpdm,
            'fphm' => $fphm,
            'pkey' => $this->pkey,
        ];

        $httpUrl = $this->advserUrl . 'inv/getfp';
        return $this->post($httpUrl, $data);
    }

    /**
     * 统计发票信息
     * @param string $stime
     * @param string $etime
     * @return mixed
     * @throws ErrorException
     */
    public function getFprePort(string $stime,string $etime)
    {
        $asInfo = $this->getInfo();

        $data = [
            'nsrsbh' => $asInfo['map']['nsrsbh'],
            'stime' => $stime,
            'etime' => $etime,
            'pkey' => $this->pkey,
        ];

        $httpUrl = $this->advserUrl . 'inv/fpreport';
        return $this->post($httpUrl, $data);
    }

    /**
     * @param $name
     * @return mixed
     * @throws ErrorException
     */
    public function getSpmc($name)
    {
        $asInfo = $this->getInfo();
        $data = [
            'nsrsbh' => $asInfo['map']['nsrsbh'],
            'mc' => $name,
            'pkey' => $this->pkey,
        ];

        $httpUrl = $this->url . 'grspbm/selectByNsrMc';

        return $this->post($httpUrl, $data);
    }

    /**
     * @return mixed
     * @throws ErrorException
     */
    public function findAllSpbmByNsr()
    {
        $asInfo = $this->getInfo();
        $data = [
            'nsrsbh' => $asInfo['map']['nsrsbh'],
            'pkey' => $this->pkey,
        ];

        $httpUrl = $this->url . 'grspbm/findAllSpbmByNsr';
        return $this->post($httpUrl, $data);
    }

    /**
     * 系统签到 TokenKey
     * @param string $appId 用户AS账号
     * @param string $appSecret    用户AS密码
     * @return bool|mixed
     * @throws ErrorException
     */
    public function getActsign(string $appId, string $appSecret)
    {
        $data = [
            'appId' => $appId,
            'appSecret' => $appSecret,
        ];
        $httpUrl = $this->advserUrl . 'getToken';
        $res = $this->post($httpUrl, $data);

        if($res['map']){
            \Yii::$app->cache->set(self::P_KEY . $this->appSecret, $res, 400);
            return $res;
        } else {
            throw new ErrorException(serialize($res));
        }
    }

    public function getReciptInfo()
    {
        return $this->info;
    }

    public function getError()
    {
        return $this->error;
    }

    private function getInfo()
    {
        if(empty($this->info)){
            throw new ErrorException('数据为空');
        }

        return $this->info;
    }

    private function post($url, $data, $dataFormat=false)
    {
        if ($dataFormat) {
            $data = json_encode($data);
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_NOSIGNAL, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT_MS, 30000);
        $result = curl_exec($ch);
        curl_close($ch);
        $result = json_decode($result, true);

        if($result['message'] == '10002:pkey错误'){
            \Yii::$app->cache->delete(self::P_KEY . $this->appSecret);
            throw new ErrorException('服务升级中');
        }

        return $result;
    }

}