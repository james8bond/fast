<?php
/**
 *   User:   邦德
 *   Date:   2021/3/11 : 13:14
 */


namespace app\wechat\model;

use think\Model;
use think\Request;

class Order extends Model
{
    protected $table = 'yl_order';

    protected $pk = 'id';

    protected $resultSetType = 'collection';

    protected $hidden = ['weigh', 'updatetime', 'deletetime'];

    //protected $visible = ['url'];

    protected $createTime = 'createtime';

    protected $updateTime = 'updatetime';

    protected $autoWriteTimestamp = 'int';
    
    public function product()
    {
        return $this->belongsTo('Product','product_id', 'id');
    }

    public function client()
    {
        return $this->belongsTo('app\\common\\model\\User','user_id', 'id');
    }

    public function getQrcodeUrlAttr($value, $data)
    {
        return Request::instance()->domain() . DS . $value;
    }


}