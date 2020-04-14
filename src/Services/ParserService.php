<?php
/**
 * Created by PhpStorm.
 * User: suhanyu
 * Date: 2020-04-10
 * Time: 15:49
 */

namespace App\Services;

use App\Libs\MdParser;

/**
 * 文档解析
 * Class ParserService
 * @package App\Services
 */
class ParserService
{
    protected $data = [];

    /**
     * @desc 设定文档内容
     */
    public function setDoc($content = '')
    {
        $this->data['content'] = $content;
    }

    /**
     * @desc 设定文档
     */
    public function setDocFile($fileName = '')
    {
        if (!file_exists($fileName)) {
            throw new \Exception("文档文件不存在！", -1);
        }
        $this->data['full_filename'] = $fileName;
        $this->data['content'] = file_get_contents($fileName);
        $fileName = pathinfo($fileName, PATHINFO_FILENAME);
        $this->data['filename'] = $fileName;
    }

    /**
     * @desc 获取接口标题
     */
    public function getTitle()
    {
        return $this->data['filename'];
    }

    /**
     * @desc 解析文档分类信息
     */
    public function getYapiFlag()
    {
        $docContent = $this->data['content'];
        $pattern = '@[#]+\srelate_flag\s{0,}\n\s{0,}-\s([^\n]+)\n\s{0,}-\s([^\n]+)\n\s{0,}-\s([^\n]+)\n@';
        preg_match_all($pattern, $docContent, $matchRes);
        if (empty($matchRes[1][0]) || empty($matchRes[2][0]) || empty($matchRes[3][0])) {
            throw new \Exception("没有匹配到文档对应的分类信息", -1);
        }
        $group = str_replace('group=', '', $matchRes[1][0]);
        $project = str_replace('project=', '', $matchRes[2][0]);
        $cateid = str_replace('cateid=', '', $matchRes[3][0]);
        $returnArr = [
            'group'   => $group,
            'project' => $project,
            'cateid'  => $cateid,
        ];

        return $returnArr;
    }

    /**
     * @desc 获取文档接口的 uri
     */
    public function getApiPath()
    {
        $doc = $this->data['content'];
        if (empty($doc)) return '';
        $pattern = '@### 请求URL\n- `http://api.example.com([^`]+)`@';
        preg_match_all($pattern, $doc, $matchRes);
        return trim($matchRes[1][0] ?? '');
    }

    /**
     * @desc 获取文档接口的入参示例
     */
    public function getApiParam()
    {
        $doc = $this->data['content'];
        if (empty($doc)) return '';
        $pattern = '@#### 请求参数示例[\n]{1,}```json\n([^`]+)```@';
        preg_match_all($pattern, $doc, $matchRes);
        return trim($matchRes[1][0] ?? '');
    }

    /**
     * @desc 获取文档接口的响应参数示例
     */
    public function getApiResponseParam()
    {
        $doc = $this->data['content'];
        if (empty($doc)) return '';
        $pattern = '@### 接口返回\n#### 返回示例\n\n```json\n([^`]+)```@';
        preg_match_all($pattern, $doc, $matchRes);
        return trim($matchRes[1][0] ?? '');
    }

    /**
     * @desc 获取文档接口的备注内容
     */
    public function getApiMarkdown()
    {
        return $this->data['content'];
    }

    /**
     * @desc 获取文档接口的备注内容
     */
    public function getApiDesc()
    {
        $md = $this->data['content'];
        $parser = new MdParser();
        $desc = $parser->makeHtml($md);

        return $desc;
    }
}
