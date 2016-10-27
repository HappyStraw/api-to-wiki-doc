<?php
namespace core\utils;

/**
 * wiki格式类
 * @author fangyutao
 * @date 2016-08-27
 */
class WikiFormat
{
    public static function createWikiFormat($info, $isjson = false, $json = '')
    {
        $str = '';

        $str .= self::_buildTitle($info['title']);

        $str .= self::_buildUrl($info['url']);

        $str .= self::_buildDesc($info['desc']);

        $str .= self::_buildRequest(empty($info['request']) ? [] : $info['request']);

        $str .= self::_buildResponse($info['response'], $isjson, $json);

        return $str;
    }

    private static function _buildTitle($title)
    {
        $titleFormat = Config::get('wikiformat.TITLE');

        return str_replace('{$title}', $title, $titleFormat);
    }

    private static function _buildUrl($url)
    {
        $urlFormat = Config::get('wikiformat.URL');

        return str_replace('{$url}', $url, $urlFormat);
    }

    private static function _buildDesc($desc)
    {
        $descTitle = Config::get('wikiformat.DESC_TITLE');

        $descFormat = Config::get('wikiformat.DESC_ROW');

        $str = $descTitle;

        $arrDesc = explode("\n", $desc);

        foreach ($arrDesc as $value) {
            $str .= str_replace('{$descrow}', trim($value, "\r"), $descFormat);
        }

        return $str;
    }

    private static function _buildRequest($request)
    {
        $str = Config::get('wikiformat.REQUEST_HEADER');

        $requestFormat = Config::get('wikiformat.REQUEST_BODY_ROW');

        foreach ($request as $value) {
            $required = $value['required'] == 1 ? '是' : '否';
            $str .= str_replace(
                ['{$name}', '{$required}', '{$type}', '{$desc}'],
                [$value['name'], $required, $value['type'], $value['desc']],
                $requestFormat
            );
        }

        $str .= Config::get('wikiformat.REQUSET_FOOTER');

        return $str;
    }

    private static function _buildResponse($response, $isjson = false, $json = '')
    {
        $str = Config::get('wikiformat.RESPONSE_HEADER');

        $rowFormat = Config::get('wikiformat.RESPONSE_BODY_ROW');

        foreach ($response as $key => $value) {
            if ($key == 'result' && !$isjson && empty($value['child'])) continue;
            $str .= str_replace(
                ['{$name}', '{$type}', '{$desc}'],
                [$value['name'], $value['type'], $value['desc']],
                $rowFormat
            );
        }

        $strHeader = "{\$tabspace}{\$tabspace}\"{$response['errno']['name']}\"  : \"{$response['errno']['desc']}\"\r\n";

        $strHeader .= "{\$tabspace}{\$tabspace}\"{$response['errmsg']['name']}\" : \"{$response['errmsg']['desc']}\"\r\n";

        $strHeader .= ($isjson ||  !empty($response['result']['child']))
            ? "{\$tabspace}{\$tabspace}\"{$response['result']['name']}\" : // {$response['result']['desc']}\r\n"
            : '';

        $formatExample = str_replace('{$header}', $strHeader, Config::get('wikiformat.RESPONSE_EXAMPLIE'));

        $tabspace = Config::get('wikiformat.TAB_SPACE');

        $replace = '';

        if ($isjson) {
            $replace = str_replace('{$prefix}', str_repeat('{$tabspace}', 3), $json) . "\r\n";
        } elseif (!empty($response['result']['child'])) {
            $replace = self::_buildExample($response['result']['child']);
        }

        $str .= str_replace(
            '{$example}',
            $replace,
            $formatExample
        );

        $str .= Config::get('wikiformat.RESPONSE_FOOTER');

        return str_replace('{$tabspace}', $tabspace, $str);
    }

    private static function _buildExample($example, $repeat = 3)
    {
        $header = Config::get('wikiformat.EXAMPLE_HEADER');

        $footer = Config::get('wikiformat.EXAMPLE_FOOTER');

        $format = Config::get('wikiformat.EXAMPLE_BODY_ROW');

        $str = '';

        $i = 0;

        if (isset($example[0]['name']) && substr($example[0]['name'], 0, 3) == '+++') {
            $example[0]['name'] = substr($example[0]['name'], 3);
            $header = str_replace('{$prefix}', '{$prefix}{$tabspace}', $header);
            $footer = str_replace('{$prefix}', '{$prefix}{$tabspace}', $footer);
            $format = str_replace('{$prefix}', '{$prefix}{$tabspace}', $format);
            $header = Config::get('wikiformat.EXAMPLE_ARR_HEADER') . $header;
            $footer = $footer . Config::get('wikiformat.EXAMPLE_ARR_FOOTER');
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