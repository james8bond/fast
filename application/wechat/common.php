<?php
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
        return EasyWeChat\Factory::miniProgram($wechat);
    }
}