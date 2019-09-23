<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/1/11
 * Time: 11:39
 */

namespace console\controllers;


use common\models\House;
use common\models\HouseExt;
use common\models\Project;
use components\newWindow\NewWindow;
use yii\base\ErrorException;
use yii\console\Controller;

class HouseExtController extends Controller
{
    protected  const MAX_PID = 50;


    public function actionSaveHouseExt($houseId=null)
    {
        $this->stdout("running: $houseId \n");
        if ($houseId){
            $res = (new NewWindow())->getHouse($houseId);
            if ($res)
            {
                foreach ($res as $key => $row) {
                    $this->saveHouseExt($row);
                }
                return 1;
            }
        } else {

            $projectData = Project::find()->select('house_id')->all();

            foreach ($projectData as $key => $projectHouseId){
                $this->stdout("projectHouseId: {$projectHouseId['house_id']} \n");

                foreach (House::find()->select('house_id')->where(['project_house_id' => $projectHouseId['house_id']])->each(100) as $row){
                    /**
                     * @var $row House
                     */
                    $this->stdout("HouseId: $row->house_id \n");
                    $newWindowRes = (new NewWindow())->getHouse($row->house_id);

                    if ($newWindowRes)
                    {
                        $this->saveHouseExt($newWindowRes[0]);
                    }
                    continue;
                }
                unset($projectData[$key]);
            }

            return 1;
        }
        throw new ErrorException("no data");
    }

    protected function actionTestRunFork()
    {
        $PIds = [];
        $pid = pcntl_fork();
        if ($pid == -1) {
            die('could not fork');
        } else if ($pid) {
            $PIds[] = $pid;
            pcntl_wait($status); //等待子进程中断，防止子进程成为僵尸进程。
        } else {

            if(count($PIds) > self::MAX_PID){
                $PIdsKey = array();
            }

        }
    }

    private function saveHouseExt($row)
    {
        $houseExt = HouseExt::findOrCreate($row['HouseID']);

        $this->stdout("findOrCreate:houseId:".$houseExt->house_id."\n");
        $birthDay = 0;
        if ($row['BirthDay'] != '/Date(-62135596800000+0800)/'){
            $rules = '/-?\d{6,}/';
            preg_match($rules, $row['BirthDay'], $matches);

            $numberLength = strlen($matches[0]);
            $birthDay = substr($matches[0], 0, $numberLength - 3) + (24 * 3600);
        }

        $houseExt->customer_id = $row['CustomerID'];
        $houseExt->birth_day = $birthDay;
        $houseExt->charge_area = $row['ChargeArea'];
        $houseExt->id_number = $row['IDNumber'];
        $houseExt->hurry_phone = $row['HurryPhone'];
        $houseExt->link_man = $row['LinkMan'];
        $houseExt->customer_name = trim($row['CustomerName']);
        $houseExt->mobile_phone = $row['MobilePhone'];
        $houseExt->updated_at = time();

        if (!$houseExt->save()) {
            echo $this->stdout("save Error");
            throw new ErrorException(serialize($houseExt->getErrors()));
        }
    }

}