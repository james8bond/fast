<?php
/**
 *   User:   邦德
 *   Date:   2020/11/4 : 10:07
 */


namespace app\cs\validate;


class MobileMustBePostive extends BaseValidate
{
    protected $rule = [
        'mobile' => 'require|isMobile',
    ];

    protected $message = [
        'mobile.require'  => 'mobile参数不能为空',
        'mobile.isMobile' => 'mobile参数必须是合法的手机号',
    ];
}