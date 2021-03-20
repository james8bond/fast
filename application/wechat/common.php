<?php

use app\common\library\Token;
use think\Request;

if (!function_exists('get_easywechat_app')) {
    /**
     * 获取EasyWeChat对象
     */
    function get_easywechat_app($wechat = [])
    {
        if (empty($wechat)) {
            exception('小程序配置信息不能为空');
        }
        return EasyWeChat\Factory::miniProgram($wechat);
    }
}

if (!function_exists('get_easywechat_payment')) {
    /**
     * 获取EasyWeChat对象
     */
    function get_easywechat_payment($wechat = [])
    {
        if (empty($wechat)) {
            exception('小程序配置信息不能为空');
        }
        return EasyWeChat\Factory::payment($wechat);
    }
}

if (!function_exists('get_uid')) {
    /**
     * 获取EasyWeChat对象
     */
    function get_uid()
    {
        $request = Request::instance();
        $token   = $token = $request->server('HTTP_TOKEN', $request->request('token', \think\Cookie::get('token')));
        if (!$token) {
            return null;
        }
        $data = Token::get($token);
        if (!$data) {
            return null;
        }
        $user_id = intval($data['user_id']);
        return $user_id;
    }
}