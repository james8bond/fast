<?php
/**
 *   User:   邦德
 *   Date:   2021/3/11 : 10:46
 */


namespace app\wechat\controller;


use app\common\controller\Api;
use app\common\model\User;
use app\wechat\libs\exception\BaseException;
use app\wechat\libs\validate\MyValidate;
use app\wechat\model\Order as OrderModel;
use app\wechat\model\Product as ProductModel;
use fast\Random;
use think\Exception;
use think\Log;
use think\Request;
use app\wechat\service\Order as Server;

class Order extends Api
{
    protected $noNeedLogin = [''];
    protected $noNeedRight = ['*'];

    protected $app = null;

    public function __construct()
    {
        parent::__construct($request = null);
        $this->app = get_easywechat_miniProgram(config('wechat_mini.youlan'));
    }

    /**
     *  下单接口
     */
    public function place_order()
    {
        /**
         *  下单前的检测
         *  1: 商品是否可以购买, 查看商品的status状态
         *  2: 商品的库存量, 为0时, 不能下单
         *
         */
        // 必须传递的字段
        $params = ['product_id', 'store_id'];
        (new MyValidate($params))->goCheck();


        $params               = [];
        $params['product_id'] = input('product_id/d');                      // 商品id
        $params['store_id']   = input('store_id/d');                        // 店id
        $total_count          = 1;                                               // 购买的数量, 商品数量只会有一件, 可以多次核销

        $product = ProductModel::get($params['product_id']);
        // 用户只能参加一次活动
        $user = User::get($this->auth->getUser()['id']);


        if (in_array($product['theme'], explode(",", $user->limit))) {
            $this->error('已经参加过该活动了, 不能再参加了');
        }

        if (!$product) {
            $this->error('该商品不存在 !!');
        }
        if ($product->status == 1) {
            $this->error('该商品已经下架');
        }
        if ($product->stock < $total_count) {
            $this->error('库存不足 !!');
        }

        $params['order_no']    = self::makeOrderNo();                  // 订单号
        $params['user_id']     = $this->auth->getUserinfo()['id'];     // 购买人id
        $params['total_price'] = $total_count * $product->price;       // 订单总金额
        $params['total_count'] = $total_count;                         // 商品数量
        try {
            $order = OrderModel::create($params, true);
        } catch (Exception $e) {
            throw new BaseException(['msg' => $e->getMessage(), 'errorCode' => '999', 'code' => '200']);
        }

        $order_              = OrderModel::get($order->id);
        $arr                 = $order_->visible(['name', 'order_no', 'createtime', 'total_price', 'id'])->toArray();
        $arr['product_name'] = $product->name;
        $arr['createtime']   = date('Y-m-d H:i:s', $arr['createtime']);
        $this->success('下单成功', $arr);

    }

    /**
     *  订单号的生成
     */
    public static function makeOrderNo()
    {
        $yCode   = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');
        $orderSn =
            $yCode[intval(date('Y')) - 2017] . strtoupper(dechex(date('m'))) . date(
                'd') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf(
                '%02d', rand(0, 99));
        return $orderSn;
    }

    /**
     *  核销订单
     */
    public function verify()
    {
        // 必须传递的字段
        $params = ['order_no'];
        (new MyValidate($params))->goCheck();

        /**
         *  谁扫码登录的, 谁就是核销人
         *  核销第一步, 判断核销条件
         *      1:  待核销的订单是否存在
         *      2:  扫码核销的会员是否具有核销员的权限
         *      3:  待核销的订单是否已经支付过了
         *      4:  待核销的订单是否已经核销过了
         *      5:
         *  核销第二步, 核销
         *      1:  更新订单的verification的状态, 置1;
         */

        $order_id = input('order_no/d');
        Log::init([
            'apart_level' => ['Bourne'] // 把Bourne设置成独立日志
        ]);
        Log::write($order_id, 'Bourne');




        $verify_user = $this->uid();
        try {
            $order = OrderModel::get(input('order_no/d'));
            if (!$order) {
                $this->error('订单不存在');
            }
            if ($order->verification == 1) {
                $this->error('订单已经核销过了');
            }

            $order->verification = 1;
            $order->verify_user  = $verify_user;
            $order->isUpdate(true)->allowField(true)->save();
        } catch (Exception $e) {
            throw new BaseException(['msg' => $e->getMessage(), 'errorCode' => '1000', 'code' => '200']);
        }

        $this->success('核销成功');
    }

    public function get_order_by_user()
    {
        // 必须传递的字段
        $params = ['page', 'size'];
        (new MyValidate($params))->goCheck();
        $page = input('page/d', 1);
        $size = input('size/d', 15);
        $uid  = $this->auth->getUser()['id'];


        $list = OrderModel::where('user_id', '=', $uid)
            ->with(['product'])
            ->order('createtime desc')
            ->paginate($size, true, ['page' => $page]);

        $list->hidden(['prepay_id', 'product_id', 'user_id', 'sale_count', 'next_item', 'product' => ['id', 'price', 'stock', 'theme', 'status']]);
        $this->success('ok', $list);
    }

    public function get_detail()
    {
        // 必须传递的字段
        $params = ['id'];
        (new MyValidate($params))->goCheck();

        $detail = OrderModel::get(input('id/d'));
        $detail->hidden(['prepay_id']);
        if (!$detail) {
            $this->error('订单不存在');
        } else {
            $this->success('ok', $detail);
        }

    }

    /**
     *  创建小程序码
     */
    public function create_mini_code()
    {
        // 必须传递的字段
        $params = ['order_no'];
        (new MyValidate($params))->goCheck();

        $result = Server::createMiniCode(input('order_no/d'));

        $this->success('ok', ['qrcode_url' => $result]);
    }

    public function create_mini_code_()
    {
        $result = Server::test();
        $this->success('ok', $result);
    }


}
