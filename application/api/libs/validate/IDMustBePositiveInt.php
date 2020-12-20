<?php
/**
 *   User:   邦德
 *   Date:   2020/11/4 : 14:06
 */


namespace app\cs\validate;


class IDMustBePositiveInt extends BaseValidate
{
    protected $rule = [
        'id' => 'require|isPostiveInteger',
    ];

    protected $message = [
        'id.require'          => 'ID不能为空',
        'id.isPostiveInteger' => 'ID必须是正整数',
    ];
}