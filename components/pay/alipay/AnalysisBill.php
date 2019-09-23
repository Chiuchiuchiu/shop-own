<?php
/**
 * Created by
 * Author: zhao
 * Time: 16/6/13 14:33
 * Description:
 */

namespace components\pay\alipay;



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
            $line = stream_get_line($fp, 4096,"\n");
            $line = (mb_convert_encoding($line, 'utf8', 'gbk'));
            $line = explode(",",$line);
            $line = array_map(function($data){ return trim($data," \r\n\0\t\"");},$line);
            if(isset($line[10])){
                switch($line[10]){
                    case "在线支付":
                        $this->data[$line[2]]['orderid'] = $line[2];
                        $this->data[$line[2]]['money'] = round($line[6],2);
                        break;
                    case "收费":
                        $this->data[$line[2]]['orderid'] = $line[2];
                        $this->data[$line[2]]['fee'] = abs(round($line[7],2));
                        break;
                }
            }
        }
        return $this->data;
    }

}