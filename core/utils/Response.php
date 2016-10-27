<?php
namespace core\utils;

/**
 * 返回http请求
 * @author fangyutao
 * @date 2016-08-27
 */
class Response
{
    const AJAXRETURN_SUCCESS = 0;

    const AJAXRETURN_FAIL    = 1;

    public static function ajaxReturn($data)
    {
        die(json_encode($data));
    }
}