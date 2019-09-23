<?php
/**
 * Created by
 * Author: zhao
 * Time: 16/6/13 14:33
 * Description:
 */

namespace components\pay\wechat;



class AnalysisBill implements \components\pay\AnalysisBill
{
    private $file;
    private $data;
    private $fee=0;
    private $money=0;

    function __construct($file)
    {
        $this->file = $file;
    }

    public function run()
    {
        $fp = fopen($this->file, 'r');
        while (!feof($fp)) {
            $line = stream_get_line($fp, 8192,"\n");
            $line = mb_convert_encoding($line, 'utf8');
            $line = explode(",",$line);
            $line = array_map(function($data){ return trim($data," `\r\n\0\t\"");},$line);
            if(isset($line[9]) && $line[9]=='SUCCESS'){
                $this->data[$line[6]] = [
                    'orderid'=>$line[6],
                    'money'=>round($line[12],2),
                    'fee'=>round($line[22],2)
                ];
            }
        }
        return $this->data;
    }

}