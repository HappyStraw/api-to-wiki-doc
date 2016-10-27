<?php
namespace core;

use core\utils\CommentFormat;
use core\utils\Request;
use core\utils\Response;
use core\utils\WikiFormat;

/**
 * 操作类
 * @author fangyutao
 * @date 2016-08-27
 */
class TODO
{
    public static function test()
    {
        var_dump('dadadada');
    }

    public static function getOptList()
    {
        $dirHandle = opendir(_DATA_);

        $array = [];

        while (FALSE !== ($file = readdir($dirHandle))) {
            $pathinfo = pathinfo($file);
            if ($pathinfo['extension'] == 'json') {
                $array[] = urldecode($pathinfo['filename']);
            }
        }

        Response::ajaxReturn($array);
    }

    public static function savetojson()
    {
        $title = Request::get('post.title', '', 'trim');

        $info = Request::get('post.info', []);

        if ('' === $title) Response::ajaxReturn(['error' => Response::AJAXRETURN_FAIL, 'msg' => '配置名不为空']);

        if (empty($info)) Response::ajaxReturn(['error' => Response::AJAXRETURN_FAIL, 'msg' => '配置信息不为空']);

        $flag = file_put_contents(_DATA_ . urlencode($title) . '.json', json_encode($info));

        if (!$flag) Response::ajaxReturn(['error' => Response::AJAXRETURN_FAIL, 'msg' => '文件保存失败']);

        Response::ajaxReturn(['error' => Response::AJAXRETURN_SUCCESS, 'msg' => '保存成功']);
    }

    public static function getOptInfo()
    {
        $filename = Request::get('get.filename', '', 'trim');

        if ('' === $filename) Response::ajaxReturn(['error' => Response::AJAXRETURN_FAIL, 'msg' => '配置名不为空']);

        $filename = _DATA_ . urlencode($filename) . '.json';

        if (!file_exists($filename)) Response::ajaxReturn(['error' => Response::AJAXRETURN_FAIL, 'msg' => '配置文件不存在']);

        $data = json_decode(file_get_contents($filename), true);

        Response::ajaxReturn(['error' => Response::AJAXRETURN_SUCCESS, 'msg' => '获取成功', 'data' => $data]);
    }

    public static function create()
    {
        $info = Request::get('post.info');

        $isjson = Request::get('post.isjson', 0) ? true : false;

        $isdoc = Request::get('post.isdoc', 0) ? true : false;

        if (empty($info)) Response::ajaxReturn(['error' => Response::AJAXRETURN_FAIL, 'msg' => '配置不为空']);

        $json = '';

        if ($isjson) {
            $json = Request::get('post.json', '', 'trim');
            if (empty($json)) Response::ajaxReturn(['error' => Response::AJAXRETURN_FAIL, 'msg' => 'JSON不为空']);
            if (NULL === json_decode($json)) {
                Response::ajaxReturn(['error' => Response::AJAXRETURN_FAIL, 'msg' => 'JSON格式错误']);
            }
            $json = indentJson($json, '{$prefix}');
        }

        if ($isdoc) {
            $result = CommentFormat::createCommentFormat($info, $isjson, $json);
        } else {
            $result = WikiFormat::createWikiFormat($info, $isjson, $json);
        }

        Response::ajaxReturn(['error' => Response::AJAXRETURN_SUCCESS, 'msg' => 'Success', 'data' => htmlspecialchars($result)]);
    }

}
