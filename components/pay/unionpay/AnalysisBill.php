<?php
/**
 * Created by
 * Author: zhao
 * Time: 16/6/13 14:33
 * Description:
 */

namespace components\pay\unionpay;



class AnalysisBill implements \components\pay\AnalysisBill
{
    private $file;
    private $data;

    function __construct($file)
    {
        $this->file = $file;
    }

    public function run()
    {
        $fp = fopen($this->file, 'r');
        while (!feof($fp)) {
            $line = stream_get_line($fp, 8192,"\n");
            $line = (mb_convert_encoding($line, 'utf8', 'gbk'));
            $line = explode(",",$line);
            $line = array_map(function($data){ return trim($data," `\r\n\0\t\"");},$line);
            if($line[7]=='交易成功'){
                $this->data[$line[1]] = [
                    'orderid'=>$line[1],
                    'money'=>round($line[5],2),
                    'fee'=>0
                ];
            }
        }
        return $this->data;
    }

}