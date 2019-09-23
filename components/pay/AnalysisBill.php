<?php
/**
 * Created by
 * Author: zhao
 * Time: 16/6/13 14:33
 * Description:
 */

namespace components\pay;


/**
 * Interface AnalysisBill
 * @package components\pay
 * 这个方法用于分析账单，根据账单生成需要的数组格式
 */
interface AnalysisBill
{
    public function __construct($file);

    public function run();
}