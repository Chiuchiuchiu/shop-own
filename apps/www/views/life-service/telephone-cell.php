<?php
/**
 * Created by
 * Author: zhaowenxi
 * Description:
 * @var $model \common\models\ProjectServicePhone
 * @var $dataProvider \yii\data\ActiveDataProvider
 */
foreach ($dataProvider->getModels() as $model) { ?>
    <div class="repair-lists">
        <div class="list-cell">
            <div class="new-info">
                <ul>
                    <li>
                        <h4>
                            <label class="flow_style_w2"><?= $model->name ?></label>
                        </h4>
                    </li>
                    <li>
                        <span style="color: #888888">通讯地址：</span>
                        <?= $model->address ?>
                    </li>
                    <li>
                        <span style="color: #888888">联系电话：</span>
                        <label style="font-size: 16px;color:#E2574C"><?= $model->telephone ?></label>
                        <span>
                            <a style="color: #888888" href="tel:<?= $model->telephone ?>">（点击拨打）</a>
                        </span>
                    </li>
                </ul>
            </div>

        </div>
    </div>

<?php } ?>