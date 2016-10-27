<?php
require __DIR__ . '/core/autoload.php';

$action = \core\utils\Request::get('get.action', '', 'trim');

if (!empty($action)) {
    \core\TODO::$action();
}

