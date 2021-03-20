<?php
/**
 *   User:   邦德
 *   Date:   2021/3/15 : 9:53
 */


namespace app\wechat\controller;

use app\common\controller\Api;
use app\wechat\libs\exception\BaseException;
use app\wechat\libs\validate\MyValidate;
use app\wechat\model\Coupon as CouponModel;
use app\wechat\model\Order as OrderModel;
use think\Exception;
use think\Log;


class Pay extends Api
{
    protected $noNeedLogin = ['receive_notify'];
    protected $noNeedRight = ['*'];

    protected $payment = null;

    public function __construct()
    {
        parent::__construct();
        $this->payment = get_easywechat_payment(config('wechat_mini.youlan'));
    }

    public function pay()
    {
        /**
         *  支付之前要做的检测
         *  1:  该订单是否存在?
         *  2:  该订单是否已经支付过了?
         *  3:  库存量的检测
         */
        // 必须传递的字段
        $params = ['order_no'];
        (new MyValidate($params))->goCheck();

        $order_no = input('order_no/s');
        $order    = OrderModel::get(['id' => $order_no,], ['product']);
        if (!$order) {
            $this->error('订单不存在');
        }

        $theme = $order->product->theme;
        $uid   = $order->user_id;
        $user  = \app\common\model\User::get($order->user_id);
        $user->limit .= ',' . $theme;     // 会员支付成功了, 才标注他已经参加过活动了
        $user->isUpdate(true)->allowField(true)->save();

//        return json($order);
//        $payment =  get_easywechat_payment(config('wechat_mini.youlan'));

        $result = $this->payment->order->unify([
            'body'         => '优蓝设计1314狂欢节',
            'out_trade_no' => $order_no,
            'total_fee'    => 1,
            'notify_url'   => 'https://brian.james8bond.top/wechat/pay/receive_notify', // 支付结果通知网址，如果不设置则会使用配置里的默认地址
            'trade_type'   => 'JSAPI', // 请对应换成你的支付方式对应的值类型
            'openid'       => $this->auth->getUser()['openid'],
        ]);
        try {
            $ret = OrderModel::update(['prepay_id' => $result['prepay_id']], ['id' => $order_no], true);
        } catch (Exception $e) {
            throw new BaseException(['msg' => $e->getMessage(), 'errorCode' => '1000', 'code' => '200']);
        }

        $jssdk  = $this->payment->jssdk;
        $config = $jssdk->bridgeConfig($result['prepay_id'], false);
// 创建优惠券应该在, 微信回调地址中, 做业务, 不应该在这里
        $data_               = [
            'name'       => '520',
            'store'      => '1',
            'own_id'     => $this->uid(),
            'product_id' => $order->product->id,
            'stock'      => '1',
        ];
        $coupon              = CouponModel::create($data_, true);
        $config['coupon_id'] = $coupon->id;

        $this->success('微信拉起支付的参数', $config);

// $config:
//{
//    "return_code": "SUCCESS",
//    "return_msg": "OK",
//    "appid": "wx2421b1c4390ec4sb",
//    "mch_id": "10000100",
//    "nonce_str": "IITRi8Iabbblz1J",
//    "openid": "oUpF8uMuAJO_M2pxb1Q9zNjWeSs6o",
//    "sign": "7921E432F65EB8ED0CE9755F0E86D72F2",
//    "result_code": "SUCCESS",
//    "prepay_id": "wx201411102639507cbf6ffd8b0779950874",
//    "trade_type": "JSAPI"
//}
        /**
         *  返回给客户端拉起微信支付的参数
         *  1:  timeStamp   时间戳                    自己写的
         *  2:  nonceStr    随机字符串                自己写的
         *  3:  package     预订单号 prepay_id        微信下单接口生成的
         *  4:  signType    签名类型，默认为MD5        自己写的
         *  5:  paySign     签名                      自己算的
         *  我需要的东西是 prepay_id 预订单号
         *
         */
    }


    public function receive_notify()
    {
        Log::init([
            'apart_level' => ['Bourne'] // 把Bourne设置成独立日志
        ]);
        Log::write('微信调用了接口', 'Bourne');

        $response = $this->payment->handlePaidNotify(function ($message, $fail) {
            // 你的逻辑
            Log::write($message, 'Bourne');
            return true;
            // 或者错误消息
            $fail('Order not exists.');
        });

        $response->send(); // Laravel 里请使用：return $response;

    }

    /**
     *  接收微信的回调通知
     */
    public function receive_notify_1()
    {
        /**
         *  微信回调, 确定支付成功
         *  检测库存量, 超卖
         *  减库存量
         *  更新订单的状态 status = 1 已经付款
         */


        Log::init([
            'apart_level' => ['Bourne'] // 把Bourne设置成独立日志
        ]);
        Log::write('微信回调了', 'Bourne');

        $response = $this->payment->handlePaidNotify(function ($message, $fail) {
            // 你的逻辑
            // Log::write($message, 'Bourne');
            /**
             *  1:  支付成功后, 会员都会获取一张卷, 创建一条优惠券记录
             *  2:  标注该会员已经参加过该活动了, 更新用户的limit字段的状态
             *  3:  更新订单状态
             *  4:  库存量减1
             *
             *
             */

            Log::write($message, 'Bourne');

            $order_no = $message['out_trade_no'];

            try {
                // 更新订单状态, status = 1 已经支付
                $order         = OrderModel::get(['id' => $order_no], ['product', 'client']);
                $user          = $order->client;
                $product       = $order->product;
                $order->status = 1;
                $order->isUpdate(true)->save();


                $limit_arr   = explode(",", $user->limit);
                $limit_arr[] = $product->theme;
                $user->isUpdate(true)->save(['limit' => join(",", $limit_arr)]);

            } catch (Exception $e) {
                $fail('Order not exists.');
            }


//            return true;
            // 或者错误消息
//            $fail('Order not exists.');
        });

        $response->send();



    }

    // 模拟支付
    public function false_pay()
    {

        Log::init([
            'apart_level' => ['Bourne'] // 把Bourne设置成独立日志
        ]);
        Log::write('微信回调了', 'Bourne');

        return $this->auth->getUser()['openid'];

        /*
        $order_no = 179;

        try {
            // 更新订单状态, status = 1 已经支付
            $order         = OrderModel::get(['id' => $order_no], ['product', 'client']);
            $user          = $order->client;
            $product       = $order->product;
            $order->status = 1;
            $order->isUpdate(true)->save();


            $limit_arr   = explode(",", $user->limit);
            $limit_arr[] = $product->theme;
            $user->isUpdate(true)->save(['limit' => join(",", $limit_arr)]);

        } catch (Exception $e) {
            throw new BaseException(['msg' => 'KKK', 'errorCode' => '999', 'code' => '200']);
        }

        $this->success('ok');

        */
    }

}