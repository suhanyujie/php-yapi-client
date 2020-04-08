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

$result = Yapi::getCatMenu([
    'token'      => $token,
    'project_id' => '526',
]);

echo json_encode($result, 320);die;

class Yapi
{
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
        $host = $config['base_section']['host'] ?? '';
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
