<?php
/**
 *   User:   邦德
 *   Date:   2021/3/17 : 15:46
 */


namespace app\wechat\controller;

use app\common\controller\Api;
use app\wechat\libs\exception\BaseException;
use app\wechat\libs\validate\MyValidate;
use app\wechat\model\Coupon as CouponModel;
use think\Db;
use think\Exception;
use function Complex\asec;

class Coupon extends Api
{
    protected $noNeedLogin = [''];
    protected $noNeedRight = ['*'];

    /**
     *  领取优惠券
     */
    public function get_free_coupon()
    {
        // 必须传递的字段
        $params = ['coupon_id'];
        (new MyValidate($params))->goCheck();

        /**
         *  领券之前的操作
         *      1:  券是否存在
         *      2:  是否还剩余次数, 有的券可以领两次
         *      3:  自己发的券, 自己不能领
         *      4:  已经领过券的, 不能在次领取
         *      5:  已经购买过该券中商品的, 不能领取
         *
         *
         *  领券之后的操作
         *      1:  给领券的会员创建一条已支付过的订单, 待核销
         *      2:  券的领取次数减一
         *
         */

        Db::startTrans();
        try {
            $coupon = CouponModel::get(input('coupon_id/d'));
            if (!$coupon) {
                $this->error('该券不存在, 请检查');
            }

            if ($coupon->stock <= 0) {
                $this->error($coupon->name . '已经领完了');
            }
            $coupon->stock -= 1;   // 券的库存减1
            $coupon->isUpdate(true)->allowField(true)->save();
            /**
             *  创建订单, 并且是已经支付的订单
             */
            $product_id = $coupon->product_id;
            $order_no = \app\wechat\service\Order::place_order($product_id, $this->uid());


            Db::commit();
        } catch (Exception $e) {
            Db::rollback();
            throw new BaseException(['msg' => $e->getMessage(), 'errorCode' => '1000', 'code' => '200']);
        }
        $this->success('领取成功', ['order_no' => $order_no]);
    }


}