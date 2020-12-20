<?php
/**
 *   User:   邦德
 *   Date:   2020/11/4 : 12:21
 */


namespace app\api\libs\exception;

use Exception;
use think\exception\Handle;
use think\Request;

class ExceptionHandle extends Handle
{
    private $code;
    private $msg;
    private $errorCode;

    public function render(Exception $e)
    {
        if ($e instanceof BaseException) {
            $this->code      = $e->code;
            $this->msg       = $e->msg;
            $this->errorCode = $e->errorCode;
        } else {
            if (config('app_debug')) {
                return parent::render($e);
            } else {
                $this->code      = 500;
                $this->msg       = '服务器内部错误, 不想告诉你';
                $this->errorCode = 999;
            }
        }

        $request = Request::instance();
//        $result  = [
//            'msg'         => $this->msg,
//            'error_code'  => $this->errorCode,
//            'request_url' => $request = $request->url()
//        ];
        $result = [
            'data' => [],
            'meta' => [
                'msg'        => $this->msg,
                'error_code' => $this->errorCode,
                'url'        => $request = $request->url()
            ]
        ];

        return json($result, $this->code);

    }
}