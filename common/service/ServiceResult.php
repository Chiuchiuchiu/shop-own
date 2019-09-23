<?php
/**
 * Created by
 * Author: zhao
 * Time: 16/5/16 12:14
 * Description:
 */

namespace common\service;


class ServiceResult
{
    public $isSuccess = false;
    public $message = null;
    public $data = [];
    public $code = null;

    public function __construct($success, $code, $data, $message = '')
    {
        $this->isSuccess = $success;
        $this->code = $code;
        $this->data = $data;
        $this->message = $message;
    }
}