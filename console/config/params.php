<?php
return [
    'adminEmail' => 'admin@example.com',

    'wechat'=>[
        'token' => '8ary', //填写你设定的key
        'encodingAesKey' => '2', //填写加密用的EncodingAESKey
        'appId' => 'wx2e40e46c993bd183', //填写高级调用功能的app id
        'appSecret' => 'd4624c36b6795d1d99dcf0547af5443d',//填写高级调用功能的密钥
        'templateIds' => [
            1 => [
                'templateId' => 'GEIeq7WlamAY2Q1SFYPkz339gg5-9GOCyYMzoFpQ24I',
                'message' => '您本月的物业账单已生成！',
            ],
            10 => [
                'templateId' => 'QtzX6SbrGnygaSqmu1DSFpDPWV5qXOHCv_60SXibjpc',
                'message' => '再忙也别忘了缴物业费哦！',
            ],
            25 => [
                'templateId' => '2EwekBSLcAzT9hQjZgA6t8A-HcfMs8IifJyCgfNNu4U',
                'message' => '您当月物业费仍欠缴，请月底前处理！',
            ],
        ],
    ],
    'test_butler_id' => [],
];
