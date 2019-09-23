<?php
/**
 * Created by
 * Author: zhao
 * Time: 16/5/17 17:29
 * Description:
 */

namespace common\widgets;

use Yii;
use yii\web\View;
use yii\widgets\Block;

class JavascriptBlock extends Block
{
    /**
     * @var null
     */
    public $key = null;
    /**
     * @var int
     */
    public $pos = View::POS_READY;

    /**
     * Ends recording a block.
     * This method stops output buffering and saves the rendering result as a named block in the view.
     */
    public function run()
    {
        $block = ob_get_clean();
        if ($this->renderInPlace) {
            throw new \Exception("not implemented yet ! ");
            // echo $block;
        }
        $block = trim($block);

        /**
         * 对于ajax/pjax的请求,直接输出script于页面,可以直接除非脚本运行
         */
        if(Yii::$app->request->isAjax){
            echo $block;
        }else{
            $jsBlockPattern = '|^<script[^>]*>(?P<block_content>.+?)</script>$|is';
            if (preg_match($jsBlockPattern, $block, $matches)) {
                $block = $matches['block_content'];
            }
            $this->view->registerJs($block, $this->pos, $this->key);
        }
    }
}