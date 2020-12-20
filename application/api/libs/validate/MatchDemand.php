<?php
/**
 *   User:   邦德
 *   Date:   2020/11/13 : 15:13
 */

// 匹配需求
namespace app\cs\validate;


class MatchDemand extends BaseValidate
{
    protected $rule = [
        'id'      => 'require|isPostiveInteger',
        'uid'     => 'require',
        'shop_id' => 'require',
        'desc'    => 'require',
    ];

}