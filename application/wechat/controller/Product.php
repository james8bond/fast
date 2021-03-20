<?php
/**
 *   User:   邦德
 *   Date:   2021/3/12 : 9:56
 */


namespace app\wechat\controller;


use app\common\controller\Api;
use app\common\model\User;
use app\wechat\libs\exception\BaseException;
use app\wechat\libs\validate\MyValidate;
use app\wechat\model\Product as ProductModel;
use think\Db;
use think\Exception;

class Product extends Api
{
    protected $noNeedLogin = [''];
    protected $noNeedRight = ['*'];

    public function index()
    {
        $this->success('ok');
    }

    public function detail()
    {
        // 必须传递的字段
        $params = ['id'];
        (new MyValidate($params))->goCheck();

        try {
            $detail = ProductModel::get(input('id/d'));
        } catch (Exception $e) {
            throw new BaseException(['msg' => $e->getMessage(), 'errorCode' => '999', 'code' => '200']);
        }
        $this->success('ok', $detail);
    }

    /**
     *  免费领取一份商品
     */
    public function get_free_product()
    {
        /**
         *  参数, product_id, 用户名和手机号
         *  表单提交报名, 提交姓名和手机号
         *  创建一条已经付款的订单, 因为是免费领取
         *
         */
        Db::startTrans();
        try {
            $user = User::get($this->auth->getUser()['id']);
            $user->mobile   = input('mobile/s', '');
            $user->username = input('username/s', '');
            $user->register = 1;

            $product = ProductModel::get(input('product_id/d'));
            if (!$product) {
                $this->error('商品不存在');
            }



            $user->isUpdate(true)->allowField(true)->save();
            Db::commit();
        } catch (Exception $e) {
            Db::rollback();
            throw new BaseException(['msg' => $e->getMessage(), 'errorCode' => '999', 'code' => '200']);
        }
    }


}