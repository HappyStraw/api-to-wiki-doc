<?php

return [
    'TAB_SPACE'             => "    ",

    'TITLE'                 => "/**\r\n * {\$title}\r\n",

    'TYPE'                  => " * 请求方式：{\$type}\r\n",

    'REQUEST_HEADER'        => " * 请求参数：\r\n",
    'REQUEST_BODY_ROW'      => " * {\$tabspace}{\$name} : {\$desc}\r\n",
    'REQUSET_FOOTER'        => " * \r\n",

    'RESPONSE_HEADER'       => " * 返回结果：\r\n * {\r\n",
    'RESPONSE_BODY_ROW'     => " * {\$tabspace}{\$name} : {\$desc}\r\n",
    'RESPONSE_EXAMPLIE'     => "{\$example}",
    'RESPONSE_FOOTER'       => " * }\r\n",

    'FOOTER'                => " */",

    'EXAMPLE_HEADER'        => " * {\$prefix}{\r\n",
    'EXAMPLE_BODY_ROW'      => " * {\$prefix}{\$tabspace}{\$name} : {\$desc}\r\n",
    'EXAMPLE_FOOTER'        => " * {\$prefix}}\r\n",

    'EXAMPLE_ARR_HEADER'    => " * {\$prefix}[\r\n",
    'EXAMPLE_ARR_FOOTER'    => " * {\$prefix}]\r\n"
];