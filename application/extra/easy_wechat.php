<?php
/**
 *   User:   邦德
 *   Date:   2021/2/11 : 20:44
 */
return [
    'app_id'        => 'wx58b7139d6d6883df',
    'secret'        => '59e266746f117d634936e02cfec1a335',

    // 下面为可选项
    // 指定 API 调用返回结果的类型：array(default)/collection/object/raw/自定义类名
    'response_type' => 'array',
    'log' => [
        'level' => 'debug',
        'file'  => RUNTIME_PATH . 'wechat.log',
    ],

];

