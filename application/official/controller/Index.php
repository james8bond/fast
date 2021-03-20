<?php
/**
 *   User:   邦德
 *   Date:   2021/3/4 : 14:13
 */


namespace app\official\controller;

use app\common\controller\Api;

class Index extends Api
{
    protected $noNeedLogin = ['*'];
    protected $noNeedRight = ['*'];

    public function index()
    {
        $test_arr = array('a' => 20, 'b' => 30, 'c' => 50);

        $prize_arr = array(
            '0' => array('id' => 1, 'prize' => 'MAC', 'rate' => 1),
            '1' => array('id' => 2, 'prize' => 'iPhone', 'rate' => 5),
            '2' => array('id' => 3, 'prize' => 'iPad', 'rate' => 10),
            '3' => array('id' => 4, 'prize' => 'iWatch', 'rate' => 12),
            '4' => array('id' => 5, 'prize' => 'iPod', 'rate' => 22),
            '5' => array('id' => 6, 'prize' => '抱歉!再接再厉', 'rate' => 50),
        );
        foreach ($prize_arr as $key => $val) {
            $arr[$val['id']] = $val['rate'];
        }

        $rid = $this->get_rand($arr);
//        $rid = get_rand($arr); //根据概率获取奖项id

//        $res['yes'] = $prize_arr[$rid-1]['prize']; //中奖项
        $result['item']  = $rid;
        $result['prize'] = $prize_arr[$rid - 1]['prize'];

        $this->success('ok', $result);


//       $result =  $this->get_rand($test_arr);
//
//        $this->success('ok', $result);

    }

    protected function get_rand($proArr)
    {
        $result = '';
        //概率数组的总概率精度
        $proSum = array_sum($proArr);
        //概率数组循环
        foreach ($proArr as $key => $proCur) {
            $randNum = mt_rand(1, $proSum);
            if ($randNum <= $proCur) {
                $result = $key;
                break;
            } else {
                $proSum -= $proCur;
            }
        }
        unset ($proArr);
        return $result;
    }
}