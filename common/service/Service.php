<?php
/**
 * Created by
 * Author: zhao
 * Time: 16/5/16 12:10
 * Description:
 */

namespace common\service;


use yii\base\ErrorException;
use yii\base\Model;
use yii\db\ActiveRecord;
use yii\db\Connection;

class Service
{

    /**
     * @param array $data
     * @param int $code
     * @return ServiceResult
     * Description:成功时候返回
     */
    protected static function success($data = [], $code = 0)
    {
        return new ServiceResult(true, $code, $data);
    }

    /**
     * @param $code
     * @param $message
     * @param array $data
     * @return ServiceResult
     * Description:失败时候返回、非异常处理
     */
    protected static function fail($code, $message, $data = [])
    {
        return new ServiceResult(false, $code, $data, $message);
    }

    protected static function failRollBack(Connection $db, $code, $message, $data = [])
    {
        $db->getTransaction()->rollBack();
        return self::fail($code, $message, $data);
    }


    public static function saveModel(array $models, $closure = null, $validate = true)
    {
        $runClosure = $closure instanceof \Closure;
        //提前验证数据
        if ($validate) {
            foreach ($models as $model) {
                if (!$model instanceof ActiveRecord) {
                    throw new ErrorException;
                }
                if (!$model->validate()) return self::fail(-1, 'error', ['model' => $model, 'message' => $model->getErrors()]);
            }
        }
        $dbs = [];
        foreach ($models as $model) {
            /**
             * @var $db Connection;
             */
            $db = $model->getDb();
            if (!in_array($db, $dbs)) {
                $dbs[] = $db;
                $db->beginTransaction();
            }
        }
        foreach ($models as $key => $model) {
            if (!$model->save()) {
                foreach ($dbs as $db) {
                    $db->getTransaction()->rollBack();
                }
                return self::fail(-1, 'error', ['model' => $model, 'message' => $model->getErrors()]);
            }
            if ($runClosure) {
                call_user_func($closure,$key);
            }
        }
        foreach ($dbs as $db) {
            $db->getTransaction()->commit();
        }
        return self::success();
    }
}