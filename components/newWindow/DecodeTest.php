<?php
namespace components\newWindow;


/**
 * Created by
 * Author: zhao
 * Time: 2016/11/10 16:30
 * Description:
 */
class DecodeTest
{
    const KET_STR = '01234567890123456789012345678901';

    private $username;
    private $password;
    private $host;

    const LOGIN_KEY_NAME = 'new_window_login_keyds';

    private $loginKey = 'new_window_login_keyds';

    public function __construct()
    {
        error_reporting(E_ERROR | E_WARNING);//由于使用了过时的加密代码，需要屏蔽错误
        $this->password = \Yii::$app->params['newWindow.password'];
        $this->username = \Yii::$app->params['newWindow.username'];
        $this->host = \Yii::$app->params['newWindow.host'];

        /*$this->loginKey = \Yii::$app->cache->get(self::LOGIN_KEY_NAME);
        if (empty($this->loginKey))
            $this->loginKey = $this->login();
        if (empty($this->loginKey)) {
            throw new ErrorException("login error");
        }*/

    }

    private function login()
    {
        $res = $this->consolePost([
            'Request' => [
                'Head' => [
                    'NWVersion' => '01',
                    'NWCode' => 1,
                    'NWGUID' => substr(md5(microtime()), 3, 20),
                    'NWExID' => '',
                ],
                'Data' => [
                    'Account' => $this->username,
                    'PassWord' => $this->password,
                    'Mac' => 'null'
                ]
            ]
        ]);

        return $res;
    }

    public function consoleLogin()
    {
        $res = $this->consolePost([
            'Request' => [
                'Head' => [
                    'NWVersion' => '01',
                    'NWCode' => 1,
                    'NWGUID' => substr(md5(microtime()), 3, 20),
                    'NWExID' => '',
                ],
                'Data' => [
                    'Account' => $this->username,
                    'PassWord' => $this->password,
                    'Mac' => 'null'
                ]
            ]
        ]);

        return $res;
    }

    /**
     * 查询订单明细开票状态
     * @param $chargeDetailIDList
     * @param $IsInputBill
     * @return bool|mixed|string
     */
    public function queryInvoice($chargeDetailIDList, $IsInputBill=0)
    {
        $data = [
            'Request' => [
                'Head' => [
                    'NWVersion' => '01',
                    'NWCode' => 5120,
                    'NWGUID' => substr(md5(microtime()), 3, 20),
                    'NWExID' => $this->loginKey,
                ],
                'Data' => [
                    'ChargeDetailIDList' => $chargeDetailIDList,
                    'IsInputBill' => $IsInputBill,
                ]
            ]
        ];

        $res = $this->consolePost($data);
        return $res;
    }

    public function getHouse($houseId, $MobilePhone=null, $ProjectHouseID = null)
    {
        $data = [
            'ProjectHouseID' => $houseId,
            'HouseID' => $houseId,//230978,
        ];

        if(!empty($MobilePhone)){
            $data['MobilePhone'] = $MobilePhone;
            $data['ProjectHouseID'] = $ProjectHouseID;
            unset($data['HouseID']);
        }

        $res = $this->consolePost([
            'Request' => [
                'Head' => [
                    'NWVersion' => '01',
                    'NWCode' => 2001,
                    'NWGUID' => substr(md5(microtime()), 3, 20),
                    'NWExID' => $this->loginKey,
                ],
                'Data' => $data,
            ]
        ]);
        return $res;
    }

    public function getBill($houseId, $CustomerName = null, $isGeneral = 0, $isNew = 0)
    {
        $data = [
            'HouseID' => $houseId,
            'IsGeneral' => $isGeneral,
            'IsNew' => $isNew,
        ];

        if(!is_null($CustomerName)){
            $data['CustomerName'] = $CustomerName;
        }

        $res = $this->consolePost([
            'Request' => [
                'Head' => [
                    'NWVersion' => '01',
                    'NWCode' => 5001,
                    'NWGUID' => substr(md5(microtime()), 3, 20),
                    'NWExID' => $this->loginKey,
                ],
                'Data' => $data,
            ]
        ]);
        return $res;
    }

    public function payBill($bill_no, $project_house_id, $contract_no, $amount, $payed_at)
    {
        $res = $this->consolePost([
            'Request' => [
                'Head' => [
                    'NWVersion' => '01',
                    'NWCode' => 5002,
                    'NWGUID' => substr(md5(microtime()), 3, 20),
                    'NWExID' => $this->loginKey,
                ],
                'Data' => [
                    'CompanyID' => $project_house_id,
                    'ContractNo' => $contract_no,
                    'PayAmount' => $amount,
                    'BillNo' => $bill_no,
                    'PayDate' => date('Ymd H:i:s', $payed_at),
                    'SquareTypeID' => '99',
                ]
            ]
        ]);
        return $res;
    }

    public function houseStructure($houseId)
    {
        $res = $this->consolePost([
            'Request' => [
                'Head' => [
                    'NWVersion' => '01',
                    'NWCode' => 7,
                    'NWGUID' => substr(md5(microtime()), 3, 20),
                    'NWExID' => $this->loginKey,
                ],
                'Data' => [
                    'HouseID' => $houseId,
                ]
            ]
        ]);
        return $res;
    }

    public function projectTreeStructure($projectHouseId)
    {
        $res = $this->consolePost([
            'Request' => [
                'Head' => [
                    'NWVersion' => '01',
                    'NWCode' => 6,
                    'NWGUID' => substr(md5(microtime()), 3, 20),
                    'NWExID' => $this->loginKey,
                ],
                'Data' => [
                    'ProjectID' => $projectHouseId,
                ]
            ]
        ]);

        return $res;
    }

    public function project($name)
    {
        $res = $this->consolePost([
            'Request' => [
                'Head' => [
                    'NWVersion' => '01',
                    'NWCode' => 5,
                    'NWGUID' => substr(md5(microtime()), 3, 20),
                    'NWExID' => $this->loginKey,
                ],
                'Data' => [
                    'ProjectName' => $name,
                ]
            ]
        ]);
        return $res;
    }

    public function getProjectOwnerOrTenants($PrecinctID, $Keyword, $CurrentPage = 0, $PageSize = 10)
    {
        $res = $this->consolePost([
            'Request' => [
                'Head' => [
                    'NWVersion' => '01',
                    'NWCode' => 2015,
                    'NWGUID' => substr(md5(microtime()), 3, 20),
                    'NWExID' => $this->loginKey,
                ],
                'Data' => [
                    'PrecinctID' => $PrecinctID,
                    'Keyword' => $Keyword,
                    'CurrentPage' => $CurrentPage,
                    'PageSize' => $PageSize,
                ]
            ]
        ]);
        return $res;
    }

    /**
     * 事务管理 07.	提交报事信息
     * @param array $postData
     * @return mixed|string
     */
    public function postRepair($postData)
    {

        $res = $this->consolePost([
            'Request' => [
                'Head' => [
                    'NWVersion' => '01',
                    'NWCode' => 2520,
                    'NWGUID' => substr(md5(microtime()), 3, 20),
                    'NWExID' => $this->loginKey,
                ],
                'Data' => $postData
            ]
        ]);
        return $res;
    }

    /**
     * @param integer $projectId
     * @param integer $houseId
     * @param integer $FlowID
     * @param int $CurrStepID
     * @param null $IsGetCurrStepUser
     * @return mixed|string
     */
    public function getRepairBindUser($projectId, $houseId, $FlowID, $IsGetCurrStepUser=null, $CurrStepID=1)
    {

        $postData = [
            'FlowID' => $FlowID,
            'CurrStepID' => $CurrStepID,
            'PrecinctID' => $projectId,
            'HouseID' => $houseId,
        ];

        if($IsGetCurrStepUser){
            $postData['IsGetCurrStepUser'] = $IsGetCurrStepUser;
        }

        return $this->consolePost([
            'Request' => [
                'Head' => [
                    'NWVersion' => '01',
                    'NWCode' => 602004,
                    'NWGUID' => substr(md5(microtime()), 3, 20),
                    'NWExID' => $this->loginKey,
                ],
                'Data' => $postData,
            ]
        ]);
    }

    /**
     * 16.	获取处理过程
     * @param integer $serviceId
     * @return mixed|string
     */
    public function getRepairStep($serviceId)
    {
        return $this->consolePost([
            'Request' => [
                'Head' => [
                    'NWVersion' => '01',
                    'NWCode' => 2522,
                    'NWGUID' => substr(md5(microtime()), 3, 20),
                    'NWExID' => $this->loginKey,
                ],
                'Data' => [
                    'ServiceID' => $serviceId,
                ]
            ]
        ]);
    }

    /**
     * 获取待处理报事列表
     * @param integer $projectId
     * @param string $CustomerName
     * @param string $KeyWord
     * @param string $FlowStyleID
     * @param integer $PageIndex
     * @param integer $PageSize
     * @return mixed|string
     */
    public function getTransactionList($projectId, $CustomerName, $KeyWord='', $FlowStyleID = 'w', $PageIndex = 0, $PageSize = 10)
    {
        $res = $this->consolePost([
            'Request' => [
                'Head' => [
                    'NWVersion' => '01',
                    'NWCode' => 2519,
                    'NWGUID' => substr(md5(microtime()), 3, 20),
                    'NWExID' => $this->loginKey,
                ],
                'Data' => [
                    'PrecinctID' => $projectId,
                    'CustomerName' => $CustomerName,
                    'KeyWord' => $KeyWord,
                    'FlowStyleID' => $FlowStyleID,
                    'PageIndex' => $PageIndex,
                    'PageSize' => $PageSize,
//                    'QueryType' => 0,
//                    'Site' => 2,
                ]
            ]
        ]);
        return $res;
    }

    /**
     * 事务管理 获取维修详细信息
     * @param $serviceID
     * @return mixed|string
     */
    public function getRepairDetail($serviceID)
    {
        $res = $this->consolePost([
            'Request' => [
                'Head' => [
                    'NWVersion' => '01',
                    'NWCode' => 602014,
                    'NWGUID' => substr(md5(microtime()), 3, 20),
                    'NWExID' => $this->loginKey,
                ],
                'Data' => [
                    'ServiceID' => $serviceID,
                ]
            ]
        ]);
        return $res;
    }

    /**
     * 事务管理 客户信息检索
     * @param integer $projectId
     * @param string $Keyword
     * @param int $KeywordType
     * @return mixed|string
     */
    public function getCustomerInfo($projectId, $Keyword, $KeywordType=1)
    {
        $data = [
            'Request' => [
                'Head' => [
                    'NWVersion' => '01',
                    'NWCode' => 602001,
                    'NWGUID' => substr(md5(microtime()), 3, 20),
                    'NWExID' => $this->loginKey,
                ],
                'Data' => [
                    'PrecinctID' => $projectId,
                    'Keyword' => $Keyword,
                    'KeywordType' => $KeywordType,
                ]
            ]
        ];

        $res = $this->consolePost($data);
        return $res;
    }

    /**
     * 事务管理 选择紧急程度
     * @param string $StyleID 默认（报修）w-报修、8-投诉、j-服务、k咨询
     * @return mixed|string
     */
    public function selectionOfEmergency($StyleID='w')
    {
        $data = [
            'Request' => [
                'Head' => [
                    'NWVersion' => '01',
                    'NWCode' => 602002,
                    'NWGUID' => substr(md5(microtime()), 3, 20),
                    'NWExID' => $this->loginKey,
                ],
                'Data' => [
                    'StyleID' => $StyleID,
                ]
            ]
        ];

        $res = $this->consolePost($data);
        return $res;
    }

    /**
     * 事务管理 选择报事来源和退单转单原因
     * @param string $ParamTypeID
     * 3015  报事来源 60203005 退单转单原因 60203010 是否强制拍照
     * @return mixed|string
     */
    public function selectSourceAndSingleSlipCause($ParamTypeID='3015')
    {
        $data = [
            'Request' => [
                'Head' => [
                    'NWVersion' => '01',
                    'NWCode' => 9904,
                    'NWGUID' => substr(md5(microtime()), 3, 20),
                    'NWExID' => $this->loginKey,
                ],
                'Data' => [
                    'ParamTypeID' => $ParamTypeID,
                ]
            ]
        ];

        $res = $this->consolePost($data);
        return $res;
    }

    /**
     * 事务管理 获取报事分类
     * @param string $StyleID
     * @param string $Keyword
     * @param string $ParentID
     * @return mixed|string
     */
    public function getNewspaperClassify($StyleID='w', $Keyword=null, $ParentID=null)
    {
        $data = [
            'Request' => [
                'Head' => [
                    'NWVersion' => '01',
                    'NWCode' => 602003,
                    'NWGUID' => substr(md5(microtime()), 3, 20),
                    'NWExID' => $this->loginKey,
                ],
                'Data' => [
                    'StyleID' => $StyleID,
                    'Keyword' => $Keyword,
                    'ParentID' => $ParentID,
                ]
            ]
        ];

        $res = $this->consolePost($data);
        return $res;
    }

    /**
     * 事务管理 获取步骤列表
    //     * @param int $ServiceID
     * @param integer $projectId
     * @param string $FlowStyleID
     * @return mixed|string
     */
    public function getStepList($projectId, $FlowStyleID=null)
    {
        $postData = [
            'PrecinctID' => $projectId,
            'FlowStyleID' => $FlowStyleID,
        ];

        /*if($FlowStyleID){
            $postData['FlowStyleID'] = $FlowStyleID;
            unset($postData['ServiceID']);
        }*/

        $data = [
            'Request' => [
                'Head' => [
                    'NWVersion' => '01',
                    'NWCode' => 602007,
                    'NWGUID' => substr(md5(microtime()), 3, 20),
                    'NWExID' => $this->loginKey,
                ],
                'Data' => $postData,
            ]
        ];

        $res = $this->consolePost($data);
        return $res;
    }

    /**
     * 事务管理 14.	获取当前步骤信息
     * @param int $ServiceID
     * @return mixed|string
     */
    public function getNowStepInfo($ServiceID)
    {
        $data = [
            'Request' => [
                'Head' => [
                    'NWVersion' => '01',
                    'NWCode' => 602005,
                    'NWGUID' => substr(md5(microtime()), 3, 20),
                    'NWExID' => $this->loginKey,
                ],
                'Data' => [
                    'ServiceID' => $ServiceID,
                ]
            ]
        ];

        $res = $this->consolePost($data);
        return $res;
    }

    /**
     * 事务管理 接单
     * @param int $ServiceID
     * @return mixed|string
     */
    public function orders($ServiceID)
    {
        $data = [
            'Request' => [
                'Head' => [
                    'NWVersion' => '01',
                    'NWCode' => 602006,
                    'NWGUID' => substr(md5(microtime()), 3, 20),
                    'NWExID' => $this->loginKey,
                ],
                'Data' => [
                    'ServiceID' => $ServiceID,
                ]
            ]
        ];

        $res = $this->consolePost($data);
        return $res;
    }

    /**
     * 事务管理 转单
     * @param integer $ServiceID
     * @param integer $ToUserID
     * @return mixed|string
     */
    public function singleTurn($ServiceID, $ToUserID)
    {
        $data = [
            'Request' => [
                'Head' => [
                    'NWVersion' => '01',
                    'NWCode' => 602009,
                    'NWGUID' => substr(md5(microtime()), 3, 20),
                    'NWExID' => $this->loginKey,
                ],
                'Data' => [
                    'ServiceID' => $ServiceID,
                    'ToUserID' => $ToUserID,
                ]
            ]
        ];

        $res = $this->consolePost($data);
        return $res;
    }

    /**
     * 事务管理 退单
     * @param $ServiceID
     * @return mixed|string
     */
    public function backOrder($ServiceID)
    {
        $data = [
            'Request' => [
                'Head' => [
                    'NWVersion' => '01',
                    'NWCode' => 602010,
                    'NWGUID' => substr(md5(microtime()), 3, 20),
                    'NWExID' => $this->loginKey,
                ],
                'Data' => [
                    'ServiceID' => $ServiceID,
                    /*'CurrStepID' => '',
                    'Explain' => '',
                    'ToStepID' => '',*/
                ]
            ]
        ];

        $res = $this->consolePost($data);
        return $res;
    }

    /**
     * 事务管理 获取评价信息（暂缓）
     * @param $ServiceID
     * @return bool|mixed|string
     */
    public function getEvaluationInfo($ServiceID)
    {
        $data = [
            'Request' => [
                'Head' => [
                    'NWVersion' => '01',
                    'NWCode' => 602008,
                    'NWGUID' => substr(md5(microtime()), 3, 20),
                    'NWExID' => $this->loginKey,
                ],
                'Data' => [
                    'ServiceID' => $ServiceID,
                ]
            ]
        ];

        $res = $this->consolePost($data);
        return $res;
    }

    /**
     * 事务管理 提交升级流程
     * @param integer $ServiceID
     * @param int $UpgradeType
     * @return mixed|string
     */
    public function postRepairUpdate($ServiceID, $UpgradeType)
    {
        $data = [
            'Request' => [
                'Head' => [
                    'NWVersion' => '01',
                    'NWCode' => 602012,
                    'NWGUID' => substr(md5(microtime()), 3, 20),
                    'NWExID' => $this->loginKey,
                ],
                'Data' => [
                    'ServiceID' => $ServiceID,
                    'UpgradeType' => $UpgradeType,
                    /*'UpgradeType' => '',
                    'ServiceTypeID' => '',*/
                ]
            ]
        ];

        $res = $this->consolePost($data);
        return $res;
    }

    /**
     * 事务管理 作废
     * @param $ServiceID
     * @return mixed|string
     */
    public function cancel($ServiceID)
    {
        $data = [
            'Request' => [
                'Head' => [
                    'NWVersion' => '01',
                    'NWCode' => 602013,
                    'NWGUID' => substr(md5(microtime()), 3, 20),
                    'NWExID' => $this->loginKey,
                ],
                'Data' => [
                    'ServiceID' => $ServiceID,
                    //'Explain' => '',
                ]
            ]
        ];

        $res = $this->consolePost($data);
        return $res;
    }

    /**
     * 事务管理 查找历史操作步骤
     * @param $ServiceID
     * @return mixed|string
     */
    public function searchHistoryDoStep($ServiceID)
    {
        $data = [
            'Request' => [
                'Head' => [
                    'NWVersion' => '01',
                    'NWCode' => 602015,
                    'NWGUID' => substr(md5(microtime()), 3, 20),
                    'NWExID' => $this->loginKey,
                ],
                'Data' => [
                    'ServiceID' => $ServiceID,
                    //'CurrStepID' => '',
                ]
            ]
        ];

        $res = $this->consolePost($data);
        return $res;
    }

    /**
     * 事务管理 工程报事不合格升级
     * @param $ServiceID
     * @return mixed|string
     */
    public function repairUnqualifiedUpdate($ServiceID)
    {
        $data = [
            'Request' => [
                'Head' => [
                    'NWVersion' => '01',
                    'NWCode' => 602018,
                    'NWGUID' => substr(md5(microtime()), 3, 20),
                    'NWExID' => $this->loginKey,
                ],
                'Data' => [
                    'ServiceID' => $ServiceID,
                ]
            ]
        ];

        $res = $this->consolePost($data);
        return $res;
    }

    /**
     * 事务管理 报事跟进新增修改
     * @param integer $BusinessID
     * @param integer $ID
     * @param string $ProcessInfo
     * @param string $UnFinishReason
     * @param string $FinishTime
     * @param string $FollowTime
     * @param int $FollowType 0(默认) 一般跟进 1 超时预约跟进
     * @return mixed|string
     */
    public function followAndModify($BusinessID, $ID, $ProcessInfo, $UnFinishReason, $FinishTime, $FollowTime, $FollowType=0)
    {
        $data = [
            'Request' => [
                'Head' => [
                    'NWVersion' => '01',
                    'NWCode' => 602020,
                    'NWGUID' => substr(md5(microtime()), 3, 20),
                    'NWExID' => $this->loginKey,
                ],
                'Data' => [
                    'BusinessID' => $BusinessID,
                    'ID' => $ID,
                    'ProcessInfo' => $ProcessInfo,
                    'UnFinishiReason' => $UnFinishReason,
                    'FinishiTime' => $FinishTime,
                    'FollowTime' => $FollowTime,
                    'FollowType' => $FollowType,
                ]
            ]
        ];

        $res = $this->consolePost($data);
        return $res;
    }

    /**
     * 事务管理 获取报事跟进列表
     * @param $ServiceID
     * @return mixed|string
     */
    public function followUp($ServiceID)
    {
        $data = [
            'Request' => [
                'Head' => [
                    'NWVersion' => '01',
                    'NWCode' => 602021,
                    'NWGUID' => substr(md5(microtime()), 3, 20),
                    'NWExID' => $this->loginKey,
                ],
                'Data' => [
                    'ServiceID' => $ServiceID,
                ]
            ]
        ];

        $res = $this->consolePost($data);
        return $res;
    }

    /**
     * 业主评价
     * @param integer $ServiceID
     * @param int $Satisfaction
     * @param int $Timeliness
     * @param string $CustomerIdea
     * @return bool|mixed|string
     */
    public function customerEvaluation($ServiceID, $Satisfaction, $Timeliness, $CustomerIdea='')
    {
        $data = [
            'Request' => [
                'Head' => [
                    'NWVersion' => '01',
                    'NWCode' => 602017,
                    'NWGUID' => substr(md5(microtime()), 3, 20),
                    'NWExID' => $this->loginKey,
                ],
                'Data' => [
                    'ServiceID' => $ServiceID,
                    'Satisfaction' => $Satisfaction,
                    'Timeliness' => $Timeliness,
                    'CustomerIdea' => $CustomerIdea,
                ]
            ]
        ];

        $res = $this->consolePost($data);
        return $res;
    }

    /**
     * 临时缴款查询
     * @param $house_id
     * @return bool
     * @author zhaowenxi
     */
    public function getTempBill($house_id)
    {
        $res = $this->post([
            'Request' => [
                'Head' => [
                    'NWVersion' => '01',
                    'NWCode' => 5011,
                    'NWGUID' => substr(md5(microtime()), 3, 20),
                    'NWExID' => $this->loginKey,
                ],
                'Data' => [
                    'HouseID' => $house_id,
                ]
            ]
        ]);

        return isset($res['Response']['Data']['Record']) ? $res['Response']['Data']['Record'] : false;
    }

    public function uploadFile($file, $reportPhoto)
    {
        $FileName = md5(microtime() . time() . rand(0, 9999999));
//        $url = "http://hznewsee.oicp.net:82/ipadserver/newseeserver.aspx?UploadFile=1&BusinessFlag=3&FilePath={$file}&FileName={$FileName}&FileID={$reportPhoto}&PhotoType=2";
        $url = "http://hznewsee.oicp.net:82/ipadserver/newseeserver.aspx";

        $data = [
            'UploadFile' => 1,
            'BusinessFlag' => 3,
            'FilePath' => $file,
            'FileName' => $FileName,
            'FileID' => $reportPhoto,
            'PhotoType' => 2,
        ];

        $opts = array(
            'http' => array(
                'method' => 'POST',
                'header' => 'content-type:application/x-www-form-urlencoded',
                'content' => $data
            )
        );

        $context = stream_context_create($opts);
        $response = file_get_contents($url, false, $context);

        var_dump($response);die;


        $data = json_encode($data);

        $ch = curl_init();

        /*curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_NOSIGNAL, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT_MS, 15000);
        $result = curl_exec($ch);
        curl_close($ch);*/

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $sContent = curl_exec($ch);
        $aStatus = curl_getinfo($ch);
        curl_close($ch);

        var_dump($sContent);die;

        return file_get_contents($url);

    }

    public function getLoginKey()
    {
        return $this->loginKey;
    }

    private function consolePost($data)
    {
        /*if (!is_string($data)) {
            $data = json_encode($data);
        }
        $data = self::encrypt($data, self::KET_STR);
        $result = self::decrypt($data, self::KET_STR);

        return $result;*/

        var_export($data);

        return $this->post($data);
    }


    private function post($data, $ag = false)
    {
        if (!is_string($data)) {
            $data = json_encode($data);
        }
        $data = self::encrypt($data, self::KET_STR);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->host);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_NOSIGNAL, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT_MS, 15000);
        $result = curl_exec($ch);
        $curlInfo = curl_getinfo($ch);
        curl_close($ch);
        $result = self::decrypt($result, self::KET_STR);
        $result = json_decode($result, true);

        return [$data, $result, $curlInfo];
    }

    private static function encrypt($input, $key)
    {
        $size = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB);
        $input = self::pkcs5_pad($input, $size);
        $td = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_ECB, '');
        $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
        mcrypt_generic_init($td, $key, $iv);
        $data = mcrypt_generic($td, $input);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        $data = base64_encode($data);
        return $data;
    }

    private static function pkcs5_pad($text, $blocksize)
    {
        $pad = $blocksize - (strlen($text) % $blocksize);
        return $text . str_repeat(chr($pad), $pad);
    }

    private static function decrypt($sStr, $sKey)
    {
        $decrypted = mcrypt_decrypt(
            MCRYPT_RIJNDAEL_128,
            $sKey,
            base64_decode($sStr),
            MCRYPT_MODE_ECB
        );
        $dec_s = strlen($decrypted);
        $padding = ord($decrypted[$dec_s - 1]);
        $decrypted = substr($decrypted, 0, -$padding);
        return $decrypted;
    }
}