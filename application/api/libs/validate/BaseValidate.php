<?php
/**
 *   User:   邦德
 *   Date:   2020/11/4 : 9:57
 */

namespace app\api\libs\validate;


use app\api\libs\exception\ParameterException;
use think\Request;
use think\Validate;

class BaseValidate extends Validate
{

    // 指定客户端(Client)传递过来参数的字段
    public function getDataByRule($params)
    {
        $newArray = [];
        foreach ($this->rule as $key => $item) {
            $newArray[$key] = $params[$key];
        }

        return $newArray;
    }



    public function goCheck()
    {
        //获取http传入的参数,对这些参数做校验
        $request = Request::instance();
        $params  = $request->param();
        $result  = $this->batch()->check($params);
        if (!$result) {
            throw new ParameterException([
                'msg'       => $this->error,
                'error_code' => 10101,
                'code'      => 400
            ]);
        } else {
            return true;
        }
    }


    // 判断参数是否是整正数
    protected function isPostiveInteger($value, $rule = '', $data = '', $field = '')
    {
        if (is_numeric($value) && is_int($value + 0) && ($value + 0) > 0) {
            return true;
        } else {
            return false;
        }
    }


    // 判断是否是手机号
    protected function isMobile($value)
    {
        $rule   = '^1(3|4|5|7|8)[0-9]\d{8}$^';
        $result = preg_match($rule, $value);
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    protected function isNotEmpty($value, $rule = '', $data = '', $field = '')
    {
        if (empty($value)) {
            return false;
        } else {
            return true;
        }
    }
}