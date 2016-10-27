<?php
namespace core\utils;

/**
 * 获取http请求
 * @author fangyutao
 * @date 2016-08-27
 */
class Request
{
    public static function get($name, $default = null, $func = null)
    {
        if (!strpos($name, '.')) {
            $type = isset($_SERVER['REQUEST_METHOD']) ? strtolower($_SERVER['REQUEST_METHOD']) : 'post';
            $type = '_' . $type;
            return self::$type($name, $default, $func);
        } else {
            $array = explode('.', $name);
            $type = strtolower($array[0]);
            $type = '_' . $type;
            return self::$type($array[1], $default, $func);
        }
    }

    private static function _get($name, $default = null, $func = '')
    {
        $data = isset($_GET[$name]) ? $_GET[$name] : $default;

        return empty($func) ? $data : $func($data);
    }

    private static function _post($name, $default = null, $func = '')
    {
        $data = isset($_POST[$name]) ? $_POST[$name] : $default;

        return empty($func) ? $data : $func($data);
    }
}