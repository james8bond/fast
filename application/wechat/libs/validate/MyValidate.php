<?php
/**
 *   User:   邦德
 *   Date:   2020/11/13 : 16:33
 */


namespace app\wechat\libs\validate;

class MyValidate extends BaseValidate
{
    protected $rule = [

    ];

    public function __construct($params)
    {
        parent::__construct();
        if (empty($params)) {
            return;
        }
        foreach ($params as $item) {
            $this->rule[$item] = 'require';
        }
    }

    public function create($params)
    {
        if (empty($params)) {
            return;
        }
        foreach ($params as $item) {
            $this->rule[$item] = 'require';
        }
//        return $this;
    }

    public static function instance($params)
    {
        $instance = new self;
        $instance->create($params);
        return $instance;
    }
}