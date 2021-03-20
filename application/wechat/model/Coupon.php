<?php
/**
 *   User:   邦德
 *   Date:   2021/3/18 : 10:29
 */


namespace app\wechat\model;

use think\Model;

class Coupon extends Model
{
    protected $table = 'yl_coupon';

    protected $pk = 'id';

    protected $resultSetType = 'collection';

    protected $hidden = ['weigh', 'updatetime', 'deletetime'];

    protected $createTime = 'createtime';

    protected $updateTime = 'updatetime';

    protected $autoWriteTimestamp = 'int';
    
    public function product()
    {
        return $this->belongsTo('Product','product_id', 'id');
    }
    
    public function store()
    {
        return $this->belongsTo('Store','store_id', 'id');
    }
}