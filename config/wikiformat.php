<?php

return [
    'TAB_SPACE'             => "    ",

    'TITLE'                 => "== {\$title} ==\r\n",

    'URL'                   => "=== 接口名称 ===\r\n" . ": <big>''{\$url} ''</big>\r\n",

    'DESC_TITLE'            => "=== 接口描述 ===\r\n",
    'DESC_ROW'              => ": {\$descrow}\r\n",

    'REQUEST_HEADER'        => "=== 请求消息 ===\r\n{| class=\"wikitable\" width=\"800\"\r\n|参数名||必填||类型||描述\r\n",
    'REQUEST_BODY_ROW'      => "|-\r\n| {\$name} || {\$required} || {\$type} ||{\$desc}\r\n",
    'REQUSET_FOOTER'        => "|}\r\n",

    'RESPONSE_HEADER'       => "=== 应答消息 ===\r\n{| class=\"wikitable\" width=\"800\"\r\n|参数名||类型||描述\r\n",
    'RESPONSE_BODY_ROW'     => "|-\r\n| {\$name} || {\$type} || {\$desc}\r\n",
    'RESPONSE_EXAMPLIE'     => "|-\r\n|colspan=\"6\"|\r\n示例：\r\n{\$tabspace}{\r\n{\$header}{\$example}{\$tabspace}}\r\n",
    'RESPONSE_FOOTER'       => "|}\r\n",

    'EXAMPLE_HEADER'        => "{\$prefix}{\r\n",
    'EXAMPLE_BODY_ROW'      => "{\$prefix}{\$tabspace}{\$name} : {\$desc}\r\n",
    'EXAMPLE_FOOTER'        => "{\$prefix}}\r\n",

    'EXAMPLE_ARR_HEADER'    => "{\$prefix}[\r\n",
    'EXAMPLE_ARR_FOOTER'    => "{\$prefix}]\r\n"
];