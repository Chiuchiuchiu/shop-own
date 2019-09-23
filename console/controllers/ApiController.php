<?php
/**
 * Created by
 * Author: zhao
 * Time: 2016/11/8 10:26
 * Description:
 */

namespace console\controllers;


use common\models\House;
use components\newWindow\NewWindow;
use yii\base\ErrorException;
use yii\console\Controller;

class ApiController extends Controller
{
    const USERNAME = 'wf2016';
//    const USERNAME = 'cdj';
    const PASSWORD = 'abc123456';
//    const PASSWORD = 'cdj12345';
    const HOST = 'http://183.63.113.10:8888/pda/newseeserver.aspx';
    const KET_STR = '01234567890123456789012345678901';

    const LOGIN_KEY = 'zJx1+M3BAtp10fW3/9zCV2VOle4DkqwhW4jckrWrz2yQizVjuBIDIuiSUftHqIsK';

    public function actionLogin()
    {
        $loginData = [
            'Request' => [
                'Head' => [
                    'NWVersion' => '01',
                    'NWCode' => 1,
                    'NWGUID' => substr(md5(microtime()), 3, 20),
                    'NWExID' => '',
                ],
                'Data' => [
                    'Account' => self::USERNAME,
                    'PassWord' => self::PASSWORD,
                    'Mac' => 'null'
                ]
            ]
        ];
        $res = $this->post($loginData);
        var_dump($res);
    }

    public function actionSaveHouse($houseId,$projectId=null)
    {
        if($projectId===null) $projectId=$houseId;
        $this->stdout("running: $houseId \n");
        $res = $this->post([
            'Request' => [
                'Head' => [
                    'NWVersion' => '01',
                    'NWCode' => 7,
                    'NWGUID' => substr(md5(microtime()), 3, 20),
                    'NWExID' => self::LOGIN_KEY,
                ],
                'Data' => [
                    'HouseID' => $houseId,
                ]
            ]
        ]);
        if (isset($res['Response']['Data']['NWRespCode'])) {
            if ($res['Response']['Data']['NWRespCode'] == '0000') {
                foreach ($res['Response']['Data']['Record'] as $row) {
                    $this->saveHouse($row, $houseId,$projectId);
                    if (isset($res['Response']['Data']['lsChild']) && is_array($res['Response']['Data']['lsChild'])) {
                        foreach ($res['Response']['Data']['lsChild'] as $v)
                            $this->saveHouse($v, $row['HouseID'],$projectId);
                    }
                }
                return 1;
            }elseif($res['Response']['Data']['NWRespCode'] == 'Cannot find column [ParentID].'){
                return 1;
            }
        }
        throw new ErrorException("no data");
    }

    private function saveHouse($row, $parentId,$projectId, $FindChild = true)
    {
        $showStatusMap = [
            1 => 1,//小区,展示
            2 => 2,//组团,跳过显示下一级
            3 => 1,//大楼,展示
            4 => 2,//单元,跳过显示下一级
            5 => 1,//房间,不展示
            6 => 1,//别墅,展示
            7 => 1,//排屋,展示
            8 => 0,//储藏室,不展示
            9 => 0,//车位,不展示
            10 => 0,//停车场,不展示
            11 => 0,//车区,不展示
            13 => 0,//自行车位,不展示
            14 => 0,//广告位,不展示
            15 => 0,//卫星收视,不展示
        ];
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
        $house->show_status = $showStatusMap[$house->reskind];//$row['DeepestNode'];

        if (!$house->save()) {
            echo $this->stdout("save Error");
            var_dump($row);
            throw new ErrorException(serialize($house->getErrors()));
        } else {
            if ($FindChild && !$house->deepest_node && $house->house_id != $parentId)
                $this->actionSaveHouse($house->house_id,$projectId);
        }
    }

    private function post($data)
    {
        if (!is_string($data)) {
            $data = json_encode($data);
        }
        $data = self::encrypt($data, self::KET_STR);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::HOST);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_NOSIGNAL, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT_MS, 15000);
        $result = curl_exec($ch);
        curl_close($ch);
        $result = self::decrypt($result, self::KET_STR);
        return json_decode($result, true);
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