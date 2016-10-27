<?php
namespace core\utils;

/**
 * 配置类
 * @author fangyutao
 * @date 2016-08-27
 */
final class Config
{
    /**
     * 全局配置
     * @var array
     */
    private static $_config = [];

    /**
     * 加载php配置文件
     * @param string $file 目录或文件
     */
    public static function load($file)
    {
        if (is_dir($file)) {
            $dirHandle = opendir($file);
            while (FALSE !== ($filename = readdir($dirHandle))) {
                $pathinfo = pathinfo($filename);
                if ($pathinfo['extension'] === 'php') {
                    self::set(include $file . '/' . $filename, $pathinfo['filename']);
                }
            }
        } elseif (is_file($file)) {
            $pathinfo = pathinfo($file);
            if ($pathinfo['extension'] === 'php') {
                self::set(include $file, $pathinfo['filename']);
            } else {
                throw new \Exception("Only Support *.php extension");
            }
        } else {
            throw new \Exception("FILE/DIR[{$file}] not Exists");
        }
    }

    /**
     * 获取配置
     * @param  string $name
     * @return mixed
     */
    public static function get($name = null)
    {
        if (empty($name)) {
            return self::$_config;
        } else {
            // 二维数组配置
            if (!strpos($name, '.')) {
                return isset(self::$_config[$name]) ? self::$_config[$name] : null;
            } else {
                $array = explode('.', $name);
                return isset(self::$_config[$array[0]][$array[1]]) ? self::$_config[$array[0]][$array[1]] : null;
            }
        }
    }

    /**
     * 设置配置
     * @param array|string $name
     * @param mixed $value
     */
    public static function set($name, $value = null)
    {
        // 单一设置
        if (is_string($name)) {
            if (!strpos($name, '.')) {
                self::$_config[$name] = $value;
            } else {
                // 二维数组配置
                $array = explode('.', $name);
                self::$_config[$array[0]][$array[1]] = $value;
            }
        } elseif (is_array($name)) {
            // 批量设置
            if (!empty($value)) {
                self::$_config[$value] = isset(self::$_config[$value])
                ? array_merge(self::$_config[$value], $name)
                : $name;
            } else {
                self::$_config = array_merge(self::$_config, $name);
            }
        } else {
            // 返回配置
            return self::$_config;
        }
    }

    /**
     * 判断配置是否存在
     * @param  string  $name
     * @return boolean
     */
    public static function has($name) {
        if (!strpos($name, '.')) {
            return isset(self::$_config[$name]);
        } else {
            $array = explode('.', $name);
            return isset(self::$_config[$array[0]][$array[1]]);
        }
    }

    /**
     * 重置
     */
    public static function reset()
    {
        self::$_config = [];
    }

}