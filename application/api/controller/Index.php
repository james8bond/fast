<?php

namespace app\api\controller;

use app\api\model\TUser;
use app\common\controller\Api;
use fast\Random;
use think\Hook;
use think\Log;

/**
 * 首页接口
 */
class Index extends Api
{
    protected $noNeedLogin = ['*'];
    protected $noNeedRight = ['*'];

    /**
     * 首页
     *
     */
    public function index()
    {
        $this->success('请求成功');
    }
    public function uuid()
    {
        $token = Random::uuid();
        $this->success('ok', $token);
    }

    public function get_user_list()
    {
        Hook::add('bourne', 'app\\wechat\\behavior\\Test');
        Hook::listen('bourne');

        $list = TUser::select();

        $this->success('ok');
    }
}
