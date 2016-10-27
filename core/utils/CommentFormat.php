<?php

namespace core\utils;

class CommentFormat
{
    public static function createCommentFormat($info, $isjson = false, $json = '')
    {
        $str = '';
        $str .=self::_buildTitle($info['title']);
        $str .=self::_buildType();
        $str .=self::_buildRequest(empty($info['request']) ? [] : $info['request']);
        $str .=self::_buildResponse($info['response'], $isjson, $json);
        $str .=Config::get('commentformat.FOOTER');
        return $str;
    }

    private static function _buildTitle($title)
    {
        return str_replace('{$title}', $title, Config::get('commentformat.TITLE'));
    }

    private static function _buildType($type = 'POST')
    {
        return str_replace('{$type}', $type, Config::get('commentformat.TYPE'));
    }

    private static function _buildRequest($request)
    {
        $str = Config::get('commentformat.REQUEST_HEADER');

        $format = Config::get('commentformat.REQUEST_BODY_ROW');

        foreach ($request as $value) {
            $str .= str_replace(['{$name}', '{$desc}'], ["\"{$value['name']}\"", "\"{$value['desc']}\""], $format);
        }

        $str .= Config::get('commentformat.REQUEST_FOOTER');

        return str_replace('{$tabspace}', Config::get('commentformat.TAB_SPACE'), $str);
    }

    private static function _buildResponse($response, $isjson = false, $json = '')
    {
        $str = Config::get('commentformat.RESPONSE_HEADER');

        $format = Config::get('commentformat.RESPONSE_BODY_ROW');

        foreach ($response as $key => $value) {
            if ($key == 'result' && empty($value['child']) && !$isjson) {
                continue;
            }
            $str .= str_replace(
                ['{$name}', '{$desc}'],
                ["\"{$value['name']}\"", $key == 'result' ? "// {$value['desc']}" : "\"{$value['desc']}\""],
                $format
            );
        }

        $formatExample = Config::get('commentformat.RESPONSE_EXAMPLIE');

        $replace = '';

        if ($isjson) {
            $replace = str_replace('{$prefix}', ' * {$tabspace}{$tabspace}', $json) . "\r\n";
        } elseif (!empty($response['result']['child'])) {
            $replace = self::_buildExample($response['result']['child']);
        }

        $str .= str_replace(
            '{$example}',
            $replace,
            $formatExample
        );

        $str .= Config::get('commentformat.RESPONSE_FOOTER');

        return str_replace('{$tabspace}', Config::get('commentformat.TAB_SPACE'), $str);
    }

    private static function _buildExample($example, $repeat = 2)
    {
        $header = Config::get('commentformat.EXAMPLE_HEADER');

        $footer = Config::get('commentformat.EXAMPLE_FOOTER');

        $format = Config::get('commentformat.EXAMPLE_BODY_ROW');

        $str = '';

        $i = 0;

        if (isset($example[0]['name']) && substr($example[0]['name'], 0, 3) == '+++') {
            $example[0]['name'] = substr($example[0]['name'], 3);
            $header = str_replace('{$prefix}', '{$prefix}{$tabspace}', $header);
            $footer = str_replace('{$prefix}', '{$prefix}{$tabspace}', $footer);
            $format = str_replace('{$prefix}', '{$prefix}{$tabspace}', $format);
            $header = Config::get('commentformat.EXAMPLE_ARR_HEADER') . $header;
            $footer = $footer . Config::get('commentformat.EXAMPLE_ARR_FOOTER');
            $i ++;
        }

        $str .= $header;

        foreach ($example as $value) {
            if (!empty($value['child'])) {
                $str .= str_replace(
                    ['{$name}', '{$desc}'],
                    ["\"{$value['name']}\"", empty($value['desc']) ? '' : "// {$value['desc']}"],
                    $format
                );
                $str .= self::_buildExample($value['child'], $repeat + $i + 2);
            } else {
                $str .= str_replace(
                    ['{$name}', '{$desc}'],
                    ["\"{$value['name']}\"", "\"{$value['desc']}\""],
                    $format
                );
            }
        }

        $str .= $footer;

        return str_replace('{$prefix}', str_repeat('{$tabspace}', $repeat), $str);
    }
}