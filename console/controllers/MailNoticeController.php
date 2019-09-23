<?php
/**
 * Created by
 * Author: zhao
 * Time: 2016/12/22 18:34
 * Description:
 */

namespace console\controllers;


use common\models\PmOrder;
use common\models\Project;
use yii\console\Controller;

class MailNoticeController extends Controller
{
    public function actionIndex($date=null){
        if(empty($date)) $date = date('Y-m-d',strtotime('-1 days'));
        $mail= \Yii::$app->mailer->compose();
//        $mail->setTo(['liuxiaoju@51homemoney.com','huangqimin@51homemoney.com','ally@51homemoney.com','zhao@51homemoney.com','zhangting@51homemoney.com','wufu@51homemoney.com']);

        $mail->setTo(['huangqimin@51homemoney.com', 'ally@51homemoney.com', 'zhao@51homemoney.com', 'liuxiaoju@51homemoney.com']);

        $mail->setSubject( $date.' 收款情况日报');
        $str = '';
        $projects = Project::findAll(['status'=>Project::STATUS_ACTIVE]);
        $data=[
            'sum'=>[
                'name'=>'汇总',
                'amount'=>0,
                'num'=>0,
                'itemNum'=>0,
                'numCount'=>[]
            ]
        ];
        for($i=1;$i<13;$i++) $data['sum']['numCount'][$i]=0;
        foreach($projects as $p){
            $data[$p->house_id] = [
                'name'=>$p->house_name,
                'amount'=>0,
                'num'=>0,
                'itemNum'=>0,
                'numCount'=>[]
            ];
            for($i=1;$i<13;$i++) $data[$p->house_id]['numCount'][$i]=0;

        }
        $rs = PmOrder::find()->where([
            'status'=>PmOrder::STATUS_PAYED,
        ])->andWhere(['BETWEEN','payed_at',strtotime($date.' 00:00:00'),strtotime($date.' 23:59:59')]);
        foreach($rs->each() as $row){
            /**
             * @var $row PmOrder
             */
            $data['sum']['amount'] += round($row->total_amount,2);
            $data['sum']['num'] += 1;
            $data['sum']['itemNum'] += count($row->items);

            if(isset($data['sum']['numCount'][count($row->items)])){
                $data['sum']['numCount'][count($row->items)]++;
            }

            $data[$row->project_house_id]['amount'] += round($row->total_amount,2);
            $data[$row->project_house_id]['num'] += 1;
            $data[$row->project_house_id]['itemNum'] += count($row->items);

            if(isset($data[$row->project_house_id]['numCount'][count($row->items)])){
                $data[$row->project_house_id]['numCount'][count($row->items)]++;
            }

        }
        //历史
        foreach($data as$key=>&$row){
            if($key=='sum'){
                $row['total'] = PmOrder::find()->where(['status'=>PmOrder::STATUS_PAYED])->sum('total_amount');
                $row['30d'] = PmOrder::find()->where(['status'=>PmOrder::STATUS_PAYED])->andWhere(['>','payed_at',strtotime(date('Y-m-d',time()-30*86400))])->sum('total_amount');
                $row['90d'] = PmOrder::find()->where(['status'=>PmOrder::STATUS_PAYED])->andWhere(['>','payed_at',strtotime(date('Y-m-d',time()-90*86400))])->sum('total_amount');
                $row['365d'] = PmOrder::find()->where(['status'=>PmOrder::STATUS_PAYED])->andWhere(['>','payed_at',strtotime(date('Y-m-d',time()-365*86400))])->sum('total_amount');
            }else{
                $row['total'] = PmOrder::find()->where(['status'=>PmOrder::STATUS_PAYED,'project_house_id'=>$key])->sum('total_amount');
                $row['30d'] = PmOrder::find()->where(['status'=>PmOrder::STATUS_PAYED,'project_house_id'=>$key])->andWhere(['>','payed_at',strtotime(date('Y-m-d',time()-30*86400))])->sum('total_amount');
                $row['90d'] = PmOrder::find()->where(['status'=>PmOrder::STATUS_PAYED,'project_house_id'=>$key])->andWhere(['>','payed_at',strtotime(date('Y-m-d',time()-90*86400))])->sum('total_amount');
                $row['365d'] = PmOrder::find()->where(['status'=>PmOrder::STATUS_PAYED,'project_house_id'=>$key])->andWhere(['>','payed_at',strtotime(date('Y-m-d',time()-365*86400))])->sum('total_amount');
            }
        }
        foreach($data as $value){
        $str .=<<<HTML
        <h3>{$value['name']}</h3>
<table style="width: 100%" border="1"><tr>
<th>收款金额</th><td>{$value['amount']}</td>
<th>收款笔数</th><td>{$value['num']}</td>
<th>核销账单数量</th><td>{$value['itemNum']}</td>
</tr></table>

<table style="width: 100%" border="1"><tr>
<th>历史总额</th><th>近30日</th><th>近90日</th><th>近365日</th>
</tr><tr><td>{$value['total']}</td><td>{$value['30d']}</td><td>{$value['90d']}</td><td>{$value['365d']}</td></tr></table>


<table style="width: 100%" border="1"><tr>
<th>1月</th>
<th>2月</th>
<th>3月</th>
<th>4月</th>
<th>5月</th>
<th>6月</th><th>7月</th>
<th>8月</th>
<th>9月</th>
<th>10月</th>
<th>11月</th>
<th>12月</th>
</tr>
<tr>
<td>{$value['numCount'][1]}</td>
<td>{$value['numCount'][2]}</td>
<td>{$value['numCount'][3]}</td>
<td>{$value['numCount'][4]}</td>
<td>{$value['numCount'][5]}</td>
<td>{$value['numCount'][6]}</td>
<td>{$value['numCount'][7]}</td>
<td>{$value['numCount'][8]}</td>
<td>{$value['numCount'][9]}</td>
<td>{$value['numCount'][10]}</td>
<td>{$value['numCount'][11]}</td>
<td>{$value['numCount'][12]}</td>
</tr>
</table><br /><br /><br />
HTML;
        }
        $mail->setHtmlBody($str);    //发布可以带html标签的文本
        if($mail->send())
            echo "success";
        else
            echo "failse";
        die();
    }
}