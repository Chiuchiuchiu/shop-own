<?php
/**
 * Created by
 * Author: zhao
 * Time: 16/6/13 14:33
 * Description:
 */

namespace components\pay\baidu;



class AnalysisBill implements \components\pay\AnalysisBill
{
    private $file;
    private $data;
    private $orderMap;

    function __construct($file)
    {
        $this->file = $file;
        $this->makeOrderMap();
    }

    public function run()
    {
        $fp = fopen($this->file, 'r');
        while (!feof($fp)) {
            $line = stream_get_line($fp, 8192,"\n");
            $line = (mb_convert_encoding($line, 'utf8', 'gbk'));
            $line = explode(",",$line);
            $line = array_map(function($data){ return trim($data," \r\n\0\t\"");},$line);
            if(isset($line[5])){
                switch($line[5]){
                    case "交易":
                        $this->data[$line[0]]['orderid']=$line[0];
                        $this->data[$line[0]]['money']=round($line[3],2);
                        break;
                    case "手续费":
                        if (!isset($this->data[$line[0]]['fee'])) {
                            $this->data[$line[0]]['fee']=0;
                        }
                        $this->data[$line[0]]['orderid']=$line[0];
                        $this->data[$line[0]]['fee']+=round($line[4],2);
                        break;
                    case "返现":
                        if(isset($this->orderMap[$line[0]])){
                            if (!isset($this->data[$this->orderMap[$line[0]]]['fee'])) {
                                $this->data[$this->orderMap[$line[0]]]['fee']=0;
                            }
                        }
                            $this->data[$this->orderMap[$line[0]]]['fee']+=round($line[4],2);
                        break;
                }
            }
        }
        return $this->data;
    }

    public function makeOrderMap(){
        $fp = fopen($this->file, 'r');
        while (!feof($fp)) {
            $line = stream_get_line($fp, 8192, "\n");
            $line = (mb_convert_encoding($line, 'utf8', 'gbk'));
            $line = explode(",", $line);
            $line = array_map(function ($data) {
                return trim($data, " \t\"");
            }, $line);
            if(isset($line[5])){
                switch($line[5]){
                    case "交易":
                        $this->orderMap[$line[1]] = $line[0];
                        break;
                }
            }
        }
    }

}