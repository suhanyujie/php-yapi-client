<?php
/**
 * Created by PhpStorm.
 * User: suhanyu
 * Date: 2020-04-11
 * Time: 19:24
 */

namespace App\Services;

use App\Libs\ConfigParse;

class YapiService
{
    public static $staticData = [];

    protected $data = [];

    public function __construct()
    {

    }

    /**
     * @desc 执行保存或更新
     */
    public static function doSaveOrUpdate()
    {
        global $argv;
        $cliArgs = $argv;
        $file = $cliArgs[2] ?? '';
        // 如果不是绝对路径，则将文件路径转为绝对路径
        if (strpos($file[0], '/') !== 0) {
            $file = realpath($file);
        }
        if (empty($file) || !file_exists($file)) throw new \Exception("文件不存在！", -1);
        $config = ConfigParse::parseConfig();
        $token = $config['token_section']['default_token'] ?? '';
        $exampleProjectId = 526;
        $exampleMdFile = $file;
        // 获取接口内容
//        $interfaceDoc = YapiService::getOneInterface([
//            'project_id'   => $exampleProjectId,
//            'token'        => $token,
//            'interface_id' => 90341,
//        ]);
        // 解析出项目、分类
        $parseService = new \App\Services\ParserService;
        $parseService->setDocFile($exampleMdFile);
        $apiTitle = $parseService->getTitle();
        $cateInfo = $parseService->getYapiFlag();
        if (empty($cateInfo['cateid'])) {
            throw new \Exception("请在文档中编辑好文档所属分类id", -49);
        }
        // 解析出文档url、请求参数、响应参数、返回值示例等
        $urlPath = $parseService->getApiPath();
        $apiMethod = $parseService->getApiMethod();
        $exampleParam = $parseService->getApiParam();
        $exampleResponseParam = $parseService->getApiResponseParam();
        $desc = $parseService->getApiDesc();
        $markdown = $parseService->getApiMarkdown();
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
    }

    // 获取一个接口的详细信息
    public static function getOneInterface($params = [])
    {
        $options = [
            'project_id'   => '',
            'token'        => '',
            'interface_id' => '',
        ];
        $options = array_merge($options, $params);
        $host = YapiService::getYapiHost();
        $options['url'] = $host."/api/interface/get?token={$options['token']}&id={$options['interface_id']}";
        $result = (new \App\Libs\Request)->get($options);

        return $result;
    }

    // 保存或更新接口
    public static function saveOrUpdateDoc($params = [])
    {
        $options = [
            'project_id'    => '',// 可选
            'cateid'        => '7401',// 分类id
            'token'         => '',
            'title'         => '',
            'url_path'      => '',
            'desc'          => '',
            'markdown'      => '',
            'method'        => 'POST',
            'interface_id'  => '',
            'status'        => 'undone',
            'res_body_type' => 'json',
            'res_body'      => '',
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
        self::$staticData['pre_one_api_params'] = $options;
        self::$staticData['save_one_api_res'] =
        $result = (new \App\Libs\Request)->post([
            'url'    => $url,
            'body'   => json_encode($body, 320),
        ]);

        return $result;
    }

    /**
     * @desc 输出显示上一次更新的文档的链接地址
     */
    public static function showInterfaceUrl()
    {
        $url = self::getInterfaceUrl();
        echo date('Y-m-d H:i:s')."\tdoc_url: {$url}";
    }

    /**
     * @desc 获取上一次更新的文档的链接地址
     */
    public static function getInterfaceUrl()
    {
        $result = self::$staticData['save_one_api_res'];
        $interfaceId = $result['data']['data'][0]['_id'] ?? '';
        $host = YapiService::getYapiHost();
        $param = self::$staticData['pre_one_api_params'];
        // $result['data']['data'] 为空时，可能是新增接口，新增接口时在响应中无法拿到接口id
        // 新增接口，直接展示对应的分类链接
        if (empty($result['data']['data'])) {
            $url = "{$host}/project/{$param['project_id']}/interface/api/cat_{$param['cateid']}";
        } else {
            $url = "{$host}/project/{$param['project_id']}/interface/api/{$interfaceId}";
        }

        return $url;
    }


    // 增加文档文件
    public static function parseOneDoc()
    {
        $config = ConfigParse::parseConfig();
        $token = $config['token_section']['hr_staff_center'] ?? '';
        if (empty($token)) throw new \Exception("请配置对应的 token", -1);
        // todo
        // var_dump($token);exit(PHP_EOL.'20:25'.PHP_EOL);
    }

    // 获取某个项目下的菜单信息
    public static function getCatMenu($params = [])
    {
        $options = [
            'token'      => '',
            'project_id' => '',
        ];
        $uri = '/api/interface/getCatMenu';
        $options = array_merge($options, $params);
        $config = ConfigParse::parseConfig();
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

    // 获取 yapi 的地址 host
    public static function getYapiHost()
    {
        $config = ConfigParse::parseConfig();
        $host = $config['base_section']['host'] ?? '';
        $host = rtrim($host, '/');

        return $host;
    }
}
