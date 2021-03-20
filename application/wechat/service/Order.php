<?php
/**
 *   User:   邦德
 *   Date:   2021/3/18 : 13:57
 */


namespace app\wechat\service;

use app\common\model\User;
use app\wechat\libs\exception\BaseException;
use app\wechat\model\Order as OrderModel;
use app\wechat\model\Product as ProductModel;
use fast\Random;
use think\Exception;
use think\Request;

class Order
{
    public function __construct()
    {

    }

    public static function place_order($product_id = '', $uid = '')
    {
        if (empty($product_id) || empty($uid)) {
            exception('订单号不能为空或uid不能为空');
        }
        $params  = [];
        $product = ProductModel::get($product_id);
        $user    = User::get($uid);
        if (!$user) {
            exception('用户不存在');
        }
        if (in_array($product['theme'], explode(",", $user->limit))) {
            exception('你已经参加过该活动了, 不能再参加了');
        }
        if (!$product) {
            exception('该商品不存在 !!');
        }
        if ($product->status == 1) {
            exception('该商品已经下架');
        }
        if ($product->stock <= 1) {
            exception('库存不足 !!');
        }

        if (in_array($product['theme'], explode(",", $user->limit))) {
            exception('已经参加过该活动了, 不能再参加了');
        }


        $params['product_id']  = $product_id;
        $params['order_no']    = \app\wechat\controller\Order::makeOrderNo();
        $params['user_id']     = $uid;
        $params['total_price'] = $product->price;
        $params['total_count'] = 1;

        $user->limit .= ',' . $product->theme;
        $user->isUpdate(true)->save();

        $order = OrderModel::create($params, true);
        return $order->order_no;

    }

    public static function createMiniCode($order_no = '')
    {
        if (empty($order_no)) {
            throw new BaseException(['msg' => '订单号 order_no 不能为空', 'errorCode' => '1000', 'code' => '200']);
        }
        /**
         *  用于核销的二维码, 里面只存储订单号
         *  page 页面的路径可以写死
         *  因为是订单的二维码, 所以要有订单的id
         *  在创建订单的时候, 就应该创建订单的核销二维码
         */
        $app      = get_easywechat_miniProgram(config('wechat_mini.youlan'));
        $scene    = $order_no;
        $page     = 'pages/verfy/index';
        $options  = [
            'page'  => $page,
//            'width' => 280,
        ];
        $url      = 'uploads' . DS . 'qrcode' . DS;
        $filename = strtoupper(Random::alpha(32));
        try {
            $response = $app->app_code->getUnlimit($scene, $options);

            if ($response instanceof \EasyWeChat\Kernel\Http\StreamResponse) {
                $filename_ = $response->saveAs(config('common.upload_path') . 'qrcode' . DS, $filename . '.png');
            } else {
                throw new BaseException(['msg' => '生成二维码时,出错', 'errorCode' => '1000', 'code' => '200']);
            }
            $url .= $filename_;

            OrderModel::update(['qrcode_url' => $url], ['id' => $order_no], true);
        } catch (Exception $e) {
            throw new BaseException(['msg' => $e->getMessage(), 'errorCode' => '1001', 'code' => '200']);
        }
        return $url;
    }

    public static function test()
    {
        $request = Request::instance();
        $token = $token = $request->server('HTTP_TOKEN', $request->request('token', \think\Cookie::get('token')));

        $uid = get_uid();
        return $uid;
    }
}