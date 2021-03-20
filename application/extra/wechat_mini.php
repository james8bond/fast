<?php
/**
 *   User:   邦德
 *   Date:   2021/3/9 : 12:51
 */
return [
    // My风袖 小程序
    'fengxiu' => [
        'app_id'        => 'wx58b7139d6d6883df',
        'secret'        => '59e266746f117d634936e02cfec1a335',
        'response_type' => 'array',
        'log'           => [
            'level' => 'debug',
            'file'  => RUNTIME_PATH . 'wechat.log',
        ],
    ],

    // 儒儒家 小程序 
    'rurujia' => [
        'app_id'        => 'wxcdc9699ac01c1f23',
        'secret'        => 'ea3a61914767faf0877545623fa9b60d',
        'response_type' => 'array',
        'log'           => [
            'level' => 'debug',
            'file'  => RUNTIME_PATH . 'wechat.log',
        ],
        'mch_id'        => 'your-mch-id',           // 商户号
        'key'           => 'key-for-signature',     // 商户号秘钥
        'notify_url'    => '',                      // 回调地址

        // 开启沙箱模式
        'sandbox' => true,
    ],
    // 优蓝设计
    'youlan' => [
        'app_id'        => 'wx26f6929acfb406a4',
        'secret'        => 'd7d30e7b96ebd5606b0e71850d2ca03b',
        'response_type' => 'array',
        'log'           => [
            'level' => 'debug',
            'file'  => RUNTIME_PATH . 'wechat.log',
        ],
        'mch_id'        => '1586790681',                            // 商户号
        'key'           => 'kunmingublue1380872326120210317k',      // 商户号秘钥
        'notify_url'    => 'https://brian.james8bond.top/wechat/pay/receive_notify', // 回调地址
    ],
];

