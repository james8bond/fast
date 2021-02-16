<?php
/**
 *   User:   邦德
 *   Date:   2021/2/16 : 22:38
 */

namespace app\wechat\behavior;


use think\Log;

class Test
{
    public function bourne()
    {
        Log::init([
            'apart_level' => ['Bourne'] // 把Bourne设置成独立日志
        ]);
        Log::write('action_init is woking !!', 'Bourne');
    }
}