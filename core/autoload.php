<?php
// 定义目录
define('_ROOT_', __DIR__ . '/../');
define('_CONFIG_', _ROOT_ . 'config/');
define('_CORE_', _ROOT_ . 'core/');
define('_DATA_', _ROOT_ . 'data/');

// 包含自定义函数
require _CORE_ . 'funcs/myfuncs.php';

// autoload
function _my_autoload($class)
{
    $filename = _ROOT_ . str_replace("\\", '/', $class) . '.php';

    if (file_exists($filename)) {
        require $filename;
    } else {
        throw new \Exception("The Class[{$class}] not exists");
    }
}

// 注册 autoload
spl_autoload_register('_my_autoload');

// 加载配置
\core\utils\Config::load(_CONFIG_);
