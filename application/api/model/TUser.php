<?php
/**
 *   User:   邦德
 *   Date:   2021/2/15 : 14:06
 */


namespace app\api\model;


use think\Model;

class TUser extends Model
{
    protected $table = 'tp_user';

    protected $pk = 'id';

    protected $resultSetType = 'collection';

    protected $hidden = ['weigh', 'createtime', 'updatetime', 'deletetime'];

    //protected $visible = ['url'];

    protected $createTime = 'createtime';

    protected $updateTime = 'updatetime';

    protected $autoWriteTimestamp = 'datetime';

    protected $type = [
    	'desc'    =>  'json',
    ];


}