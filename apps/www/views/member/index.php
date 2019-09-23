<?php
/**
 * @var $_user \apps\www\models\Member
 * @var $zsy
 */
?>
<div class="panel" id="member">
    <div class="info">
        <img src="<?= Yii::getAlias($_user->headimg) ?>">
        <h4><?php echo $_user->name ? $_user->name : $_user->nickname ?></h4>
        <div>
            <?php echo $_user->phone ? "<i class='icon-success'>手机已认证</i>" : "<i class='icon-fail'>手机未认证</i>" ?>
        </div>
    </div>
    <?php if (\common\models\SysSwitch::getValue('zsyModule') && !YII_DEBUG && !empty($zsy['data'])): ?>
    <div class="asset" data-go="/zsy/assets">
        <h5>总资产（元）</h5>
        <p class="money"><?=$zsy['data']['resultInfo']['totalAsset']?number_format($zsy['data']['resultInfo']['totalAsset']/100,2):'0'?></p>
        <small>昨日最新收益 <span><?=$zsy['data']['resultInfo']['totalAsset']?number_format($zsy['data']['resultInfo']['yesterdayIncome']/100,2):'0'?></span></small>
    </div>
    <?php endif; ?>
    <ul class="list">
        <li data-go="/member/manage"><i class="icon-member-m"></i>账号管理</li>
        <li data-go="/order/pm-list"><i class="icon-house"></i>物业缴费</li>
        <li data-go="/parking-order/order-list"><i class="icon-parking-o"></i>停车缴费</li>
        <li data-go="/new-repair/list?status=0"><i class="icon-repair"></i>报事报修</li>
        <li data-go="/feedback/list"><i class="icon-feedback"></i>投诉处理</li>
        <li data-go="/tcis/lists"><i class="icon-history"></i>发票历史</li>
        <li data-go="/coupon/index"><i class="icon-coupon"></i>我的优惠券</li>
        <?php if (\common\models\SysSwitch::getValue('zsyModule')): ?>
            <li data-go="/zsy/deposit"><i class="icon-deposit"></i>充值</li>
            <li data-go="/zsy/withdraw"><i class="icon-withdraw"></i>提现</li>
            <li data-go="/zsy/record"><i class="icon-record"></i>资金流水</li>
        <?php endif; ?>
    </ul>
</div>
