<?php
/**
 * Created by PhpStorm.
 * User: suhanyu
 * Date: 2020-04-11
 * Time: 19:27
 */

namespace App\Services;

/**
 * 命令行参数解析以及运行
 * Class CliParamParseService
 * @package App\Services
 */
class CliParamParseService
{
    public static function run($params = [])
    {

    }

    // 获取菜单
    public static function showCateInfo($params = [])
    {
        $options = [
            'token'      => '',
            'project_id' => '',
        ];
        $options = array_merge($options, $params);
        $result = YapiService::getCatMenu([
            'token'      => $options['token'],
            'project_id' => $options['project_id'],
        ]);

        return $result;
    }

    // 保存或更新接口文档
    public static function saveOrUpdateDoc($params = [])
    {
        $options = [
            'token'      => '',
            'project_id' => '',
        ];
        $options = array_merge($options, $params);
        $result = YapiService::saveOrUpdateDoc([
            'token'      => $token,
            'project_id' => $cateInfo['project'],
            'cateid'     => $cateInfo['cateid'],
            'title'      => $apiTitle,
            'url_path'   => $urlPath,
            'desc'       => $desc,
            'markdown'   => $markdown,
            'req_params' => $exampleParam,
            'res_body'   => $exampleResponseParam,
            'status'     => 'done',
        ]);
        if ($result['status'] !== 1) {
            throw new \Exception($result['message'] ?? '', -1);
        }
        $resultData = $result['data'] ?? [];
        if ($resultData['errcode'] !== 0) {
            throw new \Exception($result['errmsg'] ?? '', -2);
        }
        $returnArr = [
            'status'  => 1,
            'message' => $resultData['errmsg'] ?? '',
        ];
        echo json_encode($returnArr, 320);
        die;

    }
}
