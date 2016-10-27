<?php
function objectToArray ($object)
{
    $array = is_object($object) ? get_object_vars($object) : $object;

    if (is_array($array)) {
        return array_map(__FUNCTION__, $array);
    } else {
        return $array;
    }
}

function arrayToObject ($array)
{
    if (is_array($array)) {
        return (object) array_map(__FUNCTION__, $array);
    } else {
        return $array;
    }
}

function indentJson ($json, $prefix = '')
{
    $result = '';

    $pos = 0;

    $strLen = strlen($json);

    $indentStr = '    ';

    $newLine = "\r\n";

    $prevChar = '';

    $outOfQuotes = true;

    for ($i = 0 ; $i <= $strLen; $i ++) {

        // 获取下一个字符
        $char = substr( $json , $i , 1 );

        // 判断是否在 " 双引号内部
        if ($char == '"' && $prevChar != '\\') {

            $outOfQuotes = !$outOfQuotes;

            // 如果在结束字符外部就另起一行
        } else if (($char == '}' || $char == ']') && $outOfQuotes) {

            $result .= $newLine;

            $pos --;

            $result .= $prefix . str_repeat($indentStr, $pos);

        }

        // 添加 : 前后空格
        if ($char == ':' && $outOfQuotes) {
            $char = ' : ';
        }

        // 添加字符
        $result .= $char;

        // 如果是开始字符就另起一行并添加空格缩进
        if (($char == ',' || $char == '{' || $char == '[') && $outOfQuotes) {

            $result .= $newLine;

            if ($char == '{' || $char == '[' ) {

                $pos ++;

            }

            $result .= $prefix . str_repeat($indentStr, $pos);

        }

        $prevChar = $char;

    }

    return $prefix . $result;

}