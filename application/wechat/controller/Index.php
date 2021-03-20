<?php

namespace app\wechat\controller;

use addons\third\library\Wechat;
use app\common\controller\Api;
use app\wechat\libs\exception\BaseException;
use app\wechat\libs\validate\MyValidate;
use think\Exception;
use app\common\model\User;
use think\Log;
use think\Request;
use app\wechat\model\Product;
use app\wechat\model\Order as OrderModel;
use app\wechat\model\Store as StoreModel;

/**
 * 首页接口
 */
class Index extends Api
{
    protected $noNeedLogin = ['login', 'init'];
    protected $noNeedRight = ['*'];

    /**
     * 首页
     *
     */
    public function index()
    {
       $result = Product::get(3);
//       $result->headerimages = Request::instance()->domain() . $result->headerimages;
       $this->success('ok', $result);
    }

    /**
     *  小程序初始化接口
     */
    public function init()
    {
        $result = [];
        $store_list =  StoreModel::all(['status' => '0']);   // 条件 status=0, 正常开业的点
        $store_list->visible(['id', 'name']);
        $result['store_list'] = $store_list;
        $result['register'] = $this->getUserModel()['register'];
//        $result['register'] = $this->getUserModel();

        $this->success('数据获取成功', $result);
    }

    public function name()
    {
        // 必须传递的字段
        $params = ['name'];
        (new MyValidate($params))->goCheck();

        $this->success('获取Banner成功', 'Hello Banner');
    }

    public function test()
    {
        $this->success('ok', (intval(0.58 * 100)));
        $app    = get_easywechat_app(config('wechat_mini.fengxiu'));
        $response = $app->app_code->get("120");
        if ($response instanceof \EasyWeChat\Kernel\Http\StreamResponse) {
            $filename = $response->save(config('common.upload_path'));
        }
        $this->success('ok', $filename);
    }

    /**
     *  注册接口, 更新手机号和用户名
     */
    public function register_mobile()
    {
        // 必须传递的字段
        $params = ['mobile', 'username'];
        (new MyValidate($params))->goCheck();

        try {
            $user = User::get($this->auth->getUser()['id']);
            $user->mobile = input('mobile/s', '');
            $user->username = input('username/s', '');
            $user->register = 1;
            $user->isUpdate(true)->allowField(true)->save();
        } catch (Exception $e) {
            throw new BaseException(['msg' => $e->getMessage(), 'errorCode' => '999', 'code' => '200']);
        }

        $this->success('用户名和手机号绑定成功');
    }

    public function login()
    {
        // 必须传递的字段
        $params = ['code'];
        (new MyValidate($params))->goCheck();

        try {
            $code = input('code/s');
            $app    = get_easywechat_app(config('wechat_mini.youlan'));
            $result = $app->auth->session($code);

        } catch (Exception $e) {
            throw new BaseException(['msg' => $e->getMessage(), 'errorCode' => '999', 'code' => '200']);
        }

        if (array_key_exists('errcode', $result) || array_key_exists('errmsg', $result)) {
            // 根据code获取openid失败, 返回错误信息
            $this->error($result['errmsg']);
        } else {
            $openid = $result['openid'];
            cache($result['openid'], $result['session_key'], 0);  // 缓存解密数据的签名
            $user = User::get(['openid' => $openid]);
            if ($user) {
                // 已经注册
                $ret =  $this->auth->direct($user->id);
                if ($ret) {
                    $this->success('登录成功', ['token' => $this->auth->getToken()]);
                } else {
                    $this->error('登录失败');
                }
            } else {
                // 还没有注册
                $ret = $this->auth->register_code($openid);
                if ($ret) {
                    $this->success('注册成功', ['token' => $this->auth->getToken()]);
                } else {
                    $this->error('注册失败');
                }
            }
        }
    }

    public function login_test()
    {
//        return json(config('database'));
        $openid = input('openid/s', '');
        $user = User::get(['openid' => $openid]);
        if ($user) {
            // 已经注册
            $ret =  $this->auth->direct($user->id);
            if ($ret) {
                $this->success('登录成功', ['token' => $this->auth->getToken()]);
            } else {
                $this->error('登录失败');
            }
        } else {
            // 还没有注册
            $ret = $this->auth->register_code($openid);
            if ($ret) {
                $this->success('注册成功');
            } else {
                $this->error('注册失败');
            }
        }
    }

    public function get_userinfo()
    {
        $userinfo = $this->auth->getUser();
        $userinfo['avatar'] = Request::instance()->domain() . $userinfo['avatar'];
        $this->success('获取成功', $userinfo);
    }



    public function get_user_mobile()
    {
        $openid = $this->auth->getUser()['openid'];
        $session_key = cache($openid);



        $this->success('ok', config('wechat_mini.youlan'));
    }

}
