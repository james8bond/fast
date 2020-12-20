<?php
/**
 *   User:   邦德
 *   Date:   2020/11/4 : 12:27
 */


namespace app\cs\exception;


class ParameterException extends BaseException
{
    public $code      = 400;
    public $errorCode = 10000;
    public $msg       = "invalid parameters";
}