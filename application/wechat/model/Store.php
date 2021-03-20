<?php
/**
 *   User:   邦德
 *   Date:   2021/3/16 : 16:26
 */


namespace app\wechat\model;

use think\Model;

class Store extends Model
{
    protected $table = 'yl_store';

    protected $pk = 'id';

    protected $resultSetType = 'collection';

    protected $hidden = ['weigh', 'createtime', 'updatetime', 'deletetime'];


    protected $createTime = 'createtime';

    protected $updateTime = 'updatetime';

    protected $autoWriteTimestamp = 'int';

}