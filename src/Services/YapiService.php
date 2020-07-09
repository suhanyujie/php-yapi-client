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
        $token = getenv('YC_TOKEN');
        var_dump($token);exit(PHP_EOL.'4:04 下午'.PHP_EOL);
    }

    /**
     * @desc 执行保存或更新
     * 1.参数个数为 2 个时，表示直接提交某个md文档到 yapi 上，如 `yc /some/path/a.md`，token 从环境变量总获取 YC_TOKEN
     * 2.参数个数为 3 个时，如 `yc file /some/path/a.md` 同第一种功能
     * 3.参数个数为 3 个时，如 `yc dir /some/path` 表示使用环境变量中的 token 提交 path 目录下的所有 md 文档
     * 4.参数个数为 4 个时，如 `yc file someTokenFlag /some/path/a.md` 表示使用 someTokenFlag 对应的 token 提交该md文档
     * 5.参数个数为 4 个时，如 `yc dir someTokenFlag /some/path` 表示使用 someTokenFlag 对应的 token 提交目录`/some/path`下的md文档
     */
    public static function doSaveOrUpdate()
    {
        global $argv;
        $originCliArgs = $cliArgs = $argv;
        if ($cliArgs[0] === 'php') {
            $cliArgs = array_slice($cliArgs, 1, 10);
        }
        $originFilePath =
        $file = $cliArgs[2] ?? '';
        $config = ConfigParse::parseConfig();
        $tokenList = $config['token_section'];
        $actionType = 'file';// file/fold 表示提交文档/提交目录下的所有md文档
        if (count($tokenList) > 1 && count($cliArgs) == 2) {
            // 参数个数为 2 个时，表示 token 从环境变量中获取
            // 如 `yc /some/path/a.md`，`yc` 为第 0 个参数
            $token = getenv('YC_TOKEN');
            $file      = $cliArgs[1];
            if (strpos($file, '/') !== 0) {
                $file = realpath($file);
            }
        } elseif (count($tokenList) > 1 && count($cliArgs) == 3) {
            // 参数个数为 3 个时，表示 tokenFlag 从环境变量中获取
            // 如 `yc file /some/path/a.md`，其中 `yc` 为第 0 个参数
            $token = getenv('YC_TOKEN');
            if (empty($token)) {
                throw new \Exception("请配置环境变量 YC_TOKEN", -1);
            }
            $actionType = $cliArgs[1];// file/fold
            $file      = $cliArgs[2];
            if (strpos($file, '/') !== 0) {
                $file = realpath($file);
            }
        } elseif (count($tokenList) > 1 && count($cliArgs) != 4) {
            $tokenKeys = array_keys($tokenList);
            $cmd       = "【 yc file {$originFilePath} {$tokenKeys[0]} 】";
            echo "请确定使用的 token，例如：" . $cmd . "\n";
            echo "可选的 token key 列表：" . json_encode($tokenKeys, 320) . "\n";
            die;
        } elseif (count($tokenList) > 1 && count($cliArgs) == 4) {
            // 参数个数为 4 个时，表示 tokenFlag 从参数中获取
            // 如 `yc file someTokenFlag /some/path/a.md`，`yc` 为第 0 个参数
            $tokenFlag = $cliArgs[2];
            $file      = $cliArgs[3];
            if (strpos($file, '/') !== 0) {
                $file = realpath($file);
            }
            $token = $tokenList[$tokenFlag] ?? '';
            $actionType = $cliArgs[1];// file/fold
        } else {
            $token = $config['token_section']['default_token'] ?? '';
        }
        if (empty($token)) {
            throw new \Exception("指定的 token 不存在", -2);
        }
        if (empty($file) || !file_exists($file)) throw new \Exception("文件不存在！", -1);
        $exampleProjectId = 526;
        self::$staticData['token'] = $token;
        switch ($actionType) {
            case 'dir':// 提交目录下的所有md文档
                $dir = $file;
                $fs = new \FilesystemIterator($dir);
                foreach ($fs as $fileObj) {
                    $tmpFilename = $fileObj->getFilename();
                    $tmpExt = $fileObj->getExtension();
                    // 不是 md 文档的，则不上传
                    if (!in_array($tmpExt, ['md', ])) {
                        continue;
                    }
                    // 忽略 readme 文件
                    if (strpos($tmpFilename, 'README') !== false) {
                        continue;
                    }
                    $tmpFileRealPath = $fileObj->getRealPath();
                    self::saveOrUpdateOneDoc($tmpFileRealPath);
                    echo "|----------------------------------------------\n";
                }
                break;
            default:// 默认提交一个 md 文档
                self::saveOrUpdateOneDoc($file);
        }
        // 获取接口内容
//        $interfaceDoc = YapiService::getOneInterface([
//            'project_id'   => $exampleProjectId,
//            'token'        => $token,
//            'interface_id' => 90341,
//        ]);
    }

    public static function saveOrUpdateOneDoc($file = '')
    {
        if (!file_exists($file)) {
            throw new \Exception("文档[{$file}]不存在",-3);
        }
        $token = self::$staticData['token'];
        $mdFile = $file;
        // 解析出项目、分类
        $parseService = new \App\Services\ParserService;
        $parseService->setDocFile($mdFile);
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
