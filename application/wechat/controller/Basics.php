<?php
/**
 *   基础功能模块
 *   User:   邦德
 *   Date:   2021/3/17 : 11:03
 */


namespace app\wechat\controller;

use app\common\controller\Api;
use app\wechat\libs\validate\MyValidate;
use EasyWeChat\Factory;


class Basics extends Api
{
    protected $noNeedLogin = [''];
    protected $noNeedRight = ['*'];

    public function index()
    {
        $this->success('基础api接口');
    }

    public function get_user_mobile()
    {
        // 必须传递的字段
        $params = ['iv', 'encryptedData'];
        (new MyValidate($params))->goCheck();

        $openid = $this->auth->getUser()['openid'];
        $session_key = cache($openid);
//return json($session_key);
        $app =  get_easywechat_miniProgram(config('wechat_mini.youlan'));

        $decryptedData = $app->encryptor->decryptData($session_key, input('iv/s'), input('encryptedData/s'));

        $this->success('成功获取用户手机号', $decryptedData['phoneNumber']);
    }
}