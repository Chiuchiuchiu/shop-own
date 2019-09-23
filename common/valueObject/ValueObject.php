<?php
/**
 * Created by
 * Author: zhao
 * Time: 16/5/20 13:50
 * Description:
 */

namespace common\valueObject;


use yii\base\Model;

class ValueObject extends Model
{
    /**
     * @param $data
     * @param string $scenario
     * @param null $formName
     * @return $this
     * Description:
     */
    public function autoLoad($data,$scenario='default', $formName = null){
        if($this instanceof Model){
            $this->scenario=$scenario;
            $this->load($data,$formName);
            $this->validate();
        }
        return $this;
    }

}