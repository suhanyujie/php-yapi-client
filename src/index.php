#!/usr/bin/env php
<?php
/**
 * Created by PhpStorm.
 * User: suhanyu
 * Date: 2020-04-07
 * Time: 16:53
 */

define('ROOT', dirname(dirname(__FILE__)));
include_once ROOT . "/vendor/autoload.php";

use App\Libs\ConfigParse;
use App\Services\YapiService;

$cliArgs = $argv;
$file = $cliArgs[1] ?? '';
if (empty($file) || !file_exists($file)) throw new \Exception("请传入合法的文件名", -1);

$request = new \App\Libs\Request;
$result = $request->post();
$config = ConfigParse::parseConfig();
$token = $config['token_section']['default_token'] ?? '';
$exampleProjectId = 526;
$exampleInterfaceId = '';
// $exampleMdFile = $config['base_section']['example_file'] ?? '';
$exampleMdFile = $file;

// 获取接口内容
$interfaceDoc = YapiService::getOneInterface([
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
$apiMethod = $parseService->getApiMethod();
$exampleParam = $parseService->getApiParam();
$exampleResponseParam = $parseService->getApiResponseParam();
$desc = $parseService->getApiDesc();
$markdown = $parseService->getApiMarkdown();

if (empty($cateInfo['cateid'])) {
    throw new \Exception("请在文档中编辑好文档所属分类id", -49);
}

// 保存接口文档
$result = YapiService::saveOrUpdateDoc([
    'token'      => $token,
    'project_id' => $cateInfo['project'],
    'cateid'     => $cateInfo['cateid'],
    'title'      => $apiTitle,
    'url_path'   => $urlPath,
    'method'     => $apiMethod,
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
    throw new \Exception(json_encode($resultData, 320), -2);
}
$returnArr = [
    'status'  => 1,
    'message' => $resultData['errmsg'] ?? '',
];
echo json_encode($returnArr, 320);

echo PHP_EOL;
YapiService::showInterfaceUrl();
echo PHP_EOL;
die;
?>
