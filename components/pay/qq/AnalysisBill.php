<?php

namespace components\pay\qq;



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
//            $line = mb_convert_encoding($line, 'utf8');
            $line = explode(",",$line);
            $line = array_map(function($data){ return trim($data," `\r\n\0\t\"");},$line);
//            var_dump($line[17]);
            if(isset($line[17]) && $line[17]=='用户已支付'){
                $this->data[$line[8]] = [
                    'orderid'=>$line[8],
                    'money'=>round($line[12],2),
                    'fee'=>round($line[28],2)
                ];
            }
        }
        var_dump($this->data);
        return $this->data;
    }

}