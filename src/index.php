<?php
/**
 * Created by PhpStorm.
 * User: suhanyu
 * Date: 2020-04-07
 * Time: 16:53
 */

define('ROOT', realpath('./'));
include_once ROOT."/vendor/autoload.php";

$request = new \App\Libs\Request;
$result = $request->post();
$config = Config::parseConfig();
$token = $config['token_section']['hr_staff_center'] ?? '';
$exampleProjectId = 526;
$exampleInterfaceId = '';


// 保存接口文档
$result = Yapi::saveOneInterfaceDoc([
    'token'=>$token,
]);
echo json_encode($result);
die;

// 获取菜单
$result = Yapi::getCatMenu([
    'token'      => $token,
    'project_id' => $exampleProjectId,
]);
// 获取接口内容
$interfaceDoc = Yapi::getOneInterface([]);

echo json_encode($result, 320);die;

class Yapi
{
    public static function getOneInterface($params = [])
    {
        $options = [
            'project_id'   => '',
            'token'        => '',
            'interface_id' => '',
        ];
        $options = array_merge($options, $params);
        $result = (new \App\Libs\Request)->get([
        ]);
    }

    // 保存新接口
    public static function saveOneInterfaceDoc($params = [])
    {
        $options = [
            'project_id'    => '',// 可选
            'cateid'        => '7401',// 分类id
            'token'         => '',
            'desc'          => 'this is a desc',
            'method'        => 'POST',
            'interface_id'  => '',
            'status'        => 'undone',
            'res_body_type' => 'undone',
        ];
        $options = array_merge($options, $params);
        $authParam = [
            'token'=> $options['token'],
        ];
        $apiPath = '/api/interface/save';
        $url = self::getYapiHost().$apiPath;
        $content1 = [
            'err_no'  => 0,
            'err_msg' => "success",
            'results' => [],
        ];
        $body = [
                'title'         => 'rpc-测试接口',
                'catid'         => $options['cateid'],
                'path'          => '/bapi/rpc/test',
                'status'        => $options['status'],
                'res_body_type' => $options['res_body_type'],
                'res_body'      => json_encode($content1, 320),
                'desc'          => $options['desc'],
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
