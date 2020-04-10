#!/usr/bin/env php
<?php
/**
 * Created by PhpStorm.
 * User: suhanyu
 * Date: 2020-04-07
 * Time: 16:53
 */

define('ROOT', realpath('./'));
include_once ROOT . "/vendor/autoload.php";
$cliArgs = $argv;
$file = $cliArgs[1] ?? '';
if (empty($file) || !file_exists($file)) throw new \Exception("请传入合法的文件名", -1);

$request = new \App\Libs\Request;
$result = $request->post();
$config = Config::parseConfig();
$token = $config['token_section']['hr_staff_center'] ?? '';
$exampleProjectId = 526;
$exampleInterfaceId = '';
// $exampleMdFile = $config['base_section']['example_file'] ?? '';
$exampleMdFile = $file;

// 获取接口内容
$interfaceDoc = Yapi::getOneInterface([
    'project_id'   => $exampleProjectId,
    'token'        => $token,
    'interface_id' => 90341,
]);

// 解析出项目、分类
$parseService = new \App\Services\ParserService;
$parseService->setDocFile($exampleMdFile);
$apiTitle = $parseService->getTitle();
$cateInfo = $parseService->getYapiFlag();

// 解析出文档url、请求参数、响应参数、返回值示例等
$urlPath = $parseService->getApiPath();
$exampleParam = $parseService->getApiParam();
$exampleResponseParam = $parseService->getApiResponseParam();
$desc = $parseService->getApiDesc();
$markdown = $parseService->getApiMarkdown();

// 保存接口文档
$result = Yapi::saveOneInterfaceDoc([
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
    'status'=>1,
    'message'=>$resultData['errmsg'] ?? '',
];
echo json_encode($returnArr, 320);
die;

// 获取菜单
$result = Yapi::getCatMenu([
    'token'      => $token,
    'project_id' => $exampleProjectId,
]);


echo json_encode($result, 320);die;

class Yapi
{
    // 获取一个接口的详细信息
    public static function getOneInterface($params = [])
    {
        $options = [
            'project_id'   => '',
            'token'        => '',
            'interface_id' => '',
        ];
        $options = array_merge($options, $params);
        $host = Yapi::getYapiHost();
        $options['url'] = $host."/api/interface/get?token={$options['token']}&id={$options['interface_id']}";
        $result = (new \App\Libs\Request)->get($options);

        return $result;
    }

    // 保存新接口
    public static function saveOneInterfaceDoc($params = [])
    {
        $options = [
            'project_id'    => '',// 可选
            'cateid'        => '7401',// 分类id
            'token'         => '',
            'title'         => '',
            'url_path'         => '',
            'desc'          => '',
            'markdown'          => '',
            'method'        => 'POST',
            'interface_id'  => '',
            'status'        => 'undone',
            'res_body_type' => 'json',
            'res_body' => '',
        ];
        $options = array_merge($options, $params);
        $authParam = [
            'token'=> $options['token'],
        ];
        $apiPath = '/api/interface/save';
        $url = self::getYapiHost().$apiPath;
        $body = [
                'title'         => $options['title'],
                'catid'         => $options['cateid'],
                'path'          => $options['url_path'],
                'status'        => $options['status'],
                'res_body_type' => $options['res_body_type'],
                'res_body'      => $options['res_body'],
                'desc'          => $options['desc'],
                'markdown'      => $options['markdown'],
                'method'        => $options['method'],
                'req_params'    => [],
            ] + $authParam;
        $result = (new \App\Libs\Request)->post([
            'url'    => $url,
            'body'   => json_encode($body, 320),
        ]);

        return $result;
    }

    // 增加文档文件
    public static function parseOneDoc()
    {
        $config = Config::parseConfig();
        $token = $config['token_section']['hr_staff_center'] ?? '';
        if (empty($token)) throw new \Exception("请配置对应的 token", -1);
        // todo
        // var_dump($token);exit(PHP_EOL.'20:25'.PHP_EOL);
    }

    public static function getCatMenu($params = [])
    {
        $uri = '/api/interface/getCatMenu';
        $options = [
            'token'      => '',
            'project_id' => '',
        ];
        $options = array_merge($options, $params);
        $config = Config::parseConfig();
        $host = self::getYapiHost();
        $url = "{$host}{$uri}";
        $queryParam = [
            'project_id' => $options['project_id'],
            'token'      => $options['token'],
        ];
        $result = (new \App\Libs\Request)->get([
            'url'  => $url,
            'data' => $queryParam,
        ]);

        return $result;
    }

    public static function getYapiHost()
    {
        $config = Config::parseConfig();
        $host = $config['base_section']['host'] ?? '';
        $host = rtrim($host, '/');

        return $host;
    }
}

class Config
{
    public static $config = [];

    // 解析配置文件
    public static function parseConfig(): array
    {
        if (!self::$config) {
            //解析配置文件
            self::$config = parse_ini_file(ROOT . "/.env", true);
        }

        return self::$config;
    }
}
