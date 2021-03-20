<?php
/**
 *   User:   邦德
 *   Date:   2021/3/10 : 15:46
 */


namespace app\wechat\model;


use think\Request;

class Product extends BaseModel
{
    protected $table = 'fa_product';

    protected $pk = 'id';

    protected $resultSetType = 'collection';

    protected $hidden = ['weigh', 'createtime', 'updatetime', 'deletetime'];

    //protected $visible = ['url'];

    protected $createTime = 'createtime';

    protected $updateTime = 'updatetime';

    protected $autoWriteTimestamp = 'datetime';

    public function getHeaderimagesAttr($value, $row)
    {
        return Request::instance()->domain() . $value;
    }

}